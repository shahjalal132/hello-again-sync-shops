<?php if ( !defined( 'ABSPATH' ) ) {
    die;
} // Cannot access directly.

// metabox id
$prefix_shop_page_opts = '_sync_shop_info';

// Create Metabox
CSF::createMetabox( $prefix_shop_page_opts, array(
    'title'        => 'Shop Info',
    'post_type'    => 'sync_shops',
) );

// Create Section
CSF::createSection( $prefix_shop_page_opts, array(
    'title'  => 'Address',
    'fields' => array(
        array(
            'id'          => '_street',
            'type'        => 'text',
            'title'       => 'Street',
            'placeholder' => 'Enter street',
        ),
        array(
            'id'          => '_city_code',
            'type'        => 'number',
            'title'       => 'City Code',
            'placeholder' => 'City code',
        ),
        array(
            'id'          => '_city',
            'type'        => 'text',
            'title'       => 'City',
            'placeholder' => 'Enter city',
        ),
    ),
) );

// Create Section
CSF::createSection( $prefix_shop_page_opts, array(
    'title'  => 'Social Medias',
    'fields' => array(
        array(
            'id'          => '_website',
            'type'        => 'text',
            'title'       => 'Website',
            'placeholder' => 'Enter website',
        ),
        array(
            'id'          => '_fb_page_id',
            'type'        => 'text',
            'title'       => 'Facebook Page ID',
            'placeholder' => 'Enter facebook page id',
        ),
        array(
            'id'          => '_google_places_id',
            'type'        => 'text',
            'title'       => 'Google Places ID',
            'placeholder' => 'Enter google places id',
        ),
        /* array(
            'id'          => '_image_url',
            'type'        => 'text',
            'title'       => 'Image URL',
            'placeholder' => 'Enter image url',
        ),
        array(
            'id'          => '_logo_url',
            'type'        => 'text',
            'title'       => 'Logo URL',
            'placeholder' => 'Enter logo url',
        ), */
    ),
) );

// Create Section
CSF::createSection( $prefix_shop_page_opts, array(
    'title'  => 'Contact',
    'fields' => array(
        array(
            'id'          => '_email',
            'type'        => 'text',
            'title'       => 'Email',
            'placeholder' => 'Enter email',
        ),
        array(
            'id'          => '_phone_number',
            'type'        => 'text',
            'title'       => 'Phone Number',
            'placeholder' => 'Enter phone number',
        ),
    ),
) );