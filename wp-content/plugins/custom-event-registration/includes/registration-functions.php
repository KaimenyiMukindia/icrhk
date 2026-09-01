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
		$event = $wpdb->get_row( $wpdb->prepare( "SELECT slug, summary_note FROM {$wpdb->prefix}evt_events WHERE id = %d LIMIT 1", $event_id ) );
		$table = $wpdb->prefix . 'evt_ticket_types';
		$ticket_types = $wpdb->get_results( $wpdb->prepare( "SELECT id, name, price FROM $table WHERE event_id = %d ORDER BY order_index ASC, id ASC", $event_id ) );
		if ( ! empty( $ticket_types ) ) {
			$amount = (string) $ticket_types[0]->price;
		}
	}

	$event_reference = ! empty( $event->slug ) ? strtoupper( str_replace( '-', '-', $event->slug ) ) : strtoupper( get_bloginfo( 'name' ) );
	$summary_note = ! empty( $event->summary_note ) ? $event->summary_note : 'Payment verification and receipt delivery are handled after registration using a secure gateway workflow.';
	$payee_name = get_bloginfo( 'name' );
	?>
	<form id="cer-registration-form" class="cer-registration-form" method="post" novalidate>
		<div class="cer-payment-toggle" role="tablist" aria-label="Select payment method">
			<button class="cer-payment-toggle-btn is-active" id="btn-mpesa" onclick="togglePaymentMethod('mpesa')" type="button">M-Pesa</button>
			<button class="cer-payment-toggle-btn" id="btn-card" onclick="togglePaymentMethod('card')" type="button">Credit/Debit Card</button>
		</div>
		<input id="payment_method_hidden" name="payment_method" type="hidden" value="mpesa">
		<div class="cer-payment-panel cer-mpesa-panel" data-payment-panel="mpesa">
		<div class="cer-form-grid">
			<div class="cer-field-group">
				<label for="cer-full-name">Full Name</label>
				<input id="cer-full-name" name="full_name" placeholder="Jane Doe" required type="text" />
			</div>
			<div class="cer-field-group">
				<label for="cer-email">Email Address</label>
				<input id="cer-email" name="email" placeholder="jane@example.com" required type="email" />
			</div>
			<div class="cer-field-group payment-field mpesa-field block md:col-span-2">
				<label for="cer-phone">Phone Number</label>
				<div class="cer-phone-field">
					<span class="cer-phone-prefix">+254</span>
					<input id="cer-phone" name="phone" placeholder="712 345 678" required type="tel" />
				</div>
			</div>
			<div class="cer-field-group cer-span-2">
				<label for="cer-ticket-type">Ticket / Event Type</label>
				<select id="cer-ticket-type" name="ticket_type" required>
					<option value="">Select a ticket</option>
					<?php if ( ! empty( $ticket_types ) ) : ?>
						<?php foreach ( $ticket_types as $type ) : ?>
							<option value="<?php echo esc_attr( $type->name ); ?>" data-id="<?php echo esc_attr( $type->id ); ?>" data-price="<?php echo esc_attr( $type->price ); ?>"><?php echo esc_html( $type->name . ' (KES ' . number_format_i18n( (float) $type->price, 2 ) . ')' ); ?></option>
						<?php endforeach; ?>
					<?php else : ?>
						<option value="" disabled>No ticket types available</option>
					<?php endif; ?>
				</select>
			</div>
		</div>
			<button type="submit" class="cer-primary-button">Register & Pay with M-Pesa</button>
		</div>
		<div class="cer-payment-panel cer-card-panel cer-modal-hidden" data-payment-panel="card" aria-hidden="true">
			<div class="cer-form-grid cer-card-fields">
				<div class="cer-field-group">
					<label for="cer-card-email">Email Address</label>
					<input id="cer-card-email" name="card_email" placeholder="jane@example.com" type="email" disabled />
				</div>
				<div class="cer-field-group">
					<label for="cer-card-ticket-type">Ticket / Event Type</label>
					<select id="cer-card-ticket-type" name="card_ticket_type" disabled>
						<option value="">Select a ticket</option>
						<?php if ( ! empty( $ticket_types ) ) : ?>
							<?php foreach ( $ticket_types as $type ) : ?>
								<option value="<?php echo esc_attr( $type->name ); ?>" data-id="<?php echo esc_attr( $type->id ); ?>" data-price="<?php echo esc_attr( $type->price ); ?>"><?php echo esc_html( $type->name . ' (KES ' . number_format_i18n( (float) $type->price, 2 ) . ')' ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>
			</div>
			<p class="cer-card-flow-note">Secure Card checkout loads below after you select a ticket.</p>
			<button type="submit" class="cer-primary-button cer-card-submit">Register & Pay with Card</button>
		</div>
		<input type="hidden" id="cer-amount" name="amount" value="<?php echo esc_attr( $amount ); ?>" />
		<input type="hidden" id="cer-event-id" name="event_id" value="<?php echo esc_attr( $event_id ); ?>" />
		<input type="hidden" id="cer-ticket-type-id" name="ticket_type_id" value="0" />
		<input type="hidden" id="cer-event-reference" value="<?php echo esc_attr( $event_reference ); ?>" />
		<input type="hidden" id="cer-payee-name" value="<?php echo esc_attr( $payee_name ); ?>" />
		<?php wp_nonce_field( 'cer_registration_form', 'security' ); ?>
		<input type="hidden" name="action" value="cer_submit_registration" />
		<div class="cer-form-footnote">
			<?php echo wp_kses_post( $summary_note ); ?>
		</div>
	</form>
	<div class="cer-hosted-payment cer-modal-hidden" id="cer-hosted-payment" aria-live="polite">
		<iframe id="cer-hosted-payment-frame" title="Secure PesaPal payment checkout" loading="lazy"></iframe>
	</div>
	<?php
	return ob_get_clean();
}
