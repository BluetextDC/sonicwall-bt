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
if ( ! defined( 'TIMELINE_EXPRESS_SIDENAV_VERSION' ) ) {

	define( 'TIMELINE_EXPRESS_SIDENAV_VERSION', '1.0.0' );

}

/**
 * Define the path to the plugin
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_SIDENAV_PATH' ) ) {

	define( 'TIMELINE_EXPRESS_SIDENAV_PATH', plugin_dir_path( __FILE__ ) );

}

/**
 * Define the url to the plugin
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_SIDENAV_URL' ) ) {

	define( 'TIMELINE_EXPRESS_SIDENAV_URL', plugin_dir_url( __FILE__ ) );

}
