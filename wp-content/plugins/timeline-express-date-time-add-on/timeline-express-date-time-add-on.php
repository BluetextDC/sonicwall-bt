<?php
/**
#_________________________________________________ PLUGIN
Plugin Name: Timeline Express - Date - Time Add-On
Plugin URI: https://www.wp-timelineexpress.com
Description: Assign and display times to your announcements with a time selector field alongside the announcement dates.
Version: 1.0.1
Author: Code Parrots
Text Domain: timeline-express-date-time-add-on
Author URI: https://www.codeparrots.com
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
*/

if ( ! defined( 'WPINC' ) ) {

	die;

}

/**
 * Localization
 * Include our textdomain and translation files
 **/
function tedt_text_domain_init() {

	/**
	 * Load the text domain from the theme root (Note: The theme/timeline-express/i18n/ directory)
	 *
	 * @since 2.0.0
	 *
	 * @return Custom mofile path if timeline-express/i18n/ is found in the theme root, else default mofile path.
	 */
	add_filter( 'load_textdomain_mofile', function( $mofile, $domain ) {

		$local_i18n_dir = trailingslashit( get_stylesheet_directory() ) . 'timeline-express/i18n/';
		$mo_path_split  = explode( '/', $mofile );

		if (
			'timeline-express-date-time-add-on' !== $domain
			|| ! is_dir( $local_i18n_dir )
			|| ! is_file( $local_i18n_dir . end( $mo_path_split ) )
		) {

			return $mofile;

		}

		return $local_i18n_dir . end( $mo_path_split );

	}, 10, 2 );

	load_plugin_textdomain( 'timeline-express-date-time-add-on', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n' );

}
add_action( 'init', 'tedt_text_domain_init' );

// Include required files.
include_once plugin_dir_path( __FILE__ ) . '/constants.php';
include_once TIMELINE_EXPRESS_DATE_TIME_PATH . 'lib/migration.php';

/**
 * Ensure that Timeline Express free or pro is active, else deactivate this add-on and display a notice
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'is_plugin_active' ) ) {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

}

if ( ! is_plugin_active( 'timeline-express/timeline-express.php' ) && ! is_plugin_active( 'timeline-express-pro/timeline-express-pro.php' ) ) {

	deactivate_plugins( plugin_basename( __FILE__ ) );

	add_action( 'admin_notices', function() {
		?>
		<style>
		#message.updated,
		.notice.tedt-migration {
			display: none;
		}
		.codeparrots-bg.cp-flying-parrot {
			background: url( "<?php echo esc_url( TIMELINE_EXPRESS_DATE_TIME_URL . 'lib/img/code-parrots-sad-parrot.png' ); ?>") #FFFFFF no-repeat;
			background-size: 48px;
			background-position-x: 15px;
		}
		.error.codeparrots-bg.cp-flying-parrot p {
			padding-left: 65px;
		}
	</style>
		<div class="error codeparrots-bg cp-flying-parrot">
			<p>
				<?php
				printf(
					/* translators: HTML markup wrapped around the text "Error" */
					esc_html( '%s Timeline Express - Date - Time Add-On could not be activated.', 'timeline-express-date-time-add-on' ),
					'<strong>' . esc_html__( 'Error', 'timeline-express-date-time-add-on' ) . ':</strong>'
				);
				?>
			</p>
			<p><?php esc_html_e( 'This is an add-on to Timeline Express. Please install and activate Timeline Express Free or Pro before activating this add-on.', 'timeline-express-date-time-add-on' ); ?></p>
		</div>
		<?php
	} );

	return;

}

/**
 * Activation hook
 *
 * Note: Check for any announcements that do not have announcement-date-time
 *       and migrate them, since the announcement date fields are hidden.
 *
 * @since 1.0.0
 */
function tede_announcement_date_time_activation() {

	include_once TIMELINE_EXPRESS_DATE_TIME_PATH . 'lib/activation.php';

}
register_activation_hook( __FILE__, 'tede_announcement_date_time_activation' );

