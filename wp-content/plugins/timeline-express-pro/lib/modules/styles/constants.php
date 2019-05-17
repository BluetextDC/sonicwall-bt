<?php
/**
 * Constants for the Timeline Express Starter Add-on
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
if ( ! defined( 'TIMELINE_EXPRESS_STYLES_VERSION' ) ) {

	define( 'TIMELINE_EXPRESS_STYLES_VERSION', '1.0.0' );

}

/**
 * Define the path to the plugin
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_STYLES_PATH' ) ) {

	define( 'TIMELINE_EXPRESS_STYLES_PATH', plugin_dir_path( __FILE__ ) );

}

/**
 * Define the url to the plugin
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_STYLES_URL' ) ) {

	define( 'TIMELINE_EXPRESS_STYLES_URL', plugin_dir_url( __FILE__ ) );

}

/**
 * Define the plugin basename
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_STYLES_BASENAME' ) ) {

	define( 'TIMELINE_EXPRESS_STYLES_BASENAME', plugin_basename( 'timeline-express-styles-add-on/timeline-express-styles-add-on.php' ) );

}

/**
 * Define the option name
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_STYLES_OPTION' ) ) {

	define( 'TIMELINE_EXPRESS_STYLES_OPTION', 'timeline_express_styles_storage' );

}
