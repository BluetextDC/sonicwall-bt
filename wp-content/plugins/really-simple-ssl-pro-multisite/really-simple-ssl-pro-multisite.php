<?php
/**
 * Plugin Name: Really Simple SSL pro multisite
 * Plugin URI: https://www.really-simple-ssl.com/pro
 * Description: Add on for Really Simple SSL
 * Version: 2.0.20
 * Text Domain: really-simple-ssl-pro
 * Domain Path: /languages
 * Author: Rogier Lankhorst
 * Author URI: https://www.rogierlankhorst.com
 */

/*  Copyright 2017  Rogier Lankhorst

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
*/

defined('ABSPATH') or die("you do not have access to this page!");

class REALLY_SIMPLE_SSL_PRO_MULTISITE {

    private static $instance;
    public $rssl_front_end;
    public $rssl_mixed_content_fixer;
    public $rsssl_multisite;
    public $rsssl_cache;
    public $rsssl_server;
    public $really_simple_ssl;
    public $rsssl_help;


    private function __construct() {}

    public static function instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof REALLY_SIMPLE_SSL_PRO_MULTISITE ) ) {
            self::$instance = new REALLY_SIMPLE_SSL_PRO_MULTISITE;
            if (self::$instance->is_compatible()) {

                self::$instance->setup_constants();
                self::$instance->includes();

                self::$instance->rsssl_premium_options = new rsssl_premium_options();
                self::$instance->rsssl_scan = new rsssl_scan();
                self::$instance->rsssl_importer = new rsssl_importer();

                if ( is_multisite() ) {
                    self::$instance->rsssl_pro_multisite = new rsssl_pro_multisite();
                    self::$instance->rsssl_pro_multisite = new rsssl_licensing_ms();
                } else {
                    // Backwards compatibility for add-ons
                    //global $rsssl_licensing;
                    //$rsssl_licensing           = self::$instance->rsssl_licensing;
                    self::$instance->rsssl_licensing = new rsssl_licensing();
                }

                if (version_compare(phpversion(), '5.6', '>=')) {
                    self::$instance->rsssl_support = new rsssl_support();
                }

                self::$instance->hooks();
            } else {
                add_action('network_admin_notices', array('REALLY_SIMPLE_SSL_PRO_MULTISITE', 'admin_notices'));
                //deactivate_plugins( plugin_basename( __FILE__ ) );
            }

        }

        return self::$instance;

    }

    /*

       Checks if one of the necessary plugins is active, and of the required version.

    */

    private function is_compatible(){
        require_once(ABSPATH.'wp-admin/includes/plugin.php');
        $core_plugin = 'really-simple-ssl/rlrsssl-really-simple-ssl.php';
        if ( is_plugin_active($core_plugin)) $core_plugin_data = get_plugin_data( WP_PLUGIN_DIR .'/'. $core_plugin, false, false );
        if ( is_plugin_active($core_plugin) && version_compare($core_plugin_data['Version'] ,'2.5.11','>') ) {
            return true;
        }

        $per_page_plugin = 'really-simple-ssl-on-specific-pages/really-simple-ssl-on-specific-pages.php';
        if (is_plugin_active($per_page_plugin)) $per_page_plugin_data = get_plugin_data( WP_PLUGIN_DIR .'/'. $per_page_plugin, false, false );
        if (is_plugin_active($per_page_plugin) && version_compare($per_page_plugin_data['Version'] , '1.0.6','>' )) {
            return true;
        }

        //nothing yet? then...sorry, but no, not compatible.
        return false;
    }

    private function setup_constants() {
        require_once(ABSPATH.'wp-admin/includes/plugin.php');
        $plugin_data = get_plugin_data( __FILE__ );

        define('rsssl_pro_url', plugin_dir_url(__FILE__ ));
        define('rsssl_pro_path', plugin_dir_path(__FILE__ ));
        define('rsssl_pro_plugin', plugin_basename( __FILE__ ) );
        define('rsssl_pro_version', $plugin_data['Version'] );
        define('rsssl_pro_ms_version', $plugin_data['Version'].time());
        define('rsssl_pro_plugin_file', __FILE__);

        if (!defined('REALLY_SIMPLE_SSL_URL')) define( 'REALLY_SIMPLE_SSL_URL', 'https://www.really-simple-ssl.com');
        define( 'REALLY_SIMPLE_SSL_PRO', 'Really Simple SSL pro multisite' );

    }

    private function includes() {
        require_once( rsssl_pro_path .  '/class-premium-options.php' );

        require_once( rsssl_pro_path .  '/class-scan.php' );
        require_once( rsssl_pro_path .  '/class-importer.php' );
        require_once( rsssl_pro_path .  '/class-cert-expiration.php' );

        if (is_multisite()) {
            require_once( rsssl_pro_path .  '/class-multisite.php' );
            require_once( rsssl_pro_path .  '/class-licensing-multisite.php' );
        } else {
            require_once( rsssl_pro_path .  '/class-licensing.php' );
        }

        if (version_compare(phpversion(), '5.6', '>=')) {
            require_once(rsssl_pro_path . '/class-support.php');
        }
    }

    private function hooks() {

    }


    /**
     * Handles the displaying of any notices in the admin area
     *
     * @since 1.0.28
     * @access public
     * @return void
     */

    public static function admin_notices() {
        require_once(ABSPATH.'wp-admin/includes/plugin.php');
        $core_plugin = false;
        $per_page_plugin = false;

        $core_plugin = '/really-simple-ssl/rlrsssl-really-simple-ssl.php';
        if (is_plugin_active($core_plugin)) $core_plugin_data = get_plugin_data( WP_PLUGIN_DIR . $core_plugin, false, false );

        if (!is_plugin_active_for_network($core_plugin) || !is_plugin_active($core_plugin))  {
            ?>
            <div id="message" class="error fade notice">
                <h1><?php echo __("Plugin dependency error","really-simple-ssl-pro");?></h1>
                <p><?php echo __("Really Simple SSL pro is an add-on for Really Simple SSL, and cannot do it on its own :(","really-simple-ssl-pro");?></p>
                <?php if(!is_plugin_active_for_network($core_plugin)){?>
                <p><?php echo __("Please activate Really Simple SSL network wide.","really-simple-ssl-pro");?>
                    <?php } else {?>
                <p><?php echo __("Please install and activate Really Simple SSL before activating this add-on.","really-simple-ssl-pro");?>
                    <?php }?>
                </p></div>
            <?php
        }elseif ($core_plugin && version_compare($core_plugin_data['Version'], '2.5.12', '<') ) {
            ?>
            <div id="message" class="error fade notice">
                <h1><?php echo __("Plugin dependency error","really-simple-ssl-pro");?></h1>
                <p><?php echo __("Really Simple SSL needs to be updated to the latest version to be compatible.","really-simple-ssl-pro");?></p>
                <p><?php echo __("Please upgrade to the latest version to be able use the full functionality of the plugin.","really-simple-ssl-pro");?>
                </p></div>
            <?php
        }

    }
}

