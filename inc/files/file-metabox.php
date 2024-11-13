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
    'title'  => 'Overview',
    'icon'   => 'fas fa-rocket',
    'fields' => array(
        array(
            'id'    => 'opt-text',
            'type'  => 'text',
            'title' => 'Text',
        ),

    ),
) );