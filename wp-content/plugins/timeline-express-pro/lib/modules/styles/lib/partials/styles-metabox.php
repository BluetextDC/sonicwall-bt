<?php
/**
 * Additional Metaboxes on the Announcements post types
 *
 * @since 1.0.0
 */

function timeline_express_styles_metaboxes() {

	$box_options = [
		'id'           => 'announcement_styles_metabox',
		'title'        => sprintf(
			/* translators: Timeline Express singular post type name (eg: Announcement) */
			esc_html__( '%s Styles', 'timeline-express-pro' ),
			ucfirst( Timeline_Express_Styles::$announcement_singular )
		),
		'object_types' => [ 'te_announcements' ],
		'show_names'   => true,
		'context'      => 'advanced',
		'priority'     => 'high',
	];

	$cmb = new_cmb2_box( $box_options );

	$prefix = '_timeline_styles';

	$meta_tabs = [
		'config' => $box_options,
		'layout' => 'vertical',
		'tabs'   => [
			[
				'id'     => 'icon_styles',
				'title'  => esc_html__( 'Icon Styles', 'timeline-express-pro' ),
				'fields' => [
					[
						'name'             => esc_html__( 'Icon Animation', 'timeline-express-pro' ),
						'desc'             => sprintf(
							/* translators: Timeline Express singular post type name (eg: announcement) */
							esc_html__( 'Select the animation for the %s icon.', 'timeline-express-pro' ),
							esc_html( Timeline_Express_Styles::$announcement_singular )
						),
						'id'               => $prefix . '_icon_animation',
						'type'             => 'select',
						'show_option_none' => false,
						'default'          => 'bounce-in',
						'options'          => get_timeline_express_animations( 'icon' ),
					],
					[
						'name'             => esc_html__( 'Icon Style', 'timeline-express-pro' ),
						'desc'             => sprintf(
							/* translators: 1. Singular announcement name (eg: announcement). */
							esc_html__( 'Select the icon style for this %s.', 'timeline-express-pro' ),
							esc_html( Timeline_Express_Styles::$announcement_singular )
						),
						'id'               => $prefix . '_icon_style',
						'type'             => 'radio',
						'show_option_none' => false,
						'default'          => 'bounce-in',
						'options'          => [
							'bounce-in'      => esc_html__( 'Default', 'timeline-express-pro' ),
							'small-dot'      => esc_html__( 'Small Dot (no icon)', 'timeline-express-pro' ),
							'square'         => esc_html__( 'Square', 'timeline-express-pro' ),
							'rotated-square' => esc_html__( 'Rotated Square', 'timeline-express-pro' ),
						],
					],
					[
						'name'    => esc_html__( 'Border color', 'timeline-express-pro' ),
						'id'      => $prefix . '_icon_border_style',
						'type'    => 'colorpicker',
						'default' => '#ffffff',
					],
					[
						'name'        => esc_html__( 'Hide Icon', 'timeline-express-pro' ),
						'id'          => $prefix . '_hide_icon',
						'type'        => 'checkbox',
						'default'     => false,
						'description' => sprintf(
							/* translators: Announcement singular name. */
							esc_html__( 'Hide the icon associated with this %s.', 'timeline-express-pro' ),
							Timeline_Express_Styles::$announcement_singular
						),

					],
				],
			],
			[
				'id'     => 'container_styles',
				'title'  => __( 'Container Styles', 'timeline-express-pro' ),
				'fields' => [
					[
						'name'             => esc_html__( 'Container Style', 'timeline-express-pro' ),
						'desc'             => sprintf(
							/* translators: 1. Singular announcement name (eg: announcement). */
							esc_html__( 'Select the conatiner style for this %s.', 'timeline-express-pro' ),
							esc_html( Timeline_Express_Styles::$announcement_singular )
						),
						'id'               => $prefix . '_container_style',
						'type'             => 'select',
						'show_option_none' => false,
						'default'          => 'default',
						'options'          => timeline_express_styles_styles( 'container' ),
					],
					[
						'name'             => esc_html__( 'Container Animation', 'timeline-express-pro' ),
						'desc'             => sprintf(
							/* translators: Timeline Express singular post type name (eg: announcement) */
							esc_html__( 'Select the animation for the %s container.', 'timeline-express-pro' ),
							esc_html( Timeline_Express_Styles::$announcement_singular )
						),
						'id'               => $prefix . '_container_animation',
						'type'             => 'select',
						'show_option_none' => false,
						'default'          => 'bounce-in',
						'options'          => get_timeline_express_animations( 'container' ),
					],
					[
						'name'    => esc_html__( 'Container Arrow', 'timeline-express-pro' ),
						'desc'    => sprintf(
							/* translators: Timeline Express singular post type name (eg: announcement) */
							esc_html__( 'Hide the %s container arrow pointing to the icon.', 'timeline-express-pro' ),
							esc_html( Timeline_Express_Styles::$announcement_singular )
						),
						'id'      => $prefix . '_container_hide_arrow',
						'type'    => 'checkbox',
						'default' => false,
					],
					[
						'name'    => esc_html__( 'Container Background', 'timeline-express-pro' ),
						'id'      => $prefix . '_container_background_color', // here
						'type'    => 'container_background_color',
						'default' => [
							'inherit' => true,
							'color'   => timeline_express_get_options( 'announcement-bg-color' ),
						],
					],
					[
						'name'    => esc_html__( 'Container Border', 'timeline-express-pro' ),
						'id'      => $prefix . '_container_border',
						'type'    => 'container_border',
						'default' => [
							'inherit' => true,
							'style'   => 'solid',
							'width'   => '1px',
							'radius'  => '0px',
							'color'   => '',
						],
					],
					[
						'name'    => esc_html__( 'Container Shadow', 'timeline-express-pro' ),
						'id'      => $prefix . '_container_shadow',
						'type'    => 'container_shadow',
						'default' => [
							'inherit'  => true,
							'x_offset' => '0',
							'y_offset' => '3',
							'blur'     => '0',
							'spread'   => '0',
							'color'    => '#B9C5CD',
						],
					],
				],
			],
			[
				'id'     => 'title_styles',
				'title'  => __( 'Title Styles', 'timeline-express-pro' ),
				'fields' => [
					[
						'name'    => __( 'Styles', 'timeline-express-pro' ),
						'id'      => $prefix . '_container_title_styles',
						'type'    => 'container_title_styles',
						'default' => [
							'inherit'     => true,
							'color'       => '#333333',
							'size'        => '0',
							'style'       => 'normal',
							'font_weight' => 'inherit',
						],
					],
				],
			],
		],
	];

	// set tabs
	$cmb->add_field(
		[
			'id'   => '__tabs',
			'type' => 'tabs',
			'tabs' => (array) apply_filters( 'timeline_express_styles_meta_tabs', $meta_tabs, $prefix ),
		]
	);

}
add_filter( 'cmb2_init', 'timeline_express_styles_metaboxes' );

