<?php
/**
 * Activation & Deactivation Function
 * Functions to run on activation and deactivation
 *
 * @since 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {

	die;

}

/**
 * Timeline Express Add-on activate/deactivate
 *
 * @since 1.0.0
 */
function timeline_express_html_excerpts_addon_activate_deactivate() {

	/**
	 * Clear out any stored Timeline Express transients
	 */
	delete_timeline_express_transients();

}
register_activation_hook( __FILE__, 'timeline_express_html_excerpts_addon_activate_deactivate' );
register_deactivation_hook( __FILE__, 'timeline_express_html_excerpts_addon_activate_deactivate' );
