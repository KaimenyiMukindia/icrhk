<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( did_action( 'plugins_loaded' ) ) {
	cer_install_admin_ticket_schema();
} else {
	add_action( 'plugins_loaded', 'cer_install_admin_ticket_schema' );
}
add_action( 'admin_post_cer_assign_ticket', 'cer_handle_admin_ticket_assignment' );
add_action( 'wp_ajax_cer_admin_assignment_inventory', 'cer_ajax_admin_assignment_inventory' );

function cer_ajax_admin_assignment_inventory(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'Permission denied.' ), 403 );
	}
	check_ajax_referer( 'cer_assignment_inventory' );
	nocache_headers();
	$event_id = absint( $_GET['event_id'] ?? 0 );
	$data = cer_get_ticket_availability( $event_id );
	if ( is_wp_error( $data ) ) {
		wp_send_json_error( array( 'message' => $data->get_error_message() ), 503 );
	}
	global $wpdb;
	$prices = $wpdb->get_results( $wpdb->prepare( "SELECT id, price FROM {$wpdb->prefix}evt_ticket_types WHERE event_id = %d AND deleted_at IS NULL", $event_id ), OBJECT_K );
	if ( $wpdb->last_error ) {
		error_log( 'CER assignment prices failed: ' . $wpdb->last_error );
		wp_send_json_error( array( 'message' => 'Ticket prices could not be loaded.' ), 503 );
	}
	foreach ( $data['tickets'] as &$item ) {
		if ( ! isset( $prices[ $item['id'] ] ) ) {
			wp_send_json_error( array( 'message' => 'Inventory changed while loading. Retry.' ), 409 );
		}
		$item['price'] = (string) $prices[ $item['id'] ]->price;
	}
	unset( $item );
	wp_send_json_success( $data );
}

function cer_get_admin_assignment_inventory(): array {
	global $wpdb;
	$events = $wpdb->get_results( "SELECT id, name, max_attendees FROM {$wpdb->prefix}evt_events WHERE status = 'published' AND deleted_at IS NULL ORDER BY name" );
	if ( $wpdb->last_error ) { throw new RuntimeException( 'Assignment events could not be loaded. ' . $wpdb->last_error ); }
	$tickets = $wpdb->get_results( "SELECT t.* FROM {$wpdb->prefix}evt_ticket_types t INNER JOIN {$wpdb->prefix}evt_events e ON e.id = t.event_id WHERE e.status = 'published' AND e.deleted_at IS NULL AND t.deleted_at IS NULL ORDER BY t.order_index, t.id" );
	if ( $wpdb->last_error ) { throw new RuntimeException( 'Assignment tickets could not be loaded. ' . $wpdb->last_error ); }
	$occupancy = $wpdb->get_results( "SELECT r.event_id, r.ticket_type_id, SUM(r.ticket_quantity) AS occupied FROM {$wpdb->prefix}evt_registrations r INNER JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id WHERE e.status = 'published' AND e.deleted_at IS NULL AND r.parent_registration_id IS NULL AND (r.status = 'paid' OR (r.status = 'confirmed' AND r.amount <= 0) OR (r.status IN ('pending', 'awaiting_payment', 'failed') AND r.reserved_until IS NOT NULL)) GROUP BY r.event_id, r.ticket_type_id", ARRAY_A );
	if ( $wpdb->last_error ) { throw new RuntimeException( 'Assignment capacity could not be loaded. ' . $wpdb->last_error ); }
	$by_event = array();
	$used = array();
	$prices = array();
	foreach ( $tickets as $ticket ) {
		$by_event[ $ticket->event_id ][] = $ticket;
		$prices[ $ticket->id ] = (string) $ticket->price;
	}
	foreach ( $occupancy as $row ) { $used[ $row['event_id'] ][] = $row; }
	$result = array();
	foreach ( $events as $event ) {
		$data = cer_build_ticket_availability( $event, $by_event[ $event->id ] ?? array(), $used[ $event->id ] ?? array() );
		foreach ( $data['tickets'] as &$item ) {
			$item['price'] = $prices[ $item['id'] ];
		}
		unset( $item );
		$result[ $event->id ] = array( 'name' => $event->name, 'availability' => $data );
	}
	return $result;
}

