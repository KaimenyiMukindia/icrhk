<?php

if ( PHP_SAPI !== 'cli' ) {
	exit( "Run this test from the command line.\n" );
}

$root = dirname( __DIR__, 4 );
$config = file_get_contents( $root . '/wp-config.php' );
if ( ! preg_match( "/define\\s*\\(\\s*['\"]DB_HOST['\"]\\s*,\\s*['\"](localhost|127\\.0\\.0\\.1)(?::[0-9]+)?['\"]/i", $config ) ) {
	exit( "The test requires a literal loopback WordPress database host.\n" );
}
$_SERVER['HTTP_HOST'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';
define( 'DISABLE_WP_CRON', true );
define( 'WP_USE_THEMES', false );
$GLOBALS['wp_filter']['pre_option_active_plugins'][10][] = array( 'function' => static function () { return array(); }, 'accepted_args' => 0 );
$GLOBALS['wp_filter']['pre_option_template'][10][] = array( 'function' => static function () { return 'cer-test-disabled-theme'; }, 'accepted_args' => 0 );
$GLOBALS['wp_filter']['pre_option_stylesheet'][10][] = array( 'function' => static function () { return 'cer-test-disabled-theme'; }, 'accepted_args' => 0 );
require $root . '/wp-load.php';

if ( in_array( $argv[1] ?? '', array( '--submit', '--availability' ), true ) ) {
	$child_database = $argv[2] ?? '';
	if ( ! preg_match( '/^cer_notifications_test_[a-f0-9]{12}$/', $child_database ) ) {
		throw new RuntimeException( 'Invalid concurrent test database.' );
	}
	$wpdb->select( $child_database );
	wp_cache_flush();
	require dirname( __DIR__ ) . '/custom-event-registration.php';
	if ( '--availability' === $argv[1] ) {
		$_GET['event_id'] = $argv[3] ?? 0;
		cer_ajax_ticket_availability();
		exit;
	}
	remove_filter( 'pre_wp_mail', 'cer_maybe_send_via_resend', 10 );
	add_filter( 'pre_wp_mail', '__return_true' );
	add_filter( 'pre_http_request', static function () { throw new RuntimeException( 'Concurrent tests must not use HTTP.' ); } );
	add_filter( 'upload_dir', static function ( $uploads ) use ( $child_database ) {
		$uploads['basedir'] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $child_database;
		$uploads['path'] = $uploads['basedir'];
		$uploads['error'] = false;
		return $uploads;
	} );
	$payload = json_decode( base64_decode( $argv[3], true ), true, 512, JSON_THROW_ON_ERROR );
	$payload['security'] = wp_create_nonce( 'cer_registration_form' );
	echo json_encode( cer_process_registration_submission( $payload ) );
	exit;
}

function cer_notify_assert( bool $condition, string $message ): void {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}
function cer_notify_query( string $sql ): void {
	global $wpdb;
	if ( false === $wpdb->query( $sql ) ) {
		throw new RuntimeException( $wpdb->last_error );
	}
}
function cer_notify_insert( string $table, array $row ): int {
	global $wpdb;
	cer_notify_assert( false !== $wpdb->insert( $wpdb->prefix . $table, $row ), 'Fixture insert failed: ' . $wpdb->last_error );
	return (int) $wpdb->insert_id;
}
function cer_notify_availability_ajax( string $database, int $event_id ): array {
	$pipes = array();
	$process = proc_open(
		array( PHP_BINARY, __FILE__, '--availability', $database, (string) $event_id ),
		array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ),
		$pipes
	);
	cer_notify_assert( is_resource( $process ), 'Availability AJAX fixture failed to start.' );
	fclose( $pipes[0] );
	$output = stream_get_contents( $pipes[1] );
	$errors = stream_get_contents( $pipes[2] );
	fclose( $pipes[1] );
	fclose( $pipes[2] );
	cer_notify_assert( 0 === proc_close( $process ), 'Availability AJAX fixture failed: ' . $errors );
	return json_decode( $output, true, 512, JSON_THROW_ON_ERROR );
}

