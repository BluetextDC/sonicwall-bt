<?php

defined('ABSPATH') or die("No script kiddies please!");
$labels = array(
    'name' => _x('Smart Logo', 'post type general name', SMLS_TD),
    'singular_name' => _x('Smart Logo', 'post type singular name', SMLS_TD),
    'menu_name' => _x('Smart Logo', 'admin menu', SMLS_TD),
    'name_admin_bar' => _x('Smart Logo', 'add new on admin bar', SMLS_TD),
    'add_new' => _x('Add New', 'Smart Logo', SMLS_TD),
    'add_new_item' => __('Add New Smart Logo', SMLS_TD),
    'new_item' => __('New Smart Logo', SMLS_TD),
    'edit_item' => __('Edit Smart Logo', SMLS_TD),
    'view_item' => __('View Smart Logo', SMLS_TD),
    'all_items' => __('All Smart Logo', SMLS_TD),
    'search_items' => __('Search Smart Logo', SMLS_TD),
    'parent_item_colon' => __('Parent Smart Logo:', SMLS_TD),
    'not_found' => __('No Smart Logo found.', SMLS_TD),
    'not_found_in_trash' => __('No Smart Logo found in Trash.', SMLS_TD)
);

$args = array(
    'labels' => $labels,
    'description' => __('Description.', SMLS_TD),
    'public' => false,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'menu_icon' => 'dashicons-slides',
    'query_var' => true,
    'rewrite' => array('slug' => 'smart-logo-showcase'),
    'capability_type' => 'post',
    'has_archive' => true,
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array('title')
);

