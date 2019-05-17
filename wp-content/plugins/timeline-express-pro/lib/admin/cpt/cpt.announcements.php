<?php
/**
 * Register our Announcement Custom Post Type
 * used to easily manage the announcements on the site
 * By Code Parrots
 *
 * @link http://www.codeparrots.com
 *
 * @package WordPress
 * @subpackage Component
 * @since 1.2
 */

// store our options for use here
$timeline_express_options = timeline_express_get_options();

/**
 * Wrapped in apply_filters() twice, for legacy support.
 * Allow users to alter the timeline express slug.
 *
 * Legacy Filter: timeline-express-slug @since 1.1.3
 * New Filter: timeline_express_slug @since 1.2
 */
$announcement_slug = apply_filters( 'timeline_express_slug', apply_filters( 'timeline-express-slug', __( 'announcement', 'timeline-express-pro' ) ) );
/**
 * Allow users to alter the timeline express menu text (singular items)
 * @since 1.2
 */
$announcement_singular_text = apply_filters( 'timeline_express_singular_name', esc_html__( 'Announcement', 'timeline-express-pro' ) );
/**
 * Allow users to alter the timeline express menu text (plural items)
 * @since 1.2
 */
$announcement_plural_text = apply_filters( 'timeline_express_plural_name', esc_html__( 'Announcements', 'timeline-express-pro' ) );

/**
 * Custom Post Type Label Array
 */
$timeline_express_labels = array(
	'name'               => sprintf( /* translators: Plural custom post type name (ie: Announcements) */ esc_html__( 'Timeline Express %s', 'timeline-express-pro' ), $announcement_plural_text ),
	'singular_name'      => $announcement_singular_text,
	'menu_name'          => esc_html__( 'Timeline Express', 'timeline-express-pro' ),
	'parent_item_colon'  => esc_html__( 'Timeline Express:', 'timeline-express-pro' ),
	'all_items'          => sprintf( /* translators: Plural custom post type name (ie: Announcements) */ esc_html__( 'All %s', 'timeline-express-pro' ), $announcement_plural_text ),
	'view_item'          => sprintf( /* translators: Plural custom post type name (ie: Announcements) */ esc_html__( 'View %s', 'timeline-express-pro' ), $announcement_singular_text ),
	'add_new_item'       => sprintf( /* translators: Singular custom post type name (ie: Announcement) */ esc_html__( 'New %s', 'timeline-express-pro' ), $announcement_singular_text ),
	'add_new'            => sprintf( /* translators: Singular custom post type name (ie: Announcement) */ esc_html__( 'New %s', 'timeline-express-pro' ), $announcement_singular_text ),
	'edit_item'          => sprintf( /* translators: Singular custom post type name (ie: Announcement) */ esc_html__( 'Edit %s', 'timeline-express-pro' ), $announcement_singular_text ),
	'update_item'        => sprintf( /* translators: Singular custom post type name (ie: Announcement) */ esc_html__( 'Update %s', 'timeline-express-pro' ), $announcement_singular_text ),
	'search_items'       => sprintf( /* translators: Plural custom post type name (ie: Announcements) */ esc_html__( 'Search %s', 'timeline-express-pro' ), $announcement_plural_text ),
	'not_found'          => sprintf( /* translators: Singular custom post type name (ie: Announcement) */ esc_html__( 'No Timeline Express %s Found', 'timeline-express-pro' ), $announcement_plural_text ),
	'not_found_in_trash' => sprintf( /* translators: Plural custom post type name (ie: Announcements) */ esc_html__( 'No Timeline Express %s in Trash', 'timeline-express-pro' ), $announcement_plural_text ),
);

/**
 * Custom post type rewrite rules
 */
$timeline_express_rewrite = array(
	'slug'       => $announcement_slug,
	'with_front' => false,
	'pages'      => true,
	'feeds'      => true,
);

$announcement_support = apply_filters( 'timeline_express_announcement_supports', array( 'title', 'editor' ) );

// Enable/Disable comment support for announcements (global settings)
( isset( $timeline_express_options['enable_comments'] ) ) ? $announcement_support[] = 'comments' : null;

/**
 * Custom post type arguments
 */
$timeline_express_args = array(
	'label'               => 'timeline-express-announcement',
	'description'         => sprintf( /* translators: Plural custom post type name (ie: Announcements) */ esc_html__( 'Post type for adding timeline express %s to the site', 'timeline-express-pro' ), strtolower( $announcement_plural_text ) ),
	'labels'              => $timeline_express_labels,
	'supports'            => $announcement_support,
	'taxonomies'          => array( 'timeline', 'timeline_express_categories' ),
	'hierarchical'        => true,
	'public'              => true,
	'show_ui'             => true,
	'show_in_menu'        => true,
	'show_in_nav_menus'   => true,
	'show_in_admin_bar'   => true,
	'menu_position'       => 5,
	'menu_icon'           => TIMELINE_EXPRESS_URL . 'lib/admin/images/timeline-express-menu-icon.png',
	'can_export'          => true,
	'has_archive'         => true,
	'exclude_from_search' => ( isset( $timeline_express_options['announcement-appear-in-searches'] ) && 'true' === $timeline_express_options['announcement-appear-in-searches'] ) ? true : false,
	'publicly_queryable'  => true,
	'rewrite'             => $timeline_express_rewrite,
	'rest_base'           => $announcement_slug,
	'show_in_rest'        => 'WP_REST_Posts_Controller',
	'capability_type'     => 'page',
);

