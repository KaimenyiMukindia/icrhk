<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have permission to view this page.', 'custom-event-registration' ) );
}

global $wpdb;

$per_page = 20;
$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
$status_filter = isset( $_GET['event_status'] ) ? sanitize_key( wp_unslash( $_GET['event_status'] ) ) : '';
$month_filter = isset( $_GET['event_month'] ) ? sanitize_text_field( wp_unslash( $_GET['event_month'] ) ) : '';
$page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
$offset = ( $page - 1 ) * $per_page;

$events_table = $wpdb->prefix . 'evt_events';
$registrations_table = $wpdb->prefix . 'evt_registrations';
$where = array();
$bindings = array();

if ( $search ) {
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
	COALESCE(reg.tickets_sold, 0) AS tickets_sold, COALESCE(reg.revenue, 0) AS revenue
	FROM {$events_table} e
	LEFT JOIN (
		SELECT event_id,
			COUNT(*) AS total_registrations,
			SUM(CASE WHEN status IN ('paid','confirmed') THEN 1 ELSE 0 END) AS tickets_sold,
			SUM(CASE WHEN status IN ('paid','confirmed') THEN amount ELSE 0 END) AS revenue
		FROM {$registrations_table}
		GROUP BY event_id
	) reg ON reg.event_id = e.id";

$query = $select_sql . $where_sql . ' ORDER BY e.event_date DESC LIMIT %d OFFSET %d';
$prepared_query = $wpdb->prepare( $query, array_merge( $bindings, array( $per_page, $offset ) ) );
$events = $wpdb->get_results( $prepared_query );

$total_query = 'SELECT COUNT(*) FROM ' . $events_table . ' e' . $where_sql;
$total_prepared = $wpdb->prepare( $total_query, $bindings );
$total_events = (int) $wpdb->get_var( $total_prepared );

$bulk_action = isset( $_POST['bulk_action'] ) ? sanitize_key( wp_unslash( $_POST['bulk_action'] ) ) : '';
$event_ids = isset( $_POST['event_ids'] ) && is_array( $_POST['event_ids'] ) ? array_map( 'absint', $_POST['event_ids'] ) : array();
if ( ! empty( $event_ids ) && ! empty( $bulk_action ) && check_admin_referer( 'cer_event_bulk_action', 'cer_event_bulk_nonce' ) ) {
	foreach ( $event_ids as $event_id ) {
		if ( 'publish' === $bulk_action ) {
			$wpdb->update( $events_table, array( 'status' => 'published' ), array( 'id' => $event_id ), array( '%s' ), array( '%d' ) );
		} elseif ( 'close' === $bulk_action ) {
			$wpdb->update( $events_table, array( 'status' => 'closed' ), array( 'id' => $event_id ), array( '%s' ), array( '%d' ) );
		} elseif ( 'delete' === $bulk_action ) {
			$wpdb->delete( $events_table, array( 'id' => $event_id ), array( '%d' ) );
		}
	}
	wp_safe_redirect( add_query_arg( array( 'page' => 'cer-events' ), admin_url( 'admin.php' ) ) );
	exit;
}
?>
<div class="wrap cer-admin-shell">
	<div class="cer-admin-header">
		<h1><?php esc_html_e( 'All Events', 'custom-event-registration' ); ?></h1>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-event-new' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Add New Event', 'custom-event-registration' ); ?></a>
	</div>

	<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<input type="hidden" name="page" value="cer-events" />
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

	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=cer-events' ) ); ?>">
		<?php wp_nonce_field( 'cer_event_bulk_action', 'cer_event_bulk_nonce' ); ?>
		<div class="cer-table-wrap">
			<table class="cer-events-table">
				<thead>
					<tr>
						<th style="width:40px;"><input type="checkbox" id="cer-select-all-events" /></th>
						<th><?php esc_html_e( 'Event Title', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Date', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Venue', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Tickets Sold', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Revenue', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Registrations', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Status', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Front-End URL', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'custom-event-registration' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( ! empty( $events ) ) : ?>
						<?php foreach ( $events as $event ) : ?>
							<tr>
								<td><input type="checkbox" name="event_ids[]" value="<?php echo esc_attr( $event->id ); ?>" class="cer-event-checkbox" /></td>
								<td>
									<strong><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'cer-event-new', 'event_id' => $event->id ), admin_url( 'admin.php' ) ) ); ?>"><?php echo esc_html( $event->name ); ?></a></strong>
								</td>
								<td><?php echo esc_html( date_i18n( 'M j, Y', strtotime( $event->event_date ) ) ); ?></td>
								<td><?php echo esc_html( $event->venue ?: '—' ); ?></td>
								<td><?php echo esc_html( number_format_i18n( (int) $event->tickets_sold ) ); ?></td>
								<td><?php echo esc_html( 'KES ' . number_format_i18n( (float) $event->revenue, 2 ) ); ?></td>
								<td><?php echo esc_html( number_format_i18n( (int) $event->total_registrations ) ); ?></td>
								<td><span class="cer-status-badge cer-status-<?php echo esc_attr( $event->status ); ?>"><?php echo esc_html( ucfirst( $event->status ) ); ?></span></td>
								<td><a href="<?php echo esc_url( cer_get_event_public_url( $event->slug ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( cer_get_event_public_url( $event->slug ) ); ?></a></td>
								<td>
									<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'cer-event-new', 'event_id' => $event->id ), admin_url( 'admin.php' ) ) ); ?>" class="button button-small"><?php esc_html_e( 'Edit', 'custom-event-registration' ); ?></a>
										<a href="<?php echo esc_url( cer_get_event_public_url( $event->slug ) ); ?>" class="button button-small" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open', 'custom-event-registration' ); ?></a>
										<button type="button" class="button button-small cer-share-event" data-event-title="<?php echo esc_attr( $event->name ); ?>" data-event-url="<?php echo esc_url( cer_get_event_public_url( $event->slug ) ); ?>"><span class="dashicons dashicons-share" aria-hidden="true"></span><?php esc_html_e( 'Share', 'custom-event-registration' ); ?></button>
									<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'cer-registrations', 'event_id' => $event->id ), admin_url( 'admin.php' ) ) ); ?>" class="button button-small"><?php esc_html_e( 'View Registrations', 'custom-event-registration' ); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="10"><?php esc_html_e( 'No events match the current filters.', 'custom-event-registration' ); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

		<div class="cer-filter-bar" style="margin-top:18px;">
			<select name="bulk_action">
				<option value=""><?php esc_html_e( 'Bulk actions', 'custom-event-registration' ); ?></option>
				<option value="publish"><?php esc_html_e( 'Publish', 'custom-event-registration' ); ?></option>
				<option value="close"><?php esc_html_e( 'Close', 'custom-event-registration' ); ?></option>
				<option value="delete"><?php esc_html_e( 'Delete', 'custom-event-registration' ); ?></option>
			</select>
			<input type="submit" class="button button-secondary" value="<?php esc_attr_e( 'Apply', 'custom-event-registration' ); ?>" />
		</div>
	</form>

	<?php
	$total_pages = max( 1, (int) ceil( $total_events / $per_page ) );
	if ( $total_pages > 1 ) {
		$pagination_url = add_query_arg(
			array(
				'page' => 'cer-events',
				'event_status' => $status_filter,
				'event_month' => $month_filter,
				's' => $search,
			),
			admin_url( 'admin.php' )
		);
		echo '<div class="tablenav bottom"><div class="tablenav-pages">';
		for ( $i = 1; $i <= $total_pages; $i++ ) {
			$url = add_query_arg( 'paged', $i, $pagination_url );
			$class = ( $i === $page ) ? 'page-numbers current' : 'page-numbers';
			echo '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $i ) . '</a>';
		}
		echo '</div></div>';
	}
	?>
