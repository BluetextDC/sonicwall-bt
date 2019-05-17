<?php
/**
#_________________________________________________ PLUGIN
Module Name: Timeline Express - Banner Popups
Module URI: https://www.wp-timelineexpress.com
Description: Preview full size image and video announcement banners in a popup.
Version: 1.0.0
Author: Code Parrots
Author URI: http://www.codeparrots.com
License: GPL2

#_________________________________________________ LICENSE
Copyright 2012-16 Code Parrots (email : codeparrots@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

#_________________________________________________ CONSTANTS
*/

if ( ! defined( 'WPINC' ) ) {

	die;

}

include_once plugin_dir_path( __FILE__ ) . '/constants.php';

function initialize_timeline_express_banner_popups() {

	class Timeline_Express_Banner_Popups extends TimelineExpressBase {

		/**
		 * Default sidenav shortcode attributes array.
		 *
		 * @var array
		 */
		private $shortcode_defaults;

		public function __construct() {

			$defaults = [
				'banner-popups' => false,
			];

			$this->shortcode_defaults = (array) apply_filters( 'timeline_express_banner_popups_default_atts', $defaults );

			add_filter( 'timeline_express_custom_fields', [ $this, 'enable_banner_popup_field' ] );

			add_filter( 'shortcode_atts_timeline-express', [ $this, 'banner_popups_shortcode_param' ], 15, 4 );

			add_filter( 'timeline-express-announcement-container-class', [ $this, 'banner_popups_container_class' ], 10, 2 );

			add_action( 'timeline_express_announcement_banner', [ $this, 'filter_announcement_banner' ] );

		}

		/**
		 * Enable the banner popup field.
		 *
		 * @param  array $fields The default Timeline Express metabox fields.
		 *
		 * @since 2.0.0
		 *
		 * @return array         Filtered array of metabox fields.
		 */
		public function enable_banner_popup_field( $fields ) {

			$timeline_express_singular_name = apply_filters( 'timeline_express_singular_name', esc_html__( 'Announcement', 'timeline-express-pro' ) );

			$fields[] = [
				'name' => sprintf(
					/* translators: Timeline Express singular post type name (eg: Announcement) */
					esc_html__( '%s Banner Popup', 'timeline-express-pro' ),
					$timeline_express_singular_name
				),
				'desc' => sprintf(
					/* translators: Timeline Express singular post type name - lowercase (eg: announcement) */
					esc_html__( 'Preview the %s banner in a popup window.', 'timeline-express-pro' ),
					strtolower( $timeline_express_singular_name )
				),
				'id'   => 'announcement_banner_popup',
				'type' => 'checkbox',
			];

			return $fields;

		}

		/**
		 * Enable the banner popup shortcode parameters.
		 *
		 * @param mixed  $output
		 * @param array  $pairs
		 * @param array  $atts
		 * @param string $shortcode
		 *
		 * @since 1.0.0
		 */
		public function banner_popups_shortcode_param( $output, $pairs, $atts, $shortcode ) {

			$atts = wp_parse_args( $atts, $this->shortcode_defaults );

			if ( ! $atts['banner-popups'] ) {

				return $output;

			}

			return $output;

		}

		/**
		 * Add the banner popups container class.
		 *
		 * @param  string  $classes         The initial container classes.
		 * @param  integer $announcement_id The announcement id.
		 *
		 * @since 2.0.0
		 *
		 * @return string                   The final announcement class string.
		 */
		public function banner_popups_container_class( $classes, $announcement_id ) {

			$banner_popup = get_post_meta( $announcement_id, 'announcement_banner_popup', true );

			if ( ! $banner_popup ) {

				return $classes;

			}

			$suffix = SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( 'fancybox', TIMELINE_EXPRESS_BANNER_POPUPS_URL . "/lib/css/fancybox{$suffix}.css", [], '3.1.20', 'all' );
			wp_enqueue_style( 'timeline-express-banner-popups', TIMELINE_EXPRESS_BANNER_POPUPS_URL . '/lib/css/timeline-express-banner-popups.css', [ 'timeline-express-base' ], TIMELINE_EXPRESS_BANNER_POPUPS_VERSION, 'all' );

			wp_enqueue_script( 'fancybox', TIMELINE_EXPRESS_BANNER_POPUPS_URL . "/lib/js/fancybox{$suffix}.js", [ 'jquery' ], '3.1.20', true );

			wp_enqueue_script( 'timeline-express-banner-popups', TIMELINE_EXPRESS_BANNER_POPUPS_URL . "/lib/js/timeline-express-banner-popups{$suffix}.js", [ 'fancybox' ], TIMELINE_EXPRESS_BANNER_POPUPS_VERSION, true );

			$script_atts = [
				'icon'      => 'search',
				'animation' => 'fade', // possible 'zoom', 'fade', 'zoom-in-out'.
			];

			wp_localize_script( 'timeline-express-banner-popups', 'bannerPopup', (array) apply_filters( 'timeline_express_banner_popups_js', $script_atts ) );

			$classes .= $banner_popup ? ' banner-popup' : '';

			return $classes;

		}

		/**
		 * Wrap the announcement banner image in the appropriate anchor tag.
		 *
		 * @param  mixed $announcement_image Markup for the announcement image.
		 *
		 * @since 2.0.0
		 *
		 * @return mixed                     Markup.
		 */
		public function filter_announcement_banner( $announcement_image ) {

			$image        = get_post_meta( get_the_ID(), 'announcement_image', true );
			$image_id     = get_post_meta( get_the_ID(), 'announcement_image_id', true );
			$banner_popup = get_post_meta( get_the_ID(), 'announcement_banner_popup', true );
			$is_image     = wp_attachment_is_image( $image_id );

			$popup_src = $is_image ? wp_get_attachment_image_src( $image_id, 'full' ) : $image;

			if ( is_single() || ! $banner_popup || ! $image_id || ! $popup_src ) {

				return $announcement_image;

			}

			$overlay_container = sprintf(
				'<div class="overlay"><span class="fa fa-%s preview-icon"></span></div>',
				apply_filters( 'timeline_express_banner_popups_icon', 'search' )
			);

			return sprintf(
				'<a href="%1$s" class="banner-preview">%2$s%3$s</a>',
				esc_url( $is_image ? $popup_src[0] : $popup_src ),
				$announcement_image,
				$overlay_container
			);

		}

	}

	new Timeline_Express_Banner_Popups;

}
add_action( 'plugins_loaded', 'initialize_timeline_express_banner_popups' );
