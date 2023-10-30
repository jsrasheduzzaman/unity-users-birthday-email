<?php


$labels = array(
    'name' => _x('Birthday Emails', 'Post Type General Name', 'unity-birthday-email'),
    'singular_name' => _x('Birthday Email', 'Post Type Singular Name', 'unity-birthday-email'),
    'menu_name' => __('Birthday Emails', 'unity-birthday-email'),
    'parent_item_colon' => __('Parent Email', 'unity-birthday-email'),
    'all_items' => __('All Birthday Emails', 'unity-birthday-email'),
    'view_item' => __('View Birthday Email', 'unity-birthday-email'),
    'add_new_item' => __('Add New Birthday Email', 'unity-birthday-email'),
    'add_new' => __('Add New', 'unity-birthday-email'),
    'edit_item' => __('Edit Birthday Email Template', 'unity-birthday-email'),
    'update_item' => __('Update Birthday Email', 'unity-birthday-email'),
    'search_items' => __('Search Birthday Emails', 'unity-birthday-email'),
    'not_found' => __('Not Found', 'unity-birthday-email'),
    'not_found_in_trash' => __('Not found in Trash', 'unity-birthday-email'),
);

$args = array(
    'label' => 'cjlbdemails',
    'description' => __('Email Templates for Birthday Emails', 'unity-birthday-email'),
    'labels' => $labels,
    'supports' => array('title', 'editor'),
    'hierarchical' => false,
    'public' => false,
    'show_ui' => true,
    'show_in_menu' => false,
    'show_in_nav_menus' => false,
    'show_in_admin_bar' => false,
    'menu_position' => 5,
    'can_export' => false,
    'has_archive' => false,
    'exclude_from_search' => true,
    'publicly_queryable' => false,
    'capability_type' => 'page',
    'capabilities' => array(
        'create_posts' => 'do_not_allow',
    ),
    'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts
);

register_post_type('cjlbdemails', $args);
