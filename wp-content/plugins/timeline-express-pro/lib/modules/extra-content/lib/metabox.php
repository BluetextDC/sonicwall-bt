<?php
// Start with an underscore to hide fields from custom fields list
$prefix = 'announcement_';

$timeline_express_options = timeline_express_get_options();

// Setup the singular Name
$timeline_express_singular_name = apply_filters( 'timeline_express_singular_name', esc_html__( 'Announcement', 'timeline-express-pro' ) );

/**
 * Initiate the metabox
 */
$announcement_extra_content_metabox = new_cmb2_box(
	array(
		'id'           => 'announcement_extra_content',
		'title'        => sprintf( /* translators: Timeline Express singular post type name (eg: Announcement) */ esc_html__( '%s Info.', 'timeline-express-pro' ), $timeline_express_singular_name ),
		'object_types' => array( 'te_announcements' ),
		'context'      => 'advanced',
		'priority'     => 'default',
		'show_names'   => true,
	)
);

// Regular text field
$announcement_extra_content_metabox->add_field(
	array(
		'name' => esc_html__( 'Before Content', 'timeline-express-pro' ),
		'desc' => sprintf(
			/* translators: 1. Timeline Express singular post type name - lowercase (eg: announcement) 2. Additional notes markup. */
			esc_html__( 'Add content before this %1$s on the timeline. %2$s', 'timeline-express-pro' ),
			strtolower( $timeline_express_singular_name ),
			/* translators: 1. Additional notes markup. */
			sprintf(
				'<br />%1$s',
				wp_kses_post( __( '<strong>Note:</strong> This will not be visible on horizontal timelines.', 'timeline-express' ) )
			)
		),
		'id'   => $prefix . 'before_content',
		'type' => 'wysiwyg',
	)
);

// Regular text field
$announcement_extra_content_metabox->add_field(
	array(
		'name' => esc_html__( 'After Content', 'timeline-express-pro' ),
		'desc' => sprintf(
			/* translators: Timeline Express singular post type name - lowercase (eg: announcement) */
			esc_html__( 'Add content after this %s on the timeline.', 'timeline-express-pro' ),
			strtolower( $timeline_express_singular_name )
		),
		'id'   => $prefix . 'after_content',
		'type' => 'wysiwyg',
	)
);
