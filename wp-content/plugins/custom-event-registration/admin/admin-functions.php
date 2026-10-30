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

function cer_render_trash_admin_notice(): void {
	$notice = isset( $_GET['cer_notice'] ) ? sanitize_key( wp_unslash( $_GET['cer_notice'] ) ) : '';
	$messages = array(
		'trashed' => __( 'The selected record was moved to trash. It can be restored for 30 days.', 'custom-event-registration' ),
		'restored' => __( 'The selected record was restored.', 'custom-event-registration' ),
		'permanently_deleted' => __( 'The event and its content were permanently deleted. Registration and payment history was retained without event or ticket links.', 'custom-event-registration' ),
		'published' => __( 'The selected events were published.', 'custom-event-registration' ),
		'closed' => __( 'The selected events were closed.', 'custom-event-registration' ),
		'updated' => __( 'The selected registrations were updated.', 'custom-event-registration' ),
	);

	if ( isset( $messages[ $notice ] ) ) {
		printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( $messages[ $notice ] ) );
	} elseif ( 'error' === $notice ) {
		$message = get_transient( 'cer_registration_bulk_error' );
		delete_transient( 'cer_registration_bulk_error' );
		printf( '<div class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $message ?: __( 'One or more registrations could not be updated.', 'custom-event-registration' ) ) );
	}

}

function cer_render_admin_pagination( int $current, int $total, string $url, string $page_arg = 'paged', string $label = '' ): void {
	$total = max( 1, $total );
	$current = max( 1, min( $current, $total ) );
	echo '<nav class="cer-list-pagination" aria-label="' . esc_attr( $label ?: __( 'Pages', 'custom-event-registration' ) ) . '">';
	echo '<span class="cer-page-summary">' . esc_html( sprintf( __( 'Page %1$d of %2$d', 'custom-event-registration' ), $current, $total ) ) . '</span>';
	if ( $current > 1 ) {
		echo '<a class="page-numbers" href="' . esc_url( add_query_arg( $page_arg, $current - 1, $url ) ) . '">' . esc_html__( 'Previous', 'custom-event-registration' ) . '</a>';
	} else {
		echo '<span class="page-numbers is-disabled" aria-disabled="true">' . esc_html__( 'Previous', 'custom-event-registration' ) . '</span>';
	}
	// Unlike paginate_links(), keep every link limited to the supplied query arguments.
	$page_numbers = array( 1, $total );
	for ( $page_number = max( 1, $current - 1 ); $page_number <= min( $total, $current + 1 ); $page_number++ ) {
		$page_numbers[] = $page_number;
	}
	$page_numbers = array_unique( $page_numbers );
	sort( $page_numbers, SORT_NUMERIC );
	$previous_number = 0;
	foreach ( $page_numbers as $page_number ) {
		if ( $previous_number && $page_number > $previous_number + 1 ) {
			echo '<span class="page-numbers dots" aria-hidden="true">&hellip;</span>';
		}
		if ( $page_number === $current ) {
			echo '<span class="page-numbers current" aria-current="page">' . esc_html( number_format_i18n( $page_number ) ) . '</span>';
		} else {
			echo '<a class="page-numbers" href="' . esc_url( add_query_arg( $page_arg, $page_number, $url ) ) . '">' . esc_html( number_format_i18n( $page_number ) ) . '</a>';
		}
		$previous_number = $page_number;
	}
	if ( $current < $total ) {
		echo '<a class="page-numbers" href="' . esc_url( add_query_arg( $page_arg, $current + 1, $url ) ) . '">' . esc_html__( 'Next', 'custom-event-registration' ) . '</a>';
	} else {
		echo '<span class="page-numbers is-disabled" aria-disabled="true">' . esc_html__( 'Next', 'custom-event-registration' ) . '</span>';
	}
	echo '</nav>';
}
