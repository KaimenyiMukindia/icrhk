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
require_once CER_PLUGIN_DIR . 'includes/class-cer-mail-smtp-resolver.php';
require_once CER_PLUGIN_DIR . 'includes/class-cer-mailer.php';
require_once CER_PLUGIN_DIR . 'includes/class-cer-ticket-service.php';
require_once CER_PLUGIN_DIR . 'includes/class-cer-event-notifications.php';
require_once CER_PLUGIN_DIR . 'admin/admin-functions.php';

register_activation_hook( __FILE__, 'cer_activate_plugin' );
register_deactivation_hook( __FILE__, 'cer_deactivate_plugin' );
add_action( 'admin_menu', 'cer_register_admin_menu' );
add_action( 'admin_init', 'cer_maybe_ensure_event_schema' );
add_action( 'admin_init', 'cer_handle_admin_actions' );
if ( did_action( 'plugins_loaded' ) ) {
	cer_maybe_install_dashboard_trash_schema();
	cer_maybe_install_event_item_trash_schema();
	cer_maybe_install_ticket_maintenance_schema();
} else {
	add_action( 'plugins_loaded', 'cer_maybe_install_dashboard_trash_schema' );
	add_action( 'plugins_loaded', 'cer_maybe_install_event_item_trash_schema' );
	add_action( 'plugins_loaded', 'cer_maybe_install_ticket_maintenance_schema' );
}
add_action( 'cer_purge_expired_event_trash', 'cer_purge_expired_event_trash' );
add_action( 'wp_ajax_cer_submit_registration', 'cer_handle_ajax_submission' );
add_action( 'wp_ajax_nopriv_cer_submit_registration', 'cer_handle_ajax_submission' );
add_action( 'wp_ajax_cer_ticket_availability', 'cer_ajax_ticket_availability' );
add_action( 'wp_ajax_nopriv_cer_ticket_availability', 'cer_ajax_ticket_availability' );
add_action( 'wp_ajax_cer_dashboard_event_metrics', 'cer_ajax_dashboard_event_metrics' );
add_action( 'rest_api_init', 'cer_register_rest_routes' );
add_action( 'cer_sync_registration', 'cer_sync_registration_async' );
add_action( 'cer_release_expired_registrations', 'cer_release_expired_registrations' );
add_action( 'init', 'cer_schedule_registration_expiry_cleanup' );
add_action( 'wp_loaded', 'cer_maybe_release_expired_registrations_on_load' );
add_action( 'wp_enqueue_scripts', 'cer_enqueue_frontend_assets' );
add_action( 'admin_enqueue_scripts', 'cer_enqueue_admin_assets' );
add_action( 'wp_head', 'cer_maybe_output_event_seo_meta', 20 );
add_filter( 'pre_get_document_title', 'cer_filter_event_document_title', 20 );
add_filter( 'document_title_parts', 'cer_filter_event_document_title_parts', 20 );
add_action( 'init', 'cer_register_rewrite_rules' );
add_action( 'template_redirect', 'cer_handle_ticket_request', 0 );
add_action( 'wp_mail_failed', 'cer_log_mail_failure' );
add_filter( 'pre_wp_mail', 'cer_maybe_send_via_resend', 10, 2 );
add_action( 'phpmailer_init', 'cer_configure_smtp' );
add_filter( 'wp_mail_from', 'cer_mail_from_address' );
add_filter( 'wp_mail_from_name', 'cer_mail_from_name' );
add_action( 'template_redirect', 'cer_maybe_render_clean_event_url', 0 );
add_filter( 'query_vars', 'cer_register_query_vars' );
// 99: after Elementor Pro's Theme Builder (11), which would otherwise swap in its archive template.
add_filter( 'template_include', 'cer_maybe_load_event_template', 99 );
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
	if ( '' === $value || '0000-00-00' === $value || '0000-00-00 00:00:00' === $value ) {
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
	$partners_table = $wpdb->prefix . 'evt_partners';
	$ticket_types_table = $wpdb->prefix . 'evt_ticket_types';
	$schema_version = get_option( 'cer_schema_version', '' );
	$branding_columns = array(
		'brand_color_1' => array( 'VARCHAR(20) NULL', 'secondary_logo_id' ),
		'brand_color_2' => array( 'VARCHAR(20) NULL', 'brand_color_1' ),
		'background_image_id' => array( 'BIGINT(20) UNSIGNED NULL', 'brand_color_2' ),
	);
	$existing_branding_columns = $wpdb->get_col(
		$wpdb->prepare(
			"SHOW COLUMNS FROM {$events_table} WHERE Field IN (%s, %s, %s)",
			array_keys( $branding_columns )
		)
	);
	if ( $wpdb->last_error ) {
		error_log( 'CER branding schema check failed: ' . $wpdb->last_error );
		$existing_branding_columns = array();
	}
	foreach ( $branding_columns as $column => $definition ) {
		if ( ! in_array( $column, $existing_branding_columns, true ) ) {
			cer_add_column_if_missing( $events_table, $column, $definition[0], $definition[1] );
		}
	}

	$registration_profile_columns = array(
		'attendee_title' => array( 'VARCHAR(512) NULL', 'full_name' ),
		'organization' => array( 'VARCHAR(1024) NULL', 'attendee_title' ),
		'attendee_role' => array( 'VARCHAR(1024) NULL', 'organization' ),
		'county' => array( 'VARCHAR(512) NULL', 'phone' ),
		'age_group' => array( 'VARCHAR(128) NULL', 'county' ),
		'gender' => array( 'VARCHAR(128) NULL', 'age_group' ),
		'accessibility_support' => array( 'VARCHAR(512) NULL', 'gender' ),
		'accessibility_details' => array( 'LONGTEXT NULL', 'accessibility_support' ),
	);
	foreach ( $registration_profile_columns as $column => $definition ) {
		cer_add_column_if_missing( $registrations_table, $column, $definition[0], $definition[1] );
	}

	if ( '11' === $schema_version ) {
		return;
	}
	if ( in_array( $schema_version, array( '9', '10' ), true ) ) {
		update_option( 'cer_schema_version', '11', false );
		return;
	}

	if ( '8' === $schema_version ) {
		cer_install_event_schema();
		cer_add_column_if_missing( $registrations_table, 'reserved_until', 'DATETIME NULL', 'status' );
		cer_add_column_if_missing( $registrations_table, 'release_notified_at', 'DATETIME NULL', 'reserved_until' );
		cer_backfill_paid_ticket_inventory();
		cer_add_column_if_missing( $events_table, 'deleted_at', 'DATETIME NULL', 'status' );
		cer_add_column_if_missing( $registrations_table, 'deleted_at', 'DATETIME NULL', 'status' );
		cer_add_index_if_missing( $events_table, 'deleted_at', 'deleted_at' );
		cer_add_index_if_missing( $registrations_table, 'deleted_at', 'deleted_at' );
		update_option( 'cer_schema_version', '9', false );
		return;
	}

	if ( '7' === $schema_version ) {
		cer_install_event_schema();
		cer_add_column_if_missing( $events_table, 'mail_sender_email', 'VARCHAR(255) NULL', 'location_address' );
		cer_add_column_if_missing( $events_table, 'mail_password_encrypted', 'TEXT NULL', 'mail_sender_email' );
		cer_add_column_if_missing( $events_table, 'mail_smtp_host', 'VARCHAR(255) NULL', 'mail_password_encrypted' );
		cer_add_column_if_missing( $events_table, 'mail_smtp_port', 'VARCHAR(10) NULL', 'mail_smtp_host' );
		cer_add_column_if_missing( $events_table, 'mail_smtp_secure', 'VARCHAR(20) NULL', 'mail_smtp_port' );
		cer_add_column_if_missing( $events_table, 'mail_from_name', 'VARCHAR(255) NULL', 'mail_smtp_secure' );
		cer_add_column_if_missing( $events_table, 'mail_notification_email', 'VARCHAR(255) NULL', 'mail_from_name' );
		cer_add_column_if_missing( $registrations_table, 'reserved_until', 'DATETIME NULL', 'status' );
		cer_add_column_if_missing( $registrations_table, 'release_notified_at', 'DATETIME NULL', 'reserved_until' );
		cer_backfill_paid_ticket_inventory();
		update_option( 'cer_schema_version', '8', false );
		return;
	}

	if ( '6' === $schema_version ) {
		cer_install_event_schema();
		cer_add_column_if_missing( $events_table, 'mail_sender_email', 'VARCHAR(255) NULL', 'location_address' );
		cer_add_column_if_missing( $events_table, 'mail_password_encrypted', 'TEXT NULL', 'mail_sender_email' );
		cer_add_column_if_missing( $events_table, 'mail_smtp_host', 'VARCHAR(255) NULL', 'mail_password_encrypted' );
		cer_add_column_if_missing( $events_table, 'mail_smtp_port', 'VARCHAR(10) NULL', 'mail_smtp_host' );
		cer_add_column_if_missing( $events_table, 'mail_smtp_secure', 'VARCHAR(20) NULL', 'mail_smtp_port' );
		cer_add_column_if_missing( $events_table, 'mail_from_name', 'VARCHAR(255) NULL', 'mail_smtp_secure' );
		cer_add_column_if_missing( $events_table, 'mail_notification_email', 'VARCHAR(255) NULL', 'mail_from_name' );
		update_option( 'cer_schema_version', '7', false );
		cer_add_column_if_missing( $partners_table, 'url', 'VARCHAR(500) NULL', 'logo_id' );
		cer_add_column_if_missing( $partners_table, 'tel_no', 'VARCHAR(50) NULL', 'url' );
		cer_add_column_if_missing( $partners_table, 'email', 'VARCHAR(255) NULL', 'tel_no' );
		cer_add_column_if_missing( $partners_table, 'address', 'VARCHAR(255) NULL', 'email' );
		if ( cer_column_exists( $partners_table, 'link_url' ) && ! cer_column_exists( $partners_table, 'url' ) ) {
			$wpdb->query( "ALTER TABLE {$partners_table} CHANGE link_url url VARCHAR(500) NULL" );
		}
		if ( cer_column_exists( $partners_table, 'link_url' ) && cer_column_exists( $partners_table, 'url' ) ) {
			$wpdb->query( "UPDATE {$partners_table} SET url = IFNULL(url, link_url) WHERE url IS NULL OR url = ''" );
			$wpdb->query( "ALTER TABLE {$partners_table} DROP COLUMN link_url" );
		}
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
	cer_add_column_if_missing( $events_table, 'mail_sender_email', 'VARCHAR(255) NULL', 'location_address' );
	cer_add_column_if_missing( $events_table, 'mail_password_encrypted', 'TEXT NULL', 'mail_sender_email' );
	cer_add_column_if_missing( $events_table, 'mail_smtp_host', 'VARCHAR(255) NULL', 'mail_password_encrypted' );
	cer_add_column_if_missing( $events_table, 'mail_smtp_port', 'VARCHAR(10) NULL', 'mail_smtp_host' );
	cer_add_column_if_missing( $events_table, 'mail_smtp_secure', 'VARCHAR(20) NULL', 'mail_smtp_port' );
	cer_add_column_if_missing( $events_table, 'mail_from_name', 'VARCHAR(255) NULL', 'mail_smtp_secure' );
	cer_add_column_if_missing( $events_table, 'mail_notification_email', 'VARCHAR(255) NULL', 'mail_from_name' );
	cer_add_column_if_missing( $registrations_table, 'reserved_until', 'DATETIME NULL', 'status' );
	cer_add_column_if_missing( $registrations_table, 'release_notified_at', 'DATETIME NULL', 'reserved_until' );
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
	cer_backfill_paid_ticket_inventory();
	update_option( 'cer_schema_version', '8', false );
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

function cer_get_registration_hold_seconds(): int {
	return 1800;
}

function cer_registration_hold_is_expired( $registration ): bool {
	if ( ! is_object( $registration ) && ! is_array( $registration ) ) {
		return false;
	}
	$reserved_until = is_object( $registration ) ? ( $registration->reserved_until ?? null ) : ( $registration['reserved_until'] ?? null );
	if ( empty( $reserved_until ) ) {
		return false;
	}
	return strtotime( (string) $reserved_until ) <= time();
}

function cer_should_allow_late_payment_confirmation( $registration, $ticket = null ): bool {
	if ( ! cer_registration_hold_is_expired( $registration ) ) {
		return false;
	}
	if ( is_object( $registration ) && isset( $registration->status ) && 'paid' === $registration->status ) {
		return false;
	}
	if ( is_array( $registration ) && isset( $registration['status'] ) && 'paid' === $registration['status'] ) {
		return false;
	}
	return true;
}

function cer_send_registration_release_notifications( int $registration_id ): void {
	global $wpdb;
	$table = $wpdb->prefix . 'evt_registrations';
	$registration = $wpdb->get_row( $wpdb->prepare( "SELECT r.*, e.name AS event_name, e.mail_notification_email, e.mail_sender_email, e.mail_from_name FROM {$table} r LEFT JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id WHERE r.id = %d LIMIT 1", $registration_id ), ARRAY_A );
	if ( ! $registration || ! empty( $registration['release_notified_at'] ) ) {
		return;
	}
	$event_id = (int) ( $registration['event_id'] ?? 0 );
	$event_name = ! empty( $registration['event_name'] ) ? (string) $registration['event_name'] : 'ICRHK Event';
	$recipient = cer_get_event_mail_recipient( $registration_id );
	if ( '' === $recipient ) {
		return;
	}
	$config = cer_get_mail_config_from_event( $event_id, $event_name );
	if ( empty( $config ) ) {
		return;
	}
	$registrant_name = function_exists( 'cer_decrypt_pii' ) ? cer_decrypt_pii( $registration['full_name'] ?? '' ) : (string) ( $registration['full_name'] ?? '' );
	$body = '<p>Registration hold expired for <strong>' . esc_html( $event_name ) . '</strong>.</p><p><strong>Attendee:</strong> ' . esc_html( $registrant_name ) . '<br><strong>Email:</strong> ' . esc_html( (string) ( $registration['email'] ?? '' ) ) . '<br><strong>Ticket:</strong> ' . esc_html( (string) ( $registration['ticket_type'] ?? '' ) ) . '</p><p>The reserved ticket has been released back to inventory due to a timed-out payment hold.</p>';
	$context = cer_set_current_mail_config( $event_id, $event_name );
	$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: ' . $config['from_email'] . ' <' . $config['from_email'] . '>', 'Reply-To: ' . $config['from_email'] );
	$sent = wp_mail( $recipient, 'Reservation release for ' . $event_name, $body, $headers );
	cer_clear_current_mail_config();
	if ( $sent ) {
		$wpdb->update( $table, array( 'release_notified_at' => current_time( 'mysql' ) ), array( 'id' => $registration_id ), array( '%s' ), array( '%d' ) );
	}
}

function cer_backfill_paid_ticket_inventory(): void {
	global $wpdb;
	$registrations_table = $wpdb->prefix . 'evt_registrations';
	$ticket_types_table = $wpdb->prefix . 'evt_ticket_types';
	$rows = $wpdb->get_results( "SELECT ticket_type_id, COUNT(*) AS paid_count FROM {$registrations_table} WHERE (status = 'paid' OR (status = 'confirmed' AND amount <= 0)) AND ticket_type_id IS NOT NULL GROUP BY ticket_type_id" );
	if ( empty( $rows ) ) {
		return;
	}
	foreach ( $rows as $row ) {
		$ticket_id = (int) $row->ticket_type_id;
		$paid_count = (int) $row->paid_count;
		$wpdb->query( $wpdb->prepare( "UPDATE {$ticket_types_table} SET quantity_sold = %d, updated_at = %s WHERE id = %d", $paid_count, current_time( 'mysql' ), $ticket_id ) );
	}
}

function cer_schedule_registration_expiry_cleanup(): void {
	if ( ! wp_next_scheduled( 'cer_release_expired_registrations' ) ) {
		wp_schedule_event( time() + 300, 'every_5_minutes', 'cer_release_expired_registrations' );
	}
	if ( ! wp_next_scheduled( 'cer_purge_expired_event_trash' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'hourly', 'cer_purge_expired_event_trash' );
	}
}

function cer_register_inventory_cleanup_schedule( $schedules ) {
	$schedules['every_5_minutes'] = array(
		'interval' => 300,
		'display'  => 'Every 5 Minutes',
	);
	return $schedules;
}

add_filter( 'cron_schedules', 'cer_register_inventory_cleanup_schedule' );

function cer_maybe_release_expired_registrations_on_load(): void {
	if ( is_admin() ) {
		return;
	}
	cer_release_expired_registrations( 25 );
}

function cer_release_expired_registrations( $limit = 100 ): int {
	global $wpdb;
	$registrations_table = $wpdb->prefix . 'evt_registrations';
	$ticket_types_table = $wpdb->prefix . 'evt_ticket_types';
	$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$registrations_table} WHERE reserved_until IS NOT NULL AND reserved_until < NOW() AND status IN ('pending', 'awaiting_payment') ORDER BY reserved_until ASC LIMIT %d", (int) $limit ) );
	if ( empty( $rows ) ) {
		return 0;
	}

	$released = 0;
	foreach ( $rows as $registration ) {
		$registration_id = (int) $registration->id;
		$wpdb->query( 'START TRANSACTION' );
		try {
			$current = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$registrations_table} WHERE id = %d FOR UPDATE", $registration_id ) );
			if ( ! $current || empty( $current->reserved_until ) || $current->reserved_until >= current_time( 'mysql' ) || ! in_array( $current->status, array( 'pending', 'awaiting_payment' ), true ) ) {
				$wpdb->query( 'COMMIT' );
				continue;
			}

			$ticket = $wpdb->get_row( $wpdb->prepare( "SELECT id, quantity_sold, quantity_available FROM {$ticket_types_table} WHERE id = %d AND event_id = %d FOR UPDATE", $current->ticket_type_id, $current->event_id ) );
			if ( $ticket ) {
				$wpdb->query( $wpdb->prepare( "UPDATE {$ticket_types_table} SET quantity_sold = GREATEST( quantity_sold - 1, 0 ), updated_at = %s WHERE id = %d", current_time( 'mysql' ), $ticket->id ) );
			}
			$wpdb->update(
				$registrations_table,
				array(
					'status' => 'expired',
					'reserved_until' => null,
					'updated_at' => current_time( 'mysql' ),
				),
				array( 'id' => $registration_id ),
				array( '%s', '%s', '%s' ),
				array( '%d' )
			);
			$wpdb->query( 'COMMIT' );
			$released++;
			cer_send_registration_release_notifications( $registration_id );
		} catch ( Exception $e ) {
			$wpdb->query( 'ROLLBACK' );
			error_log( 'CER expired registration release failed: ' . $e->getMessage() );
		}
	}

	return $released;
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
	$event = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}evt_events WHERE slug = %s AND status = %s AND deleted_at IS NULL LIMIT 1", $event_slug, 'published' ) );
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

	status_header( 200 );
	nocache_headers();
	set_query_var( 'event_slug', $event_slug );
	global $wp_query;
	if ( isset( $wp_query ) ) {
		$wp_query->is_404 = false;
		$wp_query->is_page = true;
	}

	// Rendering is left to cer_maybe_load_event_template(). Exiting here would skip the rest of
	// template_redirect, where Elementor initialises its frontend and queues the header's fonts.
}