require_once(ABSPATH.'wp-admin/includes/plugin.php');
$pro_plugin = 'really-simple-ssl-pro/really-simple-ssl-pro.php';
if (is_plugin_active( $pro_plugin) ) {
    deactivate_plugins( $pro_plugin );
}

if (!class_exists('REALLY_SIMPLE_SSL_PRO')) {
    function RSSSL_PRO() {
        return REALLY_SIMPLE_SSL_PRO_MULTISITE::instance();
    }

    $scan_active = get_option("rsssl_scan_active");
    if (is_admin()) {
        add_action( 'plugins_loaded', 'RSSSL_PRO', 10 );
    }
}

/**
 * Enables the HTTP Strict Transport Security (HSTS) header also on non apache servers.
 *
 * @since 1.0.25
 */

$wp_hsts = get_option("rsssl_hsts_no_apache");
if ($wp_hsts && is_ssl()) {
    add_action( 'send_headers', 'rsssl_pro_hsts' );
}

if (!function_exists('rsssl_pro_hsts')) {
    function rsssl_pro_hsts() {
        $preload = get_option("rsssl_hsts_preload");
        if (!$preload) {
            header( 'Strict-Transport-Security: max-age=31536000' );
        } else {
            header( 'Strict-Transport-Security: max-age=63072000; includeSubDomains; preload' );
        }
    }
}

/*/*
*
* Set a secure cookie, but only if the site is enabled for SSL, not per page.
* This setting can be used on multisite as well, as it will decide per site what setting to use.
* @since 2.0.6
* */

add_filter( 'secure_logged_in_cookie', 'rsssl_secure_logged_in_cookie' , 10, 3);
function rsssl_secure_logged_in_cookie($secure_logged_in_cookie, $user_id, $secure){
    $options = get_option("rsssl_options");
    $ssl_enabled = isset($options['ssl_enabled']) ? $options['ssl_enabled'] : false;

    if (!defined('rsssl_pp_version') && $ssl_enabled) {
        return true;
    }
    return $secure_logged_in_cookie;
}
