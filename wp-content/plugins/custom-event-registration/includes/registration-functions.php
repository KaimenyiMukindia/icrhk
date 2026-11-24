<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cer_kenyan_counties(): array {
	return array(
		'Baringo', 'Bomet', 'Bungoma', 'Busia', 'Elgeyo-Marakwet', 'Embu', 'Garissa',
		'Homa Bay', 'Isiolo', 'Kajiado', 'Kakamega', 'Kericho', 'Kiambu', 'Kilifi',
		'Kirinyaga', 'Kisii', 'Kisumu', 'Kitui', 'Kwale', 'Laikipia', 'Lamu', 'Machakos',
		'Makueni', 'Mandera', 'Marsabit', 'Meru', 'Migori', 'Mombasa', 'Murang\'a',
		'Nairobi', 'Nakuru', 'Nandi', 'Narok', 'Nyamira', 'Nyandarua', 'Nyeri', 'Samburu',
		'Siaya', 'Taita-Taveta', 'Tana River', 'Tharaka-Nithi', 'Trans Nzoia', 'Turkana',
		'Uasin Gishu', 'Vihiga', 'Wajir', 'West Pokot',
	);
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
		"SELECT ticket_type_id, SUM(ticket_quantity) AS occupied FROM {$wpdb->prefix}evt_registrations WHERE parent_registration_id IS NULL AND event_id = %d
		AND (status = 'paid' OR (status = 'confirmed' AND amount <= 0)
		OR (status IN ('pending', 'awaiting_payment', 'failed') AND reserved_until IS NOT NULL))
		GROUP BY ticket_type_id",
		$event_id
	), ARRAY_A );
	if ( $wpdb->last_error ) {
		error_log( 'CER availability capacity query failed: ' . $wpdb->last_error );
		return new WP_Error( 'cer_availability_failed', 'Availability could not be refreshed.' );
	}
	return cer_build_ticket_availability( $event, $tickets, $occupancy );
}

