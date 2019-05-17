<?php

 /*
  Plugin Name: Show-Hide/Collapse-Expand
  Plugin URI: http://showhide.bunte-giraffe.de
  Description: Save space on your pages, posts, sidebars. Hide the content before user clicks to see it. Collapse long lists, create FAQs & more.
  Version: 1.2.1
  Author: Bunte Giraffe
  Author URI: http://bunte-giraffe.de
  License: GPLv2
  Domain Path: /languages
  Text Domain: show_hide_collapse_expand
  */
 
/* Make sure we don't expose any info if called directly */
if ( !function_exists( 'add_action' ) ) {
	die("This application is not meant to be called directly!");
}

require_once('ErrorLogging.php');

/* Tabs-related includes */
require_once('PluginContext.php');
require_once('TabManager.php');

require_once('Tab_help.php');
require_once('Tab_settings.php');
require_once('Tab_presets.php');

/* Plugin settings */
define( "BG_SHCE_TMCE_STYLESHEET_NAME", "bg_show_hide_styleBE-tmce.css");
define( "BG_SHCE_TMCE_STYLESHEET_FILE_URL", "assets/css/bg-show-hide.css");
define( "BG_SHCE_TMCE_REGISTERED_PLUGIN_NAME", "bg_show_hide_tc_button");
define( "BG_SHCE_TMCE_REGISTERED_PLUGIN_FILE_URL", "assets/js/bg-show-hide-mce-plugin.js");
define( "BG_SHCE_TMCE_PLUGIN_USER_OPTION_NAME", "rich_editing");

/* Register Wordpress Hooks */
register_uninstall_hook( __FILE__, 'bg_show_hide_plugin_uninstall' );
register_activation_hook( __FILE__, 'bg_show_hide_activate' );
register_deactivation_hook( __FILE__, 'bg_show_hide_deactivate' );

function bg_show_hide_plugin_uninstall() {
	delete_option('bg_shce_effectsEnabled');
	delete_option('bg_shce_animationEffect');
	delete_option('bg_shce_animationSpeed');
	delete_option('bg_shce_stickToBottom');
	delete_option('bg_shce_preset1');
}

function bg_show_hide_activate() {

}

function bg_show_hide_deactivate() {

}

/* Adding a custom "Admin Action" to hook to requests sent from plugin's backend */
/* TODO: comment/uncomment relevant action handlers */
$bg_show_hide_registered_admin_actions = array(
	"bg_show_hide_add_plugin_mgmgt_menu" => "admin_menu",
	"bg_show_hide_register_shortcodes" => "init",
	"bg_show_hide_prepare_options" => "init",
	"bg_show_hide_enqueue_scripts" => array("wp_enqueue_scripts","admin_enqueue_scripts"),
	"bg_show_hide_enqueue_styles" => "wp_enqueue_scripts",	
	"bg_show_hide_save_plugin_settings" => "admin_post_bg_show_hide_save_plugin_settings",
	/* TinyMCE-related actions */
	"bg_show_hide_add_mce_style" => "admin_enqueue_scripts",
	"bg_show_hide_add_tc_button" => "admin_head"
);
add_filter("the_content", "bg_filter_shortcode_presets", 9);

/* Register admin action handlers.
 * One admin action can have multiple handlers.
 * Up on an action the handlers will be called in the same order they were registered.
 * This could be prioritised with add_action() parameter $priority
 */
foreach( $bg_show_hide_registered_admin_actions as $action_handler => $admin_action ) {
	if ( !is_array($admin_action)) {
		add_action( $admin_action, $action_handler);
	}
	elseif ( is_array($admin_action) ) {
		foreach ($admin_action as $admin_action_in_array) {
			add_action( $admin_action_in_array, $action_handler);
		}
	}
}

