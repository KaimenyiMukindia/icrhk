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
        global $wpdb;
        $table = $wpdb->prefix . 'evt_registrations';
        $wpdb->delete( $table, array( 'id' => $registration_id ), array( '%d' ) );
        wp_safe_redirect( remove_query_arg( array( 'cer_action', 'registration_id', '_wpnonce' ) ) );
        exit;
    }
}

if ( isset( $_GET['cer_export'] ) && $_GET['cer_export'] === '1' && wp_verify_nonce( $_GET['cer_export_nonce'] ?? '', 'cer_export_registrations' ) ) {
    global $wpdb;
    $table = $wpdb->prefix . 'evt_registrations';
    $where = array();
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
    <h1><?php esc_html_e( 'Event Registrations', 'custom-event-registration' ); ?></h1>
    <form method="get">
        <input type="hidden" name="page" value="event-registrations" />
        <?php $registration_table->search_box( esc_html__( 'Search Registrations', 'custom-event-registration' ), 'cer-search' ); ?>
        <?php $registration_table->display(); ?>
        <?php wp_nonce_field( 'cer_bulk_action', 'cer_bulk_action_nonce' ); ?>
    </form>
</div>
