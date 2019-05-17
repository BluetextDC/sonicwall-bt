<?php
/**
 * Constants for the Timeline Express Date Time Add-on
 *
 * @since 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {

	die;

}

/**
 * Define the version number
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_DATE_TIME_VERSION' ) ) {

	define( 'TIMELINE_EXPRESS_DATE_TIME_VERSION', '1.0.1' );

}

/**
 * Define the path to the plugin
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_DATE_TIME_PATH' ) ) {

	define( 'TIMELINE_EXPRESS_DATE_TIME_PATH', plugin_dir_path( __FILE__ ) );

}

/**
 * Define the url to the plugin
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_DATE_TIME_URL' ) ) {

	define( 'TIMELINE_EXPRESS_DATE_TIME_URL', plugin_dir_url( __FILE__ ) );

}

/**
 * Define the plugin basename
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_DATE_TIME_BASENAME' ) ) {

	define( 'TIMELINE_EXPRESS_DATE_TIME_BASENAME', plugin_basename( 'timeline-express-date-time-add-on/timeline-express-date-time-add-on.php' ) );

}

/**
 * Define the option name
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_DATE_TIME_OPTION' ) ) {

	define( 'TIMELINE_EXPRESS_DATE_TIME_OPTION', 'timeline_express_date_time_storage' );

}
