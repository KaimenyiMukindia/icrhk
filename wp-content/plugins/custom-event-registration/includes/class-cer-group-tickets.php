<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( did_action( 'plugins_loaded' ) ) {
	cer_install_group_ticket_schema();
} else {
	add_action( 'plugins_loaded', 'cer_install_group_ticket_schema' );
}
add_action( 'admin_post_cer_allocate_group', 'cer_handle_group_allocation' );

function cer_install_group_ticket_schema(): void {
	if ( '3' === get_option( 'cer_group_ticket_schema', '' ) ) {
		return;
	}
	global $wpdb;
	$table = $wpdb->prefix . 'evt_registrations';
	foreach ( array(
		'ticket_quantity' => 'SMALLINT UNSIGNED NOT NULL DEFAULT 1',
		'admission_quantity' => 'SMALLINT UNSIGNED NOT NULL DEFAULT 1',
		'parent_registration_id' => 'BIGINT UNSIGNED NULL',
		'checkout_uuid' => 'VARCHAR(64) NULL',
	) as $column => $definition ) {
		cer_add_column_if_missing( $table, $column, $definition );
		if ( ! cer_column_exists( $table, $column ) ) {
			error_log( 'CER group ticket schema unavailable: ' . $column . ' ' . $wpdb->last_error );
			return;
		}
	}
	$index = $wpdb->get_var( $wpdb->prepare( "SHOW INDEX FROM {$table} WHERE Key_name = %s", 'parent_registration_id' ) );
	if ( $wpdb->last_error || ( ! $index && false === $wpdb->query( "ALTER TABLE {$table} ADD KEY parent_registration_id (parent_registration_id)" ) ) ) {
		error_log( 'CER group ticket index unavailable: ' . $wpdb->last_error );
		return;
	}
	$checkout_index = $wpdb->get_var( $wpdb->prepare( "SHOW INDEX FROM {$table} WHERE Key_name = %s", 'checkout_uuid' ) );
	if ( $wpdb->last_error || ( ! $checkout_index && false === $wpdb->query( "ALTER TABLE {$table} ADD KEY checkout_uuid (checkout_uuid)" ) ) ) {
		error_log( 'CER checkout index unavailable: ' . $wpdb->last_error );
		return;
	}
	update_option( 'cer_group_ticket_schema', '3', false );
}

function cer_checkout_registrations( int $registration_id, bool $lock = false ): array {
	global $wpdb;
	$table = $wpdb->prefix . 'evt_registrations';
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $registration_id ), ARRAY_A );
	if ( ! $row || $wpdb->last_error ) {
		throw new RuntimeException( 'The checkout registration could not be loaded. ' . $wpdb->last_error );
	}
	$where = empty( $row['checkout_uuid'] )
		? $wpdb->prepare( 'id = %d', $registration_id )
		: $wpdb->prepare( 'checkout_uuid = %s AND parent_registration_id IS NULL', $row['checkout_uuid'] );
	$rows = $wpdb->get_results( "SELECT * FROM {$table} WHERE {$where} ORDER BY id" . ( $lock ? ' FOR UPDATE' : '' ), ARRAY_A );
	if ( ! $rows || $wpdb->last_error ) {
		throw new RuntimeException( 'The checkout tickets could not be loaded. ' . $wpdb->last_error );
	}
	return $rows;
}

function cer_checkout_leader_id( int $registration_id ): int {
	$rows = cer_checkout_registrations( $registration_id );
	return (int) $rows[0]['id'];
}

