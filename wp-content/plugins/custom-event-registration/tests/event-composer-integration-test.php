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

function cer_composer_test_assert( bool $condition, string $message ): void {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}
function cer_composer_test_query( string $query ): void {
	global $wpdb;
	if ( false === $wpdb->query( $query ) ) {
		throw new RuntimeException( $wpdb->last_error );
	}
}
$database = 'cer_composer_test_' . bin2hex( random_bytes( 6 ) );
$directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $database;
$prefix = $wpdb->prefix;
cer_composer_test_assert( 1 === preg_match( '/^[A-Za-z0-9_]+$/', $prefix ) && 1 === preg_match( '/^[A-Za-z0-9_]+$/', DB_NAME ), 'Unsafe schema name.' );
$created = false;
$keep_browser_fixture = false;
$wpdb->suppress_errors( true );
try {
	cer_composer_test_query( "CREATE DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci" );
	$created = true;
	foreach ( array( 'options', 'posts', 'postmeta', 'users', 'usermeta', 'evt_events', 'evt_ticket_types', 'evt_registrations', 'evt_speakers', 'evt_sponsorships', 'evt_pillars', 'evt_objectives', 'evt_summit_structure', 'evt_partners', 'evt_faqs' ) as $suffix ) {
		cer_composer_test_query( "CREATE TABLE `{$database}`.`{$prefix}{$suffix}` LIKE `" . DB_NAME . "`.`{$prefix}{$suffix}`" );
	}
	$wpdb->select( $database );
	wp_cache_flush();
	add_filter( 'pre_option_home', static function () { return 'http://127.0.0.1'; } );
	add_filter( 'upload_dir', static function ( $uploads ) use ( $directory ) {
		$uploads['basedir'] = $directory;
		$uploads['path'] = $directory;
		$uploads['error'] = false;
		return $uploads;
	} );
	$gateway_status = 'pending';
	$gateway_calls = 0;
	$gateway_initiation_fails = false;
	$gateway_payloads = array();
	add_filter( 'pre_http_request', static function ( $pre, $args, $url ) use ( &$gateway_status, &$gateway_calls, &$gateway_initiation_fails, &$gateway_payloads ) {
		if ( false !== strpos( $url, '/api/payment/verify' ) ) {
			return array( 'response' => array( 'code' => 200, 'message' => 'OK' ), 'body' => wp_json_encode( array( 'status' => $gateway_status, 'registration_status' => 'pending', 'message' => 'Fixture gateway status' ) ), 'headers' => array(), 'cookies' => array() );
		}
		if ( false !== strpos( $url, '/api/payment/initiate' ) ) {
			++$gateway_calls;
			if ( $gateway_initiation_fails ) {
				return new WP_Error( 'http_request_failed', 'Fixture gateway timeout' );
			}
			$payload = json_decode( $args['body'], true );
			$gateway_payloads[] = $payload;
			return array( 'response' => array( 'code' => 200, 'message' => 'OK' ), 'body' => wp_json_encode( array( 'status' => 'pending', 'reference' => $payload['payment_uuid'], 'access_code' => 'fixture', 'authorization_url' => 'https://checkout.paystack.com/fixture' ) ), 'headers' => array(), 'cookies' => array() );
		}
		throw new RuntimeException( 'Tests must never make external HTTP requests.' );
	}, 1, 3 );
	require dirname( __DIR__ ) . '/custom-event-registration.php';
	cer_composer_test_assert( array( 1, 2 ) === cer_parse_event_mail_recipient_json( '[1,2]' ), 'The enhanced picker must submit selected IDs as a single JSON field.' );
	foreach ( array( '[1,', '[1,"2"]', '[0]', '{"other":1}', array( 1 ) ) as $invalid_selection ) {
		cer_composer_test_assert( is_wp_error( cer_parse_event_mail_recipient_json( $invalid_selection ) ), 'Invalid recipient JSON must fail explicitly.' );
	}
	remove_filter( 'pre_wp_mail', 'cer_maybe_send_via_resend', 10 );
	$mail = array();
	$mail_result = true;
	add_filter( 'pre_wp_mail', static function ( $pre, $atts ) use ( &$mail, &$mail_result ) { $mail[] = $atts; return $mail_result; }, 1, 2 );
	cer_composer_test_assert( false !== $wpdb->insert( $prefix . 'evt_events', array(
		'event_uuid' => wp_generate_uuid4(), 'name' => 'Composer fixture', 'slug' => $database,
		'event_date' => '2030-01-01 10:00:00', 'status' => 'published', 'mail_sender_email' => 'sender@example.test',
	) ), 'Could not create event.' );
	$event = (int) $wpdb->insert_id;
	cer_composer_test_assert( false !== $wpdb->insert( $prefix . 'evt_ticket_types', array(
		'event_id' => $event, 'name' => 'Delegate', 'price' => 0,
	) ), 'Could not create ticket.' );
	$ticket = (int) $wpdb->insert_id;
	cer_composer_test_assert( false !== $wpdb->insert( $prefix . 'evt_events', array(
		'event_uuid' => wp_generate_uuid4(), 'name' => 'Other fixture event', 'slug' => $database . '-other',
		'event_date' => '2030-01-01 10:00:00', 'status' => 'published',
	) ), 'Could not create second event.' );
	$other_event = (int) $wpdb->insert_id;
	$wrong_event = cer_assign_admin_ticket( array(
		'event_id' => $other_event, 'ticket_type_id' => $ticket, 'full_name' => 'Wrong event fixture',
		'email' => 'wrong-event@example.test', 'amount' => '0', 'payment_method' => 'offline', 'reason' => 'Fixture',
	) );
	cer_composer_test_assert( is_wp_error( $wrong_event ), 'Server-side assignment must reject a ticket belonging to another event.' );
	$ids = array();
	for ( $i = 0; $i < 13; ++$i ) {
		$row = cer_prepare_registration_row( array(
			'registration_uuid' => wp_generate_uuid4(), 'payment_uuid' => wp_generate_uuid4(), 'event_id' => $event,
			'ticket_type_id' => $ticket, 'full_name' => 'Fixture ' . $i, 'email' => 'person' . $i . '@example.test',
			'phone' => '', 'ticket_type' => 'Delegate', 'payment_method' => 'none', 'amount' => 0,
			'status' => 12 === $i ? 'pending' : 'confirmed',
		) );
		cer_composer_test_assert( false !== $wpdb->insert( $prefix . 'evt_registrations', $row ), 'Could not create recipient.' );
		$ids[] = (int) $wpdb->insert_id;
	}
	cer_composer_test_assert( 13 === cer_queue_event_mail( $event, 'Fixture subject', '<p><strong>Hello</strong></p>', $ids, true ), 'Batch should queue all selected records.' );
	cer_composer_test_assert( is_wp_error( cer_queue_event_mail( $event, 'Another', '<p>Hello</p>', $ids, false ) ), 'An active batch must not be overwritten.' );
	cer_process_event_mail_batch( $event );
	$job = get_option( cer_event_mail_job_key( $event ) );
	cer_composer_test_assert( 10 === count( $mail ) && 3 === count( array_filter( $job['recipients'], static function ( $state ) { return 'queued' === $state; } ) ), 'The first run must send at most ten individual messages.' );
	cer_process_event_mail_batch( $event );
	$job = get_option( cer_event_mail_job_key( $event ) );
	cer_composer_test_assert( 12 === count( $mail ) && 'failed' === $job['recipients'][ $ids[12] ], 'Pending admissions must not receive attached tickets.' );
	$attachments = array();
	foreach ( $mail as $message ) {
		cer_composer_test_assert( is_string( $message['to'] ) && in_array( 'Cc: sender@example.test', $message['headers'], true ), 'Each recipient must get an individual message copied to the event sender.' );
		cer_composer_test_assert( 1 === count( $message['attachments'] ) && is_file( $message['attachments'][0] ), 'Each confirmed recipient must get a generated PDF.' );
		$attachments[] = $message['attachments'][0];
	}
	cer_composer_test_assert( 12 === count( array_unique( $attachments ) ), 'Recipients must not share ticket PDFs.' );
	cer_process_event_mail_batch( $event );
	cer_composer_test_assert( 12 === count( $mail ), 'A completed batch must not be resent.' );
	cer_composer_test_assert( 13 === cer_queue_event_mail( $event, 'All recipients', '<p>Hello everyone</p>', array(), false, true ), 'All-recipient mode must load registrations server-side without posting their IDs.' );
	cer_process_event_mail_batch( $event );
	cer_process_event_mail_batch( $event );
	cer_composer_test_assert( 25 === count( $mail ), 'All active registrations must receive one individual message when no ticket is attached.' );
	$wpdb->update( $prefix . 'evt_registrations', array( 'ticket_sent_at' => null, 'ticket_generated_at' => null ), array( 'id' => $ids[0] ) );
	$mail_result = false;
	cer_composer_test_assert( 1 === cer_queue_event_mail( $event, 'Failed delivery', '<p>Hello</p>', array( $ids[0] ), true ), 'A single selected recipient must queue.' );
	cer_process_event_mail_batch( $event );
	$failed_job = get_option( cer_event_mail_job_key( $event ) );
	cer_composer_test_assert( 'failed' === $failed_job['recipients'][ $ids[0] ] && ! empty( $failed_job['errors'][ $ids[0] ] ) && null === $wpdb->get_var( $wpdb->prepare( "SELECT ticket_sent_at FROM {$prefix}evt_registrations WHERE id = %d", $ids[0] ) ), 'Failed mail must be reported without marking the ticket sent.' );
	$mail_result = true;
	$failed_job['recipients'][ $ids[0] ] = 'sending';
	cer_save_event_mail_job( $event, $failed_job );
	$before_uncertain = count( $mail );
	cer_process_event_mail_batch( $event );
	cer_composer_test_assert( $before_uncertain === count( $mail ) && is_wp_error( cer_queue_event_mail( $event, 'Blocked', '<p>Hello</p>', array( $ids[0] ), false ) ), 'An interrupted send must not be automatically resent or overwritten.' );
	$failed_job['recipients'][ $ids[0] ] = 'failed';
	cer_save_event_mail_job( $event, $failed_job );
	cer_composer_test_assert( is_wp_error( cer_queue_event_mail( $event, 'Invalid mode', '<p>Hello</p>', array( $ids[0] ), false, false, 'unknown' ) ), 'Invalid delivery mode must fail.' );
	cer_composer_test_assert( 1 === cer_queue_event_mail( $event, 'Scheduled fixture', '<p>Hello later</p>', array( $ids[0] ), false, false, 'daily' ), 'A daily batch must be saved.' );
	$scheduled = get_option( cer_event_mail_job_key( $event ) );
	$scheduled_local = ( new DateTimeImmutable( '@' . $scheduled['not_before'] ) )->setTimezone( new DateTimeZone( 'Africa/Nairobi' ) );
	cer_composer_test_assert( '03:05' === $scheduled_local->format( 'H:i' ) && $scheduled['not_before'] > time(), 'Daily delivery must target the next 03:05 Nairobi window.' );
	$before_scheduled = count( $mail );
	cer_process_event_mail_batch( $event );
	cer_process_scheduled_event_mail( current_datetime() );
	cer_composer_test_assert( $before_scheduled === count( $mail ), 'A scheduled batch must not send early, including from an existing immediate cron event.' );
	$scheduled['not_before'] = time() - 1;
	cer_save_event_mail_job( $event, $scheduled );
	cer_process_scheduled_event_mail( current_datetime() );
	cer_composer_test_assert( $before_scheduled + 1 === count( $mail ) && 'sent' === get_option( cer_event_mail_job_key( $event ) )['recipients'][ $ids[0] ], 'The existing notification worker must deliver due scheduled messages.' );
	$first_job = str_repeat( 'a', 32 );
	$second_job = str_repeat( 'b', 32 );
	cer_composer_test_assert( 1 === cer_queue_event_mail( $event, 'First group', '<p>First</p>', array( $ids[0] ), false, false, 'now', $first_job ), 'The first message must queue independently.' );
	cer_composer_test_assert( 1 === cer_queue_event_mail( $event, 'Second group', '<p>Second</p>', array( $ids[1] ), false, false, 'daily', $second_job ), 'A second message must queue while the first is active.' );
	cer_composer_test_assert( is_wp_error( cer_queue_event_mail( $event, 'Collision', '<p>No</p>', array( $ids[0] ), false, false, 'now', $first_job ) ), 'An existing batch identifier must never be overwritten.' );
	cer_process_event_mail_batch( $event, $first_job );
	cer_composer_test_assert( 'sent' === get_option( cer_event_mail_job_key( $event, $first_job ) )['recipients'][ $ids[0] ] && 'queued' === get_option( cer_event_mail_job_key( $event, $second_job ) )['recipients'][ $ids[1] ], 'Sending one batch must leave the other scheduled batch intact.' );
	$second = get_option( cer_event_mail_job_key( $event, $second_job ) );
	$second['not_before'] = time() - 1;
	cer_save_event_mail_job( $event, $second, $second_job );
	$wpdb->update( $prefix . 'evt_registrations', array( 'ticket_sent_at' => current_time( 'mysql' ) ), array( 'id' => $ids[0] ) );
	cer_process_event_notifications();
	cer_composer_test_assert( 'sent' === get_option( cer_event_mail_job_key( $event, $second_job ) )['recipients'][ $ids[1] ], 'The notification worker must process each independently scheduled batch.' );
	$formatted_body = "Dear attendee,\n\nPlease read <strong>carefully</strong>.<br />Keep this line together.\n\n"
		. '<h2 style="text-align: center;">Event details</h2>'
		. '<ul><li>First bullet</li><li>Second bullet<ul><li>Nested bullet</li></ul></li></ul>'
		. '<ol start="3"><li>Third step</li><li>Fourth step</li></ol>'
		. '<p><a href="https://example.test/agenda?a=1&amp;b=2" target="_blank" rel="noopener">View agenda</a> &amp; details</p>'
		. '<p style="text-align: right;"><span style="color: #123456;"><em>Thank you</em></span></p>'
		. '<blockquote><p>A quotation</p></blockquote>'
		. '<table><tbody><tr><td>Time</td><td>09:00</td></tr></tbody></table>'
		. '<p><img src="https://example.test/banner.png" alt="Event banner" width="240" height="80" /></p>'
		. "<pre>Keep  spaces\nand line breaks</pre>";
	$formatted_body = wp_kses_post( $formatted_body );
	$expected_body = wpautop( $formatted_body );
	$document = new DOMDocument();
	cer_composer_test_assert( $document->loadHTML( '<!doctype html><html><body>' . $expected_body . '</body></html>' ), 'Formatted email must remain parseable HTML.' );
	$xpath = new DOMXPath( $document );
	foreach ( array(
		'//p[text()="Dear attendee,"]',
		'//p[strong="carefully" and br]',
		'//ul/li[2]/ul/li[text()="Nested bullet"]',
		'//ol[@start="3"]/li[text()="Third step"]',
		'//h2[contains(@style, "text-align: center")]',
		'//a[@href="https://example.test/agenda?a=1&b=2"]',
		'//span[contains(@style, "color: #123456")]/em',
		'//blockquote/p',
		'//table/tbody/tr/td[text()="09:00"]',
		'//img[@alt="Event banner" and @width="240"]',
	) as $format_query ) {
		cer_composer_test_assert( 1 === $xpath->query( $format_query )->length, 'Email formatting was lost: ' . $format_query );
	}
	cer_composer_test_assert( "Keep  spaces\nand line breaks" === $xpath->query( '//pre' )->item( 0 )->textContent, 'Preformatted content must retain spaces and line breaks.' );
	foreach ( array( 'now', 'daily' ) as $format_delivery ) {
		$format_job_id = bin2hex( random_bytes( 16 ) );
		cer_composer_test_assert( 2 === cer_queue_event_mail( $event, 'Formatted ' . $format_delivery, $formatted_body, array( $ids[0] ), true, false, $format_delivery, $format_job_id, array( 'formatted@example.test' ) ), 'Formatted registered and added-address messages must use the existing queue.' );
		$format_job = get_option( cer_event_mail_job_key( $event, $format_job_id ) );
		cer_composer_test_assert( $expected_body === $format_job['body'], 'The queue must restore editor paragraph breaks without escaping rich HTML.' );
		$before_format = count( $mail );
		if ( 'daily' === $format_delivery ) {
			cer_process_event_mail_batch( $event, $format_job_id );
			cer_composer_test_assert( $before_format === count( $mail ), 'Formatted scheduled messages must not send early.' );
			$format_job['not_before'] = time() - 1;
			cer_save_event_mail_job( $event, $format_job, $format_job_id );
		}
		cer_process_event_mail_batch( $event, $format_job_id );
		$formatted_mail = array_slice( $mail, $before_format );
		cer_composer_test_assert( 2 === count( $formatted_mail ), 'Both recipient paths must deliver the formatted message.' );
		foreach ( $formatted_mail as $message ) {
			cer_composer_test_assert( $expected_body === $message['message'] && in_array( 'Content-Type: text/html; charset=UTF-8', $message['headers'], true ), 'Delivered HTML must match the prepared editor content exactly, including formatting and links.' );
		}
		cer_composer_test_assert( 1 === count( $formatted_mail[0]['attachments'] ) && empty( $formatted_mail[1]['attachments'] ), 'HTML formatting must not change existing ticket attachment rules.' );
	}
	$legacy_key = $wpdb->get_var( $wpdb->prepare( "SELECT user_access_key FROM {$prefix}evt_registrations WHERE id = %d", $ids[12] ) );
	$assignment = cer_assign_admin_ticket( array(
		'event_id' => $event, 'ticket_type_id' => $ticket, 'registration_id' => $ids[12],
		'amount' => '2500.00', 'payment_method' => 'offline', 'offline_confirmed' => 1,
		'reason' => 'Student; offline receipt', 'ticket_title' => 'Student', 'ticket_color' => '#123456',
	) );
	cer_composer_test_assert( ! is_wp_error( $assignment ), 'An eligible existing registration should be assigned: ' . ( is_wp_error( $assignment ) ? $assignment->get_error_message() : '' ) );
	$assigned_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_registrations WHERE id = %d", $ids[12] ), ARRAY_A );
	cer_composer_test_assert( 'paid' === $assigned_row['status'] && 'offline' === $assigned_row['payment_method'] && 'Student' === $assigned_row['ticket_display_title'] && '#123456' === $assigned_row['ticket_display_color'] && $legacy_key === $assigned_row['user_access_key'] && ! empty( $assigned_row['ticket_sent_at'] ), 'Offline assignment must preserve the access key, price and customized ticket and send it.' );
	cer_composer_test_assert( is_wp_error( cer_assign_admin_ticket( array(
		'event_id' => $event, 'ticket_type_id' => $ticket, 'full_name' => 'Missing confirmation', 'email' => 'offline@example.test',
		'amount' => '2500', 'payment_method' => 'offline', 'reason' => 'Student',
	) ) ), 'A nonzero offline assignment must require explicit payment confirmation.' );
	$card = cer_assign_admin_ticket( array(
		'event_id' => $event, 'ticket_type_id' => $ticket, 'full_name' => 'Card Attendee', 'email' => 'card@example.test',
		'amount' => '2500', 'payment_method' => 'card', 'reason' => 'Student', 'ticket_title' => 'Student', 'email_checkout' => 1,
	) );
	cer_composer_test_assert( ! is_wp_error( $card ) && 1 === $gateway_calls, 'Admin card assignment must initiate exactly one checkout.' );
	$card_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_registrations WHERE id = %d", $card['id'] ), ARRAY_A );
	cer_composer_test_assert( 'pending' === $card_row['status'] && 'https://checkout.paystack.com/fixture' === $card_row['payment_checkout_url'] && null === $card_row['ticket_sent_at'], 'Card checkout must remain unpaid and expose its saved link without sending a ticket.' );
	cer_composer_test_assert( false !== strpos( end( $mail )['message'], 'KES 2500.00' ), 'The optional checkout email must show the discounted amount.' );
	cer_composer_test_assert( is_wp_error( cer_retry_registration_payment( $card['id'] ) ) && 1 === $gateway_calls, 'Pending or unknown gateway outcomes must never start another charge.' );
	$gateway_status = 'unknown';
	cer_composer_test_assert( is_wp_error( cer_retry_registration_payment( $card['id'] ) ) && 1 === $gateway_calls, 'An unknown gateway outcome must never start another charge.' );
	$gateway_status = 'failed';
	$old_uuid = $card_row['payment_uuid'];
	$inventory_before = (int) $wpdb->get_var( $wpdb->prepare( "SELECT quantity_sold FROM {$prefix}evt_ticket_types WHERE id = %d", $ticket ) );
	$retried = cer_retry_registration_payment( $card['id'] );
	cer_composer_test_assert( ! is_wp_error( $retried ) && 2 === $gateway_calls, 'A verified failed payment should start one new checkout.' );
	$card_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_registrations WHERE id = %d", $card['id'] ), ARRAY_A );
	cer_composer_test_assert( $old_uuid !== $card_row['payment_uuid'] && $inventory_before === (int) $wpdb->get_var( $wpdb->prepare( "SELECT quantity_sold FROM {$prefix}evt_ticket_types WHERE id = %d", $ticket ) ), 'A retry must get a new reference without double-counting an existing hold.' );
	$gateway_initiation_fails = true;
	$failed_payment = cer_assign_admin_ticket( array(
		'event_id' => $event, 'ticket_type_id' => $ticket, 'full_name' => 'M-Pesa Attendee', 'email' => 'mpesa@example.test',
		'phone' => '0712345678', 'amount' => '1250', 'payment_method' => 'mpesa', 'reason' => 'Student',
	) );
	$failed_row = $wpdb->get_row( $wpdb->prepare( "SELECT status, payment_error FROM {$prefix}evt_registrations WHERE id = %d", $failed_payment['id'] ), ARRAY_A );
	cer_composer_test_assert( 'failed' === $failed_row['status'] && ! empty( $failed_row['payment_error'] ) && ! empty( $failed_payment['error'] ) && false !== strpos( $failed_payment['message'], 'Verify' ), 'An M-Pesa initiation timeout must persist a visible error and require verification.' );
	$gateway_initiation_fails = false;
	foreach ( array( '0', '21', '1.5', '-1', '01', true, array( 2 ) ) as $invalid_quantity ) {
		cer_composer_test_assert( is_wp_error( cer_parse_ticket_quantity( $invalid_quantity ) ), 'Quantity must be a strict integer from 1 through 20.' );
	}
	cer_composer_test_assert( 20 === cer_parse_ticket_quantity( '20' ), 'The maximum purchase is twenty tickets.' );
	foreach ( array(
		array( '01' => array( 'quantity' => '1', 'amount' => '10' ) ),
		array( 1 => array( 'quantity' => array( 1 ), 'amount' => '10' ) ),
		array( 1 => array( 'quantity' => '1', 'amount' => array( '10' ) ) ),
		array( 1 => array( 'quantity' => '1', 'amount' => '10.001' ) ),
		array( 1 => array( 'quantity' => '20', 'amount' => '10' ), 2 => array( 'quantity' => '1', 'amount' => '10' ) ),
	) as $invalid_lines ) {
		cer_composer_test_assert( is_wp_error( cer_parse_admin_assignment_lines( array( 'tickets' => $invalid_lines ) ) ), 'Invalid IDs, quantities, prices and over-limit baskets must be rejected.' );
	}
	$parsed_lines = cer_parse_admin_assignment_lines( array( 'tickets' => array(
		2 => array( 'quantity' => '2', 'amount' => '10.05' ),
		1 => array( 'quantity' => '1', 'amount' => '0' ),
	) ) );
	cer_composer_test_assert( array( 1, 2 ) === array_keys( $parsed_lines ) && 2010 === $parsed_lines[2]['cents'], 'Assignment lines must retain integer-cent amounts in deterministic lock order.' );
	cer_composer_test_assert( is_wp_error( cer_parse_group_attendees( "Invalid,not-an-email\n" ) ), 'Bulk attendee errors must reject the entire input.' );
	cer_composer_test_assert( 'Quoted, Name' === cer_parse_group_attendees( '"Quoted, Name",guest@example.test' )[0]['full_name'], 'CSV quoting must be decoded rather than stored.' );
	cer_composer_test_assert( false !== $wpdb->insert( $prefix . 'evt_events', array(
		'event_uuid' => wp_generate_uuid4(), 'name' => 'Bulk fixture', 'slug' => $database . '-bulk',
		'event_date' => '2030-01-01 10:00:00', 'status' => 'published', 'max_attendees' => 3, 'mail_sender_email' => 'sender@example.test',
	) ), 'Could not create bulk event.' );
	$bulk_event = (int) $wpdb->insert_id;
	$wpdb->insert( $prefix . 'evt_ticket_types', array( 'event_id' => $bulk_event, 'name' => 'Group delegates', 'price' => 0, 'quantity_available' => 3 ) );
	$bulk_ticket = (int) $wpdb->insert_id;
	$markup = cer_get_registration_form_markup( $bulk_event, cer_get_ticket_availability( $bulk_event ) );
	cer_composer_test_assert( false !== strpos( $markup, 'data-ticket-plus' ) && false !== strpos( $markup, 'data-ticket-minus' )
		&& false !== strpos( $markup, 'name="ticket_quantities[' . $bulk_ticket . ']"' )
		&& false === strpos( $markup, '<details class="cer-ticket-picker"' )
		&& false === strpos( $markup, 'cer-quantity-field' ), 'One type must show inline quantity controls in its existing ticket field without a selector or separate quantity section.' );
	$bulk_payload = array(
		'security' => wp_create_nonce( 'cer_registration_form' ), 'full_name' => 'Group Buyer', 'email' => 'buyer@example.test',
		'payment_method' => 'card', 'ticket_type_id' => $bulk_ticket, 'event_id' => $bulk_event, 'ticket_quantity' => '3',
		'accessibility_support' => 'no',
	);
	$purchase = cer_process_registration_submission( $bulk_payload );
	cer_composer_test_assert( $purchase['success'] && $purchase['data']['ticket_sent'], 'The real public handler must confirm and email a three-seat free purchase.' );
	$parent_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(id) FROM {$prefix}evt_registrations WHERE event_id = %d", $bulk_event ) );
	$parent = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_registrations WHERE id = %d", $parent_id ), ARRAY_A );
	$original_key = $parent['user_access_key'];
	$original_pdf = cer_ticket_pdf_path( $parent );
	cer_composer_test_assert( 3 === (int) $parent['ticket_quantity'] && 3 === (int) $parent['admission_quantity']
		&& 0 === cer_get_ticket_availability( $bulk_event )['remaining'] && is_file( $original_pdf ), 'A group must reserve exactly its purchased seats and generate one group PDF.' );
	cer_composer_test_assert( false !== strpos( end( $mail )['message'], 'individual names and email addresses' ), 'Group mail must request individual attendee details.' );
	cer_composer_test_assert( ! cer_process_registration_submission( array_merge( $bulk_payload, array( 'email' => 'overflow@example.test', 'ticket_quantity' => '1' ) ) )['success'], 'A second buyer must not exceed the shared event inventory.' );
	$allocation = cer_allocate_group_tickets( $parent_id, "First Guest,first@example.test\nSecond Guest,", $original_key );
	cer_composer_test_assert( ! is_wp_error( $allocation ) && 1 === $allocation['remaining'] && 2 === count( $allocation['ids'] ) && ! $allocation['failed'], 'Partial allocation must issue two individual tickets and one replacement group ticket.' );
	$parent = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_registrations WHERE id = %d", $parent_id ), ARRAY_A );
	cer_composer_test_assert( $original_key !== $parent['user_access_key'] && ! is_file( $original_pdf )
		&& null === $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$prefix}evt_registrations WHERE user_access_key = %s", $original_key ) ), 'Partial allocation must remove the obsolete PDF and invalidate its code.' );
	cer_composer_test_assert( 0 === cer_get_ticket_availability( $bulk_event )['remaining'] && 3 === (int) $wpdb->get_var( $wpdb->prepare( "SELECT quantity_sold FROM {$prefix}evt_ticket_types WHERE id = %d", $bulk_ticket ) ), 'Allocation must not consume inventory again.' );
	$child = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_registrations WHERE id = %d", $allocation['ids'][1] ), ARRAY_A );
	cer_composer_test_assert( cer_registration_ticket_is_valid( $child ) && 'buyer@example.test' === cer_decrypt_pii( $child['email'] ), 'Missing individual email must deliver that attendee’s ticket to the purchaser.' );
	cer_composer_test_assert( is_wp_error( cer_allocate_group_tickets( $parent_id, 'Replay,replay@example.test', $original_key ) ), 'A stale or replayed allocation must not create duplicate seats.' );
	cer_composer_test_assert( is_wp_error( cer_allocate_group_tickets( $parent_id, "Too Many,a@example.test\nSeats,b@example.test", $parent['user_access_key'] ) ), 'Allocation must reject more than the remaining seats atomically.' );
	$replacement_pdf = cer_ticket_pdf_path( $parent );
	$final_allocation = cer_allocate_group_tickets( $parent_id, 'Last Guest,last@example.test', $parent['user_access_key'] );
	$parent = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_registrations WHERE id = %d", $parent_id ), ARRAY_A );
	cer_composer_test_assert( ! is_wp_error( $final_allocation ) && 0 === $final_allocation['remaining'] && ! cer_registration_ticket_is_valid( $parent )
		&& ! is_file( $replacement_pdf ) && '' === cer_generate_ticket_pdf( $parent_id ) && is_wp_error( cer_retry_unsent_ticket_for_registration( $parent_id ) ), 'Fully allocated roots must remain financial history, not downloadable or resendable admissions.' );
	cer_composer_test_assert( ! $final_allocation['notice_failed'] && false !== strpos( end( $mail )['message'], 'previous group ticket and its code are invalid' ), 'Full replacement must inform the purchaser that their group ticket is invalid.' );
	$wpdb->update( $prefix . 'evt_registrations', array( 'deleted_at' => current_time( 'mysql' ) ), array( 'id' => $parent_id ) );
	cer_composer_test_assert( ! cer_registration_ticket_is_valid( $child ), 'Trashing a group must invalidate all its individual children.' );
	$wpdb->update( $prefix . 'evt_registrations', array( 'deleted_at' => null ), array( 'id' => $parent_id ) );
	cer_composer_test_assert( cer_registration_ticket_is_valid( $child ), 'Restoring a group must restore its active individual admissions without increasing stock.' );
	$wpdb->update( $prefix . 'evt_events', array( 'max_attendees' => 6 ), array( 'id' => $bulk_event ) );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'price' => 1000, 'quantity_available' => 6 ), array( 'id' => $bulk_ticket ) );
	$paid_purchase = cer_process_registration_submission( array_merge( $bulk_payload, array( 'email' => 'paidbuyer@example.test', 'amount' => '1', 'ticket_quantity' => '3' ) ) );
	cer_composer_test_assert( $paid_purchase['success'], 'A paid three-seat purchase must start checkout using the existing gateway bridge.' );
	$paid_parent_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(id) FROM {$prefix}evt_registrations WHERE event_id = %d", $bulk_event ) );
	$paid_parent = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_registrations WHERE id = %d", $paid_parent_id ), ARRAY_A );
	cer_composer_test_assert( 3000.0 === (float) $paid_parent['amount'] && 3 === (int) $paid_parent['ticket_quantity']
		&& 6 === (int) $wpdb->get_var( $wpdb->prepare( "SELECT quantity_sold FROM {$prefix}evt_ticket_types WHERE id = %d", $bulk_ticket ) ), 'Server-side unit price times quantity must determine payment and held stock; client amount is ignored.' );
	$wpdb->update( $prefix . 'evt_registrations', array( 'reserved_until' => '2000-01-01 00:00:00' ), array( 'id' => $paid_parent_id ) );
	cer_composer_test_assert( 1 === cer_release_expired_registrations() && 3 === (int) $wpdb->get_var( $wpdb->prepare( "SELECT quantity_sold FROM {$prefix}evt_ticket_types WHERE id = %d", $bulk_ticket ) ), 'Reservation expiry must release the whole group exactly once.' );
	cer_composer_test_assert( 0 === cer_release_expired_registrations(), 'Repeating expiry must not release the group twice.' );
	$group_retry = cer_retry_registration_payment( $paid_parent_id );
	cer_composer_test_assert( ! is_wp_error( $group_retry ) && 6 === (int) $wpdb->get_var( $wpdb->prepare( "SELECT quantity_sold FROM {$prefix}evt_ticket_types WHERE id = %d", $bulk_ticket ) ), 'A verified failed group retry must reserve exactly its three seats again.' );
	cer_composer_test_assert( cer_confirm_registration_paid( $paid_parent_id )['ok'] && 6 === (int) $wpdb->get_var( $wpdb->prepare( "SELECT quantity_sold FROM {$prefix}evt_ticket_types WHERE id = %d", $bulk_ticket ) ), 'Confirmation must consume the existing group hold without adding stock twice.' );
	cer_composer_test_assert( cer_confirm_registration_paid( $paid_parent_id )['already_paid'], 'Duplicate group confirmation must remain idempotent.' );
	$wpdb->update( $prefix . 'evt_events', array( 'max_attendees' => 8 ), array( 'id' => $bulk_event ) );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'quantity_available' => 8 ), array( 'id' => $bulk_ticket ) );
	$admin_group = cer_assign_admin_ticket( array(
		'event_id' => $bulk_event, 'ticket_type_id' => $bulk_ticket, 'full_name' => 'Student Group', 'email' => 'student@example.test',
		'ticket_quantity' => '2', 'amount' => '500', 'payment_method' => 'offline', 'offline_confirmed' => 1, 'reason' => 'Student discount',
		'ticket_title' => 'Student', 'ticket_color' => '#123456',
	) );
	$admin_row = ! is_wp_error( $admin_group ) ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_registrations WHERE id = %d", $admin_group['id'] ), ARRAY_A ) : array();
	cer_composer_test_assert( $admin_row && 1000.0 === (float) $admin_row['amount'] && 2 === (int) $admin_row['admission_quantity'], 'Admin group prices must be per seat and retain existing title and color customization.' );
	$mail_result = false;
	$failed_allocation = cer_allocate_group_tickets( $admin_group['id'], 'Retry Guest,retryguest@example.test', $admin_row['user_access_key'] );
	cer_composer_test_assert( ! is_wp_error( $failed_allocation ) && 2 === count( $failed_allocation['failed'] ), 'Mail failure must persist the issued child and replacement group for manual recovery, never roll back committed seat allocation.' );
	$mail_result = true;
	foreach ( $failed_allocation['failed'] as $failed_id ) {
		cer_composer_test_assert( 'sent' === cer_retry_unsent_ticket_for_registration( $failed_id ), 'Missing child and replacement mail must recover through the existing manual retry.' );
	}
	$address_job_id = str_repeat( 'c', 32 );
	cer_composer_test_assert( is_wp_error( cer_parse_event_mail_addresses( "valid@example.test\ninvalid" ) ), 'Invalid added addresses must be reported, not silently omitted.' );
	cer_composer_test_assert( 2 === cer_queue_event_mail( $bulk_event, 'Mixed audience', '<p><strong>Mixed</strong></p>', array( $child['id'] ), true, false, 'now', $address_job_id, array( 'new@example.test', 'NEW@example.test', 'buyer@example.test' ) ), 'Registered and bulk added recipients must share one queue without duplicate addresses.' );
	$before_mixed = count( $mail );
	cer_process_event_mail_batch( $bulk_event, $address_job_id );
	$mixed_mail = array_slice( $mail, $before_mixed );
	cer_composer_test_assert( 2 === count( $mixed_mail ) && 1 === count( $mixed_mail[0]['attachments'] ) && ! $mixed_mail[1]['attachments']
		&& in_array( 'Cc: sender@example.test', $mixed_mail[1]['headers'], true ), 'A mixed batch must attach only the registered attendee’s own ticket, CC Sender Email and keep added recipients separate.' );
	$wpdb->insert( $prefix . 'evt_events', array(
		'event_uuid' => wp_generate_uuid4(), 'name' => 'Mixed checkout', 'slug' => $database . '-mixed',
		'event_date' => '2030-01-01 10:00:00', 'status' => 'published', 'max_attendees' => 10, 'mail_sender_email' => 'sender@example.test',
	) );
	$mixed_event = (int) $wpdb->insert_id;
	$wpdb->insert( $prefix . 'evt_ticket_types', array( 'event_id' => $mixed_event, 'name' => 'Delegate', 'price' => 5000, 'quantity_available' => 5 ) );
	$delegate_type = (int) $wpdb->insert_id;
	$wpdb->insert( $prefix . 'evt_ticket_types', array( 'event_id' => $mixed_event, 'name' => 'Student', 'price' => 2500, 'quantity_available' => 5 ) );
	$student_type = (int) $wpdb->insert_id;
	$mixed_payload = array_merge( $bulk_payload, array(
		'event_id' => $mixed_event, 'ticket_type_id' => $delegate_type, 'email' => 'mixedbuyer@example.test',
		'amount' => '1', 'ticket_quantities' => array( $delegate_type => '2', $student_type => '1' ),
	) );
	$before_gateway = $gateway_calls;
	$before_mail = count( $mail );
	$mixed_purchase = cer_process_registration_submission( $mixed_payload );
	$mixed_rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$prefix}evt_registrations WHERE event_id = %d ORDER BY id", $mixed_event ), ARRAY_A );
	cer_composer_test_assert( $mixed_purchase['success'] && $gateway_calls === $before_gateway + 1 && count( $mail ) === $before_mail
		&& 12500.0 === (float) end( $gateway_payloads )['amount'] && 2 === count( $mixed_rows ), 'Two types must reserve two registrations and initiate exactly one server-priced checkout without early ticket delivery.' );
	cer_composer_test_assert( $mixed_rows[0]['checkout_uuid'] === $mixed_rows[1]['checkout_uuid']
		&& $mixed_rows[0]['registration_uuid'] !== $mixed_rows[1]['registration_uuid'] && $mixed_rows[0]['payment_uuid'] !== $mixed_rows[1]['payment_uuid']
		&& 10000.0 === (float) $mixed_rows[0]['amount'] && 2500.0 === (float) $mixed_rows[1]['amount']
		&& 2 === (int) $mixed_rows[0]['ticket_quantity'] && 1 === (int) $mixed_rows[1]['ticket_quantity'], 'Each type must keep its own UUID, quantity and line amount under the shared checkout.' );
	$mixed_leader = (int) $mixed_rows[0]['id'];
	$mixed_other = (int) $mixed_rows[1]['id'];
	cer_composer_test_assert( 7 === cer_get_ticket_availability( $mixed_event )['remaining'], 'A basket must consume shared capacity only once per seat.' );
	$wpdb->update( $prefix . 'evt_registrations', array( 'reserved_until' => '2000-01-01 00:00:00' ), array( 'checkout_uuid' => $mixed_rows[0]['checkout_uuid'] ) );
	cer_composer_test_assert( 2 === cer_release_expired_registrations() && 10 === cer_get_ticket_availability( $mixed_event )['remaining'], 'Expiry must release both types independently without retaining basket stock.' );
	$gateway_status = 'failed';
	cer_composer_test_assert( ! is_wp_error( cer_retry_registration_payment( $mixed_other ) )
		&& 12500.0 === (float) end( $gateway_payloads )['amount'] && 7 === cer_get_ticket_availability( $mixed_event )['remaining'], 'Retry from either type must reserve and charge the entire basket once.' );
	cer_composer_test_assert( cer_confirm_registration_paid( $mixed_other )['ok'], 'Manual confirmation from either type must confirm the complete checkout atomically.' );
	$mixed_rows = cer_checkout_registrations( $mixed_leader );
	foreach ( $mixed_rows as $line ) {
		cer_composer_test_assert( 'paid' === $line['status'] && cer_send_ticket_for_registration( (int) $line['id'] ), 'Each confirmed type must receive its own existing ticket.' );
	}
	cer_composer_test_assert( cer_ticket_pdf_path( $mixed_rows[0] ) !== cer_ticket_pdf_path( $mixed_rows[1] )
		&& 7 === cer_get_ticket_availability( $mixed_event )['remaining'], 'Different ticket types must produce distinct PDFs without duplicate stock consumption.' );
	$mixed_markup = cer_get_registration_form_markup( $mixed_event, cer_get_ticket_availability( $mixed_event ) );
	$previous_errors = libxml_use_internal_errors( true );
	$document = new DOMDocument();
	$document->loadHTML( $mixed_markup );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous_errors );
	$xpath = new DOMXPath( $document );
	cer_composer_test_assert( 3 === $xpath->query( '//select[@id="cer-ticket-type"]/option' )->length
		&& 2 === $xpath->query( '//form//input[@data-ticket-quantity]' )->length
		&& 1 === $xpath->query( '//form//button[@data-cer-next]' )->length
		&& 2 === $xpath->query( '//form//div[@data-cer-step]' )->length, 'The parsed form must preserve both steps and all ticket options without swallowing markup into a select.' );
	cer_composer_test_assert( false !== strpos( $mixed_markup, '<details class="cer-ticket-picker"' )
		&& false !== strpos( $mixed_markup, '<small>5000</small>' ) && false === strpos( $mixed_markup, '(5000.00)' ), 'The existing ticket selection must contain compact per-type prices and controls.' );
	$before_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}evt_registrations" );
	foreach ( array(
		array( $delegate_type => '1', $ticket => '1' ),
		array( $delegate_type => '20', $student_type => '1' ),
		array( $delegate_type => '1.5' ), array( $delegate_type => '0', $student_type => '0' ),
		array( $delegate_type => '1', $student_type => '5' ),
	) as $invalid_basket ) {
		cer_composer_test_assert( ! cer_process_registration_submission( array_merge( $mixed_payload, array( 'ticket_quantities' => $invalid_basket ) ) )['success']
			&& $before_count === (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}evt_registrations" ), 'Invalid, mismatched and sold-out baskets must reject atomically without saved rows.' );
	}
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'price' => 0 ), array( 'id' => $student_type ) );
	$zero_line_purchase = cer_process_registration_submission( array_merge( $mixed_payload, array(
		'email' => 'mixedfree@example.test', 'ticket_quantities' => array( $delegate_type => '1', $student_type => '1' ),
	) ) );
	cer_composer_test_assert( $zero_line_purchase['success'] && 5000.0 === (float) end( $gateway_payloads )['amount'], 'A paid/free basket must wait for one checkout and charge only its paid line.' );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'price' => 0 ), array( 'id' => $delegate_type ) );
	$before_gateway = $gateway_calls;
	$before_mail = count( $mail );
	$free_basket = cer_process_registration_submission( array_merge( $mixed_payload, array(
		'email' => 'allfreebasket@example.test', 'ticket_quantities' => array( $delegate_type => '1', $student_type => '1' ),
	) ) );
	cer_composer_test_assert( $free_basket['success'] && $free_basket['data']['ticket_sent']
		&& $gateway_calls === $before_gateway && count( $mail ) === $before_mail + 2, 'Two free types must issue two existing tickets without a gateway charge.' );
	$wpdb->update( $prefix . 'evt_ticket_types', array( 'price' => 2500 ), array( 'id' => $student_type ) );
	$free_leader_purchase = cer_process_registration_submission( array_merge( $mixed_payload, array(
		'email' => 'freeleader@example.test', 'ticket_quantities' => array( $delegate_type => '1', $student_type => '1' ),
	) ) );
	$free_leader_rows = cer_checkout_registrations( (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(id) FROM {$prefix}evt_registrations WHERE event_id = %d", $mixed_event ) ) );
	cer_composer_test_assert( $free_leader_purchase['success'] && 0.0 === (float) $free_leader_rows[0]['amount']
		&& 2500.0 === (float) end( $gateway_payloads )['amount']
		&& ! is_wp_error( cer_retry_registration_payment( (int) $free_leader_rows[1]['id'] ) )
		&& 2500.0 === (float) end( $gateway_payloads )['amount']
		&& cer_confirm_registration_paid( (int) $free_leader_rows[0]['id'] )['ok'], 'A free coordinating line must still verify, retry and confirm the paid/free checkout using the aggregate amount.' );

	$wpdb->insert( $prefix . 'evt_events', array(
		'event_uuid' => wp_generate_uuid4(), 'name' => 'Admin basket fixture', 'slug' => $database . '-admin',
		'event_date' => '2030-01-01 10:00:00', 'status' => 'published', 'max_attendees' => 40, 'mail_sender_email' => 'sender@example.test',
	) );
	$admin_event = (int) $wpdb->insert_id;
	$wpdb->insert( $prefix . 'evt_ticket_types', array( 'event_id' => $admin_event, 'name' => 'Delegate', 'price' => 1000, 'quantity_available' => 20 ) );
	$admin_delegate = (int) $wpdb->insert_id;
	$wpdb->insert( $prefix . 'evt_ticket_types', array( 'event_id' => $admin_event, 'name' => 'Student', 'price' => 500, 'quantity_available' => 20 ) );
	$admin_student = (int) $wpdb->insert_id;
	$admin_input = array( 'event_id' => $admin_event, 'full_name' => 'Admin buyer', 'email' => 'adminbuyer@example.test', 'reason' => 'Fixture discount', 'offline_confirmed' => '1', 'payment_method' => 'offline',
		'tickets' => array( $admin_delegate => array( 'quantity' => '5', 'amount' => '500' ), $admin_student => array( 'quantity' => '2', 'amount' => '250' ) ) );
	$before_mail = count( $mail );
	$admin_basket = cer_assign_admin_ticket( $admin_input );
	cer_composer_test_assert( ! is_wp_error( $admin_basket ) && 2 === count( $admin_basket['ids'] ) && count( $mail ) === $before_mail + 2, 'Admin multi-type offline assignment must issue one existing ticket per type.' );
	$admin_roots = cer_checkout_registrations( $admin_basket['id'] );
	cer_composer_test_assert( 2500.0 === (float) $admin_roots[0]['amount'] && 500.0 === (float) $admin_roots[1]['amount'] && $admin_roots[0]['registration_uuid'] !== $admin_roots[1]['registration_uuid'], 'Admin basket must retain per-type quantities, discounted amounts and distinct UUIDs.' );
	$before_queries = $wpdb->num_queries;
	$start = microtime( true );
	$snapshot = cer_get_admin_assignment_inventory();
	$elapsed = ( microtime( true ) - $start ) * 1000;
	cer_composer_test_assert( 3 === $wpdb->num_queries - $before_queries && 33 === $snapshot[ $admin_event ]['availability']['remaining'] && '1000.00' === $snapshot[ $admin_event ]['availability']['tickets'][0]['price'], 'All event assignment details must preload in three queries using existing live capacity and stored prices.' );
	echo 'Assignment snapshot: 3 queries, ' . number_format( $elapsed, 2 ) . " ms (isolated fixture).\n";
	$before_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}evt_registrations" );
	$invalid_admin = $admin_input;
	$invalid_admin['email'] = 'invalid-admin@example.test';
	$invalid_admin['tickets'][ $admin_student ]['quantity'] = '20';
	cer_composer_test_assert( is_wp_error( cer_assign_admin_ticket( $invalid_admin ) ), 'Admin basket total above twenty must fail.' );
	$invalid_admin['tickets'][ $admin_student ] = array( 'quantity' => '1', 'amount' => '250' );
	$invalid_admin['tickets'][ $ticket ] = array( 'quantity' => '1', 'amount' => '0' );
	cer_composer_test_assert( is_wp_error( cer_assign_admin_ticket( $invalid_admin ) ) && $before_count === (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$prefix}evt_registrations" ) && 5 === (int) $wpdb->get_var( $wpdb->prepare( "SELECT quantity_sold FROM {$prefix}evt_ticket_types WHERE id = %d", $admin_delegate ) ), 'A wrong-event line must roll back every admin basket row and inventory increment.' );
	$gateway_admin = $admin_input;
	$gateway_admin['email'] = 'admincard@example.test';
	$gateway_admin['payment_method'] = 'card';
	$gateway_admin['tickets'] = array( $admin_delegate => array( 'quantity' => '1', 'amount' => '0' ), $admin_student => array( 'quantity' => '1', 'amount' => '250' ) );
	$before_gateway = $gateway_calls;
	$before_mail = count( $mail );
	$admin_card = cer_assign_admin_ticket( $gateway_admin );
	cer_composer_test_assert( ! is_wp_error( $admin_card ) && empty( $admin_card['error'] ) && $gateway_calls === $before_gateway + 1 && 250.0 === (float) end( $gateway_payloads )['amount'] && count( $mail ) === $before_mail, 'Admin paid/free basket must charge the aggregate once and defer all ticket mail.' );
	cer_composer_test_assert( cer_confirm_registration_paid( $admin_card['ids'][1] )['ok'], 'Existing confirmation from either admin line must confirm all types.' );
	$group = $admin_roots[0];
	$group_addresses = array( 'seat1@example.test', 'seat2@example.test', 'seat3@example.test', 'seat4@example.test', 'seat5@example.test' );
	cer_composer_test_assert( is_wp_error( cer_queue_group_event_mail( $other_event, (int) $group['id'], $group['user_access_key'], 'Allocation', '<p>Hello</p>', array(), $group_addresses, 'now' ) ), 'Group mail must reject a parent from another event.' );
	cer_composer_test_assert( is_wp_error( cer_queue_group_event_mail( $admin_event, (int) $group['id'], $group['user_access_key'], 'Allocation', '<p>Hello</p>', array(), array_slice( $group_addresses, 0, 4 ), 'now' ) ), 'Group replacement requires exactly one unique email for every remaining seat.' );
	$before_mail = count( $mail );
	$replacement = cer_queue_group_event_mail( $admin_event, (int) $group['id'], $group['user_access_key'], 'Individual group tickets', $formatted_body, array(), $group_addresses, 'daily' );
	$replaced_parent = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_registrations WHERE id = %d", $group['id'] ), ARRAY_A );
	$children = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$prefix}evt_registrations WHERE parent_registration_id = %d ORDER BY id", $group['id'] ), ARRAY_A );
	cer_composer_test_assert( 5 === $replacement && 5 === count( $children ) && 0 === (int) $replaced_parent['admission_quantity'] && $group['user_access_key'] !== $replaced_parent['user_access_key'] && ! cer_registration_ticket_is_valid( $replaced_parent ) && $group['amount'] === $replaced_parent['amount'] && count( $mail ) === $before_mail, 'Group mailing must preserve purchase totals, invalidate the old admission and defer five fresh individual tickets.' );
	cer_composer_test_assert( is_wp_error( cer_queue_group_event_mail( $admin_event, (int) $group['id'], $group['user_access_key'], 'Repeat', '<p>Hello</p>', array(), $group_addresses, 'now' ) ), 'A stale repeated allocation must not duplicate seats.' );
	$group_jobs = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $wpdb->esc_like( cer_event_mail_job_key( $admin_event ) . '_' ) . '%' ) );
	foreach ( $group_jobs as $key ) {
		$job = get_option( $key );
		$job_id = substr( $key, strlen( cer_event_mail_job_key( $admin_event ) . '_' ) );
		cer_process_event_mail_batch( $admin_event, $job_id );
		cer_composer_test_assert( count( $mail ) === $before_mail, 'Daily group tickets must not send before 03:05.' );
		$job['not_before'] = time() - 1;
		cer_save_event_mail_job( $admin_event, $job, $job_id );
	}
	foreach ( $group_jobs as $unused_job ) {
		cer_process_scheduled_event_mail( new DateTimeImmutable( 'now', new DateTimeZone( 'Africa/Nairobi' ) ) );
	}
	foreach ( $group_jobs as $key ) {
		$processed_job = get_option( $key );
		cer_composer_test_assert( ! in_array( 'failed', $processed_job['recipients'], true ) && ! in_array( 'queued', $processed_job['recipients'], true ), 'Group batch did not complete: ' . $key . ' due ' . $processed_job['not_before'] . ' now ' . time() . ' ' . wp_json_encode( $processed_job['errors'] ) . ' states ' . wp_json_encode( $processed_job['recipients'] ) );
	}
	$group_mail = array_filter( array_slice( $mail, $before_mail ), static function ( $message ) { return 'Individual group tickets' === $message['subject']; } );
	cer_composer_test_assert( 5 === count( $group_mail ), 'The existing daily batch must deliver exactly five individual-ticket messages.' );
	$pdfs = array();
	foreach ( $group_mail as $message ) {
		cer_composer_test_assert( $expected_body === $message['message'], 'Group replacement mail must preserve the same rich editor formatting.' );
		cer_composer_test_assert( 1 === count( $message['attachments'] ) && in_array( $message['to'], $group_addresses, true ) && in_array( 'Cc: sender@example.test', $message['headers'], true ), 'Every allocated recipient needs their own attachment through the existing CC/reply mail pipeline.' );
		$pdfs[] = $message['attachments'][0];
	}
	cer_composer_test_assert( 5 === count( array_unique( $pdfs ) ) && 6 === (int) $wpdb->get_var( $wpdb->prepare( "SELECT quantity_sold FROM {$prefix}evt_ticket_types WHERE id = %d", $admin_delegate ) ), 'Group-to-individual allocation must not consume stock again or reuse PDFs.' );

	$browser_manifest = getenv( 'CER_TEST_BROWSER_MANIFEST' );
	if ( $browser_manifest ) {
		cer_composer_test_assert( ! file_exists( $browser_manifest ) && is_dir( dirname( $browser_manifest ) ), 'Browser manifest path unavailable.' );
		$wpdb->insert( $prefix . 'evt_events', array(
			'event_uuid' => wp_generate_uuid4(), 'name' => 'Synthetic Browser Bulk Event', 'slug' => $database . '-browser',
			'event_date' => '2030-01-01 10:00:00', 'status' => 'published', 'max_attendees' => 20,
			'mail_sender_email' => 'sender@example.test', 'mail_notification_email' => 'vendor@example.test',
		) );
		$browser_event = (int) $wpdb->insert_id;
		$wpdb->insert( $prefix . 'evt_ticket_types', array( 'event_id' => $browser_event, 'name' => 'Delegate', 'price' => 250, 'quantity_available' => 20 ) );
		$browser_ticket = (int) $wpdb->insert_id;
		$wpdb->insert( $prefix . 'evt_ticket_types', array( 'event_id' => $browser_event, 'name' => 'Student', 'price' => 100, 'quantity_available' => 20 ) );
		$browser_group = cer_assign_admin_ticket( array( 'event_id' => $browser_event, 'ticket_type_id' => $browser_ticket, 'ticket_quantity' => '5', 'amount' => '0', 'full_name' => 'Synthetic Buyer', 'email' => 'synthetic-buyer@example.test', 'reason' => 'Browser fixture', 'payment_method' => 'offline' ) );
		cer_composer_test_assert( ! is_wp_error( $browser_group ), 'Browser group seed must succeed.' );
		$browser_token = bin2hex( random_bytes( 24 ) );
		cer_composer_test_query( 'CREATE TABLE cer_fixture_marker (token VARCHAR(48) NOT NULL PRIMARY KEY)' );
		$wpdb->insert( 'cer_fixture_marker', array( 'token' => $browser_token ) );
		cer_composer_test_assert( false !== file_put_contents( $browser_manifest, wp_json_encode( array(
			'database' => $database, 'token' => $browser_token, 'directory' => $directory, 'event_id' => $browser_event, 'group_id' => $browser_group['id'],
		) ), LOCK_EX ), 'Browser manifest could not be saved.' );
		$keep_browser_fixture = true;
	}
	echo "Event composer and admin ticket integration tests passed (isolated MySQL, intercepted mail and gateway).\n";
} finally {
	$wpdb->select( DB_NAME );
	if ( $created && ! $keep_browser_fixture ) {
		$wpdb->query( "DROP DATABASE `{$database}`" );
	}
	foreach ( $keep_browser_fixture ? array() : ( glob( $directory . DIRECTORY_SEPARATOR . 'tickets' . DIRECTORY_SEPARATOR . 'ticket-*.pdf' ) ?: array() ) as $pdf ) {
		unlink( $pdf );
	}
	if ( ! $keep_browser_fixture && is_dir( $directory . DIRECTORY_SEPARATOR . 'tickets' ) ) {
		rmdir( $directory . DIRECTORY_SEPARATOR . 'tickets' );
	}
	if ( ! $keep_browser_fixture && is_dir( $directory ) ) {
		rmdir( $directory );
	}
	wp_cache_flush();
}
