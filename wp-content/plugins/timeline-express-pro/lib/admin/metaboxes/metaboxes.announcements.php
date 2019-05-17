<?php
// Start with an underscore to hide fields from custom fields list
$prefix = 'announcement_';

$timeline_express_options = timeline_express_get_options();

// Setup the singular Name
$timeline_express_singular_name = apply_filters( 'timeline_express_singular_name', esc_html__( 'Announcement', 'timeline-express-pro' ) );

/**
 * Initiate the metabox
 */
$announcement_metabox = new_cmb2_box(
	array(
		'id'           => 'announcement_metabox',
		'title'        => sprintf( /* translators: Timeline Express singular post type name (eg: Announcement) */ esc_html__( '%s Info.', 'timeline-express-pro' ), $timeline_express_singular_name ),
		'object_types' => array( 'te_announcements' ),
		'context'      => 'advanced',
		'priority'     => 'high',
		'show_names'   => true,
	)
);

// Regular text field
$announcement_metabox->add_field(
	array(
		'name'    => sprintf( /* translators: Timeline Express singular post type name (eg: Announcement) */ esc_html__( '%s Color', 'timeline-express-pro' ), $timeline_express_singular_name ),
		'desc'    => sprintf( /* translators: Timeline Express singular post type name - lowercase (eg: announcement) */ esc_html__( 'Select the color for this %s.', 'timeline-express-pro' ), strtolower( $timeline_express_singular_name ) ),
		'id'      => $prefix . 'color',
		'type'    => 'colorpicker',
		'default' => $timeline_express_options['default-announcement-color'],
	)
);

// URL text field
$announcement_metabox->add_field(
	array(
		'name'    => sprintf( /* translators: Timeline Express singular post type name (eg: Announcement) */ esc_html__( '%s Icon', 'timeline-express-pro' ), $timeline_express_singular_name ),
		'desc'    => sprintf( /* translators: Timeline Express singular post type name - lowercase (eg: announcement) */ esc_html__( 'Select an icon from the drop down above. This is used for the icon associated with the %s.', 'timeline-express-pro' ), strtolower( $timeline_express_singular_name ) ),
		'id'      => $prefix . 'icon',
		'type'    => 'te_bootstrap_dropdown',
		'default' => 'fa-' . $timeline_express_options['default-announcement-icon'],
	)
);

// Email text field
$announcement_metabox->add_field(
	array(
		'name'        => sprintf( /* translators: Timeline Express singular post type name (eg: Announcement) */ esc_html__( '%s Date', 'timeline-express-pro' ), $timeline_express_singular_name ),
		'desc'        => sprintf( /* translators: Timeline Express singular post type name - lowercase (eg: announcement) */ esc_html__( 'Enter the date of the %s. Announcements will appear in chronological order according to this date.', 'timeline-express-pro' ), strtolower( $timeline_express_singular_name ) ),
		'id'          => $prefix . 'date',
		'type'        => 'text_date_timestamp',
		'default'     => strtotime( 'now' ),
		'date_format' => te_dateformat_php_to_jqueryui( get_option( 'date_format' ) ),
	)
);

// Email text field
$announcement_metabox->add_field(
	array(
		'name' => sprintf( /* translators: Timeline Express singular post type name (eg: Announcement) */ esc_html__( '%s Banner', 'timeline-express-pro' ), $timeline_express_singular_name ),
		'desc' => sprintf( /* translators: Timeline Express singular post type name - lowercase (eg: announcement) */ esc_html__( 'Select a banner image/video for this %s (optional). (recommended 650px wide or larger)', 'timeline-express-pro' ), strtolower( $timeline_express_singular_name ) ),
		'id'   => $prefix . 'image',
		'type' => 'file',
	)
);