function cer_maybe_load_event_template( $template ) {
	$event_slug = get_query_var( 'event_slug' );
	if ( empty( $event_slug ) ) {
		return $template;
	}

	$located = locate_template( 'page-templates/template-event-registration.php' );
	if ( ! $located ) {
		return $template;
	}

	add_filter( 'elementor/frontend/builder_content_data', 'cer_unwrap_event_header_container', 10, 2 );
	return $located;
}

/**
 * The event header template nests the site header's container inside an extra full-width
 * container that adds its own padding, min-height and z-index. On event pages only, render
 * the inner container as the header's top level. The saved template is not modified.
 */
function cer_unwrap_event_header_container( $data, $post_id ) {
	if ( ! is_array( $data ) || 1 !== count( $data ) || 'header' !== get_post_meta( $post_id, '_elementor_template_type', true ) ) {
		return $data;
	}

	$outer    = $data[0];
	$children = isset( $outer['elements'] ) && is_array( $outer['elements'] ) ? $outer['elements'] : array();
	if (
		'container' !== ( $outer['elType'] ?? '' ) ||
		'48909e04' !== ( $outer['id'] ?? '' ) ||
		1 !== count( $children ) ||
		'container' !== ( $children[0]['elType'] ?? '' ) ||
		'6423679' !== ( $children[0]['id'] ?? '' )
	) {
		return $data;
	}

	// Elementor's element cache is per document: a cached copy would ignore this data, and a fresh
	// render would store the unwrapped markup for every other page that uses this header. Skip the
	// cache for this one render only.
	add_filter( 'pre_option_elementor_element_cache_ttl', 'cer_disable_element_cache' );
	add_filter( 'elementor/frontend/the_content', 'cer_restore_element_cache' );

	return $children;
}

function cer_disable_element_cache() {
	return 'disable';
}