function bg_show_hide_prepare_options() {
	/* Add default plugin options if not added already */
	if (get_option('bg_shce_effectsEnabled', false) === false) {
		add_option('bg_shce_effectsEnabled', '0');
	}
	
	if (get_option('bg_shce_animationEffect', false) === false) {
		add_option('bg_shce_animationEffect', 'blind');
	}
	
	if (get_option('bg_shce_animationSpeed', false) === false) {
		add_option('bg_shce_animationSpeed', '400');
	}
	
	if (get_option('bg_shce_stickToBottom', false) === false) {
		add_option('bg_shce_stickToBottom', '0');
	}
	
	if (get_option('bg_shce_preset1', false) === false) {
		add_option('bg_shce_preset1', 'view="link" icon="arrow" color="#0071bb" collapse_text="Click to close" inline_css="font-weight:bold;"');
	}

}

function bg_show_hide_register_shortcodes() {
	/* List valid shortcodes here and their appropriate tags here */
	$bg_show_hide_registered_shortcodes = array(
		"bg_collapse" => "bg_show_hide_shortcode",
		"bg_collapse_level2" => "bg_show_hide_shortcode",
		"bg_collapse_level3" => "bg_show_hide_shortcode",
	);
	/* Register shortcodes */
	foreach( $bg_show_hide_registered_shortcodes as $shortcodeTag => $shortcodeHandler) {
		add_shortcode( $shortcodeTag, $shortcodeHandler);
	}
}

function bg_show_hide_save_plugin_settings() {
	if ( isset ( $_POST["save_plugin_settings"] ) ) {
		if ( isset( $_POST["enable_effects"] ) ) {
			update_option( "bg_shce_effectsEnabled", "1" );			
		}
		else {
			update_option( "bg_shce_effectsEnabled", "0" );			
		}
		if ( isset( $_POST["bg_shce_speed"] ) ) {					
			update_option( "bg_shce_animationSpeed", intval($_POST["bg_shce_speed"] ));			
		}
		
		if ( isset( $_POST["bg_shce_stick_to_bottom"] ) ) {
			update_option( "bg_shce_stickToBottom", "1" );			
		}
		else {
			update_option( "bg_shce_stickToBottom", "0" );			
		}

		if ( isset( $_POST["bg_shce_effect"] ) ) {
			update_option( "bg_shce_animationEffect", $_POST["bg_shce_effect"] );			
		}						
	}
	else {
		// TODO: write to log
	}
	$urlBack = urldecode( admin_url( 'tools.php' ) );
	$urlBack = add_query_arg( "page", "bg_show_hide", $urlBack );
	$urlBack = add_query_arg( "tab", "settings", $urlBack);

	wp_safe_redirect( $urlBack );

}

function bg_filter_shortcode_presets( $content = null) {
	if ( strstr($content, '[bg_collapse_preset1') || strstr($content, strtoupper('[bg_collapse_preset1')) ) {
		$content = str_replace('bg_collapse_preset1', 'bg_collapse '.str_replace('\"', '"',get_option('bg_shce_preset1',' ')), $content);
	}
	return $content;
} 

