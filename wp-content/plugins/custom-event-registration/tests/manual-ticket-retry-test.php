<?php

if ( PHP_SAPI !== 'cli' ) {
	exit( "Run this test from the command line.\n" );
}

$tokens = token_get_all( file_get_contents( dirname( __DIR__ ) . '/includes/class-cer-ticket-service.php' ) );
$source = '';
$collecting = false;
$depth = 0;
$opened = false;
foreach ( $tokens as $index => $token ) {
	if ( is_array( $token ) && T_FUNCTION === $token[0] ) {
		$next = $index + 1;
		while ( is_array( $tokens[ $next ] ) && T_WHITESPACE === $tokens[ $next ][0] ) {
			++$next;
		}
		$collecting = is_array( $tokens[ $next ] ) && 'cer_retry_unsent_ticket_for_registration' === $tokens[ $next ][1];
	}
	if ( ! $collecting ) {
		continue;
	}
	$source .= is_array( $token ) ? $token[1] : $token;
	if ( '{' === $token || ( is_array( $token ) && in_array( $token[0], array( T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES ), true ) ) ) {
		++$depth;
		$opened = true;
	} elseif ( '}' === $token && 0 === --$depth && $opened ) {
		break;
	}
}
if ( '' === $source ) {
	throw new RuntimeException( 'Ticket retry function not found.' );
}
eval( $source );

class WP_Error {
	public $code;
	public function __construct( $code, $message ) { $this->code = $code; }
}
function __( $message, $domain ) { return $message; }
function cer_registration_ticket_is_valid( $row ) { return ( $row['admission_quantity'] ?? 1 ) > 0 && ( 'paid' === $row['status'] || ( 'confirmed' === $row['status'] && $row['amount'] <= 0 ) ); }
function cer_send_ticket_for_registration( $id ) {
	++$GLOBALS['send_calls'];
	return $GLOBALS['send_success'];
}
class Ticket_Retry_DB {
	public $prefix = 'wp_';
	public $last_error = '';
	public $row = null;
	public $query = '';
	public function prepare( $query, $id ) { return $query; }
	public function get_row( $query, $format ) { $this->query = $query; return $this->row; }
}
define( 'ARRAY_A', 'ARRAY_A' );
$wpdb = new Ticket_Retry_DB();
$send_calls = 0;
$send_success = true;

function expect_ticket_retry( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

$wpdb->row = array( 'status' => 'paid', 'amount' => 100, 'ticket_sent_at' => null );
expect_ticket_retry( 'sent' === cer_retry_unsent_ticket_for_registration( 1 ) && 1 === $send_calls, 'Paid admission should use the existing sender.' );
expect_ticket_retry( false !== strpos( $wpdb->query, 'deleted_at IS NULL' ), 'Trashed registrations must be excluded.' );
$wpdb->row = array( 'status' => 'confirmed', 'amount' => 0, 'ticket_sent_at' => null );
expect_ticket_retry( 'sent' === cer_retry_unsent_ticket_for_registration( 2 ) && 2 === $send_calls, 'Free confirmed admission should use the existing sender.' );
$wpdb->row['ticket_sent_at'] = '2026-01-01 10:00:00';
expect_ticket_retry( 'already_sent' === cer_retry_unsent_ticket_for_registration( 2 ) && 2 === $send_calls, 'Delivered tickets must not be sent again.' );
$wpdb->row = array( 'status' => 'pending', 'amount' => 100, 'ticket_sent_at' => null );
expect_ticket_retry( cer_retry_unsent_ticket_for_registration( 3 ) instanceof WP_Error && 2 === $send_calls, 'Unpaid admission must not be sent.' );
$wpdb->row = null;
expect_ticket_retry( cer_retry_unsent_ticket_for_registration( 4 ) instanceof WP_Error && 2 === $send_calls, 'Missing admission must not be sent.' );
$wpdb->row = array( 'status' => 'paid', 'amount' => 100, 'ticket_sent_at' => null );
$send_success = false;
expect_ticket_retry( cer_retry_unsent_ticket_for_registration( 5 ) instanceof WP_Error && 3 === $send_calls, 'Mail failure must be reported.' );
echo "Manual ticket retry tests passed.\n";
