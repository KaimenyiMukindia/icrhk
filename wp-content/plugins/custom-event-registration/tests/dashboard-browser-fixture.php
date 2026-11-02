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
if ( ! preg_match( '/^cer_dashboard_test_[a-f0-9]{12}$/', $database ) || ! preg_match( '/^[a-f0-9]{48}$/', $token ) ) {
	exit( "Invalid fixture identity.\n" );
}

$_SERVER['HTTP_HOST'] = '127.0.0.1' . ( $cleanup ? '' : ':' . (int) $_SERVER['SERVER_PORT'] );
$_SERVER['SERVER_NAME'] = '127.0.0.1';
define( 'DISABLE_WP_CRON', true );
define( 'WP_USE_THEMES', false );
define( 'WP_ADMIN', true );
define( 'SCRIPT_DEBUG', true );
define( 'CONCATENATE_SCRIPTS', false );
if ( ! $cleanup && '/wp-admin/admin-ajax.php' === $path ) {
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
	if ( ! unlink( $manifest_path ) ) {
		exit( "The test database was removed, but the manifest could not be removed.\n" );
	}
	echo "Browser fixture database removed.\n";
	exit;
}

require_once $root . '/wp-admin/includes/admin.php';
add_filter( 'pre_option_cer_schema_version', static function () { return '10'; } );
add_filter( 'user_has_cap', static function ( $caps ) { $caps['manage_options'] = true; return $caps; } );
wp_set_current_user( 0 );
set_current_screen( 'cer-test' );
require dirname( __DIR__ ) . '/custom-event-registration.php';

if ( '/wp-admin/admin-ajax.php' === $path ) {
	$action = isset( $_POST['action'] ) ? sanitize_key( wp_unslash( $_POST['action'] ) ) : '';
	if ( 'cer_dashboard_event_metrics' !== $action ) {
		wp_send_json_error( array( 'message' => 'Unsupported fixture action.' ), 400 );
	}
	do_action( 'wp_ajax_' . $action );
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