function bg_show_hide_shortcode($attr, $content = null) {
	
	$a = shortcode_atts( 
			array( 	'view' => 'link',
					'color' => 'inherit',
					'expand_text' => 'Show More',
					'collapse_text' => '',
					'onclick' => '',
					'inline_css' => '',
					'icon' => ''), 	
			$attr
		);
		
	$bg_uniq_id = uniqid('',true);
	$bg_uniq_id = str_replace(".","0",$bg_uniq_id);
	$bg_list_start = "";
	$bg_list_end = "";
	$bg_hidden_content_start = "";
	$bg_hidden_content_end = "";
	$bg_showmore_content_tag = "div";
	$bg_showmore_extra_attributes = "";
	$bg_text_color = $a["color"];
	$bg_expand_text = $a["expand_text"];
	$bg_collapse_text = $a["collapse_text"];
	$bg_btn_icon_class = $a["icon"] ? "bg-" . $a["icon"] : "";
	$bg_onclick = $a["onclick"] === '' ? '' : 'onclick="'.$a["onclick"].'"';
	$bg_inline_css = $a["inline_css"];
	$bg_button_or_link_sticks_to_content_bottom = get_option('bg_shce_stickToBottom', '0');
	$showmore_btn = "";
	$bg_button_or_link = false;	
	$bg_uniq_id_holder = "<input type='hidden' bg_collapse_expand='$bg_uniq_id' value='$bg_uniq_id'>";
	
	$bg_showmore_text = "<input type='hidden' id='bg-show-more-text-$bg_uniq_id' value='" . htmlspecialchars($bg_expand_text) . "'>";
	$bg_showless_text = "<input type='hidden' id='bg-show-less-text-$bg_uniq_id' value='" . htmlspecialchars($bg_collapse_text) . "'>";

	$bg_hidden_data = $bg_uniq_id_holder . $bg_showmore_text . $bg_showless_text;
	
	
	if ( $a["view"] == 'link' || $a["view"] == 'faq-link' ) {
		$showmore_btn = "<a id='bg-showmore-action-$bg_uniq_id' class='bg-showmore-plg-link $bg_btn_icon_class' $bg_onclick style=\" color:" . $bg_text_color . ';' . $bg_inline_css . ";\" href='#'>" . $bg_expand_text . "</a>";
		$bg_hidden_content_start = "<div class=\"bg-margin-for-link\">";
		$bg_hidden_content_end = "</div>";
		$bg_button_or_link = ($a["view"] == 'link') ?  true : false;	
	}
	elseif ( $a["view"] == 'link-inline') {
		$showmore_btn = "<a id='bg-showmore-action-$bg_uniq_id' class='bg-showmore-plg-link $bg_btn_icon_class' $bg_onclick style=\" color:" . $bg_text_color . ";" . $bg_inline_css . "\" href='#'>" . $bg_expand_text . "</a>";	
		$bg_hidden_content_start = "<span style='display:inline;'>";
		$bg_hidden_content_end = "</span>";
		$bg_showmore_content_tag = "span";
	}
	elseif ( $a["view"] == 'link-list') {
		$showmore_btn = "<a id='bg-showmore-action-$bg_uniq_id' class='bg-showmore-plg-link $bg_btn_icon_class' $bg_onclick style=\" color:" . $bg_text_color . ";" . $bg_inline_css . "\" href='#'>" . $bg_expand_text . "</a>";	
		$bg_list_start = "<ul style='margin-top:0;margin-bottom:0'>";
		$bg_list_end = "</ul>";
		$bg_showmore_content_tag = "ul";
		$bg_showmore_extra_attributes = " class='bg-showmore-no-padding-no-margin'";
	}
	elseif ( $a["view"] == 'button-orange') {
		$showmore_btn = "<button id='bg-showmore-action-$bg_uniq_id' class='bg-showmore-plg-button bg-orange-button $bg_btn_icon_class' $bg_onclick "  . " style=\" color:" . $bg_text_color . ";". $bg_inline_css ."\">" . $bg_expand_text . "</button>";	
		$bg_button_or_link = true;
		$bg_showmore_content_tag = "div";
	}	
	elseif ( $a["view"] == 'button-blue') {
		$showmore_btn = "<button id='bg-showmore-action-$bg_uniq_id' class='bg-showmore-plg-button bg-blue-button $bg_btn_icon_class' $bg_onclick "  . " style=\" color:" . $bg_text_color . ";" . $bg_inline_css . "\">" . $bg_expand_text . "</button>";	
		$bg_button_or_link = true;
	}	
	elseif ( $a["view"] == 'button-green') {
		$showmore_btn = "<button id='bg-showmore-action-$bg_uniq_id' class='bg-showmore-plg-button bg-green-button $bg_btn_icon_class' $bg_onclick "  . " style=\" color:" . $bg_text_color . ";" . $bg_inline_css . "\">" . $bg_expand_text . "</button>";
		$bg_button_or_link = true;
	}	
	elseif ( $a["view"] == 'button-red') {
		$showmore_btn = "<button id='bg-showmore-action-$bg_uniq_id' class='bg-showmore-plg-button bg-red-button $bg_btn_icon_class' $bg_onclick " . " style=\" color:" . $bg_text_color . ";" . $bg_inline_css . "\">" . $bg_expand_text . "</button>";	
		$bg_button_or_link = true;
	}	
			
	$content = $bg_hidden_content_start . 
			   $bg_hidden_data . 
			   ( ($bg_button_or_link_sticks_to_content_bottom && $bg_button_or_link) ? '' : $showmore_btn) .
			   $bg_list_end . 
			   "<" . $bg_showmore_content_tag . $bg_showmore_extra_attributes . " id='bg-showmore-hidden-$bg_uniq_id' >" . 
			   $content . 
			   "</" . $bg_showmore_content_tag . ">" . 
			   $bg_hidden_content_end . 
			   $bg_list_start.
			   ( ($bg_button_or_link_sticks_to_content_bottom && $bg_button_or_link) ? $showmore_btn : '');;
	$content = do_shortcode($content);
	
	wp_localize_script( "bg-show-hide-script", 'BG_SHCE_USE_EFFECTS', get_option('bg_shce_effectsEnabled','0'));
	wp_localize_script( "bg-show-hide-script", 'BG_SHCE_TOGGLE_SPEED', get_option('bg_shce_animationSpeed','400'));
	wp_localize_script( "bg-show-hide-script", 'BG_SHCE_TOGGLE_OPTIONS', 'none');
	wp_localize_script( "bg-show-hide-script", 'BG_SHCE_TOGGLE_EFFECT', get_option('bg_shce_animationEffect','blind'));	

    // always return
    return $content;
}


