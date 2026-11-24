<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_post_cer_queue_event_mail', 'cer_handle_queue_event_mail' );
add_action( 'cer_event_mail_batch', 'cer_process_event_mail_batch', 10, 2 );

function cer_event_mail_job_key( int $event_id, string $job_id = '' ): string {
	return 'cer_event_mail_job_' . $event_id . ( '' !== $job_id ? '_' . $job_id : '' );
}

function cer_parse_event_mail_recipient_json( $value ) {
	if ( ! is_string( $value ) ) {
		return new WP_Error( 'cer_mail_recipient_input', __( 'The recipient selection is invalid. Reload the page and select recipients again.', 'custom-event-registration' ) );
	}
	$ids = json_decode( wp_unslash( $value ), true );
	if ( ! is_array( $ids ) || ( $ids && array_keys( $ids ) !== range( 0, count( $ids ) - 1 ) ) || array_filter( $ids, static function ( $id ) {
		return ! is_int( $id ) || $id < 1;
	} ) ) {
		return new WP_Error( 'cer_mail_recipient_input', __( 'The recipient selection is invalid. Reload the page and select recipients again.', 'custom-event-registration' ) );
	}
	return $ids;
}

function cer_save_event_mail_job( int $event_id, array $job, string $job_id = '' ): bool {
	$key = cer_event_mail_job_key( $event_id, $job_id );
	if ( update_option( $key, $job, false ) || get_option( $key ) === $job ) {
		return true;
	}
	error_log( 'CER could not persist event mail job for event ' . $event_id );
	return false;
}

function cer_parse_event_mail_addresses( string $text ) {
	$addresses = array();
	foreach ( preg_split( '/\r\n|\r|\n/', trim( $text ) ) as $index => $line ) {
		$email = trim( $line );
		if ( '' === $email ) {
			continue;
		}
		if ( ! is_email( $email ) ) {
			return new WP_Error( 'cer_mail_bulk_address', sprintf( __( 'Invalid email on line %d. Enter one email address per line.', 'custom-event-registration' ), $index + 1 ) );
		}
		$addresses[ strtolower( $email ) ] = $email;
	}
	return array_values( $addresses );
}

