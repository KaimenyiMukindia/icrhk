<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have permission to view this page.', 'custom-event-registration' ) );
}

global $wpdb;

$per_page = 5;
$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
$status_filter = isset( $_GET['event_status'] ) ? sanitize_key( wp_unslash( $_GET['event_status'] ) ) : '';
$month_filter = isset( $_GET['event_month'] ) ? sanitize_text_field( wp_unslash( $_GET['event_month'] ) ) : '';
$trash_view = isset( $_GET['view'] ) && 'trash' === sanitize_key( wp_unslash( $_GET['view'] ) );
$page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
$offset = ( $page - 1 ) * $per_page;

$events_table = $wpdb->prefix . 'evt_events';
$registrations_table = $wpdb->prefix . 'evt_registrations';
$where = $trash_view ? array( 'e.deleted_at IS NOT NULL AND e.deleted_at >= %s' ) : array( 'e.deleted_at IS NULL' );
$bindings = $trash_view ? array( cer_dashboard_trash_cutoff() ) : array();

if ( '' !== $search ) {
	$where[] = '(e.name LIKE %s OR e.venue LIKE %s)';
	$search_like = '%' . $wpdb->esc_like( $search ) . '%';
	$bindings[] = $search_like;
	$bindings[] = $search_like;
}
if ( $status_filter ) {
	$where[] = 'e.status = %s';
	$bindings[] = $status_filter;
}
if ( $month_filter ) {
	$month_start = DateTime::createFromFormat( '!Y-m-d', $month_filter . '-01' );
	if ( $month_start ) {
		$month_end = ( clone $month_start )->modify( '+1 month' );
		$where[] = 'e.event_date >= %s AND e.event_date < %s';
		$bindings[] = $month_start->format( 'Y-m-d H:i:s' );
		$bindings[] = $month_end->format( 'Y-m-d H:i:s' );
	}
}

$where_sql = $where ? ' WHERE ' . implode( ' AND ', $where ) : '';

$select_sql = "SELECT e.*, COALESCE(reg.total_registrations, 0) AS total_registrations,
	COALESCE(reg.tickets_sold, 0) AS tickets_sold, COALESCE(reg.revenue, 0) AS revenue,
	COALESCE(ticket_inventory.tickets_available, 0) AS tickets_available
	FROM {$events_table} e
	LEFT JOIN (
		SELECT event_id,
			SUM(deleted_at IS NULL) AS total_registrations,
			SUM(CASE WHEN status IN ('paid','confirmed') THEN 1 ELSE 0 END) AS tickets_sold,
			SUM(CASE WHEN status IN ('paid','confirmed') THEN amount ELSE 0 END) AS revenue
		FROM {$registrations_table}
		GROUP BY event_id
	) reg ON reg.event_id = e.id
	LEFT JOIN (
		SELECT event_id,
			SUM(CASE WHEN quantity_available IS NULL THEN 0 ELSE GREATEST(quantity_available - quantity_sold, 0) END) AS tickets_available
		FROM {$wpdb->prefix}evt_ticket_types
		WHERE deleted_at IS NULL
		GROUP BY event_id
	) ticket_inventory ON ticket_inventory.event_id = e.id";

$query = $select_sql . $where_sql . ' ORDER BY e.event_date DESC LIMIT %d OFFSET %d';
$total_query = 'SELECT COUNT(*) FROM ' . $events_table . ' e' . $where_sql;
$total_prepared = $wpdb->prepare( $total_query, $bindings );
$total_events = (int) $wpdb->get_var( $total_prepared );
if ( $wpdb->last_error ) {
	error_log( 'CER event filtering count failed: ' . $wpdb->last_error );
	wp_die( esc_html__( 'Unable to load events. Please try again.', 'custom-event-registration' ), '', array( 'response' => 500 ) );
}
$page = min( $page, max( 1, (int) ceil( $total_events / $per_page ) ) );
$offset = ( $page - 1 ) * $per_page;
$prepared_query = $wpdb->prepare( $query, array_merge( $bindings, array( $per_page, $offset ) ) );
$events = $wpdb->get_results( $prepared_query );
if ( $wpdb->last_error ) {
	error_log( 'CER event filtering failed: ' . $wpdb->last_error );
	wp_die( esc_html__( 'Unable to load events. Please try again.', 'custom-event-registration' ), '', array( 'response' => 500 ) );
}