function bg_show_hide_add_plugin_mgmgt_menu() {
	add_management_page(										/* Add submenu to Tools menu */
		"Show-Hide/Collapse-Expand Options:",					/* $page_title */
		"Show-Hide / <br>Collapse-Expand ", 					/* $menu_title */
		"edit_posts",											/* $capability (aka access rights). 
																 * Other valid values: administrator, editor, author, contributor and subscriber */
		"bg_show_hide",											/* $menu_slug - unique part of the plugin URL that leads directly to the menu */
		"bg_show_hide_menu_handler"								/* $function - menu action handler function (aka main() function)*/
	);
}

function bg_show_hide_add_scripts() {

}

function bg_show_hide_enqueue_scripts() {
	wp_enqueue_script( "bg-show-hide-script", 
		plugins_url( "assets/js/bg-show-hide.js", __FILE__ ), array('jquery', 'jquery-effects-core', 'jquery-effects-slide', 'jquery-effects-highlight', 'jquery-effects-fold', 'jquery-effects-blind'), false, true
	);
	
}

function bg_show_hide_enqueue_styles() {
	wp_enqueue_style( "bg-shce-genericons", 
		plugins_url( "assets/css/genericons/genericons.css", __FILE__ ) 
	); 

	wp_enqueue_style( "bg-show-hide", 
		plugins_url( "assets/css/bg-show-hide.css", __FILE__ )
	);
}

/* Tegisterd Wordpress filters */
$bg_show_hide_registered_filters = array(
	"do_shortcode" => "widget_text",
	/* TinyMCE-related filters */
	"bg_show_hide_add_tinymce_plugin" => "mce_external_plugins",
	"bg_show_hide_register_tc_button" => "mce_buttons"
);

foreach( $bg_show_hide_registered_filters as $filterHandler => $filterTag) {
	add_filter( $filterTag, $filterHandler);
}

function bg_show_hide_settings_link($links, $file) {
	if ( $file == plugin_basename( __FILE__  ) ) {
		$links['settings'] = sprintf( '<a href="%s"> %s </a>', admin_url( 'tools.php?page=bg_show_hide&tab=settings' ), __( 'Settings', 'plugin_domain' ) );
		$links['help'] = sprintf( '<a href="%s"> %s </a>', admin_url( 'tools.php?page=bg_show_hide&tab=help' ), __( 'Help', 'plugin_domain' ) );
	}
	
	return $links;
}

