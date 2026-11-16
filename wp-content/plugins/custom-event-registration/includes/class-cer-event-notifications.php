<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'cer_schedule_event_notifications' );
add_action( 'cer_event_notifications', 'cer_process_event_notifications' );

function cer_clear_event_notifications(): void {
	wp_clear_scheduled_hook( 'cer_event_notifications' );
}

function cer_schedule_event_notifications(): void {
	global $wpdb;
	if ( '1' !== get_option( 'cer_notification_schema_version', '' ) ) {
		$table = $wpdb->prefix . 'evt_registrations';
		foreach ( array( 'reminder_event_date', 'reminder_attempted_at', 'ticket_delivery_attempted_at' ) as $column ) {
			cer_add_column_if_missing( $table, $column, 'DATETIME NULL' );
			if ( ! cer_column_exists( $table, $column ) ) {
				error_log( 'CER notification schema unavailable: ' . $column );
				return;
			}
		}
		update_option( 'cer_notification_schema_version', '1', false );
	}
	if ( ! wp_next_scheduled( 'cer_event_notifications' ) && ! wp_schedule_event( time() + 60, 'every_5_minutes', 'cer_event_notifications' ) ) {
		error_log( 'CER event notification scheduling failed.' );
	}
}

function cer_process_event_notifications(): void {
	global $wpdb;
	$lock = 'cer_notifications_' . md5( $wpdb->prefix );
	if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(SHA2(CONCAT(DATABASE(), %s), 256), 0)', $lock ) ) ) {
		error_log( 'CER event notification worker lock unavailable: ' . $wpdb->last_error );
		return;
	}
	try {
		$now = current_datetime();
		$retry_before = $now->modify( '-15 minutes' )->format( 'Y-m-d H:i:s' );
		$registrations = $wpdb->prefix . 'evt_registrations';
		$events = $wpdb->prefix . 'evt_events';
		$tickets = $wpdb->get_col( $wpdb->prepare(
			"SELECT r.id FROM {$registrations} r INNER JOIN {$events} e ON e.id = r.event_id
			WHERE r.status = 'confirmed' AND r.amount <= 0 AND r.deleted_at IS NULL
			AND r.ticket_sent_at IS NULL AND e.deleted_at IS NULL AND e.status = 'published'
			AND e.event_date > %s AND (r.ticket_delivery_attempted_at IS NULL OR r.ticket_delivery_attempted_at <= %s)
			ORDER BY r.ticket_delivery_attempted_at ASC, r.id ASC LIMIT 25",
			$now->format( 'Y-m-d H:i:s' ), $retry_before
		) );
		if ( $wpdb->last_error ) {
			error_log( 'CER ticket retry query failed: ' . $wpdb->last_error );
			return;
		}
		foreach ( $tickets as $registration_id ) {
			if ( false === $wpdb->update( $registrations, array( 'ticket_delivery_attempted_at' => $now->format( 'Y-m-d H:i:s' ) ), array( 'id' => $registration_id ), array( '%s' ), array( '%d' ) ) ) {
				error_log( 'CER ticket retry could not be recorded for ' . $registration_id );
				continue;
			}
			if ( ! cer_send_ticket_for_registration( (int) $registration_id ) ) {
				error_log( 'CER ticket retry failed for registration ' . $registration_id );
			}
		}
		$reminders = $wpdb->get_results( $wpdb->prepare(
			"SELECT r.id, r.event_id, r.full_name, r.email, r.user_access_key, e.name AS event_name, e.event_date, e.venue FROM {$registrations} r
			INNER JOIN {$events} e ON e.id = r.event_id
			WHERE (r.status = 'paid' OR (r.status = 'confirmed' AND r.amount <= 0))
			AND r.deleted_at IS NULL AND e.deleted_at IS NULL AND e.status = 'published'
			AND e.event_date > %s AND e.event_date <= %s
			AND (r.reminder_event_date IS NULL OR r.reminder_event_date <> e.event_date)
			AND (r.reminder_attempted_at IS NULL OR r.reminder_attempted_at <= %s)
			ORDER BY r.reminder_attempted_at ASC, r.id ASC LIMIT 25",
			$now->format( 'Y-m-d H:i:s' ), $now->modify( '+5 days' )->format( 'Y-m-d H:i:s' ), $retry_before
		), ARRAY_A );
		if ( $wpdb->last_error ) {
			error_log( 'CER reminder query failed: ' . $wpdb->last_error );
			return;
		}
		foreach ( $reminders as $registration ) {
			$id = (int) $registration['id'];
			if ( false === $wpdb->update( $registrations, array( 'reminder_attempted_at' => $now->format( 'Y-m-d H:i:s' ) ), array( 'id' => $id ), array( '%s' ), array( '%d' ) ) ) {
				error_log( 'CER reminder attempt could not be recorded for ' . $id );
				continue;
			}
			if ( ! cer_send_event_reminder( $registration ) ) {
				error_log( 'CER reminder delivery failed for registration ' . $id );
				continue;
			}
			if ( false === $wpdb->update( $registrations, array( 'reminder_event_date' => $registration['event_date'] ), array( 'id' => $id ), array( '%s' ), array( '%d' ) ) ) {
				error_log( 'CER reminder delivery timestamp could not be saved for ' . $id );
			}
		}
	} finally {
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(SHA2(CONCAT(DATABASE(), %s), 256))', $lock ) );
	}
}

function cer_send_event_reminder( array $registration ): bool {
	$email = cer_decrypt_pii( $registration['email'] );
	if ( ! is_email( $email ) || empty( $registration['user_access_key'] ) ) {
		error_log( 'CER reminder recipient or ticket key invalid for ' . (int) $registration['id'] );
		return false;
	}
	$date = new DateTimeImmutable( $registration['event_date'], wp_timezone() );
	$event_name = $registration['event_name'];
	$view_url = add_query_arg( 'cer_ticket', $registration['user_access_key'], home_url( '/' ) );
	$body = '<p>Dear ' . esc_html( cer_decrypt_pii( $registration['full_name'] ) ) . ',</p>'
		. '<p>Thank you for registering for <strong>' . esc_html( $event_name ) . '</strong>. We look forward to welcoming you.</p>'
		. '<p>Date and time: ' . esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $date->getTimestamp() ) ) . '<br>'
		. 'Venue: ' . esc_html( $registration['venue'] ?: 'Venue to be announced' ) . '</p>'
		. '<p>Please bring your ticket for admission. <a href="' . esc_url( $view_url ) . '">View your ticket</a>.</p>';
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	$reply_to = cer_get_event_reply_to( (int) $registration['event_id'] );
	if ( '' !== $reply_to ) {
		$headers[] = 'Reply-To: ' . $reply_to;
		$body .= '<p>Questions? Reply to this email or contact ' . esc_html( $reply_to ) . '.</p>';
	}
	cer_set_current_mail_config( (int) $registration['event_id'], $event_name );
	try {
		return (bool) wp_mail( $email, 'Event reminder: ' . $event_name, $body, $headers );
	} finally {
		cer_clear_current_mail_config();
	}
}
