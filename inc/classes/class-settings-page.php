<?php

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Program_Logs;
use BOILERPLATE\Inc\Traits\Singleton;

class Settings_Page {

    use Singleton;
    use Program_Logs;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {

        add_action( 'admin_menu', [ $this, 'ha_options_page' ] );
        add_filter( 'plugin_action_links_' . PLUGIN_BASENAME, [ $this, 'add_plugin_action_links' ] );

        add_action( 'wp_ajax_save_ha_options', [ $this, 'save_ha_options' ] );
    }

    // Add settings link on the plugin page
    function add_plugin_action_links( $links ) {
        $settings_link = '<a href="admin.php?page=ha-settings-options">' . __( 'Settings', 'hello-again' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    // AJAX handler to save the options
    public function save_ha_options() {
        // Check if user has the right permissions
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Permission denied' ] );
        }

        // Sanitize and save the options
        if ( isset( $_POST['api_base_url'] ) && isset( $_POST['api_key'] ) && isset( $_POST['how_many_posts_to_display'] ) ) {
            update_option( 'api_base_url', sanitize_text_field( $_POST['api_base_url'] ) );
            update_option( 'api_key', sanitize_text_field( $_POST['api_key'] ) );
            update_option( 'how_many_posts_to_display', intval( $_POST['how_many_posts_to_display'] ) );

            wp_send_json_success( [ 'message' => 'Settings saved successfully' ] );
        } else {
            wp_send_json_error( [ 'message' => 'Missing data' ] );
        }
    }

    public function ha_options_page() {
        add_submenu_page(
            'options-general.php',
            'HA Options',
            'HA Options',
            'manage_options',
            'ha-settings-options',
            [ $this, 'ha_options_page_html' ]
        );
    }

    public function ha_options_page_html() {
        include_once PLUGIN_BASE_PATH . '/template-parts/settings-page.php';
    }

}