</div>
<div id="cer-share-modal" class="cer-share-modal" hidden>
	<div class="cer-share-modal-backdrop" data-cer-share-close="1"></div>
	<div class="cer-share-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="cer-share-modal-title" tabindex="-1">
		<button type="button" class="cer-share-modal-close" data-cer-share-close="1" aria-label="<?php esc_attr_e( 'Close share dialog', 'custom-event-registration' ); ?>"><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button>
		<h2 id="cer-share-modal-title"><?php esc_html_e( 'Share Event', 'custom-event-registration' ); ?></h2>
		<p class="cer-share-event-name"></p>
		<div class="cer-share-url-row">
			<label for="cer-share-url"><?php esc_html_e( 'Event URL', 'custom-event-registration' ); ?></label>
			<div class="cer-share-url-controls">
				<input type="text" id="cer-share-url" readonly />
				<button type="button" class="button" id="cer-copy-share-url"><span class="dashicons dashicons-admin-page" aria-hidden="true"></span><?php esc_html_e( 'Copy', 'custom-event-registration' ); ?></button>
			</div>
			<span class="cer-share-copy-status" role="status" aria-live="polite"></span>
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
		$('#cer-select-all-events').on('change', function () {
			$('.cer-event-checkbox').prop('checked', $(this).is(':checked'));
		});
	});
</script>
