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

            $users_json = $this->fetch_users_from_api();
            // $this->put_program_logs( 'User: ' . $users );

            if ( empty( $users_json ) ) {
                return new \WP_Error( 'no_users', 'No users found to insert', [ 'status' => 404 ] );
            }

            $users       = [];
            $users_array = json_decode( $users_json, true );
            if ( array_key_exists( 'results', $users_array ) ) {
                $users = $users_array['results'];
            } else {
                $users = $users_array;
            }

            global $wpdb;
            $table_name = $wpdb->prefix . 'sync_users';

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

            return 'All users inserted or updated successfully.';

        } catch (\Exception $e) {
            return new \WP_Error( 'exception', $e->getMessage(), [ 'status' => 500 ] );
        }
    }

    public function fetch_users_from_api() {

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
            CURLOPT_URL            => $api_base_url . '/users/?limit=100',
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