function cer_parse_admin_assignment_lines( array $input ) {
	$raw = $input['tickets'] ?? array( (string) ( $input['ticket_type_id'] ?? 0 ) => array( 'quantity' => $input['ticket_quantity'] ?? '1', 'amount' => $input['amount'] ?? '' ) );
	if ( ! is_array( $raw ) || count( $raw ) > 20 ) {
		return new WP_Error( 'cer_assignment_lines', __( 'Choose valid ticket quantities and prices.', 'custom-event-registration' ) );
	}
	$lines = array();
	$total_quantity = 0;
	foreach ( $raw as $ticket_id => $line ) {
		if ( ! is_array( $line ) || ! ctype_digit( (string) $ticket_id ) || (int) $ticket_id < 1 || (string) (int) $ticket_id !== (string) $ticket_id ) {
			return new WP_Error( 'cer_assignment_lines', __( 'The ticket selection is invalid.', 'custom-event-registration' ) );
		}
		$value = $line['quantity'] ?? '';
		if ( '0' === $value || 0 === $value ) { continue; }
		$quantity = cer_parse_ticket_quantity( $value );
		if ( ! is_string( $line['amount'] ?? null ) && ! is_int( $line['amount'] ?? null ) ) {
			return new WP_Error( 'cer_assignment_lines', __( 'Choose a valid per-seat price.', 'custom-event-registration' ) );
		}
		$price = trim( (string) ( $line['amount'] ?? '' ) );
		if ( is_wp_error( $quantity ) || ! preg_match( '/^\d{1,8}(?:\.\d{1,2})?$/', $price ) ) {
			return new WP_Error( 'cer_assignment_lines', __( 'Choose whole ticket quantities and valid per-seat prices.', 'custom-event-registration' ) );
		}
		$parts = explode( '.', $price );
		$cents = (int) $parts[0] * 100 + (int) str_pad( $parts[1] ?? '', 2, '0' );
		$total_quantity += $quantity;
		$lines[ (int) $ticket_id ] = array( 'quantity' => $quantity, 'cents' => $cents * $quantity );
	}
	if ( ! $lines || $total_quantity > 20 ) {
		return new WP_Error( 'cer_assignment_lines', __( 'Choose between 1 and 20 seats in total.', 'custom-event-registration' ) );
	}
	ksort( $lines, SORT_NUMERIC );
	return $lines;
}

function cer_install_admin_ticket_schema(): void {
	if ( '1' === get_option( 'cer_admin_ticket_schema', '' ) ) {
		return;
	}
	global $wpdb;
	$table = $wpdb->prefix . 'evt_registrations';
	$columns = array(
		'ticket_display_title' => 'VARCHAR(255) NULL',
		'ticket_display_color' => 'VARCHAR(7) NULL',
		'payment_checkout_url' => 'TEXT NULL',
	);
	foreach ( $columns as $column => $definition ) {
		cer_add_column_if_missing( $table, $column, $definition );
		if ( ! cer_column_exists( $table, $column ) ) {
			error_log( 'CER admin ticket schema unavailable: ' . $column . ' ' . $wpdb->last_error );
			return;
		}
	}
	update_option( 'cer_admin_ticket_schema', '1', false );
}

