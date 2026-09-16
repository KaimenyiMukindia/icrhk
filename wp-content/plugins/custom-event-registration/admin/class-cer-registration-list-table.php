<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'CER_Registration_List_Table' ) ) {
    if ( ! class_exists( 'WP_List_Table' ) ) {
        require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
    }

    class CER_Registration_List_Table extends WP_List_Table {
        public function __construct() {
            parent::__construct(
                array(
                    'singular' => 'registration',
                    'plural'   => 'registrations',
                    'ajax'     => false,
                )
            );
        }

        public function get_columns() {
            return array(
                'cb'             => '<input type="checkbox" />',
                'id'             => esc_html__( 'ID', 'custom-event-registration' ),
                'full_name'      => esc_html__( 'Full Name', 'custom-event-registration' ),
                'email'          => esc_html__( 'Email', 'custom-event-registration' ),
                'phone'          => esc_html__( 'Phone', 'custom-event-registration' ),
                'ticket_type'    => esc_html__( 'Ticket Type', 'custom-event-registration' ),
                'payment_method' => esc_html__( 'Payment Method', 'custom-event-registration' ),
                'amount'         => esc_html__( 'Amount', 'custom-event-registration' ),
                'status'         => esc_html__( 'Status', 'custom-event-registration' ),
                'created_at'     => esc_html__( 'Date', 'custom-event-registration' ),
            );
        }

        protected function get_sortable_columns() {
            return array(
                'id'         => array( 'id', false ),
                'full_name'  => array( 'full_name', false ),
                'email'      => array( 'email', false ),
                'status'     => array( 'status', false ),
                'created_at' => array( 'created_at', false ),
            );
        }

        public function column_cb( $item ) {
            return sprintf(
                '<input type="checkbox" name="registration_ids[]" value="%d" />',
                absint( $item['id'] )
            );
        }

        public function column_full_name( $item ) {
            $actions = array();
            $actions['confirm'] = sprintf(
                '<a href="%s">%s</a>',
                esc_url( wp_nonce_url( add_query_arg( array( 'page' => 'event-registrations', 'cer_action' => 'toggle_status', 'registration_id' => absint( $item['id'] ) ) ), 'cer_toggle_status_' . absint( $item['id'] ) ) ),
                esc_html__( 'Confirm', 'custom-event-registration' )
            );
            $actions['delete'] = sprintf(
                '<a href="%s" onclick="return confirm(\'%s\');">%s</a>',
                esc_url( wp_nonce_url( add_query_arg( array( 'page' => 'event-registrations', 'cer_action' => 'delete', 'registration_id' => absint( $item['id'] ) ) ), 'cer_delete_' . absint( $item['id'] ) ) ),
                esc_html__( 'Delete this registration?', 'custom-event-registration' ),
                esc_html__( 'Delete', 'custom-event-registration' )
            );

            return sprintf( '%1$s %2$s', esc_html( $item['full_name'] ), $this->row_actions( $actions ) );
        }

        public function column_default( $item, $column_name ) {
            if ( isset( $item[ $column_name ] ) ) {
                return esc_html( $item[ $column_name ] );
            }
            return '&ndash;';
        }

        public function get_bulk_actions() {
            return array(
                'mark_confirmed' => esc_html__( 'Mark as Confirmed', 'custom-event-registration' ),
                'delete'         => esc_html__( 'Delete', 'custom-event-registration' ),
            );
        }

        public function process_bulk_action() {
            $action = $this->current_action();
            if ( empty( $action ) || ! isset( $_REQUEST['registration_ids'] ) ) {
                return;
            }

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            if ( ! isset( $_REQUEST['cer_bulk_action_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_REQUEST['cer_bulk_action_nonce'] ), 'cer_bulk_action' ) ) {
                return;
            }

            $registration_ids = array_map( 'absint', (array) wp_unslash( $_REQUEST['registration_ids'] ) );
            global $wpdb;
            $table = $wpdb->prefix . 'evt_registrations';

            if ( 'mark_confirmed' === $action ) {
                foreach ( $registration_ids as $registration_id ) {
                    $wpdb->update(
                        $table,
                        array(
                            'status'     => 'confirmed',
                            'updated_at' => current_time( 'mysql' ),
                        ),
                        array( 'id' => $registration_id ),
                        array( '%s', '%s' ),
                        array( '%d' )
                    );
                }
                wp_safe_redirect( remove_query_arg( array( 'action', 'registration_ids', 'cer_bulk_action_nonce' ) ) );
                exit;
            }

            if ( 'delete' === $action ) {
                foreach ( $registration_ids as $registration_id ) {
                    $wpdb->delete( $table, array( 'id' => $registration_id ), array( '%d' ) );
                }
                wp_safe_redirect( remove_query_arg( array( 'action', 'registration_ids', 'cer_bulk_action_nonce' ) ) );
                exit;
            }
        }

        protected function get_views() {
            return array();
        }

        protected function extra_tablenav( $which ) {
            if ( 'top' === $which ) {
                $status = isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : '';
                $ticket_type = isset( $_REQUEST['ticket_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['ticket_type'] ) ) : '';
                $export_url = esc_url( add_query_arg( array( 'cer_export' => '1', 'page' => 'event-registrations' ) ) );
                ?>
                <div class="alignleft actions">
                    <select name="ticket_type">
                        <option value=""><?php esc_html_e( 'All Tickets', 'custom-event-registration' ); ?></option>
                        <option value="Community Outreach" <?php selected( $ticket_type, 'Community Outreach' ); ?>><?php esc_html_e( 'Community Outreach', 'custom-event-registration' ); ?></option>
                        <option value="Volunteer Summit" <?php selected( $ticket_type, 'Volunteer Summit' ); ?>><?php esc_html_e( 'Volunteer Summit', 'custom-event-registration' ); ?></option>
                        <option value="Fundraising Gala" <?php selected( $ticket_type, 'Fundraising Gala' ); ?>><?php esc_html_e( 'Fundraising Gala', 'custom-event-registration' ); ?></option>
                    </select>
                    <select name="status">
                        <option value=""><?php esc_html_e( 'All Statuses', 'custom-event-registration' ); ?></option>
                        <option value="pending" <?php selected( $status, 'pending' ); ?>><?php esc_html_e( 'Pending', 'custom-event-registration' ); ?></option>
                        <option value="confirmed" <?php selected( $status, 'confirmed' ); ?>><?php esc_html_e( 'Confirmed', 'custom-event-registration' ); ?></option>
                        <option value="cancelled" <?php selected( $status, 'cancelled' ); ?>><?php esc_html_e( 'Cancelled', 'custom-event-registration' ); ?></option>
                    </select>
                    <button type="submit" class="button action"><?php esc_html_e( 'Filter', 'custom-event-registration' ); ?></button>
                    <a href="<?php echo esc_url( wp_nonce_url( $export_url, 'cer_export_registrations', 'cer_export_nonce' ) ); ?>" class="button action"><?php esc_html_e( 'Export CSV', 'custom-event-registration' ); ?></a>
                </div>
                <?php
            }
        }

        public function prepare_items() {
            $this->process_bulk_action();

            global $wpdb;
            $table = $wpdb->prefix . 'evt_registrations';
            $per_page = 20;
            $current_page = $this->get_pagenum();
            $search = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';
            $status = isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : '';
            $ticket_type = isset( $_REQUEST['ticket_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['ticket_type'] ) ) : '';

            $where = array();
            if ( $search ) {
                $search_like = '%' . $wpdb->esc_like( $search ) . '%';
                $where[] = $wpdb->prepare( '(full_name LIKE %s OR email LIKE %s OR phone LIKE %s OR ticket_type LIKE %s)', $search_like, $search_like, $search_like, $search_like );
            }
            if ( $status ) {
                $where[] = $wpdb->prepare( 'status = %s', $status );
            }
            if ( $ticket_type ) {
                $where[] = $wpdb->prepare( 'ticket_type = %s', $ticket_type );
            }
            $where_sql = ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '';

            $total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table $where_sql" );

            $sql = "SELECT * FROM $table $where_sql ORDER BY created_at DESC LIMIT %d OFFSET %d";
            $offset = ( $current_page - 1 ) * $per_page;
            $this->items = array_map( 'cer_decrypt_registration_row', $wpdb->get_results( $wpdb->prepare( $sql, $per_page, $offset ), ARRAY_A ) ?: array() );

            $this->set_pagination_args(
                array(
                    'total_items' => $total_items,
                    'per_page'    => $per_page,
                )
            );
        }
    }
}
