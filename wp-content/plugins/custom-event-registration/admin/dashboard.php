<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have permission to view this page.', 'custom-event-registration' ) );
}

global $wpdb;

$cache_key = 'cer_dashboard_metrics';
$metrics = get_transient( $cache_key );

if ( false === $metrics ) {
	$events_table = $wpdb->prefix . 'evt_events';
	$registrations_table = $wpdb->prefix . 'evt_registrations';
	$sponsorships_table = $wpdb->prefix . 'evt_sponsorships';

	$event_metrics = $wpdb->get_row( "SELECT COUNT(*) AS total_events, SUM(status = 'published') AS published_events FROM {$events_table}", ARRAY_A );
	$registration_metrics = $wpdb->get_row( "SELECT COUNT(*) AS total_registrations, SUM(status IN ('paid','confirmed')) AS tickets_sold, COALESCE(SUM(CASE WHEN status IN ('paid','confirmed') THEN amount ELSE 0 END), 0) AS revenue_collected FROM {$registrations_table}", ARRAY_A );
	$inventory_metrics = $wpdb->get_row( "SELECT COALESCE(SUM(CASE WHEN quantity_available IS NULL THEN 0 ELSE GREATEST(quantity_available - quantity_sold, 0) END), 0) AS tickets_available FROM {$wpdb->prefix}evt_ticket_types", ARRAY_A );
	$sponsorship_packages = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$sponsorships_table}" );
	$total_events = (int) ( $event_metrics['total_events'] ?? 0 );
	$published_events = (int) ( $event_metrics['published_events'] ?? 0 );
	$total_registrations = (int) ( $registration_metrics['total_registrations'] ?? 0 );
	$tickets_sold = (int) ( $registration_metrics['tickets_sold'] ?? 0 );
	$tickets_available = (int) ( $inventory_metrics['tickets_available'] ?? 0 );
	$revenue_collected = (float) ( $registration_metrics['revenue_collected'] ?? 0 );

	$metrics = array(
		'total_events' => $total_events,
		'published_events' => $published_events,
		'total_registrations' => $total_registrations,
		'tickets_sold' => $tickets_sold,
		'tickets_available' => $tickets_available,
		'revenue_collected' => $revenue_collected,
		'sponsorship_packages' => $sponsorship_packages,
	);

	set_transient( $cache_key, $metrics, 300 );
}

$recent_registrations = $wpdb->get_results(
	"SELECT r.*, e.name AS event_name, tt.name AS ticket_name
	 FROM {$wpdb->prefix}evt_registrations r
	 LEFT JOIN {$wpdb->prefix}evt_events e ON e.id = r.event_id
	 LEFT JOIN {$wpdb->prefix}evt_ticket_types tt ON tt.id = r.ticket_type_id
	 ORDER BY r.created_at DESC
	 LIMIT 10",
	ARRAY_A
);
$recent_registrations = array_map( 'cer_decrypt_registration_row', $recent_registrations ?: array() );
?>
<div class="wrap cer-admin-shell">
	<div class="cer-admin-header">
		<h1><?php esc_html_e( 'Event Dashboard', 'custom-event-registration' ); ?></h1>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-event-new' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Create New Event', 'custom-event-registration' ); ?></a>
	</div>

	<div class="cer-dashboard-grid">
		<div class="cer-metric-card">
			<span class="label"><?php esc_html_e( 'Total Events', 'custom-event-registration' ); ?></span>
			<p class="value"><?php echo esc_html( number_format_i18n( $metrics['total_events'] ) ); ?></p>
			<span class="trend"><?php esc_html_e( 'All events in the system', 'custom-event-registration' ); ?></span>
		</div>
		<div class="cer-metric-card">
			<span class="label"><?php esc_html_e( 'Total Registrations', 'custom-event-registration' ); ?></span>
			<p class="value"><?php echo esc_html( number_format_i18n( $metrics['total_registrations'] ) ); ?></p>
			<span class="trend"><?php esc_html_e( 'All time', 'custom-event-registration' ); ?></span>
		</div>
		<div class="cer-metric-card">
			<span class="label"><?php esc_html_e( 'Tickets Sold', 'custom-event-registration' ); ?></span>
			<p class="value"><?php echo esc_html( number_format_i18n( $metrics['tickets_sold'] ) ); ?></p>
			<span class="trend"><?php esc_html_e( 'Paid or confirmed', 'custom-event-registration' ); ?></span>
		</div>
		<div class="cer-metric-card">
			<span class="label"><?php esc_html_e( 'Tickets Available', 'custom-event-registration' ); ?></span>
			<p class="value"><?php echo esc_html( number_format_i18n( $metrics['tickets_available'] ) ); ?></p>
			<span class="trend"><?php esc_html_e( 'Currently remaining', 'custom-event-registration' ); ?></span>
		</div>
		<div class="cer-metric-card">
			<span class="label"><?php esc_html_e( 'Revenue Collected', 'custom-event-registration' ); ?></span>
			<p class="value"><?php echo esc_html( 'KES ' . number_format_i18n( $metrics['revenue_collected'], 2 ) ); ?></p>
			<span class="trend"><?php esc_html_e( 'Successful registrations', 'custom-event-registration' ); ?></span>
		</div>
		<div class="cer-metric-card">
			<span class="label"><?php esc_html_e( 'Sponsorship Packages', 'custom-event-registration' ); ?></span>
			<p class="value"><?php echo esc_html( number_format_i18n( $metrics['sponsorship_packages'] ) ); ?></p>
			<span class="trend"><?php esc_html_e( 'Available packages', 'custom-event-registration' ); ?></span>
		</div>
	</div>

	<div class="cer-panel-grid">
		<div class="cer-panel">
			<div class="panel-header">
				<h2><?php esc_html_e( 'Recent Registrations', 'custom-event-registration' ); ?></h2>
			</div>
			<div class="panel-body">
				<div class="cer-table-wrap">
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
			</div>
		</div>
	</div>

	<div class="cer-panel-grid" style="margin-top:24px;">
		<div class="cer-panel">
			<div class="panel-header">
				<h3><?php esc_html_e( 'Quick Links', 'custom-event-registration' ); ?></h3>
			</div>
			<div class="panel-body">
				<div class="cer-dashboard-actions">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-event-new' ) ); ?>" class="cer-action-link"><?php esc_html_e( 'Create New Event', 'custom-event-registration' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-registrations' ) ); ?>" class="cer-action-link"><?php esc_html_e( 'View All Registrations', 'custom-event-registration' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=cer-events' ) ); ?>" class="cer-action-link"><?php esc_html_e( 'Manage Events', 'custom-event-registration' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
