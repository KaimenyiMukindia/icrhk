<?php
/* Template Name: Event Registration Page */

if ( defined( 'CER_PLUGIN_DIR' ) ) {
	require_once CER_PLUGIN_DIR . 'includes/event-page-renderer.php';
}

if ( function_exists( 'cer_event_page_get_requested_slug' ) && function_exists( 'cer_event_page_get_current_event' ) ) {
	$requested_event_slug = cer_event_page_get_requested_slug();
	$resolved_event = cer_event_page_get_current_event();
	if ( empty( $resolved_event ) && ! empty( $requested_event_slug ) ) {
		status_header( 404 );
		nocache_headers();
	}
}
get_header();

if ( function_exists( 'cer_render_event_registration_page' ) ) {
	cer_render_event_registration_page();
} else {
	echo '<div class="auto-container" style="padding:48px 20px;">Event registration rendering is unavailable.</div>';
}

get_footer();

