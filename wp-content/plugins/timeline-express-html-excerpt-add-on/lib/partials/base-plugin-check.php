<?php
/**
 * Functions to check if the Timeline Express base plugin is active
 * If it's not, abort, self-deactivate this add-on and display a warning.
 *
 * @since 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {

	die;

}

// Include plugin.php
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Deactivaate this add-on
deactivate_plugins( TIMELINE_EXPRESS_PLUGIN_BASENAME );

// Display the admin notice
add_action( 'admin_notices', 'timeline_express_html_excerpts_addon_display_activation_notice_error' );

/**
*	Admin notice when the base plugin is not installed.
*
* @hooked   admin_notices
*
* @since    1.0.0
*/
function timeline_express_html_excerpts_addon_display_activation_notice_error() {
	?>

		<!-- Hide the 'Plugin Activated' default message -->
		<style>
		#message.updated {

			display: none;

		}
		</style>

		<!-- Display the error -->
		<div class="error">

			<p><?php printf( esc_attr__( '%s could not be activated because Timeline Express is not installed and active.', 'timeline-express-html-excerpts-add-on' ), '<strong>Timeline Express HTML Excerpts Add-on</strong>' ); ?></p>

			<p><?php printf( esc_attr__( 'Please install and activate %s before activating this addon.', 'timeline-express-html-excerpts-add-on' ) , '<a href="' . esc_url_raw( admin_url( 'plugin-install.php?tab=search&type=term&s=Timeline+Express+Evan+Herman' ) ) . '" title="Timeline Express">Timeline Express</a>' ); ?></p>

		</div>

	<?php
}
