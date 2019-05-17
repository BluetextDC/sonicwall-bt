<?php
/**
#_________________________________________________ PLUGIN
Module Name: Timeline Express - Styles Module
Module URI: https://www.wp-timelineexpress.com
Description: Customize many aspects of Timeline Express with the Styles Add-On.
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

function initialize_timeline_express_styles() {

	class Timeline_Express_Styles extends TimelineExpressBase {

		public static $announcement_singular;

		public function __construct() {

			self::$announcement_singular = (string) apply_filters( 'timeline_express_singular_name', 'announcement' );

			add_action( 'timeline-express-styles', [ $this, 'enqueue_styles' ] );
			add_action( 'timeline-express-scripts', [ $this, 'enqueue_scripts' ] );

			include_once( TIMELINE_EXPRESS_STYLES_PATH . 'lib/partials/styles-helpers.php' );
			include_once( TIMELINE_EXPRESS_STYLES_PATH . 'lib/partials/styles-filters.php' );

			add_action( 'init', [ $this, 'init' ] );

		}

		public function init() {

			if ( ! is_admin() ) {

				return;

			}

			include_once( TIMELINE_EXPRESS_STYLES_PATH . 'lib/partials/styles-metabox.php' );

			$this->load_cmb2_tabs();

			add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

		}

		/**
		 * Enqueue Timeline Express Styles Add-On admin scripts.
		 *
		 * @since 1.0.0
		 */
		public function admin_scripts( $hook ) {

			$screen = get_current_screen();

			if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) || ( ! $screen->post_type || 'te_announcements' !== $screen->post_type ) ) {

				return;

			}

			$suffix = SCRIPT_DEBUG ? '' : '.min';
			$rtl    = ! is_rtl() ? '' : '-rtl';

			wp_enqueue_script( 'timeline-express-styles-metabox', TIMELINE_EXPRESS_STYLES_URL . "lib/js/timeline-express-styles-metabox{$suffix}.js", [ 'jquery' ], TIMELINE_EXPRESS_STYLES_VERSION, true );
			wp_enqueue_style( 'timeline-express-styles-metabox', TIMELINE_EXPRESS_STYLES_URL . "lib/css/timeline-express-styles-metabox{$rtl}{$suffix}.css", [], TIMELINE_EXPRESS_STYLES_VERSION, 'all' );

		}

		/**
		 * Enqueue Timeline Express Styles Add-On Styles.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_styles() {

			$suffix = SCRIPT_DEBUG ? '' : '.min';
			$rtl    = ! is_rtl() ? '' : '-rtl';

			wp_enqueue_style( 'timeline-express-icon-styles', TIMELINE_EXPRESS_STYLES_URL . "lib/css/timeline-express-styles-add-on{$rtl}{$suffix}.css", [ 'timeline-express-base' ], TIMELINE_EXPRESS_STYLES_VERSION, 'all' );

			$scripts_atts = array(
				// Filter animation_disabled option
				'animation_disabled' => (boolean) apply_filters( 'timeline_express_animation_disabled', (bool) timeline_express_get_options( 'disable-animation' ) ),
			);

			wp_localize_script( 'timeline-express-js-base', 'timeline_base_data', $scripts_atts );

		}

		/**
		 * Enqueue Timeline Express Styles Add-On Scripts.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {

			$suffix = SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'timeline-express-icon-styles', TIMELINE_EXPRESS_STYLES_URL . "lib/js/timeline-express-styles-add-on{$suffix}.js", [ 'timeline-express-js-base' ], TIMELINE_EXPRESS_STYLES_VERSION, true );

		}

		/**
		 * Load the CMB2 tabs assets on the Timeline Express screens.
		 *
		 * @since 2.0.1
		 */
		public function load_cmb2_tabs() {

			$post_type = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING );

			if ( ! $post_type ) {

				$post_id   = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_STRING );
				$post      = $post_id ? get_post( $post_id ) : false;
				$post_type = $post ? ( isset( $post->post_type ) ? $post->post_type : false ) : false;

			}

			if ( ! $post_type || 'te_announcements' !== $post_type ) {

				return;

			}

			if ( file_exists( TIMELINE_EXPRESS_STYLES_PATH . 'lib/cmb2-tabs/plugin.php' ) ) {

				include_once( TIMELINE_EXPRESS_STYLES_PATH . 'lib/cmb2-tabs/plugin.php' );

			}

		}

	}

	new Timeline_Express_Styles;

}
add_action( 'plugins_loaded', 'initialize_timeline_express_styles' );

/**
 * Save the Timeline Express styles module meta.
 *
 * @param $override_value
 * @param $value
 * @param $post_id
 * @param $data
 */
function timeline_express_save_styles( $override_value, $value, $post_id, $data ) {

	$post_type = get_post_type( $post_id );

	if ( ! $post_type || 'te_announcements' !== $post_type ) {

		return;

	}

	foreach ( $data['tabs']['tabs'] as $tab ) {

		// @codingStandardsIgnoreStart
		$setting_fields = array_merge( $data['tabs']['config'], array( 'fields' => $tab['fields'] ) );
		$CMB2           = new \CMB2( $setting_fields, $post_id );

		if ( $CMB2->is_options_page_mb() ) {

			$cmb2_options = cmb2_options( $post_id );
			$values       = $CMB2->get_sanitized_values( $_POST );

			foreach ( $values as $key => $value ) {

				$cmb2_options->update( $key, $value );

			}

		} else {

			$CMB2->save_fields();

		}
		// @codingStandardsIgnoreEnd

	}

}
add_filter( 'cmb2_sanitize_tabs', 'timeline_express_save_styles', 10, 4 );
