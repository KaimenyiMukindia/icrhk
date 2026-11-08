<?php

// Execute the existing handler with an isolated database/gateway fixture, never a live charge.
$tokens = token_get_all( file_get_contents( dirname( __DIR__ ) . '/custom-event-registration.php' ) );
$function_source = '';
$collecting = false;
$depth = 0;
$opened = false;
foreach ( $tokens as $index => $token ) {
	if ( is_array( $token ) && T_FUNCTION === $token[0] ) {
		$next = $index + 1;
		while ( is_array( $tokens[ $next ] ) && T_WHITESPACE === $tokens[ $next ][0] ) {
			++$next;
		}
		$collecting = is_array( $tokens[ $next ] ) && 'cer_process_registration_submission' === $tokens[ $next ][1];
	}
	if ( ! $collecting ) {
		continue;
	}
	$function_source .= is_array( $token ) ? $token[1] : $token;
	if ( '{' === $token || ( is_array( $token ) && in_array( $token[0], array( T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES ), true ) ) ) {
		++$depth;
		$opened = true;
	} elseif ( '}' === $token && 0 === --$depth && $opened ) {
		break;
	}
}
if ( '' === $function_source ) {
	throw new RuntimeException( 'Registration handler not found.' );
}
eval( $function_source );

function wp_unslash( $value ) { return $value; }
function sanitize_text_field( $value ) { return trim( strip_tags( $value ) ); }
function sanitize_textarea_field( $value ) { return sanitize_text_field( $value ); }
function sanitize_email( $value ) { return filter_var( $value, FILTER_VALIDATE_EMAIL ) ?: ''; }
function absint( $value ) { return abs( (int) $value ); }
function wp_verify_nonce( $value, $action ) { return 'valid' === $value; }
function wp_generate_uuid4() { return uniqid( 'test-', true ); }
function current_time( $type ) { return '2026-11-08 10:00:00'; }
function cer_get_registration_hold_seconds() { return 600; }
function cer_prepare_registration_row( $row ) { return $row; }
function delete_transient( $name ) {}
function wp_schedule_single_event( $time, $hook, $args ) { return true; }
function wp_strip_all_tags( $value ) { return strip_tags( $value ); }
function wp_kses_post( $value ) { return $value; }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES ); }
function esc_html( $value ) { return esc_attr( $value ); }
function number_format_i18n( $value, $decimals ) { return number_format( $value, $decimals ); }
function get_bloginfo( $name ) { return 'Test Events'; }
function wp_nonce_field( $action, $name ) { echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="valid">'; }
function cer_initiate_laravel_payment( $data ) {
	global $gateway_calls, $gateway_fails;
	++$gateway_calls;
	if ( $gateway_fails ) {
		throw new Exception( 'Laravel bridge payment call returned HTTP 404: Not Found' );
	}
	return array( 'reference' => 'fixture-reference', 'access_code' => 'fixture-code', 'status' => 'pending' );
}

class CER_Feedback_DB_Fixture {
	public $prefix = 'wp_';
	public $last_error = '';
	public $price;
	public $saved = array();
	public $ticket_updates = 0;
	public $insert_fails = false;
	public $queries = array();
	public $summary_note = '';
	public function __construct( $price ) { $this->price = $price; }
	public function prepare( $query, ...$args ) { return $query; }
	public function get_row( $query ) {
		if ( false !== strpos( $query, 'evt_events' ) ) {
			return (object) array( 'id' => 1, 'max_attendees' => null, 'slug' => 'test-event', 'summary_note' => $this->summary_note );
		}
		return (object) array( 'id' => 2, 'name' => 'Delegate', 'price' => $this->price, 'quantity_available' => 10, 'quantity_sold' => 0 );
	}
	public function get_var( $query ) { return 0; }
	public function get_results( $query ) { return array( $this->get_row( 'evt_ticket_types' ) ); }
	public function query( $query ) { $this->queries[] = $query; return 1; }
	public function insert( $table, $row, $formats ) {
		if ( $this->insert_fails ) { return false; }
		$this->saved = $row;
		return 1;
	}
	public function update( $table, $row, $where, $formats, $where_formats ) {
		if ( false !== strpos( $table, 'ticket_types' ) ) {
			++$this->ticket_updates;
		}
		return 1;
	}
}

function expect_feedback( $condition, $message ) {
	if ( ! $condition ) { throw new RuntimeException( $message ); }
}
$payload = array(
	'security' => 'valid', 'full_name' => 'Test Visitor', 'email' => 'test@example.com',
	'phone' => '0712345678', 'ticket_type_id' => 2, 'event_id' => 1, 'payment_method' => 'mpesa',
	'amount' => 999,
);
$gateway_fails = false;
foreach ( array( null, '', 0, '0.00' ) as $price ) {
	foreach ( array( 'mpesa', 'card' ) as $method ) {
		$wpdb = new CER_Feedback_DB_Fixture( $price );
		$gateway_calls = 0;
		$result = cer_process_registration_submission( array_merge( $payload, array( 'payment_method' => $method ) ) );
		expect_feedback( $result['success'] && 'not_on_sale' === $result['data']['payment_status'], 'Unpriced ticket must report not on sale.' );
		expect_feedback( $wpdb->saved['email'] === $payload['email'] && '254712345678' === $wpdb->saved['phone'], 'Contacts must be persisted before acknowledgement.' );
		expect_feedback( null === $wpdb->saved['reserved_until'] && 0 === $wpdb->ticket_updates, 'Unpriced tickets must not consume inventory.' );
		expect_feedback( 0 === $gateway_calls, 'Unpriced ticket must not call the gateway.' );
	}
}
foreach ( array( 'mpesa', 'card' ) as $method ) {
	$wpdb = new CER_Feedback_DB_Fixture( '100.00' );
	$gateway_calls = 0;
	$result = cer_process_registration_submission( array_merge( $payload, array( 'payment_method' => $method ) ) );
	expect_feedback( $result['success'] && 'fixture-reference' === $result['data']['reference'], 'Priced tickets must keep their gateway contract.' );
	expect_feedback( 1 === $gateway_calls && 1 === $wpdb->ticket_updates && null !== $wpdb->saved['reserved_until'], 'Priced ticket reservation must be preserved.' );
	expect_feedback( '100.00' === $wpdb->saved['amount'], 'Price must come from the ticket, not the submitted amount.' );
}
$gateway_fails = true;
$wpdb = new CER_Feedback_DB_Fixture( 100 );
$result = cer_process_registration_submission( $payload );
expect_feedback( ! $result['success'] && false === strpos( $result['message'], 'HTTP' ), 'Gateway diagnostics must not be exposed.' );
expect_feedback( false !== strpos( $result['message'], 'saved your registration details' ), 'Gateway failure must acknowledge persisted contacts.' );
$wpdb = new CER_Feedback_DB_Fixture( null );
$wpdb->insert_fails = true;
$gateway_calls = 0;
$result = cer_process_registration_submission( $payload );
expect_feedback( ! $result['success'] && empty( $wpdb->saved ) && 0 === $gateway_calls, 'Failed contact persistence must not claim success.' );
expect_feedback( in_array( 'ROLLBACK', $wpdb->queries, true ), 'Failed contact persistence must roll back.' );

define( 'ABSPATH', dirname( __DIR__ ) );
require dirname( __DIR__ ) . '/includes/registration-functions.php';
$secure_copy = 'Your registration is secure. Once submitted, payment verification and your receipt will be processed automatically through our secure gateway.';
foreach ( array(
	'',
	'Payment verification and receipt delivery are handled after registration using a secure gateway workflow.',
	'Payment verification and receipt delivery are handled after registration using the secure gateway workflow.',
) as $note ) {
	$wpdb = new CER_Feedback_DB_Fixture( 100 );
	$wpdb->summary_note = $note;
	$markup = cer_get_registration_form_markup( 1 );
	expect_feedback( 1 === substr_count( $markup, 'id="cer-form-message"' ), 'There must be one shared status region.' );
	expect_feedback( 1 === substr_count( $markup, $secure_copy ), 'New secure-registration copy must appear exactly once.' );
	expect_feedback( false !== strpos( $markup, 'aria-live="polite"' ), 'Feedback must be announced accessibly.' );
}
$wpdb->summary_note = 'Custom event payment guidance.';
expect_feedback( false !== strpos( cer_get_registration_form_markup( 1 ), $wpdb->summary_note ), 'Custom event notes must be preserved.' );
echo "Registration feedback handler tests passed.\n";