function cer_queue_event_mail( int $event_id, string $subject, string $body, array $recipient_ids, bool $attach_ticket, bool $all_recipients = false, string $delivery = 'now', string $job_id = '', array $addresses = array() ) {
	global $wpdb;
	if ( '' !== $job_id && ! preg_match( '/^[a-f0-9]{32}$/', $job_id ) ) {
		return new WP_Error( 'cer_mail_job_id', __( 'The email batch identifier is invalid.', 'custom-event-registration' ) );
	}
	if ( ! in_array( $delivery, array( 'now', 'daily' ), true ) ) {
		return new WP_Error( 'cer_mail_delivery_mode', __( 'Choose a valid delivery time.', 'custom-event-registration' ) );
	}
	$event = $wpdb->get_row( $wpdb->prepare(
		"SELECT id, name, mail_sender_email FROM {$wpdb->prefix}evt_events WHERE id = %d AND deleted_at IS NULL",
		$event_id
	), ARRAY_A );
	if ( $wpdb->last_error ) {
		error_log( 'CER event mail event lookup failed: ' . $wpdb->last_error );
		return new WP_Error( 'cer_mail_event_query', __( 'The event could not be loaded.', 'custom-event-registration' ) );
	}
	if ( ! $event || ! is_email( $event['mail_sender_email'] ) ) {
		return new WP_Error( 'cer_mail_sender', __( 'Save a valid Sender Email for this event before sending.', 'custom-event-registration' ) );
	}
	if ( '' === trim( $subject ) || '' === trim( wp_strip_all_tags( $body ) ) ) {
		return new WP_Error( 'cer_mail_content', __( 'Enter a subject and message.', 'custom-event-registration' ) );
	}
	// Restore paragraphs removed by the WordPress editor's save routine.
	$body = wpautop( $body );
	$recipient_ids = array_values( array_unique( array_filter( array_map( 'absint', $recipient_ids ) ) ) );
	$addresses = cer_parse_event_mail_addresses( implode( "\n", $addresses ) );
	if ( is_wp_error( $addresses ) ) {
		return $addresses;
	}
	if ( ! $all_recipients && empty( $recipient_ids ) && empty( $addresses ) ) {
		return new WP_Error( 'cer_mail_recipients', __( 'Select a registration or add an email address.', 'custom-event-registration' ) );
	}
	$lock = 'cer_mail_' . md5( $wpdb->prefix . $event_id );
	if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(SHA2(CONCAT(DATABASE(), %s), 256), 0)', $lock ) ) ) {
		return new WP_Error( 'cer_mail_locked', __( 'An email batch is currently being processed. Try again shortly.', 'custom-event-registration' ) );
	}
	try {
		$previous = get_option( cer_event_mail_job_key( $event_id, $job_id ), array() );
		if ( '' !== $job_id && $previous ) {
			return new WP_Error( 'cer_mail_job_exists', __( 'This email batch already exists.', 'custom-event-registration' ) );
		}
		if ( ! empty( $previous['recipients'] ) && ( in_array( 'queued', $previous['recipients'], true ) || in_array( 'sending', $previous['recipients'], true ) ) ) {
			return new WP_Error( 'cer_mail_busy', __( 'The previous email batch is queued or has an uncertain delivery. Review it before starting another batch.', 'custom-event-registration' ) );
		}
		if ( $all_recipients ) {
			$rows = $wpdb->get_col( $wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}evt_registrations WHERE event_id = %d AND deleted_at IS NULL ORDER BY id",
				$event_id
			) );
		} elseif ( $recipient_ids ) {
			$placeholders = implode( ',', array_fill( 0, count( $recipient_ids ), '%d' ) );
			$rows = $wpdb->get_col( $wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}evt_registrations WHERE event_id = %d AND deleted_at IS NULL AND id IN ({$placeholders})",
				array_merge( array( $event_id ), $recipient_ids )
			) );
		} else {
			$rows = array();
		}
		if ( $wpdb->last_error || ( ! $all_recipients && count( $rows ) !== count( $recipient_ids ) ) ) {
			error_log( 'CER event mail recipient selection failed: ' . $wpdb->last_error );
			return new WP_Error( 'cer_mail_recipient_selection', __( 'One or more selected registrations are unavailable for this event.', 'custom-event-registration' ) );
		}
		if ( $all_recipients ) {
			$recipient_ids = array_map( 'absint', $rows );
			if ( ! $recipient_ids && ! $addresses ) {
				return new WP_Error( 'cer_mail_recipients', __( 'There are no active registrations for this event.', 'custom-event-registration' ) );
			}
		}
		$job = array(
			'subject' => $subject,
			'body' => $body,
			'attach_ticket' => $attach_ticket,
			'recipients' => array_fill_keys( $recipient_ids, 'queued' ),
			'errors' => array(),
			'created_at' => current_time( 'mysql' ),
			'delivery' => $delivery,
			'added_addresses' => array(),
		);
		$registered_emails = array();
		if ( $recipient_ids ) {
			$placeholders = implode( ',', array_fill( 0, count( $recipient_ids ), '%d' ) );
			$emails = $wpdb->get_col( $wpdb->prepare(
				"SELECT email FROM {$wpdb->prefix}evt_registrations WHERE event_id = %d AND id IN ({$placeholders})",
				array_merge( array( $event_id ), $recipient_ids )
			) );
			if ( $wpdb->last_error ) {
				error_log( 'CER mail address deduplication failed: ' . $wpdb->last_error );
				return new WP_Error( 'cer_mail_addresses', __( 'Recipient addresses could not be loaded.', 'custom-event-registration' ) );
			}
			$registered_emails = array_map( static function ( $email ) { return strtolower( cer_decrypt_pii( $email ) ); }, $emails );
		}
		foreach ( $addresses as $email ) {
			if ( in_array( strtolower( $email ), $registered_emails, true ) ) {
				continue;
			}
			$key = 'email_' . hash( 'sha256', strtolower( $email ) );
			$job['added_addresses'][ $key ] = cer_encrypt_pii( $email );
			$job['recipients'][ $key ] = 'queued';
		}
		if ( 'daily' === $delivery ) {
			$local_now = new DateTimeImmutable( 'now', new DateTimeZone( 'Africa/Nairobi' ) );
			$due = $local_now->setTime( 3, 5 );
			if ( $due <= $local_now ) {
				$due = $due->modify( '+1 day' );
			}
			$job['not_before'] = $due->getTimestamp();
		}
		if ( ! cer_save_event_mail_job( $event_id, $job, $job_id ) ) {
			return new WP_Error( 'cer_mail_save', __( 'The email batch could not be saved.', 'custom-event-registration' ) );
		}
		$args = '' === $job_id ? array( $event_id ) : array( $event_id, $job_id );
		if ( 'now' === $delivery && ! wp_next_scheduled( 'cer_event_mail_batch', $args ) && ! wp_schedule_single_event( time() + 5, 'cer_event_mail_batch', $args ) ) {
			return new WP_Error( 'cer_mail_schedule', __( 'The batch was saved but could not be scheduled. Use Resume queued batch.', 'custom-event-registration' ) );
		}
		return count( $job['recipients'] );
	} finally {
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(SHA2(CONCAT(DATABASE(), %s), 256))', $lock ) );
	}
}

