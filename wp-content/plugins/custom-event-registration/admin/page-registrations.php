<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'CER_Registration_List_Table' ) ) {
    return;
}

$registration_table = new CER_Registration_List_Table();
$registration_table->prepare_items();

if ( isset( $_GET['cer_action'] ) && isset( $_GET['registration_id'] ) && current_user_can( 'manage_options' ) ) {
    $action = sanitize_text_field( wp_unslash( $_GET['cer_action'] ) );
    $registration_id = absint( wp_unslash( $_GET['registration_id'] ) );
    $nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

    if ( 'toggle_status' === $action && wp_verify_nonce( $nonce, 'cer_toggle_status_' . $registration_id ) ) {
        global $wpdb;
        $table = $wpdb->prefix . 'evt_registrations';
        $status = $wpdb->get_var( $wpdb->prepare( "SELECT status FROM $table WHERE id = %d", $registration_id ) );
        $new_status = 'pending' === $status ? 'confirmed' : 'pending';
        $wpdb->update( $table, array( 'status' => $new_status, 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $registration_id ), array( '%s', '%s' ), array( '%d' ) );
        wp_safe_redirect( remove_query_arg( array( 'cer_action', 'registration_id', '_wpnonce' ) ) );
        exit;
    }
    if ( 'delete' === $action && wp_verify_nonce( $nonce, 'cer_delete_' . $registration_id ) ) {
        if ( '1' !== wp_unslash( $_GET['cer_confirm_trash'] ?? '' ) ) {
            wp_die( esc_html__( 'Confirm moving this registration to Trash. It will be recoverable for 30 days.', 'custom-event-registration' ) );
        }
        $result = cer_trash_dashboard_record( 'registration', $registration_id );
        if ( is_wp_error( $result ) ) {
            wp_die( esc_html( $result->get_error_message() ) );
        }
        wp_safe_redirect( add_query_arg( array( 'page' => 'event-registrations', 'cer_notice' => 'trashed' ), admin_url( 'admin.php' ) ) );
        exit;
    }
}

if ( isset( $_GET['cer_export'] ) && $_GET['cer_export'] === '1' && wp_verify_nonce( $_GET['cer_export_nonce'] ?? '', 'cer_export_registrations' ) ) {
    global $wpdb;
    $table = $wpdb->prefix . 'evt_registrations';
    $trash_view = isset( $_GET['view'] ) && 'trash' === sanitize_key( wp_unslash( $_GET['view'] ) );
    $where = $trash_view
        ? array( $wpdb->prepare( 'deleted_at IS NOT NULL AND deleted_at >= %s', cer_dashboard_trash_cutoff() ) )
        : array( 'deleted_at IS NULL' );
    $status = isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : '';
    $ticket_type = isset( $_REQUEST['ticket_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['ticket_type'] ) ) : '';
    if ( $status ) {
        $where[] = $wpdb->prepare( 'status = %s', $status );
    }
    if ( $ticket_type ) {
        $where[] = $wpdb->prepare( 'ticket_type = %s', $ticket_type );
    }
    $where_sql = ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '';
    $rows = $wpdb->get_results( "SELECT * FROM $table $where_sql ORDER BY created_at DESC", ARRAY_A );
    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename=event-registrations.csv' );
    $output = fopen( 'php://output', 'w' );
    fputcsv( $output, array( 'ID', 'Name', 'Email', 'Phone', 'Ticket Type', 'Payment Method', 'Amount', 'Notes', 'Status', 'Created At', 'Updated At' ) );
    foreach ( $rows as $row ) {
        fputcsv( $output, array(
            $row['id'],
            $row['full_name'],
            $row['email'],
            $row['phone'],
            $row['ticket_type'],
            $row['payment_method'],
            $row['amount'],
            $row['notes'],
            $row['status'],
            $row['created_at'],
            $row['updated_at'],
        ) );
    }
    exit;
}

?>
<div class="wrap">
    <?php cer_render_trash_admin_notice(); ?>
    <h1><?php esc_html_e( 'Event Registrations', 'custom-event-registration' ); ?></h1>
    <form method="get">
        <input type="hidden" name="page" value="event-registrations" />
        <?php $registration_table->search_box( esc_html__( 'Search Registrations', 'custom-event-registration' ), 'cer-search' ); ?>
        <?php $registration_table->display(); ?>
        <?php wp_nonce_field( 'cer_bulk_action', 'cer_bulk_action_nonce' ); ?>
        <input type="hidden" name="cer_confirm_trash" value="0" />
    </form>
</div>
<script>
	(function () {
		const form = document.querySelector('.wrap form');
		if (!form) {
			return;
		}
		form.addEventListener('submit', function (event) {
			const action = this.querySelector('[name="action"]');
			const action2 = this.querySelector('[name="action2"]');
			const selectedAction = action2 && action2.value ? action2 : action;
			if (selectedAction && 'delete' === selectedAction.value) {
				if (!window.confirm('<?php echo esc_js( __( 'Move the selected registration(s) to trash? They can be restored for 30 days.', 'custom-event-registration' ) ); ?>')) {
					event.preventDefault();
					return;
				}
				this.querySelector('[name="cer_confirm_trash"]').value = '1';
			}
		});
		document.addEventListener('click', function (event) {
			const link = event.target.closest('a.cer-confirm-trash');
			if (!link) {
				return;
			}
			event.preventDefault();
			if (!window.confirm(link.dataset.confirm)) {
				return;
			}
			const url = new URL(link.href, window.location.href);
			url.searchParams.set('cer_confirm_trash', '1');
			window.location.href = url.toString();
		});
	})();
</script>
