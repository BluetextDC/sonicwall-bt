<?php
/**
 * Timeline Express Styles Filter Overrides
 */

/**
 * Additional container/icon styles from the metabox settings
 *
 * @since 1.0.0
 */
function timeline_express_inline_metabox_styles() {

	global $post;

	$container_styles = get_site_transient( "timeline_express_styles_announcement_{$post->ID}" );

	if ( false !== $container_styles && ! WP_DEBUG ) {

		wp_add_inline_style( 'timeline-express-icon-styles', $container_styles );

		return;

	}

	$metabox_values = timeline_express_styles_metabox_values( $post->ID );

	$styles = '';

	$icon_style        = $metabox_values['icon_style'];
	$icon_border_color = $metabox_values['icon_border_color'] ? $metabox_values['icon_border_color'] : 'inherit';
	$hide_icon         = $metabox_values['hide_icon'];

	if ( $hide_icon ) {

		$styles .= "
			#cd-timeline .announcement-{$post->ID}.icon-{$icon_style} .cd-timeline-img span.fa {
				display: none;
			}
		";

	}

	if ( 'style-default' !== $icon_style ) {

		$styles .= "
			#cd-timeline .announcement-{$post->ID}.icon-{$icon_style} .cd-timeline-img {
				border-color: {$icon_border_color};
			}
		";

	}

	// Hide Container Arrow
	if ( (bool) $metabox_values['container_hide_arrow'] ) {

		$styles .= "
			#cd-timeline .announcement-{$post->ID} .cd-timeline-content:before {
				border-color: transparent !important;
			}
		";

	}

	// Container Background Styles
	if ( ! (bool) $metabox_values['container_background']['inherit'] ) {

		$styles .= "
			#cd-timeline .announcement-{$post->ID} .cd-timeline-content {
				background: {$metabox_values['container_background']['color']};
			}
			#cd-timeline .announcement-{$post->ID}.timeline-announcement-left .cd-timeline-content:before {
				border-left-color: {$metabox_values['container_background']['color']};
			}
			#cd-timeline .announcement-{$post->ID}.timeline-announcement-right .cd-timeline-content:before {
				border-right-color: {$metabox_values['container_background']['color']};
			}
			@media only screen and (max-width: 821px) {
				#cd-timeline .announcement-{$post->ID} .cd-timeline-content:before {
					border-left-color: transparent !important;
					border-right-color: {$metabox_values['container_background']['color']};
				}
			}
		";

	}

	// Container Border Styles
	if ( ! (bool) $metabox_values['container_border']['inherit'] ) {

		$styles .= "
			#cd-timeline .announcement-{$post->ID} .cd-timeline-content {
				border-style: {$metabox_values['container_border']['style']};
				border-width: {$metabox_values['container_border']['width']}px;
				border-radius: {$metabox_values['container_border']['radius']}px;
				border-color: {$metabox_values['container_border']['color']};
			}
		";

	}

	// Container Shadow Styles
	if ( ! (bool) $metabox_values['container_shadow']['inherit'] ) {

		$color = empty( $metabox_values['container_shadow']['color'] ) ? 'transparent' : $metabox_values['container_shadow']['color'];

		$styles .= "
			#cd-timeline .announcement-{$post->ID} .cd-timeline-content {
				-webkit-box-shadow: {$metabox_values['container_shadow']['x_offset']}px {$metabox_values['container_shadow']['y_offset']}px {$metabox_values['container_shadow']['blur']}px {$metabox_values['container_shadow']['spread']}px {$color};
					 -moz-box-shadow: {$metabox_values['container_shadow']['x_offset']}px {$metabox_values['container_shadow']['y_offset']}px {$metabox_values['container_shadow']['blur']}px {$metabox_values['container_shadow']['spread']}px {$color};
								box-shadow: {$metabox_values['container_shadow']['x_offset']}px {$metabox_values['container_shadow']['y_offset']}px {$metabox_values['container_shadow']['blur']}px {$metabox_values['container_shadow']['spread']}px {$color};
			}
		";

	}

	// Container Title Styles
	if ( ! (bool) $metabox_values['container_title']['inherit'] ) {

		$size        = ( '0' === $metabox_values['container_title']['size'] ) ? '' : $metabox_values['container_title']['size'] . 'px';
		$style       = $metabox_values['container_title']['style'];
		$font_weight = $metabox_values['container_title']['font_weight'];
		$color       = empty( $metabox_values['container_title']['color'] ) ? 'transparent' : $metabox_values['container_title']['color'];

		$styles .= "
			#cd-timeline .announcement-{$post->ID} .cd-timeline-content .cd-timeline-item-title {
				color: {$color};
				font-size: {$size};
				font-style: {$style};
				font-weight: {$font_weight};
			}
		";

	}

	// Container Date Styles
	if ( ! (bool) $metabox_values['container_date']['inherit'] ) {

		$size        = ( '0' === $metabox_values['container_date']['size'] ) ? 'inherit' : $metabox_values['container_date']['size'] . 'px';
		$style       = $metabox_values['container_date']['style'];
		$font_weight = $metabox_values['container_date']['font_weight'];
		$color       = empty( $metabox_values['container_date']['color'] ) ? 'transparent' : $metabox_values['container_date']['color'];

		$styles .= "
			#cd-timeline .announcement-{$post->ID} .cd-timeline-content .timeline-date {
				color: {$color};
				font-size: {$size};
				font-style: {$style};
				font-weight: {$font_weight};
			}
		";

	}

	// Container Excerpt Styles
	if ( ! (bool) $metabox_values['container_excerpt']['inherit'] ) {

		$size        = ( '0' === $metabox_values['container_excerpt']['size'] ) ? 'inherit' : $metabox_values['container_excerpt']['size'] . 'px';
		$style       = $metabox_values['container_excerpt']['style'];
		$font_weight = $metabox_values['container_excerpt']['font_weight'];
		$color       = empty( $metabox_values['container_excerpt']['color'] ) ? 'transparent' : $metabox_values['container_excerpt']['color'];

		$styles .= "
			#cd-timeline .announcement-{$post->ID} .cd-timeline-content .the-excerpt :not( a ) {
				color: {$color};
				font-size: {$size};
				font-style: {$style};
				font-weight: {$font_weight};
			}
		";

	}

	// Custom CSS
	$custom_css = timeline_express_styles_metabox_values( $post->ID, 'custom_css' );

	if ( $custom_css && ! empty( $custom_css ) ) {

		$styles .= '.cd-timeline-block.announcement-' . $post->ID . ' ' . $custom_css;

	}

	$styles = apply_filters( 'timeline_express_container_styles', $styles, timeline_express_styles_metabox_values( $post->ID, 'container_style' ), $post->ID );

	set_site_transient( "timeline_express_styles_announcement_{$post->ID}", trim( $styles ) );

	wp_add_inline_style( 'timeline-express-icon-styles', trim( $styles ) );

}
add_action( 'timeline-express-container-top', 'timeline_express_inline_metabox_styles', PHP_INT_MAX );

