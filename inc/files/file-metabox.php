<?php if ( !defined( 'ABSPATH' ) ) {
    die;
} // Cannot access directly.

// metabox id
$prefix_page_opts = '_sync_users';

// Create Metabox
CSF::createMetabox( $prefix_page_opts, array(
    'title'        => 'User Details',
    'post_type'    => 'sync_users',
    'show_restore' => true,
) );

// Create Section
CSF::createSection( $prefix_page_opts, array(
    'title'  => 'User Info',
    'fields' => array(
        array(
            'id'    => '_email',
            'type'  => 'text',
            'title' => 'Email',
            'placeholder' => 'Enter email address',
        ),
        array(
            'id'    => '_company',
            'type'  => 'text',
            'title' => 'Company',
            'placeholder' => 'Enter company name',
        ),
        array(
            'id'    => '_phone_number',
            'type'  => 'text',
            'title' => 'Phone Number',
            'placeholder' => 'Enter phone number',
        ),
        array(
            'id'    => '_birthday',
            'type'  => 'date',
            'title' => 'Birthday',
        ),
        array(
            'id'      => '_gender',
            'type'    => 'radio',
            'title'   => 'Gender',
            'options' => array(
                'male'   => 'Male',
                'female' => 'Female',
            ),
        ),
        array(
            'id'    => '_points',
            'type'  => 'number',
            'title' => 'Points',
            'placeholder' => 'Enter points',
        ),

    ),
) );

// Create Section
CSF::createSection( $prefix_page_opts, array(
    'title'  => 'Address',
    'fields' => array(
        array(
            'id'    => '_street',
            'type'  => 'text',
            'title' => 'Street',
            'placeholder' => 'Enter street',
        ),
        array(
            'id'    => '_city_code',
            'type'  => 'number',
            'title' => 'City Code',
            'placeholder' => 'City code',
        ),
        array(
            'id'    => '_city',
            'type'  => 'text',
            'title' => 'City',
            'placeholder' => 'Enter city',
        ),
        array(
            'id'    => '_street_number',
            'type'  => 'text',
            'title' => 'Street Number',
            'placeholder' => 'Enter street number',
        ),
        array(
            'id'    => '_state',
            'type'  => 'text',
            'title' => 'Street',
            'placeholder' => 'Enter state',
        ),
        array(
            'id'    => '_country',
            'type'  => 'text',
            'title' => 'Country',
            'placeholder' => 'Enter country',
        ),
    ),
) );

// Create Section
CSF::createSection( $prefix_page_opts, array(
    'title'  => 'Social Media',
    'fields' => array(
        array(
            'id'    => '_fbid',
            'type'  => 'text',
            'title' => 'Facebook ID',
            'placeholder' => 'Enter facebook id',
        ),
        array(
            'id'    => '_apple_user_id',
            'type'  => 'text',
            'title' => 'Apple User ID',
            'placeholder' => 'Enter apple id',
        ),
        array(
            'id'    => '_google_id',
            'type'  => 'text',
            'title' => 'Google ID',
            'placeholder' => 'Enter google id',
        ),
    ),
) );