function cer_restore_element_cache( $content ) {
	remove_filter( 'pre_option_elementor_element_cache_ttl', 'cer_disable_element_cache' );
	remove_filter( 'elementor/frontend/the_content', 'cer_restore_element_cache' );
	return $content;
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

function cer_maybe_install_dashboard_trash_schema(): void {
	if ( '8' !== get_option( 'cer_schema_version', '' ) ) {
		return;
	}

	global $wpdb;
	$events_table = $wpdb->prefix . 'evt_events';
	$registrations_table = $wpdb->prefix . 'evt_registrations';

	cer_add_column_if_missing( $events_table, 'deleted_at', 'DATETIME NULL', 'status' );
	cer_add_column_if_missing( $registrations_table, 'deleted_at', 'DATETIME NULL', 'status' );
	cer_add_index_if_missing( $events_table, 'deleted_at', 'deleted_at' );
	cer_add_index_if_missing( $registrations_table, 'deleted_at', 'deleted_at' );
	update_option( 'cer_schema_version', '9', false );
}

function cer_maybe_install_event_item_trash_schema(): void {
	if ( '9' !== get_option( 'cer_schema_version', '' ) ) {
		return;
	}

	global $wpdb;
	$tables = array(
		$wpdb->prefix . 'evt_ticket_types',
		$wpdb->prefix . 'evt_speakers',
		$wpdb->prefix . 'evt_sponsorships',
		$wpdb->prefix . 'evt_pillars',
	);

	foreach ( $tables as $table ) {
		cer_add_column_if_missing( $table, 'deleted_at', 'DATETIME NULL', 'updated_at' );
		cer_add_index_if_missing( $table, 'deleted_at', 'deleted_at' );
		$index_exists = $wpdb->get_var( $wpdb->prepare( "SHOW INDEX FROM {$table} WHERE Key_name = %s", 'deleted_at' ) );
		if ( ! cer_column_exists( $table, 'deleted_at' ) || ! $index_exists ) {
			error_log( sprintf( 'CER schema migration incomplete for %s.deleted_at; schema version remains 9.', $table ) );
			return;
		}
	}

	update_option( 'cer_schema_version', '10', false );
}

function cer_maybe_install_ticket_maintenance_schema(): void {
	if ( '1' === get_option( 'cer_ticket_maintenance_schema_version', '' ) ) {
		return;
	}

	global $wpdb;
	$table = $wpdb->prefix . 'evt_ticket_types';
	cer_add_column_if_missing( $table, 'is_maintenance', 'TINYINT(1) NOT NULL DEFAULT 0', 'quantity_sold' );
	if ( ! cer_column_exists( $table, 'is_maintenance' ) ) {
		error_log( 'CER ticket maintenance schema migration is incomplete; maintenance status remains unavailable.' );
		return;
	}
	update_option( 'cer_ticket_maintenance_schema_version', '1', false );
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
		deleted_at DATETIME NULL,
		show_speakers TINYINT(1) NOT NULL DEFAULT 1,
		show_sponsors TINYINT(1) NOT NULL DEFAULT 1,
		show_pillars TINYINT(1) NOT NULL DEFAULT 1,
		max_attendees INT UNSIGNED,
		brand_color_1 VARCHAR(20) NULL,
		brand_color_2 VARCHAR(20) NULL,
		background_image_id BIGINT(20) UNSIGNED,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		UNIQUE KEY event_uuid (event_uuid),
		UNIQUE KEY slug (slug),
		KEY status (status),
		KEY event_date (event_date),
		KEY deleted_at (deleted_at)
	) $charset_collate;";

	$sql_ticket_types = "CREATE TABLE IF NOT EXISTS $ticket_types_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		name VARCHAR(255) NOT NULL,
		price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
		currency VARCHAR(3) NOT NULL DEFAULT 'KES',
		quantity_available INT UNSIGNED,
		quantity_sold INT UNSIGNED DEFAULT 0,
		is_maintenance TINYINT(1) NOT NULL DEFAULT 0,
		description TEXT,
		order_index INT UNSIGNED DEFAULT 0,
		deleted_at DATETIME NULL,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		KEY deleted_at (deleted_at),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	$sql_speakers = "CREATE TABLE IF NOT EXISTS $speakers_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		name VARCHAR(255) NOT NULL,
		role VARCHAR(255),
		bio TEXT,
		photo_id BIGINT(20) UNSIGNED,
		order_index INT UNSIGNED DEFAULT 0,
		deleted_at DATETIME NULL,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		KEY deleted_at (deleted_at),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	$sql_sponsorships = "CREATE TABLE IF NOT EXISTS $sponsorships_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		name VARCHAR(255) NOT NULL,
		description TEXT,
		benefits LONGTEXT,
		cta_url VARCHAR(255),
		order_index INT UNSIGNED DEFAULT 0,
		deleted_at DATETIME NULL,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		KEY deleted_at (deleted_at),
		FOREIGN KEY (event_id) REFERENCES $events_table(id) ON DELETE CASCADE
	) $charset_collate;";

	$sql_pillars = "CREATE TABLE IF NOT EXISTS $pillars_table (
		id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		event_id BIGINT(20) UNSIGNED NOT NULL,
		title VARCHAR(255) NOT NULL,
		description LONGTEXT,
		icon VARCHAR(255),
		order_index INT UNSIGNED DEFAULT 0,
		deleted_at DATETIME NULL,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		KEY deleted_at (deleted_at),
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
		url VARCHAR(500),
		tel_no VARCHAR(50),
		email VARCHAR(255),
		address VARCHAR(255),
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
		attendee_title VARCHAR(512) NULL,
		organization VARCHAR(1024) NULL,
		attendee_role VARCHAR(1024) NULL,
		full_name_search_hash CHAR(64) NULL,
		payer_name VARCHAR(512) NULL,
		email VARCHAR(255) NOT NULL,
		email_search_hash CHAR(64) NULL,
		phone VARCHAR(50) NOT NULL,
		phone_search_hash CHAR(64) NULL,
		county VARCHAR(512) NULL,
		age_group VARCHAR(128) NULL,
		gender VARCHAR(128) NULL,
		accessibility_support VARCHAR(512) NULL,
		accessibility_details LONGTEXT NULL,
		ticket_type VARCHAR(100),
		payment_method VARCHAR(50) NOT NULL,
		amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
		confirmed_amount DECIMAL(10,2) NULL,
		notes TEXT,
		receipt_number VARCHAR(255) NULL,
		serial_number VARCHAR(255) NULL,
		confirmation_code VARCHAR(255) NULL,
		status VARCHAR(50) NOT NULL DEFAULT 'pending',
		deleted_at DATETIME NULL,
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
		KEY deleted_at (deleted_at),
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
	cer_add_column_if_missing( $events_table, 'secondary_logo_id', 'BIGINT(20) UNSIGNED NULL', 'max_attendees' );
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
	cer_add_column_if_missing( $events_table, 'mail_sender_email', 'VARCHAR(255) NULL', 'location_address' );
	cer_add_column_if_missing( $events_table, 'mail_password_encrypted', 'TEXT NULL', 'mail_sender_email' );
	cer_add_column_if_missing( $events_table, 'mail_smtp_host', 'VARCHAR(255) NULL', 'mail_password_encrypted' );
	cer_add_column_if_missing( $events_table, 'mail_smtp_port', 'VARCHAR(10) NULL', 'mail_smtp_host' );
	cer_add_column_if_missing( $events_table, 'mail_smtp_secure', 'VARCHAR(20) NULL', 'mail_smtp_port' );
	cer_add_column_if_missing( $events_table, 'mail_from_name', 'VARCHAR(255) NULL', 'mail_smtp_secure' );
	cer_add_column_if_missing( $events_table, 'mail_notification_email', 'VARCHAR(255) NULL', 'mail_from_name' );
	cer_add_column_if_missing( $partners_table, 'url', 'VARCHAR(500) NULL', 'logo_id' );
	cer_add_column_if_missing( $partners_table, 'tel_no', 'VARCHAR(50) NULL', 'url' );
	cer_add_column_if_missing( $partners_table, 'email', 'VARCHAR(255) NULL', 'tel_no' );
	cer_add_column_if_missing( $partners_table, 'address', 'VARCHAR(255) NULL', 'email' );
	if ( cer_column_exists( $partners_table, 'link_url' ) && ! cer_column_exists( $partners_table, 'url' ) ) {
		$wpdb->query( "ALTER TABLE {$partners_table} CHANGE link_url url VARCHAR(500) NULL" );
	}
	if ( cer_column_exists( $partners_table, 'link_url' ) && cer_column_exists( $partners_table, 'url' ) ) {
		$wpdb->query( "UPDATE {$partners_table} SET url = IFNULL(url, link_url) WHERE url IS NULL OR url = ''" );
		$wpdb->query( "ALTER TABLE {$partners_table} DROP COLUMN link_url" );
	}
	cer_add_index_if_missing( $events_table, 'event_date', 'event_date' );
	cer_add_index_if_missing( $registrations_table, 'created_at', 'created_at' );
	cer_add_index_if_missing( $registrations_table, 'status', 'status' );
	cer_add_index_if_missing( $registrations_table, 'payment_method', 'payment_method' );
	cer_add_index_if_missing( $registrations_table, 'event_status_created', 'event_id, status, created_at' );
}

function cer_deactivate_plugin() {
	wp_clear_scheduled_hook( 'cer_purge_expired_event_trash' );
	cer_clear_event_notifications();
	flush_rewrite_rules();
}

function cer_dashboard_trash_cutoff(): string {
	return wp_date( 'Y-m-d H:i:s', time() - ( 30 * DAY_IN_SECONDS ) );
}

function cer_dashboard_trash_table( string $record_type ) {
	global $wpdb;

	$tables = array(
		'event' => $wpdb->prefix . 'evt_events',
		'registration' => $wpdb->prefix . 'evt_registrations',
	);

	return $tables[ $record_type ] ?? new WP_Error( 'cer_invalid_trash_type', __( 'That record type cannot be moved to trash.', 'custom-event-registration' ) );
}

function cer_trash_dashboard_record( string $record_type, int $record_id ) {
	global $wpdb;

	$table = cer_dashboard_trash_table( $record_type );
	if ( is_wp_error( $table ) ) {
		return $table;
	}

	$record = $wpdb->get_row( $wpdb->prepare( "SELECT id, deleted_at FROM {$table} WHERE id = %d LIMIT 1", $record_id ), ARRAY_A );
	if ( ! $record || ! empty( $record['deleted_at'] ) ) {
		return new WP_Error( 'cer_record_not_active', __( 'The selected record is no longer active.', 'custom-event-registration' ) );
	}

	$now = current_time( 'mysql' );
	$updated = $wpdb->query(
		$wpdb->prepare(
			"UPDATE {$table} SET deleted_at = %s, updated_at = %s WHERE id = %d AND deleted_at IS NULL",
			$now,
			$now,
			$record_id
		)
	);
	if ( false === $updated ) {
		return new WP_Error( 'cer_trash_failed', __( 'The record could not be moved to trash. Please try again.', 'custom-event-registration' ) . ' ' . $wpdb->last_error );
	}
	if ( 0 === $updated ) {
		return new WP_Error( 'cer_record_not_active', __( 'The selected record is no longer active.', 'custom-event-registration' ) );
	}

	cer_invalidate_dashboard_trash_caches( $record_type );
	return true;
}

function cer_restore_dashboard_record( string $record_type, int $record_id ) {
	global $wpdb;

	$table = cer_dashboard_trash_table( $record_type );
	if ( is_wp_error( $table ) ) {
		return $table;
	}

	$record = $wpdb->get_row( $wpdb->prepare( "SELECT id, deleted_at FROM {$table} WHERE id = %d LIMIT 1", $record_id ), ARRAY_A );
	if ( ! $record || empty( $record['deleted_at'] ) ) {
		return new WP_Error( 'cer_record_not_trashed', __( 'The selected record is not in the trash.', 'custom-event-registration' ) );
	}
	if ( $record['deleted_at'] < cer_dashboard_trash_cutoff() ) {
		return new WP_Error( 'cer_trash_expired', __( 'This record has been in the trash for more than 30 days and can no longer be restored.', 'custom-event-registration' ) );
	}

	$updated = $wpdb->query(
		$wpdb->prepare(
			"UPDATE {$table} SET deleted_at = NULL, updated_at = %s WHERE id = %d AND deleted_at = %s",
			current_time( 'mysql' ),
			$record_id,
			$record['deleted_at']
		)
	);
	if ( false === $updated ) {
		return new WP_Error( 'cer_restore_failed', __( 'The record could not be restored. Please try again.', 'custom-event-registration' ) . ' ' . $wpdb->last_error );
	}
	if ( 0 === $updated ) {
		return new WP_Error( 'cer_record_not_trashed', __( 'The selected record is no longer in the trash.', 'custom-event-registration' ) );
	}

	cer_invalidate_dashboard_trash_caches( $record_type );
	return true;
}

