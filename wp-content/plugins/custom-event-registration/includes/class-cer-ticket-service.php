<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cer_ticket_pdf_path( array $registration ): string {
	$uploads = wp_upload_dir();
	$directory = trailingslashit( $uploads['basedir'] ) . 'tickets';
	if ( ! wp_mkdir_p( $directory ) ) {
		return '';
	}

	return trailingslashit( $directory ) . sanitize_file_name( 'ticket-' . $registration['user_access_key'] . '.pdf' );
}

function cer_generate_ticket_pdf( int $registration_id ): string {
	global $wpdb;
	$registrations = $wpdb->prefix . 'evt_registrations';
	$events = $wpdb->prefix . 'evt_events';
	$ticket_types = $wpdb->prefix . 'evt_ticket_types';
	$registration = $wpdb->get_row( $wpdb->prepare( "SELECT r.*, e.name AS event_name, e.event_date, e.venue, tt.name AS ticket_name FROM {$registrations} r LEFT JOIN {$events} e ON e.id = r.event_id LEFT JOIN {$ticket_types} tt ON tt.id = r.ticket_type_id WHERE r.id = %d LIMIT 1", $registration_id ), ARRAY_A );
	if ( ! $registration || empty( $registration['user_access_key'] ) ) {
		error_log( 'CER ticket generation skipped: registration or access key missing for ' . $registration_id );
		return '';
	}

	$path = cer_ticket_pdf_path( $registration );
	if ( '' === $path ) {
		error_log( 'CER ticket generation skipped: ticket path unavailable for ' . $registration_id );
		return '';
	}

	$autoload = ABSPATH . 'laravel-engine/vendor/autoload.php';
	if ( ! file_exists( $autoload ) ) {
		error_log( 'CER ticket generation failed: Composer autoload missing at ' . $autoload );
		return '';
	}
	require_once $autoload;
	if ( ! class_exists( '\Dompdf\Dompdf' ) ) {
		error_log( 'CER ticket generation failed: Dompdf class unavailable for ' . $registration_id );
		return '';
	}

	$qr_data_uri = '';
	if ( class_exists( '\Endroid\QrCode\QrCode' ) && class_exists( '\Endroid\QrCode\Writer\SvgWriter' ) ) {
		// Size the QR to its rendered box (112px + quiet-zone margin). The default
		// 320px intrinsic SVG size overflowed the fixed table cell and pushed the
		// image outside the page bounds in dompdf.
		$qr_code = \Endroid\QrCode\QrCode::create( $registration['user_access_key'] )->setSize( 104 )->setMargin( 4 );
		$qr_data_uri = ( new \Endroid\QrCode\Writer\SvgWriter() )->write( $qr_code )->getDataUri();
	}

	$name = cer_decrypt_pii( $registration['full_name'] );
	$email = cer_decrypt_pii( $registration['email'] );
	$event_name = $registration['event_name'] ?: 'ICRHK Event';
	$event_date = $registration['event_date'] ? wp_date( get_option( 'date_format' ), strtotime( $registration['event_date'] ) ) : 'Date to be announced';
	$event_time = $registration['event_date'] ? wp_date( get_option( 'time_format' ), strtotime( $registration['event_date'] ) ) : '';
	$venue = $registration['venue'] ?: 'Venue to be announced';
	$ticket_name = $registration['ticket_name'] ?: ( $registration['ticket_type'] ?: 'General admission' );
	// Chunk the 64-char access code into readable groups of 8 so it wraps inside
	// its cell instead of forcing the QR column off the page.
	$access_code_display = trim( chunk_split( $registration['user_access_key'], 8, ' ' ) );
	$html = '<!doctype html><html><head><meta charset="utf-8"><style>';
	$html .= 'body{font-family:DejaVu Sans,sans-serif;background:#f5f2fa;color:#241b35;margin:0;padding:34px}.ticket{background:#fff;border:1px solid #e4dcef;border-radius:18px;overflow:hidden}.top{background:#5A2A8C;color:#fff;padding:30px 34px 27px}.brand{font-size:11px;letter-spacing:2px;color:#F0761E;text-transform:uppercase}.eyebrow{font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#f5b87a;margin:23px 0 8px}.title{font-size:27px;line-height:1.2;margin:0;color:#fff}.intro{font-size:12px;color:#e6dcf4;margin:12px 0 0}.content{padding:28px 34px 32px}.status{display:inline-block;background:#efe6f9;color:#5A2A8C;border-radius:13px;padding:7px 12px;font-size:10px;font-weight:bold;letter-spacing:1px;text-transform:uppercase}.grid{width:100%;margin-top:23px}.cell{width:50%;vertical-align:top;padding:0 18px 20px 0}.label{font-size:9px;letter-spacing:1.2px;text-transform:uppercase;color:#8a7fa0;margin-bottom:6px}.value{font-size:14px;font-weight:bold;color:#241b35;line-height:1.35}.subvalue{font-size:11px;color:#6f6583;margin-top:4px}.lower{width:100%;table-layout:fixed;border-top:1px solid #e4dcef;padding-top:24px}.code-label{font-size:9px;letter-spacing:1.2px;text-transform:uppercase;color:#8a7fa0}.code{font-family:DejaVu Sans Mono,monospace;color:#5A2A8C;font-size:11px;letter-spacing:0.5px;word-break:break-all;margin:8px 0 0}.qr{width:128px;text-align:center;vertical-align:top}.qr img{width:112px;height:112px}.qr-note{font-size:9px;color:#6f6583;line-height:1.35;margin-top:7px}.footer{background:#f3edfa;color:#6f6583;font-size:9px;padding:15px 34px;line-height:1.5}.footer strong{color:#5A2A8C}</style></head><body>';
	$html .= '<div class="ticket"><div class="top"><div class="brand">ICRHK Events</div><div class="eyebrow">Confirmed admission</div><h1 class="title">' . esc_html( $event_name ) . '</h1><p class="intro">A verified ticket reserved for ' . esc_html( $name ) . '.</p></div><div class="content"><span class="status">Payment confirmed</span><table class="grid"><tr><td class="cell"><div class="label">Attendee</div><div class="value">' . esc_html( $name ) . '</div><div class="subvalue">' . esc_html( $email ) . '</div></td><td class="cell"><div class="label">Ticket type</div><div class="value">' . esc_html( $ticket_name ) . '</div><div class="subvalue">Admit one guest</div></td></tr><tr><td class="cell"><div class="label">Date and time</div><div class="value">' . esc_html( $event_date ) . '</div><div class="subvalue">' . esc_html( $event_time ) . '</div></td><td class="cell"><div class="label">Venue</div><div class="value">' . esc_html( $venue ) . '</div></td></tr></table><table class="lower"><tr><td style="vertical-align:top"><div class="code-label">Ticket access code</div><div class="code">' . esc_html( $access_code_display ) . '</div></td><td class="qr">';
	if ( $qr_data_uri ) {
		$html .= '<img src="' . esc_attr( $qr_data_uri ) . '" alt="Ticket QR code" width="112" height="112" loading="lazy" decoding="async"><div class="qr-note">Scan at the entrance<br>for validation</div>';
	}
	$html .= '</td></tr></table></div><div class="footer"><strong>Keep this ticket ready at arrival.</strong> Your access code is unique to this registration and should not be shared.</div></div></body></html>';

	try {
		$dompdf = new \Dompdf\Dompdf();
		$dompdf->loadHtml( $html );
		$dompdf->setPaper( 'A4', 'portrait' );
		$dompdf->render();
		if ( false === file_put_contents( $path, $dompdf->output(), LOCK_EX ) ) {
			error_log( 'CER ticket generation failed: PDF could not be written to ' . $path );
			return '';
		}
	} catch ( Throwable $exception ) {
		error_log( 'CER ticket generation exception for ' . $registration_id . ': ' . $exception->getMessage() );
		return '';
	}
	return file_exists( $path ) ? $path : '';
}