function cer_build_ticket_availability( object $event, array $tickets, array $occupancy ): array {
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
	$show_remaining = null !== $remaining && null !== $capacity && $remaining <= $capacity / 4;
	return '<strong class="cer-availability-count">' . esc_html( 0 === $remaining ? 'Sold out' : ( $show_remaining ? number_format_i18n( $remaining, 0 ) . ' left' : 'Available' ) ) . '</strong>'
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
	if ( ! isset( $ticket['capacity'] ) || $ticket['remaining'] > $ticket['capacity'] / 4 ) {
		return $sold_out ? ' - Available (refresh to select)' : '';
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
		$event = $wpdb->get_row( $wpdb->prepare( "SELECT slug, max_attendees FROM {$wpdb->prefix}evt_events WHERE id = %d LIMIT 1", $event_id ) );
		$table = $wpdb->prefix . 'evt_ticket_types';
		$ticket_types = $wpdb->get_results( $wpdb->prepare( "SELECT id, name, price, quantity_available, quantity_sold, is_maintenance FROM $table WHERE event_id = %d AND deleted_at IS NULL ORDER BY order_index ASC, id ASC", $event_id ) );
		if ( ! empty( $ticket_types ) ) {
			$amount = (string) $ticket_types[0]->price;
		}
	}

	$event_reference = ! empty( $event->slug ) ? strtoupper( str_replace( '-', '-', $event->slug ) ) : strtoupper( get_bloginfo( 'name' ) );
	$summary_note = 'Your registration is secure. Once submitted, payment verification and your receipt will be processed automatically through our secure gateway.';
	$payee_name = get_bloginfo( 'name' );
	// Mirrors the submission handler's capacity and inventory checks so the form shows availability up front.
	$event_full = false;
	if ( ! empty( $event->max_attendees ) ) {
		$paid_registrations = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(ticket_quantity), 0) FROM {$wpdb->prefix}evt_registrations WHERE parent_registration_id IS NULL AND event_id = %d AND (status = 'paid' OR (status = 'confirmed' AND amount <= 0))", $event_id ) );
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
	$contact_note = 'Your contact details will be saved securely. No payment is required yet to submit this form.';
	?>
	<form id="cer-registration-form" class="cer-registration-form<?php echo $single_ticket ? ' cer-single-ticket' : ''; ?><?php echo $sold_out ? ' cer-sold-out' : ''; ?>" method="post" data-payment-method="mpesa" data-payment-required="<?php echo $payment_required ? 'true' : 'false'; ?>" data-ticket-count="<?php echo esc_attr( count( $ticket_types ) ); ?>"<?php if ( $availability_enabled ) : ?> data-availability="<?php echo $availability_data ? esc_attr( wp_json_encode( $availability_data ) ) : ''; ?>" data-availability-ready="<?php echo $availability_data ? 'true' : 'false'; ?>"<?php endif; ?> data-payment-note="<?php echo esc_attr( wp_kses_post( $summary_note ) ); ?>" data-contact-note="<?php echo esc_attr( $contact_note ); ?>" data-sold-out-note="Tickets for this event are sold out. Please contact the event team about future availability." novalidate>
		<div class="cer-payment-toggle" role="tablist" aria-label="Select payment method">
			<button class="cer-payment-toggle-btn is-active" id="btn-mpesa" type="button">M-Pesa</button>
			<button class="cer-payment-toggle-btn" id="btn-card" type="button"><span class="cer-toggle-label-full">Credit/Debit Card</span><span class="cer-toggle-label-short">Card</span></button>
		</div>
		<input id="payment_method_hidden" name="payment_method" type="hidden" value="mpesa">
		<input id="cer-ticket-quantity" name="ticket_quantity" type="hidden" value="1">
		<div class="cer-registration-progress" aria-label="Registration progress">
			<span class="cer-progress-step is-active"><b>1</b> Contact</span>
			<span class="cer-progress-line" aria-hidden="true"></span>
			<span class="cer-progress-step"><b>2</b> Conference details</span>
		</div>
		<div class="cer-form-steps">
		<div class="cer-form-step is-active" data-cer-step="1">
			<div class="cer-form-grid cer-contact-layout">
				<div class="cer-field-row cer-identity-row" data-cer-field-row>
					<div class="cer-field-group cer-title-group">
						<label for="cer-title">Title <span class="cer-optional-label">Optional</span></label>
						<input id="cer-title" name="attendee_title" placeholder="Dr / Eng / Ms" autocomplete="honorific-prefix" type="text" />
					</div>
					<div class="cer-field-group cer-full-name-group">
						<label for="cer-full-name">Full Name <span aria-hidden="true">*</span></label>
						<input id="cer-full-name" name="full_name" placeholder="Jane Doe" autocomplete="name" required type="text" />
					</div>
				</div>
				<div class="cer-field-row cer-contact-details-row" data-cer-field-row>
					<div class="cer-field-group cer-email-group">
						<label for="cer-email">Email Address <span aria-hidden="true">*</span></label>
						<input id="cer-email" name="email" placeholder="jane@example.com" autocomplete="email" required type="email" />
					</div>
					<div class="cer-field-group payment-field">
						<label for="cer-phone">Mobile / Telephone</label>
						<div class="cer-phone-field">
							<span class="cer-phone-prefix">+254</span>
							<input id="cer-phone" name="phone" placeholder="712 345 678" autocomplete="tel-national" type="tel" />
						</div>
					</div>
					<div class="cer-field-group">
						<label for="cer-identification-number">Identification Number</label>
						<input id="cer-identification-number" name="identification_number" placeholder="ID / Passport number" autocomplete="off" type="text" maxlength="100" />
					</div>
				</div>
				<div class="cer-field-row cer-profile-row" data-cer-field-row>
					<div class="cer-field-group">
						<label for="cer-organization">Organization</label>
						<input id="cer-organization" name="organization" placeholder="Organization name" autocomplete="organization" type="text" />
					</div>
					<div class="cer-field-group">
						<label for="cer-role">Role</label>
						<input id="cer-role" name="attendee_role" placeholder="e.g. Programme Manager" autocomplete="organization-title" type="text" />
					</div>
						<div class="cer-field-group cer-ticket-field">
							<label>Ticket / Event Type</label>
							<?php if ( ! $single_ticket ) : ?><details class="cer-ticket-picker"><summary id="cer-ticket-picker-label">Select ticket</summary><?php endif; ?>
							<div class="cer-ticket-choices" aria-label="Ticket quantities">
								<?php foreach ( $single_ticket ? array( $single_ticket ) : $ticket_types as $type ) :
									$price_label = rtrim( rtrim( number_format( (float) $type->price, 2, '.', '' ), '0' ), '.' );
									$unavailable = $type->sold_out || $type->maintenance;
									?>
									<div class="cer-ticket-choice" data-ticket-choice="<?php echo esc_attr( $type->id ); ?>">
										<span><?php echo esc_html( $type->name ); ?> <small><?php echo esc_html( (float) $type->price > 0 ? $price_label : 'Free' ); ?></small><small data-ticket-stock><?php echo esc_html( cer_ticket_option_availability_suffix( $ticket_availability[ $type->id ] ?? null, $unavailable ) ); ?></small></span>
										<div class="cer-quantity-control">
											<button type="button" data-ticket-minus aria-label="<?php echo esc_attr( 'Remove one ' . $type->name . ' ticket' ); ?>"<?php disabled( $unavailable ); ?>>-</button>
											<input name="ticket_quantities[<?php echo esc_attr( $type->id ); ?>]" data-ticket-quantity data-id="<?php echo esc_attr( $type->id ); ?>" data-price="<?php echo esc_attr( $type->price ); ?>" data-title="<?php echo esc_attr( $type->name ); ?>" aria-label="<?php echo esc_attr( $type->name . ' quantity' ); ?>" type="number" min="0" max="20" step="1" value="<?php echo $single_ticket && ! $unavailable ? '1' : '0'; ?>"<?php disabled( $unavailable ); ?>>
											<button type="button" data-ticket-plus aria-label="<?php echo esc_attr( 'Add one ' . $type->name . ' ticket' ); ?>"<?php disabled( $unavailable ); ?>>+</button>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
							<?php if ( ! $single_ticket ) : ?></details><?php endif; ?>
							<?php if ( $single_ticket ) : ?>
								<input id="cer-ticket-type" name="ticket_type" type="hidden" value="<?php echo esc_attr( $single_ticket->name ); ?>" data-id="<?php echo esc_attr( $single_ticket->id ); ?>" data-price="<?php echo esc_attr( $single_ticket->price ); ?>">
							<?php else : ?>
							<select id="cer-ticket-type" name="ticket_type" hidden aria-hidden="true" tabindex="-1">
								<option value="">Select a ticket</option>
								<?php if ( ! empty( $ticket_types ) ) : ?>
									<?php foreach ( $ticket_types as $type ) : ?>
										<?php $option_label = $type->name . ( (float) $type->price > 0 ? ' (' . rtrim( rtrim( number_format( (float) $type->price, 2, '.', '' ), '0' ), '.' ) . ')' : '' ); ?>
										<option value="<?php echo esc_attr( $type->name ); ?>" data-id="<?php echo esc_attr( $type->id ); ?>" data-price="<?php echo esc_attr( $type->price ); ?>"<?php selected( $single_ticket && $single_ticket->id === $type->id ); ?><?php if ( $availability_enabled ) : ?> data-base-label="<?php echo esc_attr( $option_label ); ?>"<?php endif; ?><?php echo ( $type->sold_out || $type->maintenance ) ? ' disabled' : ''; ?><?php echo $type->sold_out ? ' data-sold-out="true"' : ''; ?><?php echo $type->maintenance ? ' data-maintenance="true"' : ''; ?>><?php echo esc_html( $option_label . cer_ticket_option_availability_suffix( $ticket_availability[ $type->id ] ?? array( 'maintenance' => $type->maintenance, 'remaining' => null ), $type->sold_out ) ); ?></option>
									<?php endforeach; ?>
								<?php else : ?>
									<option value="" disabled>No ticket types available</option>
								<?php endif; ?>
							</select>
							<?php endif; ?>
						</div>
				</div>
			</div>
			<div class="cer-step-actions cer-step-actions-first">
				<span class="cer-step-required-note">Fields marked * are required.</span>
				<button type="button" class="cer-secondary-button" data-cer-next>Continue</button>
			</div>
		</div>
		<div class="cer-form-step" data-cer-step="2" hidden inert>
			<div class="cer-form-grid cer-details-layout">
				<div class="cer-field-row cer-demographic-row" data-cer-field-row>
					<div class="cer-field-group cer-county-group">
						<label for="cer-county">County</label>
						<input id="cer-county" name="county" list="cer-kenyan-counties" placeholder="Type or choose" autocomplete="address-level1" type="text" />
						<datalist id="cer-kenyan-counties">
							<?php foreach ( cer_kenyan_counties() as $county_name ) : ?><option value="<?php echo esc_attr( $county_name ); ?>"></option><?php endforeach; ?>
						</datalist>
					</div>
					<div class="cer-field-group cer-age-group">
						<label for="cer-age-group">Age</label>
						<select id="cer-age-group" name="age_group">
							<option value="">Select</option>
							<option value="18-24">18–24</option>
							<option value="25-34">25–34</option>
							<option value="35-and-above">35 and above</option>
						</select>
					</div>
					<div class="cer-field-group cer-gender-group">
						<label for="cer-gender">Gender</label>
						<select id="cer-gender" name="gender">
							<option value="">Select</option>
							<option value="M">Male</option>
							<option value="F">Female</option>
							<option value="Other">Other</option>
							<option value="Prefer not to say">Prefer not to say</option>
						</select>
					</div>
				</div>
				<fieldset class="cer-field-group cer-accommodation-field">
					<legend class="cer-visually-hidden">Do you require accessibility accommodations or support to participate in this conference? *</legend>
					<div class="cer-accessibility-options">
						<div class="cer-accessibility-prompt">
							<p class="cer-accessibility-question">Do you require accessibility accommodations or support to participate in this conference?</p>
							<div class="cer-radio-stack">
								<label class="cer-radio-option"><input type="radio" name="accessibility_support" value="no"> <span>No</span></label>
								<label class="cer-radio-option"><input type="radio" name="accessibility_support" value="yes"> <span>Yes</span></label>
							</div>
						</div>
						<div class="cer-accessibility-details">
							<label for="cer-accessibility-details">Please specify</label>
							<textarea id="cer-accessibility-details" name="accessibility_details" rows="2" maxlength="1000" placeholder="Describe the support you need"></textarea>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="cer-step-actions">
				<button type="button" class="cer-secondary-button cer-back-button" data-cer-back>Back</button>
				<button type="submit" class="cer-primary-button"<?php echo $sold_out ? ' disabled' : ''; ?>><?php echo $sold_out ? ( $maintenance_unavailable ? 'Temporarily Unavailable' : 'Sold Out' ) : ( $payment_required ? 'Register &amp; Pay' : 'Register' ); ?></button>
			</div>
		</div>
		</div>
		<?php if ( $availability_enabled ) : ?><small id="cer-availability-error" role="status"><?php echo $availability_data ? '' : 'Live availability unavailable. Counts may be out of date; retrying automatically.'; ?></small><?php endif; ?>
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
