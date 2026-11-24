<?php

$root = dirname( __DIR__, 4 );
$config = file_get_contents( $root . '/wp-config.php' );
if ( ! preg_match( "/define\\s*\\(\\s*['\"]DB_HOST['\"]\\s*,\\s*['\"](localhost|127\\.0\\.0\\.1)(?::[0-9]+)?['\"]/i", $config ) ) {
	exit( "The browser fixture requires a loopback WordPress database.\n" );
}

$cleanup = PHP_SAPI === 'cli' && isset( $argv[1], $argv[2] ) && 'cleanup' === $argv[1];
if ( ! $cleanup && PHP_SAPI !== 'cli-server' ) {
	exit( "This fixture must run on the local PHP development server.\n" );
}
if ( $cleanup ) {
	$manifest_path = $argv[2];
	$manifest = json_decode( file_get_contents( $manifest_path ), true );
	$database = $manifest['database'] ?? '';
	$token = $manifest['token'] ?? '';
} else {
	if ( ! in_array( $_SERVER['REMOTE_ADDR'] ?? '', array( '127.0.0.1', '::1' ), true ) ) {
		http_response_code( 403 );
		exit( 'Local requests only.' );
	}
	$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
	if ( preg_match( '#^/(?:wp-includes/(?:js|css)/|wp-admin/(?:js|css)/|wp-content/plugins/custom-event-registration/assets/).+\\.(?:js|css|png|gif|svg|woff2?)$#', $path ) ) {
		return false;
	}
	$database = getenv( 'CER_TEST_DB' );
	$token = getenv( 'CER_TEST_TOKEN' );
}
if ( ! preg_match( '/^cer_(?:dashboard|composer)_test_[a-f0-9]{12}$/', $database ) || ! preg_match( '/^[a-f0-9]{48}$/', $token ) ) {
	exit( "Invalid fixture identity.\n" );
}

$_SERVER['HTTP_HOST'] = '127.0.0.1' . ( $cleanup ? '' : ':' . (int) $_SERVER['SERVER_PORT'] );
$_SERVER['SERVER_NAME'] = '127.0.0.1';
define( 'DISABLE_WP_CRON', true );
define( 'WP_USE_THEMES', false );
define( 'WP_ADMIN', true );
define( 'SCRIPT_DEBUG', true );
define( 'CONCATENATE_SCRIPTS', false );
if ( ! $cleanup && in_array( $path, array( '/wp-admin/admin-ajax.php', '/fixture-verify' ), true ) ) {
	define( 'DOING_AJAX', true );
}
$site_url = 'http://' . $_SERVER['HTTP_HOST'];
$GLOBALS['wp_filter']['pre_option_active_plugins'][10][] = array( 'function' => static function () { return array(); }, 'accepted_args' => 0 );
$GLOBALS['wp_filter']['pre_option_template'][10][] = array( 'function' => static function () { return 'cer-test-disabled-theme'; }, 'accepted_args' => 0 );
$GLOBALS['wp_filter']['pre_option_stylesheet'][10][] = array( 'function' => static function () { return 'cer-test-disabled-theme'; }, 'accepted_args' => 0 );
$GLOBALS['wp_filter']['pre_option_siteurl'][10][] = array( 'function' => static function () use ( $site_url ) { return $site_url; }, 'accepted_args' => 0 );
$GLOBALS['wp_filter']['pre_option_home'][10][] = array( 'function' => static function () use ( $site_url ) { return $site_url; }, 'accepted_args' => 0 );
require $root . '/wp-load.php';
$wpdb->select( $database );
$stored_token = $wpdb->get_var( 'SELECT token FROM cer_fixture_marker LIMIT 1' );
if ( ! is_string( $stored_token ) || ! hash_equals( $stored_token, $token ) ) {
	exit( "The fixture marker did not match.\n" );
}
if ( $cleanup ) {
	$wpdb->select( DB_NAME );
	if ( false === $wpdb->query( "DROP DATABASE `{$database}`" ) ) {
		exit( "Failed to remove the test database.\n" );
	}
	if ( isset( $manifest['directory'] ) ) {
		$directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $database;
		if ( $manifest['directory'] !== $directory ) {
			exit( 'Unexpected fixture upload directory; inspect manually.' );
		}
		foreach ( glob( $directory . DIRECTORY_SEPARATOR . 'tickets' . DIRECTORY_SEPARATOR . 'ticket-*.pdf' ) ?: array() as $pdf ) {
			unlink( $pdf );
		}
		if ( is_dir( $directory . DIRECTORY_SEPARATOR . 'tickets' ) ) { rmdir( $directory . DIRECTORY_SEPARATOR . 'tickets' ); }
		if ( is_dir( $directory ) ) { rmdir( $directory ); }
	}
	if ( ! unlink( $manifest_path ) ) {
		exit( "The test database was removed, but the manifest could not be removed.\n" );
	}
	echo "Browser fixture database removed.\n";
	exit;
}

