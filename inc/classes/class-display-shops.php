<?php

namespace BOILERPLATE\Inc;

use BOILERPLATE\Inc\Traits\Program_Logs;
use BOILERPLATE\Inc\Traits\Singleton;

class Display_Shops {

    use Singleton;
    use Program_Logs;

    private $item_to_display;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {

        add_shortcode( 'helloagain_sync_shops', [ $this, 'display_shops_html' ] );

        add_action( 'wp_ajax_load_more_shops', [ $this, 'load_more_shops' ] );
        add_action( 'wp_ajax_nopriv_load_more_shops', [ $this, 'load_more_shops' ] );

        add_action( 'wp_ajax_search_shops', [ $this, 'search_shops' ] );
        add_action( 'wp_ajax_nopriv_search_shops', [ $this, 'search_shops' ] );

        // live search for shops
        add_action( 'wp_ajax_live_search_shops', [ $this, 'search_shops' ] );
        add_action( 'wp_ajax_nopriv_live_search_shops', [ $this, 'search_shops' ] );

        add_action( 'wp_ajax_category_filter_shops', [ $this, 'category_filter_shops' ] );
        add_action( 'wp_ajax_nopriv_category_filter_shops', [ $this, 'category_filter_shops' ] );

        $this->item_to_display = get_option( 'how_many_posts_to_display', 9 );
    }

