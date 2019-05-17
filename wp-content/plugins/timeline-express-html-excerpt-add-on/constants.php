<?php
/**
 * Constants for the Timeline Express HTML Excerpts Add-on
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
if ( ! defined( 'TIMELINE_EXPRESS_HTML_EXCERPT_VERSION' ) ) {

	define( 'TIMELINE_EXPRESS_HTML_EXCERPT_VERSION', '1.1.0' );

}

/**
 * Define the path to the plugin
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_HTML_EXCERPT_PATH' ) ) {

	define( 'TIMELINE_EXPRESS_HTML_EXCERPT_PATH', plugin_dir_path( __FILE__ ) );

}

/**
 * Define the url to the plugin
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_HTML_EXCERPT_URL' ) ) {

	define( 'TIMELINE_EXPRESS_HTML_EXCERPT_URL', plugin_dir_url( __FILE__ ) );

}

/**
 * Define the plugin basename
 *
 * @since 1.0.0
 */
if ( ! defined( 'TIMELINE_EXPRESS_PLUGIN_BASENAME' ) ) {

	define( 'TIMELINE_EXPRESS_PLUGIN_BASENAME', plugin_basename( 'timeline-express-html-excerpts-add-on/timeline-express-html-excerpts-add-on.php' ) );

}
