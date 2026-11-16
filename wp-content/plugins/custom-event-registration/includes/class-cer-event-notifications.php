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
	if ( '2' !== get_option( 'cer_notification_schema_version', '' ) ) {
		$table = $wpdb->prefix . 'evt_registrations';
		foreach ( array( 'reminder_event_date', 'reminder_attempted_at', 'ticket_delivery_attempted_at', 'vendor_notified_at', 'vendor_notify_attempted_at' ) as $column ) {
			cer_add_column_if_missing( $table, $column, 'DATETIME NULL' );
			if ( ! cer_column_exists( $table, $column ) ) {
				error_log( 'CER notification schema unavailable: ' . $column );
				return;
			}
		}
		// Admissions confirmed before the digest existed were already notified individually; never replay them.
		if ( false === $wpdb->query( $wpdb->prepare(
			"UPDATE {$table} SET vendor_notified_at = %s WHERE vendor_notified_at IS NULL AND (status = 'paid' OR (status = 'confirmed' AND amount <= 0))",
			current_time( 'mysql' )
		) ) ) {
			error_log( 'CER vendor notification baseline failed: ' . $wpdb->last_error );
			return;
		}
		update_option( 'cer_notification_schema_version', '2', false );
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
		cer_process_vendor_notifications( $now );
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

// Vendor registration notices: a daily digest at 03:05 Nairobi time (after Resend's 00:00 UTC quota reset),
// switching to real-time notices from 36 hours before the event starts until it ends.
function cer_vendor_notifications_ready(): bool {
	return '2' === (string) get_option( 'cer_notification_schema_version', '' );
}

function cer_event_vendor_realtime( array $event, ?DateTimeImmutable $now = null ): bool {
	$timezone = wp_timezone();
	$now = $now ?: current_datetime();
	try {
		$start = new DateTimeImmutable( (string) ( $event['event_date'] ?? '' ), $timezone );
		$end_value = trim( (string) ( $event['event_end_date'] ?? '' ) );
		$end = '' !== $end_value && 0 !== strpos( $end_value, '0000' ) ? new DateTimeImmutable( $end_value, $timezone ) : null;
	} catch ( Exception $e ) {
		return false;
	}
	if ( ! $end || $end < $start ) {
		$end = $start->setTimezone( new DateTimeZone( 'Africa/Nairobi' ) )->setTime( 23, 59, 59 );
	}
	return $now >= $start->modify( '-36 hours' ) && $now <= $end;
}

function cer_claim_vendor_notice( int $registration_id, DateTimeImmutable $now ): bool {
	global $wpdb;
	return 1 === $wpdb->query( $wpdb->prepare(
		"UPDATE {$wpdb->prefix}evt_registrations SET vendor_notify_attempted_at = %s
		WHERE id = %d AND vendor_notified_at IS NULL AND (vendor_notify_attempted_at IS NULL OR vendor_notify_attempted_at <= %s)",
		$now->format( 'Y-m-d H:i:s' ), $registration_id, $now->modify( '-15 minutes' )->format( 'Y-m-d H:i:s' )
	) );
}

function cer_mark_vendor_notified( array $registration_ids ): void {
	global $wpdb;
	$registration_ids = array_map( 'intval', $registration_ids );
	if ( empty( $registration_ids ) ) {
		return;
	}
	$placeholders = implode( ',', array_fill( 0, count( $registration_ids ), '%d' ) );
	if ( false === $wpdb->query( $wpdb->prepare(
		"UPDATE {$wpdb->prefix}evt_registrations SET vendor_notified_at = %s WHERE id IN ({$placeholders})",
		array_merge( array( current_time( 'mysql' ) ), $registration_ids )
	) ) ) {
		error_log( 'CER vendor notification timestamp could not be saved: ' . $wpdb->last_error );
	}
}

// Called after a ticket is delivered: notify the vendor now only inside the real-time window, otherwise queue for the digest.
function cer_notify_vendor_of_registration( int $registration_id ): void {
	global $wpdb;
	if ( ! cer_vendor_notifications_ready() ) {
		cer_send_admin_receipt_for_registration( $registration_id );
		return;
	}
	$event = $wpdb->get_row( $wpdb->prepare(
		"SELECT e.event_date, e.event_end_date FROM {$wpdb->prefix}evt_registrations r INNER JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id WHERE r.id = %d LIMIT 1",
		$registration_id
	), ARRAY_A );
	$now = current_datetime();
	if ( ! $event || ! cer_event_vendor_realtime( $event, $now ) || ! cer_claim_vendor_notice( $registration_id, $now ) ) {
		return;
	}
	if ( cer_send_admin_receipt_for_registration( $registration_id ) ) {
		cer_mark_vendor_notified( array( $registration_id ) );
	}
}

function cer_process_vendor_notifications( DateTimeImmutable $now ): void {
	global $wpdb;
	if ( ! cer_vendor_notifications_ready() ) {
		return;
	}
	$local = $now->setTimezone( new DateTimeZone( 'Africa/Nairobi' ) );
	$today = $local->format( 'Y-m-d' );
	$digest_due = $local->format( 'H:i' ) >= '03:05' && get_option( 'cer_vendor_digest_last_date', '' ) !== $today;
	$cutoff = $local->setTime( 0, 0 )->setTimezone( wp_timezone() )->format( 'Y-m-d H:i:s' );
	$rows = $wpdb->get_results( $wpdb->prepare(
		"SELECT r.id, r.event_id, r.full_name, r.email, r.ticket_type, r.amount, COALESCE(r.ticket_sent_at, r.created_at) AS confirmed_on,
		e.name AS event_name, e.event_date, e.event_end_date, e.mail_notification_email, t.name AS ticket_name
		FROM {$wpdb->prefix}evt_registrations r
		INNER JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id
		LEFT JOIN {$wpdb->prefix}evt_ticket_types t ON t.id = r.ticket_type_id
		WHERE (r.status = 'paid' OR (r.status = 'confirmed' AND r.amount <= 0))
		AND r.deleted_at IS NULL AND e.deleted_at IS NULL AND r.vendor_notified_at IS NULL
		AND e.mail_notification_email IS NOT NULL AND e.mail_notification_email <> ''
		AND (r.vendor_notify_attempted_at IS NULL OR r.vendor_notify_attempted_at <= %s)
		ORDER BY r.event_id ASC, r.id ASC LIMIT 2000",
		$now->modify( '-15 minutes' )->format( 'Y-m-d H:i:s' )
	), ARRAY_A );
	if ( $wpdb->last_error ) {
		error_log( 'CER vendor notification query failed: ' . $wpdb->last_error );
		return;
	}
	$by_event = array();
	foreach ( $rows as $row ) {
		$by_event[ (int) $row['event_id'] ][] = $row;
	}
	foreach ( $by_event as $event_id => $event_rows ) {
		$realtime = cer_event_vendor_realtime( $event_rows[0], $now );
		if ( ! $realtime ) {
			$event_rows = $digest_due ? array_values( array_filter( $event_rows, static function ( $row ) use ( $cutoff ) {
				return (string) $row['confirmed_on'] < $cutoff;
			} ) ) : array();
		}
		$event_rows = array_values( array_filter( $event_rows, static function ( $row ) use ( $now ) {
			return cer_claim_vendor_notice( (int) $row['id'], $now );
		} ) );
		if ( empty( $event_rows ) ) {
			continue;
		}
		$sent = $realtime && 1 === count( $event_rows )
			? cer_send_admin_receipt_for_registration( (int) $event_rows[0]['id'] )
			: cer_send_vendor_registration_summary( $event_id, $event_rows, $realtime, $local );
		if ( $sent ) {
			cer_mark_vendor_notified( array_column( $event_rows, 'id' ) );
		} else {
			error_log( 'CER vendor registration summary failed for event ' . $event_id );
		}
	}
	// Failed digests are logged and carried into the next day's digest (or the real-time window) to protect the daily quota.
	if ( $digest_due ) {
		update_option( 'cer_vendor_digest_last_date', $today, false );
	}
}

function cer_send_vendor_registration_summary( int $event_id, array $rows, bool $realtime, DateTimeImmutable $local_now ): bool {
	$recipient = trim( (string) ( $rows[0]['mail_notification_email'] ?? '' ) );
	if ( ! is_email( $recipient ) ) {
		return false;
	}
	$event_name = ! empty( $rows[0]['event_name'] ) ? (string) $rows[0]['event_name'] : 'ICRHK Event';
	$yesterday = wp_date( get_option( 'date_format' ), $local_now->modify( '-1 day' )->getTimestamp(), $local_now->getTimezone() );
	$intro = $realtime
		? 'The following registrations for <strong>' . esc_html( $event_name ) . '</strong> were confirmed since the last summary. From now until the event ends you will receive each new registration as it happens.'
		: 'The following registered for <strong>' . esc_html( $event_name ) . '</strong> (' . esc_html( $yesterday ) . '):';
	$items = '';
	foreach ( $rows as $row ) {
		$email = cer_decrypt_pii( $row['email'] );
		$items .= '<li>' . esc_html( cer_decrypt_pii( $row['full_name'] ) ) . ' &ndash; '
			. ( is_email( $email ) ? '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>' : esc_html( $email ) )
			. ' &ndash; ' . esc_html( $row['ticket_name'] ?: ( $row['ticket_type'] ?: 'General admission' ) ) . '</li>';
	}
	$body = '<p>' . $intro . '</p><ol>' . $items . '</ol><p>Total: ' . count( $rows ) . '</p>';
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	$reply_to = cer_get_event_reply_to( $event_id );
	if ( '' !== $reply_to ) {
		$headers[] = 'Reply-To: ' . $reply_to;
	}
	$subject = ( $realtime ? 'Registration summary for ' : 'Daily registrations for ' ) . $event_name . ( $realtime ? '' : ' - ' . $yesterday );
	cer_set_current_mail_config( $event_id, $event_name );
	try {
		return (bool) wp_mail( $recipient, $subject, $body, $headers );
	} finally {
		cer_clear_current_mail_config();
	}
}