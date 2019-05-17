<?php
/**
 * Timeline Express Style Helpers
 *
 * @since 1.0.0
 *
 * @todo Setup our options page to allow users to override the defaults
 *       when setting up a new announcement.
 */
function timeline_express_styles_options( $option = false ) {

	$default_options = [
		'container_style' => 'style-default',
		'icon_style'      => 'style-default',
	];

	$options = get_option( TIMELINE_EXPRESS_STYLES_OPTION, $default_options );

	return ! $option ? $options : $options[ $option ];

}

/**
 * Return all of the styles metabox values.
 *
 * @uses  timeline_express_style_metabox_options
 * @since 1.0.0
 *
 * @param  boolean $post_id The ID of the announcement to retreive meta for.
 *
 * @return array            _timeline_styles_ meta value array.
 */
function timeline_express_styles_metabox_values( $post_id = false, $value = '' ) {

	if ( ! $post_id ) {

		return;

	}

	$inherit = [
		'inherit' => true,
	];

	$metabox_options = [
		'container_style'        => get_post_meta( $post_id, '_timeline_styles_container_style', true ) ? get_post_meta( $post_id, '_timeline_styles_container_style', true ) : 'style-default',
		'container_style_styles' => get_post_meta( $post_id, '_timeline_styles_container_style_styles', true ) ? get_post_meta( $post_id, '_timeline_styles_container_style_styles', true ) : false,
		'container_animation'    => get_post_meta( $post_id, '_timeline_styles_container_animation', true ) ? get_post_meta( $post_id, '_timeline_styles_container_animation', true ) : 'bounce-in',
		'container_hide_arrow'   => get_post_meta( $post_id, '_timeline_styles_container_hide_arrow', true ) ? get_post_meta( $post_id, '_timeline_styles_container_hide_arrow', true ) : false,
		'container_background'   => get_post_meta( $post_id, '_timeline_styles_container_background_color', true ) ? get_post_meta( $post_id, '_timeline_styles_container_background_color', true ) : $inherit,
		'container_border'       => get_post_meta( $post_id, '_timeline_styles_container_border', true ) ? get_post_meta( $post_id, '_timeline_styles_container_border', true ) : $inherit,
		'container_shadow'       => get_post_meta( $post_id, '_timeline_styles_container_shadow', true ) ? get_post_meta( $post_id, '_timeline_styles_container_shadow', true ) : $inherit,
		'container_title'        => get_post_meta( $post_id, '_timeline_styles_container_title_styles', true ) ? get_post_meta( $post_id, '_timeline_styles_container_title_styles', true ) : $inherit,
		'container_date'         => get_post_meta( $post_id, '_timeline_styles_container_date_styles', true ) ? get_post_meta( $post_id, '_timeline_styles_container_date_styles', true ) : $inherit,
		'container_excerpt'      => get_post_meta( $post_id, '_timeline_styles_container_excerpt_styles', true ) ? get_post_meta( $post_id, '_timeline_styles_container_excerpt_styles', true ) : $inherit,
		'icon_animation'         => get_post_meta( $post_id, '_timeline_styles_icon_animation', true ) ? get_post_meta( $post_id, '_timeline_styles_icon_animation', true ) : 'bounce-in',
		'icon_style'             => get_post_meta( $post_id, '_timeline_styles_icon_style', true ) ? get_post_meta( $post_id, '_timeline_styles_icon_style', true ) : 'style-default',
		'icon_border_color'      => get_post_meta( $post_id, '_timeline_styles_icon_border_style', true ),
		'hide_icon'              => get_post_meta( $post_id, '_timeline_styles_hide_icon', true ),
		'hide_read_more'         => get_post_meta( $post_id, '_timeline_styles_hide_read_more', true ),
		'custom_css'             => get_post_meta( $post_id, '_timeline_styles_container_custom_css', true ),
	];

	$meta_array = (array) apply_filters( 'timeline_express_style_metabox_options', $metabox_options, $post_id );

	return ( ! empty( $value ) && isset( $meta_array[ $value ] ) ) ? $meta_array[ $value ] : $meta_array;

}

/**
 * Prefix the animation where needed.
 *
 * Note: We must prefix everything but the default, bounce-in.
 *
 * @param  string $animation The animation name to prefix.
 *
 * @return string            Animation name.
 */
function timeline_express_styles_prefix_animation( $animation = 'bounce-in' ) {

	return ( 'bounce-in' !== $animation ? 'te-' : '' ) . $animation;

}

/**
 * Return an array of available styles.
 *
 * @param string $type The style type to retreive. Cotnainer|Icon
 *
 * @return array Available styles to choose from.
 *
 * @since 1.0.0
 */
function timeline_express_styles_styles( $type = 'container' ) {

	$styles = [
		'style-default' => esc_html__( 'Default', 'timeline-express-pro' ),
		'style-one'     => esc_html__( 'Style One', 'timeline-express-pro' ),
		'style-two'     => esc_html__( 'Style Two', 'timeline-express-pro' ),
		'style-three'   => esc_html__( 'Style Three', 'timeline-express-pro' ),
	];

	/**
	 * Filter the array of possible container/icon styles.
	 *
	 * @param array  $styles The style array
	 * @param string $type The style type to retreive. Cotnainer|Icon
	 *
	 * @since 1.0.0
	 */

	return (array) apply_filters( 'timeline_express_styles_styles', $styles, $type );

}

