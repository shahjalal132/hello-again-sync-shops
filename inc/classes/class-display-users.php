<?php

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Program_Logs;
use BOILERPLATE\Inc\Traits\Singleton;

class Display_Users {

    use Singleton;
    use Program_Logs;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_shortcode( 'helloagain_sync_users', [ $this, 'display_users_html' ] );
    }

    public function display_users_html() {
        ob_start();
        ?>

        

        <?php
        return ob_get_clean();
    }

}