function cer_event_item_config( string $item_type ) {
	global $wpdb;

	$item_types = array(
		'tickets' => array( 'table' => $wpdb->prefix . 'evt_ticket_types', 'label' => __( 'ticket type', 'custom-event-registration' ), 'name_field' => 'name' ),
		'speakers' => array( 'table' => $wpdb->prefix . 'evt_speakers', 'label' => __( 'speaker', 'custom-event-registration' ), 'name_field' => 'name' ),
		'sponsors' => array( 'table' => $wpdb->prefix . 'evt_sponsorships', 'label' => __( 'sponsorship package', 'custom-event-registration' ), 'name_field' => 'name' ),
		'pillars' => array( 'table' => $wpdb->prefix . 'evt_pillars', 'label' => __( 'thematic pillar', 'custom-event-registration' ), 'name_field' => 'title' ),
	);

	return $item_types[ $item_type ] ?? new WP_Error( 'cer_invalid_event_item_type', __( 'That event item type is not supported.', 'custom-event-registration' ) );
}

function cer_bump_event_item_cache_version(): void {
	update_option( 'cer_event_cache_version', (int) get_option( 'cer_event_cache_version', 1 ) + 1, false );
	delete_transient( 'cer_dashboard_metrics' );
}

function cer_trash_event_item( string $item_type, int $item_id, int $event_id ) {
	global $wpdb;

	$config = cer_event_item_config( $item_type );
	if ( is_wp_error( $config ) ) {
		return $config;
	}
	$event = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}evt_events WHERE id = %d AND deleted_at IS NULL", $event_id ) );
	if ( ! $event ) {
		return new WP_Error( 'cer_event_item_parent_inactive', __( 'Items can only be trashed while their parent event is active.', 'custom-event-registration' ) );
	}

	$item = $wpdb->get_row( $wpdb->prepare( "SELECT id, deleted_at FROM {$config['table']} WHERE id = %d AND event_id = %d", $item_id, $event_id ), ARRAY_A );
	if ( ! $item || ! empty( $item['deleted_at'] ) ) {
		return new WP_Error( 'cer_event_item_not_active', __( 'The selected item is no longer active.', 'custom-event-registration' ) );
	}

	$now = current_time( 'mysql' );
	$updated = $wpdb->update(
		$config['table'],
		array( 'deleted_at' => $now, 'updated_at' => $now ),
		array( 'id' => $item_id, 'event_id' => $event_id, 'deleted_at' => null ),
		array( '%s', '%s' ),
		array( '%d', '%d', '%s' )
	);
	if ( false === $updated ) {
		return new WP_Error( 'cer_event_item_trash_failed', __( 'The item could not be moved to Trash.', 'custom-event-registration' ) . ' ' . $wpdb->last_error );
	}
	if ( 0 === $updated ) {
		return new WP_Error( 'cer_event_item_not_active', __( 'The selected item is no longer active.', 'custom-event-registration' ) );
	}

	cer_bump_event_item_cache_version();
	return true;
}

function cer_restore_event_item( string $item_type, int $item_id, int $event_id ) {
	global $wpdb;

	$config = cer_event_item_config( $item_type );
	if ( is_wp_error( $config ) ) {
		return $config;
	}
	$event = $wpdb->get_row( $wpdb->prepare( "SELECT id, deleted_at FROM {$wpdb->prefix}evt_events WHERE id = %d", $event_id ), ARRAY_A );
	if ( ! $event ) {
		return new WP_Error( 'cer_event_item_parent_missing', __( 'The parent event no longer exists, so this item cannot be restored.', 'custom-event-registration' ) );
	}
	if ( ! empty( $event['deleted_at'] ) ) {
		return new WP_Error( 'cer_event_item_parent_trashed', __( 'Restore the parent event before restoring this item.', 'custom-event-registration' ) );
	}

	$item = $wpdb->get_row( $wpdb->prepare( "SELECT id, deleted_at FROM {$config['table']} WHERE id = %d AND event_id = %d", $item_id, $event_id ), ARRAY_A );
	if ( ! $item || empty( $item['deleted_at'] ) ) {
		return new WP_Error( 'cer_event_item_not_trashed', __( 'The selected item is not in this event’s Trash.', 'custom-event-registration' ) );
	}
	if ( $item['deleted_at'] < cer_dashboard_trash_cutoff() ) {
		return new WP_Error( 'cer_event_item_trash_expired', __( 'This item has been in Trash for more than 30 days and can no longer be restored.', 'custom-event-registration' ) );
	}

	$updated = $wpdb->update(
		$config['table'],
		array( 'deleted_at' => null, 'updated_at' => current_time( 'mysql' ) ),
		array( 'id' => $item_id, 'event_id' => $event_id, 'deleted_at' => $item['deleted_at'] ),
		array( '%s', '%s' ),
		array( '%d', '%d', '%s' )
	);
	if ( false === $updated ) {
		return new WP_Error( 'cer_event_item_restore_failed', __( 'The item could not be restored.', 'custom-event-registration' ) . ' ' . $wpdb->last_error );
	}
	if ( 0 === $updated ) {
		return new WP_Error( 'cer_event_item_not_trashed', __( 'The selected item is no longer in Trash.', 'custom-event-registration' ) );
	}

	cer_bump_event_item_cache_version();
	return true;
}

function cer_permanently_delete_event_item( string $item_type, int $item_id, int $event_id ) {
	global $wpdb;

	$config = cer_event_item_config( $item_type );
	if ( is_wp_error( $config ) ) {
		return $config;
	}
	$item = $wpdb->get_row( $wpdb->prepare( "SELECT id, deleted_at FROM {$config['table']} WHERE id = %d AND event_id = %d", $item_id, $event_id ), ARRAY_A );
	if ( ! $item || empty( $item['deleted_at'] ) ) {
		return new WP_Error( 'cer_event_item_not_trashed', __( 'Only items in Trash can be permanently deleted.', 'custom-event-registration' ) );
	}
	if ( 'tickets' === $item_type ) {
		$linked_registrations = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}evt_registrations WHERE ticket_type_id = %d", $item_id ) );
		if ( $linked_registrations ) {
			return new WP_Error( 'cer_ticket_has_history', __( 'This ticket has registration history and cannot be permanently deleted. Keep it in Trash to preserve those links.', 'custom-event-registration' ) );
		}
	}

	$deleted = $wpdb->delete( $config['table'], array( 'id' => $item_id, 'event_id' => $event_id ), array( '%d', '%d' ) );
	if ( false === $deleted ) {
		return new WP_Error( 'cer_event_item_delete_failed', __( 'The item could not be permanently deleted.', 'custom-event-registration' ) . ' ' . $wpdb->last_error );
	}
	if ( 0 === $deleted ) {
		return new WP_Error( 'cer_event_item_not_trashed', __( 'The selected item is no longer in Trash.', 'custom-event-registration' ) );
	}

	cer_bump_event_item_cache_version();
	return true;
}

function cer_render_event_item_trash_list( int $event_id, string $item_type ): void {
	global $wpdb;

	$config = cer_event_item_config( $item_type );
	if ( is_wp_error( $config ) ) {
		return;
	}

	$search = isset( $_GET['item_search'] ) ? sanitize_text_field( wp_unslash( $_GET['item_search'] ) ) : '';
	$per_page = 5;
	$total_where = 'event_id = %d AND deleted_at IS NOT NULL AND deleted_at >= %s';
	$bindings = array( $event_id, cer_dashboard_trash_cutoff() );
	if ( '' !== $search ) {
		$total_where .= " AND {$config['name_field']} LIKE %s";
		$bindings[] = '%' . $wpdb->esc_like( $search ) . '%';
	}
	$total_items = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$config['table']} WHERE {$total_where}", $bindings ) );
	if ( $wpdb->last_error ) {
		error_log( 'CER item Trash filtering count failed: ' . $wpdb->last_error );
		wp_die( esc_html__( 'Unable to load trashed items. Please try again.', 'custom-event-registration' ), '', array( 'response' => 500 ) );
	}
	$total_pages = max( 1, (int) ceil( $total_items / $per_page ) );
	$page = isset( $_GET['item_paged'] ) ? min( $total_pages, max( 1, absint( wp_unslash( $_GET['item_paged'] ) ) ) ) : 1;
	$bindings[] = $per_page;
	$bindings[] = ( $page - 1 ) * $per_page;
	$items = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT id, {$config['name_field']} AS item_name, deleted_at FROM {$config['table']}
			WHERE {$total_where}
			ORDER BY deleted_at DESC, id DESC
			LIMIT %d OFFSET %d",
			$bindings
		),
		ARRAY_A
	);
	if ( $wpdb->last_error ) {
		error_log( 'CER item Trash filtering failed: ' . $wpdb->last_error );
		wp_die( esc_html__( 'Unable to load trashed items. Please try again.', 'custom-event-registration' ), '', array( 'response' => 500 ) );
	}

	$active_tab_by_type = array( 'tickets' => 'setup', 'speakers' => 'program', 'sponsors' => 'partners', 'pillars' => 'program' );
	$base_args = array(
		'page' => 'cer-event-new',
		'event_id' => $event_id,
		'item_type' => $item_type,
		'item_view' => 'trash',
		'cer_event_active_tab' => $active_tab_by_type[ $item_type ],
		'item_search' => $search,
	);
	?>
	<div class="cer-item-trash-view" data-cer-live-list="items-<?php echo esc_attr( $item_type ); ?>">
		<div class="cer-item-trash-search" data-cer-live-filters data-cer-filter-url="<?php echo esc_url( add_query_arg( $base_args, admin_url( 'admin.php' ) ) ); ?>">
			<label for="cer-item-trash-search-input"><?php esc_html_e( 'Search trashed items', 'custom-event-registration' ); ?></label>
			<input id="cer-item-trash-search-input" name="item_search" type="search" value="<?php echo esc_attr( $search ); ?>" />
			<button type="button" class="button cer-item-trash-search-submit"><?php esc_html_e( 'Search', 'custom-event-registration' ); ?></button>
		</div>
		<div data-cer-live-results>
		<p class="description"><?php printf( esc_html__( '%s matching trashed items', 'custom-event-registration' ), esc_html( number_format_i18n( $total_items ) ) ); ?></p>
		<p class="description"><?php esc_html_e( 'Items remain recoverable for 30 days. Expired items are no longer listed or restorable.', 'custom-event-registration' ); ?></p>
		<div class="cer-table-wrap" tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Trashed items table, scroll to see all columns', 'custom-event-registration' ); ?>">
			<table class="widefat striped cer-item-trash-table">
				<thead><tr><th><?php esc_html_e( 'Removed item', 'custom-event-registration' ); ?></th><th><?php esc_html_e( 'Deleted', 'custom-event-registration' ); ?></th><th><?php esc_html_e( 'Actions', 'custom-event-registration' ); ?></th></tr></thead>
				<tbody>
					<?php if ( $items ) : ?>
						<?php foreach ( $items as $item ) : ?>
							<tr>
								<td><?php echo esc_html( $item['item_name'] ?: sprintf( __( '%1$s #%2$d', 'custom-event-registration' ), ucfirst( $config['label'] ), (int) $item['id'] ) ); ?></td>
								<td><?php echo esc_html( date_i18n( 'M j, Y H:i', strtotime( $item['deleted_at'] ) ) ); ?></td>
								<td>
									<?php
									$restore_url = add_query_arg( array_merge( $base_args, array( 'cer_action' => 'restore_event_item', 'item_id' => (int) $item['id'], 'item_paged' => $page ) ), admin_url( 'admin.php' ) );
									$delete_url = add_query_arg( array_merge( $base_args, array( 'cer_action' => 'delete_event_item_permanently', 'item_id' => (int) $item['id'], 'item_paged' => $page ) ), admin_url( 'admin.php' ) );
									?>
									<a class="button button-small" href="<?php echo esc_url( wp_nonce_url( $restore_url, 'restore_event_item_' . $item_type . '_' . (int) $item['id'] . '_' . $event_id ) ); ?>"><?php esc_html_e( 'Restore', 'custom-event-registration' ); ?></a>
									<a class="button button-small cer-item-action-confirm" data-confirm-kind="delete" href="<?php echo esc_url( wp_nonce_url( $delete_url, 'delete_event_item_permanently_' . $item_type . '_' . (int) $item['id'] . '_' . $event_id ) ); ?>" data-confirm="<?php echo esc_attr__( 'Permanently delete this item? This cannot be undone. Ticket types with registration history cannot be deleted.', 'custom-event-registration' ); ?>"><?php esc_html_e( 'Delete permanently', 'custom-event-registration' ); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr><td colspan="3"><?php esc_html_e( 'No items in Trash during the 30-day recovery period.', 'custom-event-registration' ); ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php cer_render_admin_pagination( $page, $total_pages, add_query_arg( $base_args, admin_url( 'admin.php' ) ), 'item_paged', sprintf( __( '%s trash pages', 'custom-event-registration' ), ucfirst( $config['label'] ) ) ); ?>
		</div>
	</div>
	<?php
}