    // Category filter function
    public function category_filter_shops() {
        $category_id = isset( $_POST['sync_shops_category'] ) ? intval( $_POST['sync_shops_category'] ) : 0;
        $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;

        $args = [
            'post_type'      => 'sync_shops',
            'posts_per_page' => intval( $this->item_to_display ),
            'paged'          => $paged,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ];

        // If category is provided, add to the query
        if ( $category_id ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'sync_shops_category', // Replace with your custom taxonomy name
                    'field'    => 'term_id',
                    'terms'    => $category_id,
                ],
            ];
        }

        $shops = new \WP_Query( $args );

        if ( $shops->have_posts() ) {
            ob_start();

            while ( $shops->have_posts() ) {
                $shops->the_post();

                $post_id        = get_the_ID();
                $title          = get_the_title();
                $featured_image = get_the_post_thumbnail_url( $post_id, 'medium' ) ?: 'https://placehold.jp/150x150.png';
                $shop_data      = get_post_meta( $post_id, '_sync_shop_info', true );

                $email   = $shop_data['_email'] ?? '';
                $phone   = $shop_data['_phone_number'] ?? '';
                $website = $shop_data['_website'] ?? '';
                $_street = $shop_data['_street'] ?? '';
                $_city   = $shop_data['_city'] ?? '';

                $address = $_street . "<br>" . $_city;

                ?>
                <!-- Render Shop Card -->
                <div class="col-sm-6 col-md-6 col-lg-4" data-post-id="<?php echo esc_attr( $post_id ); ?>">
                    <div class="card h-100">
                        <div class="position-relative">
                            <img src="<?php echo esc_url( $featured_image ); ?>" class="card-img-top" alt="<?php echo esc_attr( $title ); ?>">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo esc_html( $title ); ?></h5>
                            <p class="card-text">
                                <?php echo wp_kses_post( nl2br( $address ) ); ?><br>
                                <?php if ( $email ) : ?>
                                    <a href="mailto:<?php echo esc_attr( $email ); ?>" class="color-gold"><?php echo esc_html( $email ); ?></a><br>
                                <?php endif; ?>
                                <?php if ( $phone ) : ?>
                                    <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a>
                                <?php endif; ?>
                            </p>
                            <?php if ( $website ) : ?>
                                <a href="<?php echo esc_url( $website ); ?>" class="link color-gold" target="_blank">Visit Website</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php
            }

            wp_reset_postdata();

            $response = [
                'html' => ob_get_clean(),
            ];

            echo json_encode( $response );
        } else {
            echo json_encode( [ 'html' => '<p>No results found for this category.</p>' ] );
        }

        wp_die();
    }


    // Search function
    public function search_shops() {
        // Use 'query' instead of 'search_query'
        $search_query = isset( $_POST['query'] ) ? sanitize_text_field( $_POST['query'] ) : '';
    
        $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
    
        // Log the search query for debugging
        error_log('Search query: ' . $search_query);
    
        $args = [
            'post_type'      => 'sync_shops',
            'posts_per_page' => intval( $this->item_to_display ),
            'paged'          => $paged,
            's'              => $search_query,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ];
    
        $shops = new \WP_Query( $args );
    
        if ( $shops->have_posts() ) {
            ob_start();
    
            while ( $shops->have_posts() ) {
                $shops->the_post();

                $post_id        = get_the_ID();
                $title          = get_the_title();
                $featured_image = get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ?: 'https://placehold.jp/150x150.png'; // fallback image

                // Get user data
                $shop_data = get_post_meta( $post_id, '_sync_shop_info', true );
                // $this->put_program_logs( json_encode( $shop_data ) );

                $email   = $shop_data['_email'] ?? '';
                $phone   = $shop_data['_phone_number'] ?? '';
                $website = $shop_data['_website'] ?? '';

                $_street    = $shop_data['_street'] ?? '';
                $_city_code = $shop_data['_city_code'] ?? '';
                $_city      = $shop_data['_city'] ?? '';

                $location    = $shop_data['_location'] ?? [];
                $coordinates = $location['coordinates'] ?? [];

                if ( count( $coordinates ) === 2 ) {
                    // Reverse the coordinates order to latitude, longitude
                    $coordinates = array_reverse( $coordinates );
                }

                $coordinates_string = implode( ',', $coordinates );


                // Concatenate the address into a single variable
                $address = $_street . "<br>" . $_city_code . "<br>" . $_city;

                ?>

                <!-- Card -->
                <div class="col-sm-6 col-md-6 col-lg-4" data-post-id="<?php echo esc_attr( $post_id ); ?>">
                    <div class="card h-100">
                        <div class="position-relative">
                            <!-- Dynamic featured image -->
                            <img src="<?php echo esc_url( $featured_image ); ?>" class="card-img-top"
                                alt="<?php echo esc_attr( $title ); ?>">
                        </div>
                        <div class="card-body">
                            <!-- Dynamic title -->
                            <h5 class="card-title"><?php echo esc_html( $title ); ?></h5>
                            <p class="card-text mb-0">
                                <!-- Dynamic address -->
                                <?php echo wp_kses_post( nl2br( $address ) ); ?><br>
                                <!-- Dynamic email -->
                                <?php if ( $email ) : ?>
                                    <a href="mailto:<?php echo esc_attr( $email ); ?>"
                                        class="color-gold"><?php echo esc_html( $email ); ?></a><br>
                                <?php endif; ?>
                                <!-- Dynamic phone -->
                                <?php if ( $phone ) : ?>
                                    <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a>
                                <?php endif; ?>
                            </p>
                            <?php if ( $website ) : ?>
                                <a href="<?php echo esc_url( $website ); ?>" class="link color-gold" target="_blank">zur
                                    Website</a>
                            <?php endif; ?>
                            <a href="https://maps.google.com/?q=<?php echo esc_attr( $coordinates_string ); ?>"
                                class="link color-gold" target="_blank">Route in Google anzeigen</a>
                        </div>
                    </div>
                </div>

                <?php
            }
    
            wp_reset_postdata();
    
            $response = [
                'html' => ob_get_clean(),
            ];
    
            echo json_encode( $response );
        } else {
            echo json_encode( [ 'html' => '<p>No results found.</p>' ] );
        }
    
        wp_die();
    }
    

    public function load_more_shops() {

        $paged = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;

        $args = [
            'post_type'      => 'sync_shops',
            'posts_per_page' => intval( $this->item_to_display ),
            'paged'          => $paged,
            'orderby'        => 'title',
            'order'          => 'ASC',  
        ];

        $shops = new \WP_Query( $args );

        if ( $shops->have_posts() ) {
            ob_start();

            while ( $shops->have_posts() ) {
                $shops->the_post();

                $post_id        = get_the_ID();
                $title          = get_the_title();
                $featured_image = get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ?: 'https://placehold.jp/150x150.png';

                // Get user data
                $shop_data = get_post_meta( $post_id, '_sync_shop_info', true );

                // $this->put_program_logs( json_encode( $shop_data ) );

                $email   = $shop_data['_email'] ?? '';
                $phone   = $shop_data['_phone_number'] ?? '';
                $website = $shop_data['_website'] ?? '';

                $_street    = $shop_data['_street'] ?? '';
                $_city_code = $shop_data['_city_code'] ?? '';
                $_city      = $shop_data['_city'] ?? '';

                $location    = $shop_data['_location'] ?? [];
                $coordinates = $location['coordinates'] ?? [];

                if ( count( $coordinates ) === 2 ) {
                    // Reverse the coordinates order to latitude, longitude
                    $coordinates = array_reverse( $coordinates );
                }

                $coordinates_string = implode( ',', $coordinates );


                // Concatenate the address into a single variable
                $address = $_street . "<br>" . $_city_code . "<br>" . $_city;

                // Card HTML output
                ?>

                <!-- Card Load More-->
                <div class="col-sm-6 col-md-6 col-lg-4" data-post-id="<?php echo esc_attr( $post_id ); ?>">
                    <div class="card h-100">
                        <div class="position-relative">
                            <!-- Dynamic featured image -->
                            <img src="<?php echo esc_url( $featured_image ); ?>" class="card-img-top"
                                alt="<?php echo esc_attr( $title ); ?>">
                        </div>
                        <div class="card-body">
                            <!-- Dynamic title -->
                            <h5 class="card-title"><?php echo esc_html( $title ); ?></h5>
                            <p class="card-text mb-0">
                                <!-- Dynamic address -->
                                <?php echo wp_kses_post( nl2br( $address ) ); ?><br>
                                <!-- Dynamic email -->
                                <?php if ( $email ) : ?>
                                    <a href="mailto:<?php echo esc_attr( $email ); ?>"
                                        class="color-gold"><?php echo esc_html( $email ); ?></a><br>
                                <?php endif; ?>
                                <!-- Dynamic phone -->
                                <?php if ( $phone ) : ?>
                                    <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a>
                                <?php endif; ?>
                            </p>
                            <?php if ( $website ) : ?>
                                <a href="<?php echo esc_url( $website ); ?>" class="link color-gold" target="_blank">zur
                                    Website</a>
                            <?php endif; ?>
                            <a href="https://maps.google.com/?q=<?php echo esc_attr( $coordinates_string ); ?>" class="link color-gold"
                                target="_blank">Route in Google anzeigen</a>
                        </div>
                    </div>
                </div>
                <?php
            }

            wp_reset_postdata();

            $response = [
                'html' => ob_get_clean(),
            ];

            echo json_encode( $response );
        } else {
            echo json_encode( [ 'html' => '' ] );
        }

        wp_die();
    }

    public function display_shops_html() {
        ob_start();
        ?>
    
        <div class="container mt-5 mb-5">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3 order-1 order-lg-2">
                    <!-- Search form -->
                    <div class="category-filter mb-4">
                        <label for="search-shops-input" class="form-label me-2">Suche Mitgliedsbetriebe:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search-shops-input" placeholder="Suche Mitgliedsbetriebe">
                            <button class="btn btn-primary" id="search-shops-button">Suche</button>
                        </div>
                    </div>
                    <!-- Category Filter Dropdown -->
                    <div class="category-filter mb-4">
                        <label for="category-filter" class="form-label">Filter by Category:</label>
                        <select id="category-filter" class="form-select">
                            <option value="">All Categories</option>
                            <?php
                            $categories = get_terms( [
                                'taxonomy'   => 'sync_shops_category', // Replace with your custom taxonomy
                                'hide_empty' => false,
                            ] );
                            if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                                foreach ( $categories as $category ) {
                                    echo '<option value="' . esc_attr( $category->term_id ) . '">' . esc_html( $category->name ) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                </div>
                <!-- Main Content -->
                <div class="col-lg-9 order-2 order-lg-1">
    
                    <div class="row g-4" id="shop-results">
                        <?php
                        $args = [
                            'post_type'      => 'sync_shops',
                            'posts_per_page' => intval( $this->item_to_display ),
                            'orderby'        => 'title',
                            'order'          => 'ASC',
                        ];
    
                        $shops = new \WP_Query( $args );
    
                        if ( $shops->have_posts() ) {
                            while ( $shops->have_posts() ) {
                                $shops->the_post();
    
                                $post_id        = get_the_ID();
                                $title          = get_the_title();
                                $featured_image = get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ?: 'https://placehold.jp/150x150.png';
    
                                $shop_data = get_post_meta( $post_id, '_sync_shop_info', true );
    
                                $email   = $shop_data['_email'] ?? '';
                                $phone   = $shop_data['_phone_number'] ?? '';
                                $website = $shop_data['_website'] ?? '';
    
                                $_street    = $shop_data['_street'] ?? '';
                                $_city_code = $shop_data['_city_code'] ?? '';
                                $_city      = $shop_data['_city'] ?? '';
    
                                $location    = $shop_data['_location'] ?? [];
                                $coordinates = $location['coordinates'] ?? [];
    
                                if ( count( $coordinates ) === 2 ) {
                                    $coordinates = array_reverse( $coordinates );
                                }
    
                                $coordinates_string = implode( ',', $coordinates );
    
                                $address = $_street . "<br>" . $_city_code . "<br>" . $_city;
    
                                ?>
    
                                <!-- Card -->
                                <div class="col-sm-6 col-md-6 col-lg-4" data-post-id="<?php echo esc_attr( $post_id ); ?>">
                                    <div class="card h-100">
                                        <div class="position-relative">
                                            <img src="<?php echo esc_url( $featured_image ); ?>" class="card-img-top"
                                                alt="<?php echo esc_attr( $title ); ?>">
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo esc_html( $title ); ?></h5>
                                            <p class="card-text mb-0">
                                                <?php echo wp_kses_post( nl2br( $address ) ); ?><br>
                                                <?php if ( $email ) : ?>
                                                    <a href="mailto:<?php echo esc_attr( $email ); ?>" class="color-gold"><?php echo esc_html( $email ); ?></a><br>
                                                <?php endif; ?>
                                                <?php if ( $phone ) : ?>
                                                    <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a>
                                                <?php endif; ?>
                                            </p>
                                            <?php if ( $website ) : ?>
                                                <a href="<?php echo esc_url( $website ); ?>" class="link color-gold" target="_blank">Visit Website</a>
                                            <?php endif; ?>
                                            <a href="https://maps.google.com/?q=<?php echo esc_attr( $coordinates_string ); ?>" class="link color-gold" target="_blank">View on Google Maps</a>
                                        </div>
                                    </div>
                                </div>
    
                                <?php
                            }
                        }
    
                        wp_reset_postdata();
                        ?>
                    </div>
    
                    <div class="text-center">
                        <button type="button" class="btn btn-primary mt-3" id="btn-shop-load-more">Load More</button>
                    </div>
                </div>
            </div>
        </div>
    
        <?php
        return ob_get_clean();
    }
    


}
