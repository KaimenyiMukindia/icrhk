<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CER_KAMGC_CAMPAIGN_OPTION = 'cer_kamgc_reregistration_campaign_v1';

add_action( 'init', 'cer_schedule_kamgc_reregistration_campaign' );
add_action( 'cer_kamgc_reregistration_campaign', 'cer_process_kamgc_reregistration_campaign' );

function cer_kamgc_campaign_is_live_site(): bool {
	if ( defined( 'CER_KAMGC_REREGISTRATION_CAMPAIGN' ) ) {
		return (bool) CER_KAMGC_REREGISTRATION_CAMPAIGN;
	}
	$host = strtolower( (string) parse_url( home_url( '/' ), PHP_URL_HOST ) );
	return is_ssl() && ! in_array( $host, array( 'localhost', '127.0.0.1', '::1' ), true )
		&& ! str_ends_with( $host, '.test' ) && ! str_ends_with( $host, '.local' );
}

function cer_kamgc_campaign_subject(): string {
	return 'KAMGC 2026: Important Registration Update';
}

function cer_kamgc_campaign_body(): string {
	return "Dear Participant,\n\nThank you for registering for the Kenya Annual Multi-Sectoral GBV Conference (KAMGC) 2026.\n\nWe would like to sincerely apologize for an oversight that occurred during the online registration process. Due to a technical hitch on the registration platform, some important conference information, including the delegates' subsidized participation fee and related registration details, was inadvertently omitted from the registration form.\n\nPlease note that all delegates attending the conference will be required to pay a subsidized delegates' fee of Ksh 5,000. This contribution will help cater for meals and other conference-related hospitality costs during the event.\n\nTo ensure that all registration details are accurately captured and to facilitate proper conference planning, we kindly request all participants who had previously registered to complete the registration process again using the updated registration link below:\n\nRegistration Link: https://bit.ly/KAMGC2026\n\nWe kindly ask that you submit your updated registration at your earliest convenience.\n\nWe regret any inconvenience this may have caused and appreciate your understanding and continued interest in participating in the conference. Your support and cooperation will help us ensure a smooth and successful event for all delegates.\n\nShould you have any questions or require clarification regarding the registration process or payment arrangements, please do not hesitate to contact us.\n\nWe look forward to welcoming you to the conference.\n\nKind regards,";
}

function cer_kamgc_campaign_state(): array {
	$state = get_option( CER_KAMGC_CAMPAIGN_OPTION, array() );
	$defaults = array(
		'phase'       => 'test',
		'test_sent'   => false,
		'sent'        => array(),
		'last_error'  => '',
		'completed'   => false,
		'completed_at'=> '',
	);
	return is_array( $state ) ? wp_parse_args( $state, $defaults ) : $defaults;
}

function cer_schedule_kamgc_reregistration_campaign(): void {
	if ( ! cer_kamgc_campaign_is_live_site() ) {
		return;
	}
	$state = cer_kamgc_campaign_state();
	if ( ! empty( $state['completed'] ) || wp_next_scheduled( 'cer_kamgc_reregistration_campaign' ) ) {
		return;
	}
	if ( ! wp_schedule_single_event( time() + 60, 'cer_kamgc_reregistration_campaign' ) ) {
		error_log( 'CER KAMGC campaign could not be scheduled.' );
	}
}

function cer_kamgc_campaign_recipients(): array {
	global $wpdb;
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT r.id, r.email, e.id AS event_id, e.name AS event_name
			FROM {$wpdb->prefix}evt_registrations r
			INNER JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id
			WHERE e.slug = %s AND r.deleted_at IS NULL
			ORDER BY r.id ASC",
			'kamgc-2026'
		),
		ARRAY_A
	);
	if ( $wpdb->last_error ) {
		error_log( 'CER KAMGC campaign recipient query failed: ' . $wpdb->last_error );
		return array();
	}
	$recipients = array();
	foreach ( $rows as $row ) {
		$email = strtolower( trim( (string) cer_decrypt_pii( $row['email'] ?? '' ) ) );
		if ( ! is_email( $email ) || isset( $recipients[ $email ] ) ) {
			continue;
		}
		$recipients[ $email ] = array(
			'email'      => $email,
			'event_id'   => (int) ( $row['event_id'] ?? 0 ),
			'event_name' => (string) ( $row['event_name'] ?? 'KAMGC 2026' ),
		);
	}
	return array_values( $recipients );
}