/**
 * Conditionally display the read more styles tab.
 *
 * @param  array  $meta_tabs Array of metabox tabs.
 * @param  string $prefix    Prefix for our meta field.
 *
 * @filter timeline_express_styles_meta_tabs
 * @since  2.0.0
 *
 * @return array             Final array of metaboxes.
 */
function display_conditional_tabs( $meta_tabs, $prefix ) {

	if ( (bool) timeline_express_get_options( 'date-visibility' ) ) {

		$meta_tabs['tabs'][] = [
			'id'     => 'date_styles',
			'title'  => __( 'Date Styles', 'timeline-express-pro' ),
			'fields' => [
				[
					'name'    => __( 'Placeholder', 'timeline-express-pro' ),
					'id'      => $prefix . '_container_date_styles',
					'type'    => 'container_date_styles',
					'default' => [
						'inherit'     => true,
						'color'       => '#333333',
						'size'        => '0',
						'style'       => 'normal',
						'font_weight' => 'inherit',
					],
				],
			],
		];

	}

	if ( (bool) timeline_express_get_options( 'read-more-visibility' ) ) {

		$meta_tabs['tabs'][] = [
			'id'     => 'excerpt_styles',
			'title'  => __( 'Excerpt Styles', 'timeline-express-pro' ),
			'fields' => [
				[
					'name'    => __( 'Excerpt Styles', 'timeline-express-pro' ),
					'id'      => $prefix . '_container_excerpt_styles',
					'type'    => 'container_excerpt_styles',
					'default' => [
						'inherit'     => true,
						'color'       => '#333333',
						'size'        => '0',
						'style'       => 'normal',
						'font_weight' => 'inherit',
					],
				],
			],
		];

	}

	if ( (bool) apply_filters( 'timeline_express_styles_read_more_visibility', true ) ) {

		$meta_tabs['tabs'][] = [
			'id'     => 'read_more',
			'title'  => __( 'Read More' ),
			'fields' => [
				[
					'name'        => __( 'Disable Read More', 'timeline-express-pro' ),
					'id'          => $prefix . '_hide_read_more',
					'default'     => false,
					'type'        => 'checkbox',
					'description' => sprintf(
						/* translators: Announcement singular name. */
						esc_html__( "Disable the 'Read More' links for this %s.", 'timeline-express-pro' ),
						Timeline_Express_Styles::$announcement_singular
					),
				],
			],
		];

	}

	if ( (bool) apply_filters( 'timeline_express_styles_custom_css', true ) ) {

		$meta_tabs['tabs'][] = [
			'id'     => 'custom_css',
			'title'  => __( 'Custom CSS', 'timeline-express-pro' ),
			'fields' => [
				[
					'name'        => __( 'Custom CSS', 'timeline-express-pro' ),
					'id'          => $prefix . '_container_custom_css',
					'type'        => 'textarea_code',
					'description' => sprintf(
						/* translators: 1. Announcement singular name. (eg: annoucement) 2. Link to help documentation. */
						esc_html__( 'Enter custom CSS for this %1$s. Note: Styles added here will only affect this %1$s. %2$s', 'timeline-express-pro' ),
						esc_html( Timeline_Express_Styles::$announcement_singular ),
						sprintf(
							'<a href="%1$s">%2$s</a>',
							'#',
							'Help'
						)
					),
				],
			],
		];

	}

	return $meta_tabs;

}
add_filter( 'timeline_express_styles_meta_tabs', 'display_conditional_tabs', 10, 2 );

