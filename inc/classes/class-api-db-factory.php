<?php

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Program_Logs;
use BOILERPLATE\Inc\Traits\Singleton;

class API_DB_Factory {

    use Singleton;
    use Program_Logs;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        // Register REST route for inserting users to database
        add_action( 'rest_api_init', [ $this, 'register_rest_route' ] );
    }

    public function register_rest_route() {
        register_rest_route( 'hello-again/v1', '/insert-users', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'insert_user' ],
            'permission_callback' => '__return_true',
        ] );
    }

    public function insert_user() {
        try {
            // Fetch the first page to get total users and pages.
            $users_json = $this->fetch_users_from_api( 1 );

            if ( empty( $users_json ) ) {
                return new \WP_Error( 'no_users', 'No users found to insert', [ 'status' => 404 ] );
            }

            // Initialize variables
            $total_users = 0;
            $total_pages = 0;

            // Decode JSON
            $users_array = json_decode( $users_json, true );

            // Get total users
            if ( array_key_exists( 'count', $users_array ) ) {
                $total_users = $users_array['count'];
            }

            // Update total users to options table
            update_option( 'sync_total_users', $total_users );

            // Calculate total pages
            $total_pages = ceil( $total_users / 100 );

            // Update total pages to options table
            update_option( 'sync_total_pages', $total_pages );

            global $wpdb;
            $table_name = $wpdb->prefix . 'sync_users';

            // Get the last processed page from options table or start from 1
            $start_page = get_option( 'sync_current_page', 1 );

            // Loop through all pages starting from the last unprocessed page
            for ( $page = $start_page; $page <= $total_pages; $page++ ) {

                // Fetch users
                $users_json  = $this->fetch_users_from_api( $page );
                // Decode JSON
                $users_array = json_decode( $users_json, true );

                // Update current page to options table
                update_option( 'sync_current_page', $page );

                // Get users on the current page
                $users = $users_array['results'] ?? [];

                foreach ( $users as $user ) {
                    $user_id   = $user['id'];
                    $email     = $user['email'] ?? null;
                    $user_data = json_encode( $user );
                    $status    = 'pending';

                    $sql = $wpdb->prepare(
                        "INSERT INTO $table_name (user_id, email, user_data, status) VALUES (%s, %s, %s, %s)
                        ON DUPLICATE KEY UPDATE user_data = %s, status = %s",
                        $user_id,
                        $email,
                        $user_data,
                        $status,
                        $user_data,
                        $status
                    );

                    $result = $wpdb->query( $sql );

                    if ( $result === false ) {
                        return new \WP_Error( 'db_error', 'Failed to insert or update user in database', [ 'status' => 500 ] );
                    }
                }
            }

            // Reset current page after all pages have been processed
            update_option( 'sync_current_page', 1 );

            return 'All users inserted or updated successfully.';

        } catch (\Exception $e) {
            return new \WP_Error( 'exception', $e->getMessage(), [ 'status' => 500 ] );
        }
    }

    public function fetch_users_from_api( $page = 1 ) {

        // Credentials
        $api_base_url = '';
        $api_key      = '';

        $credentials_file = PLUGIN_BASE_PATH . '/credentials.json';
        if ( file_exists( $credentials_file ) ) {
            $credentials  = json_decode( file_get_contents( $credentials_file ), true );
            $api_base_url = $credentials['api_base_url'];
            $api_key      = $credentials['api_key'];
        }

        $curl = curl_init();
        curl_setopt_array( $curl, array(
            CURLOPT_URL            => $api_base_url . '/users/?limit=100&page=' . $page,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => array(
                'accept: application/json',
                'Authorization: API-Key ' . $api_key,
            ),
        ) );

        $response = curl_exec( $curl );

        if ( curl_errno( $curl ) ) {
            $error_msg = curl_error( $curl );
            curl_close( $curl );
            return new \WP_Error( 'curl_error', 'cURL error: ' . $error_msg, [ 'status' => 500 ] );
        }

        $http_code = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
        curl_close( $curl );

        if ( $http_code !== 200 ) {
            return new \WP_Error( 'api_error', 'API responded with HTTP code ' . $http_code, [ 'status' => $http_code ] );
        }

        return $response;
    }

}
