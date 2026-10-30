<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have permission to view this page.', 'custom-event-registration' ) );
}

global $wpdb;

$events_table = $wpdb->prefix . 'evt_events';
$registrations_table = $wpdb->prefix . 'evt_registrations';
$tickets_table = $wpdb->prefix . 'evt_ticket_types';
$sponsorships_table = $wpdb->prefix . 'evt_sponsorships';
$event_metrics = $wpdb->get_row( "SELECT COUNT(*) AS total_events, SUM(status = 'published') AS published_events FROM {$events_table} WHERE deleted_at IS NULL", ARRAY_A );
$metrics = array(
	'total_events' => (int) ( $event_metrics['total_events'] ?? 0 ),
	'published_events' => (int) ( $event_metrics['published_events'] ?? 0 ),
	'total_registrations' => 0,
	'tickets_sold' => 0,
	'tickets_available' => 0,
	'revenue_collected' => 0,
	'sponsorship_packages' => 0,
);

$event_page_size = 5;
$total_latest_events = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$events_table} WHERE deleted_at IS NULL" );
$total_event_pages = max( 1, (int) ceil( $total_latest_events / $event_page_size ) );
$selected_event_id = isset( $_GET['event_id'] ) ? absint( wp_unslash( $_GET['event_id'] ) ) : 0;
$selected_event = $selected_event_id
	? $wpdb->get_row( $wpdb->prepare( "SELECT id, name, status, created_at FROM {$events_table} WHERE id = %d AND deleted_at IS NULL", $selected_event_id ), ARRAY_A )
	: null;
$events_page = isset( $_GET['events_paged'] ) ? max( 1, absint( wp_unslash( $_GET['events_paged'] ) ) ) : 1;
if ( $selected_event ) {
	$event_position = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*) FROM {$events_table}
			WHERE deleted_at IS NULL AND (created_at > %s OR (created_at = %s AND id > %d))",
			$selected_event['created_at'],
			$selected_event['created_at'],
			$selected_event_id
		)
	);
	$events_page = (int) floor( $event_position / $event_page_size ) + 1;
}
$events_page = min( $events_page, $total_event_pages );
$latest_events = $wpdb->get_results(
	$wpdb->prepare(
		"SELECT id, name, status FROM {$events_table}
		WHERE deleted_at IS NULL
		ORDER BY created_at DESC, id DESC
		LIMIT %d OFFSET %d",
		$event_page_size,
		( $events_page - 1 ) * $event_page_size
	),
	ARRAY_A
);
if ( ! $selected_event && ! empty( $latest_events ) ) {
	$selected_event = $latest_events[0];
	$selected_event_id = (int) $selected_event['id'];
}

if ( $selected_event_id ) {
	$registration_metrics = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT SUM(deleted_at IS NULL) AS total_registrations,
				SUM(status IN ('paid','confirmed')) AS tickets_sold,
				COALESCE(SUM(CASE WHEN status IN ('paid','confirmed') THEN amount ELSE 0 END), 0) AS revenue_collected
			FROM {$registrations_table}
			WHERE event_id = %d",
			$selected_event_id
		),
		ARRAY_A
	);
	$inventory_metrics = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT COALESCE(SUM(CASE WHEN quantity_available IS NULL THEN 0 ELSE GREATEST(quantity_available - quantity_sold, 0) END), 0) AS tickets_available
			FROM {$tickets_table}
			WHERE event_id = %d AND deleted_at IS NULL",
			$selected_event_id
		),
		ARRAY_A
	);
	$metrics['total_registrations'] = (int) ( $registration_metrics['total_registrations'] ?? 0 );
	$metrics['tickets_sold'] = (int) ( $registration_metrics['tickets_sold'] ?? 0 );
	$metrics['tickets_available'] = (int) ( $inventory_metrics['tickets_available'] ?? 0 );
	$metrics['revenue_collected'] = (float) ( $registration_metrics['revenue_collected'] ?? 0 );
	$metrics['sponsorship_packages'] = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$sponsorships_table} WHERE event_id = %d AND deleted_at IS NULL", $selected_event_id ) );
}