function cer_send_ticket_for_registration( int $registration_id ): bool {
	global $wpdb;
	$table = $wpdb->prefix . 'evt_registrations';
	$registration = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d LIMIT 1", $registration_id ), ARRAY_A );
	if ( ! $registration || 'paid' !== $registration['status'] || ! empty( $registration['ticket_sent_at'] ) ) {
		error_log( 'CER ticket send skipped for ' . $registration_id . ': registration missing, unpaid, or already sent.' );
		return false;
	}

	$path = cer_generate_ticket_pdf( $registration_id );
	if ( '' === $path ) {
		error_log( 'CER ticket generation failed for registration ' . $registration_id );
		return false;
	}
	$wpdb->update( $table, array( 'ticket_generated_at' => current_time( 'mysql' ) ), array( 'id' => $registration_id ), array( '%s' ), array( '%d' ) );

	$registration = $wpdb->get_row( $wpdb->prepare( "SELECT r.*, e.name AS event_name FROM {$table} r LEFT JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id WHERE r.id = %d LIMIT 1", $registration_id ), ARRAY_A );
	$email = cer_decrypt_pii( $registration['email'] );
	$name = cer_decrypt_pii( $registration['full_name'] );
	$event_name = $registration['event_name'] ?: 'ICRHK Event';
	$payment_method_value = strtolower( (string) $registration['payment_method'] );
	$payment_method = 'mpesa' === $payment_method_value ? 'M-PESA' : ( 'card' === $payment_method_value ? 'Card' : 'payment gateway' );
	$view_url = add_query_arg( 'cer_ticket', rawurlencode( $registration['user_access_key'] ), home_url( '/' ) );
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	$body = '<p>Dear ' . esc_html( $name ) . ',</p><p>Your ' . esc_html( $payment_method ) . ' payment is confirmed. Your ticket for <strong>' . esc_html( $event_name ) . '</strong> is attached.</p><p>You can also view your ticket online: <a href="' . esc_url( $view_url ) . '">' . esc_html( $view_url ) . '</a></p><p>We look forward to seeing you.</p>';
	$event_id = (int) ( $registration['event_id'] ?? 0 );
	$context = cer_set_current_mail_config( $event_id, $event_name );
	if ( ! wp_mail( $email, 'Your Ticket for ' . $event_name . ' - ' . $payment_method, $body, $headers, array( $path ) ) ) {
		error_log( 'CER ticket email failed for registration ' . $registration_id . ' to ' . $email . ' event ' . $event_id );
		cer_clear_current_mail_config();
		return false;
	}
	cer_clear_current_mail_config();

	$wpdb->update( $table, array( 'ticket_sent_at' => current_time( 'mysql' ) ), array( 'id' => $registration_id ), array( '%s' ), array( '%d' ) );
	return true;
}