/**
* Add date/time picker field to Timeline Express announcements
*
* @param array $custom_field Array of custom fields to append to our announcements.
*/
function tedt_add_custom_fields_to_announcements( $custom_fields ) {

	$announcement_singular_text = apply_filters( 'timeline_express_singular_name', esc_html__( 'Announcement', 'timeline-express-date-time-add-on' ) );

	$custom_fields[] = array(
		'name' => sprintf(
			/* translators: 1. The singular post type name, capitalized. */
			esc_html__( '%s Date & Time', 'timeline-express-date-time-add-on' ),
			esc_html( $announcement_singular_text )
		),
		'desc' => sprintf(
			/* translators: 1. The singular post type name, lowercase. */
			esc_html__( 'Select the time that this %s occurred, or will occur, on.', 'timeline-express-date-time-add-on' ),
			esc_html( strtolower( $announcement_singular_text ) )
		),
		'id'   => 'announcement-date-time',
		'type' => 'text_datetime_timestamp',
	);

	$date_format = get_option( 'date_format' );
	$time_format = get_option( 'time_format' );
	$full_date   = $date_format . ' ' . $time_format;

	// wp_die( print_r( $custom_fields ) );
	$custom_fields[] = array(
		'name'    => sprintf(
			/* translators: 1. The singular post type name, capitalized. */
			esc_html__( '%s Date Type', 'timeline-express-date-time-add-on' ),
			esc_html( $announcement_singular_text )
		),
		'desc'    => sprintf(
			/* translators: 1. The singular post type name, lowercase. */
			esc_html__( 'Customize the date format for this %s.', 'timeline-express-date-time-add-on' ),
			esc_html( strtolower( $announcement_singular_text ) )
		),
		'id'      => 'announcement-date-format',
		'type'    => 'radio',
		'default' => $full_date,
		'options' => (array) apply_filters( 'timeline_express_date_time_formats', [
			$full_date   => __( 'Full Date (Date & Time)', 'timeline-express-date-time-add-on' ),
			$date_format => __( 'Date Only', 'timeline-express-date-time-add-on' ),
			'Y'          => __( 'Year Only', 'timeline-express-date-time-add-on' ),
			$time_format => __( 'Time Only', 'timeline-express-date-time-add-on' ),
		] ),
	);

	return $custom_fields;

}
add_filter( 'timeline_express_custom_fields', 'tedt_add_custom_fields_to_announcements' );

/**
 * Hide the original "Announcement Date" field/row.
 *
 * @return mixed Style tag to hide the original date picker
 */
function tedt_hide_default_announcement_date_picker() {

	$screen = get_current_screen();

	if ( ! isset( $screen->base ) || 'te_announcements' !== $screen->id ) {

		return;

	}

	?>

	<style>.cmb2-id-announcement-date { display: none; }</style>

	<?php

}
add_action( 'admin_head', 'tedt_hide_default_announcement_date_picker' );

/**
 * Append new announcement time and date on the frontend
 *
 * @return string New string to append to our announcement date.
 */
function tedt_display_announcement_date( $date ) {

	global $post;

	$announcement_date_time = get_post_meta( $post->ID, 'announcement-date-time', true );
	$date_format            = get_post_meta( $post->ID, 'announcement-date-format', true );

	$date = $announcement_date_time ? $announcement_date_time : get_post_meta( $post->ID, 'announcement_date', true );

	// No date format set fall back to just the date
	$format = $date_format ? $date_format : get_option( 'date_format' );

	return date_i18n( $format, $date );

}
add_filter( 'timeline_express_admin_column_date_format', 'tedt_display_announcement_date' );
add_filter( 'timeline_express_frontend_date_filter', 'tedt_display_announcement_date' );

/**
 * Filter the Timeline Express query 'orderby' param & add a date fallback
 *
 * Timeline Express sorts by the announcement date by default.
 * Use this function to fallback to the published date when two
 * or more announcements are using the same announcement date.
 * This allows for manual control over the order of announcements.
 *
 * Source Code: https://github.com/EvanHerman/timeline-express/blob/master/lib/classes/class.timeline-express-initialize.php#L86
 */
function tedt_timeline_express_sort_by_date_time( $args, $post, $atts ) {

	$args['meta_key'] = 'announcement-date-time';
	$args['orderby']  = 'meta_value_num date';

	return (array) apply_filters( 'timeline_express_date_time_query_args', $args );

}
add_filter( 'timeline_express_announcement_query_args', 'tedt_timeline_express_sort_by_date_time', 10, 3 );