/**
 * Register the announcement post type.
 */
register_post_type( 'te_announcements', $timeline_express_args );
/* End release cycle cpt */

/**
 * Register our 'Categories' taxonomy
 */
$single_category_name = apply_filters( 'timeline_express_single_category_name', __( 'Category', 'timeline-express-pro' ) );
$plural_category_name = apply_filters( 'timeline_express_plural_category_name', __( 'Categories', 'timeline-express-pro' ) );

$field_args = array(
	'labels'       => array(
		'name'              => $plural_category_name,
		'singular_name'     => __( 'Category', 'timeline-express-pro' ),
		'search_items'      => sprintf( /* translators: Plural category name (ie: Categories) */ __( 'Search %s', 'timeline-express-pro' ), $plural_category_name ),
		'all_items'         => sprintf( /* translators: Plural category name (ie: Categories) */ __( 'All %s', 'timeline-express-pro' ), $plural_category_name ),
		'parent_item'       => sprintf( /* translators: Singular category name (ie: Category) */ __( 'Parent %s', 'timeline-express-pro' ), $single_category_name ),
		'parent_item_colon' => sprintf( /* translators: Singular category name (ie: Category) */ __( 'Parent %s:', 'timeline-express-pro' ), $single_category_name ),
		'edit_item'         => sprintf( /* translators: Singular category name (ie: Category) */ __( 'Edit %s', 'timeline-express-pro' ), $single_category_name ),
		'update_item'       => sprintf( /* translators: Singular category name (ie: Category) */ __( 'Update %s', 'timeline-express-pro' ), $single_category_name ),
		'add_new_item'      => sprintf( /* translators: Singular category name (ie: Category) */ __( 'Add New %s', 'timeline-express-pro' ), $single_category_name ),
		'new_item_name'     => sprintf( /* translators: Singular category name (ie: Category) */ __( 'New %s', 'timeline-express-pro' ), $single_category_name ),
		'menu_name'         => $plural_category_name,
	),
	'hierarchical' => true,
	'rewrite'      => array(
		'slug' => strtolower( $single_category_name ),
	),
);
register_taxonomy( 'timeline_express_categories', 'te_announcements', $field_args );

/**
 * Register our 'Timeline' taxonomy
 */
$single_timeline_name = apply_filters( 'timeline_express_single_timeline_name', __( 'Timeline', 'timeline-express-pro' ) );
$plural_timeline_name = apply_filters( 'timeline_express_plural_timeline_name', __( 'Timelines', 'timeline-express-pro' ) );

$labels = array(
	'name'              => $plural_timeline_name,
	'singular_name'     => $single_timeline_name,
	'search_items'      => sprintf( /* translators: Plural timeline name (ie: Timelines) */ __( 'Search %s', 'timeline-express-pro' ), $plural_timeline_name ),
	'all_items'         => sprintf( /* translators: Plural timeline name (ie: Timelines) */ __( 'All %s', 'timeline-express-pro' ), $plural_timeline_name ),
	'parent_item'       => sprintf( /* translators: Singular timeline name (ie: Timeline) */ __( 'Parent %s', 'timeline-express-pro' ), $single_timeline_name ),
	'parent_item_colon' => sprintf( /* translators: Singular timeline name (ie: Timeline) */ __( 'Parent %s:', 'timeline-express-pro' ), $single_timeline_name ),
	'edit_item'         => sprintf( /* translators: Singular timeline name (ie: Timeline) */ __( 'Edit %s', 'timeline-express-pro' ), $single_timeline_name ),
	'update_item'       => sprintf( /* translators: Singular timeline name (ie: Timeline) */ __( 'Update %s', 'timeline-express-pro' ), $single_timeline_name ),
	'add_new_item'      => sprintf( /* translators: Singular timeline name (ie: Timeline) */ __( 'Add New %s', 'timeline-express-pro' ), $single_timeline_name ),
	'new_item_name'     => sprintf( /* translators: Singular timeline name (ie: Timeline) */ __( 'New %s Name', 'timeline-express-pro' ), $single_timeline_name ),
	'menu_name'         => $plural_timeline_name,
);

$args = array(
	'hierarchical'      => false,
	'labels'            => $labels,
	'show_ui'           => true,
	'show_admin_column' => true,
	'query_var'         => true,
	'rewrite'           => array(
		'slug' => strtolower( $single_timeline_name ),
	),
);

register_taxonomy( 'timeline', array( 'te_announcements' ), $args );

/* Flush the re-write rules/permalinks - prevents 404 on initial plugin activation */
$set = get_option( 'post_type_rules_flushed_te-announcements', false );

if ( ! $set ) {

	flush_rewrite_rules( false );

	update_option( 'post_type_rules_flushed_te-announcements', true );

}
