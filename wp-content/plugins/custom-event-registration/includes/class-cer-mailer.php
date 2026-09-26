<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cer_get_mail_config_from_event( ?int $event_id = null, string $default_from_name = '' ): array {
	$event_id = $event_id ?: 0;
	$default_from_name = trim( (string) $default_from_name );
	if ( $event_id > 0 ) {
		global $wpdb;
		$event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_events WHERE id = %d LIMIT 1", $event_id ), ARRAY_A );
		if ( $event ) {
			$sender_email = isset( $event['mail_sender_email'] ) ? trim( (string) $event['mail_sender_email'] ) : '';
			if ( '' !== $sender_email ) {
				error_log( 'CER event sender metadata retained for event ' . $event_id . '; SMTP is used only without Resend constants.' );
			}
		} else {
			error_log( 'CER SMTP config unavailable: event not found for event ' . $event_id );
		}
	}

	return array();
}

function cer_set_current_mail_config( ?int $event_id = null, string $default_from_name = '' ): array {
	$config = cer_get_mail_config_from_event( $event_id, $default_from_name );
	$config['event_id'] = $event_id ?: 0;
	$GLOBALS['cer_current_mail_config'] = $config;
	return $config;
}

function cer_clear_current_mail_config(): void {
	unset( $GLOBALS['cer_current_mail_config'] );
}

function cer_get_resend_from(): string {
	$from = defined( 'CER_RESEND_FROM' ) ? trim( (string) CER_RESEND_FROM ) : '';
	if ( preg_match( '/<([^>]+)>/', $from, $matches ) ) {
		return is_email( trim( $matches[1] ) ) ? $from : '';
	}

	return is_email( $from ) ? $from : '';
}

function cer_get_mail_addresses( $value ): array {
	$values = is_array( $value ) ? $value : preg_split( '/[,\r\n]+/', (string) $value );
	$addresses = array();
	foreach ( $values as $item ) {
		$item = trim( (string) $item );
		if ( preg_match( '/<([^>]+)>/', $item, $matches ) ) {
			$item = trim( $matches[1] );
		}
		if ( is_email( $item ) ) {
			$addresses[] = $item;
		}
	}

	return array_values( array_unique( $addresses ) );
}

function cer_maybe_send_via_resend( $pre, array $atts ) {
	$api_key = defined( 'CER_RESEND_API_KEY' ) ? trim( (string) CER_RESEND_API_KEY ) : '';
	$config = $GLOBALS['cer_current_mail_config'] ?? array();
	$from = cer_get_resend_from();
	if ( '' === $api_key || '' === $from ) {
		return $pre;
	}

	$to = cer_get_mail_addresses( $atts['to'] ?? '' );
	if ( empty( $to ) ) {
		return new WP_Error( 'cer_resend_recipient_missing', 'Resend recipient is missing.' );
	}

	$payload = array(
		'from' => $from,
		'to' => $to,
		'subject' => (string) ( $atts['subject'] ?? '' ),
		'html' => (string) ( $atts['message'] ?? '' ),
	);
	$headers = $atts['headers'] ?? array();
	$headers = is_array( $headers ) ? $headers : preg_split( '/\r?\n/', (string) $headers );
	foreach ( $headers as $header ) {
		$header = (string) $header;
		if ( preg_match( '/^Reply-To:\s*(.+)$/i', $header, $matches ) ) {
			$reply_to = cer_get_mail_addresses( $matches[1] );
			if ( ! empty( $reply_to ) ) {
				$payload['reply_to'] = $reply_to;
			}
		}
		if ( preg_match( '/^(Cc|Bcc):\s*(.+)$/i', $header, $matches ) ) {
			$addresses = cer_get_mail_addresses( $matches[2] );
			if ( ! empty( $addresses ) ) {
				$payload[ strtolower( $matches[1] ) ] = $addresses;
			}
		}
	}

	$attachments = $atts['attachments'] ?? array();
	$attachments = is_array( $attachments ) ? $attachments : array_filter( preg_split( '/\r?\n/', (string) $attachments ) );
	foreach ( $attachments as $attachment ) {
		$attachment = (string) $attachment;
		if ( ! is_readable( $attachment ) ) {
			return new WP_Error( 'cer_resend_attachment_missing', 'Resend attachment is not readable.' );
		}
		$payload['attachments'][] = array(
			'filename' => basename( $attachment ),
			'content' => base64_encode( (string) file_get_contents( $attachment ) ),
		);
	}

	$response = wp_remote_post(
		'https://api.resend.com/emails',
		array(
			'timeout' => 30,
			'headers' => array(
				'Authorization' => 'Bearer ' . $api_key,
				'Content-Type' => 'application/json',
			),
			'body' => wp_json_encode( $payload ),
		)
	);
	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$status = (int) wp_remote_retrieve_response_code( $response );
	if ( $status < 200 || $status >= 300 ) {
		return new WP_Error(
			'cer_resend_rejected',
			'Resend rejected the message.',
			array( 'status' => $status, 'body' => wp_remote_retrieve_body( $response ) )
		);
	}

	error_log( 'CER Resend accepted message for ' . count( $to ) . ' recipient(s).' );
	return true;
}