function cer_update_checkout_payment_state( int $registration_id, string $payment_uuid, array $updates ): int {
	global $wpdb;
	$table = $wpdb->prefix . 'evt_registrations';
	if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
		throw new RuntimeException( 'Could not start the checkout state transaction.' );
	}
	try {
		$rows = cer_checkout_registrations( $registration_id, true );
		if ( $rows[0]['payment_uuid'] !== $payment_uuid || ! in_array( $rows[0]['status'], array( 'pending', 'awaiting_payment', 'failed' ), true ) ) {
			$changed = 0;
		} else {
			$changed = 0;
			foreach ( $rows as $row ) {
				if ( ! in_array( $row['status'], array( 'pending', 'awaiting_payment', 'failed' ), true ) ) {
					throw new RuntimeException( 'The checkout changed while payment was starting. Verify payment before retrying.' );
				}
				$result = $wpdb->update( $table, $updates, array( 'id' => $row['id'] ) );
				if ( false === $result ) {
					throw new RuntimeException( 'Could not save the checkout payment state. ' . $wpdb->last_error );
				}
				$changed += $result;
			}
		}
		if ( false === $wpdb->query( 'COMMIT' ) ) {
			throw new RuntimeException( 'Could not confirm the checkout payment state.' );
		}
		return $changed;
	} catch ( Throwable $error ) {
		$wpdb->query( 'ROLLBACK' );
		throw $error;
	}
}

function cer_parse_ticket_quantity( $value ) {
	if ( ! ( is_string( $value ) || is_int( $value ) ) || ! preg_match( '/^(?:[1-9]|1[0-9]|20)$/', (string) $value ) ) {
		return new WP_Error( 'cer_quantity', __( 'Choose between 1 and 20 tickets.', 'custom-event-registration' ) );
	}
	return (int) $value;
}

function cer_ticket_admissions( array $row ): int {
	return max( 0, (int) ( $row['admission_quantity'] ?? 1 ) );
}

function cer_registration_ticket_is_valid( array $row ): bool {
	if ( ! empty( $row['deleted_at'] ) || cer_ticket_admissions( $row ) < 1
		|| ! ( 'paid' === $row['status'] || ( 'confirmed' === $row['status'] && (float) $row['amount'] <= 0 ) ) ) {
		return false;
	}
	global $wpdb;
	$event_exists = $wpdb->get_var( $wpdb->prepare(
		"SELECT id FROM {$wpdb->prefix}evt_events WHERE id = %d AND deleted_at IS NULL", $row['event_id'] ?? 0
	) );
	if ( $wpdb->last_error ) {
		error_log( 'CER ticket event validation failed: ' . $wpdb->last_error );
		return false;
	}
	if ( ! $event_exists ) {
		return false;
	}
	if ( ! empty( $row['parent_registration_id'] ) ) {
		$parent = $wpdb->get_row( $wpdb->prepare(
			"SELECT status, amount, deleted_at FROM {$wpdb->prefix}evt_registrations WHERE id = %d AND event_id = %d",
			$row['parent_registration_id'], $row['event_id']
		), ARRAY_A );
		if ( $wpdb->last_error ) {
			error_log( 'CER parent ticket validation failed: ' . $wpdb->last_error );
		}
		return $parent && empty( $parent['deleted_at'] )
			&& ( 'paid' === $parent['status'] || ( 'confirmed' === $parent['status'] && (float) $parent['amount'] <= 0 ) );
	}
	return true;
}

function cer_parse_group_attendees( string $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', trim( $text ) );
	$rows = array();
	foreach ( $lines as $index => $line ) {
		if ( '' === trim( $line ) ) {
			continue;
		}
		$fields = str_getcsv( $line, ',', '"', '' );
		$name = sanitize_text_field( $fields[0] ?? '' );
		$email = trim( $fields[1] ?? '' );
		if ( count( $fields ) < 1 || count( $fields ) > 2 || '' === $name || strlen( $name ) > 255 || ( '' !== $email && ! is_email( $email ) ) ) {
			return new WP_Error( 'cer_attendee_input', sprintf( __( 'Invalid attendee on line %d. Use Name,email (email may be blank).', 'custom-event-registration' ), $index + 1 ) );
		}
		$rows[] = array( 'full_name' => $name, 'email' => $email );
	}
	if ( ! $rows || count( $rows ) > 20 ) {
		return new WP_Error( 'cer_attendee_count', __( 'Enter between 1 and 20 attendees.', 'custom-event-registration' ) );
	}
	return $rows;
}

