<?php
/**
 * Admin event management functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render events list page.
 */
function cer_render_events_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view this page.', 'custom-event-registration' ) );
	}

	include CER_PLUGIN_DIR . 'admin/events-list.php';
}

function cer_render_dashboard_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view this page.', 'custom-event-registration' ) );
	}

	include CER_PLUGIN_DIR . 'admin/dashboard.php';
}


/**
 * Render event form page.
 */
function cer_render_event_form_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view this page.', 'custom-event-registration' ) );
	}

	include CER_PLUGIN_DIR . 'admin/event-form.php';
}

/**
 * Render registrations page.
 */
function cer_render_registrations_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view this page.', 'custom-event-registration' ) );
	}

	include CER_PLUGIN_DIR . 'admin/registrations-list.php';
}
