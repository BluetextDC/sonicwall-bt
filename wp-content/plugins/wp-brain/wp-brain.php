<?php
/*
 * Plugin Name: WP Brain
 * Plugin URI:  https://www.wpbrain.com
 * Version:     1.3.6
 * Author:      ERROPiX
 * Author URI:  https://www.erropix.com
 * Description: Use the WP Brain filters to control when and where your website content will be visible and who can see it.
 *
 * Text Domain: wpbrain
 * Domain Path: /languages
 */

// don't load directly
if (!defined('ABSPATH')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// Plugin constants
define('WPBRAIN_ID', 20101086);
define('WPBRAIN_FILE', __FILE__);
define('WPBRAIN_URL', plugin_dir_url(__FILE__));
define('WPBRAIN_DIR', plugin_dir_path(__FILE__));
define('WPBRAIN_BASE', plugin_basename(__FILE__));
define('WPBRAIN_VERSION', '1.3.6');
define('WPBRAIN_PHP_VERSION', '5.4.0');

// Loading text domain
function wpbrain_load_textdomain()
{
    load_plugin_textdomain('wpbrain', false, '/wp-brain/languages/');
}

add_action('plugins_loaded', 'wpbrain_load_textdomain');

// Require the minimum PHP version
if (version_compare(PHP_VERSION, WPBRAIN_PHP_VERSION, '<')) {
    add_action('admin_notices', 'wpbrain_php_version_admin_notice');
    function wpbrain_php_version_admin_notice()
    {
        echo '<div class="error notice"><p>';
        printf(__("WP Brain require PHP version %s or newer", 'wpbrain'), WPBRAIN_PHP_VERSION);
        echo '</p></div>';
    }
} else {
    // Composer autoloader
    require 'vendor/autoload.php';
}
