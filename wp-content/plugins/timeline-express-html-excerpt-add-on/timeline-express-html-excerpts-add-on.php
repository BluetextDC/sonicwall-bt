<?php
/**
#_________________________________________________ PLUGIN
Plugin Name: Timeline Express - HTML Excerpts Add-on
Plugin URI: https://www.wp-timelineexpress.com
Description: Enable new HTML excerpt fields on the Timeline Express announcements.
Version: 1.1.0
Author: Code Parrots
Text Domain: timeline-express-html-excerpts-add-on
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

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {

	die;

}

// Include our constants
include_once plugin_dir_path( __FILE__ ) . '/constants.php';

// Plugin activate/deactivate functions
include_once TIMELINE_EXPRESS_HTML_EXCERPT_PATH . 'lib/partials/activation-functions.php';

// Hook in and load our class
function initialize_timeline_express_html_excerpts_addon() {

	if ( ! class_exists( 'TimelineExpressBase' ) ) {

		// Include the base plugin checker
		include_once TIMELINE_EXPRESS_HTML_EXCERPT_PATH . 'lib/partials/base-plugin-check.php';

		return;

	}

	// Initialize our class
	class Timeline_Express_HTML_Excerpts extends TimelineExpressBase {

		// Main Constructor
		public function __construct() {

			// Generate the HTML excerpt metabox
			add_action( 'timeline_express_metaboxes', [ $this, 'define_custom_excerpt_metabox' ] );

			// Override the default announcement excerpts with HTML excerpt field
			add_filter( 'timeline_express_frontend_excerpt', [ $this, 'replace_default_timeline_express_excerpt' ], 10, 2 );

		}

		/**
		* Define our custom metabox and assign a WYSIWYG field
		* to use as our HTML excerpts.
		*
		* @param   array  $options  Array of options for Timeline Express.
		*
		* @return  null
		*
		* @since   1.0.0
		*/
		public function define_custom_excerpt_metabox( $options ) {

			// Define the new metabox
			$announcement_custom_metabox = new_cmb2_box( array(
				'id' => 'custom_meta',
				'title' => __( 'Announcement Custom Excerpt', 'timeline-express-html-excerpts-add-on' ),
				'object_types' => array( 'te_announcements' ), // Post type
				'context' => 'advanced',
				'priority' => 'high',
				'show_names' => true, // Show field names on the left
			) );

			// Add our WYSIWYG field to the metabox
			$announcement_custom_metabox->add_field( array(
				'name' => __( 'Custom Excerpt', 'timeline-express-html-excerpts-add-on' ),
				'desc' => __( 'Enter the custom excerpt for this announcement in the field above.', 'timeline-express-html-excerpts-add-on' ),
				'id' => 'announcement_custom_excerpt',
				'type' => 'wysiwyg',
			) );

		}

		/**
		 * Replace the default excerpt with our new custom excerpt.
		 *
		 * @param  string   $excerpt  The original announcement excerpt.
		 * @param  integer  $post_id  The announcement post ID
		 *
		 * @return string             Return the new excerpt to use.
		 *
		 * @since 1.0.0
		 */
		public function replace_default_timeline_express_excerpt( $excerpt, $post_id ) {

			// If the new custom excerpt is set, return it.
			if ( get_post_meta( $post_id, 'announcement_custom_excerpt', true ) ) {

				echo apply_filters( 'the_content', get_post_meta( $post_id, 'announcement_custom_excerpt', true ) );

				return;

			}

			return $excerpt;

		}
	}

	new Timeline_Express_HTML_Excerpts;

}
add_action( 'plugins_loaded', 'initialize_timeline_express_html_excerpts_addon' );
/* End */
