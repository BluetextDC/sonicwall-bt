<?php

  $aip_standalone = true;

  
 
  $iframeStandaloneDefaultOptions = array(
    'securitykey'=>'standalone',
    'src' => '//www.tinywebgallery.com', 'width' => '100%','height' => '600', 
    'scrolling' => 'auto', 'marginwidth' => '0', 'marginheight' => '0',
    'frameborder' => '0', 'transparency' => 'true', 'content_id' => '', 'content_styles' => '',
    'hide_elements' => '', 'class' => '', 'shortcode_attributes' => 'true', 'url_forward_parameter' => '',
    'id' => 'advanced_iframe', 'name' => '',
    'onload' => '', 'onload_resize' => 'false', 'onload_scroll_top' => 'false',
    'additional_js' => '', 'additional_css' => '', 'store_height_in_cookie' => 'false',
    'additional_height' => '0', 'iframe_content_id' => '', 'iframe_content_styles' => '',
    'iframe_hide_elements' => '', 'version_counter' => '1', 'onload_show_element_only' => '' ,
    'include_url'=> '','include_content'=> '','include_height'=> '','include_fade'=> '',
    'include_hide_page_until_loaded' => 'false', 'donation_bottom' => 'false',
    'onload_resize_width' => 'false', 'resize_on_ajax' => '', 'resize_on_ajax_jquery' => 'true',
    'resize_on_click' => '', 'resize_on_click_elements' => 'a', 'hide_page_until_loaded' => 'false',
    'show_part_of_iframe' => 'false', 'show_part_of_iframe_x' => '100', 'show_part_of_iframe_y' => '100',
    'show_part_of_iframe_width' => '400', 'show_part_of_iframe_height' => '300',
    'show_part_of_iframe_new_window' => '' ,'show_part_of_iframe_new_url' => '',
    'show_part_of_iframe_next_viewports_hide' => 'false', 'show_part_of_iframe_next_viewports' => '',
    'show_part_of_iframe_next_viewports_loop' => 'false', 'style' => '',
    'use_shortcode_attributes_only' => 'false', 'enable_external_height_workaround' => 'external',
    'keep_overflow_hidden' => 'false', 'hide_page_until_loaded_external' => 'false',
    'onload_resize_delay' => '', 'expert_mode' => 'false',
    'show_part_of_iframe_allow_scrollbar_vertical' => 'false',
    'show_part_of_iframe_allow_scrollbar_horizontal' => 'false',
    'hide_part_of_iframe' => '', 'change_parent_links_target' => '',
    'change_iframe_links' => '','change_iframe_links_target' => '',
    'browser' => '', 'show_part_of_iframe_style' => '',
    'map_parameter_to_url' => '', 'iframe_zoom' => '',
    'show_iframe_loader' => 'false',
    'tab_visible' => '', 'tab_hidden' => '',
    'enable_responsive_iframe' => 'false',
    'allowfullscreen' => 'false', 'iframe_height_ratio' => '',
    'enable_lazy_load' => 'false', 'enable_lazy_load_threshold' => '3000',
    'enable_lazy_load_fadetime' => '0', 'enable_lazy_load_manual' => 'false',
    'pass_id_by_url' => '', 'include_scripts_in_footer' => 'false',
    'write_css_directly' => 'false', 'resize_on_element_resize' => '',
    'resize_on_element_resize_delay' => '250', 'add_css_class_parent' => 'false',
    'dynamic_url_parameter'  => '', 'auto_zoom'  => 'false',
    'enable_lazy_load_manual_element' => '',  'auto_zoom_by_ratio' => '',
    'enable_lazy_load_manual_element' => '', 'alternative_shortcode' => '',
    'iframe_redirect_url' => '', 'show_part_of_iframe_last_viewport_remove' => 'false',
    'show_iframe_as_layer' => 'false',
    'add_iframe_url_as_param' => 'false', 'add_iframe_url_as_param_prefix' => '',
    'reload_interval' => '', 'iframe_content_css' => '',
    'additional_js_file_iframe' => '', 'additional_css_file_iframe' => '', 
    'add_css_class_iframe' => 'false', 
    'iframe_zoom_ie8' => 'false', 'enable_lazy_load_reserve_space' => 'true',
    'hide_content_until_iframe_color' => '', 'use_zoom_absolute_fix' => 'false',
    'include_html' => '', 'enable_ios_mobile_scolling' => 'false',
    'sandbox' => '', 'show_iframe_as_layer_header_file' => '', 
    'show_iframe_as_layer_header_height' => '100', 'show_iframe_as_layer_header_position' => 'top',
    'resize_min_height' => '1', 'show_iframe_as_layer_full' => 'false',
    'demo' => 'false', 'show_part_of_iframe_zoom' => 'false',
    'external_height_workaround_delay' => '0',
    'add_document_domain' => 'false' , 'document_domain' => '',
    'multi_domain_enabled' => 'true', 'check_shortcode' => 'false',
    'use_post_message' => 'true', 'element_to_measure_offset' => '0',
    'data_post_message' => '', 'element_to_measure' => 'default',
    'show_iframe_as_layer_keep_content' => 'true','roles' => 'none',
    'parent_content_css' => '', 'include_scripts_in_content' => 'false',
    'debug_js' => 'false', 'title' => '',
    'modify_iframe_if_cookie' => 'false', 'allow' => ''
    );
    
    // load the config
    if (isset($ai_settings_file)) {
        require_once dirname(__FILE__) . '/' . $ai_settings_file;
    } else {
        require_once dirname(__FILE__) . '/standalone-advanced-iframe-settings.php';
    }
    
    
    if ( !function_exists( 'esc_html' ) ) {
      function esc_html( $html, $char_set = 'UTF-8' ) {
        if ( empty( $html ) && $html != '0' ) {
            return '';
        }
        $html = (string) $html;
        $html = htmlspecialchars( $html, ENT_QUOTES, $char_set );
        return $html;
      }
    }
    
    if ( !function_exists( 'site_url' ) ) {
      function site_url() {
        global $site_url; 
        return $site_url;
      }
    } 
    
    if ( !function_exists( 'home_url' ) ) {
      function home_url() {
        $domain = '';
        if (isset($_SERVER['HTTP_HOST'])) {
            $domain = $_SERVER['HTTP_HOST'];
        } else if (isset($_SERVER['SERVER_NAME'])) {
            $domain = $_SERVER['SERVER_NAME'];
        }
        $port = strpos($domain, ':');
        if ($port !== false) $domain = substr($domain, 0, $port);
        return $domain;
      }
    } 
     
    if ( !function_exists( 'plugins_url' ) ) {
      function plugins_url() {
        global $site_url; 
          return $site_url;
        }
    }  
    
    if ( !function_exists( 'esc_js' ) ) {
      function esc_js($value) {
        return esc_html($value);
      }
    } 
    
    if ( !function_exists( 'get_absolute_path' ) ) {
      function get_absolute_path() {
        $domain =  home_url();
        // Get the path to the file
        $path = substr(dirname(__FILE__), strlen($_SERVER["DOCUMENT_ROOT"]));
        $apath = "//" . $domain . $path;
        $apath = str_replace('\\', '/' , $apath ); 
        return  $apath . '/..';
      }    
    }
    
    if ( !function_exists( 'plugin_dir_url' ) ) {
      function plugin_dir_url($file) {
        global $site_url;
          return $site_url . '/';
        }
      }

  // set the defaults url
  if (!isset($site_url)) {
    $site_url = get_absolute_path();   
  }
  
   require_once dirname(__FILE__) . '/../advanced-iframe.php';
 
  //  setup new instance of plugin if not standalone
  if (class_exists("advancediFrame")) {
      $cons_advancediFrame = new advancediFrame();
      echo $cons_advancediFrame->do_iframe_script(null,null);
  }

?>