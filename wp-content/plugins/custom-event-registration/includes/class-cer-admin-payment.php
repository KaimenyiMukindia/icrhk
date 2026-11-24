<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( did_action( 'plugins_loaded' ) ) {
	cer_install_payment_diagnostics_schema();
} else {
	add_action( 'plugins_loaded', 'cer_install_payment_diagnostics_schema' );
}
add_action( 'admin_post_cer_verify_payment', 'cer_handle_admin_payment_verification' );
add_action( 'admin_post_cer_retry_payment', 'cer_handle_admin_payment_retry' );

function cer_install_payment_diagnostics_schema(): void {
	if ( '1' === get_option( 'cer_payment_diagnostics_schema', '' ) ) {
		return;
	}
	global $wpdb;
	$table = $wpdb->prefix . 'evt_registrations';
	cer_add_column_if_missing( $table, 'payment_error', 'TEXT NULL' );
	if ( cer_column_exists( $table, 'payment_error' ) ) {
		update_option( 'cer_payment_diagnostics_schema', '1', false );
	} else {
		error_log( 'CER payment diagnostics schema unavailable: ' . $wpdb->last_error );
	}
}

function cer_record_payment_error( int $registration_id, string $message ): void {
	global $wpdb;
	if ( false === $wpdb->update(
		$wpdb->prefix . 'evt_registrations',
		array( 'payment_error' => sanitize_textarea_field( $message ) ),
		array( 'id' => $registration_id, 'status' => 'failed' ),
		array( '%s' ),
		array( '%d', '%s' )
	) ) {
		error_log( 'CER payment diagnostic could not be saved for registration ' . $registration_id . ': ' . $wpdb->last_error );
	}
}

function cer_request_payment_verification( string $reference ) {
	if ( '' === trim( (string) $reference ) ) {
		return new WP_Error( 'cer_payment_reference', __( 'No payment reference is available. Check the gateway before attempting another payment.', 'custom-event-registration' ) );
	}
	$url = defined( 'CER_LARAVEL_VERIFY_URL' ) ? CER_LARAVEL_VERIFY_URL : home_url( '/laravel-engine/public/api/payment/verify' );
	$response = wp_remote_post( esc_url_raw( $url ), array(
		'headers' => array( 'Content-Type' => 'application/json; charset=utf-8' ),
		'body' => wp_json_encode( array( 'reference' => $reference ) ),
		'timeout' => 90,
	) );
	if ( is_wp_error( $response ) ) {
		error_log( 'CER admin payment verification failed: ' . $response->get_error_message() );
		return new WP_Error( 'cer_payment_unavailable', __( 'The payment service could not be reached. Do not retry the charge until the gateway status is known.', 'custom-event-registration' ) );
	}
	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( wp_remote_retrieve_response_code( $response ) < 200 || wp_remote_retrieve_response_code( $response ) >= 300 || ! is_array( $data ) ) {
		$message = is_array( $data ) && ! empty( $data['message'] ) ? sanitize_text_field( $data['message'] ) : __( 'Payment verification failed.', 'custom-event-registration' );
		return new WP_Error( 'cer_payment_verification', $message . ' ' . __( 'Do not retry the charge until the gateway status is known.', 'custom-event-registration' ) );
	}
	return $data;
}

function cer_admin_verify_registration_payment( int $registration_id ) {
	global $wpdb;
	try {
		$registration_id = cer_checkout_leader_id( $registration_id );
	} catch ( Throwable $error ) {
		error_log( 'CER checkout verification lookup failed: ' . $error->getMessage() );
		return new WP_Error( 'cer_payment_lookup', __( 'The checkout could not be loaded.', 'custom-event-registration' ) );
	}
	$table = $wpdb->prefix . 'evt_registrations';
	$registration = $wpdb->get_row( $wpdb->prepare(
		"SELECT payment_uuid, gateway_reference, status FROM {$table} WHERE id = %d AND deleted_at IS NULL",
		$registration_id
	), ARRAY_A );
	if ( $wpdb->last_error ) {
		error_log( 'CER payment verification lookup failed: ' . $wpdb->last_error );
		return new WP_Error( 'cer_payment_lookup', __( 'The registration could not be loaded.', 'custom-event-registration' ) );
	}
	if ( ! $registration ) {
		return new WP_Error( 'cer_payment_missing', __( 'The active registration was not found.', 'custom-event-registration' ) );
	}
	$data = cer_request_payment_verification( (string) ( $registration['gateway_reference'] ?: $registration['payment_uuid'] ) );
	if ( is_wp_error( $data ) ) {
		return $data;
	}
	$status = sanitize_text_field( $data['status'] ?? 'unknown' );
	$detail = sanitize_text_field( $data['message'] ?? '' );
	$registration_status = sanitize_text_field( $data['registration_status'] ?? 'unknown' );
	return sprintf( __( 'Gateway status: %1$s. Registration status: %2$s.', 'custom-event-registration' ), $status, $registration_status )
		. ( '' !== $detail ? ' ' . $detail : '' )
		. ( 'success' === $status && 'paid' !== $registration_status ? ' ' . __( 'Payment was successful but admission was not confirmed; review capacity and logs.', 'custom-event-registration' ) : '' );
}