function cer_send_kamgc_campaign_message( string $recipient, int $event_id, string $event_name, bool $test = false ): bool {
	$subject = ( $test ? '[Test] ' : '' ) . cer_kamgc_campaign_subject();
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	$reply_to = cer_get_event_reply_to( $event_id );
	if ( '' !== $reply_to ) {
		$headers[] = 'Reply-To: ' . $reply_to;
	}
	cer_set_current_mail_config( $event_id, $event_name );
	try {
		return (bool) wp_mail( $recipient, $subject, cer_kamgc_campaign_body(), $headers );
	} finally {
		cer_clear_current_mail_config();
	}
}

function cer_run_kamgc_campaign_after_deploy(): array {
	if ( ! cer_kamgc_campaign_is_live_site() ) {
		return array( 'success' => false, 'message' => 'Live campaign execution is disabled.', 'state' => cer_kamgc_campaign_state() );
	}
	$recipient_count = max( 1, count( cer_kamgc_campaign_recipients() ) );
	$max_runs = (int) ceil( $recipient_count / 10 ) + 3;
	for ( $run = 0; $run < $max_runs; $run++ ) {
		cer_process_kamgc_reregistration_campaign();
		$state = cer_kamgc_campaign_state();
		if ( ! empty( $state['completed'] ) ) {
			return array( 'success' => true, 'message' => 'KAMGC campaign completed.', 'state' => $state );
		}
		if ( 'test' === $state['phase'] && ! empty( $state['last_error'] ) ) {
			return array( 'success' => false, 'message' => $state['last_error'], 'state' => $state );
		}
		usleep( 250000 );
	}
	return array( 'success' => false, 'message' => 'Campaign remains scheduled for the next WordPress cron pass.', 'state' => cer_kamgc_campaign_state() );
}

function cer_process_kamgc_reregistration_campaign(): void {
	if ( ! cer_kamgc_campaign_is_live_site() ) {
		return;
	}
	global $wpdb;
	$lock = 'cer_kamgc_campaign_' . md5( $wpdb->prefix );
	if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(SHA2(CONCAT(DATABASE(), %s), 256), 0)', $lock ) ) ) {
		return;
	}
	try {
		$state = cer_kamgc_campaign_state();
		if ( ! empty( $state['completed'] ) ) {
			return;
		}
		$event = $wpdb->get_row( $wpdb->prepare(
			"SELECT id, name FROM {$wpdb->prefix}evt_events WHERE slug = %s AND deleted_at IS NULL LIMIT 1",
			'kamgc-2026'
		), ARRAY_A );
		if ( empty( $event ) ) {
			$state['last_error'] = 'KAMGC event not found.';
			update_option( CER_KAMGC_CAMPAIGN_OPTION, $state, false );
			return;
		}
		if ( 'test' === $state['phase'] ) {
			$state['last_error'] = '';
			if ( cer_send_kamgc_campaign_message( 'mukindiakaimenyi@gmail.com', (int) $event['id'], (string) $event['name'], true ) ) {
				$state['phase'] = 'live';
				$state['test_sent'] = true;
				update_option( CER_KAMGC_CAMPAIGN_OPTION, $state, false );
				wp_schedule_single_event( time() + 300, 'cer_kamgc_reregistration_campaign' );
				return;
			}
			$state['last_error'] = 'Test message failed; live send remains blocked.';
			update_option( CER_KAMGC_CAMPAIGN_OPTION, $state, false );
			wp_schedule_single_event( time() + 900, 'cer_kamgc_reregistration_campaign' );
			return;
		}
		$sent = array_fill_keys( (array) $state['sent'], true );
		$recipients = cer_kamgc_campaign_recipients();
		$processed = 0;
		foreach ( $recipients as $recipient ) {
			$email_hash = hash( 'sha256', strtolower( $recipient['email'] ) );
			if ( isset( $sent[ $email_hash ] ) ) {
				continue;
			}
			if ( ! cer_send_kamgc_campaign_message( $recipient['email'], $recipient['event_id'], $recipient['event_name'] ) ) {
				$state['last_error'] = 'One or more campaign messages failed and will retry.';
				continue;
			}
			$state['sent'][] = $email_hash;
			$sent[ $email_hash ] = true;
			$state['last_error'] = '';
			update_option( CER_KAMGC_CAMPAIGN_OPTION, $state, false );
			if ( ++$processed >= 10 ) {
				break;
			}
		}
		if ( count( $state['sent'] ) >= count( $recipients ) ) {
			$state['completed'] = true;
			$state['completed_at'] = current_time( 'mysql' );
			update_option( CER_KAMGC_CAMPAIGN_OPTION, $state, false );
			return;
		}
		wp_schedule_single_event( time() + 60, 'cer_kamgc_reregistration_campaign' );
	} finally {
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(SHA2(CONCAT(DATABASE(), %s), 256))', $lock ) );
	}
}