$per_page = 5;
$total_recent_registrations = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}evt_registrations WHERE deleted_at IS NULL" );
$total_pages = max( 1, (int) ceil( $total_recent_registrations / $per_page ) );
$current_page = isset( $_GET['paged'] ) ? max( 1, absint( wp_unslash( $_GET['paged'] ) ) ) : 1;
$current_page = min( $current_page, $total_pages );
$offset = ( $current_page - 1 ) * $per_page;
$recent_registrations = $wpdb->get_results(
	$wpdb->prepare(
		"SELECT r.*, e.name AS event_name, tt.name AS ticket_name
		FROM {$wpdb->prefix}evt_registrations r
		LEFT JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id
		LEFT JOIN {$wpdb->prefix}evt_ticket_types tt ON tt.id = r.ticket_type_id
		WHERE r.deleted_at IS NULL
		ORDER BY r.created_at DESC
		LIMIT %d OFFSET %d",
		$per_page,
		$offset
	),
	ARRAY_A
);
$recent_registrations = array_map( 'cer_decrypt_registration_row', $recent_registrations ?: array() );
?>
<div class="wrap cer-admin-shell cer-dashboard">
	<hr class="wp-header-end" />
	<?php cer_render_trash_admin_notice(); ?>
	<header class="cer-admin-header cer-dashboard-header">
		<div class="cer-dashboard-heading">
			<h1><?php esc_html_e( 'Event Dashboard', 'custom-event-registration' ); ?></h1>
			<span class="cer-dashboard-event-count"><?php printf( esc_html__( '%s total events', 'custom-event-registration' ), esc_html( number_format_i18n( $metrics['total_events'] ) ) ); ?></span>
		</div>
		<nav class="cer-dashboard-header-actions" aria-label="<?php esc_attr_e( 'Dashboard actions', 'custom-event-registration' ); ?>">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-event-new' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Create New Event', 'custom-event-registration' ); ?></a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-events' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Manage Events', 'custom-event-registration' ); ?></a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-dashboard' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Refresh', 'custom-event-registration' ); ?></a>
		</nav>
	</header>

	<div class="cer-metric-groups">
		<section class="cer-metric-group cer-metric-group-core" aria-labelledby="cer-core-metrics-title">
			<h2 id="cer-core-metrics-title"><?php esc_html_e( 'Event Core Metrics', 'custom-event-registration' ); ?></h2>
			<p class="cer-metrics-event"><?php echo $selected_event ? esc_html( sprintf( __( 'Metrics for: %s', 'custom-event-registration' ), $selected_event['name'] ) ) : esc_html__( 'Select an event to view metrics.', 'custom-event-registration' ); ?></p>
			<div class="cer-dashboard-grid">
				<div class="cer-metric-card">
					<span class="label"><?php esc_html_e( 'Total Registrations', 'custom-event-registration' ); ?></span>
					<p class="value"><?php echo esc_html( number_format_i18n( $metrics['total_registrations'] ) ); ?></p>
					<span class="trend"><?php esc_html_e( 'Active registrations for this event', 'custom-event-registration' ); ?></span>
				</div>
				<div class="cer-metric-card">
					<span class="label"><?php esc_html_e( 'Tickets Sold', 'custom-event-registration' ); ?></span>
					<p class="value"><?php echo esc_html( number_format_i18n( $metrics['tickets_sold'] ) ); ?></p>
					<span class="trend"><?php esc_html_e( 'Paid or confirmed registrations', 'custom-event-registration' ); ?></span>
				</div>
				<div class="cer-metric-card">
					<span class="label"><?php esc_html_e( 'Tickets Available', 'custom-event-registration' ); ?></span>
					<p class="value"><?php echo esc_html( number_format_i18n( $metrics['tickets_available'] ) ); ?></p>
					<span class="trend"><?php esc_html_e( 'Remaining across this event’s ticket types', 'custom-event-registration' ); ?></span>
				</div>
			</div>
		</section>

		<section class="cer-metric-group cer-metric-group-financial" aria-labelledby="cer-financial-metrics-title">
			<h2 id="cer-financial-metrics-title"><?php esc_html_e( 'Financial Metrics', 'custom-event-registration' ); ?></h2>
			<div class="cer-dashboard-grid">
				<div class="cer-metric-card">
					<span class="label"><?php esc_html_e( 'Revenue Collected', 'custom-event-registration' ); ?></span>
					<p class="value"><?php echo esc_html( 'KES ' . number_format_i18n( $metrics['revenue_collected'], 2 ) ); ?></p>
					<span class="trend"><?php esc_html_e( 'Amounts from paid or confirmed registrations', 'custom-event-registration' ); ?></span>
				</div>
				<div class="cer-metric-card">
					<span class="label"><?php esc_html_e( 'Sponsorship Packages', 'custom-event-registration' ); ?></span>
					<p class="value"><?php echo esc_html( number_format_i18n( $metrics['sponsorship_packages'] ) ); ?></p>
					<span class="trend"><?php esc_html_e( 'Packages for this event', 'custom-event-registration' ); ?></span>
				</div>
			</div>
		</section>
	</div>

	<section class="cer-latest-events cer-panel" aria-labelledby="cer-latest-events-title">
		<div class="panel-header">
			<h2 id="cer-latest-events-title"><?php esc_html_e( 'Latest Events', 'custom-event-registration' ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-events' ) ); ?>"><?php esc_html_e( 'View all events', 'custom-event-registration' ); ?></a>
		</div>
		<div class="panel-body">
			<?php if ( ! empty( $latest_events ) ) : ?>
				<ul class="cer-latest-event-list">
					<?php foreach ( $latest_events as $event ) : ?>
						<li class="<?php echo (int) $event['id'] === $selected_event_id ? 'is-selected' : ''; ?>">
							<a class="cer-latest-event-select" href="<?php echo esc_url( add_query_arg( array( 'page' => 'cer-dashboard', 'event_id' => $event['id'] ), admin_url( 'admin.php' ) ) ); ?>" <?php echo (int) $event['id'] === $selected_event_id ? 'aria-current="true"' : ''; ?>>
								<strong><?php echo esc_html( $event['name'] ); ?></strong>
								<span class="cer-status-badge cer-status-<?php echo esc_attr( $event['status'] ); ?>"><?php echo esc_html( ucfirst( $event['status'] ) ); ?></span>
								<span class="screen-reader-text"><?php esc_html_e( 'Select metrics for this event', 'custom-event-registration' ); ?></span>
							</a>
							<a class="cer-latest-event-manage" href="<?php echo esc_url( add_query_arg( array( 'page' => 'cer-event-new', 'event_id' => $event['id'] ), admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Manage', 'custom-event-registration' ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php if ( $total_event_pages > 1 ) : ?>
					<div class="cer-dashboard-pagination">
						<?php
						cer_render_admin_pagination( $events_page, $total_event_pages, admin_url( 'admin.php?page=cer-dashboard' ), 'events_paged', __( 'Latest event pages', 'custom-event-registration' ) );
						?>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No events yet.', 'custom-event-registration' ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="cer-quick-links cer-panel" aria-labelledby="cer-quick-links-title">
		<div class="panel-header">
			<h2 id="cer-quick-links-title"><?php esc_html_e( 'Quick Links', 'custom-event-registration' ); ?></h2>
		</div>
		<div class="panel-body">
			<div class="cer-dashboard-actions">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-event-new' ) ); ?>" class="cer-action-link"><?php esc_html_e( 'Create New Event', 'custom-event-registration' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-registrations' ) ); ?>" class="cer-action-link"><?php esc_html_e( 'View All Registrations', 'custom-event-registration' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-events' ) ); ?>" class="cer-action-link"><?php esc_html_e( 'Manage Events', 'custom-event-registration' ); ?></a>
			</div>
		</div>
	</section>

	<section class="cer-panel cer-recent-registrations" aria-labelledby="cer-recent-registrations-title">
		<div class="panel-header">
			<h2 id="cer-recent-registrations-title"><?php esc_html_e( 'Recent Registrations', 'custom-event-registration' ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-registrations' ) ); ?>"><?php esc_html_e( 'View all registrations', 'custom-event-registration' ); ?></a>
		</div>
		<div class="panel-body">
			<div class="cer-table-wrap" tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Recent registrations table, scroll to see all columns', 'custom-event-registration' ); ?>">
				<table class="cer-small-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Event', 'custom-event-registration' ); ?></th>
							<th><?php esc_html_e( 'Name', 'custom-event-registration' ); ?></th>
							<th><?php esc_html_e( 'Email', 'custom-event-registration' ); ?></th>
							<th><?php esc_html_e( 'Ticket', 'custom-event-registration' ); ?></th>
							<th><?php esc_html_e( 'Amount', 'custom-event-registration' ); ?></th>
							<th><?php esc_html_e( 'Status', 'custom-event-registration' ); ?></th>
							<th><?php esc_html_e( 'Date', 'custom-event-registration' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $recent_registrations ) ) : ?>
							<?php foreach ( $recent_registrations as $registration ) : ?>
								<tr>
									<td><?php echo esc_html( $registration['event_name'] ?: __( 'Unassigned', 'custom-event-registration' ) ); ?></td>
									<td><?php echo esc_html( $registration['full_name'] ); ?></td>
									<td><?php echo esc_html( $registration['email'] ); ?></td>
									<td><?php echo esc_html( $registration['ticket_name'] ?: $registration['ticket_type'] ); ?></td>
									<td><?php echo esc_html( 'KES ' . number_format_i18n( (float) $registration['amount'], 2 ) ); ?></td>
									<td><span class="cer-status-badge cer-status-<?php echo esc_attr( strtolower( $registration['status'] ) ); ?>"><?php echo esc_html( ucfirst( $registration['status'] ) ); ?></span></td>
									<td><?php echo esc_html( date_i18n( 'M j, Y', strtotime( $registration['created_at'] ) ) ); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr>
								<td colspan="7"><?php esc_html_e( 'No registrations yet.', 'custom-event-registration' ); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
			<div class="cer-dashboard-pagination">
				<?php
				cer_render_admin_pagination( $current_page, $total_pages, add_query_arg( 'event_id', $selected_event_id, admin_url( 'admin.php?page=cer-dashboard' ) ), 'paged', __( 'Recent registration pages', 'custom-event-registration' ) );
				?>
			</div>
		</div>
	</section>

	<nav class="cer-mobile-radial" aria-label="<?php esc_attr_e( 'Dashboard quick actions', 'custom-event-registration' ); ?>">
		<div class="cer-mobile-radial-menu" id="cer-mobile-radial-menu" hidden>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-event-new' ) ); ?>"><?php esc_html_e( 'Create Event', 'custom-event-registration' ); ?></a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-registrations' ) ); ?>"><?php esc_html_e( 'Registrations', 'custom-event-registration' ); ?></a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-events' ) ); ?>"><?php esc_html_e( 'Manage Events', 'custom-event-registration' ); ?></a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-dashboard' ) ); ?>"><?php esc_html_e( 'Refresh', 'custom-event-registration' ); ?></a>
		</div>
		<button type="button" class="cer-mobile-radial-trigger" aria-expanded="false" aria-controls="cer-mobile-radial-menu" aria-label="<?php esc_attr_e( 'Open dashboard quick actions', 'custom-event-registration' ); ?>" title="<?php esc_attr_e( 'Open dashboard quick actions', 'custom-event-registration' ); ?>" data-close-label="<?php esc_attr_e( 'Close dashboard quick actions', 'custom-event-registration' ); ?>">
			<span class="dashicons dashicons-plus" aria-hidden="true"></span>
		</button>
	</nav>
</div>