function cer_handle_admin_payment_verification(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'custom-event-registration' ), '', array( 'response' => 403 ) );
	}
	$id = absint( wp_unslash( $_POST['registration_id'] ?? 0 ) );
	check_admin_referer( 'cer_verify_payment_' . $id );
	$result = cer_admin_verify_registration_payment( $id );
	set_transient( 'cer_payment_verify_' . get_current_user_id(), array(
		'message' => is_wp_error( $result ) ? $result->get_error_message() : $result,
		'error' => is_wp_error( $result ),
	), 60 );
	wp_safe_redirect( add_query_arg( array( 'page' => 'cer-registrations', 'cer_notice' => 'payment_verified' ), admin_url( 'admin.php' ) ) );
	exit;
}

function cer_retry_registration_payment( int $registration_id ) {
	global $wpdb;
	try {
		$registration_id = cer_checkout_leader_id( $registration_id );
	} catch ( Throwable $error ) {
		error_log( 'CER checkout retry lookup failed: ' . $error->getMessage() );
		return new WP_Error( 'cer_retry_lookup', __( 'The checkout could not be loaded.', 'custom-event-registration' ) );
	}
	$lock = 'cer_payment_retry_' . md5( $wpdb->prefix . $registration_id );
	if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(SHA2(CONCAT(DATABASE(), %s), 256), 0)', $lock ) ) ) {
		return new WP_Error( 'cer_retry_locked', __( 'A payment retry is already being processed.', 'custom-event-registration' ) );
	}
	try {
		$registrations = $wpdb->prefix . 'evt_registrations';
		$events = $wpdb->prefix . 'evt_events';
		$tickets = $wpdb->prefix . 'evt_ticket_types';
		$before = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$registrations} WHERE id = %d AND deleted_at IS NULL", $registration_id ), ARRAY_A );
		if ( $wpdb->last_error || ! $before || ! in_array( $before['payment_method'], array( 'mpesa', 'card' ), true ) || ! in_array( $before['status'], array( 'pending', 'awaiting_payment', 'failed', 'expired' ), true ) ) {
			return new WP_Error( 'cer_retry_ineligible', __( 'This registration cannot be retried. Review its status and payment method.', 'custom-event-registration' ) );
		}
		$verified = cer_request_payment_verification( (string) ( $before['gateway_reference'] ?: $before['payment_uuid'] ) );
		if ( is_wp_error( $verified ) ) {
			return $verified;
		}
		$gateway_status = strtolower( (string) ( $verified['status'] ?? '' ) );
		if ( ! in_array( $gateway_status, array( 'failed', 'abandoned' ), true ) ) {
			return new WP_Error( 'cer_retry_not_final', sprintf( __( 'A new charge was not started: gateway status is %s. Only failed or abandoned payments can be retried.', 'custom-event-registration' ), $gateway_status ?: 'unknown' ) );
		}
		try {
			cer_require_transactional_event_tables( array( $registrations, $events, $tickets ) );
			if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
				throw new RuntimeException( 'Could not begin the retry transaction.' );
			}
			$event = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, max_attendees FROM {$events} WHERE id = %d AND status = 'published' AND deleted_at IS NULL FOR UPDATE", $before['event_id'] ), ARRAY_A );
			$checkout = cer_checkout_registrations( $registration_id, true );
			$current = $checkout[0];
			if ( $wpdb->last_error || ! $event || ! $current
				|| $current['payment_uuid'] !== $before['payment_uuid'] || $current['gateway_reference'] !== $before['gateway_reference']
				|| ! in_array( $current['status'], array( 'pending', 'awaiting_payment', 'failed', 'expired' ), true ) ) {
				throw new RuntimeException( 'The registration or ticket changed during verification. Verify again.' );
			}
			$ids = implode( ',', array_map( 'intval', array_column( $checkout, 'id' ) ) );
			$occupied = (int) $wpdb->get_var( $wpdb->prepare(
				"SELECT COALESCE(SUM(ticket_quantity), 0) FROM {$registrations} WHERE parent_registration_id IS NULL AND event_id = %d AND id NOT IN ({$ids}) AND (status = 'paid' OR (status = 'confirmed' AND amount <= 0) OR (status IN ('pending', 'awaiting_payment', 'failed') AND reserved_until IS NOT NULL))",
				$current['event_id']
			) );
			$quantity = array_sum( array_column( $checkout, 'ticket_quantity' ) );
			$checkout_amount = array_sum( array_column( $checkout, 'amount' ) );
			if ( $wpdb->last_error || $quantity < 1 || $quantity > 20 || $checkout_amount < 1 || ( ! empty( $event['max_attendees'] ) && $occupied + $quantity > (int) $event['max_attendees'] ) ) {
				throw new RuntimeException( 'Event capacity is unavailable.' );
			}
			$new_uuid = wp_generate_uuid4();
			foreach ( $checkout as $line ) {
				$ticket = $wpdb->get_row( $wpdb->prepare( "SELECT id, quantity_available, quantity_sold, is_maintenance FROM {$tickets} WHERE id = %d AND event_id = %d AND deleted_at IS NULL FOR UPDATE", $line['ticket_type_id'], $event['id'] ), ARRAY_A );
				if ( ! $ticket || $wpdb->last_error || ! empty( $ticket['is_maintenance'] ) || ! empty( $line['deleted_at'] ) || (int) $line['event_id'] !== (int) $event['id'] || ! in_array( $line['status'], array( 'pending', 'awaiting_payment', 'failed', 'expired' ), true ) ) {
					throw new RuntimeException( 'A checkout ticket is unavailable or changed. Verify payment before retrying.' );
				}
				$held = ! empty( $line['reserved_until'] );
				$line_quantity = (int) $line['ticket_quantity'];
				if ( $line_quantity < 1 || ( ! $held && null !== $ticket['quantity_available'] && (int) $ticket['quantity_sold'] + $line_quantity > (int) $ticket['quantity_available'] ) ) {
					throw new RuntimeException( 'Ticket inventory is unavailable.' );
				}
				if ( ! $held && false === $wpdb->update( $tickets, array( 'quantity_sold' => (int) $ticket['quantity_sold'] + $line_quantity ), array( 'id' => (int) $ticket['id'] ) ) ) {
					throw new RuntimeException( 'Ticket inventory could not be reserved.' );
				}
				if ( false === $wpdb->update( $registrations, array(
					'payment_uuid' => (int) $line['id'] === $registration_id ? $new_uuid : wp_generate_uuid4(),
					'gateway_reference' => null, 'payment_checkout_url' => null,
					'payment_error' => null, 'status' => 'pending',
					'reserved_until' => gmdate( 'Y-m-d H:i:s', time() + cer_get_registration_hold_seconds() ),
					'updated_at' => current_time( 'mysql' ),
				), array( 'id' => $line['id'] ) ) ) {
					throw new RuntimeException( 'The new payment attempt could not be recorded.' );
				}
			}
			if ( false === $wpdb->query( 'COMMIT' ) ) {
				throw new RuntimeException( 'The retry transaction could not be confirmed. Verify the registration before retrying.' );
			}
		} catch ( Throwable $exception ) {
			$wpdb->query( 'ROLLBACK' );
			error_log( 'CER payment retry reservation failed: ' . $exception->getMessage() . ' ' . $wpdb->last_error );
			return new WP_Error( 'cer_retry_reservation', $exception->getMessage() );
		}
		delete_transient( 'cer_dashboard_metrics' );
		return cer_start_admin_payment(
			$registration_id, $current['registration_uuid'], $new_uuid, cer_decrypt_pii( $current['full_name'] ),
			cer_decrypt_pii( $current['email'] ), cer_decrypt_pii( $current['phone'] ),
			number_format( $checkout_amount, 2, '.', '' ), $current['payment_method'],
			(int) $current['event_id'], (int) $current['ticket_type_id'], $event['name'], false
		);
	} finally {
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(SHA2(CONCAT(DATABASE(), %s), 256))', $lock ) );
	}
}

function cer_handle_admin_payment_retry(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'custom-event-registration' ), '', array( 'response' => 403 ) );
	}
	$id = absint( wp_unslash( $_POST['registration_id'] ?? 0 ) );
	check_admin_referer( 'cer_retry_payment_' . $id );
	$result = cer_retry_registration_payment( $id );
	set_transient( 'cer_payment_verify_' . get_current_user_id(), array(
		'message' => is_wp_error( $result ) ? $result->get_error_message() : $result['message'],
		'error' => is_wp_error( $result ) || ! empty( $result['error'] ),
	), 60 );
	wp_safe_redirect( add_query_arg( array( 'page' => 'cer-registrations', 'cer_notice' => 'payment_verified' ), admin_url( 'admin.php' ) ) );
	exit;
}
