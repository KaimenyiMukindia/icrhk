<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="cer-mail-composer">
	<?php if ( $event_id ) : ?>
		<?php
		$mail_rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT id, full_name, email, status, amount, ticket_type, ticket_quantity, admission_quantity, parent_registration_id, user_access_key FROM {$wpdb->prefix}evt_registrations WHERE event_id = %d AND deleted_at IS NULL ORDER BY id DESC",
			$event_id
		), ARRAY_A );
		if ( $wpdb->last_error ) {
			error_log( 'CER event mail recipients could not be loaded: ' . $wpdb->last_error );
			echo '<p>' . esc_html__( 'Recipients could not be loaded. Please try again.', 'custom-event-registration' ) . '</p>';
			$mail_rows = array();
		}
		$mail_jobs = array();
		$mail_prefix = cer_event_mail_job_key( $event_id );
		$mail_job_names = $wpdb->get_col( $wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options} WHERE option_name = %s OR option_name LIKE %s ORDER BY option_id DESC",
			$mail_prefix, $wpdb->esc_like( $mail_prefix . '_' ) . '%'
		) );
		if ( $wpdb->last_error ) {
			error_log( 'CER event mail batch lookup failed: ' . $wpdb->last_error );
			echo '<p>' . esc_html__( 'Email batch history could not be loaded. Please try again.', 'custom-event-registration' ) . '</p>';
		} else {
			foreach ( $mail_job_names as $mail_job_name ) {
				$mail_job_id = substr( $mail_job_name, strlen( $mail_prefix ) );
				if ( '' !== $mail_job_id && ! preg_match( '/^_[a-f0-9]{32}$/', $mail_job_id ) ) {
					continue;
				}
				$mail_job = get_option( $mail_job_name, array() );
				if ( ! empty( $mail_job['recipients'] ) ) {
					$mail_jobs[ '' === $mail_job_id ? 'legacy' : substr( $mail_job_id, 1 ) ] = $mail_job;
				}
			}
		}
		?>
		<h3><?php esc_html_e( 'Compose event email', 'custom-event-registration' ); ?></h3>
		<p><?php esc_html_e( 'Save event settings before sending. Each message is sent separately and copies the saved Sender Email. Sending does not save other event edits.', 'custom-event-registration' ); ?></p>
		<div class="cer-mail-recipient-cards">
		<div id="cer-mail-picker" class="cer-mail-picker">
			<p><label for="cer-mail-recipients"><strong><?php esc_html_e( 'Recipients', 'custom-event-registration' ); ?></strong></label></p>
			<select id="cer-mail-recipients" form="cer-event-mail-form" name="cer_mail_recipients[]" multiple size="7" style="width:100%">
				<?php foreach ( $mail_rows as $mail_row ) : ?>
					<option value="<?php echo esc_attr( $mail_row['id'] ); ?>" data-email="<?php echo esc_attr( cer_decrypt_pii( $mail_row['email'] ) ); ?>"><?php echo esc_html( '#' . $mail_row['id'] . ' ' . cer_decrypt_pii( $mail_row['full_name'] ) . ' <' . cer_decrypt_pii( $mail_row['email'] ) . '> (' . $mail_row['status'] . ')' ); ?></option>
				<?php endforeach; ?>
			</select>
			<input id="cer-mail-recipient-json" type="hidden" form="cer-event-mail-form" name="cer_mail_recipient_json" disabled />
			<div id="cer-mail-picker-enhanced" hidden>
				<label for="cer-mail-search"><?php esc_html_e( 'Find an existing registration', 'custom-event-registration' ); ?></label>
				<input id="cer-mail-search" type="search" autocomplete="off" placeholder="<?php esc_attr_e( 'Search name, email or registration ID', 'custom-event-registration' ); ?>" />
				<div id="cer-mail-search-results" class="cer-mail-search-results" aria-live="polite"></div>
			</div>
			<p><label><input id="cer-mail-all" type="checkbox" form="cer-event-mail-form" name="cer_mail_all" value="1" /> <?php esc_html_e( 'Send to all active registrations for this event', 'custom-event-registration' ); ?></label></p>
		</div>
		<div class="cer-mail-added">
			<label for="cer-mail-added-addresses"><strong><?php esc_html_e( 'Add individual or bulk email addresses', 'custom-event-registration' ); ?></strong></label>
			<p><?php esc_html_e( 'One email per line. Added addresses receive the message only; ticket attachments require selecting the attendee’s existing registration above. Duplicates of selected registration emails are ignored.', 'custom-event-registration' ); ?></p>
			<textarea id="cer-mail-added-addresses" name="cer_mail_added_addresses" form="cer-event-mail-form" rows="4" style="width:100%" placeholder="guest@example.com"></textarea>
			<button id="cer-mail-add-addresses" type="button" class="button" hidden><?php esc_html_e( 'Add to selected recipients', 'custom-event-registration' ); ?></button>
			<p id="cer-mail-address-error" role="alert" data-invalid="<?php esc_attr_e( 'Enter one valid email address per line.', 'custom-event-registration' ); ?>"></p>
		</div>
		</div>
		<div class="cer-mail-selected-heading"><strong><?php esc_html_e( 'Selected recipients', 'custom-event-registration' ); ?></strong><span id="cer-mail-selected-count" aria-live="polite"></span></div>
		<ul id="cer-mail-selected" class="cer-mail-selected cer-mail-recipient-chips" aria-label="<?php esc_attr_e( 'Selected email recipients', 'custom-event-registration' ); ?>"></ul>
		<details class="cer-mail-group-allocation">
			<summary><?php esc_html_e( 'Issue individual tickets from an existing group purchase', 'custom-event-registration' ); ?></summary>
			<p><?php esc_html_e( 'Add one email per unallocated seat in the bulk card. Each receives a fresh individual ticket with this message. The previous group code is invalidated; the purchase and totals remain recorded. This cannot be combined with “Send to all”.', 'custom-event-registration' ); ?></p>
			<label for="cer-mail-group"><?php esc_html_e( 'Existing group ticket', 'custom-event-registration' ); ?></label>
			<select id="cer-mail-group" name="cer_mail_group_id" form="cer-event-mail-form">
				<option value=""><?php esc_html_e( 'No group allocation', 'custom-event-registration' ); ?></option>
				<?php foreach ( $mail_rows as $group_row ) :
					if ( ! empty( $group_row['parent_registration_id'] ) || (int) $group_row['ticket_quantity'] < 2 || (int) $group_row['admission_quantity'] < 1 || ! ( 'paid' === $group_row['status'] || ( 'confirmed' === $group_row['status'] && (float) $group_row['amount'] <= 0 ) ) ) { continue; }
					?>
					<option value="<?php echo esc_attr( $group_row['id'] ); ?>" data-key="<?php echo esc_attr( $group_row['user_access_key'] ); ?>" data-seats="<?php echo esc_attr( $group_row['admission_quantity'] ); ?>"><?php echo esc_html( '#' . $group_row['id'] . ' ' . $group_row['ticket_type'] . ' — ' . $group_row['admission_quantity'] . ' unallocated seats' ); ?></option>
				<?php endforeach; ?>
			</select>
			<input id="cer-mail-group-key" name="cer_mail_group_key" type="hidden" form="cer-event-mail-form" />
			<p><label><input type="checkbox" name="cer_mail_group_confirm" value="1" form="cer-event-mail-form" /> <?php esc_html_e( 'I confirm these addresses replace all remaining group admissions.', 'custom-event-registration' ); ?></label></p>
		</details>
		<p><label for="cer-mail-subject"><strong><?php esc_html_e( 'Subject', 'custom-event-registration' ); ?></strong></label><br>
			<input id="cer-mail-subject" form="cer-event-mail-form" name="cer_mail_subject" type="text" style="width:100%" />
		</p>
		<label for="cer_mail_body"><strong><?php esc_html_e( 'Message', 'custom-event-registration' ); ?></strong></label>
		<?php
		ob_start();
		wp_editor( '', 'cer_mail_body', array( 'textarea_name' => 'cer_mail_body', 'media_buttons' => true, 'teeny' => false, 'textarea_rows' => 12 ) );
		$composer_editor = ob_get_clean();
		echo str_replace( '<textarea ', '<textarea form="cer-event-mail-form" ', $composer_editor ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<p><label><input type="checkbox" form="cer-event-mail-form" name="cer_mail_attach_ticket" value="1" /> <?php esc_html_e( 'Attach each recipient’s own generated ticket (confirmed admissions only)', 'custom-event-registration' ); ?></label></p>
		<p><label for="cer-mail-delivery"><?php esc_html_e( 'Delivery time', 'custom-event-registration' ); ?></label>
			<select id="cer-mail-delivery" form="cer-event-mail-form" name="cer_mail_delivery">
				<option value="now"><?php esc_html_e( 'Send now (background batches)', 'custom-event-registration' ); ?></option>
				<option value="daily"><?php esc_html_e( 'Next 03:05 Nairobi notification run', 'custom-event-registration' ); ?></option>
			</select>
		</p>
		<button type="submit" form="cer-event-mail-form" class="button button-primary"><?php esc_html_e( 'Queue email batch', 'custom-event-registration' ); ?></button>
		<?php foreach ( $mail_jobs as $mail_job_id => $mail_job ) : ?>
			<details class="cer-mail-job">
			<summary><?php echo esc_html( $mail_job['subject'] ?? __( 'Email batch', 'custom-event-registration' ) ); ?> — <?php echo esc_html( $mail_job['created_at'] ?? '' ); ?></summary>
			<?php $mail_counts = array_count_values( $mail_job['recipients'] ); ?>
			<?php if ( 'daily' === ( $mail_job['delivery'] ?? '' ) && ! empty( $mail_job['not_before'] ) ) : ?>
				<p><?php printf( esc_html__( 'Scheduled no earlier than %s Nairobi time, subject to WordPress cron.', 'custom-event-registration' ), esc_html( wp_date( 'Y-m-d H:i', (int) $mail_job['not_before'], new DateTimeZone( 'Africa/Nairobi' ) ) ) ); ?></p>
			<?php endif; ?>
			<p><?php printf( esc_html__( '%1$d queued, %2$d sent, %3$d failed, %4$d uncertain.', 'custom-event-registration' ), (int) ( $mail_counts['queued'] ?? 0 ), (int) ( $mail_counts['sent'] ?? 0 ), (int) ( $mail_counts['failed'] ?? 0 ), (int) ( $mail_counts['sending'] ?? 0 ) ); ?></p>
			<details>
				<summary><?php esc_html_e( 'Recipient delivery outcomes', 'custom-event-registration' ); ?></summary>
				<ul class="cer-mail-selected">
					<?php foreach ( $mail_job['recipients'] as $recipient_key => $recipient_state ) :
						$recipient_label = '#' . $recipient_key;
						if ( isset( $mail_job['added_addresses'][ $recipient_key ] ) ) {
							$recipient_label = cer_decrypt_pii( $mail_job['added_addresses'][ $recipient_key ] );
						} else {
							foreach ( $mail_rows as $recipient_row ) {
								if ( (int) $recipient_row['id'] === (int) $recipient_key ) {
									$recipient_label = '#' . $recipient_key . ' ' . cer_decrypt_pii( $recipient_row['full_name'] ) . ' <' . cer_decrypt_pii( $recipient_row['email'] ) . '>';
									break;
								}
							}
						}
					?>
						<li><span><?php echo esc_html( $recipient_label ); ?></span><strong><?php echo esc_html( $recipient_state ); ?></strong></li>
					<?php endforeach; ?>
				</ul>
			</details>
			<?php if ( ! empty( $mail_counts['sending'] ) ) : ?>
				<p><?php esc_html_e( 'Uncertain messages may have been accepted; check delivery before sending again.', 'custom-event-registration' ); ?></p>
				<?php foreach ( $mail_job['recipients'] as $uncertain_id => $state ) : if ( 'sending' === $state ) : ?><p><?php echo esc_html( '#' . $uncertain_id ); ?></p><?php endif; endforeach; ?>
				<button type="submit" form="cer-event-mail-form" name="cer_review_event_mail" value="<?php echo esc_attr( $mail_job_id ); ?>" class="button" onclick="return confirm('<?php echo esc_js( __( 'Have you checked the delivery outcome for each uncertain message?', 'custom-event-registration' ) ); ?>');"><?php esc_html_e( 'Mark uncertain deliveries reviewed', 'custom-event-registration' ); ?></button>
			<?php endif; ?>
			<?php foreach ( (array) ( $mail_job['errors'] ?? array() ) as $failed_id => $mail_error ) : ?><p><?php echo esc_html( '#' . $failed_id . ': ' . $mail_error ); ?></p><?php endforeach; ?>
			<?php if ( ! empty( $mail_counts['queued'] ) ) : ?><button type="submit" form="cer-event-mail-form" name="cer_resume_event_mail" value="<?php echo esc_attr( $mail_job_id ); ?>" class="button"><?php esc_html_e( 'Resume queued batch', 'custom-event-registration' ); ?></button><?php endif; ?>
			</details>
		<?php endforeach; ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Save the event before composing email.', 'custom-event-registration' ); ?></p>
	<?php endif; ?>
</div>