function cer_permanently_delete_dashboard_event( int $event_id, bool $expired_only = false ) {
	global $wpdb;

	$events_table = $wpdb->prefix . 'evt_events';
	$registrations_table = $wpdb->prefix . 'evt_registrations';
	$ticket_types_table = $wpdb->prefix . 'evt_ticket_types';
	$event = $wpdb->get_row(
		$wpdb->prepare( "SELECT id, deleted_at FROM {$events_table} WHERE id = %d LIMIT 1", $event_id ),
		ARRAY_A
	);
	if ( ! $event || empty( $event['deleted_at'] ) ) {
		return new WP_Error( 'cer_event_not_trashed', __( 'Only an event in the trash can be permanently deleted.', 'custom-event-registration' ) );
	}
	if ( $expired_only && $event['deleted_at'] >= cer_dashboard_trash_cutoff() ) {
		return new WP_Error( 'cer_event_trash_not_expired', __( 'The event is still within its 30-day recovery period.', 'custom-event-registration' ) );
	}
	if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
		return new WP_Error( 'cer_event_delete_failed', __( 'The event could not be permanently deleted. Please try again.', 'custom-event-registration' ) . ' ' . $wpdb->last_error );
	}

	$detached = $wpdb->query(
		$wpdb->prepare(
			"UPDATE {$registrations_table}
			SET event_id = NULL, ticket_type_id = NULL
			WHERE event_id = %d
				OR ticket_type_id IN (SELECT id FROM {$ticket_types_table} WHERE event_id = %d)",
			$event_id,
			$event_id
		)
	);
	if ( false === $detached ) {
		$wpdb->query( 'ROLLBACK' );
		return new WP_Error( 'cer_event_delete_failed', __( 'Registration history could not be detached from the event.', 'custom-event-registration' ) . ' ' . $wpdb->last_error );
	}

	$child_tables = array(
		$ticket_types_table,
		$wpdb->prefix . 'evt_speakers',
		$wpdb->prefix . 'evt_sponsorships',
		$wpdb->prefix . 'evt_pillars',
		$wpdb->prefix . 'evt_objectives',
		$wpdb->prefix . 'evt_summit_structure',
		$wpdb->prefix . 'evt_partners',
		$wpdb->prefix . 'evt_faqs',
	);
	foreach ( $child_tables as $child_table ) {
		if ( false === $wpdb->delete( $child_table, array( 'event_id' => $event_id ), array( '%d' ) ) ) {
			$wpdb->query( 'ROLLBACK' );
			return new WP_Error( 'cer_event_delete_failed', __( 'The event content could not be permanently deleted.', 'custom-event-registration' ) . ' ' . $wpdb->last_error );
		}
	}

	$deleted = $wpdb->delete(
		$events_table,
		array(
			'id' => $event_id,
			'deleted_at' => $event['deleted_at'],
		),
		array( '%d', '%s' )
	);
	if ( false === $deleted || 0 === $deleted ) {
		$wpdb->query( 'ROLLBACK' );
		return new WP_Error( 'cer_event_delete_failed', __( 'The event could not be permanently deleted. It may have changed; refresh and try again.', 'custom-event-registration' ) . ' ' . $wpdb->last_error );
	}

	if ( false === $wpdb->query( 'COMMIT' ) ) {
		$wpdb->query( 'ROLLBACK' );
		return new WP_Error( 'cer_event_delete_failed', __( 'The event deletion could not be completed. Please try again.', 'custom-event-registration' ) . ' ' . $wpdb->last_error );
	}

	cer_invalidate_dashboard_trash_caches( 'event' );
	return true;
}

function cer_purge_expired_event_trash(): int {
	global $wpdb;

	$events_table = $wpdb->prefix . 'evt_events';
	$cutoff = cer_dashboard_trash_cutoff();
	$event_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT id FROM {$events_table}
			WHERE deleted_at IS NOT NULL AND deleted_at < %s
			ORDER BY deleted_at ASC
			LIMIT %d",
			$cutoff,
			25
		)
	);
	if ( null === $event_ids ) {
		error_log( 'CER expired event trash lookup failed: ' . $wpdb->last_error );
		return 0;
	}

	$deleted_count = 0;
	foreach ( $event_ids as $event_id ) {
		$result = cer_permanently_delete_dashboard_event( (int) $event_id, true );
		if ( is_wp_error( $result ) ) {
			error_log( 'CER expired event purge failed for event ' . (int) $event_id . ': ' . $result->get_error_message() );
			continue;
		}
		$deleted_count++;
	}

	$purged_items = 0;
	$registrations_table = $wpdb->prefix . 'evt_registrations';
	foreach ( array( 'tickets', 'speakers', 'sponsors', 'pillars' ) as $item_type ) {
		$config = cer_event_item_config( $item_type );
		if ( is_wp_error( $config ) ) {
			error_log( 'CER expired item purge skipped: ' . $config->get_error_message() );
			continue;
		}

		if ( 'tickets' === $item_type ) {
			$deleted = $wpdb->query(
				$wpdb->prepare(
					"DELETE item FROM {$config['table']} item
					LEFT JOIN {$registrations_table} registration ON registration.ticket_type_id = item.id
					WHERE item.deleted_at IS NOT NULL AND item.deleted_at < %s AND registration.id IS NULL
					LIMIT %d",
					$cutoff,
					100
				)
			);
		} else {
			$deleted = $wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$config['table']}
					WHERE deleted_at IS NOT NULL AND deleted_at < %s
					LIMIT %d",
					$cutoff,
					100
				)
			);
		}

		if ( false === $deleted ) {
			error_log( sprintf( 'CER expired %s purge failed: %s', $item_type, $wpdb->last_error ) );
			continue;
		}
		$purged_items += (int) $deleted;
	}
	if ( $purged_items ) {
		cer_bump_event_item_cache_version();
		$deleted_count += $purged_items;
	}

	return $deleted_count;
}

