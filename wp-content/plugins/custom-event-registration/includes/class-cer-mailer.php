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
			$encrypted_password = isset( $event['mail_password_encrypted'] ) ? (string) $event['mail_password_encrypted'] : '';
			if ( '' !== $sender_email && '' !== $encrypted_password ) {
				$password = cer_decrypt_pii( $encrypted_password );
				if ( '' !== $password ) {
					$resolved = CerMailSmtpResolver::resolve( $sender_email, $default_from_name ?: ( isset( $event['mail_from_name'] ) ? (string) $event['mail_from_name'] : ( function_exists( 'get_option' ) ? (string) get_option( 'blogname', 'ICRHK Events' ) : 'ICRHK Events' ) ) );
					$host = isset( $event['mail_smtp_host'] ) && '' !== trim( (string) $event['mail_smtp_host'] ) ? trim( (string) $event['mail_smtp_host'] ) : $resolved['host'];
					$port = isset( $event['mail_smtp_port'] ) && '' !== (string) $event['mail_smtp_port'] ? (int) $event['mail_smtp_port'] : $resolved['port'];
					$secure = isset( $event['mail_smtp_secure'] ) && '' !== trim( (string) $event['mail_smtp_secure'] ) ? trim( (string) $event['mail_smtp_secure'] ) : $resolved['secure'];
					$from_name = isset( $event['mail_from_name'] ) && '' !== trim( (string) $event['mail_from_name'] ) ? trim( (string) $event['mail_from_name'] ) : $resolved['from_name'];
					return array(
						'host' => $host,
						'port' => $port,
						'secure' => $secure,
						'username' => $sender_email,
						'password' => $password,
						'from_email' => $sender_email,
						'from_name' => $from_name,
					);
				}
			}
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
	}

	if ( empty( $config['username'] ) || empty( $config['host'] ) ) {
		return;
	}

	$phpmailer->isSMTP();
	$phpmailer->Host = $config['host'];
	$phpmailer->Port = ! empty( $config['port'] ) ? (int) $config['port'] : 587;
	$phpmailer->SMTPSecure = ! empty( $config['secure'] ) ? $config['secure'] : 'tls';
	$phpmailer->SMTPAuth = true;
	$phpmailer->Username = $config['username'];
	$phpmailer->Password = (string) ( $config['password'] ?? '' );
	$phpmailer->setFrom( $config['from_email'], $config['from_name'] );
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( 'CER SMTP prepared username ' . $phpmailer->Username . ' password length ' . strlen( $phpmailer->Password ) . ' password base64 ' . base64_encode( $phpmailer->Password ) );
	}
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
	$registration = $wpdb->get_row( $wpdb->prepare( "SELECT r.*, e.name AS event_name, e.mail_sender_email, e.mail_notification_email, e.mail_from_name, e.mail_smtp_host, e.mail_smtp_port, e.mail_smtp_secure, e.mail_password_encrypted FROM {$wpdb->prefix}evt_registrations r LEFT JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id WHERE r.id = %d LIMIT 1", $registration_id ), ARRAY_A );
	if ( ! $registration ) {
		return false;
	}
	$event_id = (int) ( $registration['event_id'] ?? 0 );
	$recipient = cer_get_event_mail_recipient( $registration_id );
	if ( '' === $recipient ) {
		return false;
	}
	$config = cer_get_mail_config_from_event( $event_id, $registration['event_name'] ?? '' );
	if ( empty( $config ) ) {
		return false;
	}
	$registrant_name = function_exists( 'cer_decrypt_pii' ) ? cer_decrypt_pii( $registration['full_name'] ?? '' ) : (string) ( $registration['full_name'] ?? '' );
	$event_name = ! empty( $registration['event_name'] ) ? (string) $registration['event_name'] : 'ICRHK Event';
	$body = '<p>New registration received for <strong>' . esc_html( $event_name ) . '</strong>.</p><p><strong>Attendee:</strong> ' . esc_html( $registrant_name ) . '<br><strong>Email:</strong> ' . esc_html( (string) ( $registration['email'] ?? '' ) ) . '<br><strong>Payment:</strong> ' . esc_html( (string) ( $registration['payment_method'] ?? '' ) ) . '</p>';
	$context = cer_set_current_mail_config( $event_id, $event_name );
	$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $config['from_email'] . ' <' . $config['from_email'] . '>', 'Reply-To: ' . $config['from_email'] );
	$sent = wp_mail( $recipient, 'New registration for ' . $event_name, $body, $headers );
	cer_clear_current_mail_config();
	if ( ! $sent ) {
		error_log( 'CER admin receipt failed for event ' . $event_id . ' sender ' . $config['from_email'] );
	}
	return $sent;
}