function cer_send_added_event_mail( int $event_id, string $key, array $job ) {
	global $wpdb;
	$event = $wpdb->get_row( $wpdb->prepare(
		"SELECT name, mail_sender_email FROM {$wpdb->prefix}evt_events WHERE id = %d AND deleted_at IS NULL", $event_id
	), ARRAY_A );
	if ( $wpdb->last_error ) {
		error_log( 'CER added-address event lookup failed: ' . $wpdb->last_error );
		return new WP_Error( 'cer_mail_lookup', __( 'The event could not be loaded.', 'custom-event-registration' ) );
	}
	$email = cer_decrypt_pii( $job['added_addresses'][ $key ] ?? '' );
	$sender = trim( (string) ( $event['mail_sender_email'] ?? '' ) );
	if ( ! $event || ! is_email( $email ) || ! is_email( $sender ) ) {
		return new WP_Error( 'cer_mail_address', __( 'The added address or saved Sender Email is unavailable.', 'custom-event-registration' ) );
	}
	cer_set_current_mail_config( $event_id, $event['name'] );
	try {
		$sent = wp_mail( $email, $job['subject'], $job['body'], array( 'Content-Type: text/html; charset=UTF-8', 'Reply-To: ' . $sender, 'Cc: ' . $sender ) );
	} finally {
		cer_clear_current_mail_config();
	}
	return $sent ? true : new WP_Error( 'cer_mail_delivery', __( 'Mail delivery failed; check the server mail log before resending.', 'custom-event-registration' ) );
}

