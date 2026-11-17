<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cer_get_ticket_availability( int $event_id ) {
	global $wpdb;
	$event = $wpdb->get_row( $wpdb->prepare(
		"SELECT max_attendees FROM {$wpdb->prefix}evt_events WHERE id = %d AND status = 'published' AND deleted_at IS NULL",
		$event_id
	) );
	if ( $wpdb->last_error ) {
		error_log( 'CER availability event query failed: ' . $wpdb->last_error );
		return new WP_Error( 'cer_availability_failed', 'Availability could not be refreshed.' );
	}
	if ( ! $event ) {
		return new WP_Error( 'cer_event_unavailable', 'This event is no longer available.' );
	}
	$tickets = $wpdb->get_results( $wpdb->prepare(
		"SELECT id, name, quantity_available, quantity_sold, is_maintenance FROM {$wpdb->prefix}evt_ticket_types WHERE event_id = %d AND deleted_at IS NULL ORDER BY order_index, id",
		$event_id
	) );
	if ( $wpdb->last_error ) {
		error_log( 'CER availability ticket query failed: ' . $wpdb->last_error );
		return new WP_Error( 'cer_availability_failed', 'Availability could not be refreshed.' );
	}
	$occupancy = $wpdb->get_results( $wpdb->prepare(
		"SELECT ticket_type_id, COUNT(*) AS occupied FROM {$wpdb->prefix}evt_registrations WHERE event_id = %d
		AND (status = 'paid' OR (status = 'confirmed' AND amount <= 0)
		OR (status IN ('pending', 'awaiting_payment') AND amount > 0 AND reserved_until > %s))
		GROUP BY ticket_type_id",
		$event_id, gmdate( 'Y-m-d H:i:s' )
	), ARRAY_A );
	if ( $wpdb->last_error ) {
		error_log( 'CER availability capacity query failed: ' . $wpdb->last_error );
		return new WP_Error( 'cer_availability_failed', 'Availability could not be refreshed.' );
	}
	$occupied = 0;
	$ticket_occupied = array();
	foreach ( $occupancy as $row ) {
		$occupied += (int) $row['occupied'];
		if ( null !== $row['ticket_type_id'] ) {
			$ticket_occupied[ (int) $row['ticket_type_id'] ] = (int) $row['occupied'];
		}
	}
	$event_capacity = ! empty( $event->max_attendees ) ? (int) $event->max_attendees : null;
	$event_remaining = null !== $event_capacity ? max( 0, $event_capacity - (int) $occupied ) : null;
	$inventory_capacity = 0;
	$inventory_remaining = 0;
	$unlimited = false;
	$items = array();
	foreach ( $tickets as $ticket ) {
		$total = null !== $ticket->quantity_available ? (int) $ticket->quantity_available : null;
		$used = max( (int) $ticket->quantity_sold, $ticket_occupied[ $ticket->id ] ?? 0 );
		$remaining = null !== $total ? max( 0, $total - $used ) : null;
		$unlimited = $unlimited || null === $total;
		$inventory_capacity += $total ?? 0;
		$inventory_remaining += $remaining ?? 0;
		$ticket_capacity = null === $total ? $event_capacity : ( null === $event_capacity ? $total : min( $total, $event_capacity ) );
		if ( null !== $event_remaining ) {
			$remaining = null === $remaining ? $event_remaining : min( $remaining, $event_remaining );
		}
		$items[] = array( 'id' => (int) $ticket->id, 'name' => $ticket->name, 'capacity' => $ticket_capacity, 'remaining' => $remaining, 'maintenance' => ! empty( $ticket->is_maintenance ) );
	}
	$capacity = $event_capacity;
	if ( ! $unlimited ) {
		$capacity = null === $capacity ? $inventory_capacity : min( $capacity, $inventory_capacity );
		$event_remaining = null === $event_remaining ? $inventory_remaining : min( $event_remaining, $inventory_remaining );
	}
	return array( 'capacity' => $capacity, 'remaining' => $event_remaining, 'tickets' => $items );
}