$database = 'cer_notifications_test_' . bin2hex( random_bytes( 6 ) );
$created = false;
$directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $database;
$prefix = $wpdb->prefix;
cer_notify_assert( 1 === preg_match( '/^[A-Za-z0-9_]+$/', $prefix ) && 1 === preg_match( '/^[A-Za-z0-9_]+$/', DB_NAME ), 'Unsafe local schema name.' );
$wpdb->suppress_errors( true );
try {
	cer_notify_query( "CREATE DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci" );
	$created = true;
	foreach ( array( 'options', 'evt_events', 'evt_ticket_types', 'evt_registrations', 'evt_partners' ) as $suffix ) {
		cer_notify_query( "CREATE TABLE `{$database}`.`{$prefix}{$suffix}` LIKE `" . DB_NAME . "`.`{$prefix}{$suffix}`" );
	}
	$wpdb->select( $database );
	wp_cache_flush();
	add_filter( 'pre_option_home', static function () { return 'http://127.0.0.1'; } );
	add_filter( 'pre_option_siteurl', static function () { return 'http://127.0.0.1'; } );
	add_filter( 'pre_option_timezone_string', static function () { return 'Africa/Nairobi'; } );
	add_filter( 'upload_dir', static function ( $uploads ) use ( $directory ) {
		$uploads['basedir'] = $directory;
		$uploads['path'] = $directory;
		$uploads['error'] = false;
		return $uploads;
	} );
	add_filter( 'pre_http_request', static function () { throw new RuntimeException( 'Tests must never make external HTTP requests.' ); } );
	require dirname( __DIR__ ) . '/custom-event-registration.php';
	remove_filter( 'pre_wp_mail', 'cer_maybe_send_via_resend', 10 );
	$mail = array();
	$mail_success = true;
	add_filter( 'pre_wp_mail', static function ( $pre, $atts ) use ( &$mail, &$mail_success ) {
		$mail[] = $atts;
		return $mail_success;
	}, 1, 2 );
	cer_schedule_event_notifications();
	cer_schedule_event_notifications();
	cer_notify_assert( '2' === get_option( 'cer_notification_schema_version' ) && false !== wp_next_scheduled( 'cer_event_notifications' ), 'Schema and recurring worker must be installed idempotently.' );
	$now = current_datetime();
	$event = cer_notify_insert( 'evt_events', array(
		'event_uuid' => wp_generate_uuid4(), 'name' => 'Notification fixture', 'slug' => $database,
		'event_date' => $now->modify( '+5 days -1 minute' )->format( 'Y-m-d H:i:s' ),
		'status' => 'published', 'venue' => 'Fixture Hall', 'mail_sender_email' => 'organizer@example.test',
	) );
	$ticket = cer_notify_insert( 'evt_ticket_types', array( 'event_id' => $event, 'name' => 'Delegate', 'price' => 0, 'quantity_available' => 100 ) );
	cer_notify_assert( false !== $wpdb->update( $prefix . 'evt_events', array(
		'event_info_heading' => 'Editable information fixture', 'event_info_paragraph_1' => 'Editable paragraph fixture',
		'event_info_paragraph_2' => 'Another editable paragraph', 'target_audience' => 'Editable audience fixture',
		'show_event_information' => 1, 'show_speakers' => 0, 'show_sponsors' => 0, 'show_pillars' => 0,
		'show_objectives' => 0, 'show_summit_structure' => 0, 'show_partners' => 0, 'show_faq' => 0,
	), array( 'id' => $event ) ), 'Public page fixture could not be updated.' );
	require_once dirname( __DIR__ ) . '/includes/event-page-renderer.php';
	$render_event = static function () use ( $event ): string {
		$_GET['event_id'] = (string) $event;
		ob_start();
		try {
			cer_render_event_registration_page();
			return ob_get_contents();
		} finally {
			ob_end_clean();
			unset( $_GET['event_id'] );
		}
	};
	$event_html = $render_event();
	cer_notify_assert( false === strpos( $event_html, 'cer-registration-availability' ), 'Availability must not create another sidebar card.' );
	cer_notify_assert( 1 === substr_count( $event_html, 'Secure Your Place' ) && 1 === substr_count( $event_html, 'Ticket: <strong>Delegate</strong>' ), 'The original form heading and single-ticket label must remain in the form markup.' );
	cer_notify_assert( false !== strpos( $event_html, 'data-payment-required="false" data-ticket-count="1"' ) && false !== strpos( $event_html, '>Ticket Information</h3>' ), 'A single free ticket must use the existing summary area for ticket information.' );
	cer_notify_assert( false !== strpos( $event_html, 'id="cer-summary-ticket">Delegate</strong>' ) && false !== strpos( $event_html, '>100 left</strong><small>out of 100 capacity</small>' ), 'A single ticket must show its name, remaining places and total in the summary.' );
	cer_notify_assert( false !== strpos( $event_html, '<small>Capacity</small><strong>100 delegates</strong>' ), 'The existing hero capacity must agree with the single-ticket total.' );
	cer_notify_assert( false !== strpos( $event_html, '<h2>Secure Your Place</h2>' ) && strpos( $event_html, '<h2>Secure Your Place</h2>' ) < strpos( $event_html, 'id="cer-registration-form"' ), 'The registration heading must stay above the form.' );
	$initial_availability = cer_get_ticket_availability( $event );
	cer_notify_assert( 100 === $initial_availability['capacity'] && 100 === $initial_availability['remaining'] && 1 === count( $initial_availability['tickets'] ), 'Event availability must use configured ticket capacity when no event cap exists.' );
	$ajax_availability = cer_notify_availability_ajax( $database, $event );
	cer_notify_assert( true === $ajax_availability['success'] && 100 === $ajax_availability['data']['remaining'] && 100 === $ajax_availability['data']['capacity'], 'The public AJAX response must expose the expected live availability shape.' );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'quantity_available' => null ), array( 'id' => $ticket ) );
	$unlimited_html = $render_event();
	cer_notify_assert( false === strpos( $unlimited_html, '<small>Capacity</small>' ) && false !== strpos( $unlimited_html, '>Available</strong><small>Capacity not specified</small>' ), 'An unlimited single ticket must not show a fabricated capacity.' );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'quantity_available' => 0 ), array( 'id' => $ticket ) );
	$sold_out_html = $render_event();
	cer_notify_assert( false !== strpos( $sold_out_html, 'cer-sold-out' ) && false !== strpos( $sold_out_html, 'id="cer-summary-ticket">Delegate</strong>' ) && false !== strpos( $sold_out_html, '>Sold out</strong><small>out of 0 capacity</small>' ), 'A sold-out single ticket must retain its name and availability in the summary.' );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'quantity_available' => 100 ), array( 'id' => $ticket ) );
	$wpdb->update( $prefix . 'evt_events', array( 'max_attendees' => 50 ), array( 'id' => $event ) );
	$capped = cer_get_ticket_availability( $event );
	cer_notify_assert( 50 === $capped['capacity'] && 50 === $capped['remaining'] && 50 === $capped['tickets'][0]['capacity'], 'The total must reflect a tighter event capacity.' );
	$wpdb->update( $prefix . 'evt_events', array( 'max_attendees' => 200 ), array( 'id' => $event ) );
	$capped = cer_get_ticket_availability( $event );
	cer_notify_assert( 100 === $capped['capacity'] && 100 === $capped['remaining'], 'The total must reflect a tighter ticket capacity.' );
	$wpdb->update( $prefix . 'evt_events', array( 'max_attendees' => null ), array( 'id' => $event ) );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'price' => 150 ), array( 'id' => $ticket ) );
	$paid_html = $render_event();
	cer_notify_assert( false !== strpos( $paid_html, 'data-payment-required="true" data-ticket-count="1"' ) && false !== strpos( $paid_html, 'id="cer-summary-method">M-Pesa' ) && false !== strpos( $paid_html, 'id="cer-summary-amount"' ), 'A single paid ticket must retain the payment summary and methods.' );
	cer_notify_assert( false !== strpos( $paid_html, '>100 left</strong><small>out of 100 capacity</small>' ), 'A single paid ticket must show the count in its existing summary.' );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'price' => 0 ), array( 'id' => $ticket ) );
	$failed_availability_form = cer_get_registration_form_markup( $event, new WP_Error( 'fixture', 'Unavailable' ) );
	cer_notify_assert( false !== strpos( $failed_availability_form, 'data-availability-ready="false"' ) && false !== strpos( $failed_availability_form, 'Live availability unavailable' ) && false !== strpos( $failed_availability_form, 'Ticket: <strong>Delegate</strong>' ), 'A failed availability query must warn and retain the original ticket label.' );
	cer_notify_assert( false !== strpos( $event_html, 'id="cer-registration-form"' ), 'The event page must retain its registration form.' );
	foreach ( array( 'Editable information fixture', 'Editable paragraph fixture', 'Another editable paragraph', 'Editable audience fixture' ) as $editable_copy ) {
		cer_notify_assert( false !== strpos( $event_html, $editable_copy ), 'Editable event information must remain visible.' );
	}
	foreach ( array( 'Plenary keynotes, panels &amp; workshops', '47 Counties', 'Delegates, experts and global partners' ) as $static_copy ) {
		cer_notify_assert( false === strpos( $event_html, $static_copy ), 'Hard-coded event statistics must not appear on the public page.' );
	}
	cer_notify_assert( false !== $wpdb->update( $prefix . 'evt_events', array( 'show_event_information' => 0 ), array( 'id' => $event ) ), 'Event Information visibility could not be updated.' );
	wp_cache_flush();
	$hidden_html = $render_event();
	cer_notify_assert( false === strpos( $hidden_html, 'Editable information fixture' ) && false === strpos( $hidden_html, 'Editable paragraph fixture' ) && false !== strpos( $hidden_html, 'id="cer-registration-form"' ), 'The existing visibility setting must hide only the information card.' );
	$payload = array(
		'security' => wp_create_nonce( 'cer_registration_form' ), 'full_name' => 'Fixture Attendee',
		'email' => 'attendee@example.test', 'phone' => '0712345678', 'payment_method' => 'mpesa',
		'event_id' => $event, 'ticket_type_id' => $ticket, 'amount' => 999,
	);
	$result = cer_process_registration_submission( $payload );
	cer_notify_assert( ! empty( $result['success'] ) && ! empty( $result['data']['ticket_sent'] ), 'Free admission must generate and email its PDF: ' . json_encode( $result ) );
	$first_id = (int) $wpdb->get_var( "SELECT id FROM {$prefix}evt_registrations LIMIT 1" );
	$row = $wpdb->get_row( "SELECT * FROM {$prefix}evt_registrations WHERE id = {$first_id}", ARRAY_A );
	cer_notify_assert( 'confirmed' === $row['status'] && null === $row['reserved_until'] && 'none' === $row['payment_method'], 'Free admission must not be a payment or expiring reservation.' );
	cer_notify_assert( 64 === strlen( $row['user_access_key'] ) && file_exists( cer_ticket_pdf_path( $row ) ), 'Free admission requires its unique ticket and PDF.' );
	cer_notify_assert( '%PDF-' === file_get_contents( cer_ticket_pdf_path( $row ), false, null, 0, 5 ), 'The ticket must be a real PDF.' );
	cer_notify_assert( 1 === count( $mail ) && false !== strpos( $mail[0]['message'], 'Thank you' ) && false === strpos( $mail[0]['message'], 'payment' ), 'Free ticket mail must thank the attendee without payment wording.' );
	cer_notify_assert( 1 === count( $mail[0]['attachments'] ), 'Ticket mail must attach the PDF.' );
	$after_registration = cer_get_ticket_availability( $event );
	cer_notify_assert( 99 === $after_registration['remaining'] && 99 === $after_registration['tickets'][0]['remaining'], 'Free admissions must reduce both event and ticket availability.' );
	$ajax_after_registration = cer_notify_availability_ajax( $database, $event );
	cer_notify_assert( 99 === $ajax_after_registration['data']['remaining'] && 99 === $ajax_after_registration['data']['tickets'][0]['remaining'], 'The next AJAX request must reflect a new registration.' );
	cer_notify_assert( in_array( 'Reply-To: organizer@example.test', $mail[0]['headers'], true ) && false === strpos( $mail[0]['message'], 'Please do not reply' ), 'Ticket replies must go to the event Sender Email.' );
	cer_notify_assert( cer_send_ticket_for_registration( $first_id ) && 1 === count( $mail ), 'Repeated delivery must be idempotent.' );
	$duplicate = cer_process_registration_submission( array_merge( $payload, array( 'email' => 'ATTENDEE@example.test' ) ) );
	cer_notify_assert( ! $duplicate['success'] && false !== strpos( $duplicate['message'], 'already registered' ), 'Case-insensitive duplicate email/ticket must be rejected.' );
	cer_notify_assert( 1 === (int) $wpdb->get_var( "SELECT quantity_sold FROM {$prefix}evt_ticket_types WHERE id = {$ticket}" ), 'Duplicate must not consume inventory.' );
	$second_ticket = cer_notify_insert( 'evt_ticket_types', array( 'event_id' => $event, 'name' => 'Workshop', 'price' => 0 ) );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'price' => 200, 'quantity_available' => 10 ), array( 'id' => $second_ticket ) );
	$multiple_html = $render_event();
	cer_notify_assert( false !== strpos( $multiple_html, 'data-payment-required="true" data-ticket-count="2"' ) && false !== strpos( $multiple_html, '<select id="cer-ticket-type"' ), 'Multiple ticket types must keep the original form and payment summary.' );
	cer_notify_assert( false !== strpos( $multiple_html, '>Delegate - 99 left</option>' ) && false !== strpos( $multiple_html, '>Workshop (KES 200.00) - 10 left</option>' ), 'Each dropdown option must show its own live remaining count without losing its price.' );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'quantity_available' => 1 ), array( 'id' => $ticket ) );
	$partial_form = cer_get_registration_form_markup( $event, cer_get_ticket_availability( $event ) );
	cer_notify_assert( false === strpos( $partial_form, '<select' ) && false !== strpos( $partial_form, 'id="cer-inline-ticket-availability" aria-live="polite"> - 10 left</small>' ), 'The existing auto-selected last ticket must show its count next to its label.' );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'quantity_available' => 100 ), array( 'id' => $ticket ) );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'price' => 0, 'quantity_available' => null ), array( 'id' => $second_ticket ) );
	$other_type = cer_process_registration_submission( array_merge( $payload, array( 'ticket_type_id' => $second_ticket ) ) );
	cer_notify_assert( $other_type['success'], 'The same email may register once for another ticket type.' );

	$mail_success = false;
	$failed = cer_process_registration_submission( array_merge( $payload, array( 'email' => 'retry@example.test' ) ) );
	cer_notify_assert( $failed['success'] && ! $failed['data']['ticket_sent'], 'Mail failure must preserve admission and explicitly report delivery failure.' );
	$retry_id = (int) $wpdb->get_var( "SELECT MAX(id) FROM {$prefix}evt_registrations" );
	$mail_success = true;
	$mail = array();
	cer_process_event_notifications();
	cer_notify_assert( ! empty( $wpdb->get_var( "SELECT ticket_sent_at FROM {$prefix}evt_registrations WHERE id = {$retry_id}" ) ), 'The worker must retry the unsent no-cost ticket.' );
	$reminders = array_values( array_filter( $mail, static function ( $message ) { return 0 === strpos( $message['subject'], 'Event reminder:' ); } ) );
	cer_notify_assert( 3 === count( $reminders ), 'All three confirmed attendees must receive reminders inside the five-day threshold.' );
	foreach ( $reminders as $reminder ) {
		cer_notify_assert( is_string( $reminder['to'] ) && false === strpos( $reminder['to'], ',' ), 'Bulk reminders must not expose other attendees.' );
		cer_notify_assert( false !== strpos( $reminder['message'], 'Fixture Hall' ) && false !== strpos( $reminder['message'], 'cer_ticket=' ), 'Reminders require event details and the admission ticket link.' );
		cer_notify_assert( in_array( 'Reply-To: organizer@example.test', $reminder['headers'], true ), 'Reminder replies must go to the event Sender Email.' );
	}
	$mail = array();
	cer_process_event_notifications();
	cer_notify_assert( empty( $mail ), 'Successful tickets and reminders must not be resent on the next run.' );

	$base = array(
		'registration_uuid' => wp_generate_uuid4(), 'payment_uuid' => wp_generate_uuid4(), 'event_id' => $event,
		'ticket_type_id' => $ticket, 'full_name' => 'Reminder Fixture', 'email' => 'reminder@example.test',
		'phone' => '254712345678', 'payment_method' => 'card', 'amount' => 100, 'status' => 'paid',
		'ticket_sent_at' => current_time( 'mysql' ),
	);
	$paid = cer_notify_insert( 'evt_registrations', cer_prepare_registration_row( $base ) );
	foreach ( array( 'pending', 'failed', 'cancelled', 'expired', 'confirmed' ) as $status ) {
		cer_notify_insert( 'evt_registrations', cer_prepare_registration_row( array_merge( $base, array(
			'registration_uuid' => wp_generate_uuid4(), 'payment_uuid' => wp_generate_uuid4(), 'status' => $status,
		) ) ) );
	}
	$mail = array();
	$mail_success = false;
	cer_process_event_notifications();
	cer_notify_assert( 1 === count( $mail ) && null === $wpdb->get_var( "SELECT reminder_event_date FROM {$prefix}evt_registrations WHERE id = {$paid}" ), 'Only paid/valid confirmed admissions qualify, and failed reminders must stay retryable.' );
	$mail = array();
	cer_process_event_notifications();
	cer_notify_assert( empty( $mail ), 'Failed reminders must respect the retry delay.' );
	cer_notify_query( "UPDATE {$prefix}evt_registrations SET reminder_attempted_at = NULL WHERE id = {$paid}" );
	$mail_success = true;
	cer_process_event_notifications();
	cer_notify_assert( 1 === count( $mail ) && null !== $wpdb->get_var( "SELECT reminder_event_date FROM {$prefix}evt_registrations WHERE id = {$paid}" ), 'Failed paid-attendee reminder must eventually succeed.' );

	$mail = array();
	$new_date = $now->modify( '+5 days +10 minutes' )->format( 'Y-m-d H:i:s' );
	$wpdb->update( $prefix . 'evt_events', array( 'event_date' => $new_date ), array( 'id' => $event ) );
	cer_notify_query( "UPDATE {$prefix}evt_registrations SET reminder_attempted_at = NULL" );
	cer_process_event_notifications();
	cer_notify_assert( empty( $mail ), 'Reminders must not be sent more than five days before the event.' );
	$wpdb->update( $prefix . 'evt_events', array( 'event_date' => $now->modify( '+4 days' )->format( 'Y-m-d H:i:s' ) ), array( 'id' => $event ) );
	cer_process_event_notifications();
	cer_notify_assert( 4 === count( $mail ), 'A rescheduled event must receive one reminder for its new date.' );
	$mail = array();
	$wpdb->update( $prefix . 'evt_events', array( 'event_date' => $now->modify( '-1 minute' )->format( 'Y-m-d H:i:s' ) ), array( 'id' => $event ) );
	cer_notify_query( "UPDATE {$prefix}evt_registrations SET reminder_event_date = NULL, reminder_attempted_at = NULL" );
	cer_process_event_notifications();
	cer_notify_assert( empty( $mail ), 'Past events must never receive reminders.' );
	$wpdb->update( $prefix . 'evt_events', array( 'event_date' => $now->modify( '+4 days' )->format( 'Y-m-d H:i:s' ), 'deleted_at' => current_time( 'mysql' ) ), array( 'id' => $event ) );
	cer_process_event_notifications();
	cer_notify_assert( empty( $mail ), 'Deleted events must not receive reminders.' );
	$wpdb->update( $prefix . 'evt_events', array( 'deleted_at' => null, 'status' => 'draft' ), array( 'id' => $event ) );
	cer_process_event_notifications();
	cer_notify_assert( empty( $mail ), 'Unpublished events must not receive reminders.' );
	$wpdb->update( $prefix . 'evt_events', array( 'status' => 'published' ), array( 'id' => $event ) );
	cer_notify_query( "UPDATE {$prefix}evt_registrations SET deleted_at = NOW()" );
	cer_process_event_notifications();
	cer_notify_assert( empty( $mail ), 'Deleted registrations must not receive reminders.' );

	cer_notify_query( "DELETE FROM {$prefix}evt_registrations" );
	for ( $i = 0; $i < 26; ++$i ) {
		cer_notify_insert( 'evt_registrations', cer_prepare_registration_row( array_merge( $base, array(
			'registration_uuid' => wp_generate_uuid4(), 'payment_uuid' => wp_generate_uuid4(),
			'email' => 'bulk' . $i . '@example.test',
		) ) ) );
	}
	cer_process_event_notifications();
	cer_notify_assert( 25 === count( $mail ), 'Each run must enforce the 25-recipient reminder batch limit.' );
	cer_process_event_notifications();
	cer_notify_assert( 26 === count( $mail ), 'Subsequent runs must drain the remaining bulk recipients without duplicates.' );

	$mail = array();
	cer_notify_query( "UPDATE {$prefix}evt_registrations SET reminder_event_date = NULL, reminder_attempted_at = NULL" );
	$other_connection = new wpdb( DB_USER, DB_PASSWORD, $database, DB_HOST );
	$worker_lock = 'cer_notifications_' . md5( $prefix );
	$other_connection->get_var( $other_connection->prepare( 'SELECT GET_LOCK(SHA2(CONCAT(DATABASE(), %s), 256), 0)', $worker_lock ) );
	try {
		cer_process_event_notifications();
		cer_notify_assert( empty( $mail ), 'A concurrent bulk worker must not send duplicate reminders.' );
	} finally {
		$other_connection->get_var( $other_connection->prepare( 'SELECT RELEASE_LOCK(SHA2(CONCAT(DATABASE(), %s), 256))', $worker_lock ) );
		$other_connection->close();
	}

	$concurrent_payload = array_merge( $payload, array( 'email' => 'concurrent@example.test', 'ticket_type_id' => $second_ticket ) );
	$processes = array();
	cer_notify_query( 'START TRANSACTION' );
	$wpdb->get_row( "SELECT id FROM {$prefix}evt_events WHERE id = {$event} FOR UPDATE" );
	try {
		for ( $i = 0; $i < 2; ++$i ) {
			$pipes = array();
			$process = proc_open(
				array( PHP_BINARY, __FILE__, '--submit', $database, base64_encode( json_encode( $concurrent_payload ) ) ),
				array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ),
				$pipes
			);
			cer_notify_assert( is_resource( $process ), 'Concurrent submission process failed to start.' );
			fclose( $pipes[0] );
			$processes[] = array( $process, $pipes );
		}
		usleep( 500000 );
	} finally {
		cer_notify_query( 'COMMIT' );
	}
	$successes = 0;
	foreach ( $processes as list( $process, $pipes ) ) {
		$output = stream_get_contents( $pipes[1] );
		$errors = stream_get_contents( $pipes[2] );
		fclose( $pipes[1] );
		fclose( $pipes[2] );
		cer_notify_assert( 0 === proc_close( $process ), 'Concurrent submission failed: ' . $errors );
		$result = json_decode( $output, true, 512, JSON_THROW_ON_ERROR );
		$successes += ! empty( $result['success'] ) ? 1 : 0;
	}
	cer_notify_assert( 1 === $successes, 'Exactly one simultaneous submission for the same email/ticket may succeed.' );
	cer_notify_assert( 2 === (int) $wpdb->get_var( "SELECT quantity_sold FROM {$prefix}evt_ticket_types WHERE id = {$second_ticket}" ), 'Concurrent duplicate must consume only one additional ticket.' );

	$legacy = cer_prepare_registration_row( array_merge( $base, array(
		'registration_uuid' => wp_generate_uuid4(), 'payment_uuid' => wp_generate_uuid4(),
		'ticket_type_id' => $second_ticket, 'status' => 'pending', 'amount' => 0, 'ticket_sent_at' => null,
	) ) );
	$legacy['email'] = 'Legacy@example.test';
	$legacy['email_search_hash'] = null;
	$legacy_id = cer_notify_insert( 'evt_registrations', $legacy );
	$before = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}evt_registrations" );
	$upgrade = cer_process_registration_submission( array_merge( $payload, array( 'ticket_type_id' => $second_ticket, 'email' => 'legacy@example.test' ) ) );
	cer_notify_assert( $upgrade['success'] && $upgrade['data']['ticket_sent'], 'A returning legacy no-cost contact must receive confirmed admission.' );
	cer_notify_assert( $before === (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}evt_registrations" ), 'Returning legacy contact must be reused, not duplicated.' );
	$upgraded_row = $wpdb->get_row( "SELECT * FROM {$prefix}evt_registrations WHERE id = {$legacy_id}", ARRAY_A );
	cer_notify_assert( 'confirmed' === $upgraded_row['status'] && $legacy['user_access_key'] === $upgraded_row['user_access_key'] && $legacy['registration_uuid'] === $upgraded_row['registration_uuid'], 'Legacy upgrade must preserve its registration identity and unique access key.' );

	$capacity_event = cer_notify_insert( 'evt_events', array(
		'event_uuid' => wp_generate_uuid4(), 'name' => 'Capacity fixture', 'slug' => $database . '-capacity',
		'event_date' => $now->modify( '+10 days' )->format( 'Y-m-d H:i:s' ),
		'status' => 'published', 'max_attendees' => 1,
	) );
	$capacity_paid_ticket = cer_notify_insert( 'evt_ticket_types', array( 'event_id' => $capacity_event, 'name' => 'Paid', 'price' => 100 ) );
	$capacity_free_ticket = cer_notify_insert( 'evt_ticket_types', array( 'event_id' => $capacity_event, 'name' => 'Free', 'price' => 0 ) );
	$hold_id = cer_notify_insert( 'evt_registrations', cer_prepare_registration_row( array_merge( $base, array(
		'registration_uuid' => wp_generate_uuid4(), 'payment_uuid' => wp_generate_uuid4(),
		'event_id' => $capacity_event, 'ticket_type_id' => $capacity_paid_ticket, 'email' => 'hold@example.test',
		'status' => 'pending', 'ticket_sent_at' => null, 'reserved_until' => gmdate( 'Y-m-d H:i:s', time() + 600 ),
	) ) ) );
	$free_payload = array_merge( $payload, array(
		'event_id' => $capacity_event, 'ticket_type_id' => $capacity_free_ticket, 'email' => 'free@example.test',
	) );
	foreach ( array( 'pending', 'awaiting_payment' ) as $hold_status ) {
		$wpdb->update( $prefix . 'evt_registrations', array( 'status' => $hold_status ), array( 'id' => $hold_id ) );
		$wpdb->update( $prefix . 'evt_events', array( 'max_attendees' => null ), array( 'id' => $capacity_event ) );
		$wpdb->update( $prefix . 'evt_ticket_types', array( 'quantity_available' => 2, 'quantity_sold' => 0 ), array( 'id' => $capacity_paid_ticket ) );
		$ticket_hold = cer_get_ticket_availability( $capacity_event );
		cer_notify_assert( 2 === $ticket_hold['tickets'][0]['capacity'] && 1 === $ticket_hold['tickets'][0]['remaining'], 'Selected-ticket availability must subtract active reservations.' );
		$wpdb->update( $prefix . 'evt_ticket_types', array( 'quantity_sold' => 1 ), array( 'id' => $capacity_paid_ticket ) );
		cer_notify_assert( 1 === cer_get_ticket_availability( $capacity_event )['tickets'][0]['remaining'], 'A hold already reflected in inventory must not be counted twice.' );
		$wpdb->update( $prefix . 'evt_ticket_types', array( 'quantity_available' => null, 'quantity_sold' => 0 ), array( 'id' => $capacity_paid_ticket ) );
		$wpdb->update( $prefix . 'evt_events', array( 'max_attendees' => 1 ), array( 'id' => $capacity_event ) );
		$held_availability = cer_get_ticket_availability( $capacity_event );
		cer_notify_assert( 1 === $held_availability['capacity'] && 0 === $held_availability['remaining'] && 0 === $held_availability['tickets'][1]['remaining'], 'Live availability must account for active paid holds against shared capacity.' );
		$blocked = cer_process_registration_submission( $free_payload );
		cer_notify_assert( ! $blocked['success'] && 'This event has reached its attendance capacity.' === $blocked['message'], 'A free admission must not displace an active paid hold.' );
	}
	$wpdb->update( $prefix . 'evt_registrations', array( 'reserved_until' => gmdate( 'Y-m-d H:i:s', time() - 60 ) ), array( 'id' => $hold_id ) );
	cer_notify_assert( 1 === cer_get_ticket_availability( $capacity_event )['remaining'], 'Expired holds must stop consuming shared availability.' );
	$wpdb->update( $prefix . 'evt_events', array( 'max_attendees' => null ), array( 'id' => $capacity_event ) );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'quantity_available' => 2 ), array( 'id' => $capacity_paid_ticket ) );
	cer_notify_assert( 2 === cer_get_ticket_availability( $capacity_event )['tickets'][0]['remaining'], 'Expired holds must stop consuming selected-ticket availability when no longer held in inventory.' );
	$wpdb->update( $prefix . 'evt_events', array( 'max_attendees' => 1 ), array( 'id' => $capacity_event ) );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'quantity_available' => null ), array( 'id' => $capacity_paid_ticket ) );
	$wpdb->update( $prefix . 'evt_events', array( 'status' => 'draft' ), array( 'id' => $capacity_event ) );
	cer_notify_assert( is_wp_error( cer_get_ticket_availability( $capacity_event ) ), 'Public availability must not expose unpublished events.' );
	$unpublished_ajax = cer_notify_availability_ajax( $database, $capacity_event );
	cer_notify_assert( false === $unpublished_ajax['success'] && ! empty( $unpublished_ajax['data']['message'] ), 'The public AJAX action must reject unpublished events.' );
	$wpdb->update( $prefix . 'evt_events', array( 'status' => 'published' ), array( 'id' => $capacity_event ) );
	$after_expiry = cer_process_registration_submission( $free_payload );
	cer_notify_assert( $after_expiry['success'], 'An expired paid hold must not block a free admission.' );
	$wpdb->update( $prefix . 'evt_events', array( 'max_attendees' => 2 ), array( 'id' => $capacity_event ) );
	cer_notify_insert( 'evt_registrations', cer_prepare_registration_row( array_merge( $base, array(
		'registration_uuid' => wp_generate_uuid4(), 'payment_uuid' => wp_generate_uuid4(),
		'event_id' => $capacity_event, 'ticket_type_id' => null, 'email' => 'legacy-capacity@example.test', 'status' => 'paid',
	) ) ) );
	cer_notify_assert( 0 === cer_get_ticket_availability( $capacity_event )['remaining'], 'Confirmed admissions without a ticket type must still consume event capacity.' );

	$wpdb->update( $prefix . 'evt_events', array( 'max_attendees' => $before ), array( 'id' => $event ) );
	$full = cer_process_registration_submission( array_merge( $payload, array( 'email' => 'full@example.test' ) ) );
	cer_notify_assert( ! $full['success'] && false !== strpos( $full['message'], 'attendance capacity' ), 'No-cost confirmed tickets must enforce event capacity.' );
	$wpdb->update( $prefix . 'evt_events', array( 'max_attendees' => null ), array( 'id' => $event ) );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'quantity_available' => 3 ), array( 'id' => $second_ticket ) );
	$full_ticket = cer_process_registration_submission( array_merge( $payload, array( 'ticket_type_id' => $second_ticket, 'email' => 'soldout@example.test' ) ) );
	cer_notify_assert( ! $full_ticket['success'] && false !== strpos( $full_ticket['message'], 'sold out' ), 'No-cost confirmed tickets must enforce ticket inventory.' );

	$nairobi = new DateTimeZone( 'Africa/Nairobi' );
	$window_start = new DateTimeImmutable( '2030-03-10 09:00:00', $nairobi );
	$no_end = array( 'event_date' => $window_start->format( 'Y-m-d H:i:s' ), 'event_end_date' => null );
	cer_notify_assert( ! cer_event_vendor_realtime( $no_end, $window_start->modify( '-37 hours' ) ) && cer_event_vendor_realtime( $no_end, $window_start->modify( '-35 hours' ) ), 'Real-time vendor notices must start 36 hours before the event.' );
	cer_notify_assert( cer_event_vendor_realtime( $no_end, $window_start->setTime( 23, 30 ) ) && ! cer_event_vendor_realtime( $no_end, $window_start->modify( '+1 day' )->setTime( 0, 30 ) ), 'Without an end date real-time ends with the start day in Kenyan time.' );
	$with_end = array_merge( $no_end, array( 'event_end_date' => $window_start->modify( '+2 days' )->format( 'Y-m-d H:i:s' ) ) );
	cer_notify_assert( cer_event_vendor_realtime( $with_end, $window_start->modify( '+1 day' ) ) && ! cer_event_vendor_realtime( $with_end, $window_start->modify( '+2 days +1 minute' ) ), 'Real-time must continue until the event end date.' );

	$vendor_event = cer_notify_insert( 'evt_events', array(
		'event_uuid' => wp_generate_uuid4(), 'name' => 'Vendor fixture', 'slug' => $database . '-vendor',
		'event_date' => $now->modify( '+30 days' )->format( 'Y-m-d H:i:s' ), 'status' => 'published',
		'mail_sender_email' => 'organizer@example.test', 'mail_notification_email' => 'vendor@example.test',
	) );
	$vendor_ticket = cer_notify_insert( 'evt_ticket_types', array( 'event_id' => $vendor_event, 'name' => 'Vendor Delegate', 'price' => 0 ) );
	$vendor_base = array_merge( $base, array( 'event_id' => $vendor_event, 'ticket_type_id' => $vendor_ticket, 'status' => 'confirmed', 'amount' => 0 ) );
	$insert_vendor_row = static function ( string $email, string $sent_at ) use ( $vendor_base ): int {
		return cer_notify_insert( 'evt_registrations', cer_prepare_registration_row( array_merge( $vendor_base, array(
			'registration_uuid' => wp_generate_uuid4(), 'payment_uuid' => wp_generate_uuid4(), 'email' => $email, 'ticket_sent_at' => $sent_at,
		) ) ) );
	};
	$vendor_mail = static function () use ( &$mail ): array {
		return array_values( array_filter( $mail, static function ( $message ) { return 'vendor@example.test' === $message['to']; } ) );
	};

	$historic = $insert_vendor_row( 'historic@example.test', current_time( 'mysql' ) );
	update_option( 'cer_notification_schema_version', '1', false );
	cer_schedule_event_notifications();
	cer_notify_assert( '2' === get_option( 'cer_notification_schema_version' ) && null !== $wpdb->get_var( "SELECT vendor_notified_at FROM {$prefix}evt_registrations WHERE id = {$historic}" ), 'Existing admissions must be baselined so no historical notices are replayed.' );

	$mail = array();
	$queued = cer_process_registration_submission( array_merge( $payload, array( 'event_id' => $vendor_event, 'ticket_type_id' => $vendor_ticket, 'email' => 'queued@example.test' ) ) );
	cer_notify_assert( $queued['success'] && $queued['data']['ticket_sent'] && 1 === count( $mail ) && empty( $vendor_mail() ), 'Outside the real-time window the attendee ticket is immediate and the vendor notice is queued.' );
	$today = current_datetime()->setTimezone( $nairobi )->setTime( 0, 0 );
	cer_notify_query( "UPDATE {$prefix}evt_registrations SET ticket_sent_at = '" . $today->modify( '-1 hour' )->format( 'Y-m-d H:i:s' ) . "' WHERE event_id = {$vendor_event} AND vendor_notified_at IS NULL" );
	$yesterday_failed = $insert_vendor_row( 'yesterday@example.test', $today->modify( '-2 hours' )->format( 'Y-m-d H:i:s' ) );
	$today_row = $insert_vendor_row( 'today@example.test', $today->modify( '+30 minutes' )->format( 'Y-m-d H:i:s' ) );
	delete_option( 'cer_vendor_digest_last_date' );
	$mail = array();
	cer_process_vendor_notifications( $today->setTime( 2, 0 ) );
	cer_notify_assert( empty( $mail ), 'The vendor digest must not be sent before 03:05 Nairobi time.' );
	cer_process_vendor_notifications( $today->setTime( 3, 10 ) );
	$digests = $vendor_mail();
	cer_notify_assert( 1 === count( $mail ) && 1 === count( $digests ) && 0 === strpos( $digests[0]['subject'], 'Daily registrations for Vendor fixture' ), 'One daily digest per event must be sent at 03:05.' );
	cer_notify_assert( false !== strpos( $digests[0]['message'], 'queued@example.test' ) && false !== strpos( $digests[0]['message'], 'yesterday@example.test' ) && false === strpos( $digests[0]['message'], 'today@example.test' ) && false === strpos( $digests[0]['message'], 'historic@example.test' ), 'The digest must list only the previous day\'s new admissions.' );
	cer_notify_assert( in_array( 'Reply-To: organizer@example.test', $digests[0]['headers'], true ), 'Digest replies must go to the event Sender Email.' );
	cer_notify_assert( null !== $wpdb->get_var( "SELECT vendor_notified_at FROM {$prefix}evt_registrations WHERE id = {$yesterday_failed}" ) && null === $wpdb->get_var( "SELECT vendor_notified_at FROM {$prefix}evt_registrations WHERE id = {$today_row}" ), 'Digested rows are marked and later rows stay queued.' );
	$mail = array();
	cer_process_vendor_notifications( $today->setTime( 4, 0 ) );
	cer_notify_assert( empty( $mail ), 'Only one digest may be sent per day.' );
	$mail_success = false;
	cer_process_vendor_notifications( $today->modify( '+1 day' )->setTime( 3, 10 ) );
	cer_notify_assert( 1 === count( $mail ) && null === $wpdb->get_var( "SELECT vendor_notified_at FROM {$prefix}evt_registrations WHERE id = {$today_row}" ), 'A failed digest must leave its admissions queued.' );
	$mail_success = true;
	$mail = array();
	cer_process_vendor_notifications( $today->modify( '+2 days' )->setTime( 3, 10 ) );
	cer_notify_assert( 1 === count( $mail ) && false !== strpos( $mail[0]['message'], 'today@example.test' ), 'Failed digest admissions must be carried into the next digest.' );

	$insert_vendor_row( 'catchup1@example.test', current_time( 'mysql' ) );
	$insert_vendor_row( 'catchup2@example.test', current_time( 'mysql' ) );
	$wpdb->update( $prefix . 'evt_events', array( 'event_date' => current_datetime()->modify( '+30 hours' )->format( 'Y-m-d H:i:s' ) ), array( 'id' => $vendor_event ) );
	$mail = array();
	cer_process_vendor_notifications( current_datetime() );
	cer_notify_assert( 1 === count( $mail ) && 0 === strpos( $mail[0]['subject'], 'Registration summary for Vendor fixture' ) && false !== strpos( $mail[0]['message'], 'catchup1@example.test' ) && false !== strpos( $mail[0]['message'], 'catchup2@example.test' ), 'Entering the 36-hour window must send one catch-up summary.' );
	$mail = array();
	$live = cer_process_registration_submission( array_merge( $payload, array( 'event_id' => $vendor_event, 'ticket_type_id' => $vendor_ticket, 'email' => 'live@example.test' ) ) );
	$live_notices = $vendor_mail();
	cer_notify_assert( $live['success'] && 2 === count( $mail ) && 1 === count( $live_notices ) && 0 === strpos( $live_notices[0]['subject'], 'New registration for Vendor fixture' ), 'Inside the window each admission must notify the vendor immediately.' );
	cer_notify_assert( in_array( 'Reply-To: live@example.test', $live_notices[0]['headers'], true ), 'Real-time notices keep the attendee as Reply-To.' );
	$mail = array();
	cer_process_vendor_notifications( current_datetime() );
	cer_notify_assert( empty( $mail ), 'Real-time notices must not be repeated by the worker.' );
	cer_clear_event_notifications();
	cer_notify_assert( false === wp_next_scheduled( 'cer_event_notifications' ), 'Deactivation must remove the recurring notification hook.' );
	echo "Event notifications integration tests passed (real isolated MySQL, PDF generation, intercepted mail).\n";
} finally {
	$wpdb->select( DB_NAME );
	if ( $created ) {
		$wpdb->query( "DROP DATABASE `{$database}`" );
	}
	foreach ( glob( $directory . DIRECTORY_SEPARATOR . 'tickets' . DIRECTORY_SEPARATOR . 'ticket-*.pdf' ) ?: array() as $pdf ) {
		unlink( $pdf );
	}
	if ( is_dir( $directory . DIRECTORY_SEPARATOR . 'tickets' ) ) {
		rmdir( $directory . DIRECTORY_SEPARATOR . 'tickets' );
	}
	if ( is_dir( $directory ) ) {
		rmdir( $directory );
	}
	wp_cache_flush();
}