add_filter('plugin_action_links', 'bg_show_hide_settings_link', 10, 2);


function bg_show_hide_menu_handler() {

	/* Prepare Plugin Context */
	$pluginContext = new bg_show_hide_PluginContext( );
	$pluginContext->setAdminPostUrl( admin_url( 'admin-post.php' ) );
	$pluginContext->setRedirectValue( $_SERVER['REQUEST_URI'] );
	$pluginContext->setPluginName( "Show-Hide/Collapse-Expand");
	$pluginContext->setPluginSlug( "bg_show_hide");
	$pluginContext->setEffectsEnabledOption( get_option('bg_shce_effectsEnabled','0') );
	$pluginContext->setAnimationEffect( get_option('bg_shce_animationEffect','blind') );
	$pluginContext->setAnimationSpeed( get_option('bg_shce_animationSpeed', 400) );
	$pluginContext->setStickToBottom( get_option('bg_shce_stickToBottom', '0') );
	$pluginContext->setPreset1( get_option('bg_shce_preset1', 'view="link" icon="arrow" color="#0071bb" collapse_text="Click to close" inline_css="font-weight:bold;"') );
	
	$activeTabName = "settings";
	if( isset( $_GET["tab"] ) ) {
		$activeTabName = $_GET["tab"];
	}
		
	$registeredTabs = array(
		"settings" => "bg_show_hide_Settings",
		"presets" => "bg_show_hide_Presets",
		"help" => "bg_show_hide_help"
	);
	
	/* Init. Tab Manager and populate it with tabs */
	$tabManager = new bg_show_hide_TabManager( new bg_show_hide_TabView( $pluginContext) );

	foreach( $registeredTabs as $tabName => $tabClass) {
		if( $activeTabName == $tabName) {
			$tabManager->addTab( new $tabClass( $pluginContext, $tabName), true );
		}
		else {
			$tabManager->addTab( new $tabClass( $pluginContext, $tabName) );
		}
	}

	if( !$tabManager->displayActiveTab() ) {
		//echo "DBG: ERROR: Failed to display an active tab";
	}
}

/* Extending TinyMCE */
function bg_show_hide_add_tinymce_plugin( $registeredPlugins) {
	if ( get_user_option( BG_SHCE_TMCE_PLUGIN_USER_OPTION_NAME) != 'true') {
		return $registeredPlugins;
	}

	$registeredPlugins[ BG_SHCE_TMCE_REGISTERED_PLUGIN_NAME] =
		plugins_url( BG_SHCE_TMCE_REGISTERED_PLUGIN_FILE_URL, __FILE__ );

	return $registeredPlugins;
}

function bg_show_hide_register_tc_button( $registeredButtons) {
	if ( get_user_option( BG_SHCE_TMCE_PLUGIN_USER_OPTION_NAME) != 'true') {
		return $registeredButtons;
	}

	array_push( $registeredButtons, "bg_show_hide_tc_button");
	array_push( $registeredButtons, "bg_show_hide_tc_button_preset1");

	return $registeredButtons;
}

function bg_show_hide_add_tc_button() {
	global $typenow;
	
	if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
		return;
    }

	if( !in_array( $typenow, array( 'post', 'page' ) ) ) {
		return;
	}
}

function bg_show_hide_add_mce_style() {
	
	wp_enqueue_style( 'bg-shce-genericons', 
		plugins_url( 'assets/css/genericons/genericons.css', __FILE__ ) ); 

	wp_enqueue_style( BG_SHCE_TMCE_STYLESHEET_NAME,
		plugins_url( BG_SHCE_TMCE_STYLESHEET_FILE_URL, __FILE__ ) );

	wp_localize_script( "jquery", "BG_SHCE_PRESET1", get_option('bg_shce_preset1',' '));

}
/* end TinyMCE button*/

?>