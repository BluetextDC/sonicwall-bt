<?php
/**
#_________________________________________________ PLUGIN
Module Name: Timeline Express - Custom Icons
Module URI: https://www.wp-timelineexpress.com
Description: Allow custom icons to be used on the timeline.
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

function initialize_timeline_express_icons() {

	class Timeline_Express_Custom_Icons extends TimelineExpressBase {

		private $custom_pack;

		private $custom_pack_base;

		public function __construct() {

			$upload_dir = wp_upload_dir();

			$this->custom_pack = [
				'dir' => $upload_dir['basedir'] . '/wp-svg-icons/custom-pack/style.css',
				'url' => $upload_dir['baseurl'] . '/wp-svg-icons/custom-pack/style.css',
			];

			add_filter( 'timeline_express_announcement_icons', [ $this, 'custom_font_icons' ], PHP_INT_MAX );

			add_filter( 'timeline_express_icon_dropdown_font_base', [ $this, 'font_icon_base' ], PHP_INT_MAX, 2 );
			add_filter( 'timeline_express_icon_dropdown_font_name', [ $this, 'font_icon_name' ], PHP_INT_MAX );

			add_filter( 'timeline_express_bootstrap_dropdown_icon_value', [ $this, 'filter_dropdown_save' ], PHP_INT_MAX );

		}

		/**
		 * Pass in custom icons to the icon array.
		 *
		 * @param  array $icons Array of icons.
		 *
		 * @since 2.0.0
		 *
		 * @return array        Filtered icon array.
		 */
		public function custom_font_icons( $icons ) {

			if ( ! file_exists( $this->custom_pack['dir'] ) ) {

				return $icons;

			}

			wp_enqueue_style( 'timeline-express-custom-icons', $this->custom_pack['url'], [], '1.0.0', 'all' );

			$response = wp_remote_get( $this->custom_pack['url'] );

			if ( is_wp_error( $response ) ) {

				return $icons;

			}

			$stylesheet = wp_remote_retrieve_body( $response );

			set_site_transient( 'timeline_express_dropdown_custom_icon_body', $stylesheet, 60 * 60 * 24 );

			$custom_icon_array = $this->parse_custom_icons( $stylesheet );

			if ( empty( $custom_icon_array ) ) {

				return $icons;

			}

			$icon_array = $this->append_custom_icons( $icons, $custom_icon_array );

			set_site_transient( 'timeline_express_dropdown_icons_array', $icon_array, 60 * 60 * 24 );

			return $icon_array;

		}

		/**
		 * Parse the custom icons css file into an array of icons.
		 *
		 * This new custom icon array is merged into the dropdown.
		 *
		 * @param  string $icons The custom icon .css string
		 *
		 * @since 2.0.0
		 *
		 * @return array         The array => key value of custom icons.
		 */
		public function parse_custom_icons( $icons ) {

			$this->custom_pack_base = $this->get_custom_icon_css_base( $icons );

			if ( ! $this->custom_pack_base ) {

				return $icons;

			}

			$pattern = "/\.({$this->custom_pack_base}(?:\w+(?:-)?)+):before\s+{\s*content:\s*\"(.+)\";\s+}/";

			preg_match_all( $pattern, $icons, $matches, PREG_SET_ORDER );

			return $matches;

		}

		/**
		 * Loop through and append the custom icons to our default icon dropdown.
		 *
		 * @param  array $icons        The default icons array.
		 * @param  array $custom_icons The new custom icon array.
		 *
		 * @since 2.0.0
		 *
		 * @return array               Mixed array of default/custom icons
		 */
		public function append_custom_icons( $icons, $custom_icons ) {

			foreach ( $custom_icons as $match ) {

				if ( ! isset( $match[1] ) || ! isset( $match[2] ) ) {

					continue;

				}

				$custom_icon_base = str_replace( $this->custom_pack_base, '', $match[1] );

				/**
				 * If there is going to be a conflict with a previously named icon,
				 * skip it eg: home already exists in the default icons, so if someone
				 * updates custom-home, this would conflict and cause issues. So we skip it.
				 *
				 * Recommended fix: rename the icon to something like 'home-custom'
				 */
				if ( isset( $icons[ str_replace( $this->custom_pack_base, '', $match[1] ) ] ) ) {

					continue;

				}

				// $icons[ str_replace( $this->custom_pack_base, '', $match[1] ) ] = $match[2];
				$icons[ $match[1] ] = $match[2];

			}

			return $icons;

		}
		/**
		 * Extract the custom icons prefix (instead of fa-)
		 *
		 * @param  string $icons The custom icon stylesheet string.
		 *
		 * @since 2.0.0
		 *
		 * @return string        The custom icon prefix.
		 */
		public function get_custom_icon_css_base( $icons ) {

			$matches = array();

			preg_match( '/class\^="(.*?)"]/', $icons, $matches );

			return isset( $matches[1] ) ? $matches[1] : false;

		}

		/**
		 * Filter the font base name.
		 *
		 * @sicne 2.0.0
		 *
		 * @return string Return the custom base if it exists in the font name, else return default.
		 */
		public function font_icon_base( $font_base, $font_name ) {

			return ( false !== strpos( $font_name, $this->custom_pack_base ) ) ? str_replace( '-', '', $this->custom_pack_base ) : $font_base;

		}

		/**
		 * Filter the font name displayed to the user in the dropdown.
		 *
		 * @param string $font_name The original font name.
		 *
		 * @since 2.0.0
		 *
		 * @return string           The final font name to display.
		 */
		public function font_icon_name( $font_name ) {

			return str_replace( $this->custom_pack_base, '', $font_name );

		}

		/**
		 * Filter the saved icon name.
		 *
		 * @param string $value The original font value.
		 *
		 * @since 2.0.0
		 *
		 * @return string The new value to save.
		 */
		public function filter_dropdown_save( $value ) {

			$value = str_replace( 'fa-', '', $value );

			$icon_transient         = get_site_transient( 'timeline_express_dropdown_icons_array' );
			$this->custom_pack_base = $this->get_custom_icon_css_base( get_site_transient( 'timeline_express_dropdown_custom_icon_body' ) );

			return isset( $icon_transient[ $this->custom_pack_base . $value ] ) ? $this->custom_pack_base . $value : 'fa-' . $value;

		}

	}

	new Timeline_Express_Custom_Icons;

}
add_action( 'plugins_loaded', 'initialize_timeline_express_icons' );
