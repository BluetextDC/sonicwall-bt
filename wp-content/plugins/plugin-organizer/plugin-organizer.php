<?php
/*
Plugin Name: Plugin Organizer
Plugin URI: http://www.sterupdesign.com/dev/wordpress/plugins/plugin-organizer/
Description: A plugin for specifying the load order of your plugins.
Version: 9.6.4
Author: Jeff Sterup
Author URI: http://www.sterupdesign.com
License: GPL2
*/

require_once(WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)) . "/lib/PluginOrganizer.class.php");

$PluginOrganizer = new PluginOrganizer(__FILE__);
?>