$bulk_action = isset( $_POST['bulk_action'] ) ? sanitize_key( wp_unslash( $_POST['bulk_action'] ) ) : '';
$event_ids = isset( $_POST['event_ids'] ) && is_array( $_POST['event_ids'] ) ? array_map( 'absint', $_POST['event_ids'] ) : array();
if ( ! empty( $event_ids ) && ! empty( $bulk_action ) && check_admin_referer( 'cer_event_bulk_action', 'cer_event_bulk_nonce' ) ) {
	if ( 'delete' === $bulk_action && ! $trash_view && '1' !== wp_unslash( $_POST['cer_confirm_trash'] ?? '' ) ) {
		wp_die( esc_html__( 'Confirm moving the selected events to Trash. They will be recoverable for 30 days.', 'custom-event-registration' ) );
	}
	if ( 'delete_permanently' === $bulk_action && $trash_view && '1' !== wp_unslash( $_POST['cer_confirm_permanent_delete'] ?? '' ) ) {
		wp_die( esc_html__( 'Confirm permanently deleting the selected events and their content. Registration and payment history will be kept without event or ticket links.', 'custom-event-registration' ) );
	}

	foreach ( $event_ids as $event_id ) {
		if ( 'publish' === $bulk_action && ! $trash_view ) {
			$updated = $wpdb->update( $events_table, array( 'status' => 'published' ), array( 'id' => $event_id, 'deleted_at' => null ), array( '%s' ), array( '%d', '%s' ) );
			if ( false === $updated ) {
				wp_die( esc_html__( 'The selected events could not be published.', 'custom-event-registration' ) . ' ' . esc_html( $wpdb->last_error ) );
			}
		} elseif ( 'close' === $bulk_action && ! $trash_view ) {
			$updated = $wpdb->update( $events_table, array( 'status' => 'closed' ), array( 'id' => $event_id, 'deleted_at' => null ), array( '%s' ), array( '%d', '%s' ) );
			if ( false === $updated ) {
				wp_die( esc_html__( 'The selected events could not be closed.', 'custom-event-registration' ) . ' ' . esc_html( $wpdb->last_error ) );
			}
		} elseif ( 'delete' === $bulk_action && ! $trash_view ) {
			$result = cer_trash_dashboard_record( 'event', $event_id );
			if ( is_wp_error( $result ) ) {
				wp_die( esc_html( $result->get_error_message() ) );
			}
		} elseif ( 'restore' === $bulk_action && $trash_view ) {
			$result = cer_restore_dashboard_record( 'event', $event_id );
			if ( is_wp_error( $result ) ) {
				wp_die( esc_html( $result->get_error_message() ) );
			}
		} elseif ( 'delete_permanently' === $bulk_action && $trash_view ) {
			$result = cer_permanently_delete_dashboard_event( $event_id );
			if ( is_wp_error( $result ) ) {
				wp_die( esc_html( $result->get_error_message() ) );
			}
		}
	}
	$redirect_args = array( 'page' => 'cer-events' );
	if ( 'delete' === $bulk_action ) {
		$redirect_args['cer_notice'] = 'trashed';
	} elseif ( 'restore' === $bulk_action ) {
		$redirect_args['cer_notice'] = 'restored';
	} elseif ( 'delete_permanently' === $bulk_action ) {
		$redirect_args['view'] = 'trash';
		$redirect_args['cer_notice'] = 'permanently_deleted';
	} elseif ( 'publish' === $bulk_action ) {
		$redirect_args['cer_notice'] = 'published';
	} elseif ( 'close' === $bulk_action ) {
		$redirect_args['cer_notice'] = 'closed';
	}
	wp_safe_redirect( add_query_arg( $redirect_args, admin_url( 'admin.php' ) ) );
	exit;
}
?>
<div class="wrap cer-admin-shell" data-cer-live-list="events">
	<hr class="wp-header-end" />
	<?php cer_render_trash_admin_notice(); ?>
	<div class="cer-admin-header">
		<h1><?php esc_html_e( 'All Events', 'custom-event-registration' ); ?></h1>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-event-new' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Add New Event', 'custom-event-registration' ); ?></a>
	</div>

	<nav class="cer-list-view-links" aria-label="<?php esc_attr_e( 'Event list views', 'custom-event-registration' ); ?>">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-events' ) ); ?>" class="<?php echo $trash_view ? '' : 'current'; ?>"><?php esc_html_e( 'Active Events', 'custom-event-registration' ); ?></a>
		<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'cer-events', 'view' => 'trash' ), admin_url( 'admin.php' ) ) ); ?>" class="<?php echo $trash_view ? 'current' : ''; ?>"><?php esc_html_e( 'Trash', 'custom-event-registration' ); ?></a>
	</nav>

	<form method="get" data-cer-live-filters action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<input type="hidden" name="page" value="cer-events" />
		<?php if ( $trash_view ) : ?><input type="hidden" name="view" value="trash" /><?php endif; ?>
		<div class="cer-filter-bar">
			<div class="field">
				<label for="event-status"><?php esc_html_e( 'Status', 'custom-event-registration' ); ?></label>
				<select id="event-status" name="event_status">
					<option value=""><?php esc_html_e( 'All Statuses', 'custom-event-registration' ); ?></option>
					<option value="draft" <?php selected( $status_filter, 'draft' ); ?>><?php esc_html_e( 'Draft', 'custom-event-registration' ); ?></option>
					<option value="published" <?php selected( $status_filter, 'published' ); ?>><?php esc_html_e( 'Published', 'custom-event-registration' ); ?></option>
					<option value="closed" <?php selected( $status_filter, 'closed' ); ?>><?php esc_html_e( 'Closed', 'custom-event-registration' ); ?></option>
					<option value="cancelled" <?php selected( $status_filter, 'cancelled' ); ?>><?php esc_html_e( 'Cancelled', 'custom-event-registration' ); ?></option>
				</select>
			</div>
			<div class="field">
				<label for="event-month"><?php esc_html_e( 'Month', 'custom-event-registration' ); ?></label>
				<select id="event-month" name="event_month">
					<option value=""><?php esc_html_e( 'All Months', 'custom-event-registration' ); ?></option>
					<?php
					$months = $wpdb->get_col( "SELECT DISTINCT DATE_FORMAT(event_date, '%Y-%m') FROM {$events_table} ORDER BY event_date DESC" );
					foreach ( $months as $month ) {
						echo '<option value="' . esc_attr( $month ) . '" ' . selected( $month_filter, $month, false ) . '>' . esc_html( date_i18n( 'F Y', strtotime( $month . '-01' ) ) ) . '</option>';
					}
					?>
				</select>
			</div>
			<div class="field" style="min-width:260px;">
				<label for="event-search"><?php esc_html_e( 'Search', 'custom-event-registration' ); ?></label>
				<input id="event-search" type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="Search events or venue" />
			</div>
			<div class="field" style="justify-content:flex-end;">
				<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Filter', 'custom-event-registration' ); ?>" />
			</div>
		</div>
	</form>

	<div data-cer-live-results>
	<p class="description"><?php printf( esc_html__( '%s matching events', 'custom-event-registration' ), esc_html( number_format_i18n( $total_events ) ) ); ?></p>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=cer-events' . ( $trash_view ? '&view=trash' : '' ) ) ); ?>">
		<?php wp_nonce_field( 'cer_event_bulk_action', 'cer_event_bulk_nonce' ); ?>
		<input type="hidden" name="cer_confirm_trash" value="0" />
		<input type="hidden" name="cer_confirm_permanent_delete" value="0" />
		<div class="cer-table-wrap" tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Events table, scroll to see all columns', 'custom-event-registration' ); ?>">
			<table class="cer-events-table cer-events-list-table">
				<thead>
					<tr>
						<th style="width:40px;"><input type="checkbox" id="cer-select-all-events" aria-label="<?php esc_attr_e( 'Select all events', 'custom-event-registration' ); ?>" /></th>
						<th><?php esc_html_e( 'Event Title', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Date', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Venue', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Tickets Sold', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Available', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Revenue', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Registrations', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Status', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Front-End URL', 'custom-event-registration' ); ?></th>
						<?php if ( $trash_view ) : ?><th><?php esc_html_e( 'Deleted', 'custom-event-registration' ); ?></th><?php endif; ?>
						<th><?php esc_html_e( 'Actions', 'custom-event-registration' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( ! empty( $events ) ) : ?>
						<?php foreach ( $events as $event ) : ?>
							<tr>
								<td><input type="checkbox" name="event_ids[]" value="<?php echo esc_attr( $event->id ); ?>" class="cer-event-checkbox" aria-label="<?php echo esc_attr( sprintf( __( 'Select event: %s', 'custom-event-registration' ), $event->name ) ); ?>" /></td>
								<td>
									<strong>
										<?php if ( $trash_view ) : ?>
											<?php echo esc_html( $event->name ); ?>
										<?php else : ?>
											<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'cer-event-new', 'event_id' => $event->id ), admin_url( 'admin.php' ) ) ); ?>"><?php echo esc_html( $event->name ); ?></a>
										<?php endif; ?>
									</strong>
								</td>
								<td><?php echo esc_html( date_i18n( 'M j, Y', strtotime( $event->event_date ) ) ); ?></td>
								<td><?php echo esc_html( $event->venue ?: '—' ); ?></td>
								<td><?php echo esc_html( number_format_i18n( (int) $event->tickets_sold ) ); ?></td>
								<td><?php echo esc_html( number_format_i18n( (int) $event->tickets_available ) ); ?></td>
								<td><?php echo esc_html( 'KES ' . number_format_i18n( (float) $event->revenue, 2 ) ); ?></td>
								<td><?php echo esc_html( number_format_i18n( (int) $event->total_registrations ) ); ?></td>
								<td><span class="cer-status-badge cer-status-<?php echo esc_attr( $event->status ); ?>"><?php echo esc_html( ucfirst( $event->status ) ); ?></span></td>
								<td><a href="<?php echo esc_url( cer_get_event_public_url( $event->slug ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( cer_get_event_public_url( $event->slug ) ); ?></a></td>
								<?php if ( $trash_view ) : ?><td><?php echo esc_html( date_i18n( 'M j, Y H:i', strtotime( $event->deleted_at ) ) ); ?></td><?php endif; ?>
								<td>
									<?php if ( ! $trash_view ) : ?>
									<div class="cer-event-radial-actions">
										<button type="button" class="button button-small cer-event-actions-trigger" aria-haspopup="dialog" aria-expanded="false" aria-controls="cer-event-actions-<?php echo absint( $event->id ); ?>"><?php esc_html_e( 'Actions', 'custom-event-registration' ); ?><span class="screen-reader-text">: <?php echo esc_html( $event->name ); ?></span></button>
										<dialog id="cer-event-actions-<?php echo absint( $event->id ); ?>" class="cer-event-actions-dialog" aria-label="<?php echo esc_attr( sprintf( __( 'Actions: %s', 'custom-event-registration' ), $event->name ) ); ?>">
											<button type="button" class="cer-radial-close" aria-label="<?php esc_attr_e( 'Close event actions', 'custom-event-registration' ); ?>"><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button>
									<?php else : ?>
									<details class="cer-row-actions">
										<summary><?php esc_html_e( 'Actions', 'custom-event-registration' ); ?><span class="screen-reader-text">: <?php echo esc_html( $event->name ); ?></span></summary>
									<?php endif; ?>
										<div class="cer-row-action-list">
									<?php if ( $trash_view ) : ?>
										<?php $restore_url = add_query_arg( array( 'page' => 'cer-events', 'cer_action' => 'restore_event', 'event_id' => $event->id ), admin_url( 'admin.php' ) ); ?>
										<a href="<?php echo esc_url( wp_nonce_url( $restore_url, 'cer_restore_event_' . absint( $event->id ) ) ); ?>" class="button button-small"><?php esc_html_e( 'Restore', 'custom-event-registration' ); ?></a>
										<?php $permanent_delete_url = add_query_arg( array( 'page' => 'cer-events', 'cer_action' => 'delete_event_permanently', 'event_id' => $event->id ), admin_url( 'admin.php' ) ); ?>
										<a href="<?php echo esc_url( wp_nonce_url( $permanent_delete_url, 'cer_delete_event_permanently_' . absint( $event->id ) ) ); ?>" class="button button-small cer-confirm-permanent-delete" data-confirm="<?php echo esc_attr__( 'Permanently delete this event and its content? This cannot be undone. Registration and payment history will be retained without event or ticket links.', 'custom-event-registration' ); ?>"><?php esc_html_e( 'Delete Permanently', 'custom-event-registration' ); ?></a>
									<?php else : ?>
										<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'cer-event-new', 'event_id' => $event->id ), admin_url( 'admin.php' ) ) ); ?>" class="button button-small"><?php esc_html_e( 'Edit', 'custom-event-registration' ); ?></a>
										<a href="<?php echo esc_url( cer_get_event_public_url( $event->slug ) ); ?>" class="button button-small" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open', 'custom-event-registration' ); ?></a>
										<?php if ( 'published' !== $event->status ) : ?>
											<a href="<?php echo esc_url( add_query_arg( array( 'event_id' => $event->id, 'cer_event_preview' => wp_create_nonce( 'cer_event_preview_' . $event->id ) ), home_url( '/event-registration/' ) ) ); ?>" class="button button-small" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Preview', 'custom-event-registration' ); ?></a>
										<?php endif; ?>
										<button type="button" class="button button-small cer-share-event" aria-expanded="false" aria-controls="cer-share-modal" data-event-title="<?php echo esc_attr( $event->name ); ?>" data-event-url="<?php echo esc_url( cer_get_event_public_url( $event->slug ) ); ?>"><span class="dashicons dashicons-share" aria-hidden="true"></span><?php esc_html_e( 'Share', 'custom-event-registration' ); ?></button>
										<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'cer-registrations', 'event_id_filter' => $event->id ), admin_url( 'admin.php' ) ) ); ?>" class="button button-small"><?php esc_html_e( 'View Registrations', 'custom-event-registration' ); ?></a>
										<?php $trash_url = add_query_arg( array( 'page' => 'cer-events', 'cer_action' => 'trash_event', 'event_id' => $event->id ), admin_url( 'admin.php' ) ); ?>
										<a href="<?php echo esc_url( wp_nonce_url( $trash_url, 'cer_trash_event_' . absint( $event->id ) ) ); ?>" class="button button-small cer-confirm-event-trash" data-confirm="<?php echo esc_attr__( 'Move this event to Trash? It can be restored for 30 days.', 'custom-event-registration' ); ?>"><?php esc_html_e( 'Move to Trash', 'custom-event-registration' ); ?></a>
									<?php endif; ?>
										</div>
									<?php if ( ! $trash_view ) : ?>
										</dialog>
									</div>
									<?php else : ?>
									</details>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="<?php echo $trash_view ? '12' : '11'; ?>"><?php echo $trash_view ? esc_html__( 'No events in the trash during the 30-day recovery period.', 'custom-event-registration' ) : esc_html__( 'No events match the current filters.', 'custom-event-registration' ); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

		<div class="cer-filter-bar" style="margin-top:18px;">
			<label for="cer-event-bulk-action"><?php esc_html_e( 'Bulk actions', 'custom-event-registration' ); ?></label>
			<select id="cer-event-bulk-action" name="bulk_action">
				<option value=""><?php esc_html_e( 'Bulk actions', 'custom-event-registration' ); ?></option>
				<?php if ( $trash_view ) : ?>
					<option value="restore"><?php esc_html_e( 'Restore', 'custom-event-registration' ); ?></option>
					<option value="delete_permanently"><?php esc_html_e( 'Delete Permanently', 'custom-event-registration' ); ?></option>
				<?php else : ?>
					<option value="publish"><?php esc_html_e( 'Publish', 'custom-event-registration' ); ?></option>
					<option value="close"><?php esc_html_e( 'Close', 'custom-event-registration' ); ?></option>
					<option value="delete"><?php esc_html_e( 'Move to Trash', 'custom-event-registration' ); ?></option>
				<?php endif; ?>
			</select>
			<input type="submit" class="button button-secondary" value="<?php esc_attr_e( 'Apply', 'custom-event-registration' ); ?>" />
		</div>
	</form>

	<?php
	$total_pages = max( 1, (int) ceil( $total_events / $per_page ) );
	$pagination_url = add_query_arg(
		array(
			'page' => 'cer-events',
			'event_status' => $status_filter,
			'event_month' => $month_filter,
			's' => $search,
			'view' => $trash_view ? 'trash' : '',
		),
		admin_url( 'admin.php' )
	);
	?>
	<?php cer_render_admin_pagination( $page, $total_pages, $pagination_url, 'paged', __( 'Event pages', 'custom-event-registration' ) ); ?>
	</div>
