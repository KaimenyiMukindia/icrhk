<?php
/**
 * Laravel connector for shared event registration operations.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('CER_Laravel_Connector')) {
    class CER_Laravel_Connector
    {
        /**
         * Path to the Laravel engine bootstrap.
         */
        private string $engine_root;

        public function __construct(string $engine_root = '')
        {
            $this->engine_root = $engine_root ?: WP_PLUGIN_DIR . '/custom-event-registration/laravel-engine';
        }

        /**
         * Load the Laravel bootstrap file if present.
         */
        public function load_engine(): bool
        {
            $bootstrap = $this->engine_root . '/public/index.php';
            if (!file_exists($bootstrap)) {
                return false;
            }

            require_once $bootstrap;
            return true;
        }

        /**
         * Synchronize registration data with the Laravel engine via HTTP if available.
         */
        public function sync_registration(array $data): bool
        {
            $endpoint = defined( 'CER_LARAVEL_BRIDGE_URL' ) ? CER_LARAVEL_BRIDGE_URL : home_url( '/laravel-engine/public/wordpress/registrations' );
            if ( empty( $endpoint ) ) {
                return false;
            }

            $response = wp_remote_post(
                esc_url_raw( $endpoint ),
                array(
                    'headers' => array(
                        'Content-Type' => 'application/json; charset=utf-8',
                        'Connection' => 'keep-alive',
                    ),
                    'body'    => wp_json_encode( $data ),
                    'timeout' => 8,
                    'connect_timeout' => 3,
                    'httpversion' => '1.1',
                )
            );

            if ( is_wp_error( $response ) ) {
                error_log( 'CER Laravel sync failed: ' . $response->get_error_message() );
                return false;
            }

            $code = wp_remote_retrieve_response_code( $response );
            return $code >= 200 && $code < 300;
        }

        /**
         * Read rows from the shared registrations table.
         */
        public function get_registrations(array $filters = []): array
        {
            global $wpdb;
            $table = $wpdb->prefix . 'evt_registrations';
            $query = "SELECT * FROM $table";
            $where = [];

            if (!empty($filters['status'])) {
                $where[] = $wpdb->prepare('status = %s', $filters['status']);
            }

            if (!empty($where)) {
                $query .= ' WHERE ' . implode(' AND ', $where);
            }

            $query .= ' ORDER BY created_at DESC';
            return $wpdb->get_results($query, ARRAY_A) ?: [];
        }

        /**
         * Create a registration in the shared WordPress table.
         */
        public function create_registration(array $data): int
        {
            global $wpdb;
            $table = $wpdb->prefix . 'evt_registrations';
            $data['created_at'] = current_time('mysql');
            $wpdb->insert($table, cer_prepare_registration_row($data));
            return (int) $wpdb->insert_id;
        }

        /**
         * Update a registration record in the shared WordPress table.
         */
        public function update_registration(int $id, array $data): bool
        {
            global $wpdb;
            $table = $wpdb->prefix . 'evt_registrations';
            return (bool) $wpdb->update($table, cer_prepare_registration_row($data), ['id' => $id]);
        }

        /**
         * Delete a registration record from the shared WordPress table.
         */
        public function delete_registration(int $id): bool
        {
            global $wpdb;
            $table = $wpdb->prefix . 'evt_registrations';
            return (bool) $wpdb->delete($table, ['id' => $id]);
        }
    }
}