function cer_send_composed_event_mail( int $event_id, int $registration_id, array $job ) {
	global $wpdb;
	$table = $wpdb->prefix . 'evt_registrations';
	$row = $wpdb->get_row( $wpdb->prepare(
		"SELECT r.*, e.name AS event_name, e.mail_sender_email FROM {$table} r INNER JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id WHERE r.id = %d AND r.event_id = %d AND r.deleted_at IS NULL AND e.deleted_at IS NULL",
		$registration_id, $event_id
	), ARRAY_A );
	if ( $wpdb->last_error ) {
		error_log( 'CER composed mail recipient lookup failed: ' . $wpdb->last_error );
		return new WP_Error( 'cer_mail_lookup', __( 'The recipient could not be loaded.', 'custom-event-registration' ) );
	}
	$email = $row ? cer_decrypt_pii( $row['email'] ) : '';
	$sender = $row ? trim( (string) $row['mail_sender_email'] ) : '';
	if ( ! $row || ! is_email( $email ) || ! is_email( $sender ) ) {
		return new WP_Error( 'cer_mail_address', __( 'The recipient or event Sender Email is unavailable.', 'custom-event-registration' ) );
	}
	$ticket_lock = '';
	if ( ! empty( $job['attach_ticket'] ) ) {
		$ticket_lock = 'cer_ticket_' . md5( $wpdb->prefix . $registration_id );
		if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(SHA2(CONCAT(DATABASE(), %s), 256), 0)', $ticket_lock ) ) ) {
			return new WP_Error( 'cer_mail_ticket_lock', __( 'The ticket is being processed elsewhere.', 'custom-event-registration' ) );
		}
	}
	try {
		$attachments = array();
		if ( '' !== $ticket_lock ) {
			$row = $wpdb->get_row( $wpdb->prepare(
				"SELECT r.*, e.name AS event_name, e.mail_sender_email FROM {$table} r INNER JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id WHERE r.id = %d AND r.event_id = %d AND r.deleted_at IS NULL AND e.deleted_at IS NULL",
				$registration_id, $event_id
			), ARRAY_A );
			if ( ! $row || ! cer_registration_ticket_is_valid( $row ) ) {
				return new WP_Error( 'cer_mail_ticket_unconfirmed', __( 'A ticket can only be attached to a confirmed admission.', 'custom-event-registration' ) );
			}
			$email = cer_decrypt_pii( $row['email'] );
			$sender = trim( (string) $row['mail_sender_email'] );
			if ( ! is_email( $email ) || ! is_email( $sender ) ) {
				return new WP_Error( 'cer_mail_address', __( 'The recipient or event Sender Email is unavailable.', 'custom-event-registration' ) );
			}
			$path = cer_generate_ticket_pdf( $registration_id );
			if ( '' === $path ) {
				return new WP_Error( 'cer_mail_ticket_generation', __( 'The ticket PDF could not be generated. Check the server log.', 'custom-event-registration' ) );
			}
			$attachments[] = $path;
		}
		$headers = array( 'Content-Type: text/html; charset=UTF-8', 'Reply-To: ' . $sender, 'Cc: ' . $sender );
		cer_set_current_mail_config( $event_id, $row['event_name'] );
		try {
			$sent = wp_mail( $email, $job['subject'], $job['body'], $headers, $attachments );
		} finally {
			cer_clear_current_mail_config();
		}
		if ( ! $sent ) {
			return new WP_Error( 'cer_mail_delivery', __( 'Mail delivery failed; check the server mail log before resending.', 'custom-event-registration' ) );
		}
		if ( $attachments ) {
			$now = current_time( 'mysql' );
			if ( false === $wpdb->update( $table, array( 'ticket_generated_at' => $now, 'ticket_sent_at' => $now ), array( 'id' => $registration_id ) ) ) {
				error_log( 'CER composed ticket timestamp update failed for ' . $registration_id . ': ' . $wpdb->last_error );
				return new WP_Error( 'cer_mail_ticket_timestamp', __( 'Mail was accepted but the ticket delivery timestamp could not be saved. Check before resending.', 'custom-event-registration' ) );
			}
			cer_notify_vendor_of_registration( $registration_id );
		}
		return true;
	} finally {
		if ( '' !== $ticket_lock ) {
			$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(SHA2(CONCAT(DATABASE(), %s), 256))', $ticket_lock ) );
		}
	}
}