<div id="cer-share-modal" class="cer-share-modal" hidden>
	<div class="cer-share-modal-dialog" role="group" aria-labelledby="cer-share-modal-title">
		<button type="button" class="cer-share-modal-close" data-cer-share-close="1" aria-label="<?php esc_attr_e( 'Close share dialog', 'custom-event-registration' ); ?>"><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button>
		<h2 id="cer-share-modal-title"><?php esc_html_e( 'Share Event', 'custom-event-registration' ); ?></h2>
		<p class="cer-share-event-name"></p>
		<div class="cer-share-url-row">
			<label for="cer-share-url"><?php esc_html_e( 'Event URL', 'custom-event-registration' ); ?></label>
			<div class="cer-share-url-controls">
				<input type="text" id="cer-share-url" readonly />
				<button type="button" class="button" id="cer-copy-share-url" data-copied="<?php esc_attr_e( 'Copied', 'custom-event-registration' ); ?>" data-copy-error="<?php esc_attr_e( 'Copy failed. Select and copy the URL manually.', 'custom-event-registration' ); ?>"><span class="dashicons dashicons-admin-page" aria-hidden="true"></span><?php esc_html_e( 'Copy', 'custom-event-registration' ); ?></button>
			</div>
			<span class="cer-share-copy-status" role="status" aria-live="polite"></span>
		</div>
	</div>
		<div class="cer-share-platforms" aria-label="<?php esc_attr_e( 'Social sharing platforms', 'custom-event-registration' ); ?>">
			<a class="cer-share-platform cer-share-facebook" data-share-platform="facebook" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-facebook-alt" aria-hidden="true"></span><span><?php esc_html_e( 'Facebook', 'custom-event-registration' ); ?></span></a>
			<a class="cer-share-platform cer-share-twitter" data-share-platform="twitter" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-twitter" aria-hidden="true"></span><span><?php esc_html_e( 'X / Twitter', 'custom-event-registration' ); ?></span></a>
			<a class="cer-share-platform cer-share-linkedin" data-share-platform="linkedin" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-linkedin" aria-hidden="true"></span><span><?php esc_html_e( 'LinkedIn', 'custom-event-registration' ); ?></span></a>
			<a class="cer-share-platform cer-share-whatsapp" data-share-platform="whatsapp" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-format-chat" aria-hidden="true"></span><span><?php esc_html_e( 'WhatsApp', 'custom-event-registration' ); ?></span></a>
			<a class="cer-share-platform cer-share-email" data-share-platform="email"><span class="dashicons dashicons-email-alt" aria-hidden="true"></span><span><?php esc_html_e( 'Email', 'custom-event-registration' ); ?></span></a>
	</div>
