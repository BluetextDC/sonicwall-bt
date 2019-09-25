<?php
/*
 * Plugin Name:   SW KB Sitemaps
 * Plugin URI:    http://www.sonicwall.com
 * Description:   A plugin to add KB articles to our sitemap
 * Version:       1.0
 * Author:        Brad Kendall
 * Author URI:    https://www.sonicwall.com
 */

add_action( 'bwp_gxs_modules_built', 'my_bwp_gxs_add_modules' );
function my_bwp_gxs_add_modules() {
    global $bwp_gxs;
    $bwp_gxs->add_module( 'kb' );
}

add_filter( 'bwp_gxs_rewrite_rules', 'my_bwp_gxs_add_rewrite_rules' );
function my_bwp_gxs_add_rewrite_rules() {
    $my_rules = array(
        'kb.xml' => 'index.php?gxs_module=kb'
    );
    return $my_rules;
}

add_filter('bwp_gxs_module_dir', 'bwp_gxs_module_dir');
function bwp_gxs_module_dir()
{
    return plugin_dir_path(__FILE__);
}

?>