require_once $root . '/wp-admin/includes/admin.php';
add_filter( 'pre_option_cer_schema_version', static function () { return '10'; } );
add_filter( 'user_has_cap', static function ( $caps ) { $caps['manage_options'] = true; return $caps; } );
add_filter( 'upload_dir', static function ( $uploads ) use ( $database ) {
	$uploads['basedir'] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $database;
	$uploads['path'] = $uploads['basedir'];
	$uploads['error'] = false;
	return $uploads;
} );
wp_set_current_user( 0 );
set_current_screen( 'cer-test' );
require dirname( __DIR__ ) . '/custom-event-registration.php';
remove_filter( 'pre_wp_mail', 'cer_maybe_send_via_resend', 10 );
add_filter( 'pre_wp_mail', static function ( $pre, $atts ) {
	$log = get_option( 'cer_browser_mail_log', array() );
	$log[] = $atts;
	update_option( 'cer_browser_mail_log', $log, false );
	return true;
}, 1, 2 );
add_filter( 'pre_http_request', static function ( $pre, $args, $url ) {
	if ( false !== strpos( $url, '/api/payment/initiate' ) ) {
		$data = json_decode( $args['body'], true );
		return array( 'response' => array( 'code' => 200 ), 'body' => wp_json_encode( array( 'status' => 'pending', 'reference' => $data['payment_uuid'], 'access_code' => 'fixture', 'authorization_url' => 'https://example.invalid/fixture' ) ), 'headers' => array(), 'cookies' => array() );
	}
	throw new RuntimeException( 'Browser tests must not make external HTTP requests.' );
}, 1, 3 );

if ( '/fixture-verify' === $path ) {
	$data = json_decode( file_get_contents( 'php://input' ), true );
	$id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}evt_registrations WHERE payment_uuid = %s", $data['reference'] ?? '' ) );
	$confirmed = cer_confirm_registration_paid( $id );
	$sent = ! empty( $confirmed['ok'] );
	if ( $sent ) {
		foreach ( cer_checkout_registrations( $id ) as $line ) {
			if ( ! cer_send_ticket_for_registration( (int) $line['id'] ) ) {
				$sent = false;
			}
		}
	}
	if ( ! $sent ) {
		wp_send_json_error( array( 'message' => 'Synthetic payment could not complete.' ), 500 );
	}
	wp_send_json( array( 'status' => 'success', 'registration_status' => 'paid' ) );
}