function cer_ajax_ticket_availability(): void {
	nocache_headers();
	$availability = cer_get_ticket_availability( absint( $_GET['event_id'] ?? 0 ) );
	if ( is_wp_error( $availability ) ) {
		wp_send_json_error( array( 'message' => $availability->get_error_message() ), 503 );
	}
	wp_send_json_success( $availability );
}

function cer_ticket_availability_markup( array $availability ): string {
	if ( ! empty( $availability['tickets'] ) && count( array_filter( $availability['tickets'], static function ( $ticket ) { return ! empty( $ticket['maintenance'] ); } ) ) === count( $availability['tickets'] ) ) {
		return '<strong class="cer-availability-count">Scheduled for maintenance</strong><small>Registration will reopen when maintenance is complete.</small>';
	}
	$remaining = $availability['remaining'];
	$capacity = $availability['capacity'];
	return '<strong class="cer-availability-count">' . esc_html( null === $remaining ? 'Available' : ( 0 === $remaining ? 'Sold out' : number_format_i18n( $remaining, 0 ) . ' left' ) ) . '</strong>'
		. '<small>' . esc_html( null === $capacity ? 'Capacity not specified' : 'out of ' . number_format_i18n( $capacity, 0 ) . ' capacity' ) . '</small>';
}

function cer_ticket_option_availability_suffix( ?array $ticket, bool $sold_out ): string {
	if ( ! empty( $ticket['maintenance'] ) ) {
		return ' - Scheduled for maintenance';
	}
	if ( null === $ticket || null === $ticket['remaining'] ) {
		return $sold_out ? ' - Sold out' : '';
	}
	if ( $sold_out && 0 === $ticket['remaining'] ) {
		return ' - Sold out';
	}
	return ' - ' . number_format_i18n( $ticket['remaining'], 0 ) . ' left' . ( $sold_out ? ' (refresh to select)' : '' );
}

