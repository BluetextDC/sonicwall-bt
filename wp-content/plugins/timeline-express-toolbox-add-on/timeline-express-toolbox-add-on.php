<?php
/**
#_________________________________________________ PLUGIN
Plugin Name: Timeline Express - Toolbox Add-On
Plugin URI: https://www.wp-timelineexpress.com
Description: Tweak some of the settings contained in Timeline Express.
Version: 1.1.0
Author: Code Parrots
Text Domain: timeline-express-toolbox-add-on
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

// Hook in and load our class
function initialize_timeline_express_toolbox_addon() {

	if ( ! class_exists( 'TimelineExpressBase' ) ) {

		include_once( TIMELINE_EXPRESS_TOOLBOX_PATH . 'lib/partials/base-plugin-check.php' );

		return;

	}

	class Timeline_Express_Toolbox extends TimelineExpressBase {

		private $addon_options;

		public function __construct() {

			$this->addon_options = $this->get_addon_options();

			include_once( TIMELINE_EXPRESS_TOOLBOX_PATH . 'lib/class-plugin-actions.php' );

			new Toolbox_Actions( $this->addon_options );

			add_action( 'admin_init', array( $this, 'register_settings' ) );

			add_action( 'timeline_express_add_on_options_page_header', array( $this, 'generate_options_page_header' ) );
			add_action( 'timeline_express_add_on_options_page',        array( $this, 'generate_options_section' ) );

			add_action( 'admin_init', array( $this, 'timeline_express_toolbox_activate_license' ) );
			add_action( 'admin_init', array( $this, 'timeline_express_toolbox_deactivate_license' ) );
			add_action( 'admin_init', array( $this, 'timeline_express_toolbox_addon_plugin_updater' ) );

			add_action( 'admin_menu', array( $this, 'timeline_express_toolbox_addon_menu' ) );

			// Custom per annoumcement date formats (Requires Timeline Express Pro)
			add_filter( 'timeline_express_custom_fields', array( $this, 'per_announcement_date_formats' ), PHP_INT_MAX );

			add_action( 'cmb2_render_date_formats',   array( $this, 'cmb2_render_date_formats' ), 10, 5 );
			add_filter( 'cmb2_sanitize_date_formats', array( $this, 'cmb2_sanitize_date_formats' ), 10, 2 );

			/**
			 * Load the plugin translations.
			 *
			 * @since 1.1.0
			 */
			add_action( 'init', function() {

				load_plugin_textdomain( 'timeline-express-toolbox-add-on', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n' );

			}, 0 );

		}

		/**
		 * Register our Timeline Express Toolbox Options
		 *
		 * @since 1.0.0
		 */
		public function register_settings() {

			register_setting(
				'timeline-express-toolbox-settings',
				'timeline_express_toolbox_storage',
				array( $this, 'save_timeline_express_toolbox_options' )
			);

			register_setting(
				'timeline_express_toolbox_license',
				'timeline_express_toolbox_license_key',
				'timeline_express_toolbox_sanitize_license'
			);

		}

		/**
		 * Generate the options page header
		 *
		 * @param  string $tab The current tab to render.
		 *
		 * @return mixed
		 *
		 * @since 1.0.0
		 */
		public function generate_options_page_header( $tab ) {

			if ( 'toolbox' !== $tab ) {

				return;

			}

			printf(
				'<h1>%1$s</h1><p class="description">%2$s</p>',
				esc_html__( 'Timeline Express - Toolbox Add-On', 'timeline-express-toolbox-add-on' ),
				esc_html__( 'Tweak advanced settings for Timeline Express below.', 'timeline-express-toolbox-add-on' )
			);

		}

		/**
		 * Generate our options section for the No Icons Add-on
		 *
		 * @return mixed
		 *
		 * @since 1.0.0
		 */
		public function generate_options_section( $tab ) {

			if ( 'toolbox' !== $tab ) {

				return;

			}

			$suffix = SCRIPT_DEBUG ? '' : '.min';

			ob_start();

			wp_enqueue_style( 'timeline-express-toolbox-options', TIMELINE_EXPRESS_TOOLBOX_URL . "lib/css/timeline-express-toolbox-admin{$suffix}.css" );
			wp_enqueue_script( 'timeline-express-toolbox-options', TIMELINE_EXPRESS_TOOLBOX_URL . "lib/js/timeline-express-toolbox-admin{$suffix}.js" );

			include_once( TIMELINE_EXPRESS_TOOLBOX_PATH . 'lib/partials/options-section.php' );

			return ob_get_contents();

		}

		/**
		 * Timeline Express No Icons Options Save
		 *
		 * @return array
		 *
		 * @since 1.0.0
		 */
		public function save_timeline_express_toolbox_options() {

			if ( ! isset( $_POST['timeline_express_toolbox_settings_nonce'] ) || ! wp_verify_nonce( $_POST['timeline_express_toolbox_settings_nonce'], 'timeline_express_toolbox_save_settings' ) ) {

				wp_die( esc_attr__( 'Sorry, the nonce security check did not pass. Please go back to the settings page, refresh the page and try to save your settings again.', 'timeline-express-toolbox-add-on' ), __( 'Failed Nonce Security Check', 'timeline-express-toolbox-add-on' ), array(
					'response' => 500,
					'back_link' => true,
					'text_direction' => ( is_rtl() ) ? 'rtl' : 'ltr',
				) );

			}

			$posted_option = isset( $_POST['timeline_express_storage'] ) ? $_POST['timeline_express_storage'] : false;

			if ( ! $posted_option ) {

				return $this->addon_options;

			}

			$options = array(
				'announcement_slug'     => sanitize_title_with_dashes( $posted_option['announcement_slug'] ),
				'date_string'           => sanitize_text_field( $posted_option['date_string'] ),
				'date_format'           => sanitize_text_field( $posted_option['date_format_custom'] ),
				'timeline_image_size'   => sanitize_text_field( $posted_option['timeline_image_size'] ),
				'single_image_size'     => sanitize_text_field( $posted_option['single_image_size'] ),
				'year_icons'            => sanitize_text_field( $posted_option['year_icons'] ),
				'announcement_singular' => ucwords( sanitize_text_field( $posted_option['announcement_singular'] ) ),
				'announcement_plural'   => ucwords( sanitize_text_field( $posted_option['announcement_plural'] ) ),
				'edit_caps'             => sanitize_text_field( $posted_option['edit_caps'] ),
			);

			// Announcement slug has been updated
			if ( $options['announcement_slug'] !== $this->addon_options['announcement_slug'] ) {

				/**
				 * Force flush the rewrite rules so no 404's occur
				 *
				 * @link https://github.com/EvanHerman/timeline-express/blob/master/lib/admin/cpt/cpt.announcements.php#L99-L112
				 *
				 * @since 1.0.0
				 */
				update_option( 'post_type_rules_flushed_te-announcements', false );

			}

			return $options;

		}

		/**
		 * Helper function to get default options
		 *
		 * @return array
		 */
		public function get_addon_options() {

			return get_option( TIMELINE_EXPRESS_TOOLBOX_OPTION, array(
				'announcement_slug'     => false,
				'date_string'           => _x( 'Announcement Date: {date}', 'Note: Translate everything but {date}. {date} is the location where the date will appear in the string.', 'timeline-express-toolbox-add-on' ),
				'date_format'           => get_option( 'date_format' ),
				'timeline_image_size'   => 'timeline-express-thumbnail',
				'single_image_size'     => 'timeline-express',
				'year_icons'            => 0,
				'announcement_singular' => __( 'Announcement', 'timeline-express' ),
				'announcement_plural'   => __( 'Announcements', 'timeline-express' ),
				'edit_caps'             => 'manage_options',
			) );

		}

		/**
		 * Generate markup displaying the current permalink structure for announcement post types
		 *
		 * @return mixed
		 */
		public function current_announcement_slug_markup() {

			$query = new WP_Query( array(
				'post_type'      => 'te_announcements',
				'posts_per_page' => 1,
			) );

			if ( $query->have_posts() ) {

				$query->the_post();

				printf(
					'<p class="description"><small>%1$s: <code>%2$s</code></small></p>',
					esc_html__( 'Current', 'timeline-express-toolbox-add-on' ),
					get_the_permalink( get_the_ID() )
				);

			}

		}

		/**
		 * Generate the markup for the date format option
		 * Note: Borrowed from core
		 *
		 * @link https://github.com/WordPress/WordPress/blob/8df80ae3ee3f3b6aa44ad53cb63de61c042cb0e9/wp-admin/options-general.php#L232-L261
		 *
		 * @return mixed
		 *
		 * @since 1.0.0
		 */
		public function date_format_option_markup( $post_id = false ) {

			$date_format = $post_id ? get_post_meta( $post_id, 'announcement_date_format', true ) : $this->addon_options['date_format'];

			$date_formats = array_unique( apply_filters( 'date_formats', array( __( 'F j, Y' ), 'Y-m-d', 'm/d/Y', 'd/m/Y' ) ) );

			$custom = true;

			foreach ( $date_formats as $format ) {

				echo "\t<label><input type='radio' name='timeline_express_storage[date_format]' value='" . esc_attr( $format ) . "'";

				if ( $date_format === $format ) {

					echo " checked='checked'";

					$custom = false;

				}

				echo ' /> <span class="date-time-text format-i18n">' . date_i18n( $format ) . '</span><code>' . esc_html( $format ) . "</code></label><br />\n";

			}

			echo '<label><input type="radio" name="timeline_express_storage[date_format]" id="date_format_custom_radio" value="\c\u\s\t\o\m"';

			checked( $custom );

			echo '/> <span class="date-time-text date-time-custom-text">' . __( 'Custom:' ) . '<span class="screen-reader-text"> ' . __( 'enter a custom date format in the following field' ) . '</span></label>' .
				'<label for="timeline_express_storage[date_format_custom]" class="screen-reader-text">' . __( 'Custom date format:' ) . '</label>' .
				'<input type="text" name="timeline_express_storage[date_format_custom]" id="timeline_express_storage[date_format_custom]" value="' . esc_attr( $this->addon_options['date_format'] ) . '" class="small-text" /></span>' .
				'<span class="screen-reader-text">' . __( 'example:' ) . ' </span> <span class="example">' . date_i18n( $this->addon_options['date_format'] ) . '</span>' .
				"<span class='spinner'></span>\n";

		}

		/**
		 * Generate the markup for the user capability selections
		 *
		 * @return mixed
		 *
		 * @since 1.0.0
		 */
		public function user_levels_option_markup() {

			$roles = array(
				__( 'Administrator', 'timeline-express-toolbox-add-on' ) => 'manage_options',
				__( 'Editor', 'timeline-express-toolbox-add-on' )        => 'edit_pages',
				__( 'Author', 'timeline-express-toolbox-add-on' )        => 'publish_posts',
				__( 'Subscriber', 'timeline-express-toolbox-add-on' )    => 'read',
			);

			if ( is_multisite() ) {

				$roles[ __( 'Super Admin', 'timeline-express-toolbox-add-on' ) ] = 'create_sites';

			}

			foreach ( apply_filters( 'timeline_express_toolbox_roles', $roles ) as $role_slug => $cap ) {

				printf(
					'<label>
						<input type="radio" name="timeline_express_storage[edit_caps]" id="timeline_express_storage[edit_caps]" value="%1$s" %2$s />
						%3$s
					</label><br />',
					esc_attr( $cap ),
					checked( $cap, $this->addon_options['edit_caps'], false ),
					ucwords( $role_slug )
				);

			}

		}

		/**
		 * Generate the markup for the image size option on the options page
		 *
		 * @return mixed
		 */
		public function image_size_option_markup( $type ) {

			global $_wp_additional_image_sizes;

			if ( ! empty( $_wp_additional_image_sizes ) ) {

				$option_name = ( 'timeline' === $type ) ? 'timeline_image_size' : 'single_image_size';

				$additional_sizes = array(
					_x( 'full', 'Image size name.', 'timeline-express-toolbox-add-on' ) => array(
						'description' => ' ' . __( '(Original image size)', 'timeline-express-toolbox-add-on' ),
					),
					_x( 'large', 'Image size name.', 'timeline-express-toolbox-add-on' ) => array(
						'width'  => '1024',
						'height' => '1024',
					),
					_x( 'medium_large', 'Image size name.', 'timeline-express-toolbox-add-on' ) => array(
						'width'  => '768',
						'height' => '0',
					),
					_x( 'medium', 'Image size name.', 'timeline-express-toolbox-add-on' ) => array(
						'width'  => '300',
						'height' => '300',
					),
					_x( 'thumbnail', 'Image size name.', 'timeline-express-toolbox-add-on' ) => array(
						'width'  => '150',
						'height' => '150',
						'crop'   => true,
					),
				);

				$_wp_additional_image_sizes = $additional_sizes + $_wp_additional_image_sizes;

				?>

				<select name="timeline_express_storage[<?php echo esc_attr( $option_name ); ?>]" id="timeline_express_storage[<?php echo esc_attr( $option_name ); ?>]" class="widefat">

				<?php

				foreach ( $_wp_additional_image_sizes as $name => $data ) {

					$label_text   = ucwords( str_replace( '_', ' ', str_replace( '-', ' ', $name ) ) );
					$height_width = isset( $data['description'] ) ? $data['description'] : ( ( isset( $data['width'] ) && isset( $data['height'] ) ) ? ' (' . esc_attr( $data['width'] ) . 'x' . esc_attr( $data['height'] ) . ')' : '' );
					$crop_text    = ( isset( $data['crop'] ) && $data['crop'] ) ? ' ' . __( 'Cropped', 'timeline-express-toolbox-add-on' ) : '';

					printf(
						'<option value="%1$s" %2$s>%3$s%4$s%5$s</option>',
						$name,
						selected( $this->addon_options[ $option_name ], $name ),
						$label_text,
						$height_width,
						$crop_text
					);

				}

				?>

				</select>

				<?php

			} // End if().

		}

		/***
		*	Licensing Functions
		*	@since 1.0
		*/

		/*
		* Remote Support License Activation
		*
		* @since v1.1.4
		*/
		public function timeline_express_toolbox_activate_license() {

			// listen for our activate button to be clicked
			if ( isset( $_POST['timeline_express_toolbox_license_activate'] ) ) {

				// run a quick security check
				if ( ! check_admin_referer( 'timeline_express_toolbox_nonce', 'timeline_express_toolbox_nonce' ) ) {

					return;

				}

				$license = trim( filter_input( INPUT_POST, 'timeline_express_toolbox_license_key', FILTER_SANITIZE_STRING ) );

				if ( ! $license ) {

					return;

				}

				$api_params = array(
					'edd_action' => 'activate_license',
					'license'    => $license,
					'item_name'  => urlencode( 'Timeline Express - Toolbox Add-on' ),
					'url'        => home_url(),
				);

				$response = wp_remote_post( TIMELINE_EXPRESS_SITE_URL, array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				) );

				if ( is_wp_error( $response ) ) {

					return false;

				}

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				update_option( 'timeline_express_toolbox_license_status', $license_data->license );
				update_option( 'timeline_express_toolbox_license_data',   $license_data );

			} // End if().

		}

		/*
		* Remote Support License De-activation
		*
		* @since v1.1.4
		*/
		public function timeline_express_toolbox_deactivate_license() {

			if ( isset( $_POST['timeline_express_toolbox_license_deactivate'] ) ) {

				if ( ! check_admin_referer( 'timeline_express_toolbox_nonce', 'timeline_express_toolbox_nonce' ) ) {

					return;

				}

				$license = trim( get_option( 'timeline_express_toolbox_license_key' ) );

				$api_params = array(
					'edd_action' => 'deactivate_license',
					'license'    => $license,
					'item_name'  => urlencode( 'Timeline Express - Toolbox Add-on' ),
					'url'        => home_url(),
				);

				$response = wp_remote_post( TIMELINE_EXPRESS_SITE_URL, array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				) );

				if ( is_wp_error( $response ) ) {

					return false;

				}

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( 'deactivated' === $license_data->license ) {

					delete_option( 'timeline_express_toolbox_license_status' );

				}

			} // End if().

		}

		/**
		 * Cross check if a new version exists
		 *
		 * @since 1.0.0
		 */
		function timeline_express_toolbox_addon_plugin_updater() {

			if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {

				include( TIMELINE_EXPRESS_TOOLBOX_PATH . 'lib/EDD_SL_Plugin_Updater.php' );

			}

			$license_key = trim( get_option( 'timeline_express_toolbox_license_key' , '' ) );

			if ( empty( $license_key ) ) {

				return;

			}

			$timeline_express_toolbox_crosscheck = new EDD_SL_Plugin_Updater( TIMELINE_EXPRESS_SITE_URL, TIMELINE_EXPRESS_TOOLBOX_PATH . 'timeline-express-toolbox-add-on.php' , array(
					'version'   => TIMELINE_EXPRESS_TOOLBOX_VERSION,
					'license'   => $license_key,
					'item_name' => 'Timeline Express - Toolbox Add-on',
					'author'    => 'Code Parrots',
				)
			);

		}

		/**
		 * Generate the admin support menus & sections
		 *
		 * @since 1.0.1
		 */
		public function timeline_express_toolbox_addon_menu() {

			include_once( TIMELINE_EXPRESS_TOOLBOX_PATH . 'lib/support-files/support.php' );

		}

		/**
		 * Per announcement date formats.
		 *
		 * @since 1.1.0
		 *
		 * @return array Metaboxes array.
		 */
		public function per_announcement_date_formats( $custom_metaboxes ) {

			/**
			 * Per Date Announcements
			 *
			 * Requires that the user defines a custom contant in functions.php
			 * Example: defined( 'TIMELINE_EXPRESS_ANNOUNCEMENT_DATE_FORMATS', true );
			 *
			 * @since 1.1.0
			 */
			if ( ! defined( 'TIMELINE_EXPRESS_ANNOUNCEMENT_DATE_FORMATS' ) || ! TIMELINE_EXPRESS_ANNOUNCEMENT_DATE_FORMATS ) {

				return $custom_metaboxes;

			}

			$custom_metaboxes[] = array(
				'name' => esc_html__( 'Announcement Date Format', 'timeline-express-pro' ),
				'desc' => esc_html__( 'Select the custom date format for this announcement.', 'timeline-express-pro' ),
				'id'   => 'announcement_date_format',
				'type' => 'date_formats',
			);

			return $custom_metaboxes;

		}

		/**
		 * Display the date format options.
		 *
		 * @param  string $override_value Value to override the database with.
		 * @param  string $value          Original value of the meta data.
		 *
		 * @since 1.1.0
		 *
		 * @return string                 Value to store in the database.
		 */
		public function cmb2_render_date_formats( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

			$suffix = SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( 'timeline-express-toolbox-options', TIMELINE_EXPRESS_TOOLBOX_URL . "lib/css/timeline-express-toolbox-admin{$suffix}.css" );
			wp_enqueue_script( 'timeline-express-toolbox-options', TIMELINE_EXPRESS_TOOLBOX_URL . "lib/js/timeline-express-toolbox-admin{$suffix}.js" );

			$this->date_format_option_markup( get_the_ID() );

			printf(
				'<p class="cmb2-metabox-description">%s</p>',
				esc_html__( 'Set the date format of the announcements. For additional formats please see PHP date documentation.', 'timeline-express-toolbox-add-on' )
			);

		}

		/**
		 * Save the date format to the database.
		 *
		 * @param  string $override_value Value to override the database with.
		 * @param  string $value          Original value of the meta data.
		 *
		 * @since 1.1.0
		 *
		 * @return string                 Value to store in the database.
		 */
		public function cmb2_sanitize_date_formats( $override_value, $value ) {

			$date_format = filter_input( INPUT_POST, 'timeline_express_storage', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

			if ( ! $date_format ) {

				return;

			}

			return isset( $date_format['date_format_custom'] ) ? $date_format['date_format_custom'] : get_option( 'date_format' );

		}

	}

	new Timeline_Express_Toolbox;

}
add_action( 'plugins_loaded', 'initialize_timeline_express_toolbox_addon' );

/**
 * Timeline Express Add-on activate
 *
 * @since 1.0.0
 */
function timeline_express_toolbox_addon_activate() {

	$add_ons = get_option( 'timeline_express_installed_add_ons', array() );

	if ( ! isset( $add_ons['toolbox'] ) ) {

		$add_ons['toolbox'] = __( 'Toolbox Add-On' , 'timeline-express-toolbox-add-on' );

		update_option( 'timeline_express_installed_add_ons', $add_ons );

	}

	if ( function_exists( 'delete_timeline_express_transients' ) ) {

		delete_timeline_express_transients();

	}

}
register_activation_hook( __FILE__, 'timeline_express_toolbox_addon_activate' );

/**
 * Timeline Express Add-on deactivate
 *
 * @since 1.0.0
 */
function timeline_express_toolbox_addon_deactivate() {

	$add_ons = get_option( 'timeline_express_installed_add_ons', array() );

	if ( isset( $add_ons['toolbox'] ) ) {

		unset( $add_ons['toolbox'] );

		update_option( 'timeline_express_installed_add_ons', $add_ons );

	}

}
register_deactivation_hook( __FILE__, 'timeline_express_toolbox_addon_deactivate' );