</div>
</div>
<script>
	jQuery(function($){
		$(document).on('submit', '[data-cer-live-list="events"] form[method="post"]', function (event) {
			var action = $(this).find('[name="bulk_action"]').val();
			if ('delete' === action) {
				if (!window.confirm('<?php echo esc_js( __( 'Move the selected event(s) to trash? They can be restored for 30 days.', 'custom-event-registration' ) ); ?>')) {
					event.preventDefault();
					return;
				}
				$(this).find('[name="cer_confirm_trash"]').val('1');
			} else if ('delete_permanently' === action) {
				if (!window.confirm('<?php echo esc_js( __( 'Permanently delete the selected event(s) and their content? This cannot be undone. Registration and payment history will be retained without event or ticket links.', 'custom-event-registration' ) ); ?>')) {
					event.preventDefault();
					return;
				}
				$(this).find('[name="cer_confirm_permanent_delete"]').val('1');
			}
		});
		$(document).on('click', 'a.cer-confirm-event-trash', function (event) {
			if (!window.confirm($(this).data('confirm'))) {
				event.preventDefault();
				return;
			}
			var url = new URL(this.href, window.location.href);
			url.searchParams.set('cer_confirm_trash', '1');
			this.href = url.toString();
		});
		$(document).on('click', 'a.cer-confirm-permanent-delete', function (event) {
			if (!window.confirm($(this).data('confirm'))) {
				event.preventDefault();
				return;
			}
			var url = new URL(this.href, window.location.href);
			url.searchParams.set('cer_confirm_permanent_delete', '1');
			this.href = url.toString();
		});
	});
</script>