function cer_get_registration_form_markup( $event_id = null, $availability = null ) {
	global $wpdb;
	ob_start();

	$availability_enabled = null !== $availability;
	$availability_data = is_array( $availability ) ? $availability : null;
	$ticket_availability = array();
	if ( $availability_data ) {
		foreach ( $availability_data['tickets'] as $available_ticket ) {
			$ticket_availability[ $available_ticket['id'] ] = $available_ticket;
		}
	}
	$ticket_types = array();
	$event = null;
	$amount = '0';
	if ( $event_id ) {
		$event = $wpdb->get_row( $wpdb->prepare( "SELECT slug, summary_note, max_attendees FROM {$wpdb->prefix}evt_events WHERE id = %d LIMIT 1", $event_id ) );
		$table = $wpdb->prefix . 'evt_ticket_types';
		$ticket_types = $wpdb->get_results( $wpdb->prepare( "SELECT id, name, price, quantity_available, quantity_sold, is_maintenance FROM $table WHERE event_id = %d AND deleted_at IS NULL ORDER BY order_index ASC, id ASC", $event_id ) );
		if ( ! empty( $ticket_types ) ) {
			$amount = (string) $ticket_types[0]->price;
		}
	}

	$event_reference = ! empty( $event->slug ) ? strtoupper( str_replace( '-', '-', $event->slug ) ) : strtoupper( get_bloginfo( 'name' ) );
	$summary_note = ! empty( $event->summary_note ) ? $event->summary_note : '';
	if ( '' === trim( $summary_note ) || in_array( trim( wp_strip_all_tags( $summary_note ) ), array(
		'Payment verification and receipt delivery are handled after registration using a secure gateway workflow.',
		'Payment verification and receipt delivery are handled after registration using the secure gateway workflow.',
	), true ) ) {
		$summary_note = 'Your registration is secure. Once submitted, payment verification and your receipt will be processed automatically through our secure gateway.';
	}
	$payee_name = get_bloginfo( 'name' );
	// Mirrors the submission handler's capacity and inventory checks so the form shows availability up front.
	$event_full = false;
	if ( ! empty( $event->max_attendees ) ) {
		$paid_registrations = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}evt_registrations WHERE event_id = %d AND (status = 'paid' OR (status = 'confirmed' AND amount <= 0))", $event_id ) );
		$event_full = $paid_registrations >= (int) $event->max_attendees;
	}
	$available_tickets = array();
	$has_maintenance_ticket = false;
	foreach ( $ticket_types as $type ) {
		$type->maintenance = ! empty( $type->is_maintenance );
		$has_maintenance_ticket = $has_maintenance_ticket || $type->maintenance;
		$type->sold_out = $event_full || ( null !== $type->quantity_available && (int) $type->quantity_sold >= (int) $type->quantity_available );
		if ( ! $type->sold_out && ! $type->maintenance ) {
			$available_tickets[] = $type;
		}
	}
	$sold_out = ! empty( $ticket_types ) && empty( $available_tickets );
	$maintenance_unavailable = $sold_out && ! $event_full && $has_maintenance_ticket;
	$sold_out_note = $event_full ? 'This event is fully booked. Please contact the event team about future availability.' : ( $maintenance_unavailable ? 'Registration for this ticket is temporarily unavailable while maintenance is in progress. Please check back soon.' : 'Tickets for this event are sold out. Please contact the event team about future availability.' );
	$single_ticket = 1 === count( $available_tickets ) ? $available_tickets[0] : null;
	if ( $single_ticket ) {
		$amount = (string) $single_ticket->price;
	}
	$payment_required = false;
	foreach ( $available_tickets as $type ) {
		if ( (float) $type->price > 0 ) {
			$payment_required = true;
			break;
		}
	}
	$contact_note = 'Your contact details will be saved securely. No payment is required to submit this form.';
	?>
	<form id="cer-registration-form" class="cer-registration-form<?php echo $single_ticket ? ' cer-single-ticket' : ''; ?><?php echo $sold_out ? ' cer-sold-out' : ''; ?>" method="post" data-payment-method="mpesa" data-payment-required="<?php echo $payment_required ? 'true' : 'false'; ?>" data-ticket-count="<?php echo esc_attr( count( $ticket_types ) ); ?>"<?php if ( $availability_enabled ) : ?> data-availability="<?php echo $availability_data ? esc_attr( wp_json_encode( $availability_data ) ) : ''; ?>" data-availability-ready="<?php echo $availability_data ? 'true' : 'false'; ?>"<?php endif; ?> data-payment-note="<?php echo esc_attr( wp_kses_post( $summary_note ) ); ?>" data-contact-note="<?php echo esc_attr( $contact_note ); ?>" data-sold-out-note="Tickets for this event are sold out. Please contact the event team about future availability." novalidate>
		<div class="cer-payment-toggle" role="tablist" aria-label="Select payment method">
			<button class="cer-payment-toggle-btn is-active" id="btn-mpesa" type="button">M-Pesa</button>
			<button class="cer-payment-toggle-btn" id="btn-card" type="button"><span class="cer-toggle-label-full">Credit/Debit Card</span><span class="cer-toggle-label-short">Card</span></button>
		</div>
		<input id="payment_method_hidden" name="payment_method" type="hidden" value="mpesa">
		<?php if ( $single_ticket ) : ?>
			<p class="cer-ticket-selection">Ticket: <strong><?php echo esc_html( $single_ticket->name ); ?></strong><?php if ( $availability_enabled && count( $ticket_types ) > 1 ) : ?> <small id="cer-inline-ticket-availability" aria-live="polite"><?php echo esc_html( cer_ticket_option_availability_suffix( $ticket_availability[ $single_ticket->id ] ?? null, false ) ); ?></small><?php endif; ?></p>
			<input id="cer-ticket-type" name="ticket_type" type="hidden" value="<?php echo esc_attr( $single_ticket->name ); ?>" data-id="<?php echo esc_attr( $single_ticket->id ); ?>" data-price="<?php echo esc_attr( $single_ticket->price ); ?>">
		<?php endif; ?>
		<div class="cer-form-grid">
			<div class="cer-field-group cer-full-name-group">
				<label for="cer-full-name">Full Name</label>
				<input id="cer-full-name" name="full_name" placeholder="Jane Doe" required type="text" />
			</div>
			<div class="cer-field-group cer-email-group">
				<label for="cer-email">Email Address</label>
				<input id="cer-email" name="email" placeholder="jane@example.com" required type="email" />
			</div>
			<div class="cer-field-group payment-field">
				<label for="cer-phone">Phone Number</label>
				<div class="cer-phone-field">
					<span class="cer-phone-prefix">+254</span>
					<input id="cer-phone" name="phone" placeholder="712 345 678" required type="tel" />
				</div>
			</div>
			<?php if ( ! $single_ticket ) : ?>
				<div class="cer-field-group">
					<label for="cer-ticket-type">Ticket / Event Type</label>
					<select id="cer-ticket-type" name="ticket_type" required>
						<option value="">Select a ticket</option>
						<?php if ( ! empty( $ticket_types ) ) : ?>
							<?php foreach ( $ticket_types as $type ) : ?>
								<?php $option_label = $type->name . ( (float) $type->price > 0 ? ' (KES ' . number_format_i18n( (float) $type->price, 2 ) . ')' : '' ); ?>
								<option value="<?php echo esc_attr( $type->name ); ?>" data-id="<?php echo esc_attr( $type->id ); ?>" data-price="<?php echo esc_attr( $type->price ); ?>"<?php if ( $availability_enabled ) : ?> data-base-label="<?php echo esc_attr( $option_label ); ?>"<?php endif; ?><?php echo ( $type->sold_out || $type->maintenance ) ? ' disabled' : ''; ?><?php echo $type->sold_out ? ' data-sold-out="true"' : ''; ?><?php echo $type->maintenance ? ' data-maintenance="true"' : ''; ?>><?php echo esc_html( $option_label . cer_ticket_option_availability_suffix( $ticket_availability[ $type->id ] ?? array( 'maintenance' => $type->maintenance, 'remaining' => null ), $type->sold_out ) ); ?></option>
							<?php endforeach; ?>
						<?php else : ?>
							<option value="" disabled>No ticket types available</option>
						<?php endif; ?>
					</select>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( $availability_enabled ) : ?><small id="cer-availability-error" role="status"><?php echo $availability_data ? '' : 'Live availability unavailable. Counts may be out of date; retrying automatically.'; ?></small><?php endif; ?>
		<button type="submit" class="cer-primary-button"<?php echo $sold_out ? ' disabled' : ''; ?>><?php echo $sold_out ? ( $maintenance_unavailable ? 'Temporarily Unavailable' : 'Sold Out' ) : ( $payment_required ? 'Register &amp; Pay' : 'Register' ); ?></button>
		<input type="hidden" id="cer-amount" name="amount" value="<?php echo esc_attr( $amount ); ?>" />
		<input type="hidden" id="cer-event-id" name="event_id" value="<?php echo esc_attr( $event_id ); ?>" />
		<input type="hidden" id="cer-ticket-type-id" name="ticket_type_id" value="<?php echo $single_ticket ? esc_attr( $single_ticket->id ) : '0'; ?>" />
		<input type="hidden" id="cer-event-reference" value="<?php echo esc_attr( $event_reference ); ?>" />
		<input type="hidden" id="cer-payee-name" value="<?php echo esc_attr( $payee_name ); ?>" />
		<?php wp_nonce_field( 'cer_registration_form', 'security' ); ?>
		<input type="hidden" name="action" value="cer_submit_registration" />
		<div id="cer-form-message" class="cer-form-footnote cer-form-message cer-form-message-idle" role="status" aria-live="polite" aria-atomic="true" aria-busy="false">
			<?php echo wp_kses_post( $sold_out ? $sold_out_note : ( $payment_required ? $summary_note : $contact_note ) ); ?>
		</div>
	</form>
	<?php
	return ob_get_clean();
}