/**
 * Add cotainer-style/icon-style shortcode attributes
 *
 * This allows users to override all containers/icons at the same time via shortcode
 * preventing them from having to go into each announcement to update them.
 *
 * eg:
 * [timeline-express icon-style="two"]
 * [timeline-express container-style="three"]
 *
 * @since 1.0.0
 */
function timeline_express_style_shortcode_attributes( $output, $pairs, $atts, $shortcode ) {

	$shortcode_atts = [
		'icon-style'          => isset( $atts['icon-style'] ) ? $atts['icon-style'] : false,
		'icon-animation'      => isset( $atts['icon-animation'] ) ? $atts['icon-animation'] : false,
		'container-style'     => isset( $atts['container-style'] ) ? $atts['container-style'] : false,
		'container-animation' => isset( $atts['container-animation'] ) ? $atts['container-animation'] : false,
	];

	// @codingStandardsIgnoreStart
	/**
	 * Append the icon container styles to the icon container.
	 *
	 * @param string  $class   String of classes.
	 * @param integer $post_id The announcement ID.
	 *
	 * @filter timeline-express-announcement-container-class
	 * @since  1.0.0
	 *
	 * @return string The final container classes.
	 */
	add_filter( 'timeline-express-announcement-container-class', function( $class, $post_id ) use ( $shortcode_atts ) {

		$icon_container_style         = timeline_express_styles_metabox_values( $post_id, 'icon_style' );
		$announcement_container_style = timeline_express_styles_metabox_values( $post_id, 'container_style' );

		if ( timeline_express_styles_metabox_values( $post_id, 'hide_icon' ) && 'small-dot' !== $icon_container_style && 'rotated-square' !== $icon_container_style ) {

			$class .= ' no-icon';

		}

		if ( ! $shortcode_atts['icon-style'] && ! $shortcode_atts['container-style'] ) {

			return $class . ' icon-' . $icon_container_style . ' container-' . $announcement_container_style;

		}

		$overrides = [];

		$overrides['icon']      = $shortcode_atts['icon-style'] ? 'style-' . $shortcode_atts['icon-style'] : $icon_container_style;
		$overrides['container'] = $shortcode_atts['container-style'] ? 'style-' . $shortcode_atts['container-style'] : $announcement_container_style;

		foreach ( $overrides as $type => $override ) {

			$class .= ' ' . $type . '-' . $override;

		}

		return $class;

	}, PHP_INT_MAX, 2 );

	/**
	 * Filter the container animation for the announcement.
	 *
	 * @filter timeline_express_container_animation
	 *
	 * @param  string  $animation The default animation.
	 * @param  integer $post_id   The announcement ID.
	 *
	 * @return string            The final filtered animation.
	 *
	 * @since 1.0.0
	 */
	add_filter( 'timeline_express_container_animation', function( $animation, $post_id ) use ( $shortcode_atts ) {

		return $shortcode_atts['container-animation'] ? timeline_express_styles_prefix_animation( $shortcode_atts['container-animation'] ) : timeline_express_styles_metabox_values( $post_id, 'container_animation' );

	}, PHP_INT_MAX, 2 );

	/**
	 * Filter the icon animation for the announcement.
	 *
	 * @filter timeline_express_icon_animation
	 *
	 * @param  string  $animation The default animation.
	 * @param  integer $post_id   The announcement ID.
	 *
	 * @return string            The final filtered animation.
	 *
	 * @since 1.0.0
	 */
	add_filter( 'timeline_express_icon_animation', function( $animation, $post_id ) use ( $shortcode_atts ) {

		$icon_style = $shortcode_atts['icon-style'] ? 'style-' . $shortcode_atts['icon-style'] : timeline_express_styles_metabox_values( $post_id, 'icon_style' );
		$animation  = $shortcode_atts['icon-animation'] ? timeline_express_styles_prefix_animation( $shortcode_atts['icon-animation'] ) : timeline_express_styles_metabox_values( $post_id, 'icon_animation' );
		$transform  = ( 'rotated-square' === $icon_style ) ? '-transform' : '';

		return $animation . $transform;

	}, PHP_INT_MAX, 2 );
	// @codingStandardsIgnoreEnd

	return $output;

}
add_filter( 'shortcode_atts_timeline-express', 'timeline_express_style_shortcode_attributes', 10, 4 );

