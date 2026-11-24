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
$_SERVER['SERVER_NAME'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/wp-admin/admin.php?page=cer-event-new';
define( 'DISABLE_WP_CRON', true );
define( 'WP_USE_THEMES', false );
define( 'SAVEQUERIES', true );
$GLOBALS['wp_filter']['pre_option_active_plugins'][10][] = array( 'function' => static function () { return array(); }, 'accepted_args' => 0 );
$GLOBALS['wp_filter']['pre_option_template'][10][] = array( 'function' => static function () { return 'cer-test-disabled-theme'; }, 'accepted_args' => 0 );
$GLOBALS['wp_filter']['pre_option_stylesheet'][10][] = array( 'function' => static function () { return 'cer-test-disabled-theme'; }, 'accepted_args' => 0 );
require $root . '/wp-load.php';
require_once $root . '/wp-admin/includes/admin.php';
define( 'DOING_AJAX', true );

class CER_Test_Json_Exit extends RuntimeException {}
class CER_Test_Redirect extends RuntimeException {}

function cer_test_assert( $condition, string $message ): void {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

function cer_test_query( string $sql ): void {
	global $wpdb;
	if ( false === $wpdb->query( $sql ) ) {
		throw new RuntimeException( 'Test database operation failed: ' . $wpdb->last_error );
	}
}

function cer_test_insert( string $table, array $data ): int {
	global $wpdb;
	if ( false === $wpdb->insert( $table, $data ) ) {
		throw new RuntimeException( 'Fixture insertion failed: ' . $wpdb->last_error );
	}
	return (int) $wpdb->insert_id;
}

function cer_test_json_request( callable $handler, array $post ): array {
	$_GET = array();
	$_POST = $post;
	$_REQUEST = $post;
	ob_start();
	try {
		$handler();
		throw new RuntimeException( 'The AJAX handler did not return JSON.' );
	} catch ( CER_Test_Json_Exit $exception ) {
		$output = ob_get_clean();
	}
	$result = json_decode( $output, true );
	if ( ! is_array( $result ) ) {
		throw new RuntimeException( 'Invalid AJAX JSON: ' . substr( $output, 0, 1000 ) );
	}
	return $result;
}

function cer_test_render_form( int $event_id, array $state = array() ): string {
	$_GET = array_merge( $state, array( 'page' => 'cer-event-new', 'event_id' => (string) $event_id ) );
	$_POST = array();
	$_REQUEST = $_GET;
	ob_start();
	cer_render_event_form_page();
	return ob_get_clean();
}

function cer_test_list_html( string $page, array $args = array() ): string {
	$_GET = array_merge( array( 'page' => $page ), $args );
	$_POST = array();
	$_REQUEST = $_GET;
	ob_start();
	try {
		if ( 'cer-events' === $page ) {
			cer_render_events_page();
		} elseif ( 'cer-registrations' === $page ) {
			cer_render_registrations_page();
		} else {
			cer_render_event_form_page();
		}
		return ob_get_contents();
	} finally {
		ob_end_clean();
	}
}

function cer_test_list_ids( string $html, string $field ): array {
	$document = new DOMDocument();
	$previous = libxml_use_internal_errors( true );
	$document->loadHTML( $html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );
	$xpath = new DOMXPath( $document );
	$ids = array();
	foreach ( $xpath->query( '//input[@name="' . $field . '[]"]' ) as $input ) {
		$ids[] = (int) $input->getAttribute( 'value' );
	}
	return $ids;
}

function cer_test_save_form( array $post ): array {
	$_GET = array();
	$_POST = $post;
	$_REQUEST = $post;
	$redirect = null;
	$denied = false;
	$catch_redirect = static function ( $location ) { throw new CER_Test_Redirect( $location ); };
	add_filter( 'wp_redirect', $catch_redirect );
	ob_start();
	try {
		cer_render_event_form_page();
	} catch ( CER_Test_Redirect $exception ) {
		$redirect = $exception->getMessage();
	} catch ( CER_Test_Json_Exit $exception ) {
		$denied = true;
	} finally {
		$html = ob_get_clean();
		remove_filter( 'wp_redirect', $catch_redirect );
	}
	if ( null !== $redirect ) {
		parse_str( parse_url( $redirect, PHP_URL_QUERY ), $query );
		cer_test_assert( ! isset( $query['cer_item_pages'] ) && ! isset( $query['cer_item_navigate'] ), 'A save redirect retained item pagination state.' );
		$html = cer_test_render_form( (int) $query['event_id'], $query );
	}
	return array(
		'success' => null !== $redirect,
		'denied' => $denied,
		'data' => array(
			'html' => $html,
			'save_state' => false !== strpos( $html, 'data-cer-save-state="unknown"' ) ? 'unknown' : 'not_saved',
		),
	);
}

function cer_test_form_data( string $html ): array {
	$document = new DOMDocument();
	$previous = libxml_use_internal_errors( true );
	$document->loadHTML( $html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );
	$xpath = new DOMXPath( $document );
	$form = $xpath->query( '//form[@id="cer-event-form"]' )->item( 0 );
	cer_test_assert( null !== $form, 'The event form was not rendered.' );
	$pairs = array();
	foreach ( $xpath->query( './/input[@name][not(ancestor::template)] | .//textarea[@name][not(ancestor::template)] | .//select[@name][not(ancestor::template)]', $form ) as $control ) {
		$type = strtolower( $control->getAttribute( 'type' ) );
		if ( $control->hasAttribute( 'disabled' ) || in_array( $type, array( 'submit', 'button', 'reset', 'file' ), true ) ||
			( in_array( $type, array( 'checkbox', 'radio' ), true ) && ! $control->hasAttribute( 'checked' ) ) ) {
			continue;
		}
		$value = $control->getAttribute( 'value' );
		if ( 'textarea' === $control->nodeName ) {
			$value = '';
			foreach ( $control->childNodes as $child ) {
				$value .= XML_TEXT_NODE === $child->nodeType ? $child->nodeValue : $document->saveHTML( $child );
			}
		} elseif ( 'select' === $control->nodeName ) {
			$selected = $xpath->query( './/option[@selected]', $control )->item( 0 );
			if ( ! $selected ) {
				$selected = $xpath->query( './/option', $control )->item( 0 );
			}
			$value = $selected ? $selected->getAttribute( 'value' ) : '';
		}
		$pairs[] = urlencode( $control->getAttribute( 'name' ) ) . '=' . urlencode( $value );
	}
	parse_str( implode( '&', $pairs ), $data );
	return $data;
}

$test_database = 'cer_dashboard_test_' . bin2hex( random_bytes( 6 ) );
$prefix = $wpdb->prefix;
if ( ! preg_match( '/^[A-Za-z0-9_]+$/', $prefix ) || ! preg_match( '/^[A-Za-z0-9_]+$/', DB_NAME ) ) {
	exit( "The local database name or prefix cannot be used safely by this test.\n" );
}
$created = false;
$keep_fixture = false;
$wpdb->suppress_errors( true );

try {
	cer_test_query( "CREATE DATABASE `{$test_database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci" );
	$created = true;
	$tables = array(
		'options', 'users', 'usermeta', 'posts', 'postmeta',
		'evt_events', 'evt_ticket_types', 'evt_registrations', 'evt_speakers', 'evt_sponsorships',
		'evt_pillars', 'evt_objectives', 'evt_summit_structure', 'evt_partners', 'evt_faqs',
	);
	foreach ( $tables as $suffix ) {
		$table = $prefix . $suffix;
		cer_test_query( "CREATE TABLE `{$test_database}`.`{$table}` LIKE `" . DB_NAME . "`.`{$table}`" );
	}
	$wpdb->select( $test_database );
	$local_only_event_columns = array(
		'show_summit', 'summit_heading', 'summit_intro',
		'show_faqs', 'faqs_heading', 'faqs_intro',
		'location_type', 'location_value', 'location_label',
	);
	$event_columns = $wpdb->get_col( "SHOW COLUMNS FROM `{$prefix}evt_events`" );
	if ( $wpdb->last_error ) {
		throw new RuntimeException( 'Test event schema lookup failed: ' . $wpdb->last_error );
	}
	foreach ( array_intersect( $local_only_event_columns, $event_columns ) as $column ) {
		cer_test_query( "ALTER TABLE `{$prefix}evt_events` DROP COLUMN `{$column}`" );
	}
	cer_test_query( "ALTER TABLE `{$prefix}evt_faqs` MODIFY `question` VARCHAR(255) NOT NULL" );
	$foreign_keys = array(
		array( 'evt_faqs', 'event_id', 'evt_events', 'CASCADE' ),
		array( 'evt_objectives', 'event_id', 'evt_events', 'CASCADE' ),
		array( 'evt_partners', 'event_id', 'evt_events', 'CASCADE' ),
		array( 'evt_pillars', 'event_id', 'evt_events', 'CASCADE' ),
		array( 'evt_registrations', 'event_id', 'evt_events', 'SET NULL' ),
		array( 'evt_registrations', 'ticket_type_id', 'evt_ticket_types', 'SET NULL' ),
		array( 'evt_speakers', 'event_id', 'evt_events', 'CASCADE' ),
		array( 'evt_sponsorships', 'event_id', 'evt_events', 'CASCADE' ),
		array( 'evt_summit_structure', 'event_id', 'evt_events', 'CASCADE' ),
		array( 'evt_ticket_types', 'event_id', 'evt_events', 'CASCADE' ),
	);
	foreach ( $foreign_keys as $index => $key ) {
		cer_test_query( "ALTER TABLE `{$prefix}{$key[0]}` ADD CONSTRAINT `cer_test_fk_{$index}` FOREIGN KEY (`{$key[1]}`) REFERENCES `{$prefix}{$key[2]}` (`id`) ON UPDATE RESTRICT ON DELETE {$key[3]}" );
	}

	add_filter( 'pre_option_cer_schema_version', static function () { return '10'; } );
	add_filter( 'pre_option_siteurl', static function () { return 'http://127.0.0.1'; } );
	add_filter( 'pre_option_home', static function () { return 'http://127.0.0.1'; } );
	$cache_version = 1;
	add_filter( 'pre_option_cer_event_cache_version', static function () use ( &$cache_version ) { return $cache_version; } );
	add_filter( 'pre_update_option_cer_event_cache_version', static function ( $new, $old ) use ( &$cache_version ) { $cache_version = $new; return $old; }, 10, 2 );
	$allowed = true;
	add_filter( 'user_has_cap', static function ( $caps ) use ( &$allowed ) { $caps['manage_options'] = $allowed; return $caps; } );
	add_filter( 'wp_die_ajax_handler', static function () {
		return static function () { throw new CER_Test_Json_Exit(); };
	} );
	wp_set_current_user( 0 );
	set_current_screen( 'cer-test' );
	require dirname( __DIR__ ) . '/custom-event-registration.php';

	cer_ensure_event_content_schema();
	cer_test_query( "ALTER TABLE `{$prefix}evt_events` DROP COLUMN `show_page_content`" );
	update_option( 'cer_event_content_schema', '1' );
	cer_ensure_event_content_schema();
	cer_test_assert( '2' === get_option( 'cer_event_content_schema' ), 'Content schema migration must complete on an existing event table.' );
	cer_test_assert( cer_column_exists( $prefix . 'evt_events', 'show_page_content' ), 'Existing version-one content storage must gain the visibility column.' );
	$wpdb->queries = array();
	cer_maybe_ensure_event_schema();
	$schema_checks = array_filter( $wpdb->queries, static function ( $query ) {
		return 0 === strpos( $query[0], 'SHOW COLUMNS' );
	} );
	cer_test_assert( 1 === count( $schema_checks ), 'Healthy branding schema should require only one check.' );
	cer_test_query( "ALTER TABLE `{$prefix}evt_events` DROP COLUMN `brand_color_2`" );
	cer_maybe_ensure_event_schema();
	cer_test_assert( cer_column_exists( $prefix . 'evt_events', 'brand_color_2' ), 'Schema drift recovery did not restore a missing branding column.' );

	$event_a = cer_test_insert( $prefix . 'evt_events', array(
		'event_uuid' => 'cer-test-event-a', 'name' => 'Event Alpha', 'slug' => 'cer-test-alpha',
		'event_date' => '2030-01-02 10:00:00', 'status' => 'published',
		'hero_convened_by' => 'Legacy convenor', 'event_info_heading' => 'Legacy information',
		'event_info_paragraph_1' => '<p>Legacy body</p>', 'target_audience' => 'Legacy audience',
		'summary_note' => 'Legacy summary intro must not be displayed',
	) );
	$event_b = cer_test_insert( $prefix . 'evt_events', array(
		'event_uuid' => 'cer-test-event-b', 'name' => 'Event Beta', 'slug' => 'cer-test-beta',
		'event_date' => '2030-02-02 10:00:00', 'status' => 'draft',
	) );
	$ticket_ids = array();
	for ( $i = 0; $i < 7; $i++ ) {
		$ticket_ids[] = cer_test_insert( $prefix . 'evt_ticket_types', array(
			'event_id' => $event_a, 'name' => 'Ticket ' . ( $i + 1 ), 'price' => 100,
			'currency' => 'KES', 'quantity_available' => 10, 'quantity_sold' => 0,
			'description' => '', 'order_index' => $i,
		) );
	}
	$ticket_b = cer_test_insert( $prefix . 'evt_ticket_types', array(
		'event_id' => $event_b, 'name' => 'Beta ticket', 'price' => 50,
		'currency' => 'KES', 'quantity_available' => 22, 'quantity_sold' => 2,
		'description' => '', 'order_index' => 0,
	) );
	$speaker_id = cer_test_insert( $prefix . 'evt_speakers', array(
		'event_id' => $event_a, 'name' => 'Speaker A', 'role' => '', 'bio' => '',
		'photo_id' => 0, 'order_index' => 0,
	) );
	$faq_id = cer_test_insert( $prefix . 'evt_faqs', array(
		'event_id' => $event_a, 'question' => 'Old question', 'answer' => 'Answer', 'order_index' => 0,
	) );
	cer_test_insert( $prefix . 'evt_sponsorships', array(
		'event_id' => $event_a, 'name' => 'Sponsor', 'description' => '', 'benefits' => '',
		'cta_url' => '', 'order_index' => 0,
	) );
	$statuses = array(
		array( 'paid', '1000.00', null ),
		array( 'pending', '20.00', null ),
		array( 'confirmed', '2000.00', null ),
		array( 'paid', '300.00', '2029-01-01 00:00:00' ),
	);
	foreach ( $statuses as $index => $status ) {
		cer_test_insert( $prefix . 'evt_registrations', array(
			'registration_uuid' => 'cer-test-registration-' . $index,
			'payment_uuid' => 'cer-test-payment-' . $index,
			'user_access_key' => 'cer-test-access-' . $index,
			'event_id' => $event_a, 'ticket_type_id' => $ticket_ids[0],
			'full_name' => 'Synthetic User', 'email' => 'test@example.invalid',
			'phone' => '000000', 'payment_method' => 'test', 'status' => $status[0],
			'amount' => $status[1], 'deleted_at' => $status[2],
		) );
	}

	$metrics = cer_get_dashboard_event_metrics( $event_a );
	cer_test_assert( ! is_wp_error( $metrics ), 'Metrics lookup failed.' );
	cer_test_assert( 3 === $metrics['metrics']['total_registrations'], 'Active registration count changed.' );
	cer_test_assert( 3 === $metrics['metrics']['tickets_sold'], 'Sold ticket count changed.' );
	cer_test_assert( 70 === $metrics['metrics']['tickets_available'], 'Ticket inventory is incorrect.' );
	cer_test_assert( 3300.0 === $metrics['metrics']['revenue_collected'], 'Revenue is incorrect.' );
	cer_test_assert( 1 === $metrics['metrics']['sponsorship_packages'], 'Sponsorship count is incorrect.' );
	cer_test_assert( is_wp_error( cer_get_dashboard_event_metrics( 999999 ) ), 'Missing events must be rejected.' );
	$fail_sponsorship_query = static function ( $query ) use ( $prefix ) {
		return false !== strpos( $query, "SELECT COUNT(*) FROM {$prefix}evt_sponsorships" )
			? "SELECT COUNT(*) FROM {$prefix}cer_missing_test_table"
			: $query;
	};
	add_filter( 'query', $fail_sponsorship_query );
	$failed_metrics = cer_get_dashboard_event_metrics( $event_a );
	remove_filter( 'query', $fail_sponsorship_query );
	cer_test_assert( is_wp_error( $failed_metrics ), 'A failed metrics query must not report zero sponsorships.' );

	$fail_event_totals = static function ( $query ) use ( $prefix ) {
		return false !== strpos( $query, 'SELECT COUNT(*) AS total_events' )
			? "SELECT COUNT(*) FROM {$prefix}cer_missing_test_table"
			: $query;
	};
	add_filter( 'query', $fail_event_totals );
	$dashboard_failure_reported = false;
	ob_start();
	try {
		cer_render_dashboard_page();
	} catch ( CER_Test_Json_Exit $exception ) {
		$dashboard_failure_reported = true;
	} finally {
		ob_end_clean();
		remove_filter( 'query', $fail_event_totals );
	}
	cer_test_assert( $dashboard_failure_reported, 'A failed event totals query must not render an empty dashboard.' );

	$wpdb->update( $prefix . 'evt_ticket_types', array( 'quantity_available' => 30 ), array( 'id' => $ticket_b ) );
	cer_test_assert( 28 === cer_get_dashboard_event_metrics( $event_b )['metrics']['tickets_available'], 'Metrics did not reflect a fresh database change.' );

	$dashboard_nonce = wp_create_nonce( 'cer_dashboard_metrics' );
	$dashboard_post = array( 'event_id' => (string) $event_b, 'nonce' => $dashboard_nonce );
	$allowed = false;
	cer_test_assert( false === cer_test_json_request( 'cer_ajax_dashboard_event_metrics', $dashboard_post )['success'], 'Dashboard capability check failed.' );
	$allowed = true;
	$invalid_nonce = $dashboard_post;
	$invalid_nonce['nonce'] = 'invalid';
	cer_test_assert( false === cer_test_json_request( 'cer_ajax_dashboard_event_metrics', $invalid_nonce )['success'], 'Dashboard nonce check failed.' );
	$dashboard_json = cer_test_json_request( 'cer_ajax_dashboard_event_metrics', $dashboard_post );
	cer_test_assert( true === $dashboard_json['success'] && '28' === $dashboard_json['data']['metrics']['tickets_available'], 'Dashboard AJAX did not return fresh metrics.' );

	$html = cer_test_render_form( $event_a );
	cer_test_assert( false === strpos( $html, 'cer_item_navigate' ) && false === strpos( $html, 'cer_item_pages' ), 'The event form must not render item pagination.' );
	$post = cer_test_form_data( $html );
	cer_test_assert( 7 === count( $post['tickets'] ) && 'Ticket 7' === $post['tickets'][6]['name'], 'All items beyond the former five-item limit must load together.' );
	$post['event_title'] = 'Event Alpha Updated';
	$post['event_description'] = '<p>Saved content</p>';
	$post['tickets'][0]['name'] = 'Updated ticket';
	$post['speakers'][0]['name'] = 'Updated speaker';
	$post['speakers'][0]['photo_id'] = '123';
	$post['objectives'][0]['title'] = 'New objective';
	$post['objectives'][0]['description'] = 'Objective description';
	$post['hero_details'] = array(
		array( 'label' => 'Convened by', 'value' => 'Fixture convenor' ),
		array( 'label' => 'Hosted by', 'value' => 'Fixture host' ),
	);
	$post['page_content'] = array(
		array( 'header' => 'Travel information', 'subtitle' => 'Getting here', 'body' => "<p>Bring your ticket.</p><ul><li>First instruction</li></ul>" ),
		array( 'header' => 'Access information', 'subtitle' => 'On arrival', 'body' => '<p><a href="https://example.test/guide">Read the guide</a></p>' ),
	);
	foreach ( cer_event_content_sections() as $section => $defaults ) {
		$post[ $section . '_heading' ] = 'Fixture ' . $section;
		$post[ $section . '_eyebrow' ] = 'Eyebrow ' . $section;
		$post[ 'summary' === $section ? 'summary_note' : $section . '_intro' ] = '<p>Intro ' . $section . '</p>';
	}
	$post['hero_summary_heading'] = 'Fixture hero summary';
	$post['cer_item_active_tabs']['section_content'] = '2';
	$post['cer_item_active_tabs']['section_setup'] = '1';
	$post['deleted_faqs'] = array( $faq_id );
	$version_before = $cache_version;
	$result = cer_test_save_form( $post );
	cer_test_assert( true === $result['success'], 'Event save failed.' );
	cer_test_assert( 'Event Alpha Updated' === $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$prefix}evt_events WHERE id = %d", $event_a ) ), 'Event fields were not saved.' );
	cer_test_assert( null === $wpdb->get_var( $wpdb->prepare( "SELECT event_end_date FROM {$prefix}evt_events WHERE id = %d", $event_a ) ), 'An empty optional end date must remain NULL.' );
	cer_test_assert( '' === cer_format_datetime_for_picker( '0000-00-00 00:00:00' ), 'A legacy zero date must not become a spurious calendar date.' );
	cer_test_assert( 'Updated ticket' === $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$prefix}evt_ticket_types WHERE id = %d", $ticket_ids[0] ) ), 'Ticket edits were not saved.' );
	$speaker = $wpdb->get_row( $wpdb->prepare( "SELECT name, photo_id FROM {$prefix}evt_speakers WHERE id = %d", $speaker_id ), ARRAY_A );
	cer_test_assert( 'Updated speaker' === $speaker['name'] && 123 === (int) $speaker['photo_id'], 'Speaker edits or media ID were not saved.' );
	cer_test_assert( 'New objective' === $wpdb->get_var( $wpdb->prepare( "SELECT title FROM {$prefix}evt_objectives WHERE event_id = %d", $event_a ) ), 'New rows were not saved.' );
	cer_test_assert( null === $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$prefix}evt_faqs WHERE id = %d", $faq_id ) ), 'Pending deletions were not saved.' );
	cer_test_assert( $cache_version > $version_before, 'Event cache was not invalidated.' );
	$saved_form = cer_test_form_data( $result['data']['html'] );
	cer_test_assert( '2' === $saved_form['cer_item_active_tabs']['section_content'] && '1' === $saved_form['cer_item_active_tabs']['section_setup'], 'Section tabs must retain their selection across the save redirect and reload.' );
	cer_test_assert( 7 === count( $saved_form['tickets'] ) && 'Ticket 7' === $saved_form['tickets'][6]['name'], 'Saving must preserve all items.' );
	cer_test_assert( ! empty( $saved_form['objectives'][0]['id'] ), 'New item IDs were not returned after saving.' );
	$saved_event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_events WHERE id = %d", $event_a ) );
	cer_test_assert( $post['hero_details'] === cer_event_content_rows( $saved_event, 'hero_details' ) && $post['page_content'] === cer_event_content_rows( $saved_event, 'page_content' ), 'Hero and page content rows must survive a real save and reload in order.' );
	cer_test_assert( 'Legacy convenor' === $saved_event->hero_convened_by && 'Legacy information' === $saved_event->event_info_heading && '<p>Legacy body</p>' === $saved_event->event_info_paragraph_1 && 'Legacy audience' === $saved_event->target_audience, 'Removing obsolete controls must preserve their stored copy.' );
	cer_test_assert( false === strpos( $result['data']['html'], 'name="event_info_heading"' ) && false === strpos( $result['data']['html'], 'name="show_event_information"' ), 'The redundant Event Information controls must be removed.' );
	foreach ( cer_event_content_sections() as $section => $defaults ) {
		cer_test_assert( $post[ $section . '_heading' ] === $saved_event->{$section . '_heading'} && $post[ $section . '_eyebrow' ] === $saved_event->{$section . '_eyebrow'}, 'Section copy did not save: ' . $section );
		cer_test_assert( 1 === substr_count( $result['data']['html'], 'name="' . $section . '_heading"' ), 'A section heading must have exactly one editing surface.' );
	}

	$unchanged = $saved_form;
	$event_before_unchanged = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_events WHERE id = %d", $event_a ), ARRAY_A );
	$wpdb->queries = array();
	$version_before = $cache_version;
	$unchanged_result = cer_test_save_form( $unchanged );
	cer_test_assert( true === $unchanged_result['success'], 'Unchanged save failed.' );
	$item_writes = array_filter( $wpdb->queries, static function ( $query ) use ( $prefix ) {
		return (bool) preg_match( '/^\\s*(?:UPDATE|INSERT\\s+INTO|DELETE\\s+FROM)\\s+`?' . preg_quote( $prefix . 'evt_', '/' ) . '/i', $query[0] );
	} );
	$item_lookups = array_filter( $wpdb->queries, static function ( $query ) use ( $prefix ) {
		$table = preg_quote( $prefix . 'evt_', '/' );
		return (bool) preg_match( '/^\\s*SELECT\\s+id\\s+FROM\\s+`?' . $table . '/i', $query[0] )
			|| (bool) preg_match( '/^\\s*SELECT\\s+\\*\\s+FROM\\s+`?' . $table . '[a-z0-9_]+`?\\s+WHERE\\s+id\\s*=\\s*[0-9]+\\s+AND\\s+event_id\\s*=/i', $query[0] );
	} );
	$query_summary = array_map( static function ( $query ) {
		preg_match( '/^\\s*(UPDATE|INSERT\\s+INTO|DELETE\\s+FROM|SELECT\\s+(?:id|\\*)\\s+FROM)\\s+`?([A-Za-z0-9_]+)/i', $query[0], $match );
		return ( $match[1] ?? 'query' ) . ' ' . ( $match[2] ?? '' );
	}, array_merge( $item_writes, $item_lookups ) );
	$event_after_unchanged = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_events WHERE id = %d", $event_a ), ARRAY_A );
	$changed_event_fields = array_keys( array_diff_assoc( $event_before_unchanged, $event_after_unchanged ) );
	cer_test_assert( 0 === count( $item_writes ) && 0 === count( $item_lookups ), 'Unchanged save performed redundant item writes or existence lookups: ' . implode( ', ', $query_summary ) . '; changed event fields: ' . implode( ', ', $changed_event_fields ) );
	cer_test_assert( $version_before === $cache_version, 'Unchanged save invalidated the event cache.' );

	$content_fixture_ids = array(
		'evt_pillars' => cer_test_insert( $prefix . 'evt_pillars', array( 'event_id' => $event_a, 'title' => 'Content test pillar', 'description' => 'Pillar', 'order_index' => 0 ) ),
		'evt_summit_structure' => cer_test_insert( $prefix . 'evt_summit_structure', array( 'event_id' => $event_a, 'title' => 'Content test summit', 'description' => 'Summit', 'order_index' => 0 ) ),
		'evt_partners' => cer_test_insert( $prefix . 'evt_partners', array( 'event_id' => $event_a, 'name' => 'Content test partner', 'order_index' => 0 ) ),
		'evt_faqs' => cer_test_insert( $prefix . 'evt_faqs', array( 'event_id' => $event_a, 'question' => 'Content test question?', 'answer' => 'Answer', 'order_index' => 0 ) ),
	);
	require_once dirname( __DIR__ ) . '/includes/event-page-renderer.php';
	$_GET = array( 'event_id' => $event_a );
	ob_start();
	cer_render_event_registration_page();
	$content_markup = ob_get_clean();
	$registration_position = strpos( $content_markup, 'id="registration"' );
	$page_position = strpos( $content_markup, 'id="page-content-1"' );
	cer_test_assert( false !== $registration_position && $registration_position < $page_position && $page_position < strpos( $content_markup, 'id="page-content-2"' ), 'Page rows must render after payment and in their saved order.' );
	cer_test_assert( strpos( $content_markup, 'id="page-content-2"' ) < strpos( $content_markup, 'id="faq"' ), 'Page rows must render before FAQs.' );
	cer_test_assert( false !== strpos( $content_markup, 'Travel information' ) && false !== strpos( $content_markup, '<li>First instruction</li>' ) && false !== strpos( $content_markup, 'https://example.test/guide' ), 'Page header, subtitle and formatted body must reach the frontend.' );
	cer_test_assert( false !== strpos( $content_markup, 'Fixture convenor' ) && false !== strpos( $content_markup, 'Fixture host' ) && false !== strpos( $content_markup, 'Fixture hero summary' ), 'All hero detail rows and the summary title must render.' );
	cer_test_assert( false === strpos( $content_markup, 'cer-kamgc-copy-stack' ) && false !== strpos( $content_markup, 'data-eyebrow="Eyebrow objectives"' ), 'The removed card must stay absent and section eyebrows must use saved data.' );
	$content_document = new DOMDocument();
	$previous = libxml_use_internal_errors( true );
	$content_document->loadHTML( $content_markup, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );
	$content_xpath = new DOMXPath( $content_document );
	$admin_document = new DOMDocument();
	$previous = libxml_use_internal_errors( true );
	$admin_document->loadHTML( $result['data']['html'], LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );
	$admin_xpath = new DOMXPath( $admin_document );
	cer_test_assert( 1 === $admin_xpath->query( '//*[@data-cer-page-content]//div[@class="cer-panel-header-actions"]/button[@data-section-id="page-content" and contains(@class,"cer-section-link-btn")]' )->length, 'Page Content must use the existing Section Link control targeting the entire frontend section.' );
	cer_test_assert( 1 === $admin_xpath->query( '//*[@data-cer-page-content]//div[@class="cer-panel-header-actions"]//input[@name="show_page_content"]' )->length, 'Page Content must pair Section Link and Visible in the existing section action group.' );
	cer_test_assert( 1 === $content_xpath->query( '//div[@class="cer-kamgc-actions"]/div[contains(@class,"cer-share-bar")]' )->length, 'Existing share controls must be inside the hero CTA row.' );
	cer_test_assert( 2 === $content_xpath->query( '//section[@id="page-content"]/div[@class="cer-page-content-grid"]/article' )->length, 'Page content rows must share one grid section.' );
	cer_test_assert( 1 === $content_xpath->query( '//div[contains(@class,"cer-kamgc-summary-total")]/div[contains(@class,"cer-kamgc-security")]' )->length, 'Secure Payment must share the total row.' );
	cer_test_assert( false === strpos( $content_markup, 'Legacy summary intro' ) && false === strpos( $result['data']['html'], 'name="summary_note"' ), 'Editable summary intro must not appear in the summary, status notification or admin controls.' );
	foreach ( cer_event_content_sections() as $section => $defaults ) {
		cer_test_assert( false !== strpos( $content_markup, 'data-eyebrow="Eyebrow ' . $section . '"' ) && ( 'summary' === $section || false !== strpos( $content_markup, 'Intro ' . $section ) ) && false !== strpos( $content_markup, 'Fixture ' . $section ), 'Every visible section must render its own saved copy: ' . $section );
	}
	$hidden_content = $saved_form;
	unset( $hidden_content['show_page_content'] );
	$hidden_result = cer_test_save_form( $hidden_content );
	cer_test_assert( $hidden_result['success'], 'Hiding Page Content must save.' );
	$hidden_event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_events WHERE id = %d", $event_a ) );
	cer_test_assert( 0 === (int) $hidden_event->show_page_content && $saved_event->page_content === $hidden_event->page_content && $saved_event->summary_note === $hidden_event->summary_note, 'Hiding content must preserve its rows and the legacy summary note.' );
	$_GET = array( 'event_id' => $event_a );
	ob_start();
	cer_render_event_registration_page();
	$hidden_markup = ob_get_clean();
	cer_test_assert( false === strpos( $hidden_markup, 'id="page-content"' ) && false === strpos( $hidden_markup, 'id="page-content-1"' ) && false === strpos( $hidden_markup, 'cer-page-content-grid' ) && false !== strpos( $hidden_markup, 'id="cer-form-message"' ), 'The visibility toggle must omit the entire Page Content section and cards, without hiding system notifications.' );
	cer_test_assert( cer_test_save_form( $saved_form )['success'], 'Showing Page Content again must save.' );
	$event_after_unchanged = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_events WHERE id = %d", $event_a ), ARRAY_A );
	foreach ( $content_fixture_ids as $table => $id ) {
		cer_test_assert( false !== $wpdb->delete( $prefix . $table, array( 'id' => $id ) ), 'Temporary content render fixture cleanup failed.' );
	}
	$legacy_hero = (object) array( 'hero_convened_by' => 'Legacy convenor' );
	cer_test_assert( 'Legacy convenor' === cer_event_content_rows( $legacy_hero, 'hero_details' )[0]['value'], 'Existing Convened by copy must survive before migration.' );
	$legacy_hero->hero_details = '[]';
	cer_test_assert( array() === cer_event_content_rows( $legacy_hero, 'hero_details' ), 'Removing every hero row must not resurrect the legacy convenor.' );
	foreach ( array( 'invalid', array( 'invalid' ), array( array( 'header' => array( 'bad' ) ) ), array( array( 'header' => '', 'body' => 'Missing header' ) ) ) as $invalid_content ) {
		cer_test_assert( is_wp_error( cer_parse_event_content_rows( $invalid_content ) ), 'Malformed and incomplete page rows must be rejected.' );
	}
	$safe_rows = cer_parse_event_content_rows( array( array( 'header' => '<b>Header</b>', 'subtitle' => 'Subtitle', 'body' => '<p>Safe</p><script>alert(1)</script><a href="javascript:alert(1)">Unsafe link</a>' ) ) );
	cer_test_assert( 'Header' === $safe_rows[0]['header'] && false === strpos( $safe_rows[0]['body'], '<script>' ) && false === strpos( $safe_rows[0]['body'], 'javascript:' ), 'Page rows must retain safe formatting but reject executable HTML.' );
	foreach ( array( array( array( 'body' => 'Missing header' ) ), array( array( 'header' => str_repeat( 'x', 256 ) ) ) ) as $invalid_rows ) {
		$invalid_post = $saved_form;
		$invalid_post['page_content'] = $invalid_rows;
		$invalid_post['event_title'] = 'Must not save';
		$invalid_save = cer_test_save_form( $invalid_post );
		$unchanged_event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_events WHERE id = %d", $event_a ), ARRAY_A );
		cer_test_assert( false === $invalid_save['success'] && $event_after_unchanged === $unchanged_event, 'Invalid content must visibly reject the entire save and preserve all stored event data.' );
		cer_test_assert( false !== strpos( $invalid_save['data']['html'], 'notice-error' ), 'Invalid content must display the existing form error notice.' );
	}
	cer_test_assert( '' === cer_event_section_eyebrow( (object) array( 'faq_eyebrow' => '' ), 'faq' ), 'Explicitly clearing an eyebrow must not restore the old default.' );
	$remove_content = $saved_form;
	$remove_content['page_content'] = array();
	$remove_content['hero_details'] = array();
	$remove_content_result = cer_test_save_form( $remove_content );
	cer_test_assert( $remove_content_result['success'], 'Removing all content rows must save.' );
	$empty_content_event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}evt_events WHERE id = %d", $event_a ) );
	cer_test_assert( '[]' === $empty_content_event->page_content && '[]' === $empty_content_event->hero_details, 'Removed rows must remain removed after reload.' );
	cer_test_assert( cer_test_save_form( $saved_form )['success'], 'Restoring saved content rows must succeed.' );

	$invalid = $saved_form;
	$invalid['event_title'] = '';
	$invalid['speakers'][0]['name'] = '';
	$invalid['speakers'][0]['role'] = 'Role without a name';
	$invalid_result = cer_test_save_form( $invalid );
	cer_test_assert( false === $invalid_result['success'] && 'not_saved' === $invalid_result['data']['save_state'], 'Validation must not report a successful save.' );
	cer_test_assert( false !== strpos( $invalid_result['data']['html'], 'Please enter an event title.' ) && false !== strpos( $invalid_result['data']['html'], 'Speaker name is required.' ), 'Validation errors were not rendered.' );
	cer_test_assert( 'Event Alpha Updated' === $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$prefix}evt_events WHERE id = %d", $event_a ) ), 'Invalid input changed the event.' );

	$truncated = $saved_form;
	unset( $truncated['cer_event_form_complete'] );
	$truncated['event_title'] = 'Must not save an incomplete form';
	$truncated_result = cer_test_save_form( $truncated );
	cer_test_assert( ! $truncated_result['success'] && false !== strpos( $truncated_result['data']['html'], 'submitted form was incomplete' ), 'Truncated input must be rejected explicitly.' );
	cer_test_assert( 'Event Alpha Updated' === $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$prefix}evt_events WHERE id = %d", $event_a ) ), 'Truncated input changed the event.' );

	cer_test_query( "ALTER TABLE `{$prefix}evt_speakers` ADD CONSTRAINT `cer_test_reject_name` CHECK (`name` <> 'Reject write')" );
	$failing = $saved_form;
	$failing['event_title'] = 'Must roll back';
	$failing['tickets'][5]['name'] = 'Must also roll back';
	$failing['speakers'][0]['name'] = 'Reject write';
	$version_before = $cache_version;
	$failure = cer_test_save_form( $failing );
	cer_test_assert( false === $failure['success'] && 'not_saved' === $failure['data']['save_state'], 'Database failures must be reported.' );
	cer_test_assert( 'Event Alpha Updated' === $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$prefix}evt_events WHERE id = %d", $event_a ) ), 'Event update was not rolled back.' );
	cer_test_assert( 'Ticket 6' === $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$prefix}evt_ticket_types WHERE id = %d", $ticket_ids[5] ) ), 'Ticket update was not rolled back.' );
	cer_test_assert( $version_before === $cache_version, 'Failed saves must not invalidate the cache.' );

	$connection_checks = 0;
	$fail_initial_connection_check = static function ( $query ) use ( &$connection_checks, $prefix ) {
		if ( 'SELECT CONNECTION_ID()' === trim( $query ) && 1 === ++$connection_checks ) {
			return "SELECT id FROM {$prefix}cer_missing_test_table";
		}
		return $query;
	};
	$unconfirmed = $saved_form;
	$unconfirmed['event_title'] = 'Must not save without a verified connection';
	$version_before = $cache_version;
	add_filter( 'query', $fail_initial_connection_check );
	$unconfirmed_result = cer_test_save_form( $unconfirmed );
	remove_filter( 'query', $fail_initial_connection_check );
	cer_test_assert( false === $unconfirmed_result['success'] && 'not_saved' === $unconfirmed_result['data']['save_state'], 'A connection check failure before the transaction must not be reported as an unknown save.' );
	cer_test_assert( 'Event Alpha Updated' === $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$prefix}evt_events WHERE id = %d", $event_a ) ), 'An event was saved without a verified connection.' );
	cer_test_assert( $version_before === $cache_version, 'A failure before saving must not invalidate the cache.' );

	$started_transaction = false;
	$change_connection_at_start = static function ( $query ) use ( &$started_transaction ) {
		if ( 'START TRANSACTION' === trim( $query ) ) {
			$started_transaction = true;
		}
		if ( $started_transaction && 'SELECT CONNECTION_ID()' === trim( $query ) ) {
			return 'SELECT CONNECTION_ID() + 1';
		}
		return $query;
	};
	$unconfirmed['event_title'] = 'Must roll back after a connection change at transaction start';
	$version_before = $cache_version;
	add_filter( 'query', $change_connection_at_start );
	$unconfirmed_result = cer_test_save_form( $unconfirmed );
	remove_filter( 'query', $change_connection_at_start );
	cer_test_assert( false === $unconfirmed_result['success'] && 'unknown' === $unconfirmed_result['data']['save_state'], 'A connection change at transaction start must report an unknown save.' );
	cer_test_assert( 'Event Alpha Updated' === $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$prefix}evt_events WHERE id = %d", $event_a ) ), 'The simulated connection change at transaction start was not rolled back.' );
	cer_test_assert( $cache_version > $version_before, 'An unknown save must invalidate the event cache conservatively.' );

	$connection_checks = 0;
	$change_connection_before_commit = static function ( $query ) use ( &$connection_checks ) {
		if ( 'SELECT CONNECTION_ID()' === trim( $query ) && 2 === ++$connection_checks ) {
			return 'SELECT CONNECTION_ID() + 1';
		}
		return $query;
	};
	$unconfirmed['event_title'] = 'Must roll back after a connection change';
	$version_before = $cache_version;
	add_filter( 'query', $change_connection_before_commit );
	$unconfirmed_result = cer_test_save_form( $unconfirmed );
	remove_filter( 'query', $change_connection_before_commit );
	cer_test_assert( false === $unconfirmed_result['success'] && 'unknown' === $unconfirmed_result['data']['save_state'], 'A changed transaction connection must report an unknown save.' );
	cer_test_assert( 'Event Alpha Updated' === $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$prefix}evt_events WHERE id = %d", $event_a ) ), 'The simulated pre-commit connection change was not rolled back.' );
	cer_test_assert( $cache_version > $version_before, 'An unknown save must invalidate the event cache conservatively.' );

	$connection_checks = 0;
	$change_connection_after_commit = static function ( $query ) use ( &$connection_checks ) {
		if ( 'SELECT CONNECTION_ID()' === trim( $query ) && 3 === ++$connection_checks ) {
			return 'SELECT CONNECTION_ID() + 1';
		}
		return $query;
	};
	$unconfirmed['event_title'] = 'Committed but unconfirmed';
	$version_before = $cache_version;
	add_filter( 'query', $change_connection_after_commit );
	$unconfirmed_result = cer_test_save_form( $unconfirmed );
	remove_filter( 'query', $change_connection_after_commit );
	cer_test_assert( false === $unconfirmed_result['success'] && 'unknown' === $unconfirmed_result['data']['save_state'], 'A connection change after commit must report an unknown save.' );
	cer_test_assert( 'Committed but unconfirmed' === $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$prefix}evt_events WHERE id = %d", $event_a ) ), 'The simulated post-commit write was not retained.' );
	cer_test_assert( $cache_version > $version_before, 'An unconfirmed committed save must invalidate the event cache.' );
	cer_test_assert( false !== $wpdb->update( $prefix . 'evt_events', array( 'name' => 'Event Alpha Updated' ), array( 'id' => $event_a ) ), 'The synthetic event could not be restored.' );

	$connection_checks = 0;
	$native_post = $unconfirmed;
	unset( $native_post['action'] );
	$native_post['event_title'] = 'Preserve unconfirmed native edits';
	$_GET = array();
	$_POST = $native_post;
	$_REQUEST = $native_post;
	add_filter( 'query', $change_connection_before_commit );
	ob_start();
	try {
		cer_render_event_form_page();
		$native_html = ob_get_contents();
	} finally {
		ob_end_clean();
		remove_filter( 'query', $change_connection_before_commit );
	}
	$native_document = new DOMDocument();
	$previous = libxml_use_internal_errors( true );
	$native_document->loadHTML( $native_html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );
	$native_xpath = new DOMXPath( $native_document );
	$enabled_submits = $native_xpath->query( '//form[@id="cer-event-form"]//*[@type="submit" and not(@disabled) and (not(@form) or @form="cer-event-form")] | //*[@form="cer-event-form" and @type="submit" and not(@disabled)]' );
	cer_test_assert( 0 === $enabled_submits->length, 'An unconfirmed native save must disable all save buttons.' );
	cer_test_assert( 'Preserve unconfirmed native edits' === $native_xpath->query( '//input[@name="event_title"]' )->item( 0 )->getAttribute( 'value' ), 'Unconfirmed native edits were not preserved for copying.' );

	$allowed = false;
	$denied = cer_test_save_form( $post );
	cer_test_assert( $denied['denied'], 'Event save capability check failed.' );
	$allowed = true;
	$bad_nonce = $post;
	$bad_nonce['cer_event_nonce'] = 'invalid';
	$denied = cer_test_save_form( $bad_nonce );
	cer_test_assert( $denied['denied'], 'Event save nonce check failed.' );
	cer_test_assert( ! has_action( 'wp_ajax_cer_navigate_event_items' ), 'The obsolete item pagination endpoint must not remain registered.' );

	$delete_ticket = cer_test_form_data( $unchanged_result['data']['html'] );
	array_splice( $delete_ticket['tickets'], 1, 1 );
	$delete_ticket['deleted_tickets'] = array( $ticket_ids[1] );
	$after_deletion = cer_test_save_form( $delete_ticket );
	cer_test_assert( true === $after_deletion['success'], 'Saving after deleting an item failed.' );
	cer_test_assert( null === $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$prefix}evt_ticket_types WHERE id = %d", $ticket_ids[1] ) ), 'The deleted ticket remained.' );
	cer_test_assert( 1 === (int) $wpdb->get_var( $wpdb->prepare( "SELECT order_index FROM {$prefix}evt_ticket_types WHERE id = %d", $ticket_ids[2] ) ), 'Items were not reordered.' );
	cer_test_assert( 4 === (int) $wpdb->get_var( $wpdb->prepare( "SELECT order_index FROM {$prefix}evt_ticket_types WHERE id = %d", $ticket_ids[5] ) ), 'Items beyond the former page boundary were not reordered.' );
	cer_test_assert( 60 === (int) $wpdb->get_var( $wpdb->prepare( "SELECT max_attendees FROM {$prefix}evt_events WHERE id = %d", $event_a ) ), 'Capacity was not recalculated after deletion.' );
	$after_deletion_form = cer_test_form_data( $after_deletion['data']['html'] );
	cer_test_assert( 6 === count( $after_deletion_form['tickets'] ) && 'Ticket 7' === $after_deletion_form['tickets'][5]['name'], 'All remaining items must be rendered after deletion.' );

	$new_event = cer_test_form_data( cer_test_render_form( 0 ) );
	$new_event['event_title'] = 'Synthetic new event';
	$new_event['event_date'] = '2032-04-05 10:00';
	$new_event['tickets'][0]['name'] = 'New event ticket';
	$new_event['tickets'][0]['price'] = '25';
	$new_event['tickets'][0]['quantity_available'] = '4';
	$_GET = array();
	$_POST = $new_event;
	$_REQUEST = $new_event;
	$catch_redirect = static function ( $location ) { throw new CER_Test_Redirect( $location ); };
	add_filter( 'wp_redirect', $catch_redirect );
	$redirected = false;
	ob_start();
	try {
		cer_render_event_form_page();
	} catch ( CER_Test_Redirect $exception ) {
		$redirected = true;
	} finally {
		ob_end_clean();
		remove_filter( 'wp_redirect', $catch_redirect );
	}
	cer_test_assert( $redirected, 'A normal new-event save did not redirect.' );
	$new_event_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$prefix}evt_events WHERE name = %s", 'Synthetic new event' ) );
	cer_test_assert( $new_event_id > 0, 'The new event was not saved.' );
	cer_test_assert( 'New event ticket' === $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$prefix}evt_ticket_types WHERE event_id = %d", $new_event_id ) ), 'A new event item was not saved.' );
	cer_test_query( $wpdb->prepare( "DELETE FROM {$prefix}evt_events WHERE id = %d", $new_event_id ) );

	cer_test_query( "ALTER TABLE `{$prefix}evt_objectives` DROP FOREIGN KEY `cer_test_fk_1`" );
	cer_test_query( "ALTER TABLE `{$prefix}evt_objectives` ENGINE=MyISAM" );
	$unsupported_storage = $after_deletion_form;
	$unsupported_storage['event_title'] = 'Must not save with nontransactional storage';
	$storage_result = cer_test_save_form( $unsupported_storage );
	cer_test_assert( false === $storage_result['success'] && 'not_saved' === $storage_result['data']['save_state'], 'Nontransactional event storage must be rejected before saving.' );
	cer_test_assert( 'Event Alpha Updated' === $wpdb->get_var( $wpdb->prepare( "SELECT name FROM {$prefix}evt_events WHERE id = %d", $event_a ) ), 'The event was changed despite nontransactional storage.' );
	cer_test_query( "ALTER TABLE `{$prefix}evt_objectives` ENGINE=InnoDB" );
	cer_test_query( "ALTER TABLE `{$prefix}evt_objectives` ADD CONSTRAINT `cer_test_fk_1` FOREIGN KEY (`event_id`) REFERENCES `{$prefix}evt_events` (`id`) ON UPDATE RESTRICT ON DELETE CASCADE" );

	$repeater_tables = array(
		'tickets' => array( 'evt_ticket_types', 'name' ),
		'speakers' => array( 'evt_speakers', 'name' ),
		'sponsors' => array( 'evt_sponsorships', 'name' ),
		'pillars' => array( 'evt_pillars', 'title' ),
		'objectives' => array( 'evt_objectives', 'title' ),
		'summit' => array( 'evt_summit_structure', 'title' ),
		'partners' => array( 'evt_partners', 'name' ),
		'faqs' => array( 'evt_faqs', 'question' ),
	);
	foreach ( $repeater_tables as $type => $config ) {
		for ( $i = 1; $i <= 12; $i++ ) {
			$row = array( 'event_id' => $event_b, $config[1] => $type . ' item ' . $i, 'order_index' => $i );
			if ( 'tickets' === $type ) {
				$row['price'] = 1;
				$row['currency'] = 'KES';
				$row['quantity_available'] = 1;
			}
			cer_test_insert( $prefix . $config[0], $row );
		}
	}
	$wpdb->queries = array();
	$all_items_html = cer_test_render_form( $event_b );
	$all_items_queries = $wpdb->queries;
	$all_items_form = cer_test_form_data( $all_items_html );
	foreach ( $repeater_tables as $type => $config ) {
		$expected_count = 'tickets' === $type ? 13 : 12;
		cer_test_assert( $expected_count === count( $all_items_form[ $type ] ), 'Not all ' . $type . ' items were loaded.' );
		$last = $expected_count - 1;
		$all_items_form[ $type ][ $last ][ $config[1] ] = $type . ' last item saved';
		$selects = array_filter( $all_items_queries, static function ( $query ) use ( $prefix, $config ) {
			return 0 === strpos( $query[0], "SELECT * FROM {$prefix}{$config[0]} WHERE event_id" );
		} );
		cer_test_assert( 1 === count( $selects ), 'Expected one active-item query for ' . $type . '.' );
		foreach ( $selects as $query ) {
			cer_test_assert( ! preg_match( '/\\b(?:LIMIT|OFFSET)\\b/i', $query[0] ), 'Item loading must not retain a page limit.' );
		}
	}
	$all_items_result = cer_test_save_form( $all_items_form );
	cer_test_assert( $all_items_result['success'], 'Saving all eight repeater sections failed.' );
	$all_items_saved = cer_test_form_data( $all_items_result['data']['html'] );
	foreach ( $repeater_tables as $type => $config ) {
		$last = count( $all_items_form[ $type ] ) - 1;
		cer_test_assert( $type . ' last item saved' === $all_items_saved[ $type ][ $last ][ $config[1] ], 'An edit beyond the former page limit was lost in ' . $type . '.' );
	}

	$filter_events = array();
	for ( $i = 0; $i < 12; $i++ ) {
		$filter_events[] = cer_test_insert( $prefix . 'evt_events', array(
			'event_uuid' => 'realtime-event-' . $i, 'name' => 'Realtime event ' . $i,
			'slug' => 'realtime-event-' . $i, 'status' => 'published',
			'venue' => 'Realtime venue', 'event_date' => sprintf( '2033-05-%02d 10:00:00', $i + 1 ),
		) );
	}
	$filter_ticket = cer_test_insert( $prefix . 'evt_ticket_types', array(
		'event_id' => $filter_events[0], 'name' => 'Realtime ticket', 'price' => 100,
		'currency' => 'KES', 'quantity_available' => 50,
	) );
	$filter_registrations = array();
	for ( $i = 0; $i < 12; $i++ ) {
		$filter_registrations[] = cer_test_insert( $prefix . 'evt_registrations', cer_prepare_registration_row( array(
			'registration_uuid' => 'realtime-registration-' . $i, 'payment_uuid' => 'realtime-payment-' . $i,
			'event_id' => $filter_events[0], 'ticket_type_id' => $filter_ticket,
			'full_name' => 'Realtime User ' . $i, 'email' => 'realtime' . $i . '@example.invalid',
			'phone' => '00000' . $i, 'status' => 'paid', 'payment_method' => 'mpesa',
			'amount' => 100, 'created_at' => sprintf( '2033-05-%02d 12:00:00', $i + 1 ),
		) ) );
	}
	$event_filters = array( 's' => 'Realtime venue', 'event_status' => 'published', 'event_month' => '2033-05' );
	$event_html = cer_test_list_html( 'cer-events', $event_filters );
	cer_test_assert( false !== strpos( $event_html, '12 matching events' ) && 5 === count( cer_test_list_ids( $event_html, 'event_ids' ) ), 'Combined event filters must count all matches and render five rows.' );
	$event_page_three = cer_test_list_html( 'cer-events', array_merge( $event_filters, array( 'paged' => 3 ) ) );
	cer_test_assert( 2 === count( cer_test_list_ids( $event_page_three, 'event_ids' ) ), 'Filtered event pagination did not reach all records.' );
	$empty_events = cer_test_list_html( 'cer-events', array_merge( $event_filters, array( 'event_status' => 'draft' ) ) );
	cer_test_assert( 0 === count( cer_test_list_ids( $empty_events, 'event_ids' ) ) && false !== strpos( $empty_events, '0 matching events' ), 'Empty combined event filters must not show unfiltered records.' );
	$zero_search_events = cer_test_list_html( 'cer-events', array_merge( $event_filters, array( 's' => '0' ) ) );
	cer_test_assert( array( $filter_events[10], $filter_events[0] ) === cer_test_list_ids( $zero_search_events, 'event_ids' ), 'The event search must not ignore the string zero.' );
	$registration_filters = array(
		'event_id_filter' => $filter_events[0], 'ticket_filter' => $filter_ticket,
		'payment_filter' => 'mpesa', 'reg_status' => 'paid',
		'date_start' => '2033-05-01', 'date_end' => '2033-05-12',
	);
	$registration_html = cer_test_list_html( 'cer-registrations', $registration_filters );
	cer_test_assert( false !== strpos( $registration_html, '12 matching registrations' ) && 5 === count( cer_test_list_ids( $registration_html, 'registration_ids' ) ), 'Combined registration filters must count all matching records.' );
	$registration_page_three = cer_test_list_html( 'cer-registrations', array_merge( $registration_filters, array( 'paged' => 3 ) ) );
	cer_test_assert( 2 === count( cer_test_list_ids( $registration_page_three, 'registration_ids' ) ), 'Filtered registration pagination did not reach all records.' );
	foreach ( array( 'Realtime User 11', 'realtime11@example.invalid', '0000011', 'realtime-registration-11' ) as $search ) {
		$searched = cer_test_list_html( 'cer-registrations', array_merge( $registration_filters, array( 's' => $search ) ) );
		cer_test_assert( array( $filter_registrations[11] ) === cer_test_list_ids( $searched, 'registration_ids' ), 'Exact registration search failed for an encrypted field or UUID.' );
	}
	$empty_registrations = cer_test_list_html( 'cer-registrations', array_merge( $registration_filters, array( 'payment_filter' => 'card' ) ) );
	cer_test_assert( 0 === count( cer_test_list_ids( $empty_registrations, 'registration_ids' ) ), 'Registration filters must be combined, not applied independently.' );
	$zero_search_registrations = cer_test_list_html( 'cer-registrations', array_merge( $registration_filters, array( 's' => '0' ) ) );
	cer_test_assert( 0 === count( cer_test_list_ids( $zero_search_registrations, 'registration_ids' ) ), 'The registration search must not ignore the string zero.' );
	cer_test_assert( false !== strpos( $registration_html, 'data-cer-filter-export' ) && false !== strpos( $registration_html, 'reg_status=paid' ), 'The filtered export URL is missing filter context.' );
	$now = current_time( 'mysql' );
	cer_test_assert( false !== $wpdb->update( $prefix . 'evt_events', array( 'deleted_at' => $now ), array( 'id' => $filter_events[11] ) ), 'Could not prepare an event Trash fixture.' );
	$trash_events = cer_test_list_html( 'cer-events', array_merge( $event_filters, array( 'view' => 'trash' ) ) );
	cer_test_assert( array( $filter_events[11] ) === cer_test_list_ids( $trash_events, 'event_ids' ), 'Event Trash filtering lost its view scope.' );
	cer_test_assert( false !== $wpdb->update( $prefix . 'evt_registrations', array( 'deleted_at' => $now ), array( 'id' => $filter_registrations[11] ) ), 'Could not prepare a registration Trash fixture.' );
	$trash_registrations = cer_test_list_html( 'cer-registrations', array_merge( $registration_filters, array( 'view' => 'trash', 's' => 'realtime11@example.invalid' ) ) );
	cer_test_assert( array( $filter_registrations[11] ) === cer_test_list_ids( $trash_registrations, 'registration_ids' ), 'Registration Trash filtering lost its view or exact search.' );
	foreach ( array( 'tickets', 'speakers', 'sponsors', 'pillars' ) as $type ) {
		$config = cer_event_item_config( $type );
		for ( $i = 0; $i < 12; $i++ ) {
			$row = array( 'event_id' => $event_a, $config['name_field'] => 'Realtime trash item ' . $i, 'deleted_at' => $now );
			if ( 'tickets' === $type ) {
				$row = array_merge( $row, array( 'price' => 1, 'currency' => 'KES', 'quantity_available' => 1 ) );
			}
			cer_test_insert( $config['table'], $row );
		}
		$item_args = array( 'event_id' => $event_a, 'item_type' => $type, 'item_view' => 'trash', 'item_search' => 'Realtime trash item' );
		$item_html = cer_test_list_html( 'cer-event-new', $item_args );
		cer_test_assert( false !== strpos( $item_html, '12 matching trashed items' ) && false !== strpos( $item_html, 'data-cer-live-list="items-' . $type . '"' ), 'Item Trash filtering or its live results surface is missing.' );
		$item_search_html = cer_test_list_html( 'cer-event-new', array_merge( $item_args, array( 'item_search' => 'item 11' ) ) );
		cer_test_assert( false !== strpos( $item_search_html, '1 matching trashed items' ), 'Item Trash search must match records beyond the first page.' );
	}
	$allowed = false;
	foreach ( array( 'cer-events', 'cer-registrations', 'cer-event-new' ) as $page ) {
		$denied = false;
		try {
			cer_test_list_html( $page );
		} catch ( CER_Test_Json_Exit $exception ) {
			$denied = true;
		}
		cer_test_assert( $denied, 'A filter result route did not enforce its capability check.' );
	}
	$allowed = true;

	foreach ( array(
		array( 'cer-events', array(), "SELECT COUNT(*) FROM {$prefix}evt_events e" ),
		array( 'cer-events', array(), 'SELECT e.*, COALESCE(reg.total_registrations, 0) AS total_registrations' ),
		array( 'cer-registrations', array(), "SELECT COUNT(*) FROM {$prefix}evt_registrations r" ),
		array( 'cer-registrations', array(), 'SELECT r.*, e.name AS event_name, tt.name AS ticket_name' ),
		array( 'cer-event-new', array( 'event_id' => $event_a, 'item_type' => 'tickets', 'item_view' => 'trash' ), "SELECT COUNT(*) FROM {$prefix}evt_ticket_types WHERE event_id" ),
		array( 'cer-event-new', array( 'event_id' => $event_a, 'item_type' => 'tickets', 'item_view' => 'trash' ), 'SELECT id, name AS item_name, deleted_at' ),
	) as $failure_case ) {
		$fail_filter_query = static function ( $query ) use ( $failure_case, $prefix ) {
			return 0 === strpos( $query, $failure_case[2] ) ? "SELECT COUNT(*) FROM {$prefix}cer_missing_test_table" : $query;
		};
		add_filter( 'query', $fail_filter_query );
		$failed = false;
		try {
			cer_test_list_html( $failure_case[0], $failure_case[1] );
		} catch ( CER_Test_Json_Exit $exception ) {
			$failed = true;
		} finally {
			remove_filter( 'query', $fail_filter_query );
		}
		cer_test_assert( $failed, 'A filter query failure was returned as successful empty results.' );
	}

	$fixture_manifest = getenv( 'CER_TEST_BROWSER_MANIFEST' );
	if ( $fixture_manifest ) {
		cer_test_assert( ! file_exists( $fixture_manifest ) && is_dir( dirname( $fixture_manifest ) ), 'The browser fixture manifest path is not available.' );
		for ( $i = 0; $i < 3; $i++ ) {
			cer_test_insert( $prefix . 'evt_registrations', array(
				'registration_uuid' => 'cer-browser-registration-' . $i,
				'payment_uuid' => 'cer-browser-payment-' . $i,
				'user_access_key' => 'cer-browser-access-' . $i,
				'event_id' => $event_a, 'ticket_type_id' => $ticket_ids[0],
				'full_name' => 'Synthetic Browser User', 'email' => 'browser@example.invalid',
				'phone' => '000000', 'payment_method' => 'test', 'status' => 'pending',
				'amount' => 0,
			) );
		}
		$fixture_token = bin2hex( random_bytes( 24 ) );
		cer_test_query( 'CREATE TABLE `cer_fixture_marker` (`token` VARCHAR(48) NOT NULL PRIMARY KEY)' );
		cer_test_insert( 'cer_fixture_marker', array( 'token' => $fixture_token ) );
		$manifest = wp_json_encode( array( 'database' => $test_database, 'token' => $fixture_token ) );
		cer_test_assert( false !== file_put_contents( $fixture_manifest, $manifest, LOCK_EX ), 'The browser fixture manifest could not be written.' );
		$keep_fixture = true;
	}

	echo "Dashboard integration tests passed (isolated database with production foreign keys).\n";
} finally {
	if ( $created && ! $keep_fixture ) {
		$wpdb->select( DB_NAME );
		cer_test_query( "DROP DATABASE `{$test_database}`" );
	}
}