/**
 * Retreive an array of Timeline Express Styles animations
 *
 * @param  string $type Animations to retreive. Possible 'container' or 'icon'.
 *
 * @since 1.0.0
 *
 * @return array        Filtered array list of animations.
 */
function get_timeline_express_animations( $type ) {

	$animations = [
		'bounce-in'            => esc_html__( 'Default', 'timeline-express-pro' ),
		'te-bounce'            => esc_html__( 'Bounce', 'timeline-express-pro' ),
		'te-flash'             => esc_html__( 'Flash', 'timeline-express-pro' ),
		'te-pulse'             => esc_html__( 'Pulse', 'timeline-express-pro' ),
		'te-rubberBand'        => esc_html__( 'Rubber Band', 'timeline-express-pro' ),
		'te-shake'             => esc_html__( 'Shake', 'timeline-express-pro' ),
		'te-tada'              => esc_html__( 'Tada', 'timeline-express-pro' ),
		'te-wobble'            => esc_html__( 'Wobble', 'timeline-express-pro' ),
		'te-jello'             => esc_html__( 'Jello', 'timeline-express-pro' ),
		'te-swing'             => esc_html__( 'Swing', 'timeline-express-pro' ),
		'te-bounceIn'          => esc_html__( 'Bounce In', 'timeline-express-pro' ),
		'te-bounceInDown'      => esc_html__( 'Bounce In Down', 'timeline-express-pro' ),
		'te-bounceInLeft'      => esc_html__( 'Bounce In Left', 'timeline-express-pro' ),
		'te-bounceInRight'     => esc_html__( 'Bounce In Right', 'timeline-express-pro' ),
		'te-fadeIn'            => esc_html__( 'Fade In', 'timeline-express-pro' ),
		'te-fadeInDown'        => esc_html__( 'Fade In - Down', 'timeline-express-pro' ),
		'te-fadeInDownBig'     => esc_html__( 'Fade In - Down Big', 'timeline-express-pro' ),
		'te-fadeInLeft'        => esc_html__( 'Fade In - Left', 'timeline-express-pro' ),
		'te-fadeInLeftBig'     => esc_html__( 'Fade In - Left Big', 'timeline-express-pro' ),
		'te-fadeInRight'       => esc_html__( 'Fade In - Right', 'timeline-express-pro' ),
		'te-fadeInRightBig'    => esc_html__( 'Fade In - Right Big', 'timeline-express-pro' ),
		'te-fadeInUp'          => esc_html__( 'Fade In - Up', 'timeline-express-pro' ),
		'te-fadeInUpBig'       => esc_html__( 'Fade In - Up Big', 'timeline-express-pro' ),
		'te-flipInX'           => esc_html__( 'Flip In - X Axis', 'timeline-express-pro' ),
		'te-flipInY'           => esc_html__( 'Flip In - Y Axis', 'timeline-express-pro' ),
		'te-lightSpeedIn'      => esc_html__( 'Lightspeed', 'timeline-express-pro' ),
		'te-rotateIn'          => esc_html__( 'Rotate In', 'timeline-express-pro' ),
		'te-rotateInDownLeft'  => esc_html__( 'Rotate In - Down Left', 'timeline-express-pro' ),
		'te-rotateInDownRight' => esc_html__( 'Rotate In - Down Right', 'timeline-express-pro' ),
		'te-rotateInUpLeft'    => esc_html__( 'Rotate In - Up Left', 'timeline-express-pro' ),
		'te-rotateInUpRight'   => esc_html__( 'Rotate In - Up Right', 'timeline-express-pro' ),
		'te-slideInLeft'       => esc_html__( 'Slide In - Left', 'timeline-express-pro' ),
		'te-slideInRight'      => esc_html__( 'Slide In - Right', 'timeline-express-pro' ),
		'te-zoomIn'            => esc_html__( 'Zoom In', 'timeline-express-pro' ),
		'te-zoomInDown'        => esc_html__( 'Zoom In - Down', 'timeline-express-pro' ),
		'te-zoomInLeft'        => esc_html__( 'Zoom In - Left', 'timeline-express-pro' ),
		'te-zoomInRight'       => esc_html__( 'Zoom In - Right', 'timeline-express-pro' ),
		'te-zoomInUp'          => esc_html__( 'Zoom In - Up', 'timeline-express-pro' ),
		'te-rollIn'            => esc_html__( 'Roll In', 'timeline-express-pro' ),
		'te-jackInTheBox'      => esc_html__( 'Jack in The Box', 'timeline-express-pro' ),
	];

	/**
	 * Filter the available animations, by type (container/icon)
	 *
	 * This allows us to filter the available animations for our container or
	 * our icon select fields. This also allows users to append additional
	 * animations on, if they have custom ones they'd like to use.
	 *
	 * @since 1.0.0
	 */
	return (array) apply_filters( 'timeline_express_styles_animations', $animations, $type );

}