// Initialize the timeline selection metabox
$announcement_metabox->add_field(
	array(
		'name'     => __( 'Associated Timeline', 'timeline-express-pro' ),
		'desc'     => __( 'Select a timeline to assign this announcement to.', 'timeline-express-pro' ),
		'id'       => $prefix . 'associated_timeline',
		'type'     => 'taxonomy_multicheck_inline',
		'taxonomy' => 'timeline',
		// Optional :
		'text'     => array(
			'no_terms_text' => __( 'No timelines found.', 'timeline-express-pro' ), // Change default text. Default: "No terms"
		),
	)
);

/**
 * Custom Container Classes Metabox
 *
 * Requires that the user defines a custom contant in functions.php
 * example: defined( 'TIMELINE_EXPRESS_CONTAINER_CLASSES', true )
 *
 * @since 1.2
 */
if ( defined( 'TIMELINE_EXPRESS_CONTAINER_CLASSES' ) && TIMELINE_EXPRESS_CONTAINER_CLASSES ) {
	$announcement_container_metabox = new_cmb2_box(
		array(
			'id'           => 'announcement_container_metabox',
			'title'        => esc_html__( 'Announcement Container Info.', 'timeline-express-pro' ),
			'object_types' => array( 'te_announcements' ), // Post type
			'context'      => 'advanced',
			'priority'     => 'high',
			'show_names'   => true, // Show field names on the left
		)
	);

	// Container class
	$announcement_container_metabox->add_field(
		array(
			'name' => esc_html__( 'Custom Container Class', 'timeline-express-pro' ),
			'desc' => esc_html__( 'Enter the class that you would like added to the announcement container on the timeline.', 'timeline-express-pro' ),
			'id'   => $prefix . 'container_classes',
			'type' => 'text',
		)
	);
}

/**
 * Initiate the sidebar metaboxs
 */

/**
 * Documentation sidebar Metabox
 */
$help_docs_metabox = new_cmb2_box(
	array(
		'id'           => 'help_docs_metabox',
		'title'        => esc_html__( 'Help & Documentation', 'timeline-express-pro' ),
		'object_types' => array( 'te_announcements' ),
		'context'      => 'side',
		'priority'     => 'low',
		'show_names'   => true,
	)
);

// Email text field
$help_docs_metabox->add_field(
	array(
		'name' => '',
		'desc' => '',
		'id'   => $prefix . 'help_docs',
		'type' => 'te_help_docs_metabox',
	)
);

// Filter here is to allow extra fields to be added
// loop to add fields to our array
$custom_fields = apply_filters( 'timeline_express_custom_fields', array() );

$i = 0;

// first, check if any custom fields are defined...
if ( ! empty( $custom_fields ) ) {

	foreach ( $custom_fields as $user_defined_field ) {

		$announcement_metabox->add_field( $custom_fields[ $i ] );

		$i++;

	}
}

// Action hook to allow users to hook in and define new metaboxes
do_action( 'timeline_express_metaboxes', $timeline_express_options );

/**
 * Ensure that when a YouTube URL is input, a value is stored in the _thumbnail_id
 * field. This allows has_post_thumbnail to return true, so we can filter the
 * markup and display the video in place of the featured image.
 *
 * @param int $post_id The post ID.
 * @param post $post The post object.
 * @param bool $update Whether this is an existing post being updated or not.
 */
function timeline_express_ensure_video_thumbnail_id( $post_id, $post, $update ) {

	// Is a revision, or not announcement post type.
	if ( wp_is_post_revision( $post_id ) || 'te_announcements' !== $post->post_type ) {

		return;

	}

	// Image not set.
	if ( ! isset( $_POST['announcement_image'] ) ) {

		return;

	}

	// Is an image.
	if ( wp_attachment_is_image( (int) $_POST['announcement_image_id'] ) ) {

		return;

	}

	$_POST['announcement_image_id'] = (int) PHP_INT_MAX;
	update_post_meta( $post_id, '_thumbnail_id', (int) PHP_INT_MAX );

}
add_action( 'save_post', 'timeline_express_ensure_video_thumbnail_id', 10, 3 );
