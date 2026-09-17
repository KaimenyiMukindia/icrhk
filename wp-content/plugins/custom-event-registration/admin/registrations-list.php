<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have permission to view this page.', 'custom-event-registration' ) );
}

global $wpdb;

$events_table = $wpdb->prefix . 'evt_events';
$ticket_types_table = $wpdb->prefix . 'evt_ticket_types';
$registrations_table = $wpdb->prefix . 'evt_registrations';

$event_id_filter = isset( $_GET['event_id_filter'] ) ? absint( wp_unslash( $_GET['event_id_filter'] ) ) : 0;
$ticket_filter = isset( $_GET['ticket_filter'] ) ? absint( wp_unslash( $_GET['ticket_filter'] ) ) : 0;
$payment_filter = isset( $_GET['payment_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['payment_filter'] ) ) : '';
$status_filter = isset( $_GET['reg_status'] ) ? sanitize_key( wp_unslash( $_GET['reg_status'] ) ) : '';
$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
$date_start = isset( $_GET['date_start'] ) ? sanitize_text_field( wp_unslash( $_GET['date_start'] ) ) : '';
$date_end = isset( $_GET['date_end'] ) ? sanitize_text_field( wp_unslash( $_GET['date_end'] ) ) : '';
$page = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
$per_page = 20;
$offset = ( $page - 1 ) * $per_page;

$where = array();
$bindings = array();

if ( $event_id_filter ) {
	$where[] = 'r.event_id = %d';
	$bindings[] = $event_id_filter;
}
if ( $ticket_filter ) {
	$where[] = 'r.ticket_type_id = %d';
	$bindings[] = $ticket_filter;
}
if ( $payment_filter ) {
	$where[] = 'r.payment_method = %s';
	$bindings[] = $payment_filter;
}
if ( $status_filter ) {
	$where[] = 'r.status = %s';
	$bindings[] = $status_filter;
}
if ( $date_start ) {
	$start_datetime = DateTime::createFromFormat( '!Y-m-d', $date_start );
	if ( $start_datetime ) {
		$where[] = 'r.created_at >= %s';
		$bindings[] = $start_datetime->format( 'Y-m-d H:i:s' );
	}
}
if ( $date_end ) {
	$end_datetime = DateTime::createFromFormat( '!Y-m-d', $date_end );
	if ( $end_datetime ) {
		$end_datetime->modify( '+1 day' );
		$where[] = 'r.created_at < %s';
		$bindings[] = $end_datetime->format( 'Y-m-d H:i:s' );
	}
}
if ( $search ) {
	$search_hash = cer_registration_search_hash( $search );
	$where[] = '(r.registration_uuid = %s OR r.full_name_search_hash = %s OR r.email_search_hash = %s OR r.phone_search_hash = %s)';
	$bindings[] = $search;
	$bindings[] = $search_hash;
	$bindings[] = $search_hash;
	$bindings[] = $search_hash;
}

$where_sql = $where ? ' WHERE ' . implode( ' AND ', $where ) : '';

$registrations_query = "SELECT r.*, e.name AS event_name, tt.name AS ticket_name
	FROM {$registrations_table} r
	LEFT JOIN {$events_table} e ON e.id = r.event_id
	LEFT JOIN {$ticket_types_table} tt ON tt.id = r.ticket_type_id
	{$where_sql}
	ORDER BY r.created_at DESC
	LIMIT %d OFFSET %d";

$registrations = $wpdb->get_results( $wpdb->prepare( $registrations_query, array_merge( $bindings, array( $per_page, $offset ) ) ) );
$registrations = array_map( 'cer_decrypt_registration_row', $registrations ?: array() );
$total_registrations = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$registrations_table} r {$where_sql}", $bindings ) );

if ( isset( $_POST['bulk_action'] ) && isset( $_POST['registration_ids'] ) && check_admin_referer( 'cer_reg_bulk_action', 'cer_reg_bulk_nonce' ) ) {
	$action = sanitize_key( wp_unslash( $_POST['bulk_action'] ) );
	$ids = array_map( 'absint', (array) $_POST['registration_ids'] );

	foreach ( $ids as $id ) {
		if ( 'mark_paid' === $action ) {
			$result = cer_confirm_registration_paid( $id );
			if ( empty( $result['ok'] ) ) {
				set_transient( 'cer_registration_bulk_error', $result['message'] ?? __( 'Unable to confirm registration.', 'custom-event-registration' ), 30 );
			}
		} elseif ( 'mark_cancelled' === $action ) {
			$wpdb->update( $registrations_table, array( 'status' => 'cancelled', 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $id ), array( '%s', '%s' ), array( '%d' ) );
		} elseif ( 'delete' === $action ) {
			$wpdb->delete( $registrations_table, array( 'id' => $id ), array( '%d' ) );
		}
	}

	wp_safe_redirect( add_query_arg( array( 'page' => 'cer-registrations' ), admin_url( 'admin.php' ) ) );
	exit;
}

if ( isset( $_GET['cer_export'] ) && '1' === $_GET['cer_export'] && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['cer_export_nonce'] ?? '' ) ), 'cer_export_registrations' ) ) {
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=event-registrations.csv' );
	$output = fopen( 'php://output', 'w' );
	fputcsv( $output, array( 'ID', 'Event', 'Full Name', 'Email', 'Phone', 'Ticket Type', 'Amount', 'Payment Method', 'Status', 'Date' ) );
	foreach ( $wpdb->get_results( $wpdb->prepare( "SELECT r.*, e.name AS event_name, tt.name AS ticket_name FROM {$registrations_table} r LEFT JOIN {$events_table} e ON e.id = r.event_id LEFT JOIN {$ticket_types_table} tt ON tt.id = r.ticket_type_id {$where_sql} ORDER BY r.created_at DESC", $bindings ), ARRAY_A ) as $row ) {
		$row = cer_decrypt_registration_row( $row );
		fputcsv( $output, array( $row['id'], $row['event_name'] ?: '—', $row['full_name'], $row['email'], $row['phone'], $row['ticket_name'] ?: $row['ticket_type'], $row['amount'], $row['payment_method'], $row['status'], $row['created_at'] ) );
	}
	fclose( $output );
	exit;
}
?>
<div class="wrap cer-admin-shell">
	<div class="cer-admin-header">
		<h1><?php esc_html_e( 'Event Registrations', 'custom-event-registration' ); ?></h1>
		<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'page' => 'cer-registrations', 'cer_export' => '1', 'cer_export_nonce' => wp_create_nonce( 'cer_export_registrations' ) ), admin_url( 'admin.php' ) ), 'cer_export_registrations', 'cer_export_nonce' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Export CSV', 'custom-event-registration' ); ?></a>
	</div>

	<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<input type="hidden" name="page" value="cer-registrations" />
		<div class="cer-filter-bar">
			<div class="field">
				<label for="event-filter"><?php esc_html_e( 'Event', 'custom-event-registration' ); ?></label>
				<select id="event-filter" name="event_id_filter">
					<option value=""><?php esc_html_e( 'All Events', 'custom-event-registration' ); ?></option>
					<?php foreach ( $wpdb->get_results( "SELECT id, name FROM {$events_table} ORDER BY name ASC" ) as $event ) : ?>
						<option value="<?php echo esc_attr( $event->id ); ?>" <?php selected( $event_id_filter, $event->id ); ?>><?php echo esc_html( $event->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="field">
				<label for="ticket-filter"><?php esc_html_e( 'Ticket Type', 'custom-event-registration' ); ?></label>
				<select id="ticket-filter" name="ticket_filter">
					<option value=""><?php esc_html_e( 'All Tickets', 'custom-event-registration' ); ?></option>
					<?php foreach ( $wpdb->get_results( "SELECT id, name FROM {$ticket_types_table} ORDER BY name ASC" ) as $ticket ) : ?>
						<option value="<?php echo esc_attr( $ticket->id ); ?>" <?php selected( $ticket_filter, $ticket->id ); ?>><?php echo esc_html( $ticket->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="field">
				<label for="payment-filter"><?php esc_html_e( 'Payment Method', 'custom-event-registration' ); ?></label>
				<select id="payment-filter" name="payment_filter">
					<option value=""><?php esc_html_e( 'All Methods', 'custom-event-registration' ); ?></option>
					<option value="M-Pesa" <?php selected( $payment_filter, 'M-Pesa' ); ?>>M-Pesa</option>
					<option value="Card" <?php selected( $payment_filter, 'Card' ); ?>>Card</option>
				</select>
			</div>
			<div class="field">
				<label for="reg-status"><?php esc_html_e( 'Status', 'custom-event-registration' ); ?></label>
				<select id="reg-status" name="reg_status">
					<option value=""><?php esc_html_e( 'All Statuses', 'custom-event-registration' ); ?></option>
					<option value="pending" <?php selected( $status_filter, 'pending' ); ?>><?php esc_html_e( 'Pending', 'custom-event-registration' ); ?></option>
					<option value="confirmed" <?php selected( $status_filter, 'confirmed' ); ?>><?php esc_html_e( 'Confirmed', 'custom-event-registration' ); ?></option>
					<option value="paid" <?php selected( $status_filter, 'paid' ); ?>><?php esc_html_e( 'Paid', 'custom-event-registration' ); ?></option>
					<option value="cancelled" <?php selected( $status_filter, 'cancelled' ); ?>><?php esc_html_e( 'Cancelled', 'custom-event-registration' ); ?></option>
				</select>
			</div>
			<div class="field">
				<label for="date-start"><?php esc_html_e( 'Start Date', 'custom-event-registration' ); ?></label>
				<input id="date-start" type="date" name="date_start" value="<?php echo esc_attr( $date_start ); ?>" />
			</div>
			<div class="field">
				<label for="date-end"><?php esc_html_e( 'End Date', 'custom-event-registration' ); ?></label>
				<input id="date-end" type="date" name="date_end" value="<?php echo esc_attr( $date_end ); ?>" />
			</div>
			<div class="field" style="min-width:260px;">
				<label for="reg-search"><?php esc_html_e( 'Search', 'custom-event-registration' ); ?></label>
				<input id="reg-search" type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="Exact name, email, phone, or registration UUID" />
			</div>
			<div class="field" style="justify-content:flex-end;">
				<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Filter', 'custom-event-registration' ); ?>" />
			</div>
		</div>
	</form>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=cer-registrations' ) ); ?>">
		<?php wp_nonce_field( 'cer_reg_bulk_action', 'cer_reg_bulk_nonce' ); ?>
		<div class="cer-table-wrap">
			<table class="cer-events-table">
				<thead>
					<tr>
						<th style="width:40px;"><input type="checkbox" id="cer-select-all-registrations" /></th>
						<th><?php esc_html_e( 'ID', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Event', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Full Name', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Email', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Phone', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Ticket Type', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Amount', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Payment Method', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Status', 'custom-event-registration' ); ?></th>
						<th><?php esc_html_e( 'Date', 'custom-event-registration' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( ! empty( $registrations ) ) : ?>
						<?php foreach ( $registrations as $registration ) : ?>
							<tr>
								<td><input type="checkbox" name="registration_ids[]" value="<?php echo esc_attr( $registration->id ); ?>" class="cer-registration-checkbox" /></td>
								<td>#<?php echo esc_html( $registration->id ); ?></td>
								<td><?php echo esc_html( $registration->event_name ?: '—' ); ?></td>
								<td><?php echo esc_html( $registration->full_name ); ?></td>
								<td><?php echo esc_html( $registration->email ); ?></td>
								<td><?php echo esc_html( $registration->phone ); ?></td>
								<td><?php echo esc_html( $registration->ticket_name ?: $registration->ticket_type ); ?></td>
								<td><?php echo esc_html( 'KES ' . number_format_i18n( (float) $registration->amount, 2 ) ); ?></td>
								<td><?php echo esc_html( $registration->payment_method ); ?></td>
								<td><span class="cer-status-badge cer-status-<?php echo esc_attr( strtolower( $registration->status ) ); ?>"><?php echo esc_html( ucfirst( $registration->status ) ); ?></span></td>
								<td><?php echo esc_html( date_i18n( 'M j, Y', strtotime( $registration->created_at ) ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="11"><?php esc_html_e( 'No registrations match the current filters.', 'custom-event-registration' ); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

		<div class="cer-filter-bar" style="margin-top:18px;">
			<select name="bulk_action">
				<option value=""><?php esc_html_e( 'Bulk actions', 'custom-event-registration' ); ?></option>
				<option value="mark_paid"><?php esc_html_e( 'Mark as Paid', 'custom-event-registration' ); ?></option>
				<option value="mark_cancelled"><?php esc_html_e( 'Mark as Cancelled', 'custom-event-registration' ); ?></option>
				<option value="delete"><?php esc_html_e( 'Delete', 'custom-event-registration' ); ?></option>
			</select>
			<input type="submit" class="button button-secondary" value="<?php esc_attr_e( 'Apply', 'custom-event-registration' ); ?>" />
		</div>
	</form>

	<?php
	$total_pages = max( 1, (int) ceil( $total_registrations / $per_page ) );
	if ( $total_pages > 1 ) :
		?>
		<div class="tablenav bottom">
			<div class="tablenav-pages">
				<?php
				for ( $i = 1; $i <= $total_pages; $i++ ) {
					$url = add_query_arg( array(
						'page' => 'cer-registrations',
						'event_id_filter' => $event_id_filter,
						'ticket_filter' => $ticket_filter,
						'payment_filter' => $payment_filter,
						'reg_status' => $status_filter,
						'date_start' => $date_start,
						'date_end' => $date_end,
						's' => $search,
						'paged' => $i,
					), admin_url( 'admin.php' ) );
					printf( '<a href="%s" class="page-numbers %s">%d</a>', esc_url( $url ), ( $i === $page ) ? 'current' : '', $i );
				}
				?>
			</div>
		</div>
		<?php
	endif;
	?>
</div>
<script>
	jQuery(function($){
		$('#cer-select-all-registrations').on('change', function () {
			$('.cer-registration-checkbox').prop('checked', $(this).is(':checked'));
		});
	});
</script>