function cer_invalidate_dashboard_trash_caches( string $record_type ): void {
	delete_transient( 'cer_dashboard_metrics' );
	if ( 'event' === $record_type ) {
		update_option( 'cer_event_cache_version', (int) get_option( 'cer_event_cache_version', 1 ) + 1, false );
	}
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
			$should_enqueue = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}evt_events WHERE slug = %s AND deleted_at IS NULL LIMIT 1", $event_slug ) );
		}
	}

	if ( $should_enqueue ) {
		if ( did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ) ) {
			add_filter( 'body_class', 'cer_add_active_elementor_kit_body_class', 20 );
		}

		wp_dequeue_script( 'tweenMax' );
		wp_deregister_script( 'tweenMax' );
		wp_dequeue_script( 'wow' );
		wp_deregister_script( 'wow' );
		wp_dequeue_script( 'appear' );
		wp_deregister_script( 'appear' );
		wp_dequeue_script( 'isotope' );
		wp_deregister_script( 'isotope' );

		wp_enqueue_style( 'dashicons' );

		$critical_css = "
			.cer-kamgc-page { font-family: 'Plus Jakarta Sans', 'Open Sans', sans-serif; color: #0b1726; }
			.cer-kamgc-shell { max-width: 1200px; margin: 0 auto; padding: 0 18px 48px; }
			.cer-kamgc-hero { position: relative; margin-top: 24px; padding: 32px 0 0; }
			.cer-kamgc-hero-inner { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(260px, 0.8fr); gap: 28px; align-items: start; }
			.cer-kamgc-hero-copy { position: relative; z-index: 1; }
			.cer-kamgc-hero-badges { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 18px; }
			.cer-kamgc-badge, .cer-kamgc-convening-badge, .cer-kamgc-activism-badge, .cer-kamgc-emblem-badge { display: inline-flex; align-items: center; gap: 8px; border-radius: 999px; background: rgba(255,255,255,.75); border: 1px solid rgba(18,63,70,.12); box-shadow: 0 16px 32px rgba(11,23,38,.08); }
			.cer-kamgc-hero-copy h1 { margin: 0 0 16px; font-size: clamp(2.4rem, 5vw, 5rem); line-height: 0.96; letter-spacing: -0.06em; }
			.cer-kamgc-hero-copy h1 span { display: block; color: #0b7a73; }
			.cer-kamgc-hero-description { max-width: 62ch; font-size: 1.05rem; line-height: 1.7; color: rgba(11,23,38,.8); }
			.cer-kamgc-feature-list { list-style: none; padding: 0; margin: 20px 0 0; display: grid; gap: 10px; }
			.cer-kamgc-feature-list li { display: flex; align-items: center; gap: 10px; font-weight: 600; color: #0d2f3c; }
			.cer-kamgc-summary-box { background: rgba(255,255,255,.8); border: 1px solid rgba(18,63,70,.12); border-radius: 22px; box-shadow: 0 18px 40px rgba(11,23,38,.08); }
			.cer-kamgc-hero-summary { padding: 22px; }
			.cer-kamgc-summary-metadata { display: grid; gap: 12px; }
			.cer-kamgc-summary-metadata > div { display: flex; gap: 12px; align-items: center; }
			.cer-kamgc-summary-metadata small { display: block; color: rgba(11,23,38,.62); font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; }
			.cer-kamgc-summary-metadata strong { display: block; font-size: 1rem; }
			.cer-kamgc-actions { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 24px; }
			.cer-kamgc-button { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 48px; padding: 0 22px; border-radius: 14px; text-decoration: none; font-weight: 700; }
			.cer-kamgc-button-primary { background: linear-gradient(135deg, #0b7a73, #0f9d8b); color: white; }
			.cer-kamgc-button-secondary { background: white; border: 1px solid rgba(11,23,38,.12); color: #0b1726; }
			@media (max-width: 900px) { .cer-kamgc-hero-inner { grid-template-columns: 1fr; } }
		";

		wp_register_style( 'cer-event-critical', false, array(), null );
		wp_add_inline_style( 'cer-event-critical', $critical_css );
		wp_enqueue_style( 'cer-event-critical' );
		wp_enqueue_style( 'cer-event-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:wght@400;500;600;700&display=swap', array(), null );
		wp_enqueue_style( 'cer-material-symbols', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap', array(), null );
		wp_enqueue_style( 'cer-event-registration', CER_PLUGIN_URL . 'assets/css/cer-event.css', array( 'cer-event-fonts', 'cer-material-symbols' ), filemtime( CER_PLUGIN_DIR . 'assets/css/cer-event.css' ) );
		wp_enqueue_style( 'cer-aos', 'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css', array(), '2.3.4' );
		wp_enqueue_script( 'cer-aos', 'https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js', array(), '2.3.4', true );
		wp_enqueue_script( 'cer-paystack-inline', 'https://js.paystack.co/v2/inline.js', array(), null, true );
		wp_enqueue_script( 'cer-event-registration', CER_PLUGIN_URL . 'assets/js/cer-registration.js', array( 'cer-paystack-inline', 'cer-aos' ), filemtime( CER_PLUGIN_DIR . 'assets/js/cer-registration.js' ), true );
		wp_localize_script( 'cer-event-registration', 'cerRegistrationSettings', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'rest_url' => rest_url( 'cer/v1/register' ),
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

function cer_add_active_elementor_kit_body_class( array $classes ): array {
	if ( ! did_action( 'elementor/loaded' ) || ! class_exists( '\Elementor\Plugin' ) ) {
		return $classes;
	}

	$kit = \Elementor\Plugin::$instance->kits_manager->get_kit_for_frontend();
	if ( $kit ) {
		$kit_class = 'elementor-kit-' . $kit->get_main_id();
		if ( ! in_array( $kit_class, $classes, true ) ) {
			$classes[] = $kit_class;
		}
	}

	return $classes;
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
	if ( in_array( $page, array( 'cer-events', 'cer-registrations', 'cer-event-new' ), true ) ) {
		wp_enqueue_script( 'cer-admin-list-controls', CER_PLUGIN_URL . 'assets/js/cer-admin-list-controls.js', array( 'jquery' ), filemtime( CER_PLUGIN_DIR . 'assets/js/cer-admin-list-controls.js' ), true );
		wp_localize_script( 'cer-admin-list-controls', 'cerAdminListStrings', array(
			'selected' => __( 'selected', 'custom-event-registration' ),
			'loading' => __( 'Updating results…', 'custom-event-registration' ),
			'updated' => __( 'Results updated.', 'custom-event-registration' ),
			'error' => __( 'Could not update results. The previous results are disabled. Retry Filter/Search or reset the filters.', 'custom-event-registration' ),
			'reset' => __( 'Reset filters', 'custom-event-registration' ),
		) );
	}
	if ( 'cer-dashboard' === $page ) {
		wp_enqueue_script( 'cer-admin-dashboard', CER_PLUGIN_URL . 'assets/js/cer-admin-dashboard.js', array(), filemtime( CER_PLUGIN_DIR . 'assets/js/cer-admin-dashboard.js' ), true );
		wp_localize_script(
			'cer-admin-dashboard',
			'cerDashboardMetrics',
			array(
				'url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'cer_dashboard_metrics' ),
				'loading' => __( 'Loading event metrics…', 'custom-event-registration' ),
				'updated' => __( 'Event metrics updated.', 'custom-event-registration' ),
				'error' => __( 'Could not load event metrics. Please try again or reload the page.', 'custom-event-registration' ),
			)
		);
	}
	if ( 'cer-events' === $page ) {
		wp_enqueue_script( 'cer-admin-events', CER_PLUGIN_URL . 'assets/js/cer-admin-events.js', array( 'jquery' ), filemtime( CER_PLUGIN_DIR . 'assets/js/cer-admin-events.js' ), true );
	}

	if ( 'cer-event-new' === $page ) {
		wp_enqueue_media();
		wp_enqueue_style( 'cer-flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css', array(), '4.6.13' );
		wp_enqueue_script( 'cer-flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js', array( 'jquery' ), '4.6.13', true );
		wp_enqueue_script( 'cer-admin-event-form', CER_PLUGIN_URL . 'assets/js/cer-admin-event-form.js', array( 'jquery', 'cer-flatpickr' ), filemtime( CER_PLUGIN_DIR . 'assets/js/cer-admin-event-form.js' ), true );
		wp_localize_script(
			'cer-admin-event-form',
			'cerAdminEventFormStrings',
			array(
				'tabs' => array(
					'setup' => __( 'Event Setup', 'custom-event-registration' ),
					'content' => __( 'Page Content', 'custom-event-registration' ),
					'program' => __( 'Program', 'custom-event-registration' ),
					'partners' => __( 'Partners & Sponsors', 'custom-event-registration' ),
					'registration' => __( 'Registration & FAQ', 'custom-event-registration' ),
					'appearance' => __( 'Branding & Location', 'custom-event-registration' ),
					'mail' => __( 'Email', 'custom-event-registration' ),
				),
				'panels' => array(
					'identity' => __( 'Event Identity & Publishing', 'custom-event-registration' ),
					'content' => __( 'Page Content & Search Preview', 'custom-event-registration' ),
					'appearance' => __( 'Branding & Media', 'custom-event-registration' ),
				),
				'expandField' => __( 'Edit in expanded view', 'custom-event-registration' ),
				'expandRow' => __( 'Edit row', 'custom-event-registration' ),
				'editorTitle' => __( 'Expanded editor', 'custom-event-registration' ),
				'apply' => __( 'Apply', 'custom-event-registration' ),
				'cancel' => __( 'Cancel', 'custom-event-registration' ),
				'toggleSection' => __( 'Toggle section', 'custom-event-registration' ),
				'eventSections' => __( 'Event sections', 'custom-event-registration' ),
				'noMedia' => __( 'No image selected.', 'custom-event-registration' ),
				'mediaSelected' => __( 'Image selected. Save the event to apply this change.', 'custom-event-registration' ),
				'mediaCleared' => __( 'Image cleared. Save the event to apply this change.', 'custom-event-registration' ),
				'locationApplied' => __( 'Location updated. Save the event to apply this change.', 'custom-event-registration' ),
				'contentGroups' => array(
					'information' => __( 'Event information', 'custom-event-registration' ),
					'registration' => __( 'Registration', 'custom-event-registration' ),
					'program' => __( 'Program headings', 'custom-event-registration' ),
					'partners' => __( 'Partners & FAQ', 'custom-event-registration' ),
					'description' => __( 'Description & audience', 'custom-event-registration' ),
					'hero' => __( 'Hero content', 'custom-event-registration' ),
					'search' => __( 'Search preview', 'custom-event-registration' ),
				),
			)
		);
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
		deleted_at DATETIME NULL,
		max_attendees INT UNSIGNED,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		UNIQUE KEY event_uuid (event_uuid),
		UNIQUE KEY slug (slug),
		KEY status (status),
		KEY deleted_at (deleted_at)
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
		is_maintenance TINYINT(1) NOT NULL DEFAULT 0,
		description TEXT,
		order_index INT UNSIGNED DEFAULT 0,
		deleted_at DATETIME NULL,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		KEY deleted_at (deleted_at),
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
		deleted_at DATETIME NULL,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		KEY deleted_at (deleted_at),
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
		deleted_at DATETIME NULL,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY event_id (event_id),
		KEY deleted_at (deleted_at),
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
		attendee_title VARCHAR(512) NULL,
		organization VARCHAR(1024) NULL,
		attendee_role VARCHAR(1024) NULL,
		email VARCHAR(255) NOT NULL,
		phone VARCHAR(50) NOT NULL,
		county VARCHAR(512) NULL,
		age_group VARCHAR(128) NULL,
		gender VARCHAR(128) NULL,
		accessibility_support VARCHAR(512) NULL,
		accessibility_details LONGTEXT NULL,
		ticket_type VARCHAR(100),
		payment_method VARCHAR(50) NOT NULL,
		amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
		notes TEXT,
		status VARCHAR(50) NOT NULL DEFAULT 'pending',
		deleted_at DATETIME NULL,
		created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		UNIQUE KEY registration_uuid (registration_uuid),
		UNIQUE KEY payment_uuid (payment_uuid),
		KEY event_id (event_id),
		KEY ticket_type_id (ticket_type_id),
		KEY deleted_at (deleted_at),
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

	if ( 'cer-registrations' === wp_unslash( $_GET['page'] ?? '' ) && '1' === wp_unslash( $_GET['cer_export'] ?? '' ) ) {
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['cer_export_nonce'] ?? '' ) ), 'cer_export_registrations' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'custom-event-registration' ) );
		}
		// Reuse the export renderer before WordPress emits its admin HTML.
		cer_render_registrations_page();
		exit;
	}

	if ( ! isset( $_GET['cer_action'] ) ) {
		return;
	}

	$action = sanitize_text_field( wp_unslash( $_GET['cer_action'] ) );
	$registration_id = isset( $_GET['registration_id'] ) ? absint( wp_unslash( $_GET['registration_id'] ) ) : 0;
	$event_id = isset( $_GET['event_id'] ) ? absint( wp_unslash( $_GET['event_id'] ) ) : 0;
	$item_id = isset( $_GET['item_id'] ) ? absint( wp_unslash( $_GET['item_id'] ) ) : 0;
	$item_type = isset( $_GET['item_type'] ) ? sanitize_key( wp_unslash( $_GET['item_type'] ) ) : '';
	$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

	if ( in_array( $action, array( 'trash_event_item', 'restore_event_item', 'delete_event_item_permanently' ), true ) ) {
		if ( ! $event_id || ! $item_id ) {
			wp_die( esc_html__( 'The event item could not be found.', 'custom-event-registration' ) );
		}
		$nonce_action = $action . '_' . $item_type . '_' . $item_id . '_' . $event_id;
		if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
			wp_die( esc_html__( 'Security check failed.', 'custom-event-registration' ) );
		}

		if ( 'trash_event_item' === $action ) {
			if ( '1' !== wp_unslash( $_GET['cer_confirm_item_trash'] ?? '' ) ) {
				wp_die( esc_html__( 'Confirm moving this item to Trash. It will be recoverable for 30 days.', 'custom-event-registration' ) );
			}
			$result = cer_trash_event_item( $item_type, $item_id, $event_id );
		} elseif ( 'restore_event_item' === $action ) {
			$result = cer_restore_event_item( $item_type, $item_id, $event_id );
		} else {
			if ( '1' !== wp_unslash( $_GET['cer_confirm_item_delete'] ?? '' ) ) {
				wp_die( esc_html__( 'Confirm permanently deleting this item. This cannot be undone.', 'custom-event-registration' ) );
			}
			$result = cer_permanently_delete_event_item( $item_type, $item_id, $event_id );
		}
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page' => 'cer-event-new',
					'event_id' => $event_id,
					'item_type' => $item_type,
					'item_view' => 'trash',
					'item_paged' => max( 1, absint( wp_unslash( $_GET['item_paged'] ?? 1 ) ) ),
					'item_search' => sanitize_text_field( wp_unslash( $_GET['item_search'] ?? '' ) ),
					'cer_event_active_tab' => array( 'tickets' => 'setup', 'speakers' => 'program', 'sponsors' => 'partners', 'pillars' => 'program' )[ $item_type ],
					'cer_notice' => 'updated',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	if ( 'restore_event' === $action ) {
		if ( empty( $event_id ) || ! wp_verify_nonce( $nonce, 'cer_restore_event_' . $event_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'custom-event-registration' ) );
		}
		$result = cer_restore_dashboard_record( 'event', $event_id );
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'cer-events', 'cer_notice' => 'restored' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	if ( in_array( $action, array( 'trash_event', 'delete_event_permanently' ), true ) ) {
		if ( empty( $event_id ) ) {
			wp_die( esc_html__( 'The event could not be found.', 'custom-event-registration' ) );
		}
		$nonce_action = 'trash_event' === $action ? 'cer_trash_event_' . $event_id : 'cer_delete_event_permanently_' . $event_id;
		if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
			wp_die( esc_html__( 'Security check failed.', 'custom-event-registration' ) );
		}
		if ( 'trash_event' === $action ) {
			if ( '1' !== wp_unslash( $_GET['cer_confirm_trash'] ?? '' ) ) {
				wp_die( esc_html__( 'Confirm moving this event to Trash. It will be recoverable for 30 days.', 'custom-event-registration' ) );
			}
			$result = cer_trash_dashboard_record( 'event', $event_id );
			$notice = 'trashed';
		} else {
			if ( '1' !== wp_unslash( $_GET['cer_confirm_permanent_delete'] ?? '' ) ) {
				wp_die( esc_html__( 'Confirm permanently deleting this event and its content. Registration and payment history will be kept without event or ticket links.', 'custom-event-registration' ) );
			}
			$result = cer_permanently_delete_dashboard_event( $event_id );
			$notice = 'permanently_deleted';
		}
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'cer-events', 'view' => 'trash', 'cer_notice' => $notice ), admin_url( 'admin.php' ) ) );
		exit;
	}

	if ( empty( $registration_id ) ) {
		return;
	}

	$valid_nonce = false;
	if ( 'toggle_status' === $action ) {
		$valid_nonce = wp_verify_nonce( $nonce, 'cer_toggle_status_' . $registration_id );
	} elseif ( 'delete' === $action ) {
		$valid_nonce = wp_verify_nonce( $nonce, 'cer_delete_' . $registration_id );
	} elseif ( 'restore' === $action ) {
		$valid_nonce = wp_verify_nonce( $nonce, 'cer_restore_registration_' . $registration_id );
	}

	if ( ! $valid_nonce ) {
		wp_die( esc_html__( 'Security check failed.', 'custom-event-registration' ) );
	}
	if ( 'delete' === $action && '1' !== wp_unslash( $_GET['cer_confirm_trash'] ?? '' ) ) {
		wp_die( esc_html__( 'Confirm moving this registration to Trash. It will be recoverable for 30 days.', 'custom-event-registration' ) );
	}

	global $wpdb;
	$table = $wpdb->prefix . 'evt_registrations';

	if ( 'toggle_status' === $action ) {
		$status = $wpdb->get_var( $wpdb->prepare( "SELECT status FROM $table WHERE id = %d AND deleted_at IS NULL", $registration_id ) );
		if ( null === $status ) {
			wp_die( esc_html__( 'The registration is no longer active.', 'custom-event-registration' ) );
		}
		$new_status = 'pending' === $status ? 'confirmed' : 'pending';
		$result = $wpdb->update( $table, array( 'status' => $new_status, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $registration_id ), array( '%s', '%s' ), array( '%d' ) );
		if ( false === $result ) {
			wp_die( esc_html__( 'The registration status could not be updated.', 'custom-event-registration' ) . ' ' . esc_html( $wpdb->last_error ) );
		}
		delete_transient( 'cer_dashboard_metrics' );
	} elseif ( 'delete' === $action ) {
		$result = cer_trash_dashboard_record( 'registration', $registration_id );
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'cer-registrations', 'cer_notice' => 'trashed' ), admin_url( 'admin.php' ) ) );
		exit;
	} elseif ( 'restore' === $action ) {
		$result = cer_restore_dashboard_record( 'registration', $registration_id );
		if ( is_wp_error( $result ) ) {
			wp_die( esc_html( $result->get_error_message() ) );
		}
		wp_safe_redirect( add_query_arg( array( 'page' => 'cer-registrations', 'cer_notice' => 'restored' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	$redirect_url = add_query_arg( array( 'page' => 'cer-registrations', 'cer_notice' => 'updated' ), admin_url( 'admin.php' ) );
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
				'Connection' => 'keep-alive',
			),
			'body' => wp_json_encode( $registration_data ),
			'timeout' => 8,
			'connect_timeout' => 3,
			'httpversion' => '1.1',
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

		$paid_registrations = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$registrations_table} WHERE event_id = %d AND (status = 'paid' OR (status = 'confirmed' AND amount <= 0))", $event->id ) );
		if ( ! empty( $event->max_attendees ) && $paid_registrations >= (int) $event->max_attendees ) {
			throw new Exception( 'This event has reached its attendance capacity.' );
		}

		$reservation_active = ! empty( $registration->reserved_until ) && strtotime( $registration->reserved_until ) > time();
		if ( $reservation_active ) {
			$registration_updated = $wpdb->update(
				$registrations_table,
				array( 'status' => 'paid', 'reserved_until' => null, 'updated_at' => current_time( 'mysql' ) ),
				array( 'id' => $registration->id ),
				array( '%s', '%s', '%s' ),
				array( '%d' )
			);
			if ( false === $registration_updated ) {
				throw new Exception( 'Could not confirm the registration.' );
			}
			$wpdb->query( 'COMMIT' );
			delete_transient( 'cer_dashboard_metrics' );
			return array( 'ok' => true, 'already_paid' => false );
		}

		if ( cer_should_allow_late_payment_confirmation( $registration, $ticket ) ) {
			$registration_updated = $wpdb->update( $registrations_table, array( 'status' => 'paid', 'reserved_until' => null, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $registration->id ), array( '%s', '%s', '%s' ), array( '%d' ) );
			if ( false === $registration_updated ) {
				throw new Exception( 'Could not confirm the registration.' );
			}
			$wpdb->query( 'COMMIT' );
			delete_transient( 'cer_dashboard_metrics' );
			return array( 'ok' => true, 'already_paid' => false, 'late_payment' => true );
		}

		if ( null !== $ticket->quantity_available && (int) $ticket->quantity_sold >= (int) $ticket->quantity_available ) {
			$registration_updated = $wpdb->update( $registrations_table, array( 'status' => 'paid', 'reserved_until' => null, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $registration->id ), array( '%s', '%s', '%s' ), array( '%d' ) );
			if ( false === $registration_updated ) {
				throw new Exception( 'Could not confirm the registration.' );
			}
			$wpdb->query( 'COMMIT' );
			delete_transient( 'cer_dashboard_metrics' );
			return array( 'ok' => true, 'already_paid' => false, 'oversold' => true );
		}

		$ticket_updated = $wpdb->query( $wpdb->prepare( "UPDATE {$ticket_types_table} SET quantity_sold = quantity_sold + 1, updated_at = %s WHERE id = %d", current_time( 'mysql' ), $ticket->id ) );
		$registration_updated = $wpdb->update( $registrations_table, array( 'status' => 'paid', 'reserved_until' => null, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $registration->id ), array( '%s', '%s', '%s' ), array( '%d' ) );
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

function cer_register_rest_routes() {
	register_rest_route(
		'cer/v1',
		'/register',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'cer_handle_rest_registration_submission',
			'permission_callback' => '__return_true',
		)
	);
}

function cer_handle_rest_registration_submission( WP_REST_Request $request ) {
	$payload = $request->get_body_params();
	$result = cer_process_registration_submission( $payload );

	if ( ! empty( $result['success'] ) ) {
		return rest_ensure_response( array(
			'success' => true,
			'data'    => $result['data'],
		) );
	}

	return rest_ensure_response( array(
		'success' => false,
		'data'    => array( 'message' => $result['message'] ),
	) );
}

function cer_process_registration_submission( $payload = array() ) {
	$payload = is_array( $payload ) ? $payload : array();
	$nonce = isset( $payload['security'] ) ? sanitize_text_field( wp_unslash( $payload['security'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'cer_registration_form' ) ) {
		return array( 'success' => false, 'message' => 'Your session has expired. Please refresh this page and submit again.' );
	}

	global $wpdb;

	$full_name = sanitize_text_field( wp_unslash( $payload['full_name'] ?? '' ) );
	$attendee_title = sanitize_text_field( wp_unslash( $payload['attendee_title'] ?? '' ) );
	$organization = sanitize_text_field( wp_unslash( $payload['organization'] ?? '' ) );
	$attendee_role = sanitize_text_field( wp_unslash( $payload['attendee_role'] ?? '' ) );
	$email = sanitize_email( wp_unslash( $payload['email'] ?? '' ) );
	$phone = sanitize_text_field( wp_unslash( $payload['phone'] ?? '' ) );
	$county = sanitize_text_field( wp_unslash( $payload['county'] ?? '' ) );
	$age_group = sanitize_text_field( wp_unslash( $payload['age_group'] ?? '' ) );
	$gender = sanitize_text_field( wp_unslash( $payload['gender'] ?? '' ) );
	$accessibility_support = sanitize_text_field( wp_unslash( $payload['accessibility_support'] ?? '' ) );
	$accessibility_details = sanitize_textarea_field( wp_unslash( $payload['accessibility_details'] ?? '' ) );
	$ticket_type_id = absint( wp_unslash( $payload['ticket_type_id'] ?? 0 ) );
	$ticket_type = sanitize_text_field( wp_unslash( $payload['ticket_type'] ?? '' ) );
	$payment_method = sanitize_text_field( wp_unslash( $payload['payment_method'] ?? '' ) );
	$notes = sanitize_textarea_field( wp_unslash( $payload['notes'] ?? '' ) );
	$event_id = absint( wp_unslash( $payload['event_id'] ?? 0 ) );

		$payment_method = in_array( $payment_method, array( 'mpesa', 'card' ), true ) ? $payment_method : '';
		// Phone is required for both methods; full name is only collected for M-Pesa.
		// Card intentionally never collects a name — Paystack supplies it from the charge.
		$phone_digits = preg_replace( '/\D+/', '', $phone );
		if ( preg_match( '/^0?[17]\d{8}$/', $phone_digits ) ) {
			$phone = '254' . ( '0' === $phone_digits[0] ? substr( $phone_digits, 1 ) : $phone_digits );
		} elseif ( preg_match( '/^254[17]\d{8}$/', $phone_digits ) ) {
			$phone = $phone_digits;
		} else {
			$phone = '';
		}

		$valid_age_groups = array( '18-24', '25-34', '35-and-above' );
		$valid_genders = array( 'M', 'F', 'Other', 'Prefer not to say' );
		$valid_counties = function_exists( 'cer_kenyan_counties' ) ? cer_kenyan_counties() : array();
		$valid_accessibility_answers = array( 'no', 'yes' );
		$profile_complete = '' !== $full_name && '' !== $organization && '' !== $attendee_role
			&& ( empty( $valid_counties ) || in_array( $county, $valid_counties, true ) )
			&& in_array( $age_group, $valid_age_groups, true )
			&& in_array( $gender, $valid_genders, true )
			&& in_array( $accessibility_support, $valid_accessibility_answers, true )
			&& ( 'no' === $accessibility_support || '' !== $accessibility_details );
		if ( empty( $email ) || empty( $payment_method ) || empty( $phone ) || ! $profile_complete ) {
			return array( 'success' => false, 'message' => 'Please complete your contact and conference details, including any requested accessibility support.' );
		}
	$event = $wpdb->get_row( $wpdb->prepare( "SELECT id, max_attendees FROM {$wpdb->prefix}evt_events WHERE id = %d AND status = %s AND deleted_at IS NULL LIMIT 1", $event_id, 'published' ) );
	$ticket = $wpdb->get_row( $wpdb->prepare( "SELECT id, name, price, quantity_available, quantity_sold, is_maintenance FROM {$wpdb->prefix}evt_ticket_types WHERE id = %d AND event_id = %d AND deleted_at IS NULL LIMIT 1", $ticket_type_id, $event_id ) );
	if ( ! $event || ! $ticket ) {
		return array( 'success' => false, 'message' => 'The selected event or ticket is no longer available.' );
	}
	if ( ! empty( $ticket->is_maintenance ) ) {
		return array( 'success' => false, 'message' => 'This ticket is temporarily unavailable while maintenance is in progress. Please check back soon.' );
	}

	$paid_registrations = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}evt_registrations WHERE event_id = %d AND (status = 'paid' OR (status = 'confirmed' AND amount <= 0))", $event_id ) );
	if ( ! empty( $event->max_attendees ) && $paid_registrations >= (int) $event->max_attendees ) {
		return array( 'success' => false, 'message' => 'This event has reached its attendance capacity.' );
	}
	if ( null !== $ticket->quantity_available && (int) $ticket->quantity_sold >= (int) $ticket->quantity_available ) {
		return array( 'success' => false, 'message' => 'This ticket type is sold out.' );
	}

	$ticket_type = (string) $ticket->name;
	$amount = number_format( (float) $ticket->price, 2, '.', '' );
	$registration_uuid = wp_generate_uuid4();
	$payment_uuid = wp_generate_uuid4();
	$registrations_table = $wpdb->prefix . 'evt_registrations';

	$wpdb->query( 'START TRANSACTION' );
	try {
		$locked_event = $wpdb->get_row( $wpdb->prepare( "SELECT id, max_attendees FROM {$wpdb->prefix}evt_events WHERE id = %d AND status = 'published' AND deleted_at IS NULL FOR UPDATE", $event_id ) );
		if ( ! $locked_event ) {
			throw new Exception( 'The selected event or ticket is no longer available.' );
		}
		$locked_ticket = $wpdb->get_row( $wpdb->prepare( "SELECT id, price, quantity_available, quantity_sold, is_maintenance FROM {$wpdb->prefix}evt_ticket_types WHERE id = %d AND event_id = %d AND deleted_at IS NULL FOR UPDATE", $ticket_type_id, $event_id ) );
		if ( ! $locked_ticket ) {
			throw new Exception( 'The selected ticket is no longer available.' );
		}
		if ( ! empty( $locked_ticket->is_maintenance ) ) {
			throw new Exception( 'This ticket is temporarily unavailable while maintenance is in progress. Please check back soon.' );
		}
		if ( null !== $locked_ticket->quantity_available && (int) $locked_ticket->quantity_sold >= (int) $locked_ticket->quantity_available ) {
			throw new Exception( 'This ticket type is sold out.' );
		}

		$amount = number_format( (float) $locked_ticket->price, 2, '.', '' );
		$no_payment_required = (float) $amount <= 0;
		$existing_contact = null;
		if ( $no_payment_required ) {
			if ( '' === $full_name ) {
				throw new Exception( 'Please check your email, Kenyan phone number and required fields, then try again.' );
			}
			$existing_contact = $wpdb->get_row( $wpdb->prepare(
				"SELECT * FROM {$registrations_table} WHERE event_id = %d AND ticket_type_id = %d AND deleted_at IS NULL AND (status IN ('paid', 'confirmed') OR (status = 'pending' AND amount <= 0 AND reserved_until IS NULL)) AND (email_search_hash = %s OR LOWER(email) = %s) ORDER BY CASE WHEN status IN ('paid', 'confirmed') THEN 0 ELSE 1 END, id ASC LIMIT 1 FOR UPDATE",
				$event_id, $ticket_type_id, cer_registration_search_hash( $email ), strtolower( $email )
			) );
			if ( $wpdb->last_error ) {
				error_log( 'CER duplicate registration check failed: ' . $wpdb->last_error );
				throw new Exception( 'The registration could not be saved.' );
			}
			if ( $existing_contact && in_array( $existing_contact->status, array( 'paid', 'confirmed' ), true ) ) {
				throw new Exception( 'You are already registered for this ticket. Please check your email for your ticket or contact the event team for help.' );
			}
		}
		if ( ! empty( $locked_event->max_attendees ) ) {
			if ( $no_payment_required ) {
				$confirmed_count = (int) $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*) FROM {$registrations_table} WHERE event_id = %d AND (status = 'paid' OR (status = 'confirmed' AND amount <= 0) OR (status IN ('pending', 'awaiting_payment') AND amount > 0 AND reserved_until > %s))",
					$event_id,
					gmdate( 'Y-m-d H:i:s' )
				) );
			} else {
				$confirmed_count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$registrations_table} WHERE event_id = %d AND (status = 'paid' OR (status = 'confirmed' AND amount <= 0))", $event_id ) );
			}
			if ( $wpdb->last_error ) {
				error_log( 'CER admission capacity check failed: ' . $wpdb->last_error );
				throw new Exception( 'The registration could not be saved.' );
			}
			if ( $confirmed_count >= (int) $locked_event->max_attendees ) {
				throw new Exception( 'This event has reached its attendance capacity.' );
			}
		}
		$reserved_until = $no_payment_required ? null : gmdate( 'Y-m-d H:i:s', time() + cer_get_registration_hold_seconds() );
		$registration_data = array(
			'registration_uuid' => $registration_uuid,
			'payment_uuid' => $payment_uuid,
			'event_id' => $event_id ? $event_id : null,
			'ticket_type_id' => $ticket_type_id ? $ticket_type_id : null,
			'full_name' => $full_name,
			'attendee_title' => $attendee_title,
			'organization' => $organization,
			'attendee_role' => $attendee_role,
			'email' => $email,
			'phone' => $phone,
			'county' => $county,
			'age_group' => $age_group,
			'gender' => $gender,
			'accessibility_support' => $accessibility_support,
			'accessibility_details' => 'yes' === $accessibility_support ? $accessibility_details : '',
			'ticket_type' => $ticket_type,
			'payment_method' => $no_payment_required ? 'none' : $payment_method,
			'amount' => $amount,
			'notes' => $notes,
			'status' => $no_payment_required ? 'confirmed' : 'pending',
			'reserved_until' => $reserved_until,
		);
		if ( $existing_contact ) {
			$registration_data['registration_uuid'] = $existing_contact->registration_uuid;
			$registration_data['payment_uuid'] = $existing_contact->payment_uuid;
			$registration_data['user_access_key'] = $existing_contact->user_access_key;
			$inserted = $wpdb->update( $registrations_table, cer_prepare_registration_row( $registration_data ), array( 'id' => (int) $existing_contact->id ) );
			$registration_id = (int) $existing_contact->id;
		} else {
			$inserted = $wpdb->insert(
				$registrations_table,
				cer_prepare_registration_row( $registration_data ),
				array( '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s' )
			);
			$registration_id = (int) $wpdb->insert_id;
		}
		if ( ! $inserted ) {
			throw new Exception( 'The registration could not be saved.' );
		}

		$ticket_updated = $wpdb->update(
			$wpdb->prefix . 'evt_ticket_types',
			array(
				'quantity_sold' => (int) $locked_ticket->quantity_sold + 1,
				'updated_at' => current_time( 'mysql' ),
			),
			array( 'id' => (int) $locked_ticket->id ),
			array( '%d', '%s' ),
			array( '%d' )
		);
		if ( false === $ticket_updated ) {
			throw new Exception( 'The registration could not be saved.' );
		}
		$wpdb->query( 'COMMIT' );
	} catch ( Exception $e ) {
		$wpdb->query( 'ROLLBACK' );
		return array( 'success' => false, 'message' => $e->getMessage() );
	}

	if ( $wpdb->last_error ) {
		error_log( 'CER registration insert failed: ' . $wpdb->last_error );
	}

	if ( $inserted ) {
		if ( $no_payment_required ) {
			delete_transient( 'cer_dashboard_metrics' );
			$ticket_sent = cer_send_ticket_for_registration( $registration_id );
			return array(
				'success' => true,
				'data' => array(
					'payment_status' => 'not_required',
					'ticket_sent' => $ticket_sent,
					'message' => $ticket_sent
						? 'Thank you for registering! Your registration is confirmed and your ticket has been emailed to you. Please present it at the entrance.'
						: 'Thank you for registering! Your registration is confirmed, but we could not email your ticket yet. We will retry automatically. If it does not arrive, contact the event team; please do not register again.',
				),
			);
		}
		$sync_data = array(
			'registration_uuid' => $registration_uuid,
			'payment_uuid' => $payment_uuid,
			'full_name' => $full_name,
			'attendee_title' => $attendee_title,
			'organization' => $organization,
			'attendee_role' => $attendee_role,
			'email' => $email,
			'phone' => $phone,
			'county' => $county,
			'age_group' => $age_group,
			'gender' => $gender,
			'accessibility_support' => $accessibility_support,
			'accessibility_details' => 'yes' === $accessibility_support ? $accessibility_details : '',
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
			return array(
				'success' => true,
				'data'    => array(
					'message' => 'Registration received. Opening secure payment checkout.',
					'payment_method' => $payment_method,
					'payment_status' => ! empty( $payment_response['status'] ) ? sanitize_text_field( $payment_response['status'] ) : 'pending',
					'display_text' => ! empty( $payment_response['display_text'] ) ? sanitize_text_field( $payment_response['display_text'] ) : '',
					'access_code' => ! empty( $payment_response['access_code'] ) ? sanitize_text_field( $payment_response['access_code'] ) : '',
					'reference' => ! empty( $payment_response['reference'] ) ? sanitize_text_field( $payment_response['reference'] ) : '',
				),
			);
		} catch ( Exception $e ) {
			error_log( 'CER payment initiation failed: ' . $e->getMessage() );
			$wpdb->update(
				$registrations_table,
				array( 'status' => 'failed', 'updated_at' => current_time( 'mysql' ) ),
				array( 'registration_uuid' => $registration_uuid ),
				array( '%s', '%s' ),
				array( '%s' )
			);
			return array( 'success' => false, 'message' => 'We have saved your registration details, but could not start your payment. Please try again later. If you approved a payment or were charged, do not pay again; contact the event team for help.' );
		}
	}
	return array( 'success' => false, 'message' => 'The registration could not be saved.' );
}

function cer_handle_ajax_submission() {
	$result = cer_process_registration_submission( $_POST );
	if ( ! empty( $result['success'] ) ) {
		wp_send_json_success( $result['data'] );
	}
	wp_send_json_error( array( 'message' => $result['message'] ) );
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
