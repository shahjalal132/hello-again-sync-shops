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