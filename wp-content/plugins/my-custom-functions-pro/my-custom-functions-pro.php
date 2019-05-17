<?php
/**
 * Plugin Name: My Custom Functions PRO
 * Plugin URI: https://www.spacexchimp.com/plugins/my-custom-functions-pro.html
 * Description: Easily and safely add your custome functions (PHP code) directly out of your WordPress Admin Area, without the need to have an external editor.
 * Author: Space X-Chimp
 * Author URI: https://www.spacexchimp.com
 * Version: 2.12
 * License: GPL3
 * Text Domain: my-custom-functions-pro
 * Domain Path: /languages/
 *
 * Copyright 2015-2018 Space X-Chimp ( website : https://www.spacexchimp.com )
 *
 * All PHP code, and PDF/POT/PO/MO files is released under the GNU General Public License version 3.0.
 * All HTML/CSS/JAVASCRIPT code, and PNG files is released under the restrictive proprietary license.
 *
 * ███████╗██████╗  █████╗  ██████╗███████╗    ██╗  ██╗      ██████╗██╗  ██╗██╗███╗   ███╗██████╗
 * ██╔════╝██╔══██╗██╔══██╗██╔════╝██╔════╝    ╚██╗██╔╝     ██╔════╝██║  ██║██║████╗ ████║██╔══██╗
 * ███████╗██████╔╝███████║██║     █████╗       ╚███╔╝█████╗██║     ███████║██║██╔████╔██║██████╔╝
 * ╚════██║██╔═══╝ ██╔══██║██║     ██╔══╝       ██╔██╗╚════╝██║     ██╔══██║██║██║╚██╔╝██║██╔═══╝
 * ███████║██║     ██║  ██║╚██████╗███████╗    ██╔╝ ██╗     ╚██████╗██║  ██║██║██║ ╚═╝ ██║██║
 * ╚══════╝╚═╝     ╚═╝  ╚═╝ ╚═════╝╚══════╝    ╚═╝  ╚═╝      ╚═════╝╚═╝  ╚═╝╚═╝╚═╝     ╚═╝╚═╝
 *
 */


/**
 * Prevent Direct Access
 */
defined( 'ABSPATH' ) or die( "Restricted access!" );

/**
 * Define global constants
 */
$plugin_data = get_file_data( __FILE__,
                              array(
                                     'name'    => 'Plugin Name',
                                     'version' => 'Version',
                                     'text'    => 'Text Domain'
                                   )
                            );
function spacexchimp_p011_define_constants( $constant_name, $value ) {
    $constant_name = 'SPACEXCHIMP_P011_' . $constant_name;
    if ( !defined( $constant_name ) )
        define( $constant_name, $value );
}
spacexchimp_p011_define_constants( 'FILE', __FILE__ );
spacexchimp_p011_define_constants( 'DIR', dirname( plugin_basename( __FILE__ ) ) );
spacexchimp_p011_define_constants( 'BASE', plugin_basename( __FILE__ ) );
spacexchimp_p011_define_constants( 'URL', plugin_dir_url( __FILE__ ) );
spacexchimp_p011_define_constants( 'PATH', plugin_dir_path( __FILE__ ) );
spacexchimp_p011_define_constants( 'SLUG', dirname( plugin_basename( __FILE__ ) ) );
spacexchimp_p011_define_constants( 'NAME', $plugin_data['name'] );
spacexchimp_p011_define_constants( 'VERSION', $plugin_data['version'] );
spacexchimp_p011_define_constants( 'TEXT', $plugin_data['text'] );
spacexchimp_p011_define_constants( 'PREFIX', 'spacexchimp_p011' );
spacexchimp_p011_define_constants( 'SETTINGS', 'spacexchimp_p011' );

/**
 * Load the plugin modules
 */
require_once( SPACEXCHIMP_P011_PATH . 'inc/php/core.php' );
require_once( SPACEXCHIMP_P011_PATH . 'inc/php/upgrade.php' );
require_once( SPACEXCHIMP_P011_PATH . 'inc/php/versioning.php' );
require_once( SPACEXCHIMP_P011_PATH . 'inc/php/enqueue.php' );
require_once( SPACEXCHIMP_P011_PATH . 'inc/php/functional.php' );
require_once( SPACEXCHIMP_P011_PATH . 'inc/php/controls.php' );
require_once( SPACEXCHIMP_P011_PATH . 'inc/php/page.php' );
require_once( SPACEXCHIMP_P011_PATH . 'inc/php/messages.php' );
