<?php
/**
 * Plugin Name: Gravity Forms Eloqua
 * Plugin URI: https://briandichiara.com/product/gravityforms-eloqua/
 * Description: Integrate Eloqua into Gravity Forms - passes your form data over to existing Eloqua Forms
 * Version: 2.1.6
 * Author: Brian DiChiara
 * Author URI: http://www.briandichiara.com
 */

define( 'GFELOQUA_VERSION', '2.1.6' );
define( 'GFELOQUA_OPT_PREFIX', 'gfeloqua_' );
define( 'GFELOQUA_PATH', plugin_dir_path( __FILE__ ) );
define( 'GFELOQUA_URL', plugin_dir_url( __FILE__ ) );
define( 'GFELOQUA_DIR', GFELOQUA_URL );

require_once( GFELOQUA_PATH . '/includes/class-gfeloqua-bootstrap.php' );

add_action( 'gform_loaded', array( 'GFEloqua_Bootstrap', 'load' ), 5 );