/**
 * Container Specific Styles eg: style-one, style-two etc.
 *
 * @param  string $styles          The styles string to append to.
 * @param  string $container_style The current container style.
 *
 * @return string
 */
function timeline_express_container_styles( $styles, $container_style, $post_id ) {

	if ( 'style-default' === $container_style ) {

		return $styles;

	}

	$container_styles = timeline_express_styles_metabox_values( $post_id, 'container_style_styles' );

	if ( ! $container_styles || empty( $container_styles ) ) {

		return;

	}

	foreach ( $container_styles as $element => $style ) {

		$split_key = explode( ':', $element );

		if ( 1 >= count( $split_key ) ) {

			continue;

		}

		// Match the arrow to the title background container
		if (
			'style-one' === $container_style &&
			'.cd-timeline-title-container .cd-timeline-item-title' === $split_key[0]
		) {

			$styles .= "
				#cd-timeline .cd-timeline-block.timeline-announcement-right.announcement-{$post_id} .cd-timeline-content::before {
					border-right-color: {$style};
				}
				#cd-timeline .cd-timeline-block.timeline-announcement-left.announcement-{$post_id} .cd-timeline-content::before {
					border-left-color: {$style};
				}
				#cd-timeline .cd-timeline-block.announcement-{$post_id}.single-column .cd-timeline-content::before {
					border-right-color: {$style} !important;
					border-left-color: transparent !important;
				}
				#cd-timeline.horizontal-timeline .container-style-one.announcement-{$post_id} .cd-timeline-content:after {
					border-bottom-color: {$style};
				}
				@media only screen and (max-width: 821px) {
					#cd-timeline .cd-timeline-block.timeline-announcement-left.announcement-{$post_id} .cd-timeline-content::before,
					#cd-timeline .cd-timeline-block.timeline-announcement-right.announcement-{$post_id} .cd-timeline-content::before {
						border-left-color: transparent !important;
						border-right-color: {$style};
					}
				}
			";

		}

		if (
			'style-two' === $container_style &&
			'.timeline-express-read-more-link' === $split_key[0] &&
			'width' === $split_key[1]
		) {

			$style = ( '0' !== $style ) ? $style . '%' : 'inherit';

			$styles .= "
				#cd-timeline .cd-timeline-block.announcement-{$post_id} {$split_key[0]} {
					{$split_key[1]}: {$style};
				}
			";

		} else {

			$styles .= "
				#cd-timeline .cd-timeline-block.announcement-{$post_id} {$split_key[0]} {
					{$split_key[1]}: {$style};
				}
			";

		} // @codingStandardsIgnoreLine

	} // End foreach().

	return $styles;

}
add_filter( 'timeline_express_container_styles', 'timeline_express_container_styles', 10, 3 );

/**
 * Custom image sizes based on the container style
 *
 * @param  string $image_size       The original image size.
 * @param  integer $announcement_id The announcement ID.
 *
 * @since 1.0.0
 *
 * @return string                   The final image size.
 */
function styles_module_custom_announcement_image_size( $image_size, $announcement_id ) {

	$container_style = timeline_express_styles_metabox_values( $announcement_id, 'container_style' );

	if ( ! $container_style || empty( $container_style ) ) {

		return;

	}

	switch ( $container_style ) {

		case 'style-one':
			return 'full';

		default:
			return $image_size;

	}

}
add_filter( 'timeline-express-announcement-img-size', 'styles_module_custom_announcement_image_size', 10, 2 );

// @codingStandardsIgnoreStart
/**
 * Hide/Remove the read more links on a per announcement basis
 */
add_filter( 'timeline_express_icon_animation', function( $animation, $post_id ) {

	$icon_style = timeline_express_styles_metabox_values( $post_id, 'icon_style' );
	$animation  = timeline_express_styles_metabox_values( $post_id, 'icon_animation' );
	$transform  = ( 'rotated-square' === $icon_style ) ? '-transform' : '';

	return $animation . $transform;

}, PHP_INT_MAX, 2 );
// @codingStandardsIgnoreEnd
