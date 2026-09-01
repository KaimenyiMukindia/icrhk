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
		return '';
	}

	$path = cer_ticket_pdf_path( $registration );
	if ( '' === $path ) {
		return '';
	}

	$autoload = ABSPATH . 'laravel-engine/vendor/autoload.php';
	if ( ! file_exists( $autoload ) ) {
		return '';
	}
	require_once $autoload;

	$qr_data_uri = '';
	if ( class_exists( '\Endroid\QrCode\QrCode' ) && class_exists( '\Endroid\QrCode\Writer\SvgWriter' ) ) {
		$qr_code = \Endroid\QrCode\QrCode::create( $registration['user_access_key'] );
		$qr_data_uri = ( new \Endroid\QrCode\Writer\SvgWriter() )->write( $qr_code )->getDataUri();
	}

	$name = cer_decrypt_pii( $registration['full_name'] );
	$email = cer_decrypt_pii( $registration['email'] );
	$event_name = $registration['event_name'] ?: 'ICRHK Event';
	$event_date = $registration['event_date'] ? wp_date( get_option( 'date_format' ), strtotime( $registration['event_date'] ) ) : 'Date to be announced';
	$event_time = $registration['event_date'] ? wp_date( get_option( 'time_format' ), strtotime( $registration['event_date'] ) ) : '';
	$venue = $registration['venue'] ?: 'Venue to be announced';
	$ticket_name = $registration['ticket_name'] ?: ( $registration['ticket_type'] ?: 'General admission' );
	$html = '<!doctype html><html><head><meta charset="utf-8"><style>';
	$html .= 'body{font-family:DejaVu Sans,sans-serif;background:#f3f6f4;color:#18323a;margin:0;padding:34px}.ticket{background:#fff;border:1px solid #d8e5e0;border-radius:18px;overflow:hidden}.top{background:#123f46;color:#fff;padding:30px 34px 27px}.brand{font-size:11px;letter-spacing:2px;color:#b9ddd1;text-transform:uppercase}.eyebrow{font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#70c5aa;margin:23px 0 8px}.title{font-size:27px;line-height:1.2;margin:0;color:#fff}.intro{font-size:12px;color:#d7ebe5;margin:12px 0 0}.content{padding:28px 34px 32px}.status{display:inline-block;background:#e1f4e9;color:#176344;border-radius:13px;padding:7px 12px;font-size:10px;font-weight:bold;letter-spacing:1px;text-transform:uppercase}.grid{width:100%;margin-top:23px}.cell{width:50%;vertical-align:top;padding:0 18px 20px 0}.label{font-size:9px;letter-spacing:1.2px;text-transform:uppercase;color:#769097;margin-bottom:6px}.value{font-size:14px;font-weight:bold;color:#18323a;line-height:1.35}.subvalue{font-size:11px;color:#61757a;margin-top:4px}.lower{border-top:1px solid #dce8e4;padding-top:24px}.code-label{font-size:9px;letter-spacing:1.2px;text-transform:uppercase;color:#769097}.code{font-family:DejaVu Sans Mono,monospace;color:#123f46;font-size:13px;letter-spacing:1px;word-break:break-all;margin:8px 0 0}.qr{width:145px;text-align:center;vertical-align:top}.qr img{width:122px;height:122px}.qr-note{font-size:9px;color:#71848a;line-height:1.35;margin-top:7px}.footer{background:#edf5f1;color:#60787b;font-size:9px;padding:15px 34px;line-height:1.5}.footer strong{color:#315b5d}</style></head><body>';
	$html .= '<div class="ticket"><div class="top"><div class="brand">ICRHK Events</div><div class="eyebrow">Confirmed admission</div><h1 class="title">' . esc_html( $event_name ) . '</h1><p class="intro">A verified ticket reserved for ' . esc_html( $name ) . '.</p></div><div class="content"><span class="status">Payment confirmed</span><table class="grid"><tr><td class="cell"><div class="label">Attendee</div><div class="value">' . esc_html( $name ) . '</div><div class="subvalue">' . esc_html( $email ) . '</div></td><td class="cell"><div class="label">Ticket type</div><div class="value">' . esc_html( $ticket_name ) . '</div><div class="subvalue">Admit one guest</div></td></tr><tr><td class="cell"><div class="label">Date and time</div><div class="value">' . esc_html( $event_date ) . '</div><div class="subvalue">' . esc_html( $event_time ) . '</div></td><td class="cell"><div class="label">Venue</div><div class="value">' . esc_html( $venue ) . '</div></td></tr></table><table class="lower"><tr><td style="vertical-align:top;width:70%"><div class="code-label">Ticket access code</div><div class="code">' . esc_html( $registration['user_access_key'] ) . '</div></td><td class="qr">';
	if ( $qr_data_uri ) {
		$html .= '<img src="' . esc_attr( $qr_data_uri ) . '" alt="Ticket QR code"><div class="qr-note">Scan at the entrance<br>for validation</div>';
	}
	$html .= '</td></tr></table></div><div class="footer"><strong>Keep this ticket ready at arrival.</strong> Your access code is unique to this registration and should not be shared.</div></div></body></html>';

	$dompdf = new \Dompdf\Dompdf();
	$dompdf->loadHtml( $html );
	$dompdf->setPaper( 'A4', 'portrait' );
	$dompdf->render();
	file_put_contents( $path, $dompdf->output(), LOCK_EX );
	return file_exists( $path ) ? $path : '';
}

function cer_send_ticket_for_registration( int $registration_id ): bool {
	global $wpdb;
	$table = $wpdb->prefix . 'evt_registrations';
	$registration = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d LIMIT 1", $registration_id ), ARRAY_A );
	if ( ! $registration || 'paid' !== $registration['status'] || ! empty( $registration['ticket_sent_at'] ) ) {
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
	$view_url = add_query_arg( 'cer_ticket', rawurlencode( $registration['user_access_key'] ), home_url( '/' ) );
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	$body = '<p>Dear ' . esc_html( $name ) . ',</p><p>Your payment is confirmed. Your ticket for <strong>' . esc_html( $event_name ) . '</strong> is attached.</p><p>You can also view your ticket online: <a href="' . esc_url( $view_url ) . '">' . esc_html( $view_url ) . '</a></p><p>We look forward to seeing you.</p>';
	if ( ! wp_mail( $email, 'Your Ticket for ' . $event_name, $body, $headers, array( $path ) ) ) {
		error_log( 'CER ticket email failed for registration ' . $registration_id . ' to ' . $email );
		return false;
	}

	$wpdb->update( $table, array( 'ticket_sent_at' => current_time( 'mysql' ) ), array( 'id' => $registration_id ), array( '%s' ), array( '%d' ) );
	return true;
}

function cer_log_mail_failure( $error ): void {
	if ( is_wp_error( $error ) ) {
		error_log( 'CER wp_mail failed: ' . $error->get_error_message() );
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
		cer_send_ticket_for_registration( absint( wp_unslash( $_REQUEST['cer_process_ticket'] ) ) );
		status_header( 204 );
		exit;
	}

	cer_handle_ticket_download();
}