/**
 * Wrap in additional divs for show/hide js.
 *
 * @param  array  $args   Argument array.
 * @param  object $field  Field object.
 *
 * @return mixed          Markup for the div wrap.
 *
 * @since 2.0.0
 */
function before_styles_row( $args, $field ) {

	global $post;

	$container_style = timeline_express_styles_metabox_values( $post->ID, 'container_style' );
	$hidden          = ( $container_style === $args['style'] ) ? '' : ' hidden';

	return "<div class='te-attributes-container {$args['style']}-attributes{$hidden} cmb-row'>";

}

/**
 * Close the div wrap for our show/hide js.
 *
 * @param  array  $args   Argument array.
 * @param  object $field  Field object.
 *
 * @return mixed          Markup for the closing div wrap.
 *
 * @since 2.0.0
 */
function after_styles_row( $args, $field ) {

	return '</div>';

}

/**
 * Load our conditional metabox groups.
 *
 * @since 1.0.0
 */
function styles_metabox_groups( $meta_tabs, $prefix ) {

	foreach ( glob( TIMELINE_EXPRESS_STYLES_PATH . 'lib/partials/metabox-groups/*.php' ) as $file ) {

		array_splice( $meta_tabs['tabs'][1]['fields'], 1, 0, include_once( $file ) );

	}

	return $meta_tabs;

}
add_filter( 'timeline_express_styles_meta_tabs', 'styles_metabox_groups', 10, 2 );

/**
 * Include our custom CMB2 fields.
 *
 * @since 1.0.0
 */
foreach ( glob( TIMELINE_EXPRESS_STYLES_PATH . 'lib/partials/cmb2-fields/*.php' ) as $file ) {

	include_once( $file );

}
