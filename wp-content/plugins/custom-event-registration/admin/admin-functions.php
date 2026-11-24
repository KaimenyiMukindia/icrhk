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

function cer_get_dashboard_event_metrics( int $event_id, ?array $event = null ) {
	global $wpdb;

	if ( null === $event ) {
		$event = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id, name FROM {$wpdb->prefix}evt_events WHERE id = %d AND deleted_at IS NULL",
				$event_id
			),
			ARRAY_A
		);
		if ( $wpdb->last_error ) {
			error_log( 'CER dashboard event lookup failed: ' . $wpdb->last_error );
			return new WP_Error( 'cer_dashboard_query_failed', __( 'Unable to load event metrics. Please try again.', 'custom-event-registration' ) );
		}
	}

	if ( ! $event || (int) $event['id'] !== $event_id ) {
		return new WP_Error( 'cer_dashboard_event_not_found', __( 'This event is no longer available.', 'custom-event-registration' ) );
	}

	$registration_metrics = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT SUM(deleted_at IS NULL) AS total_registrations,
				SUM(CASE WHEN parent_registration_id IS NULL AND status IN ('paid','confirmed') THEN ticket_quantity ELSE 0 END) AS tickets_sold,
				COALESCE(SUM(CASE WHEN status IN ('paid','confirmed') THEN amount ELSE 0 END), 0) AS revenue_collected
			FROM {$wpdb->prefix}evt_registrations
			WHERE event_id = %d",
			$event_id
		),
		ARRAY_A
	);
	if ( $wpdb->last_error || ! $registration_metrics ) {
		error_log( 'CER dashboard registration metrics failed: ' . $wpdb->last_error );
		return new WP_Error( 'cer_dashboard_query_failed', __( 'Unable to load event metrics. Please try again.', 'custom-event-registration' ) );
	}

	$inventory_metrics = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT COALESCE(SUM(CASE WHEN quantity_available IS NULL THEN 0 ELSE GREATEST(quantity_available - quantity_sold, 0) END), 0) AS tickets_available
			FROM {$wpdb->prefix}evt_ticket_types
			WHERE event_id = %d AND deleted_at IS NULL",
			$event_id
		),
		ARRAY_A
	);
	if ( $wpdb->last_error || ! $inventory_metrics ) {
		error_log( 'CER dashboard inventory metrics failed: ' . $wpdb->last_error );
		return new WP_Error( 'cer_dashboard_query_failed', __( 'Unable to load event metrics. Please try again.', 'custom-event-registration' ) );
	}

	$sponsorship_packages = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->prefix}evt_sponsorships WHERE event_id = %d AND deleted_at IS NULL",
			$event_id
		)
	);
	if ( $wpdb->last_error || null === $sponsorship_packages ) {
		error_log( 'CER dashboard sponsorship metrics failed: ' . $wpdb->last_error );
		return new WP_Error( 'cer_dashboard_query_failed', __( 'Unable to load event metrics. Please try again.', 'custom-event-registration' ) );
	}

	return array(
		'event' => $event,
		'metrics' => array(
			'total_registrations' => (int) $registration_metrics['total_registrations'],
			'tickets_sold' => (int) $registration_metrics['tickets_sold'],
			'tickets_available' => (int) $inventory_metrics['tickets_available'],
			'revenue_collected' => (float) $registration_metrics['revenue_collected'],
			'sponsorship_packages' => (int) $sponsorship_packages,
		),
	);
}

function cer_format_dashboard_event_metrics( array $metrics ): array {
	return array(
		'total_registrations' => number_format_i18n( $metrics['total_registrations'] ),
		'tickets_sold' => number_format_i18n( $metrics['tickets_sold'] ),
		'tickets_available' => number_format_i18n( $metrics['tickets_available'] ),
		'revenue_collected' => 'KES ' . number_format_i18n( $metrics['revenue_collected'], 2 ),
		'sponsorship_packages' => number_format_i18n( $metrics['sponsorship_packages'] ),
	);
}

function cer_ajax_dashboard_event_metrics(): void {
	if ( ! current_user_can( 'manage_options' ) || ! check_ajax_referer( 'cer_dashboard_metrics', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to load event metrics.', 'custom-event-registration' ) ), 403 );
	}

	$event_id = isset( $_POST['event_id'] ) && is_string( $_POST['event_id'] ) ? wp_unslash( $_POST['event_id'] ) : '';
	if ( ! ctype_digit( $event_id ) || ! (int) $event_id ) {
		wp_send_json_error( array( 'message' => __( 'Please select a valid event.', 'custom-event-registration' ) ), 400 );
	}

	$result = cer_get_dashboard_event_metrics( (int) $event_id );
	if ( is_wp_error( $result ) ) {
		$status = 'cer_dashboard_event_not_found' === $result->get_error_code() ? 404 : 500;
		wp_send_json_error( array( 'message' => $result->get_error_message() ), $status );
	}

	wp_send_json_success(
		array(
			'event_id' => (int) $result['event']['id'],
			'event_label' => sprintf( __( 'Metrics for: %s', 'custom-event-registration' ), $result['event']['name'] ),
			'metrics' => cer_format_dashboard_event_metrics( $result['metrics'] ),
		)
	);
}

class CER_Event_Save_Exception extends RuntimeException {}
class CER_Event_Storage_Exception extends CER_Event_Save_Exception {}
class CER_Event_Save_Unconfirmed_Exception extends CER_Event_Save_Exception {}

function cer_require_transactional_event_tables( array $tables ): void {
	global $wpdb;

	$tables = array_values( array_unique( $tables ) );
	$placeholders = implode( ', ', array_fill( 0, count( $tables ), '%s' ) );
	$engines = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT t.TABLE_NAME AS table_name, t.ENGINE AS storage_engine, e.TRANSACTIONS AS transactions_supported
			FROM information_schema.TABLES t
			LEFT JOIN information_schema.ENGINES e ON e.ENGINE = t.ENGINE
			WHERE t.TABLE_SCHEMA = DATABASE() AND t.TABLE_NAME IN ({$placeholders})",
			$tables
		),
		ARRAY_A
	);
	$unsupported = array();
	foreach ( $engines ?: array() as $engine ) {
		if ( 'YES' !== strtoupper( (string) $engine['transactions_supported'] ) ) {
			$unsupported[] = $engine['table_name'] . ' (' . $engine['storage_engine'] . ')';
		}
	}
	if ( $wpdb->last_error || count( $engines ?: array() ) !== count( $tables ) || $unsupported ) {
		$reason = $wpdb->last_error ?: ( $unsupported ? implode( ', ', $unsupported ) : 'one or more event tables were not found' );
		error_log( 'CER could not verify transactional event storage: ' . $reason );
		throw new CER_Event_Storage_Exception( 'Transactional event storage could not be verified.' );
	}
}

