<?php

define( 'ABSPATH', dirname( __DIR__ ) );
define( 'ARRAY_A', 'ARRAY_A' );
define( 'CER_RESEND_API_KEY', 'fixture-key-not-a-real-credential' );
define( 'CER_RESEND_FROM', 'Fixture Tickets <tickets@example.test>' );

class WP_Error {
	public function __construct( public string $code, public string $message, public array $data = array() ) {}
}
function is_email( $email ) { return filter_var( $email, FILTER_VALIDATE_EMAIL ) ? $email : false; }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function wp_json_encode( $value ) { return json_encode( $value ); }
function wp_remote_post( $url, $args ) {
	++$GLOBALS['http_calls'];
	$GLOBALS['http_payload'] = json_decode( $args['body'], true );
	return $GLOBALS['http_response'];
}
function wp_remote_retrieve_response_code( $response ) { return $response['response']['code']; }
function wp_remote_retrieve_body( $response ) { return $response['body'] ?? ''; }
function do_action( $hook, $error ) { $GLOBALS['failures'][] = array( $hook, $error ); }
function cer_decrypt_pii( $value ) { return str_replace( 'fixture:', '', $value ); }
function wp_mail( $to, $subject, $message, $headers = array(), $attachments = array() ) {
	return cer_maybe_send_via_resend( null, compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );
}
function expect_mail_contract( bool $condition, string $message ): void {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}
class CER_Mail_Contract_DB_Fixture {
	public $prefix = 'wp_';
	public $event = array( 'mail_sender_email' => 'organizer@example.test' );
	public $registration = array(
		'event_id' => 7, 'event_name' => 'Fixture Event', 'ticket_type_id' => 3,
		'mail_notification_email' => 'vendor@example.test', 'email' => 'fixture:buyer@example.test',
		'full_name' => 'fixture:Test Buyer', 'amount' => 0,
	);
	public function prepare( $query, ...$args ) { return $query; }
	public function get_row( $query, $format ) {
		if ( false !== strpos( $query, 'evt_registrations r' ) ) {
			return $this->registration;
		}
		if ( false !== strpos( $query, 'evt_events WHERE' ) ) {
			return $this->event;
		}
		throw new RuntimeException( 'Unexpected mail fixture query.' );
	}
	public function get_var( $query ) {
		return false !== strpos( $query, 'mail_sender_email' ) ? $this->event['mail_sender_email'] : 'Delegate';
	}
}
require dirname( __DIR__ ) . '/includes/class-cer-mailer.php';

$http_calls = 0;
$failures = array();
$atts = array( 'to' => 'attendee@example.test', 'subject' => 'Fixture', 'message' => '<p>Thank you</p>' );
expect_mail_contract( false === cer_maybe_send_via_resend( false, $atts ) && 0 === $http_calls, 'An earlier failed short circuit must not trigger another send.' );
expect_mail_contract( true === cer_maybe_send_via_resend( true, $atts ) && 0 === $http_calls, 'An earlier accepted send must not send twice.' );
foreach ( array(
	new WP_Error( 'http_request_failed', 'Fixture timeout' ),
	array( 'response' => array( 'code' => 429 ), 'body' => '{"message":"Rate limited"}' ),
	array( 'response' => array( 'code' => 500 ), 'body' => '{"message":"Unavailable"}' ),
) as $http_response ) {
	$before = count( $failures );
	expect_mail_contract( false === cer_maybe_send_via_resend( null, $atts ), 'Transport failures must return false, not a truthy WP_Error.' );
	expect_mail_contract( count( $failures ) === $before + 1 && 'wp_mail_failed' === $failures[ $before ][0], 'Transport failures must use WordPress failure logging.' );
}
expect_mail_contract( false === cer_maybe_send_via_resend( null, array_merge( $atts, array( 'to' => 'invalid-address' ) ) ), 'Invalid recipient must fail explicitly.' );
expect_mail_contract( false === cer_maybe_send_via_resend( null, array_merge( $atts, array( 'attachments' => array( __DIR__ . '/nonexistent-fixture.pdf' ) ) ) ), 'Missing ticket attachment must fail explicitly.' );
$http_response = array( 'response' => array( 'code' => 200 ), 'body' => '{"id":"fixture-message"}' );
expect_mail_contract( true === cer_maybe_send_via_resend( null, $atts ), 'Accepted mail must preserve the successful transport contract.' );
expect_mail_contract( 'Fixture Tickets <tickets@example.test>' === $http_payload['from'] && ! isset( $http_payload['reply_to'] ), 'Without event context the configured sender must be unchanged.' );
$GLOBALS['cer_current_mail_config'] = array( 'event_id' => 7, 'display_name' => "KAMGC \"2026\" <x>\r\nBcc: evil@example.test" );
expect_mail_contract( true === cer_maybe_send_via_resend( null, array_merge( $atts, array( 'headers' => array( 'Content-Type: text/html; charset=UTF-8', 'Reply-To: organizer@example.test' ) ) ) ), 'Event mail must send.' );
expect_mail_contract( '"KAMGC 2026 x Bcc: evil@example.test" <tickets@example.test>' === $http_payload['from'], 'Event name must be a sanitised display name on the verified no-reply address: ' . $http_payload['from'] );
expect_mail_contract( array( 'organizer@example.test' ) === $http_payload['reply_to'] && ! isset( $http_payload['bcc'] ) && ! isset( $http_payload['cc'] ), 'Replies must go to the event Sender Email without copying anyone.' );
unset( $GLOBALS['cer_current_mail_config'] );
$wpdb = new CER_Mail_Contract_DB_Fixture();
expect_mail_contract( 'organizer@example.test' === cer_get_event_reply_to( 7 ), 'Attendee replies must use the event Sender Email.' );
$wpdb->event['mail_sender_email'] = 'invalid-address';
expect_mail_contract( '' === cer_get_event_reply_to( 7 ), 'An invalid event Sender Email must not become a Reply-To header.' );
foreach ( array( 0, 100 ) as $amount ) {
	$wpdb->registration['amount'] = $amount;
	expect_mail_contract( cer_send_admin_receipt_for_registration( 123 ), 'The vendor notification must be sent for both free and paid tickets.' );
	expect_mail_contract( array( 'vendor@example.test' ) === $http_payload['to'], 'The notification must go to the vendor address.' );
	expect_mail_contract( array( 'buyer@example.test' ) === $http_payload['reply_to'], 'Vendor replies must go to the decrypted attendee address.' );
	expect_mail_contract( '"Fixture Event" <tickets@example.test>' === $http_payload['from'], 'The vendor notification must use the event display name and verified sender.' );
	expect_mail_contract( ! isset( $http_payload['cc'] ) && ! isset( $http_payload['bcc'] ), 'Vendor notifications must not copy other recipients.' );
}
$wpdb->registration['mail_notification_email'] = '';
$before = $http_calls;
expect_mail_contract( ! cer_send_admin_receipt_for_registration( 123 ) && $before === $http_calls, 'No vendor notification should be sent without a Notification Email.' );
echo "Resend mail contract tests passed.\n";
