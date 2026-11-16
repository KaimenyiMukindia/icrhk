<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cer_get_registration_form_markup( $event_id = null ) {
	global $wpdb;
	ob_start();

	$ticket_types = array();
	$event = null;
	$amount = '0';
	if ( $event_id ) {
		$event = $wpdb->get_row( $wpdb->prepare( "SELECT slug, summary_note, max_attendees FROM {$wpdb->prefix}evt_events WHERE id = %d LIMIT 1", $event_id ) );
		$table = $wpdb->prefix . 'evt_ticket_types';
		$ticket_types = $wpdb->get_results( $wpdb->prepare( "SELECT id, name, price, quantity_available, quantity_sold FROM $table WHERE event_id = %d AND deleted_at IS NULL ORDER BY order_index ASC, id ASC", $event_id ) );
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
	foreach ( $ticket_types as $type ) {
		$type->sold_out = $event_full || ( null !== $type->quantity_available && (int) $type->quantity_sold >= (int) $type->quantity_available );
		if ( ! $type->sold_out ) {
			$available_tickets[] = $type;
		}
	}
	$sold_out = ! empty( $ticket_types ) && empty( $available_tickets );
	$sold_out_note = $event_full ? 'This event is fully booked. Please contact the event team about future availability.' : 'Tickets for this event are sold out. Please contact the event team about future availability.';
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
	<form id="cer-registration-form" class="cer-registration-form<?php echo $single_ticket ? ' cer-single-ticket' : ''; ?><?php echo $sold_out ? ' cer-sold-out' : ''; ?>" method="post" data-payment-method="mpesa" data-payment-required="<?php echo $payment_required ? 'true' : 'false'; ?>" data-payment-note="<?php echo esc_attr( wp_kses_post( $summary_note ) ); ?>" data-contact-note="<?php echo esc_attr( $contact_note ); ?>" data-sold-out-note="Tickets for this event are sold out. Please contact the event team about future availability." novalidate>
		<div class="cer-payment-toggle" role="tablist" aria-label="Select payment method">
			<button class="cer-payment-toggle-btn is-active" id="btn-mpesa" type="button">M-Pesa</button>
			<button class="cer-payment-toggle-btn" id="btn-card" type="button"><span class="cer-toggle-label-full">Credit/Debit Card</span><span class="cer-toggle-label-short">Card</span></button>
		</div>
		<input id="payment_method_hidden" name="payment_method" type="hidden" value="mpesa">
		<?php if ( $single_ticket ) : ?>
			<p class="cer-ticket-selection">Ticket: <strong><?php echo esc_html( $single_ticket->name ); ?></strong></p>
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
								<option value="<?php echo esc_attr( $type->name ); ?>" data-id="<?php echo esc_attr( $type->id ); ?>" data-price="<?php echo esc_attr( $type->price ); ?>"<?php echo $type->sold_out ? ' disabled data-sold-out="true"' : ''; ?>><?php echo esc_html( $type->name . ( (float) $type->price > 0 ? ' (KES ' . number_format_i18n( (float) $type->price, 2 ) . ')' : '' ) . ( $type->sold_out ? ' - Sold out' : '' ) ); ?></option>
							<?php endforeach; ?>
						<?php else : ?>
							<option value="" disabled>No ticket types available</option>
						<?php endif; ?>
					</select>
				</div>
			<?php endif; ?>
		</div>
		<button type="submit" class="cer-primary-button"<?php echo $sold_out ? ' disabled' : ''; ?>><?php echo $sold_out ? 'Sold Out' : ( $payment_required ? 'Register &amp; Pay' : 'Register' ); ?></button>
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