function cer_queue_group_event_mail( int $event_id, int $parent_id, string $expected_key, string $subject, string $body, array $recipient_ids, array $addresses, string $delivery ) {
	global $wpdb;
	if ( '' === trim( $subject ) || '' === trim( wp_strip_all_tags( $body ) ) || ! in_array( $delivery, array( 'now', 'daily' ), true ) ) {
		return new WP_Error( 'cer_group_mail_content', __( 'Enter a subject, message and valid delivery time before allocating tickets.', 'custom-event-registration' ) );
	}
	$parent = $wpdb->get_row( $wpdb->prepare(
		"SELECT r.*, e.mail_sender_email FROM {$wpdb->prefix}evt_registrations r INNER JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id WHERE r.id = %d AND r.event_id = %d AND r.deleted_at IS NULL AND e.deleted_at IS NULL",
		$parent_id, $event_id
	), ARRAY_A );
	if ( $wpdb->last_error || ! $parent || ! is_email( $parent['mail_sender_email'] ) || ! hash_equals( (string) $parent['user_access_key'], $expected_key ) ) {
		return new WP_Error( 'cer_group_mail_parent', __( 'The group ticket or saved Sender Email is unavailable. Reload before allocating.', 'custom-event-registration' ) );
	}
	$attendees = array();
	foreach ( $addresses as $email ) {
		if ( ! is_email( $email ) ) {
			return new WP_Error( 'cer_group_mail_email', __( 'Each individual ticket needs a valid email address.', 'custom-event-registration' ) );
		}
		$attendees[ strtolower( $email ) ] = array( 'full_name' => $email, 'email' => $email );
	}
	foreach ( array_unique( array_map( 'absint', $recipient_ids ) ) as $id ) {
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT full_name, email FROM {$wpdb->prefix}evt_registrations WHERE id = %d AND event_id = %d AND deleted_at IS NULL", $id, $event_id ), ARRAY_A );
		$email = $row ? cer_decrypt_pii( $row['email'] ) : '';
		if ( $wpdb->last_error || ! is_email( $email ) ) {
			return new WP_Error( 'cer_group_mail_recipient', __( 'A selected registration is unavailable for this event.', 'custom-event-registration' ) );
		}
		$attendees[ strtolower( $email ) ] = array( 'full_name' => cer_decrypt_pii( $row['full_name'] ) ?: $email, 'email' => $email );
	}
	if ( count( $attendees ) !== cer_ticket_admissions( $parent ) || ! $attendees ) {
		return new WP_Error( 'cer_group_mail_seats', sprintf( __( 'Select or add exactly %d unique email addresses, one per remaining group seat.', 'custom-event-registration' ), cer_ticket_admissions( $parent ) ) );
	}
	$lines = array();
	foreach ( $attendees as $attendee ) {
		$lines[] = '"' . str_replace( '"', '""', $attendee['full_name'] ) . '",' . $attendee['email'];
	}
	$allocation = cer_allocate_group_tickets( $parent_id, implode( "\n", $lines ), $expected_key, false );
	if ( is_wp_error( $allocation ) ) {
		return $allocation;
	}
	$result = cer_queue_event_mail( $event_id, $subject, $body, $allocation['ids'], true, false, $delivery, bin2hex( random_bytes( 16 ) ) );
	if ( is_wp_error( $result ) ) {
		return new WP_Error( 'cer_group_mail_queue', sprintf( __( 'Individual tickets were created and the group code invalidated, but the email queue failed: %1$s. Select these existing registration IDs to retry mailing; do not allocate again: %2$s', 'custom-event-registration' ), $result->get_error_message(), implode( ', ', $allocation['ids'] ) ) );
	}
	$notice = cer_queue_event_mail( $event_id, 'Group ticket replaced with individual tickets',
		'<p>All seats in group purchase #' . $parent_id . ' now have individual tickets. The previous group ticket and code are invalid. Guests must bring their individual tickets for admission. If a ticket has not arrived, reply to the event team.</p>',
		array(), false, false, $delivery, bin2hex( random_bytes( 16 ) ), array( cer_decrypt_pii( $parent['email'] ) ) );
	if ( is_wp_error( $notice ) ) {
		return new WP_Error( 'cer_group_mail_notice', __( 'Individual tickets are queued, but the purchaser replacement notice could not be queued. Send that notice manually without a ticket attachment.', 'custom-event-registration' ) );
	}
	return $result;
}

function cer_process_event_mail_batch( int $event_id, string $job_id = '' ): void {
	global $wpdb;
	if ( '' !== $job_id && ! preg_match( '/^[a-f0-9]{32}$/', $job_id ) ) {
		error_log( 'CER event mail batch identifier invalid.' );
		return;
	}
	$lock = 'cer_mail_' . md5( $wpdb->prefix . $event_id );
	if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(SHA2(CONCAT(DATABASE(), %s), 256), 0)', $lock ) ) ) {
		error_log( 'CER event mail batch lock unavailable for event ' . $event_id );
		$pending = get_option( cer_event_mail_job_key( $event_id, $job_id ), array() );
		$args = '' === $job_id ? array( $event_id ) : array( $event_id, $job_id );
		if ( 'daily' !== ( $pending['delivery'] ?? 'now' ) && ! empty( $pending['recipients'] )
			&& in_array( 'queued', $pending['recipients'], true ) && ! wp_next_scheduled( 'cer_event_mail_batch', $args )
			&& ! wp_schedule_single_event( time() + 60, 'cer_event_mail_batch', $args ) ) {
			error_log( 'CER locked event mail batch could not be rescheduled for event ' . $event_id );
		}
		return;
	}
	try {
		$job = get_option( cer_event_mail_job_key( $event_id, $job_id ), array() );
		if ( empty( $job['recipients'] ) ) {
			return;
		}
		if ( 'daily' === ( $job['delivery'] ?? 'now' ) && (int) ( $job['not_before'] ?? PHP_INT_MAX ) > time() ) {
			return;
		}
		$processed = 0;
		foreach ( $job['recipients'] as $id => $state ) {
			if ( 'queued' !== $state ) {
				continue;
			}
			$job['recipients'][ $id ] = 'sending';
			if ( ! cer_save_event_mail_job( $event_id, $job, $job_id ) ) {
				return;
			}
			$result = isset( $job['added_addresses'][ $id ] )
				? cer_send_added_event_mail( $event_id, (string) $id, $job )
				: cer_send_composed_event_mail( $event_id, (int) $id, $job );
			$job['recipients'][ $id ] = is_wp_error( $result ) ? 'failed' : 'sent';
			if ( is_wp_error( $result ) ) {
				$job['errors'][ $id ] = $result->get_error_message();
			}
			if ( ! cer_save_event_mail_job( $event_id, $job, $job_id ) ) {
				return;
			}
			if ( ++$processed >= 10 ) {
				break;
			}
		}
		$args = '' === $job_id ? array( $event_id ) : array( $event_id, $job_id );
		if ( 'daily' !== ( $job['delivery'] ?? 'now' ) && in_array( 'queued', $job['recipients'], true ) && ! wp_next_scheduled( 'cer_event_mail_batch', $args ) && ! wp_schedule_single_event( time() + 60, 'cer_event_mail_batch', $args ) ) {
			error_log( 'CER event mail batch rescheduling failed for event ' . $event_id );
		}
	} finally {
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(SHA2(CONCAT(DATABASE(), %s), 256))', $lock ) );
	}
}

