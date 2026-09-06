<?php
/**
 * Plugin Name: Custom Event Registration
 * Description: Adds a custom event registration workflow with an admin dashboard and a frontend registration form.
 * Version: 1.0.0
 * Author: OpenAI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'CER_PLUGIN_DIR' ) ) {
	define( 'CER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'CER_PLUGIN_URL' ) ) {
	define( 'CER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

require_once CER_PLUGIN_DIR . 'includes/class-laravel-connector.php';
require_once CER_PLUGIN_DIR . 'includes/class-cer-security.php';
require_once CER_PLUGIN_DIR . 'includes/class-cer-mailer.php';
require_once CER_PLUGIN_DIR . 'includes/class-cer-ticket-service.php';
require_once CER_PLUGIN_DIR . 'admin/admin-functions.php';

register_activation_hook( __FILE__, 'cer_activate_plugin' );
register_deactivation_hook( __FILE__, 'cer_deactivate_plugin' );
add_action( 'admin_menu', 'cer_register_admin_menu' );
add_action( 'admin_init', 'cer_maybe_ensure_event_schema' );
add_action( 'admin_init', 'cer_handle_admin_actions' );
add_action( 'wp_ajax_cer_submit_registration', 'cer_handle_ajax_submission' );
add_action( 'wp_ajax_nopriv_cer_submit_registration', 'cer_handle_ajax_submission' );
add_action( 'cer_sync_registration', 'cer_sync_registration_async' );
add_action( 'wp_enqueue_scripts', 'cer_enqueue_frontend_assets' );
add_action( 'admin_enqueue_scripts', 'cer_enqueue_admin_assets' );
add_action( 'wp_head', 'cer_maybe_output_event_seo_meta', 20 );
add_filter( 'pre_get_document_title', 'cer_filter_event_document_title', 20 );
add_filter( 'document_title_parts', 'cer_filter_event_document_title_parts', 20 );
add_action( 'init', 'cer_register_rewrite_rules' );
add_action( 'template_redirect', 'cer_handle_ticket_request', 0 );
add_action( 'wp_mail_failed', 'cer_log_mail_failure' );
add_action( 'phpmailer_init', 'cer_configure_smtp' );
add_filter( 'wp_mail_from', 'cer_mail_from_address' );
add_filter( 'wp_mail_from_name', 'cer_mail_from_name' );
add_action( 'template_redirect', 'cer_maybe_render_clean_event_url', 0 );
add_filter( 'query_vars', 'cer_register_query_vars' );
add_filter( 'template_include', 'cer_maybe_load_event_template' );
add_action( 'init', 'cer_maybe_flush_rewrite_rules', 99 );

function cer_get_event_route_base() {
	return 'event';
}

function cer_normalize_datetime_value( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	$datetime = DateTime::createFromFormat( 'Y-m-d\TH:i', $value );
	if ( ! $datetime ) {
		$datetime = DateTime::createFromFormat( 'Y-m-d H:i', $value );
	}
	if ( ! $datetime ) {
		$datetime = DateTime::createFromFormat( 'Y-m-d\TH:i:s', $value );
	}
	if ( ! $datetime ) {
		$datetime = new DateTime( $value );
	}

	return $datetime ? $datetime->format( 'Y-m-d H:i:s' ) : '';
}

function cer_format_datetime_for_picker( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	$datetime = DateTime::createFromFormat( 'Y-m-d H:i:s', $value );
	if ( ! $datetime ) {
		$datetime = DateTime::createFromFormat( 'Y-m-d H:i', $value );
	}
	if ( ! $datetime ) {
		$datetime = new DateTime( $value );
	}

	return $datetime ? $datetime->format( 'Y-m-d H:i' ) : '';
}

function cer_get_event_page_title( $event ) {
	if ( ! $event ) {
		return '';
	}

	$meta_title = isset( $event->meta_title ) ? trim( (string) $event->meta_title ) : '';
	if ( '' !== $meta_title ) {
		return $meta_title;
	}

	return trim( (string) $event->name );
}

function cer_get_event_public_url( $slug ) {
	$slug = sanitize_title( (string) $slug );
	if ( '' === $slug ) {
		return home_url( '/' );
	}

	return home_url( '/' . trailingslashit( cer_get_event_route_base() . '/' . $slug ) );
}

function cer_get_event_share_links( $event ) {
	if ( ! $event ) {
		return array();
	}

	$event_url = rawurlencode( cer_get_event_public_url( $event->slug ) );
	$event_title = rawurlencode( cer_get_event_page_title( $event ) );
	$share_text = rawurlencode( 'Join ' . cer_get_event_page_title( $event ) );

	return array(
		'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $event_url,
		'twitter' => 'https://twitter.com/intent/tweet?url=' . $event_url . '&text=' . $share_text,
		'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $event_url,
		'whatsapp' => 'https://wa.me/?text=' . $share_text . '%20' . $event_url,
		'copy' => cer_get_event_public_url( $event->slug ),
	);
}

function cer_filter_event_document_title( $title ) {
	if ( is_admin() ) {
		return $title;
	}

	$event = function_exists( 'cer_event_page_get_current_event' ) ? cer_event_page_get_current_event() : null;
	if ( ! $event ) {
		return $title;
	}

	$meta_title = cer_get_event_page_title( $event );
	return '' === $meta_title ? $title : $meta_title;
}

function cer_filter_event_document_title_parts( $title_parts ) {
	if ( is_admin() ) {
		return $title_parts;
	}

	$event = function_exists( 'cer_event_page_get_current_event' ) ? cer_event_page_get_current_event() : null;
	if ( ! $event ) {
		return $title_parts;
	}

	$meta_title = cer_get_event_page_title( $event );
	if ( '' === $meta_title ) {
		return $title_parts;
	}

	return array( 'title' => $meta_title );
}

function cer_maybe_output_event_seo_meta() {
	if ( is_admin() || ! function_exists( 'cer_event_page_get_current_event' ) ) {
		return;
	}

	$event = cer_event_page_get_current_event();
	if ( ! $event ) {
		return;
	}

	$meta_title = cer_get_event_page_title( $event );
	$meta_description = isset( $event->meta_description ) ? trim( (string) $event->meta_description ) : '';
	if ( '' === $meta_description ) {
		$meta_description = wp_trim_words( wp_strip_all_tags( (string) $event->description ), 30, '...' );
	}
	$meta_keywords = isset( $event->meta_keywords ) ? trim( (string) $event->meta_keywords ) : '';
	$event_url = cer_get_event_public_url( $event->slug );

	echo '<meta name="description" content="' . esc_attr( $meta_description ) . '" />' . "\n";
	if ( '' !== $meta_keywords ) {
		echo '<meta name="keywords" content="' . esc_attr( $meta_keywords ) . '" />' . "\n";
	}
	echo '<meta property="og:title" content="' . esc_attr( $meta_title ) . '" />' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $meta_description ) . '" />' . "\n";
	echo '<meta property="og:type" content="website" />' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $event_url ) . '" />' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $meta_title ) . '" />' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $meta_description ) . '" />' . "\n";
}

function cer_maybe_flush_rewrite_rules() {
	$rewrite_version = get_option( 'cer_rewrite_version', '' );
	if ( '2' === $rewrite_version ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'cer_rewrite_version', '2', false );
}

function cer_maybe_ensure_event_schema() {
	global $wpdb;
	$registrations_table = $wpdb->prefix . 'evt_registrations';
	$events_table = $wpdb->prefix . 'evt_events';
	if ( '6' === get_option( 'cer_schema_version', '' ) ) {
		return;
	}

	if ( '5' === get_option( 'cer_schema_version', '' ) ) {
		cer_add_column_if_missing( $events_table, 'meta_title', 'VARCHAR(255) NULL', 'slug' );
		cer_add_column_if_missing( $events_table, 'meta_description', 'TEXT NULL', 'meta_title' );
		cer_add_column_if_missing( $events_table, 'meta_keywords', 'TEXT NULL', 'meta_description' );
		cer_add_column_if_missing( $registrations_table, 'ticket_generated_at', 'DATETIME NULL', 'updated_at' );
		cer_add_column_if_missing( $registrations_table, 'ticket_sent_at', 'DATETIME NULL', 'ticket_generated_at' );
		cer_add_column_if_missing( $registrations_table, 'full_name_search_hash', 'CHAR(64) NULL', 'full_name' );
		cer_add_column_if_missing( $registrations_table, 'email_search_hash', 'CHAR(64) NULL', 'email' );
		cer_add_column_if_missing( $registrations_table, 'phone_search_hash', 'CHAR(64) NULL', 'phone' );
		cer_add_index_if_missing( $registrations_table, 'full_name_search_hash', 'full_name_search_hash' );
		cer_add_index_if_missing( $registrations_table, 'email_search_hash', 'email_search_hash' );
		cer_add_index_if_missing( $registrations_table, 'phone_search_hash', 'phone_search_hash' );
		cer_install_event_schema();
		update_option( 'cer_schema_version', '6', false );
		return;
	}

	cer_install_event_schema();
	cer_add_column_if_missing( $registrations_table, 'user_access_key', 'VARCHAR(64) NULL', 'registration_uuid' );
	cer_add_column_if_missing( $registrations_table, 'user_id', 'BIGINT(20) UNSIGNED NULL', 'event_id' );
	cer_add_column_if_missing( $registrations_table, 'payment_uuid', 'VARCHAR(64) NULL', 'registration_uuid' );
	cer_add_column_if_missing( $registrations_table, 'gateway_reference', 'VARCHAR(255) NULL', 'payment_uuid' );
	cer_add_column_if_missing( $registrations_table, 'payer_name', 'VARCHAR(512) NULL', 'full_name' );
	cer_add_column_if_missing( $registrations_table, 'receipt_number', 'VARCHAR(255) NULL', 'payer_name' );
	cer_add_column_if_missing( $registrations_table, 'serial_number', 'VARCHAR(255) NULL', 'receipt_number' );
	cer_add_column_if_missing( $registrations_table, 'confirmation_code', 'VARCHAR(255) NULL', 'serial_number' );
	cer_add_column_if_missing( $registrations_table, 'confirmed_amount', 'DECIMAL(10,2) NULL', 'amount' );
	cer_add_column_if_missing( $registrations_table, 'ticket_generated_at', 'DATETIME NULL', 'updated_at' );
	cer_add_column_if_missing( $registrations_table, 'ticket_sent_at', 'DATETIME NULL', 'ticket_generated_at' );
	cer_add_column_if_missing( $registrations_table, 'full_name_search_hash', 'CHAR(64) NULL', 'full_name' );
	cer_add_column_if_missing( $registrations_table, 'email_search_hash', 'CHAR(64) NULL', 'email' );
	cer_add_column_if_missing( $registrations_table, 'phone_search_hash', 'CHAR(64) NULL', 'phone' );
	cer_add_index_if_missing( $registrations_table, 'full_name_search_hash', 'full_name_search_hash' );
	cer_add_index_if_missing( $registrations_table, 'email_search_hash', 'email_search_hash' );
	cer_add_index_if_missing( $registrations_table, 'phone_search_hash', 'phone_search_hash' );
	$wpdb->query( "ALTER TABLE {$registrations_table} MODIFY full_name VARCHAR(512) NOT NULL, MODIFY email VARCHAR(512) NOT NULL, MODIFY phone VARCHAR(256) NOT NULL, MODIFY notes LONGTEXT NULL" );
	$rows = $wpdb->get_results( "SELECT id, user_access_key, full_name, email, phone, notes FROM {$registrations_table}", ARRAY_A );
	foreach ( $rows as $row ) {
		$update = array();
		if ( empty( $row['user_access_key'] ) ) {
			$update['user_access_key'] = bin2hex( random_bytes( 32 ) );
		}
		foreach ( array( 'full_name', 'email', 'phone', 'notes' ) as $field ) {
			if ( isset( $row[ $field ] ) && '' !== $row[ $field ] && 0 !== strpos( $row[ $field ], 'cer:v1:' ) ) {
				$update[ $field ] = cer_encrypt_pii( $row[ $field ] );
			}
			if ( in_array( $field, array( 'full_name', 'email', 'phone' ), true ) && isset( $row[ $field ] ) && '' !== $row[ $field ] ) {
				$update[ $field . '_search_hash' ] = cer_registration_search_hash( cer_decrypt_pii( $row[ $field ] ) );
			}
		}
		if ( $update ) {
			$wpdb->update( $registrations_table, $update, array( 'id' => (int) $row['id'] ) );
		}
	}
	$wpdb->query( "ALTER TABLE {$registrations_table} MODIFY user_access_key VARCHAR(64) NOT NULL" );
	$wpdb->query( "ALTER TABLE {$registrations_table} DROP FOREIGN KEY wp_evt_registrations_ibfk_1, ADD CONSTRAINT cer_registrations_event_fk FOREIGN KEY (event_id) REFERENCES {$wpdb->prefix}evt_events(id) ON DELETE RESTRICT" );
	if ( ! $wpdb->get_var( $wpdb->prepare( "SHOW INDEX FROM {$registrations_table} WHERE Key_name = %s", 'user_access_key' ) ) ) {
		$wpdb->query( "ALTER TABLE {$registrations_table} ADD UNIQUE KEY user_access_key (user_access_key)" );
	}
	update_option( 'cer_schema_version', '6', false );
	cer_add_column_if_missing( $events_table, 'meta_title', 'VARCHAR(255) NULL', 'slug' );
	cer_add_column_if_missing( $events_table, 'meta_description', 'TEXT NULL', 'meta_title' );
	cer_add_column_if_missing( $events_table, 'meta_keywords', 'TEXT NULL', 'meta_description' );
}

function cer_register_rewrite_rules() {
	add_rewrite_rule( '^' . preg_quote( cer_get_event_route_base(), '/' ) . '/([^/]+)/?$', 'index.php?event_slug=$matches[1]', 'top' );
}

function cer_register_query_vars( array $vars ) {
	$vars[] = 'event_slug';
	return $vars;
}

function cer_get_requested_event_slug_from_path() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path = trim( (string) wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );
	if ( '' === $path ) {
		return '';
	}

	$home_path = trim( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ), '/' );
	if ( '' !== $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
		$path = substr( $path, strlen( $home_path ) + 1 );
	}

	$segments = array_values( array_filter( explode( '/', $path ) ) );
	if ( count( $segments ) < 2 ) {
		return '';
	}

	if ( cer_get_event_route_base() !== sanitize_title( (string) $segments[0] ) ) {
		return '';
	}

	return sanitize_title( (string) $segments[1] );
}

function cer_maybe_render_clean_event_url() {
	if ( is_admin() || wp_doing_ajax() || is_feed() ) {
		return;
	}

	$event_slug = cer_get_requested_event_slug_from_path();
	if ( empty( $event_slug ) ) {
		return;
	}

	if ( get_page_by_path( $event_slug ) ) {
		return;
	}

	global $wpdb;
	$event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_events WHERE slug = %s AND status = %s LIMIT 1", $event_slug, 'published' ) );
	if ( ! $event ) {
		global $wp_query;
		if ( isset( $wp_query ) ) {
			$wp_query->set_404();
		}
		status_header( 404 );
		nocache_headers();
		return;
	}

	if ( defined( 'CER_PLUGIN_DIR' ) ) {
		require_once CER_PLUGIN_DIR . 'includes/event-page-renderer.php';
	}

	$template = locate_template( 'page-templates/template-event-registration.php' );
	if ( empty( $template ) ) {
		$template = CER_PLUGIN_DIR . 'includes/event-page-renderer.php';
	}

	status_header( 200 );
	nocache_headers();
	global $wp_query;
	if ( isset( $wp_query ) ) {
		$wp_query->is_404 = false;
		$wp_query->is_page = true;
	}

	include $template;
	exit;
}

function cer_maybe_load_event_template( $template ) {
	$event_slug = get_query_var( 'event_slug' );
	if ( empty( $event_slug ) ) {
		return $template;
	}

	$located = locate_template( 'page-templates/template-event-registration.php' );
	return $located ? $located : $template;
}

function cer_column_exists( string $table, string $column ): bool {
	global $wpdb;
	return ! empty( $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", $column ) ) );
}

function cer_add_column_if_missing( string $table, string $column, string $definition, string $after = '' ): void {
	global $wpdb;
	if ( cer_column_exists( $table, $column ) ) {
		return;
	}

	$query = "ALTER TABLE {$table} ADD COLUMN {$column} {$definition}";
	if ( '' !== $after ) {
		$query .= " AFTER {$after}";
	}
	$wpdb->query( $query );
	if ( $wpdb->last_error ) {
		error_log( sprintf( 'CER schema migration failed for %s.%s: %s', $table, $column, $wpdb->last_error ) );
	}
}

function cer_add_index_if_missing( string $table, string $index, string $columns ): void {
	global $wpdb;
	$exists = $wpdb->get_var( $wpdb->prepare( "SHOW INDEX FROM {$table} WHERE Key_name = %s", $index ) );
	if ( $exists ) {
		return;
	}

	$wpdb->query( "ALTER TABLE {$table} ADD INDEX {$index} ({$columns})" );
	if ( $wpdb->last_error ) {
		error_log( sprintf( 'CER schema migration failed for %s index: %s', $index, $wpdb->last_error ) );
	}
}

function cer_install_event_schema() {
	global $wpdb;

	$events_table = $wpdb->prefix . 'evt_events';
	$ticket_types_table = $wpdb->prefix . 'evt_ticket_types';
	$speakers_table = $wpdb->prefix . 'evt_speakers';
	$sponsorships_table = $wpdb->prefix . 'evt_sponsorships';
	$pillars_table = $wpdb->prefix . 'evt_pillars';
	$registrations_table = $wpdb->prefix . 'evt_registrations';
	$charset_collate = $wpdb->get_charset_collate();

	$sql_events = "CREATE TABLE IF NOT EXISTS $events_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_uuid VARCHAR(64) NOT NULL,
		name VARCHAR(255) NOT NULL,
		slug VARCHAR(255) NOT NULL,
		target_audience LONGTEXT,
		description LONGTEXT,
		event_date DATETIME NOT NULL,
		event_end_date DATETIME,
		venue VARCHAR(255),
		status VARCHAR(50) NOT NULL DEFAULT 'draft',
		show_speakers TINYINT(1) NOT NULL DEFAULT 1,
		show_sponsors TINYINT(1) NOT NULL DEFAULT 1,
		show_pillars TINYINT(1) NOT NULL DEFAULT 1,
		max_attendees INT UNSIGNED,
		featured_image_id BIGINT(20) UNSIGNED,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		UNIQUE KEY event_uuid (event_uuid),
		UNIQUE KEY slug (slug),
		KEY status (status),
		KEY event_date (event_date)
	) $charset_collate;";

	$sql_ticket_types = "CREATE TABLE IF NOT EXISTS $ticket_types_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		name VARCHAR(255) NOT NULL,
		price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
		currency VARCHAR(3) NOT NULL DEFAULT 'KES',
		quantity_available INT UNSIGNED,
		quantity_sold INT UNSIGNED DEFAULT 0,
		description TEXT,
		order_index INT UNSIGNED DEFAULT 0,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	$sql_speakers = "CREATE TABLE IF NOT EXISTS $speakers_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		name VARCHAR(255) NOT NULL,
		role VARCHAR(255),
		bio TEXT,
		photo_id BIGINT(20) UNSIGNED,
		is_visible TINYINT(1) NOT NULL DEFAULT 1,
		order_index INT UNSIGNED DEFAULT 0,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	$sql_sponsorships = "CREATE TABLE IF NOT EXISTS $sponsorships_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		name VARCHAR(255) NOT NULL,
		description TEXT,
		benefits LONGTEXT,
		cta_url VARCHAR(255),
		is_visible TINYINT(1) NOT NULL DEFAULT 1,
		order_index INT UNSIGNED DEFAULT 0,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	$sql_pillars = "CREATE TABLE IF NOT EXISTS $pillars_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		title VARCHAR(255) NOT NULL,
		description LONGTEXT,
		icon VARCHAR(255),
		is_visible TINYINT(1) NOT NULL DEFAULT 1,
		order_index INT UNSIGNED DEFAULT 0,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	$objectives_table = $wpdb->prefix . 'evt_objectives';
	$summit_structure_table = $wpdb->prefix . 'evt_summit_structure';
	$partners_table = $wpdb->prefix . 'evt_partners';
	$faqs_table = $wpdb->prefix . 'evt_faqs';

	$sql_objectives = "CREATE TABLE IF NOT EXISTS $objectives_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		title VARCHAR(255) NOT NULL,
		description LONGTEXT,
		icon VARCHAR(255),
		is_visible TINYINT(1) NOT NULL DEFAULT 1,
		order_index INT UNSIGNED DEFAULT 0,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	$sql_summit_structure = "CREATE TABLE IF NOT EXISTS $summit_structure_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		title VARCHAR(255) NOT NULL,
		description LONGTEXT,
		icon VARCHAR(255),
		is_visible TINYINT(1) NOT NULL DEFAULT 1,
		order_index INT UNSIGNED DEFAULT 0,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	$sql_partners = "CREATE TABLE IF NOT EXISTS $partners_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		name VARCHAR(255) NOT NULL,
		logo_id BIGINT(20) UNSIGNED,
		link_url VARCHAR(255),
		is_visible TINYINT(1) NOT NULL DEFAULT 1,
		order_index INT UNSIGNED DEFAULT 0,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	$sql_faqs = "CREATE TABLE IF NOT EXISTS $faqs_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		question VARCHAR(255) NOT NULL,
		answer LONGTEXT,
		is_visible TINYINT(1) NOT NULL DEFAULT 1,
		order_index INT UNSIGNED DEFAULT 0,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	$sql_registrations = "CREATE TABLE IF NOT EXISTS $registrations_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		registration_uuid VARCHAR(64) NOT NULL,
		payment_uuid VARCHAR(64) NOT NULL,
		gateway_reference VARCHAR(255) NULL,
		event_id BIGINT(20) UNSIGNED,
		ticket_type_id BIGINT(20) UNSIGNED,
		user_id BIGINT(20) UNSIGNED,
		user_access_key VARCHAR(64) NOT NULL,
		full_name VARCHAR(255) NOT NULL,
		full_name_search_hash CHAR(64) NULL,
		payer_name VARCHAR(512) NULL,
		email VARCHAR(255) NOT NULL,
		email_search_hash CHAR(64) NULL,
		phone VARCHAR(50) NOT NULL,
		phone_search_hash CHAR(64) NULL,
		ticket_type VARCHAR(100),
		payment_method VARCHAR(50) NOT NULL,
		amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
		confirmed_amount DECIMAL(10,2) NULL,
		notes TEXT,
		receipt_number VARCHAR(255) NULL,
		serial_number VARCHAR(255) NULL,
		confirmation_code VARCHAR(255) NULL,
		status VARCHAR(50) NOT NULL DEFAULT 'pending',
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		UNIQUE KEY registration_uuid (registration_uuid),
		UNIQUE KEY payment_uuid (payment_uuid),
		UNIQUE KEY user_access_key (user_access_key),
		KEY event_id (event_id),
		KEY ticket_type_id (ticket_type_id),
		KEY created_at (created_at),
		KEY status (status),
		KEY payment_method (payment_method),
		KEY event_status_created (event_id, status, created_at),
		KEY full_name_search_hash (full_name_search_hash),
		KEY email_search_hash (email_search_hash),
		KEY phone_search_hash (phone_search_hash),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE SET NULL,
		FOREIGN KEY (ticket_type_id) REFERENCES $ticket_types_table(id) ON DELETE SET NULL
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql_events );
	dbDelta( $sql_ticket_types );
	dbDelta( $sql_speakers );
	dbDelta( $sql_sponsorships );
	dbDelta( $sql_pillars );
	dbDelta( $sql_objectives );
	dbDelta( $sql_summit_structure );
	dbDelta( $sql_partners );
	dbDelta( $sql_faqs );
	dbDelta( $sql_registrations );

	cer_add_column_if_missing( $events_table, 'subtitle', 'VARCHAR(255) NULL', 'name' );
	cer_add_column_if_missing( $events_table, 'target_audience', 'LONGTEXT NULL', 'subtitle' );
	cer_add_column_if_missing( $events_table, 'meta_title', 'VARCHAR(255) NULL', 'slug' );
	cer_add_column_if_missing( $events_table, 'meta_description', 'TEXT NULL', 'meta_title' );
	cer_add_column_if_missing( $events_table, 'meta_keywords', 'TEXT NULL', 'meta_description' );
	cer_add_column_if_missing( $events_table, 'hero_badge_text', 'VARCHAR(255) NULL', 'target_audience' );
	cer_add_column_if_missing( $events_table, 'hero_convened_by', 'VARCHAR(255) NULL', 'hero_badge_text' );
	cer_add_column_if_missing( $events_table, 'hero_features', 'LONGTEXT NULL', 'hero_convened_by' );
	cer_add_column_if_missing( $events_table, 'event_info_heading', 'VARCHAR(255) NULL', 'hero_features' );
	cer_add_column_if_missing( $events_table, 'event_info_paragraph_1', 'LONGTEXT NULL', 'event_info_heading' );
	cer_add_column_if_missing( $events_table, 'event_info_paragraph_2', 'LONGTEXT NULL', 'event_info_paragraph_1' );
	cer_add_column_if_missing( $events_table, 'target_audience_heading', 'VARCHAR(255) NULL', 'event_info_paragraph_2' );
	cer_add_column_if_missing( $events_table, 'registration_heading', 'VARCHAR(255) NULL', 'target_audience_heading' );
	cer_add_column_if_missing( $events_table, 'registration_intro', 'LONGTEXT NULL', 'registration_heading' );
	cer_add_column_if_missing( $events_table, 'sponsorship_heading', 'VARCHAR(255) NULL', 'registration_intro' );
	cer_add_column_if_missing( $events_table, 'sponsorship_intro', 'LONGTEXT NULL', 'sponsorship_heading' );
	cer_add_column_if_missing( $events_table, 'speakers_heading', 'VARCHAR(255) NULL', 'sponsorship_intro' );
	cer_add_column_if_missing( $events_table, 'pillars_heading', 'VARCHAR(255) NULL', 'speakers_heading' );
	cer_add_column_if_missing( $events_table, 'pillars_intro', 'LONGTEXT NULL', 'pillars_heading' );
	cer_add_column_if_missing( $events_table, 'summary_heading', 'VARCHAR(255) NULL', 'pillars_intro' );
	cer_add_column_if_missing( $events_table, 'summary_note', 'LONGTEXT NULL', 'summary_heading' );
	cer_add_column_if_missing( $events_table, 'primary_cta_text', 'VARCHAR(120) NULL', 'summary_note' );
	cer_add_column_if_missing( $events_table, 'secondary_cta_text', 'VARCHAR(120) NULL', 'primary_cta_text' );
	cer_add_column_if_missing( $events_table, 'show_event_information', 'TINYINT(1) NOT NULL DEFAULT 1', 'secondary_cta_text' );
	cer_add_column_if_missing( $events_table, 'show_speakers', 'TINYINT(1) NOT NULL DEFAULT 1', 'status' );
	cer_add_column_if_missing( $events_table, 'show_sponsors', 'TINYINT(1) NOT NULL DEFAULT 1', 'show_speakers' );
	cer_add_column_if_missing( $events_table, 'show_pillars', 'TINYINT(1) NOT NULL DEFAULT 1', 'show_sponsors' );
	cer_add_column_if_missing( $registrations_table, 'payment_uuid', 'VARCHAR(64) NULL', 'registration_uuid' );
	cer_add_column_if_missing( $registrations_table, 'gateway_reference', 'VARCHAR(255) NULL', 'payment_uuid' );
	cer_add_column_if_missing( $speakers_table, 'is_visible', 'TINYINT(1) NOT NULL DEFAULT 1', 'photo_id' );
	cer_add_column_if_missing( $sponsorships_table, 'is_visible', 'TINYINT(1) NOT NULL DEFAULT 1', 'cta_url' );
	cer_add_column_if_missing( $pillars_table, 'is_visible', 'TINYINT(1) NOT NULL DEFAULT 1', 'icon' );
	cer_add_column_if_missing( $events_table, 'secondary_logo_id', 'BIGINT(20) UNSIGNED NULL', 'featured_image_id' );
	cer_add_column_if_missing( $events_table, 'objectives_heading', 'VARCHAR(255) NULL', 'secondary_logo_id' );
	cer_add_column_if_missing( $events_table, 'objectives_intro', 'LONGTEXT NULL', 'objectives_heading' );
	cer_add_column_if_missing( $events_table, 'show_objectives', 'TINYINT(1) NOT NULL DEFAULT 1', 'objectives_intro' );
	cer_add_column_if_missing( $events_table, 'summit_structure_heading', 'VARCHAR(255) NULL', 'show_objectives' );
	cer_add_column_if_missing( $events_table, 'summit_structure_intro', 'LONGTEXT NULL', 'summit_structure_heading' );
	cer_add_column_if_missing( $events_table, 'show_summit_structure', 'TINYINT(1) NOT NULL DEFAULT 1', 'summit_structure_intro' );
	cer_add_column_if_missing( $events_table, 'partners_heading', 'VARCHAR(255) NULL', 'show_summit_structure' );
	cer_add_column_if_missing( $events_table, 'partners_intro', 'LONGTEXT NULL', 'partners_heading' );
	cer_add_column_if_missing( $events_table, 'show_partners', 'TINYINT(1) NOT NULL DEFAULT 1', 'partners_intro' );
	cer_add_column_if_missing( $events_table, 'faq_heading', 'VARCHAR(255) NULL', 'show_partners' );
	cer_add_column_if_missing( $events_table, 'faq_intro', 'LONGTEXT NULL', 'faq_heading' );
	cer_add_column_if_missing( $events_table, 'show_faq', 'TINYINT(1) NOT NULL DEFAULT 1', 'faq_intro' );
	cer_add_column_if_missing( $events_table, 'location_link', 'VARCHAR(500) NULL', 'show_faq' );
	cer_add_column_if_missing( $events_table, 'location_lat', 'VARCHAR(50) NULL', 'location_link' );
	cer_add_column_if_missing( $events_table, 'location_lng', 'VARCHAR(50) NULL', 'location_lat' );
	cer_add_column_if_missing( $events_table, 'location_address', 'VARCHAR(500) NULL', 'location_lng' );
	cer_add_column_if_missing( $objectives_table, 'is_visible', 'TINYINT(1) NOT NULL DEFAULT 1', 'icon' );
	cer_add_column_if_missing( $summit_structure_table, 'is_visible', 'TINYINT(1) NOT NULL DEFAULT 1', 'icon' );
	cer_add_column_if_missing( $partners_table, 'is_visible', 'TINYINT(1) NOT NULL DEFAULT 1', 'link_url' );
	cer_add_column_if_missing( $faqs_table, 'is_visible', 'TINYINT(1) NOT NULL DEFAULT 1', 'answer' );
	cer_add_index_if_missing( $events_table, 'event_date', 'event_date' );
	cer_add_index_if_missing( $registrations_table, 'created_at', 'created_at' );
	cer_add_index_if_missing( $registrations_table, 'status', 'status' );
	cer_add_index_if_missing( $registrations_table, 'payment_method', 'payment_method' );
	cer_add_index_if_missing( $registrations_table, 'event_status_created', 'event_id, status, created_at' );
}

function cer_deactivate_plugin() {
	flush_rewrite_rules();
}

function cer_generate_unique_event_slug( $source, $exclude_event_id = 0 ) {
	global $wpdb;
	$table = $wpdb->prefix . 'evt_events';
	$base_slug = sanitize_title( (string) $source );
	$slug = $base_slug ?: 'event';
	$counter = 2;

	while ( $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE slug = %s AND id != %d LIMIT 1", $slug, $exclude_event_id ) ) ) {
		$slug = $base_slug . '-' . $counter;
		$counter++;
	}

	return $slug;
}

function cer_enqueue_frontend_assets() {
	$should_enqueue = is_page_template( 'page-templates/template-event-registration.php' ) || get_query_var( 'event_slug' ) || isset( $_GET['event_slug'] ) || isset( $_GET['event_id'] );

	if ( ! $should_enqueue ) {
		$event_slug = cer_get_requested_event_slug_from_path();
		if ( ! empty( $event_slug ) && ! get_page_by_path( $event_slug ) ) {
			global $wpdb;
			$should_enqueue = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}evt_events WHERE slug = %s LIMIT 1", $event_slug ) );
		}
	}

	if ( $should_enqueue ) {
		wp_enqueue_style( 'dashicons' );
		// Plus Jakarta Sans is declared on .cer-kamgc-page but was never enqueued,
		// so the display face silently fell back to Open Sans.
		wp_enqueue_style( 'cer-event-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:wght@400;500;600;700&display=swap', array(), null );
		wp_enqueue_style( 'cer-material-symbols', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap', array(), null );
		wp_enqueue_style( 'cer-event-registration', CER_PLUGIN_URL . 'assets/css/cer-event.css', array( 'cer-event-fonts', 'cer-material-symbols' ), filemtime( CER_PLUGIN_DIR . 'assets/css/cer-event.css' ) );
		wp_enqueue_script( 'cer-paystack-inline', 'https://js.paystack.co/v2/inline.js', array(), null, true );
		wp_enqueue_script( 'cer-event-registration', CER_PLUGIN_URL . 'assets/js/cer-registration.js', array( 'cer-paystack-inline' ), filemtime( CER_PLUGIN_DIR . 'assets/js/cer-registration.js' ), true );
		wp_localize_script( 'cer-event-registration', 'cerRegistrationSettings', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'cer_registration_form' ),
			'currency' => 'KES',
			'payment_verify_url' => home_url( '/laravel-engine/public/api/payment/verify' ),
		) );
	}

	// The event page is rendered by this plugin, not an Elementor document, so Elementor
	// never flags `_has_elementor_in_page` and skips localizing `elementorFrontendConfig`,
	// even though the active Elementor-built header still depends on it.
	if ( $should_enqueue && did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ) ) {
		add_action( 'wp_enqueue_scripts', 'cer_force_elementor_frontend_config', 20 );
	}
}

function cer_force_elementor_frontend_config() {
	$frontend = \Elementor\Plugin::$instance->frontend;
	if ( $frontend && ! $frontend->has_elementor_in_page() ) {
		$frontend->enqueue_scripts();
	}
}

function cer_enqueue_admin_assets( $hook ) {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
	if ( ! in_array( $page, array( 'cer-dashboard', 'cer-events', 'cer-event-new', 'cer-registrations' ), true ) ) {
		return;
	}

	wp_enqueue_style( 'cer-admin-modern', CER_PLUGIN_URL . 'assets/css/cer-admin-modern.css', array(), filemtime( CER_PLUGIN_DIR . 'assets/css/cer-admin-modern.css' ) );
	wp_enqueue_style( 'cer-admin-reporting', CER_PLUGIN_URL . 'assets/css/cer-admin-reporting.css', array( 'cer-admin-modern' ), filemtime( CER_PLUGIN_DIR . 'assets/css/cer-admin-reporting.css' ) );
	if ( 'cer-events' === $page ) {
		wp_enqueue_script( 'cer-admin-events', CER_PLUGIN_URL . 'assets/js/cer-admin-events.js', array( 'jquery' ), filemtime( CER_PLUGIN_DIR . 'assets/js/cer-admin-events.js' ), true );
	}

	if ( 'cer-event-new' === $page ) {
		wp_enqueue_media();
		wp_enqueue_style( 'cer-flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), null );
		wp_enqueue_script( 'cer-flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'cer-admin-event-form', CER_PLUGIN_URL . 'assets/js/cer-admin-event-form.js', array( 'jquery', 'cer-flatpickr' ), filemtime( CER_PLUGIN_DIR . 'assets/js/cer-admin-event-form.js' ), true );
	}
}

function cer_activate_plugin() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	// Events table
	$events_table = $wpdb->prefix . 'evt_events';
	$sql_events = "CREATE TABLE IF NOT EXISTS $events_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_uuid VARCHAR(64) NOT NULL,
		name VARCHAR(255) NOT NULL,
		slug VARCHAR(255) NOT NULL,
		meta_title VARCHAR(255) NULL,
		meta_description TEXT NULL,
		meta_keywords TEXT NULL,
		description LONGTEXT,
		event_date DATETIME NOT NULL,
		event_end_date DATETIME,
		venue VARCHAR(255),
		status VARCHAR(50) NOT NULL DEFAULT 'draft',
		max_attendees INT UNSIGNED,
		featured_image_id BIGINT(20) UNSIGNED,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		UNIQUE KEY event_uuid (event_uuid),
		UNIQUE KEY slug (slug),
		KEY status (status)
	) $charset_collate;";

	// Ticket types table
	$ticket_types_table = $wpdb->prefix . 'evt_ticket_types';
	$sql_ticket_types = "CREATE TABLE IF NOT EXISTS $ticket_types_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		name VARCHAR(255) NOT NULL,
		price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
		currency VARCHAR(3) NOT NULL DEFAULT 'KES',
		quantity_available INT UNSIGNED,
		quantity_sold INT UNSIGNED DEFAULT 0,
		description TEXT,
		order_index INT UNSIGNED DEFAULT 0,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	// Speakers table
	$speakers_table = $wpdb->prefix . 'evt_speakers';
	$sql_speakers = "CREATE TABLE IF NOT EXISTS $speakers_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		name VARCHAR(255) NOT NULL,
		role VARCHAR(255),
		bio TEXT,
		photo_id BIGINT(20) UNSIGNED,
		order_index INT UNSIGNED DEFAULT 0,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	// Sponsorships table
	$sponsorships_table = $wpdb->prefix . 'evt_sponsorships';
	$sql_sponsorships = "CREATE TABLE IF NOT EXISTS $sponsorships_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		name VARCHAR(255) NOT NULL,
		description TEXT,
		benefits LONGTEXT,
		cta_url VARCHAR(255),
		order_index INT UNSIGNED DEFAULT 0,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	// Registrations table (update existing if needed)
	$registrations_table = $wpdb->prefix . 'evt_registrations';
	$sql_registrations = "CREATE TABLE IF NOT EXISTS $registrations_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		registration_uuid VARCHAR(64) NOT NULL,
		payment_uuid VARCHAR(64) NOT NULL,
		gateway_reference VARCHAR(255) NULL,
		event_id BIGINT(20) UNSIGNED,
		ticket_type_id BIGINT(20) UNSIGNED,
		full_name VARCHAR(255) NOT NULL,
		email VARCHAR(255) NOT NULL,
		phone VARCHAR(50) NOT NULL,
		ticket_type VARCHAR(100),
		payment_method VARCHAR(50) NOT NULL,
		amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
		notes TEXT,
		status VARCHAR(50) NOT NULL DEFAULT 'pending',
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		UNIQUE KEY registration_uuid (registration_uuid),
		UNIQUE KEY payment_uuid (payment_uuid),
		KEY event_id (event_id),
		KEY ticket_type_id (ticket_type_id),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE SET NULL,
		FOREIGN KEY (ticket_type_id) REFERENCES $ticket_types_table(id) ON DELETE SET NULL
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql_events );
	dbDelta( $sql_ticket_types );
	dbDelta( $sql_speakers );
	dbDelta( $sql_sponsorships );
	dbDelta( $sql_registrations );
}

function cer_get_laravel_bridge_url() {
	if ( defined( 'CER_LARAVEL_BRIDGE_URL' ) && ! empty( CER_LARAVEL_BRIDGE_URL ) ) {
		return CER_LARAVEL_BRIDGE_URL;
	}
	return home_url( '/laravel-engine/public/api/payment/initiate' );
}

function cer_register_admin_menu() {
	add_menu_page(
		'ICRHK Events',
		'ICRHK Events',
		'manage_options',
		'cer-dashboard',
		'cer_render_dashboard_page',
		'dashicons-calendar-alt',
		25
	);

	add_submenu_page(
		'cer-dashboard',
		'Dashboard',
		'Dashboard',
		'manage_options',
		'cer-dashboard',
		'cer_render_dashboard_page'
	);

	add_submenu_page(
		'cer-dashboard',
		'All Events',
		'All Events',
		'manage_options',
		'cer-events',
		'cer_render_events_page'
	);

	add_submenu_page(
		'cer-dashboard',
		'Add New Event',
		'Add New Event',
		'manage_options',
		'cer-event-new',
		'cer_render_event_form_page'
	);

	add_submenu_page(
		'cer-dashboard',
		'Event Registrations',
		'Registrations',
		'manage_options',
		'cer-registrations',
		'cer_render_registrations_page'
	);

	delete_option( 'cer_payment_settings' );
}

function cer_render_admin_notice( string $message, string $type = 'success' ): void {
	printf( '<div class="notice notice-%s is-dismissible"><p>%s</p></div>', esc_attr( $type ), esc_html( $message ) );
}

function cer_handle_admin_actions() {
	if ( ! is_admin() ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_GET['cer_action'] ) ) {
		return;
	}

	$action = sanitize_text_field( wp_unslash( $_GET['cer_action'] ) );
	$registration_id = isset( $_GET['registration_id'] ) ? absint( wp_unslash( $_GET['registration_id'] ) ) : 0;
	$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

	if ( empty( $registration_id ) ) {
		return;
	}

	$valid_nonce = false;
	if ( 'toggle_status' === $action ) {
		$valid_nonce = wp_verify_nonce( $nonce, 'cer_toggle_status_' . $registration_id );
	} elseif ( 'delete' === $action ) {
		$valid_nonce = wp_verify_nonce( $nonce, 'cer_delete_' . $registration_id );
	}

	if ( ! $valid_nonce ) {
		wp_die( esc_html__( 'Security check failed.', 'custom-event-registration' ) );
	}

	global $wpdb;
	$table = $wpdb->prefix . 'evt_registrations';

	if ( 'toggle_status' === $action ) {
		$status = $wpdb->get_var( $wpdb->prepare( "SELECT status FROM $table WHERE id = %d", $registration_id ) );
		$new_status = 'pending' === $status ? 'confirmed' : 'pending';
		$wpdb->update( $table, array( 'status' => $new_status, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $registration_id ), array( '%s', '%s' ), array( '%d' ) );
	} elseif ( 'delete' === $action ) {
		$wpdb->delete( $table, array( 'id' => $registration_id ), array( '%d' ) );
	}

	$redirect_url = add_query_arg( array( 'page' => 'cer-registrations', 'updated' => 1 ), admin_url( 'admin.php' ) );
	wp_safe_redirect( $redirect_url );
	exit;
}

function cer_initiate_laravel_payment( array $registration_data ): array {
	$endpoint = cer_get_laravel_bridge_url();
	$response = wp_remote_post(
		esc_url_raw( $endpoint ),
		array(
			'headers' => array(
				'Content-Type' => 'application/json; charset=utf-8',
				'X-WordPress-Bridge' => 'cer',
			),
			'body' => wp_json_encode( $registration_data ),
			'timeout' => 20,
		)
	);

	if ( is_wp_error( $response ) ) {
		throw new Exception( 'Laravel bridge payment call failed: ' . $response->get_error_message() );
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = wp_remote_retrieve_body( $response );
	$decoded = json_decode( $body, true );

	if ( $code < 200 || $code >= 300 ) {
		throw new Exception( 'Laravel bridge payment call returned HTTP ' . $code . ': ' . wp_strip_all_tags( $body ) );
	}

	if ( ! is_array( $decoded ) ) {
		return array( 'status' => 'error', 'message' => 'Invalid response from Laravel bridge.' );
	}

	return $decoded;
}

function cer_confirm_registration_paid( int $registration_id ): array {
	global $wpdb;

	$registrations_table = $wpdb->prefix . 'evt_registrations';
	$events_table = $wpdb->prefix . 'evt_events';
	$ticket_types_table = $wpdb->prefix . 'evt_ticket_types';
	$wpdb->query( 'START TRANSACTION' );

	try {
		$registration = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$registrations_table} WHERE id = %d FOR UPDATE", $registration_id ) );
		if ( ! $registration ) {
			throw new Exception( 'Registration not found.' );
		}
		if ( 'paid' === $registration->status ) {
			$wpdb->query( 'COMMIT' );
			return array( 'ok' => true, 'already_paid' => true );
		}

		$event = $wpdb->get_row( $wpdb->prepare( "SELECT id, max_attendees FROM {$events_table} WHERE id = %d FOR UPDATE", $registration->event_id ) );
		$ticket = $wpdb->get_row( $wpdb->prepare( "SELECT id, quantity_available, quantity_sold FROM {$ticket_types_table} WHERE id = %d AND event_id = %d FOR UPDATE", $registration->ticket_type_id, $registration->event_id ) );
		if ( ! $event || ! $ticket ) {
			throw new Exception( 'The event or selected ticket is no longer available.' );
		}

		$paid_registrations = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$registrations_table} WHERE event_id = %d AND status = %s", $event->id, 'paid' ) );
		if ( ! empty( $event->max_attendees ) && $paid_registrations >= (int) $event->max_attendees ) {
			throw new Exception( 'This event has reached its attendance capacity.' );
		}
		if ( null !== $ticket->quantity_available && (int) $ticket->quantity_sold >= (int) $ticket->quantity_available ) {
			throw new Exception( 'This ticket type is sold out.' );
		}

		$ticket_updated = $wpdb->query( $wpdb->prepare( "UPDATE {$ticket_types_table} SET quantity_sold = quantity_sold + 1, updated_at = %s WHERE id = %d", current_time( 'mysql' ), $ticket->id ) );
		$registration_updated = $wpdb->update( $registrations_table, array( 'status' => 'paid', 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $registration->id ), array( '%s', '%s' ), array( '%d' ) );
		if ( false === $ticket_updated || false === $registration_updated ) {
			throw new Exception( 'Could not confirm the registration.' );
		}

		$wpdb->query( 'COMMIT' );
		delete_transient( 'cer_dashboard_metrics' );
		return array( 'ok' => true, 'already_paid' => false );
	} catch ( Exception $exception ) {
		$wpdb->query( 'ROLLBACK' );
		return array( 'ok' => false, 'message' => $exception->getMessage() );
	}
}

function cer_handle_ajax_submission() {
	$nonce = isset( $_POST['security'] ) ? sanitize_text_field( wp_unslash( $_POST['security'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'cer_registration_form' ) ) {
		wp_send_json_error( array( 'message' => 'Security verification failed.' ) );
	}

	global $wpdb;

	$full_name = sanitize_text_field( wp_unslash( $_POST['full_name'] ?? '' ) );
	$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$phone = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$ticket_type_id = absint( wp_unslash( $_POST['ticket_type_id'] ?? 0 ) );
	$ticket_type = sanitize_text_field( wp_unslash( $_POST['ticket_type'] ?? '' ) );
	$payment_method = sanitize_text_field( wp_unslash( $_POST['payment_method'] ?? '' ) );
	$notes = sanitize_textarea_field( wp_unslash( $_POST['notes'] ?? '' ) );
	$event_id = absint( wp_unslash( $_POST['event_id'] ?? 0 ) );

		$payment_method = in_array( $payment_method, array( 'mpesa', 'card' ), true ) ? $payment_method : '';
		// Phone is required for both methods; full name is only collected for M-Pesa.
		// Card intentionally never collects a name — Paystack supplies it from the charge.
		$phone_digits = preg_replace( '/\D+/', '', $phone );
		if ( preg_match( '/^0?7\d{8}$/', $phone_digits ) ) {
			$phone = '254' . ( '0' === $phone_digits[0] ? substr( $phone_digits, 1 ) : $phone_digits );
		} elseif ( preg_match( '/^2547\d{8}$/', $phone_digits ) ) {
			$phone = $phone_digits;
		} else {
			$phone = '';
		}

		if ( empty( $email ) || empty( $payment_method ) || empty( $phone ) || ( 'mpesa' === $payment_method && empty( $full_name ) ) ) {
		wp_send_json_error( array( 'message' => 'Please complete all required fields.' ) );
	}
		if ( 'card' === $payment_method ) {
			$full_name = '';
		}

	$event = $wpdb->get_row( $wpdb->prepare( "SELECT id, max_attendees FROM {$wpdb->prefix}evt_events WHERE id = %d AND status = %s LIMIT 1", $event_id, 'published' ) );
	$ticket = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, price, quantity_available, quantity_sold FROM {$wpdb->prefix}evt_ticket_types WHERE id = %d AND event_id = %d LIMIT 1", $ticket_type_id, $event_id ) );
	if ( ! $event || ! $ticket ) {
		wp_send_json_error( array( 'message' => 'The selected event or ticket is no longer available.' ) );
	}

	$paid_registrations = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}evt_registrations WHERE event_id = %d AND status = %s", $event_id, 'paid' ) );
	if ( ! empty( $event->max_attendees ) && $paid_registrations >= (int) $event->max_attendees ) {
		wp_send_json_error( array( 'message' => 'This event has reached its attendance capacity.' ) );
	}
	if ( null !== $ticket->quantity_available && (int) $ticket->quantity_sold >= (int) $ticket->quantity_available ) {
		wp_send_json_error( array( 'message' => 'This ticket type is sold out.' ) );
	}

	$ticket_type = (string) $ticket->name;
	$amount = number_format( (float) $ticket->price, 2, '.', '' );
	$registration_uuid = wp_generate_uuid4();
	$payment_uuid = wp_generate_uuid4();
	$registrations_table = $wpdb->prefix . 'evt_registrations';
	$inserted = $wpdb->insert(
		$registrations_table,
		cer_prepare_registration_row( array(
			'registration_uuid' => $registration_uuid,
			'payment_uuid' => $payment_uuid,
			'event_id' => $event_id ? $event_id : null,
			'ticket_type_id' => $ticket_type_id ? $ticket_type_id : null,
			'full_name' => $full_name,
			'email' => $email,
			'phone' => $phone,
			'ticket_type' => $ticket_type,
			'payment_method' => $payment_method,
			'amount' => $amount,
			'notes' => $notes,
			'status' => 'awaiting_payment',
		) ),
		array( '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s' )
	);

	if ( $wpdb->last_error ) {
		error_log( 'CER registration insert failed: ' . $wpdb->last_error );
	}

	if ( $inserted ) {
		$sync_data = array(
			'registration_uuid' => $registration_uuid,
			'payment_uuid' => $payment_uuid,
			'full_name' => $full_name,
			'email' => $email,
			'phone' => $phone,
			'ticket_type' => $ticket_type,
			'payment_method' => $payment_method,
			'amount' => $amount,
			'notes' => $notes,
			'currency' => 'KES',
			'event_id' => $event_id,
			'ticket_type_id' => $ticket_type_id,
			'status' => 'awaiting_payment',
		);

		try {
			$payment_response = cer_initiate_laravel_payment( $sync_data );
			$requires_access_code = 'card' === $payment_method;
			if ( empty( $payment_response['reference'] ) || ( $requires_access_code && empty( $payment_response['access_code'] ) ) || ( isset( $payment_response['status'] ) && 'failed' === $payment_response['status'] ) ) {
				throw new Exception( ! empty( $payment_response['message'] ) ? sanitize_text_field( $payment_response['message'] ) : 'The payment gateway did not return a checkout reference.' );
			}
			$wpdb->update(
				$registrations_table,
				array(
					'gateway_reference' => ! empty( $payment_response['reference'] ) ? $payment_response['reference'] : '',
					'updated_at' => current_time( 'mysql' ),
					'status' => ! empty( $payment_response['status'] ) ? sanitize_text_field( $payment_response['status'] ) : 'awaiting_payment',
				),
				array( 'registration_uuid' => $registration_uuid ),
				array( '%s', '%s', '%s' ),
				array( '%s' )
			);
			delete_transient( 'cer_dashboard_metrics' );
			if ( ! wp_schedule_single_event( time() + 1, 'cer_sync_registration', array( $sync_data ) ) ) {
				error_log( 'CER Laravel sync could not be scheduled.' );
			}
			wp_send_json_success( array(
					'message' => 'Registration received. Opening secure payment checkout.',
					'access_code' => ! empty( $payment_response['access_code'] ) ? sanitize_text_field( $payment_response['access_code'] ) : '',
					'reference' => ! empty( $payment_response['reference'] ) ? sanitize_text_field( $payment_response['reference'] ) : '',
			) );
		} catch ( Exception $e ) {
			error_log( 'CER payment initiation failed: ' . $e->getMessage() );
			$wpdb->update(
				$registrations_table,
				array( 'status' => 'failed', 'updated_at' => current_time( 'mysql' ) ),
				array( 'registration_uuid' => $registration_uuid ),
				array( '%s', '%s' ),
				array( '%s' )
			);
			wp_send_json_error( array( 'message' => 'Payment initiation failed: ' . $e->getMessage() ) );
		}
	}
	wp_send_json_error( array( 'message' => 'The registration could not be saved.' ) );
}

function cer_sync_registration_async( array $sync_data ): void {
	try {
		$connector = new CER_Laravel_Connector();
		$connector->sync_registration( $sync_data );
	} catch ( Exception $e ) {
		error_log( 'CER Laravel sync failed: ' . $e->getMessage() );
	}
}

require_once CER_PLUGIN_DIR . 'includes/registration-functions.php';