function cer_log_mail_failure( $error ): void {
	if ( is_wp_error( $error ) ) {
		$context = $GLOBALS['cer_current_mail_config'] ?? array();
		$event_id = isset( $context['event_id'] ) ? (int) $context['event_id'] : 0;
		$sender = isset( $context['from_email'] ) ? $context['from_email'] : 'unknown';
		$message = 'CER wp_mail failed for event ' . $event_id . ' sender ' . $sender . ': ' . $error->get_error_message();
		$data = $error->get_error_data();
		$phpmailer = is_object( $data ) ? $data : ( is_array( $data ) && isset( $data['phpmailer'] ) ? $data['phpmailer'] : null );
		if ( is_object( $phpmailer ) && isset( $phpmailer->ErrorInfo ) ) {
			$message .= ' PHPMailer: ' . $phpmailer->ErrorInfo;
		}
		error_log( $message );
	}
}

function cer_handle_ticket_download(): void {
	$key = sanitize_text_field( wp_unslash( $_GET['cer_ticket'] ?? '' ) );
	if ( '' === $key ) {
		return;
	}

	global $wpdb;
	$table = $wpdb->prefix . 'evt_registrations';
	$registration = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE user_access_key = %s AND status = 'paid' LIMIT 1", $key ), ARRAY_A );
	$path = $registration ? cer_ticket_pdf_path( $registration ) : '';
	if ( ! $registration || ! file_exists( $path ) ) {
		status_header( 404 );
		exit( 'Ticket not found.' );
	}

	header( 'Content-Type: application/pdf' );
	header( 'Content-Disposition: inline; filename="icrhk-ticket.pdf"' );
	readfile( $path );
	exit;
}

function cer_handle_ticket_request(): void {
	if ( isset( $_REQUEST['cer_process_ticket'] ) ) {
		$registration_id = absint( wp_unslash( $_REQUEST['cer_process_ticket'] ) );
		$nonce = isset( $_REQUEST['cer_ticket_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['cer_ticket_nonce'] ) ) : '';
		$timestamp = isset( $_SERVER['HTTP_X_CER_TICKET_TIMESTAMP'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_CER_TICKET_TIMESTAMP'] ) ) : '';
		$signature = isset( $_SERVER['HTTP_X_CER_TICKET_SIGNATURE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_CER_TICKET_SIGNATURE'] ) ) : '';
		$callback_secret = defined( 'CER_TICKET_CALLBACK_SECRET' ) ? CER_TICKET_CALLBACK_SECRET : getenv( 'CER_TICKET_CALLBACK_SECRET' );
		$service_request_valid = $callback_secret && ctype_digit( $timestamp ) && abs( time() - (int) $timestamp ) <= 300 && hash_equals( hash_hmac( 'sha256', $registration_id . '|' . $timestamp, $callback_secret ), $signature );
		$admin_request_valid = current_user_can( 'manage_options' ) && wp_verify_nonce( $nonce, 'cer_process_ticket_' . $registration_id );
		if ( ! $service_request_valid && ! $admin_request_valid ) {
			error_log( 'CER ticket callback unauthorized for registration ' . $registration_id );
			wp_die( esc_html__( 'You are not authorized to process this ticket.', 'custom-event-registration' ), 403 );
		}

		$ticket_sent = cer_send_ticket_for_registration( $registration_id );
		if ( ! $ticket_sent ) {
			error_log( 'CER ticket callback failed for registration ' . $registration_id );
			status_header( 500 );
			exit;
		}
		error_log( 'CER ticket callback completed for registration ' . $registration_id );
		status_header( 204 );
		exit;
	}

	cer_handle_ticket_download();
}