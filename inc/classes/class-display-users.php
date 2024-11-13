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

        <div class="container">
            <div class="row g-4">

                <?php

                $args = [
                    'post_type'      => 'sync_users',
                    'posts_per_page' => 9,
                ];

                $users = new \WP_Query( $args );

                if ( $users->have_posts() ) {
                    while ( $users->have_posts() ) {
                        $users->the_post();

                        $post_id        = get_the_ID();
                        $title          = get_the_title();
                        $featured_image = get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ?: 'https://placehold.jp/150x150.png'; // fallback image
        
                        // Get user data
                        $user_data = get_post_meta( $post_id, '_sync_users', true );

                        // $this->put_program_logs( json_encode( $user_data ) );
        
                        $email   = $user_data['_email'] ?? '';
                        $phone   = $user_data['_phone_number'] ?? '';
                        $gender  = $user_data['_gender'] ?? '';
                        $website = 'shahjalal.com';

                        $_street  = $user_data['_street'] ?? '';
                        $_city    = $user_data['_city'] ?? '';
                        $_state   = $user_data['_state'] ?? '';
                        $_country = $user_data['_country'] ?? '';

                        // Concatenate the address into a single variable
                        $address = $_street . "<br>" . $_city . "<br>" . $_state . "<br>" . $_country;

                        ?>

                        <!-- Card -->
                        <div class="col-md-4" data-post-id="<?php echo esc_attr( $post_id ); ?>">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <!-- Dynamic featured image -->
                                    <img src="<?php echo esc_url( $featured_image ); ?>" class="card-img-top"
                                        alt="<?php echo esc_attr( $title ); ?>">
                                </div>
                                <div class="card-body">
                                    <!-- Dynamic title -->
                                    <h5 class="card-title"><?php echo esc_html( $title ); ?></h5>
                                    <p class="card-text">
                                        <!-- Dynamic address -->
                                        <?php echo wp_kses_post( nl2br( $address ) ); ?><br>
                                        <!-- Dynamic email -->
                                        <?php if ( $email ) : ?>
                                            <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a><br>
                                        <?php endif; ?>
                                        <!-- Dynamic phone -->
                                        <?php if ( $phone ) : ?>
                                            <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a>
                                        <?php endif; ?>
                                    </p>
                                    <?php if ( $website ) : ?>
                                        <a href="<?php echo esc_url( $website ); ?>" class="link text-primary" target="_blank">zur
                                            Website</a>
                                    <?php endif; ?>
                                    <a href="https://maps.google.com/?q=<?php echo urlencode( strip_tags( $address ) ); ?>"
                                        class="link text-primary" target="_blank">Route in Google anzeigen</a>
                                </div>
                            </div>
                        </div>

                        <?php
                    }
                }

                // Reset Post Data
                wp_reset_postdata();
                ?>

            </div>
        </div>

        <?php

        return ob_get_clean();
    }


}
