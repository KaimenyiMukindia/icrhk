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
function cer_registration_search_hash( $email ) { return hash( 'sha256', strtolower( trim( $email ) ) ); }
function cer_send_ticket_for_registration( $id ) { return $GLOBALS['ticket_sends_succeed'] ?? true; }
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
	public $tickets = null;
	public $max_attendees = null;
	public $paid = 0;
	public $paid_counts = array();
	public $insert_id = 42;
	public $duplicate = false;
	public function __construct( $price ) { $this->price = $price; }
	public function prepare( $query, ...$args ) { return $query; }
	public function get_row( $query ) {
		if ( false !== strpos( $query, 'evt_registrations' ) ) {
			return $this->duplicate ? (object) array( 'id' => $this->duplicate, 'status' => 'confirmed' ) : null;
		}
		if ( false !== strpos( $query, 'evt_events' ) ) {
			return (object) array( 'id' => 1, 'max_attendees' => $this->max_attendees, 'slug' => 'test-event', 'summary_note' => $this->summary_note );
		}
		return (object) array( 'id' => 2, 'name' => 'Delegate', 'price' => $this->price, 'quantity_available' => 10, 'quantity_sold' => 0 );
	}
	public function get_var( $query ) { return $this->paid_counts ? array_shift( $this->paid_counts ) : $this->paid; }
	public function get_results( $query ) { return $this->tickets ?? array( $this->get_row( 'evt_ticket_types' ) ); }
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
		expect_feedback( $result['success'] && 'not_required' === $result['data']['payment_status'], 'Unpriced tickets must confirm admission without payment.' );
		expect_feedback( $wpdb->saved['email'] === $payload['email'] && '254712345678' === $wpdb->saved['phone'], 'Contacts must be persisted before acknowledgement.' );
		expect_feedback( null === $wpdb->saved['reserved_until'] && 1 === $wpdb->ticket_updates && 'confirmed' === $wpdb->saved['status'], 'Unpriced tickets must consume inventory permanently on confirmation.' );
		expect_feedback( false !== strpos( $result['data']['message'], 'Thank you' ) && false === strpos( $result['data']['message'], 'price' ), 'No-cost confirmation must thank the attendee without discussing prices.' );
		expect_feedback( 0 === $gateway_calls, 'Unpriced ticket must not call the gateway.' );
	}
	$wpdb = new CER_Feedback_DB_Fixture( 0 );
	$wpdb->duplicate = 42;
	$result = cer_process_registration_submission( $payload );
	expect_feedback( ! $result['success'] && false !== strpos( $result['message'], 'already registered' ) && empty( $wpdb->saved ), 'Duplicate no-cost admission must be rejected before insertion.' );
	expect_feedback( in_array( 'ROLLBACK', $wpdb->queries, true ), 'Duplicate registration must release transactional locks.' );
	$wpdb = new CER_Feedback_DB_Fixture( 0 );
	$ticket_sends_succeed = false;
	$result = cer_process_registration_submission( $payload );
	expect_feedback( $result['success'] && ! $result['data']['ticket_sent'] && 'confirmed' === $wpdb->saved['status'], 'Email failure must preserve confirmed admission and report pending delivery.' );
	expect_feedback( false !== strpos( $result['data']['message'], 'retry automatically' ), 'Delivery failure must be explicit, not claim an email was sent.' );
	$ticket_sends_succeed = true;
}
foreach ( array( 'mpesa', 'card' ) as $method ) {
	$wpdb = new CER_Feedback_DB_Fixture( '100.00' );
	$gateway_calls = 0;
	$result = cer_process_registration_submission( array_merge( $payload, array( 'payment_method' => $method ) ) );
	expect_feedback( $result['success'] && 'fixture-reference' === $result['data']['reference'], 'Priced tickets must keep their gateway contract.' );
	expect_feedback( 1 === $gateway_calls && 1 === $wpdb->ticket_updates && null !== $wpdb->saved['reserved_until'], 'Priced ticket reservation must be preserved.' );
	expect_feedback( '100.00' === $wpdb->saved['amount'], 'Price must come from the ticket, not the submitted amount.' );
}
foreach ( array( 0, 100 ) as $price ) {
	$wpdb = new CER_Feedback_DB_Fixture( $price );
	$wpdb->max_attendees = 1;
	$wpdb->paid_counts = array( 0, 1 );
	$gateway_calls = 0;
	$result = cer_process_registration_submission( $payload );
	expect_feedback( ! $result['success'] && 'This event has reached its attendance capacity.' === $result['message'], 'Capacity must be rechecked after the event lock for both free and paid tickets.' );
	expect_feedback( empty( $wpdb->saved ) && 0 === $gateway_calls && in_array( 'ROLLBACK', $wpdb->queries, true ), 'A newly full event must not save or start checkout.' );
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
	preg_match( '/<div id="cer-form-message"[^>]*>(.*?)<\/div>/s', $markup, $status_region );
	expect_feedback( 1 === substr_count( $status_region[1], $secure_copy ), 'New secure-registration copy must appear exactly once in the visible status region.' );
	expect_feedback( false !== strpos( $markup, 'aria-live="polite"' ), 'Feedback must be announced accessibly.' );
}
$wpdb->summary_note = 'Custom event payment guidance.';
expect_feedback( false !== strpos( cer_get_registration_form_markup( 1 ), $wpdb->summary_note ), 'Custom event notes must be preserved.' );
foreach ( array( null, '', '0', '100.00' ) as $price ) {
	$wpdb = new CER_Feedback_DB_Fixture( $price );
	$markup = cer_get_registration_form_markup( 1 );
	expect_feedback( false === strpos( $markup, '<select' ), 'A single ticket must not render a dropdown.' );
	expect_feedback( false !== strpos( $markup, 'id="cer-ticket-type" name="ticket_type" type="hidden" value="Delegate"' ), 'A single ticket name must still be submitted.' );
	expect_feedback( false !== strpos( $markup, 'name="ticket_type_id" value="2"' ), 'A single ticket id must be selected without JavaScript.' );
	expect_feedback( false !== strpos( $markup, 'Ticket: <strong>Delegate</strong>' ), 'A single ticket must have a visible label.' );
	foreach ( array( 'full_name', 'email', 'phone' ) as $field ) {
		expect_feedback( false !== strpos( $markup, 'name="' . $field . '"' ), 'Contact fields must be retained.' );
	}
	$required = (float) $price > 0;
	expect_feedback( false !== strpos( $markup, 'data-payment-required="' . ( $required ? 'true' : 'false' ) . '"' ), 'Initial payment UI must reflect the stored price.' );
	if ( ! $required ) {
		expect_feedback( false !== strpos( $markup, '>Register</button>' ), 'Unpriced tickets need a registration-only action.' );
		expect_feedback( false !== strpos( $markup, 'No payment is required to submit this form.' ), 'Unpriced tickets need registration-only guidance.' );
	}
}
$wpdb->tickets = array(
	(object) array( 'id' => 2, 'name' => 'Contact registration', 'price' => null, 'quantity_available' => null, 'quantity_sold' => 0 ),
	(object) array( 'id' => 3, 'name' => 'Paid ticket', 'price' => '100.00', 'quantity_available' => null, 'quantity_sold' => 0 ),
);
$markup = cer_get_registration_form_markup( 1 );
expect_feedback( 1 === substr_count( $markup, '<select' ), 'Multiple tickets must retain the selector.' );
expect_feedback( false !== strpos( $markup, '>Contact registration</option>' ), 'Unpriced options must not display a misleading zero-price charge.' );
expect_feedback( false !== strpos( $markup, '>Paid ticket (KES 100.00)</option>' ), 'Paid option labels must retain their prices.' );
$wpdb->tickets = array();
$markup = cer_get_registration_form_markup( 1 );
expect_feedback( false !== strpos( $markup, 'No ticket types available' ), 'No-ticket events must not auto-select a fictitious ticket.' );
function cer_test_ticket( $id, $name, $price, $available, $sold ) {
	return (object) array( 'id' => $id, 'name' => $name, 'price' => $price, 'quantity_available' => $available, 'quantity_sold' => $sold );
}
$wpdb = new CER_Feedback_DB_Fixture( 100 );
$wpdb->tickets = array( cer_test_ticket( 2, 'Early bird', '100.00', 5, 5 ), cer_test_ticket( 3, 'Regular', '200.00', 5, 1 ), cer_test_ticket( 4, 'VIP', '500.00', null, 9 ) );
$markup = cer_get_registration_form_markup( 1 );
expect_feedback( false !== strpos( $markup, 'data-price="100.00" disabled data-sold-out="true">Early bird (KES 100.00) - Sold out</option>' ), 'Sold-out options must be disabled and labelled.' );
expect_feedback( false !== strpos( $markup, '>Regular (KES 200.00)</option>' ) && false !== strpos( $markup, '>VIP (KES 500.00)</option>' ), 'Available options must remain selectable.' );
expect_feedback( false === strpos( $markup, 'cer-sold-out' ), 'Partial sell-out must not lock the form.' );
$wpdb->tickets = array( cer_test_ticket( 2, 'Early bird', '100.00', 5, 5 ), cer_test_ticket( 3, 'Regular', '0', 5, 1 ) );
$markup = cer_get_registration_form_markup( 1 );
expect_feedback( false === strpos( $markup, '<select' ) && false !== strpos( $markup, 'Ticket: <strong>Regular</strong>' ), 'One remaining ticket must display without a dropdown.' );
expect_feedback( false !== strpos( $markup, 'data-payment-required="false"' ), 'Payment must follow the remaining ticket, not the sold-out one.' );
foreach ( array(
	array( array( cer_test_ticket( 2, 'Delegate', '100.00', 3, 3 ) ), null, 0, 'Tickets for this event are sold out.' ),
	array( array( cer_test_ticket( 2, 'A', '100.00', 3, 3 ), cer_test_ticket( 3, 'B', '0', 1, 4 ) ), null, 0, 'Tickets for this event are sold out.' ),
	array( array( cer_test_ticket( 2, 'Delegate', '100.00', null, 0 ) ), 50, 50, 'This event is fully booked.' ),
) as $case ) {
	$wpdb = new CER_Feedback_DB_Fixture( 100 );
	list( $wpdb->tickets, $wpdb->max_attendees, $wpdb->paid, $copy ) = $case;
	$markup = cer_get_registration_form_markup( 1 );
	expect_feedback( false !== strpos( $markup, 'cer-sold-out' ) && false !== strpos( $markup, 'disabled>Sold Out</button>' ), 'Sold-out events must lock the form.' );
	expect_feedback( false !== strpos( $markup, 'data-payment-required="false"' ), 'Sold-out events must hide payment UI.' );
	preg_match( '/<div id="cer-form-message"[^>]*>(.*?)<\/div>/s', $markup, $status_region );
	expect_feedback( false !== strpos( $status_region[1], $copy ), 'Sold-out copy must use the shared status region.' );
}
$wpdb = new CER_Feedback_DB_Fixture( 100 );
$wpdb->max_attendees = 50;
$wpdb->paid = 49;
expect_feedback( false === strpos( cer_get_registration_form_markup( 1 ), 'cer-sold-out' ), 'Events below capacity must remain open.' );
echo "Registration feedback handler tests passed.\n";