if ( '/fixture-mail' === $path ) {
	$event_id = absint( $_GET['event_id'] ?? 0 );
	$names = $wpdb->get_col( $wpdb->prepare( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s", $wpdb->esc_like( cer_event_mail_job_key( $event_id ) . '_' ) . '%' ) );
	foreach ( $names as $name ) {
		cer_process_event_mail_batch( $event_id, substr( $name, strlen( cer_event_mail_job_key( $event_id ) . '_' ) ) );
	}
	$jobs = array();
	foreach ( $names as $name ) {
		$job = get_option( $name );
		$jobs[] = array( 'subject' => $job['subject'], 'delivery' => $job['delivery'], 'states' => array_count_values( $job['recipients'] ) );
	}
	wp_send_json( array( 'jobs' => $jobs, 'mail' => get_option( 'cer_browser_mail_log', array() ) ) );
}

if ( '/wp-admin/admin-post.php' === $path ) {
	$action = sanitize_key( $_POST['action'] ?? '' );
	if ( ! in_array( $action, array( 'cer_allocate_group', 'cer_assign_ticket', 'cer_queue_event_mail' ), true ) ) {
		wp_die( 'Unsupported fixture action.', '', array( 'response' => 400 ) );
	}
	do_action( 'admin_post_' . $action );
	exit;
}
if ( isset( $_GET['cer_ticket'] ) || isset( $_GET['cer_validate_ticket'] ) ) {
	cer_handle_ticket_request();
}

if ( '/wp-admin/admin-ajax.php' === $path ) {
	$action = sanitize_key( wp_unslash( $_POST['action'] ?? $_GET['action'] ?? '' ) );
	if ( ! in_array( $action, array( 'cer_dashboard_event_metrics', 'cer_ticket_availability', 'cer_admin_assignment_inventory', 'cer_submit_registration' ), true ) ) {
		wp_send_json_error( array( 'message' => 'Unsupported fixture action.' ), 400 );
	}
	do_action( 'wp_ajax_' . $action );
	exit;
}
if ( '/event' === $path ) {
	$_GET['event_id'] = absint( $_GET['event_id'] ?? 0 );
	require_once dirname( __DIR__ ) . '/includes/event-page-renderer.php';
	wp_enqueue_style( 'cer-browser-event', CER_PLUGIN_URL . 'assets/css/cer-event.css' );
	wp_enqueue_script( 'cer-browser-registration', CER_PLUGIN_URL . 'assets/js/cer-registration.js', array(), null, true );
	wp_localize_script( 'cer-browser-registration', 'cerRegistrationSettings', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ), 'payment_verify_url' => $site_url . '/fixture-verify', 'nonce' => wp_create_nonce( 'cer_registration_form' ), 'currency' => 'KES',
	) );
	echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
	wp_print_styles( 'cer-browser-event' );
	echo '</head><body>';
	cer_render_event_registration_page();
	wp_print_scripts( 'cer-browser-registration' );
	echo '</body></html>';
	exit;
}
if ( '/wp-admin/admin.php' !== $path ) {
	http_response_code( 404 );
	exit( 'Not found.' );
}

$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : 'cer-dashboard';
if ( ! in_array( $page, array( 'cer-dashboard', 'cer-event-new', 'cer-events', 'cer-registrations' ), true ) ) {
	http_response_code( 404 );
	exit( 'Not found.' );
}
cer_enqueue_admin_assets( '' );
if ( 'cer-registrations' === $page && isset( $_GET['cer_export'] ) ) {
	cer_handle_admin_actions();
}
echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
wp_print_styles( array( 'cer-admin-modern', 'cer-admin-reporting', 'cer-flatpickr' ) );
wp_print_scripts( 'jquery' );
echo '</head><body class="wp-admin"><main id="wpbody-content">';
if ( 'cer-dashboard' === $page ) {
	cer_render_dashboard_page();
} elseif ( 'cer-events' === $page ) {
	cer_render_events_page();
} elseif ( 'cer-registrations' === $page ) {
	cer_render_registrations_page();
} else {
	cer_render_event_form_page();
}
echo '</main>';
do_action( 'admin_print_footer_scripts' );
wp_print_scripts( 'cer-dashboard' === $page ? 'cer-admin-dashboard' : 'cer-admin-event-form' );
wp_print_scripts( array( 'cer-admin-list-controls', 'cer-admin-events' ) );
echo '</body></html>';