function cer_allocate_group_tickets( int $parent_id, string $text, string $expected_key, bool $deliver = true ) {
	global $wpdb;
	$attendees = cer_parse_group_attendees( $text );
	if ( is_wp_error( $attendees ) ) {
		return $attendees;
	}
	$table = $wpdb->prefix . 'evt_registrations';
	$lock = 'cer_ticket_' . md5( $wpdb->prefix . $parent_id );
	if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(SHA2(CONCAT(DATABASE(), %s), 256), 0)', $lock ) ) ) {
		return new WP_Error( 'cer_group_locked', __( 'This group ticket is being processed. Try again shortly.', 'custom-event-registration' ) );
	}
	$ids = array();
	try {
		try {
			cer_require_transactional_event_tables( array( $table ) );
			if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
				throw new RuntimeException( 'Could not begin ticket allocation.' );
			}
			$parent = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d AND deleted_at IS NULL FOR UPDATE", $parent_id ), ARRAY_A );
			if ( $wpdb->last_error || ! $parent || ! empty( $parent['parent_registration_id'] ) || (int) $parent['ticket_quantity'] <= 1
				|| ! cer_registration_ticket_is_valid( $parent )
				|| ! hash_equals( (string) $parent['user_access_key'], $expected_key ) ) {
				throw new RuntimeException( 'The confirmed group ticket changed or is unavailable. Reload before allocating.' );
			}
			if ( count( $attendees ) > cer_ticket_admissions( $parent ) ) {
				throw new RuntimeException( 'There are fewer unallocated seats than the supplied attendees.' );
			}
			$allocated = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE parent_registration_id = %d", $parent_id ) );
			if ( $wpdb->last_error || $allocated + cer_ticket_admissions( $parent ) !== (int) $parent['ticket_quantity'] ) {
				throw new RuntimeException( 'Group seat accounting needs review before further allocation.' );
			}
			$buyer_email = cer_decrypt_pii( $parent['email'] );
			if ( ! is_email( $buyer_email ) ) {
				throw new RuntimeException( 'The purchaser email is invalid.' );
			}
			foreach ( $attendees as $attendee ) {
				$data = array(
					'registration_uuid' => wp_generate_uuid4(), 'payment_uuid' => wp_generate_uuid4(),
					'parent_registration_id' => $parent_id, 'event_id' => $parent['event_id'],
					'ticket_type_id' => $parent['ticket_type_id'], 'ticket_type' => $parent['ticket_type'],
					'ticket_display_title' => $parent['ticket_display_title'] ?? null,
					'ticket_display_color' => $parent['ticket_display_color'] ?? null,
					'ticket_quantity' => 1, 'admission_quantity' => 1,
					'full_name' => $attendee['full_name'], 'email' => $attendee['email'] ?: $buyer_email,
					'phone' => '', 'payment_method' => 'none', 'amount' => 0, 'status' => 'confirmed',
					'notes' => 'Seat allocated from purchase #' . $parent_id . ' by administrator #' . get_current_user_id(),
					'created_at' => current_time( 'mysql' ), 'updated_at' => current_time( 'mysql' ),
				);
				if ( false === $wpdb->insert( $table, cer_prepare_registration_row( $data ) ) ) {
					throw new RuntimeException( 'The attendee ticket could not be saved.' );
				}
				$ids[] = (int) $wpdb->insert_id;
			}
			$remaining = cer_ticket_admissions( $parent ) - count( $ids );
			if ( false === $wpdb->update( $table, array(
				'admission_quantity' => $remaining, 'user_access_key' => bin2hex( random_bytes( 32 ) ),
				'ticket_sent_at' => null, 'ticket_generated_at' => null, 'updated_at' => current_time( 'mysql' ),
			), array( 'id' => $parent_id ) ) || false === $wpdb->query( 'COMMIT' ) ) {
				throw new RuntimeException( 'Ticket allocation could not be confirmed. Reload and inspect before retrying.' );
			}
		} catch ( Throwable $error ) {
			$wpdb->query( 'ROLLBACK' );
			error_log( 'CER group allocation failed: ' . $error->getMessage() . ' ' . $wpdb->last_error );
			return new WP_Error( 'cer_group_allocation', $error->getMessage() );
		}
		$old_path = cer_ticket_pdf_path( $parent );
		if ( $old_path && is_file( $old_path ) && ! unlink( $old_path ) ) {
			error_log( 'CER obsolete group PDF could not be removed for ' . $parent_id );
		}
		if ( ! $deliver ) {
			delete_transient( 'cer_dashboard_metrics' );
			return array( 'ids' => $ids, 'remaining' => $remaining, 'failed' => array(), 'notice_failed' => false );
		}
		$failed = array();
		foreach ( $ids as $id ) {
			if ( ! cer_send_ticket_for_registration( $id ) ) {
				$failed[] = $id;
			}
		}
		if ( $remaining > 0 && ! cer_deliver_ticket_for_registration( $parent_id ) ) {
			$failed[] = $parent_id;
		}
		$notice_failed = false;
		if ( 0 === $remaining ) {
			$sender = cer_get_event_reply_to( (int) $parent['event_id'] );
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			if ( '' !== $sender ) { $headers[] = 'Reply-To: ' . $sender; }
			cer_set_current_mail_config( (int) $parent['event_id'], '' );
			try {
				$notice_failed = ! wp_mail( $buyer_email, 'Group ticket replaced with individual tickets',
					'<p>All seats in group purchase #' . $parent_id . ' now have individual tickets. The previous group ticket and its code are invalid. Guests must bring their individual tickets for admission. If you did not receive an individual ticket, reply to the event team.</p>', $headers );
			} finally {
				cer_clear_current_mail_config();
			}
			if ( $notice_failed ) {
				error_log( 'CER group replacement notice failed for purchase ' . $parent_id );
			}
		}
		delete_transient( 'cer_dashboard_metrics' );
		return array( 'ids' => $ids, 'remaining' => $remaining, 'failed' => $failed, 'notice_failed' => $notice_failed );
	} finally {
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(SHA2(CONCAT(DATABASE(), %s), 256))', $lock ) );
	}
}

