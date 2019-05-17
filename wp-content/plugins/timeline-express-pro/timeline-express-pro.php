<?php
/**
#_________________________________________________ PLUGIN
Plugin Name: Timeline Express Pro
Plugin URI: https://www.wp-timelineexpress.com
Description: Create a beautiful vertical, CSS3 animated and responsive timeline in minutes flat without writing code.
Version: 2.2.7
Author: Code Parrots
Text Domain: timeline-express-pro
Author URI: http://www.codeparrots.com
License: GPL2
#_________________________________________________ LICENSE
Copyright 2012-2018 Code Parrots (email : codeparrots@gmail.com)

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
 *
 * @link http://www.codeparrots.com
 *
 * @package Timeline Express
 * @since 1.0.0
 **/

// must include plugin.php to use is_plugin_active()
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Ensure that 'Timeline Express' (free) is not active, else abort & display a notice
if ( is_plugin_active( 'timeline-express/timeline-express.php' ) ) {

	deactivate_plugins( plugin_basename( __FILE__ ) );

	add_action( 'admin_notices', 'timeline_express_pro_activation_notice_error' );

	return;

}

/**
*   Admin notice when the base plugin is not installed.
*/
function timeline_express_pro_activation_notice_error() {
	?>
		<!-- hide the 'Plugin Activated' default message -->
		<style>
		#message.updated {
			display: none;
		}
		.codeparrots-bg.cp-flying-parrot {
			background: url( "<?php echo esc_url( plugin_dir_URL( __FILE__ ) ) . '/lib/admin/images/sad-parrot.png'; ?>") #FFFFFF no-repeat;
			background-position: middle left;
			background-size: 65px;
			background-position-x: 8px;
		}
		.error.codeparrots-bg.cp-flying-parrot p {
			padding-left: 68px;
		}
		</style>
		<!-- display our error message -->
		<div class="error codeparrots-bg cp-flying-parrot">
			<p><?php printf( /* translators: HTML markup wrapped around the text "Error" */ esc_html( '%s Timeline Express Pro could not be activated.', 'timeline-express-pro' ), '<strong>' . esc_html__( 'Error', 'timeline-express-pro' ) . ':</strong>' ); ?></p>
			<p><?php esc_html_e( 'Please de-activate the free version before activating the pro version. Once the pro version is activated, you can uninstall the free version safely.', 'timeline-express-pro' ); ?></p>
			<p><?php printf( /* translators: HTML anchor linking to Timeline Express documentation. */  esc_html( 'If you need help installing the pro version, please see our %s, and scroll down to the "Pro Version" section.', 'timeline-express-pro' ), '<a href="https://www.wp-timelineexpress.com/documentation/installation/" target="_blank" title="' . esc_attr__( 'Installation Documentation', 'timeline-express-pro' ) . '">' . esc_html__( 'Installation Documentation', 'timeline-express-pro' ) . '</a>' ); ?></p>
		</div>
	<?php
}

/* Include project constants */
include_once plugin_dir_path( __FILE__ ) . 'constants.php';

/**
 * Include our new modules
 *
 * @filter timeline_express_modules_enabled
 * Note: Return the filter above as false to disable the modules.
 *
 * @since 2.0.0
 */
if ( (bool) apply_filters( 'timeline_express_modules_enabled', true ) ) {

	$modules_array = [
		'styles',
		'custom-icons',
		'side-nav',
		'banner-popups',
		'extra-content',
	];

	$modules = (array) apply_filters( 'timeline_express_modules', $modules_array );

	foreach ( $modules as $module ) {

		$path = sprintf(
			plugin_dir_path( __FILE__ ) . 'lib/modules/%1$s/%1$s.php',
			$module
		);

		if ( ! file_exists( $path ) ) {

			continue;

		}

		include_once $path;

	} // @codingStandardsIgnoreLine

}

/**
 * Localization
 * Include our textdomain and translation files
 **/
function timeline_express_pro_text_domain_init() {

	// @codingStandardsIgnoreStart
	/**
	 * Load the text domain from the theme root (Note: The theme/timeline-express/i18n/ directory)
	 *
	 * @since 2.0.0
	 *
	 * @return Custom mofile path if timeline-express/i18n/ is found in the theme root, else default mofile path.
	 */
	add_filter(
		'load_textdomain_mofile', function( $mofile, $domain ) {

			$local_i18n_dir = trailingslashit( get_stylesheet_directory() ) . 'timeline-express/i18n/';
			$mo_path_split  = explode( '/', $mofile );

			if (
			'timeline-express-pro' !== $domain
			|| ! is_dir( $local_i18n_dir )
			|| ! is_file( $local_i18n_dir . end( $mo_path_split ) )
			) {

				return $mofile;

			}

			return $local_i18n_dir . end( $mo_path_split );

		}, 10, 2
	);
	// @codingStandardsIgnoreEnd

	load_plugin_textdomain( 'timeline-express-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n' );

}
add_action( 'init', 'timeline_express_pro_text_domain_init', 0 );

/* Include Class Files */

/* Main timeline express class file */
require_once plugin_dir_path( __FILE__ ) . 'lib/classes/class-timeline-express-base.php';

/* Initialize the plugin's base class */
$timeline_express_base = new TimelineExpressBase();

/* Activation Hook */
register_activation_hook(
	__FILE__,
	array(
		$timeline_express_base,
		'timeline_express_activate',
	)
);
/* Deactivation Hook */
register_deactivation_hook(
	__FILE__,
	array(
		$timeline_express_base,
		'timeline_express_deactivate',
	)
);