function cer_process_scheduled_event_mail( DateTimeImmutable $now ): void {
	global $wpdb;
	$prefix = 'cer_event_mail_job_';
	$names = $wpdb->get_col( $wpdb->prepare(
		"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
		$wpdb->esc_like( $prefix ) . '%'
	) );
	if ( $wpdb->last_error ) {
		error_log( 'CER scheduled event mail lookup failed: ' . $wpdb->last_error );
		return;
	}
	foreach ( $names as $name ) {
		$suffix = substr( $name, strlen( $prefix ) );
		if ( ! preg_match( '/^([1-9][0-9]*)(?:_([a-f0-9]{32}))?$/', $suffix, $matches ) ) {
			continue;
		}
		$job = get_option( $name, array() );
		if ( 'daily' === ( $job['delivery'] ?? '' ) && ! empty( $job['not_before'] )
			&& (int) $job['not_before'] <= $now->getTimestamp()
			&& ! empty( $job['recipients'] ) && in_array( 'queued', $job['recipients'], true ) ) {
			cer_process_event_mail_batch( (int) $matches[1], $matches[2] ?? '' );
			break;
		}
	}
}

function cer_handle_queue_event_mail(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'custom-event-registration' ), '', array( 'response' => 403 ) );
	}
	$event_id = absint( wp_unslash( $_POST['event_id'] ?? 0 ) );
	check_admin_referer( 'cer_event_mail_' . $event_id, 'cer_event_mail_nonce' );
	$job_id = sanitize_text_field( wp_unslash( $_POST['cer_review_event_mail'] ?? $_POST['cer_resume_event_mail'] ?? '' ) );
	if ( 'legacy' === $job_id ) {
		$job_id = '';
	} elseif ( '' !== $job_id && ! preg_match( '/^[a-f0-9]{32}$/', $job_id ) ) {
		wp_die( esc_html__( 'Invalid email batch identifier.', 'custom-event-registration' ), '', array( 'response' => 400 ) );
	}
	if ( isset( $_POST['cer_review_event_mail'] ) ) {
		global $wpdb;
		$lock = 'cer_mail_' . md5( $wpdb->prefix . $event_id );
		if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(SHA2(CONCAT(DATABASE(), %s), 256), 0)', $lock ) ) ) {
			$error = true;
			$message = __( 'The batch is being processed. Try again shortly.', 'custom-event-registration' );
		} else {
			try {
				$job = get_option( cer_event_mail_job_key( $event_id, $job_id ), array() );
				$reviewed = 0;
				foreach ( $job['recipients'] ?? array() as $id => $state ) {
					if ( 'sending' === $state ) {
						$job['recipients'][ $id ] = 'failed';
						$job['errors'][ $id ] = __( 'Delivery outcome was uncertain and was reviewed by an administrator.', 'custom-event-registration' );
						++$reviewed;
					}
				}
				$error = ! $reviewed || ! cer_save_event_mail_job( $event_id, $job, $job_id );
				$message = $error ? __( 'No uncertain deliveries were reviewed or the update failed.', 'custom-event-registration' ) : __( 'Uncertain deliveries marked reviewed. They were not resent.', 'custom-event-registration' );
			} finally {
				$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(SHA2(CONCAT(DATABASE(), %s), 256))', $lock ) );
			}
		}
	} elseif ( isset( $_POST['cer_resume_event_mail'] ) ) {
		$job = get_option( cer_event_mail_job_key( $event_id, $job_id ), array() );
		$args = '' === $job_id ? array( $event_id ) : array( $event_id, $job_id );
		$result = ! empty( $job['recipients'] ) && in_array( 'queued', $job['recipients'], true )
			&& ( 'daily' === ( $job['delivery'] ?? 'now' ) || wp_next_scheduled( 'cer_event_mail_batch', $args ) || wp_schedule_single_event( time() + 5, 'cer_event_mail_batch', $args ) );
		$message = $result ? __( 'Queued messages will be processed at their scheduled time.', 'custom-event-registration' ) : __( 'There are no queued messages or the batch could not be scheduled.', 'custom-event-registration' );
		$error = ! $result;
	} else {
		$body = wp_unslash( $_POST['cer_mail_body'] ?? '' );
		$body = current_user_can( 'unfiltered_html' ) ? $body : wp_kses_post( $body );
		$recipient_ids = (array) wp_unslash( $_POST['cer_mail_recipients'] ?? array() );
		if ( array_key_exists( 'cer_mail_recipient_json', $_POST ) ) {
			$recipient_ids = cer_parse_event_mail_recipient_json( $_POST['cer_mail_recipient_json'] );
			if ( is_wp_error( $recipient_ids ) ) {
				$result = $recipient_ids;
			}
		}
		if ( ! isset( $result ) ) {
			$addresses = cer_parse_event_mail_addresses( wp_unslash( $_POST['cer_mail_added_addresses'] ?? '' ) );
			if ( is_wp_error( $addresses ) ) {
				$result = $addresses;
			}
		}
		if ( ! isset( $result ) ) {
			$group_id = absint( $_POST['cer_mail_group_id'] ?? 0 );
			if ( $group_id && ( empty( $_POST['cer_mail_group_confirm'] ) || ! empty( $_POST['cer_mail_all'] ) ) ) {
				$result = new WP_Error( 'cer_group_mail_confirm', __( 'Confirm group replacement and turn off “Send to all” before allocating individual tickets.', 'custom-event-registration' ) );
			} elseif ( $group_id ) {
				$result = cer_queue_group_event_mail( $event_id, $group_id, sanitize_text_field( wp_unslash( $_POST['cer_mail_group_key'] ?? '' ) ),
					sanitize_text_field( wp_unslash( $_POST['cer_mail_subject'] ?? '' ) ), $body, $recipient_ids, $addresses,
					sanitize_key( wp_unslash( $_POST['cer_mail_delivery'] ?? 'now' ) ) );
			} else {
				$result = cer_queue_event_mail(
				$event_id, sanitize_text_field( wp_unslash( $_POST['cer_mail_subject'] ?? '' ) ), $body,
				$recipient_ids, ! empty( $_POST['cer_mail_attach_ticket'] ), ! empty( $_POST['cer_mail_all'] ),
				sanitize_key( wp_unslash( $_POST['cer_mail_delivery'] ?? 'now' ) ), bin2hex( random_bytes( 16 ) ), $addresses
				);
			}
		}
		$error = is_wp_error( $result );
		$message = $error ? $result->get_error_message() : sprintf( __( '%d individual messages queued. Delivery depends on WordPress cron.', 'custom-event-registration' ), $result );
	}
	set_transient( 'cer_event_mail_notice_' . get_current_user_id(), array( 'message' => $message, 'error' => $error ), 60 );
	wp_safe_redirect( add_query_arg( array( 'page' => 'cer-event-new', 'event_id' => $event_id, 'cer_event_active_tab' => 'mail', 'cer_mail_notice' => 1 ), admin_url( 'admin.php' ) ) );
	exit;
}