function cer_configure_smtp( $phpmailer ): void {
	$config = $GLOBALS['cer_current_mail_config'] ?? array();
	if ( empty( $config ) ) {
		if ( ! defined( 'CER_SMTP_HOST' ) || ! defined( 'CER_SMTP_USERNAME' ) || ! defined( 'CER_SMTP_PASSWORD' ) ) {
			return;
		}
		$config = array(
			'host' => CER_SMTP_HOST,
			'port' => defined( 'CER_SMTP_PORT' ) ? (int) CER_SMTP_PORT : 587,
			'secure' => defined( 'CER_SMTP_SECURE' ) ? CER_SMTP_SECURE : 'tls',
			'username' => CER_SMTP_USERNAME,
			'password' => CER_SMTP_PASSWORD,
			'from_email' => defined( 'CER_SMTP_FROM' ) ? CER_SMTP_FROM : CER_SMTP_USERNAME,
			'from_name' => defined( 'CER_SMTP_FROM_NAME' ) ? CER_SMTP_FROM_NAME : 'ICRHK Events',
		);
		error_log( 'CER SMTP config source: global constants' );
	}

	if ( empty( $config['username'] ) || empty( $config['host'] ) ) {
		error_log( 'CER SMTP not enabled: missing username or host' );
		return;
	}

	$phpmailer->isSMTP();
	$phpmailer->Host = $config['host'];
	$phpmailer->Port = ! empty( $config['port'] ) ? (int) $config['port'] : 587;
	$phpmailer->SMTPSecure = ! empty( $config['secure'] ) ? $config['secure'] : 'tls';
	$phpmailer->SMTPAuth = true;
	$phpmailer->Username = $config['username'];
	$phpmailer->Password = (string) ( $config['password'] ?? '' );

	$smtp_options = array(
		'ssl' => array(
			'verify_peer' => defined( 'CER_SMTP_VERIFY_PEER' ) ? (bool) CER_SMTP_VERIFY_PEER : true,
			'verify_peer_name' => defined( 'CER_SMTP_VERIFY_PEER_NAME' ) ? (bool) CER_SMTP_VERIFY_PEER_NAME : false,
			'allow_self_signed' => defined( 'CER_SMTP_ALLOW_SELF_SIGNED' ) ? (bool) CER_SMTP_ALLOW_SELF_SIGNED : false,
		),
	);
	if ( defined( 'CER_SMTP_SSL_OPTIONS' ) && is_array( CER_SMTP_SSL_OPTIONS ) ) {
		$smtp_options = array_replace_recursive( $smtp_options, CER_SMTP_SSL_OPTIONS );
	}
	$phpmailer->SMTPOptions = $smtp_options;
	$phpmailer->setFrom( $config['from_email'], $config['from_name'] );
	error_log( 'CER SMTP enabled for event ' . (int) ( $config['event_id'] ?? 0 ) . ' using host ' . $phpmailer->Host . ' and port ' . (int) $phpmailer->Port );
}

