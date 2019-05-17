<?php

/**
*           (                        (              (     
*    (  (      )\ )     (               )\ )     (     )\ )  
*    )\))(   '(()/(     )\         )   (()/((    )\   (()/(  
*   ((_)()\ )  /(_))  (((_)  (    (     /(_))\((((_)(  /(_)) 
*   _(())\_)()(_))    )\___  )\   )\  '(_))((_))\ _ )\(_))   
*   \ \((_)/ /| _ \  ((/ __|((_)_((_)) | _ \ __(_)_\(_) _ \  
*    \ \/\/ / |  _/   | (__/ _ \ '  \()|  _/ _| / _ \ |   /  
*     \_/\_/  |_|      \___\___/_|_|_| |_| |___/_/ \_\|_|_\  
*
*
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://davenicosia.com
 * @since             1.0.0
 * @package           WP_Compear
 *
 * @wordpress-plugin
 * Plugin Name:       WP Compear
 * Plugin URI:        http://wpcompear.com/
 * Description:       Increase visitor stickiness with best selling product sliders, sortable tables, drag-and-drop comparison tools. Add custom product specs, affiliate links, and more.
 * Version:           1.1.1
 * Author:            Dave Nicosia
 * Author URI:        http://davenicosia.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-compear
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define( 'WPCOMPEAR_URL', 'http://wpcompear.com' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
define( 'WPCOMPEAR_ITEM_NAME', 'WP ComPEAR Unlimited Site License' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/includes/EDD_SL_Plugin_Updater.php' );
}

function edd_sl_wpcompear_plugin_updater() {
	// retrieve our license key from the DB
	$wpcompear_options = get_option( 'WP_Compear_options' );
	$license_key = trim( $wpcompear_options['wpcompear_license_key'] );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( WPCOMPEAR_URL, __FILE__, array(
			'version' 	=> '1.1.1', 				// current version number
			'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
			'item_name' => WPCOMPEAR_ITEM_NAME, 	// name of this plugin
			'author' 	=> 'Dave Nicosia'  // author of this plugin
		)
	);
}
add_action( 'admin_init', 'edd_sl_wpcompear_plugin_updater', 0 );




/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-compear-activator.php
 */
function activate_WP_Compear() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-compear-activator.php';
	WP_Compear_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-compear-deactivator.php
 */
function deactivate_WP_Compear() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-compear-deactivator.php';
	WP_Compear_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_WP_Compear' );
register_deactivation_hook( __FILE__, 'deactivate_WP_Compear' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-compear.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_WP_Compear() {

	$plugin = new WP_Compear();
	$plugin->run();

}
run_WP_Compear();
