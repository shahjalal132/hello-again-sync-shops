<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 */

class Plugin_Activator {

    public static function activate() {

        // Create sync users table
        global $wpdb;
        $table_name      = $wpdb->prefix . 'sync_users';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT AUTO_INCREMENT,
            user_id VARCHAR(255) UNIQUE NOT NULL,
            -- email VARCHAR(100) UNIQUE NOT NULL,
            email VARCHAR(100) NULL,
            user_data LONGTEXT NOT NULL,
            status VARCHAR(20) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    public static function create_shop_table() {
        // Create sync users table
        global $wpdb;
        $table_name      = $wpdb->prefix . 'sync_shops';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT AUTO_INCREMENT,
            shop_id VARCHAR(255) UNIQUE NOT NULL,
            shop_data LONGTEXT NOT NULL,
            status VARCHAR(20) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    public static function create_shop_page() {
        // Define the page title and content with your shortcode
        $page_title   = 'Sync Shops';
        $page_content = '[helloagain_sync_shops]';
        $page_slug    = 'helloagain-sync-shops';

        // Check if a page with this title or slug already exists
        $existing_page = get_page_by_path( $page_slug );

        if ( !$existing_page ) {
            // Page doesn't exist, so create it
            $page_data = array(
                'post_title'   => $page_title,
                'post_content' => $page_content,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => 1,
            );

            // Insert the page into the database
            wp_insert_post( $page_data );
        }
    }

    public static function create_user_page() {
        // Define the page title and content with your shortcode
        $page_title   = 'Sync Users';
        $page_content = '[helloagain_sync_users]';
        $page_slug    = 'helloagain-sync-users';

        // Check if a page with this title or slug already exists
        $existing_page = get_page_by_path( $page_slug );

        if ( !$existing_page ) {
            // Page doesn't exist, so create it
            $page_data = array(
                'post_title'   => $page_title,
                'post_content' => $page_content,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => 1,
            );

            // Insert the page into the database
            wp_insert_post( $page_data );
        }
    }

}