function cer_mail_from_address( string $from ): string {
	$config = $GLOBALS['cer_current_mail_config'] ?? array();
	if ( ! empty( $config['from_email'] ) ) {
		return $config['from_email'];
	}
	return defined( 'CER_SMTP_FROM' ) ? CER_SMTP_FROM : $from;
}

function cer_mail_from_name( string $from_name ): string {
	$config = $GLOBALS['cer_current_mail_config'] ?? array();
	if ( ! empty( $config['from_name'] ) ) {
		return $config['from_name'];
	}
	return defined( 'CER_SMTP_FROM_NAME' ) ? CER_SMTP_FROM_NAME : $from_name;
}

function cer_get_event_mail_recipient( int $registration_id ): string {
	global $wpdb;
	$registration = $wpdb->get_row( $wpdb->prepare( "SELECT r.*, e.mail_notification_email, e.mail_sender_email FROM {$wpdb->prefix}evt_registrations r LEFT JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id WHERE r.id = %d LIMIT 1", $registration_id ), ARRAY_A );
	if ( ! $registration ) {
		return '';
	}
	$notification_email = trim( (string) ( $registration['mail_notification_email'] ?? '' ) );
	if ( '' !== $notification_email ) {
		return $notification_email;
	}
	$sender_email = trim( (string) ( $registration['mail_sender_email'] ?? '' ) );
	if ( '' !== $sender_email ) {
		return $sender_email;
	}
	return '';
}

function cer_send_admin_receipt_for_registration( int $registration_id ): bool {
	global $wpdb;
	$registration = $wpdb->get_row( $wpdb->prepare( "SELECT r.*, e.name AS event_name, e.mail_notification_email FROM {$wpdb->prefix}evt_registrations r LEFT JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id WHERE r.id = %d LIMIT 1", $registration_id ), ARRAY_A );
	if ( ! $registration ) {
		return false;
	}
	$event_id = (int) ( $registration['event_id'] ?? 0 );
	$recipient = is_email( trim( (string) ( $registration['mail_notification_email'] ?? '' ) ) ) ? trim( (string) $registration['mail_notification_email'] ) : '';
	if ( '' === $recipient ) {
		return false;
	}
	$config = cer_get_mail_config_from_event( $event_id, $registration['event_name'] ?? '' );
	if ( empty( $config ) && '' === cer_get_resend_from() ) {
		return false;
	}
	$registrant_name = function_exists( 'cer_decrypt_pii' ) ? cer_decrypt_pii( $registration['full_name'] ?? '' ) : (string) ( $registration['full_name'] ?? '' );
	$registrant_email = function_exists( 'cer_decrypt_pii' ) ? cer_decrypt_pii( $registration['email'] ?? '' ) : (string) ( $registration['email'] ?? '' );
	$event_name = ! empty( $registration['event_name'] ) ? (string) $registration['event_name'] : 'ICRHK Event';
	$ticket_name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$wpdb->prefix}evt_ticket_types WHERE id = %d LIMIT 1", (int) ( $registration['ticket_type_id'] ?? 0 ) ) );
	$body = "New ticket purchase for {$event_name}\n\nBuyer name: {$registrant_name}\nBuyer email: {$registrant_email}\nTicket type: " . ( $ticket_name ?: (string) ( $registration['ticket_type'] ?? 'General admission' ) );
	$context = cer_set_current_mail_config( $event_id, $event_name );
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	$sent = wp_mail( $recipient, 'New registration for ' . $event_name, $body, $headers );
	cer_clear_current_mail_config();
	if ( ! $sent ) {
		error_log( 'CER admin receipt failed for event ' . $event_id );
	}
	return $sent;
}