function cer_assign_admin_ticket( array $input ) {
	global $wpdb;
	$event_id = absint( $input['event_id'] ?? 0 );
	$existing_id = absint( $input['registration_id'] ?? 0 );
	$lines = cer_parse_admin_assignment_lines( $input );
	if ( is_wp_error( $lines ) ) { return $lines; }
	$total_quantity = array_sum( array_column( $lines, 'quantity' ) );
	$total_cents = array_sum( array_column( $lines, 'cents' ) );
	$name = sanitize_text_field( $input['full_name'] ?? '' );
	$email = sanitize_email( $input['email'] ?? '' );
	$phone = preg_replace( '/\D+/', '', (string) ( $input['phone'] ?? '' ) );
	if ( preg_match( '/^0?[17]\d{8}$/', $phone ) ) {
		$phone = '254' . ( '0' === $phone[0] ? substr( $phone, 1 ) : $phone );
	}
	$title = sanitize_text_field( $input['ticket_title'] ?? '' );
	$color_input = trim( (string) ( $input['ticket_color'] ?? '' ) );
	$color = '' === $color_input ? '' : sanitize_hex_color( $color_input );
	$reason = sanitize_textarea_field( $input['reason'] ?? '' );
	$method = sanitize_key( $input['payment_method'] ?? '' );
	if ( ! $event_id || strlen( $title ) > 255 || '' === $reason || ( '' !== $color_input && ! $color ) || ! in_array( $method, array( 'offline', 'mpesa', 'card' ), true ) || ( $existing_id && count( $lines ) > 1 ) ) {
		return new WP_Error( 'cer_assignment_input', __( 'Choose an event and ticket, a valid amount, payment method and reason.', 'custom-event-registration' ) );
	}
	$amount = number_format( $total_cents / 100, 2, '.', '' );
	if ( (float) $amount > 99999999.99 ) {
		return new WP_Error( 'cer_assignment_total', __( 'The total exceeds the supported payment amount. Reduce the quantity or per-ticket price.', 'custom-event-registration' ) );
	}
	if ( (float) $amount > 0 && (float) $amount < 1 && 'offline' !== $method ) {
		return new WP_Error( 'cer_assignment_amount', __( 'Gateway payments require at least KES 1.', 'custom-event-registration' ) );
	}
	if ( (float) $amount > 0 && 'offline' === $method && empty( $input['offline_confirmed'] ) ) {
		return new WP_Error( 'cer_assignment_offline', __( 'Confirm that the offline payment was received before issuing the ticket.', 'custom-event-registration' ) );
	}
	$registrations = $wpdb->prefix . 'evt_registrations';
	$events = $wpdb->prefix . 'evt_events';
	$tickets = $wpdb->prefix . 'evt_ticket_types';
	$assigned = array();
	$checkout_uuid = count( $lines ) > 1 ? wp_generate_uuid4() : null;
	$gateway = $total_cents > 0 && 'offline' !== $method;
	try {
		cer_require_transactional_event_tables( array( $registrations, $events, $tickets ) );
		if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
			throw new RuntimeException( 'Unable to start the assignment transaction.' );
		}
		$event = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, max_attendees FROM {$events} WHERE id = %d AND status = 'published' AND deleted_at IS NULL FOR UPDATE", $event_id ), ARRAY_A );
		if ( $wpdb->last_error || ! $event ) {
			throw new RuntimeException( 'The published event is unavailable.' );
		}
		$existing = null;
		if ( $existing_id ) {
			$existing = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$registrations} WHERE id = %d AND event_id = %d AND deleted_at IS NULL FOR UPDATE", $existing_id, $event_id ), ARRAY_A );
			if ( ! $existing || 'pending' !== $existing['status'] || (float) $existing['amount'] > 0 || ! empty( $existing['reserved_until'] ) || ! empty( $existing['gateway_reference'] ) || ! empty( $existing['ticket_sent_at'] ) || 'none' !== $existing['payment_method'] ) {
				throw new RuntimeException( 'Only an unconfirmed, no-payment registration without a reservation or ticket can be assigned. Existing payment attempts must be reviewed separately.' );
			}
			$name = cer_decrypt_pii( $existing['full_name'] );
			$email = cer_decrypt_pii( $existing['email'] );
			$phone = cer_decrypt_pii( $existing['phone'] );
		}
		if ( '' === $name || ! is_email( $email ) || ( (float) $amount > 0 && 'mpesa' === $method && ! preg_match( '/^254[17]\d{8}$/', $phone ) ) ) {
			throw new RuntimeException( 'A name, valid email and, for M-Pesa, a valid Kenyan phone number are required.' );
		}
		$occupied = (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COALESCE(SUM(ticket_quantity), 0) FROM {$registrations} WHERE parent_registration_id IS NULL AND event_id = %d AND (status = 'paid' OR (status = 'confirmed' AND amount <= 0) OR (status IN ('pending', 'awaiting_payment', 'failed') AND reserved_until IS NOT NULL))",
			$event_id
		) );
		if ( $wpdb->last_error || ( ! empty( $event['max_attendees'] ) && $occupied + $total_quantity > (int) $event['max_attendees'] ) ) {
			throw new RuntimeException( 'Event capacity is unavailable or has been reached.' );
		}
		foreach ( $lines as $ticket_id => $line ) {
			$quantity = $line['quantity'];
			$line_amount = number_format( $line['cents'] / 100, 2, '.', '' );
			$ticket = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, quantity_available, quantity_sold, is_maintenance FROM {$tickets} WHERE id = %d AND event_id = %d AND deleted_at IS NULL FOR UPDATE", $ticket_id, $event_id ), ARRAY_A );
			if ( $wpdb->last_error || ! $ticket || ! empty( $ticket['is_maintenance'] ) ) {
				throw new RuntimeException( 'The selected ticket is unavailable for this event.' );
			}
			$duplicate = $wpdb->get_var( $wpdb->prepare(
				"SELECT id FROM {$registrations} WHERE event_id = %d AND ticket_type_id = %d AND id <> %d AND deleted_at IS NULL AND (email_search_hash = %s OR LOWER(email) = %s) AND (status = 'paid' OR (status = 'confirmed' AND amount <= 0) OR (status IN ('pending', 'awaiting_payment') AND reserved_until IS NOT NULL)) LIMIT 1",
				$event_id, $ticket_id, $existing_id, cer_registration_search_hash( $email ), strtolower( $email )
			) );
			if ( $wpdb->last_error || $duplicate ) {
				throw new RuntimeException( 'A matching active registration already exists or could not be checked.' );
			}
			if ( null !== $ticket['quantity_available'] && (int) $ticket['quantity_sold'] + $quantity > (int) $ticket['quantity_available'] ) {
				throw new RuntimeException( 'The event or selected ticket has reached capacity.' );
			}
			$data = array(
				'event_id' => $event_id, 'ticket_type_id' => $ticket_id, 'ticket_type' => $ticket['name'],
				'ticket_display_title' => $title, 'ticket_display_color' => $color ?: null,
				'checkout_uuid' => $checkout_uuid,
				'payment_method' => $gateway ? $method : ( $line['cents'] <= 0 ? 'none' : $method ), 'amount' => $line_amount,
				'confirmed_amount' => $line['cents'] > 0 && ! $gateway ? $line_amount : null,
				'ticket_quantity' => $quantity, 'admission_quantity' => $quantity,
				'status' => $gateway ? 'pending' : ( $line['cents'] > 0 ? 'paid' : 'confirmed' ),
				'reserved_until' => $gateway ? gmdate( 'Y-m-d H:i:s', time() + cer_get_registration_hold_seconds() ) : null,
				'notes' => 'Admin assignment by user #' . get_current_user_id() . ': ' . $reason,
				'updated_at' => current_time( 'mysql' ),
			);
			if ( $existing ) {
				$data['notes'] = trim( cer_decrypt_pii( $existing['notes'] ?? '' ) . "\n" . $data['notes'] );
				$data['payment_uuid'] = wp_generate_uuid4();
				$data['user_access_key'] = $existing['user_access_key'];
				$result = $wpdb->update( $registrations, cer_prepare_registration_row( $data ), array( 'id' => $existing_id ) );
				$id = $existing_id;
				$registration_uuid = $existing['registration_uuid'];
			} else {
				$data['registration_uuid'] = wp_generate_uuid4();
				$data['payment_uuid'] = wp_generate_uuid4();
				$data['full_name'] = $name;
				$data['email'] = $email;
				$data['phone'] = $phone;
				$data['created_at'] = current_time( 'mysql' );
				$registration_uuid = $data['registration_uuid'];
				$result = $wpdb->insert( $registrations, cer_prepare_registration_row( $data ) );
				$id = (int) $wpdb->insert_id;
			}
			if ( false === $result || ! $id || false === $wpdb->update( $tickets, array( 'quantity_sold' => (int) $ticket['quantity_sold'] + $quantity, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $ticket_id ) ) ) {
				throw new RuntimeException( 'The registration or ticket inventory could not be saved.' );
			}
			$assigned[] = array( 'id' => $id, 'registration_uuid' => $registration_uuid, 'payment_uuid' => $data['payment_uuid'], 'ticket_id' => $ticket_id );
		}
		if ( false === $wpdb->query( 'COMMIT' ) ) {
			throw new RuntimeException( 'The assignment transaction could not be confirmed. Check the registration before retrying.' );
		}
	} catch ( Throwable $exception ) {
		$wpdb->query( 'ROLLBACK' );
		error_log( 'CER admin ticket assignment failed: ' . $exception->getMessage() . ' ' . $wpdb->last_error );
		return new WP_Error( 'cer_assignment_failed', $exception->getMessage() );
	}
	delete_transient( 'cer_dashboard_metrics' );
	$leader = $assigned[0];
	if ( ! $gateway ) {
		$failed = array();
		foreach ( $assigned as $row ) {
			if ( ! cer_send_ticket_for_registration( $row['id'] ) ) { $failed[] = $row['id']; }
		}
		return array( 'id' => $leader['id'], 'ids' => array_column( $assigned, 'id' ), 'message' => $failed ? 'Tickets assigned, but some email deliveries failed. Use Send Missing Tickets for IDs: ' . implode( ', ', $failed ) : 'Tickets assigned and emailed.', 'error' => ! empty( $failed ) );
	}
	$result = cer_start_admin_payment( $leader['id'], $leader['registration_uuid'], $leader['payment_uuid'], $name, $email, $phone, $amount, $method, $event_id, $leader['ticket_id'], $event['name'], ! empty( $input['email_checkout'] ) );
	$result['ids'] = array_column( $assigned, 'id' );
	return $result;
}