function cer_event_save_connection_id(): string {
	global $wpdb;

	$connection_id = $wpdb->get_var( 'SELECT CONNECTION_ID()' );
	if ( $wpdb->last_error || null === $connection_id ) {
		error_log( 'CER could not verify the event save connection: ' . $wpdb->last_error );
		throw new CER_Event_Save_Unconfirmed_Exception( 'Event save connection could not be verified.' );
	}
	return (string) $connection_id;
}

function cer_require_event_save_connection( string $expected ): void {
	if ( cer_event_save_connection_id() !== $expected ) {
		error_log( 'CER database connection changed during an event save.' );
		throw new CER_Event_Save_Unconfirmed_Exception( 'Event save connection changed.' );
	}
}

function cer_check_event_save_result( $result, string $operation ) {
	global $wpdb;

	if ( false === $result ) {
		error_log( 'CER event save failed during ' . $operation . ': ' . $wpdb->last_error );
		throw new CER_Event_Save_Exception( $operation );
	}

	return $result;
}

function cer_event_save_data_changed( array $existing, array $data, array $formats ): bool {
	$position = 0;
	foreach ( $data as $field => $value ) {
		$format = $formats[ $position++ ] ?? '%s';
		if ( 'updated_at' === $field ) {
			continue;
		}
		$old_value = $existing[ $field ] ?? null;
		if ( null === $old_value || null === $value ) {
			if ( $old_value !== $value ) {
				return true;
			}
		} elseif ( '%f' === $format ) {
			if ( (float) $old_value !== (float) $value ) {
				return true;
			}
		} elseif ( (string) $old_value !== (string) $value ) {
			return true;
		}
	}
	return false;
}

function cer_save_event_repeater_item( string $table, int $item_id, int $event_id, array $data, array $formats, array $loaded_rows ): bool {
	global $wpdb;

	$existing = $loaded_rows[ $item_id ] ?? null;
	if ( $item_id && ! $existing ) {
		$existing = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d AND event_id = %d", $item_id, $event_id ),
			ARRAY_A
		);
		if ( $wpdb->last_error ) {
			cer_check_event_save_result( false, 'item lookup' );
		}
		if ( ! $existing ) {
			$item_id = 0;
		}
	}

	if ( $item_id ) {
		if ( ! cer_event_save_data_changed( $existing, $data, $formats ) ) {
			return false;
		}
		$result = cer_check_event_save_result(
			$wpdb->update( $table, $data, array( 'id' => $item_id, 'event_id' => $event_id ), $formats, array( '%d', '%d' ) ),
			'item update'
		);
		if ( ! $result ) {
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE id = %d AND event_id = %d", $item_id, $event_id ) );
			if ( $wpdb->last_error || ! $exists ) {
				cer_check_event_save_result( false, 'item update verification' );
			}
		}
		return (bool) $result;
	}

	cer_check_event_save_result(
		$wpdb->insert(
			$table,
			array_merge( array( 'created_at' => current_time( 'mysql' ) ), $data ),
			array_merge( array( '%s' ), $formats )
		),
		'item insertion'
	);
	return true;
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
	} elseif ( 'ticket_assigned' === $notice ) {
		$key = 'cer_assignment_notice_' . get_current_user_id();
		$result = get_transient( $key );
		delete_transient( $key );
		if ( is_array( $result ) ) {
			cer_render_admin_notice( $result['message'], $result['error'] ? 'error' : 'success' );
		}
	} elseif ( 'payment_verified' === $notice ) {
		$key = 'cer_payment_verify_' . get_current_user_id();
		$result = get_transient( $key );
		delete_transient( $key );
		if ( is_array( $result ) ) {
			cer_render_admin_notice( $result['message'], $result['error'] ? 'error' : 'success' );
		}
	} elseif ( 'ticket_retry' === $notice ) {
		$key = 'cer_ticket_retry_result_' . get_current_user_id();
		$result = get_transient( $key );
		delete_transient( $key );
		if ( is_array( $result ) ) {
			$type = empty( $result['failed'] ) ? 'success' : 'warning';
			$message = sprintf( __( 'Ticket delivery: %1$d sent; %2$d already sent.', 'custom-event-registration' ), $result['sent'], $result['already_sent'] );
			if ( ! empty( $result['failed'] ) ) {
				$message .= ' ' . implode( ' ', $result['failed'] );
			}
			printf( '<div class="notice notice-%s is-dismissible"><p>%s</p></div>', esc_attr( $type ), esc_html( $message ) );
		}
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