function cer_handle_group_allocation(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'custom-event-registration' ), '', array( 'response' => 403 ) );
	}
	$id = absint( $_POST['registration_id'] ?? 0 );
	check_admin_referer( 'cer_allocate_group_' . $id );
	$result = cer_allocate_group_tickets( $id, wp_unslash( $_POST['attendees'] ?? '' ), sanitize_text_field( wp_unslash( $_POST['group_key'] ?? '' ) ) );
	$message = is_wp_error( $result ) ? $result->get_error_message() : sprintf(
		__( '%1$d individual tickets issued; %2$d seats remain on the replacement group ticket.', 'custom-event-registration' ),
		count( $result['ids'] ), $result['remaining']
	);
	if ( ! is_wp_error( $result ) && $result['failed'] ) {
		$message .= ' ' . __( 'Some ticket emails failed. Use Send Missing Tickets for these IDs:', 'custom-event-registration' ) . ' ' . implode( ', ', $result['failed'] );
	}
	if ( ! is_wp_error( $result ) && $result['notice_failed'] ) {
		$message .= ' ' . __( 'The purchaser replacement notice failed. Send it manually through the event email composer without a ticket attachment.', 'custom-event-registration' );
	}
	set_transient( 'cer_assignment_notice_' . get_current_user_id(), array( 'message' => $message, 'error' => is_wp_error( $result ) || ! empty( $result['failed'] ) || ! empty( $result['notice_failed'] ) ), 60 );
	wp_safe_redirect( add_query_arg( array( 'page' => 'cer-registrations', 'cer_notice' => 'ticket_assigned' ), admin_url( 'admin.php' ) ) );
	exit;
}