function cer_start_admin_payment( int $id, string $registration_uuid, string $payment_uuid, string $name, string $email, string $phone, string $amount, string $method, int $event_id, int $ticket_id, string $event_name, bool $email_checkout ): array {
	global $wpdb;
	$registrations = $wpdb->prefix . 'evt_registrations';
	try {
		$response = cer_initiate_laravel_payment( array(
			'registration_uuid' => $registration_uuid, 'payment_uuid' => $payment_uuid, 'full_name' => $name,
			'email' => $email, 'phone' => $phone, 'amount' => $amount, 'currency' => 'KES',
			'payment_method' => $method, 'event_id' => $event_id, 'ticket_type_id' => $ticket_id,
		) );
		if ( empty( $response['reference'] ) || ( 'card' === $method && empty( $response['authorization_url'] ) ) ) {
			throw new RuntimeException( 'The gateway did not return the required checkout details.' );
		}
		$checkout_url = '';
		if ( 'card' === $method ) {
			$checkout_url = esc_url_raw( $response['authorization_url'] );
			if ( 'https' !== wp_parse_url( $checkout_url, PHP_URL_SCHEME ) || 'checkout.paystack.com' !== strtolower( (string) wp_parse_url( $checkout_url, PHP_URL_HOST ) ) ) {
				throw new RuntimeException( 'The gateway returned an invalid checkout URL.' );
			}
		}
		$updated = $wpdb->query( $wpdb->prepare(
			"UPDATE {$registrations} SET gateway_reference = %s, payment_checkout_url = %s, updated_at = %s WHERE id = %d AND payment_uuid = %s AND status = 'pending'",
			$response['reference'], $checkout_url, current_time( 'mysql' ), $id, $payment_uuid
		) );
		if ( false === $updated ) {
			throw new RuntimeException( 'Payment started but its checkout details could not be saved. Verify payment before retrying.' );
		}
		cer_update_checkout_payment_state( $id, $payment_uuid, array(
			'gateway_reference' => $response['reference'], 'payment_checkout_url' => $checkout_url,
			'status' => 'pending', 'updated_at' => current_time( 'mysql' ),
		) );
		if ( 0 === $updated ) {
			$current_status = $wpdb->get_var( $wpdb->prepare( "SELECT status FROM {$registrations} WHERE id = %d", $id ) );
			if ( 'paid' === $current_status ) {
				return array( 'id' => $id, 'message' => 'Payment was confirmed during checkout initiation. Check ticket delivery before taking further action.' );
			}
			throw new RuntimeException( 'The registration changed while payment was starting. Verify payment before retrying.' );
		}
		$message = 'Registration assigned. Payment is pending; verify payment before sending a ticket or retrying.';
		$mail_failed = false;
		if ( $checkout_url && $email_checkout ) {
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$reply_to = cer_get_event_reply_to( $event_id );
			if ( $reply_to ) {
				$headers[] = 'Reply-To: ' . $reply_to;
			}
			cer_set_current_mail_config( $event_id, $event_name );
			try {
				$sent = wp_mail( $email, 'Complete your registration for ' . $event_name, '<p>Dear ' . esc_html( $name ) . ',</p><p>Your assigned ticket price is KES ' . esc_html( $amount ) . '.</p><p><a href="' . esc_url( $checkout_url ) . '">Complete secure card payment</a>.</p>', $headers );
			} finally {
				cer_clear_current_mail_config();
			}
			$message .= $sent ? ' Checkout link emailed.' : ' Checkout email failed; share the saved link manually.';
			$mail_failed = ! $sent;
		}
		return array( 'id' => $id, 'message' => $message, 'error' => $mail_failed );
	} catch ( Throwable $exception ) {
		error_log( 'CER admin payment initiation failed for registration ' . $id . ': ' . $exception->getMessage() );
		$failed = $wpdb->query( $wpdb->prepare( "UPDATE {$registrations} SET status = 'failed', updated_at = %s WHERE id = %d AND payment_uuid = %s AND status = 'pending'", current_time( 'mysql' ), $id, $payment_uuid ) );
		if ( 1 === $failed ) {
			cer_record_payment_error( $id, $exception->getMessage() );
		}
		try {
			cer_update_checkout_payment_state( $id, $payment_uuid, array(
				'status' => 'failed', 'payment_error' => sanitize_textarea_field( $exception->getMessage() ), 'updated_at' => current_time( 'mysql' ),
			) );
		} catch ( Throwable $state_error ) {
			error_log( 'CER admin checkout failure state could not be saved: ' . $state_error->getMessage() );
		}
		return array( 'id' => $id, 'message' => 'Registration saved, but payment initiation failed or its outcome is uncertain: ' . $exception->getMessage() . ' Verify the payment before retrying.', 'error' => true );
	}
}

function cer_handle_admin_ticket_assignment(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'custom-event-registration' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'cer_assign_ticket' );
	$result = cer_assign_admin_ticket( wp_unslash( $_POST ) );
	set_transient( 'cer_assignment_notice_' . get_current_user_id(), array(
		'message' => is_wp_error( $result ) ? $result->get_error_message() : '#' . $result['id'] . ': ' . $result['message'],
		'error' => is_wp_error( $result ) || ! empty( $result['error'] ),
	), 60 );
	wp_safe_redirect( add_query_arg( array( 'page' => 'cer-registrations', 'cer_notice' => 'ticket_assigned' ), admin_url( 'admin.php' ) ) );
	exit;
}
