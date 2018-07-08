<?php
add_action( 'init', 'theme_register_post_type' );
function theme_register_post_type() {
    //Create Post Type Advertises
    $labels = array(
        'name'                  => __( 'Advertises', 'thenatives' ),
        'singular_name'         => __( 'Advertise', 'thenatives' ),
        'menu_name'             => __( 'Advertises', 'thenatives' ),
        'name_admin_bar'        => __( 'Advertise', 'thenatives' ),
        'add_new'               => __( 'Add New', 'thenatives' ),
        'add_new_item'          => __( 'Add New Advertise', 'thenatives' ),
        'new_item'              => __( 'New Advertise', 'thenatives' ),
        'edit_item'             => __( 'Edit Advertise', 'thenatives' ),
        'view_item'             => __( 'View Advertise', 'thenatives' ),
        'all_items'             => __( 'All Advertises', 'thenatives' ),
        'search_items'          => __( 'Search Advertises', 'thenatives' ),
        'parent_item_colon'     => __( 'Parent Advertises:', 'thenatives' ),
        'not_found'             => __( 'No advertises found.', 'thenatives' ),
        'not_found_in_trash'    => __( 'No advertises found in Trash.', 'thenatives' ),
        'featured_image'        => __( 'Advertises Cover Image', 'thenatives' ),
        'set_featured_image'    => __( 'Set cover image', 'thenatives' ),
        'remove_featured_image' => __( 'Remove cover image', 'thenatives' ),
        'use_featured_image'    => __( 'Use as cover image', 'thenatives' ),
        'archives'              => __( 'Advertise archives', 'thenatives' ),
        'insert_into_item'      => __( 'Insert into advertise', 'thenatives' ),
        'uploaded_to_this_item' => __( 'Uploaded to this advertises', 'thenatives' ),
        'filter_items_list'     => __( 'Filter advertises list', 'thenatives' ),
        'items_list_navigation' => __( 'Advertises list navigation', 'thenatives' ),
        'items_list'            => __( 'Advertises list', 'thenatives' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position' 	 => 4,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'advertises' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'supports'           => array( 'title', 'revisions', 'editor', 'author', 'thumbnail', 'post-formats' ),
    );

    register_post_type( 'advertise', $args );

    //Create Post Type Event
    $labels = array(
        'name'                  => __( 'Events', 'thenatives' ),
        'singular_name'         => __( 'Event', 'thenatives' ),
        'menu_name'             => __( 'Events', 'thenatives' ),
        'name_admin_bar'        => __( 'Event', 'thenatives' ),
        'add_new'               => __( 'Add New', 'thenatives' ),
        'add_new_item'          => __( 'Add New Event', 'thenatives' ),
        'new_item'              => __( 'New Event', 'thenatives' ),
        'edit_item'             => __( 'Edit Event', 'thenatives' ),
        'view_item'             => __( 'View Event', 'thenatives' ),
        'all_items'             => __( 'All Events', 'thenatives' ),
        'search_items'          => __( 'Search Events', 'thenatives' ),
        'parent_item_colon'     => __( 'Parent Events:', 'thenatives' ),
        'not_found'             => __( 'No events found.', 'thenatives' ),
        'not_found_in_trash'    => __( 'No events found in Trash.', 'thenatives' ),
        'featured_image'        => __( 'Events Cover Image', 'thenatives' ),
        'set_featured_image'    => __( 'Set cover image', 'thenatives' ),
        'remove_featured_image' => __( 'Remove cover image', 'thenatives' ),
        'use_featured_image'    => __( 'Use as cover image', 'thenatives' ),
        'archives'              => __( 'Event archives', 'thenatives' ),
        'insert_into_item'      => __( 'Insert into event', 'thenatives' ),
        'uploaded_to_this_item' => __( 'Uploaded to this events', 'thenatives' ),
        'filter_items_list'     => __( 'Filter events list', 'thenatives' ),
        'items_list_navigation' => __( 'Events list navigation', 'thenatives' ),
        'items_list'            => __( 'Events list', 'thenatives' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position' 	 => 4,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'events' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'supports'           => array( 'title', 'revisions', 'editor', 'author', 'thumbnail', 'post-formats' ),
    );

    register_post_type( 'event', $args );

    //Create Post Type Sale
    $labels = array(
        'name'                  => __( 'Sales', 'thenatives' ),
        'singular_name'         => __( 'Sale', 'thenatives' ),
        'menu_name'             => __( 'Sales', 'thenatives' ),
        'name_admin_bar'        => __( 'Sale', 'thenatives' ),
        'add_new'               => __( 'Add New', 'thenatives' ),
        'add_new_item'          => __( 'Add New Sale', 'thenatives' ),
        'new_item'              => __( 'New Sale', 'thenatives' ),
        'edit_item'             => __( 'Edit Sale', 'thenatives' ),
        'view_item'             => __( 'View Sale', 'thenatives' ),
        'all_items'             => __( 'All Sales', 'thenatives' ),
        'search_items'          => __( 'Search Sales', 'thenatives' ),
        'parent_item_colon'     => __( 'Parent Sales:', 'thenatives' ),
        'not_found'             => __( 'No sale found.', 'thenatives' ),
        'not_found_in_trash'    => __( 'No sale found in Trash.', 'thenatives' ),
        'featured_image'        => __( 'Sales Cover Image', 'thenatives' ),
        'set_featured_image'    => __( 'Set cover image', 'thenatives' ),
        'remove_featured_image' => __( 'Remove cover image', 'thenatives' ),
        'use_featured_image'    => __( 'Use as cover image', 'thenatives' ),
        'archives'              => __( 'Sale archives', 'thenatives' ),
        'insert_into_item'      => __( 'Insert into sale', 'thenatives' ),
        'uploaded_to_this_item' => __( 'Uploaded to this sales', 'thenatives' ),
        'filter_items_list'     => __( 'Filter sales list', 'thenatives' ),
        'items_list_navigation' => __( 'Sales list navigation', 'thenatives' ),
        'items_list'            => __( 'Sales list', 'thenatives' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position' 	 => 4,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'sales' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'supports'           => array( 'title', 'revisions', 'editor', 'author', 'thumbnail', 'post-formats' ),
    );

    register_post_type( 'sale', $args );

    //Create Post Type Career
    $labels = array(
        'name'                  => __( 'Careers', 'thenatives' ),
        'singular_name'         => __( 'Career', 'thenatives' ),
        'menu_name'             => __( 'Careers', 'thenatives' ),
        'name_admin_bar'        => __( 'Career', 'thenatives' ),
        'add_new'               => __( 'Add New', 'thenatives' ),
        'add_new_item'          => __( 'Add New Career', 'thenatives' ),
        'new_item'              => __( 'New Career', 'thenatives' ),
        'edit_item'             => __( 'Edit Career', 'thenatives' ),
        'view_item'             => __( 'View Career', 'thenatives' ),
        'all_items'             => __( 'All Careers', 'thenatives' ),
        'search_items'          => __( 'Search Careers', 'thenatives' ),
        'parent_item_colon'     => __( 'Parent Careers:', 'thenatives' ),
        'not_found'             => __( 'No careers found.', 'thenatives' ),
        'not_found_in_trash'    => __( 'No careers found in Trash.', 'thenatives' ),
        'featured_image'        => __( 'Careers Cover Image', 'thenatives' ),
        'set_featured_image'    => __( 'Set cover image', 'thenatives' ),
        'remove_featured_image' => __( 'Remove cover image', 'thenatives' ),
        'use_featured_image'    => __( 'Use as cover image', 'thenatives' ),
        'archives'              => __( 'Career archives', 'thenatives' ),
        'insert_into_item'      => __( 'Insert into career', 'thenatives' ),
        'uploaded_to_this_item' => __( 'Uploaded to this careers', 'thenatives' ),
        'filter_items_list'     => __( 'Filter careers list', 'thenatives' ),
        'items_list_navigation' => __( 'Careers list navigation', 'thenatives' ),
        'items_list'            => __( 'Careers list', 'thenatives' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position' 	 => 4,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'careers' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'supports'           => array( 'title', 'revisions', 'editor', 'author', 'thumbnail', 'post-formats' ),
    );

    register_post_type( 'career', $args );

    /*//Create Post Type Career
    $labels = array(
        'name'                  => __( 'Sponsored', 'thenatives' ),
        'singular_name'         => __( 'Sponsored', 'thenatives' ),
        'menu_name'             => __( 'Sponsored', 'thenatives' ),
        'name_admin_bar'        => __( 'Sponsored', 'thenatives' ),
        'add_new'               => __( 'Add New', 'thenatives' ),
        'add_new_item'          => __( 'Add New Sponsored', 'thenatives' ),
        'new_item'              => __( 'New Sponsored', 'thenatives' ),
        'edit_item'             => __( 'Edit Sponsored', 'thenatives' ),
        'view_item'             => __( 'View Sponsored', 'thenatives' ),
        'all_items'             => __( 'All Sponsored', 'thenatives' ),
        'search_items'          => __( 'Search Sponsored', 'thenatives' ),
        'parent_item_colon'     => __( 'Parent Sponsored:', 'thenatives' ),
        'not_found'             => __( 'No sponsored found.', 'thenatives' ),
        'not_found_in_trash'    => __( 'No sponsored found in Trash.', 'thenatives' ),
        'featured_image'        => __( 'Sponsored Cover Image', 'thenatives' ),
        'set_featured_image'    => __( 'Set cover image', 'thenatives' ),
        'remove_featured_image' => __( 'Remove cover image', 'thenatives' ),
        'use_featured_image'    => __( 'Use as cover image', 'thenatives' ),
        'archives'              => __( 'Sponsored archives', 'thenatives' ),
        'insert_into_item'      => __( 'Insert into sponsored', 'thenatives' ),
        'uploaded_to_this_item' => __( 'Uploaded to this sponsored', 'thenatives' ),
        'filter_items_list'     => __( 'Filter sponsored list', 'thenatives' ),
        'items_list_navigation' => __( 'Sponsored list navigation', 'thenatives' ),
        'items_list'            => __( 'Sponsored list', 'thenatives' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position' 	 => 4,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'sponsored' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'supports'           => array( 'title', 'revisions', 'editor', 'author', 'thumbnail', 'post-formats' ),
    );

    register_post_type( 'sponsored', $args );*/
}