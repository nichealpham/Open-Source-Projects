<?php
add_action('init', 'theme_register_taxonomy');
function theme_register_taxonomy() {
    //Create Taxonomy Advertises
    $labels = array(
        'name' => __('Advertise Categories'),
        'singular_name' => __('Advertise Categories'),
        'search_items' => __('Search Advertise Categories'),
        'popular_items' => __('Popular Advertise Categories'),
        'all_items' => __('All Advertise Categories'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Advertise Categories'),
        'update_item' => __('Update Advertise Categories'),
        'add_new_item' => __('Add Advertise Categories'),
        'new_item_name' => __('New Advertise Categories'),
        'menu_name' => __('Advertise Categories'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'advertise-categories'),
    );

    register_taxonomy('advertise-categories', array('advertise'), $args);

    //Create Taxonomy Categories for Event
    $labels = array(
        'name' => __('Event Categories'),
        'singular_name' => __('Event City'),
        'search_items' => __('Search Event Categories'),
        'popular_items' => __('Popular Event Categories'),
        'all_items' => __('All Event Categories'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Event Categories'),
        'update_item' => __('Update Event Categories'),
        'add_new_item' => __('Add Event Categories'),
        'new_item_name' => __('New Event Categories'),
        'menu_name' => __('Categories'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'event-categories'),
    );

    register_taxonomy('event-categories', array('event'), $args);

    //Create Taxonomy Categories for Sale
    $labels = array(
        'name' => __('Sale Categories'),
        'singular_name' => __('Sale City'),
        'search_items' => __('Search Sale Categories'),
        'popular_items' => __('Popular Sale Categories'),
        'all_items' => __('All Sale Categories'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Sale Categories'),
        'update_item' => __('Update Sale Categories'),
        'add_new_item' => __('Add Sale Categories'),
        'new_item_name' => __('New Sale Categories'),
        'menu_name' => __('Categories'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'sale-categories'),
    );

    register_taxonomy('sale-categories', array('sale'), $args);

    //Create Taxonomy Cities for Events
    $labels = array(
        'name' => __('Event Cities'),
        'singular_name' => __('Event City'),
        'search_items' => __('Search Event Cities'),
        'popular_items' => __('Popular Event Cities'),
        'all_items' => __('All Event Cities'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Event Cities'),
        'update_item' => __('Update Event Cities'),
        'add_new_item' => __('Add Event Cities'),
        'new_item_name' => __('New Event Cities'),
        'menu_name' => __('Cities'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'event-cities'),
    );

    register_taxonomy('event-cities', array('event'), $args);//Create Taxonomy Cities for Sales

    $labels = array(
        'name' => __('Event Packages'),
        'singular_name' => __('Event Package'),
        'search_items' => __('Search Event Packages'),
        'popular_items' => __('Popular Event Packages'),
        'all_items' => __('All Event Packages'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Event Packages'),
        'update_item' => __('Update Event Packages'),
        'add_new_item' => __('Add Event Packages'),
        'new_item_name' => __('New Event Packages'),
        'menu_name' => __('Packages'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'event-packages'),
    );

    register_taxonomy('event-packages', array('event'), $args);

    //Create Taxonomy Cities for Sales
    $labels = array(
        'name' => __('Sale Cities'),
        'singular_name' => __('Sale City'),
        'search_items' => __('Search Sale Cities'),
        'popular_items' => __('Popular Sale Cities'),
        'all_items' => __('All Sale Cities'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Sale Cities'),
        'update_item' => __('Update Sale Cities'),
        'add_new_item' => __('Add Sale Cities'),
        'new_item_name' => __('New Sale Cities'),
        'menu_name' => __('Cities'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'sale-cities'),
    );

    register_taxonomy('sale-cities', array('sale'), $args);

    //Create Taxonomy Cities for Sales
    $labels = array(
        'name' => __('Sale Packages'),
        'singular_name' => __('Sale Package'),
        'search_items' => __('Search Sale Packages'),
        'popular_items' => __('Popular Sale Packages'),
        'all_items' => __('All Sale Packages'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Sale Packages'),
        'update_item' => __('Update Sale Packages'),
        'add_new_item' => __('Add Sale Packages'),
        'new_item_name' => __('New Sale Packages'),
        'menu_name' => __('Packages'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'sale-packages'),
    );

    register_taxonomy('sale-packages', array('sale'), $args);

    //Create Taxonomy Company for Careers
    $labels = array(
        'name' => __('Career Companies'),
        'singular_name' => __('Career Company'),
        'search_items' => __('Search Career Companies'),
        'popular_items' => __('Popular Career Companies'),
        'all_items' => __('All Career Companies'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Career Companies'),
        'update_item' => __('Update Career Companies'),
        'add_new_item' => __('Add Career Companies'),
        'new_item_name' => __('New Career Companies'),
        'menu_name' => __('Companies'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'career-companies'),
    );

    register_taxonomy('career-companies', array('career'), $args);

    //Create Taxonomy Cities for Careers
    $labels = array(
        'name' => __('Career Cities'),
        'singular_name' => __('Career City'),
        'search_items' => __('Search Career Cities'),
        'popular_items' => __('Popular Career Cities'),
        'all_items' => __('All Career Cities'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Career Cities'),
        'update_item' => __('Update Career Cities'),
        'add_new_item' => __('Add Career Cities'),
        'new_item_name' => __('New Career Cities'),
        'menu_name' => __('Cities'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'career-cities'),
    );

    register_taxonomy('career-cities', array('career'), $args);

    //Create Taxonomy Levels for Careers
    $labels = array(
        'name' => __('Career Levels'),
        'singular_name' => __('Career Level'),
        'search_items' => __('Search Career Levels'),
        'popular_items' => __('Popular Career Levels'),
        'all_items' => __('All Career Levels'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Career Levels'),
        'update_item' => __('Update Career Levels'),
        'add_new_item' => __('Add Career Levels'),
        'new_item_name' => __('New Career Levels'),
        'menu_name' => __('Levels'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'career-levels'),
    );

    register_taxonomy('career-levels', array('career'), $args);

    //Create Taxonomy Types for Careers
    $labels = array(
        'name' => __('Career Types'),
        'singular_name' => __('Career Type'),
        'search_items' => __('Search Career Types'),
        'popular_items' => __('Popular Career Types'),
        'all_items' => __('All Career Types'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Career Types'),
        'update_item' => __('Update Career Types'),
        'add_new_item' => __('Add Career Types'),
        'new_item_name' => __('New Career Types'),
        'menu_name' => __('Types'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'career-types'),
    );

    register_taxonomy('career-types', array('career'), $args);

    //Create Taxonomy Company for Package
    $labels = array(
        'name' => __('Career Packages'),
        'singular_name' => __('Career Package'),
        'search_items' => __('Search Career Packages'),
        'popular_items' => __('Popular Career Packages'),
        'all_items' => __('All Career Packages'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Career Packages'),
        'update_item' => __('Update Career Packages'),
        'add_new_item' => __('Add Career Packages'),
        'new_item_name' => __('New Career Packages'),
        'menu_name' => __('Packages'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'career-packages'),
    );

    register_taxonomy('career-packages', array('career'), $args);
}