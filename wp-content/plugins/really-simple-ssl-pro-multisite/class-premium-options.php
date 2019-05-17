<?php
defined('ABSPATH') or die("you do not have acces to this page!");
class rsssl_premium_options {
  private static $_this;
  //enter previous version
  private $required_version = "2.5.12";
  public $has_http_redirect=false;

    function __construct() {
  if ( isset( self::$_this ) )
      wp_die( sprintf( __( '%s is a singleton class and you cannot create a second instance.','really-simple-ssl-pro' ), get_class( $this ) ) );

  self::$_this = $this;

  add_action('plugins_loaded', array(&$this, 'load_translation'),20);

  add_action("update_option_rlrsssl_options", array($this, "update_hsts_no_apache"), 10,3);
  add_action("update_option_rlrsssl_options", array($this, "insert_hsts_header_in_htaccess"), 20,3);
  add_action("update_option_rlrsssl_options", array($this, "insert_upgrade_insecure_content_header_in_htaccess"), 20,3);


    //add_action('admin_init', array($this, 'add_hsts_option'),50);
  add_action('wp_loaded', array($this, 'admin_mixed_content_fixer'), 1);
  add_action('wp_loaded', array($this, 'change_notices_free'), 1);
  add_action('admin_init', array($this, 'add_pro_settings'),60);

  add_action('admin_init', array($this, 'insert_secure_cookie_settings'), 70);
  add_action('admin_init', array($this, 'maybe_remove_secure_cookie_settings'), 80);

  add_action("admin_notices", array($this, 'show_notice_redirect_to_http'), 30);


    //add_action('admin_init', array($this, 'add_pro_settings'),60);
  $plugin = rsssl_pro_plugin ;
  add_filter("plugin_action_links_$plugin", array($this,'plugin_settings_link'));
  //add_filter("rsssl_htaccess_output", array($this, "htaccess_bypass_redirect"));

  register_deactivation_hook(rsssl_pro_plugin_file, array($this,'deactivate') );
}

static function this() {
  return self::$_this;
}

public function deactivate(){
  $this->remove_HSTS();
  $this->remove_secure_cookie_settings();
}

public function load_translation() {

    $success = load_plugin_textdomain('really-simple-ssl-pro', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
}

public function check_http_redirect(){
    if (!RSSSL()->really_simple_ssl->ssl_enabled) {
       $this->has_http_redirect = $this->redirect_to_http();
    } else {
       $this->has_http_redirect = false;
    }
}

public function change_notices_free(){
  remove_action('rsssl_activation_notice', array(RSSSL()->really_simple_ssl, 'show_pro'), 20);
  remove_action('rsssl_configuration_page', array(RSSSL()->really_simple_ssl, 'configuration_page_more'),10);

  add_action('rsssl_configuration_page', array($this, 'configuration_page_more'), 10);
  add_action('rsssl_activation_notice_inner' , array($this, 'show_scan_buttons_before_activation'), 20);

}

/*
    Activate the mixed content fixer on the admin when enabled.
*/

public function admin_mixed_content_fixer(){

  if (is_admin() && is_ssl() && is_multisite() && RSSSL()->rsssl_multisite->mixed_content_admin) {
    RSSSL()->rsssl_mixed_content_fixer->fix_mixed_content();
  }

}

public function section_text(){

}


public function options_validate($input){
  if ($input==1){
    $validated_input = 1;
  }else{
    $validated_input = "";
  }
  return $validated_input;

}

    /**
     *
     * Checks if a redirect to http:// is active to prevent redirect loop issues
     * Since 2.0.19
     * @access public
     *
     */

    public function redirect_to_http()
    {
        $detected_redirect = get_transient('rsssl_redirect_to_http_check');
        if (!$detected_redirect){
            $url = site_url();
            if (!function_exists('curl_init')) {
                return false;
            }

            //CURLOPT_FOLLOWLOCATION might cause issues on php<5.4
            if (version_compare(PHP_VERSION, '5.4') < 0) {
                return false;
            }

            //Change the http:// domain to https:// to test for a possible redirect back to http://.
            $url = str_replace("http://", "https://", $url);

            //Follow the entire redirect chain.
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_NOBODY, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // follow redirects
            curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // set referer on redirect
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds
            curl_exec($ch);
            //$target is the endpoint of the redirect chain
            $target = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);

            //Check for http:// needle in target
            $http_needle = 'http://';

            $pos = strpos($target, $http_needle);

            if ($pos !== false) {
                //There is a redirect back to HTTP.
                $detected_redirect = 'http';
            } else {
                $detected_redirect = 'https';
            }
            set_transient('rsssl_redirect_to_http_check', 60);
        }

        if ($detected_redirect === 'http') {
            return true;
        } else {
            return false;
        }

    }


    public function show_notice_redirect_to_http()
    {
        if (!RSSSL()->really_simple_ssl->ssl_enabled && $this->has_http_redirect && !defined('rsssl_pp_version')) {

            $link_open = '<a target="_blank" href="https://really-simple-ssl.com/knowledge-base/my-website-is-in-a-redirect-loop/">';
            $link_close = '</a>';

            ?>
            <div id="warning" class="notice notice-error">
                <p>
                    <?php printf(__("Really Simple SSL has detected a redirect to HTTP. This can result in a redirect loop when activating SSL. See %sour article on redirect loops%s for the most common causes of a redirect back to http://. We strongly recommend to locate and disable this redirect before activating SSL.", "really-simple-ssl-pro"), $link_open, $link_close);

                    ?>
                </p>
            </div>
            <?php
        }
    }

/*
    if the server is not apache, we set the HSTS in another way.
*/

public function update_hsts_no_apache($oldvalue, $newvalue, $option){
  if (!is_admin()) return;
  if (!function_exists('RSSSL')) return;

  $options = $newvalue;
  $hsts = isset($options['hsts']) ? $options['hsts'] : FALSE;
  $hsts_no_apache = false;
  $not_using_htaccess = (!is_writable(RSSSL()->really_simple_ssl->ABSpath.".htaccess") || RSSSL()->really_simple_ssl->do_not_edit_htaccess) ? true : false;

  if (class_exists("rsssl_server")) {
    $apache = (RSSSL()->rsssl_server->get_server()=="apache");
    $contains_hsts = RSSSL()->really_simple_ssl->contains_hsts();
    if ($hsts && (!$apache || ($apache && $not_using_htaccess && !$contains_hsts ))) {
      $hsts_no_apache = true;
    } else {
      $hsts_no_apache = false;
    }

  }

  //Use this filter to override the automatic server detection.
  $hsts_no_apache = apply_filters("rsssl_hsts_no_apache", $hsts_no_apache);

  update_option("rsssl_hsts_no_apache", $hsts_no_apache);
}

/**
*     Check if PHP headers are used to set HSTS
*      @param void
*      @return boolean
*
*/

public function uses_php_header_for_hsts(){
  return get_option("rsssl_hsts_no_apache");
}

public function add_pro_settings(){
  if (!class_exists('REALLY_SIMPLE_SSL')) return;

  if(!is_multisite()) {
      add_settings_field('id_hsts', __("Turn HTTP Strict Transport Security on","really-simple-ssl-pro"), array($this,'get_option_hsts'), 'rlrsssl', 'rlrsssl_settings');

      if(RSSSL()->really_simple_ssl->hsts) {
      register_setting( 'rlrsssl_options', 'rsssl_hsts_preload', array($this,'options_validate') );
      add_settings_field('id_hsts_preload', __("Configure your site for the HSTS preload list","really-simple-ssl-pro"), array($this,'get_option_hsts_preload'), 'rlrsssl', 'rlrsssl_settings');
    }
  }

  //add_settings_section('section_rssslpp', __("Pro", "really-simple-ssl-pro"), array($this, "section_text"), 'rlrsssl');
  register_setting( 'rlrsssl_options', 'rsssl_admin_mixed_content_fixer', array($this,'options_validate') );
  register_setting( 'rlrsssl_options', 'rsssl_cert_expiration_warning', array($this,'options_validate') );
  register_setting( 'rlrsssl_options', 'rsssl_upgrade_insecure_requests_header', array($this,'options_validate') );


    if (!defined('rsssl_pp_version'))
        add_settings_field('id_upgrade_insecure_requests_header', __("Add header to force insecure requests over https","really-simple-ssl-pro"), array($this,'get_option_upgrade_insecure_requests_header'), 'rlrsssl', 'rlrsssl_settings');

  add_settings_field('id_cert_expiration_warning', __("Receive an email when your certificate is about to expire","really-simple-ssl-pro"), array($this,'get_option_cert_expiration_warning'), 'rlrsssl', 'rlrsssl_settings');
  add_settings_field('id_admin_mixed_content_fixer', __("Enable the mixed content fixer on the WordPress back-end","really-simple-ssl-pro"), array($this,'get_option_admin_mixed_content_fixer'), 'rlrsssl', 'rlrsssl_settings');

}

public function configuration_page_more() {
    if (!class_exists('REALLY_SIMPLE_SSL')) return;
    if (defined('rsssl_pp_version')) return;

    ?><table class="really-simple-ssl-table"><?php

    if (is_ssl() && get_option('rsssl_cert_expiration_warning') || (is_multisite() && RSSSL()->rsssl_multisite->cert_expiration_warning)) {

      $expiring  = rsssl_pro_almost_expired();
      $nice_date = rsssl_pro_expiration_date_nice();

      ?>
          <tr>
            <td>
              <?php echo ($expiring) ? RSSSL()->really_simple_ssl->img("error") : RSSSL()->really_simple_ssl->img("success");?>
            </td>
            <td>
            <?php if ($expiring) {?>
              <?php echo __("Your certificate needs to be renewed soon, it is valid to: ","really-simple-ssl-pro").$nice_date;?>
            <?php } else { ?>
              <?php echo __("Your certificate is valid to: ","really-simple-ssl-pro").$nice_date;?>
            <?php } ?>
              <?php echo __("(date updated once a week)","really-simple-ssl-pro");?>
            </td>
          </tr>
    <?php } ?>
    <?php if ((!is_multisite() || RSSSL()->rsssl_multisite->ssl_enabled_networkwide ) || !RSSSL()->rsssl_multisite->is_multisite_subfolder_install()) { ?>
    <tr>
      <td>
        <?php echo RSSSL()->really_simple_ssl->contains_hsts() ? RSSSL()->really_simple_ssl->img("success") :RSSSL()->really_simple_ssl->img("warning");?>
      </td>
      <td>
      <?php
      if(RSSSL()->really_simple_ssl->contains_hsts()) {
          _e("HTTP Strict Transport Security was set. ","really-simple-ssl-pro");
      }  elseif($this->uses_php_header_for_hsts()) {
          $link_start ='<a href="https://really-simple-ssl.com/knowledge-base/inserting-hsts-header-using-php/" target="_blank">';
          $link_close = "</a> ";
          echo sprintf(__("HTTP Strict Transport Security was set, but with PHP headers, %swhich might cause issues in combination in combination with caching.%s ", "really-simple-ssl-pro"),$link_start, $link_close );
      } else {
          $link_start ='<a href="https://en.wikipedia.org/wiki/HTTP_Strict_Transport_Security" target="_blank">';
          $link_close = "</a> ";
          echo sprintf(__("%sHTTP Strict Transport Security%s is not enabled ", "really-simple-ssl-pro"),$link_start, $link_close );
          ?>
          <a href="<?php echo admin_url('options-general.php?page=rlrsssl_really_simple_ssl&tab=settings');?>"><?php _e("Enable HSTS.","really-simple-ssl-pro");?></a>
       <?php  }
      ?>
    </td>
  </tr>
  <?php } ?>
  <?php
    $preload_enabled = get_option('rsssl_hsts_preload');
    if(RSSSL()->really_simple_ssl->hsts) {?>
  <tr>
    <td>
      <?php echo $preload_enabled ? RSSSL()->really_simple_ssl->img("success") :"-";?>
    </td>
    <td>
    <?php
      if($preload_enabled) {
          $link_start ='<a target="_blank" href="https://hstspreload.appspot.com/?domain='.$this->non_www_domain().'">';
          $link_close = "</a> ";
          echo sprintf(__("Your site has been configured for the HSTS preload list. If you have submitted your site, it will be preloaded. Click %shere%s to submit.", "really-simple-ssl-pro"),$link_start, $link_close );
      } else {
          $link_start ='<a target="_blank" href="https://hstspreload.appspot.com/?domain='.$this->non_www_domain().'">';
          $link_close = "</a> ";
          echo sprintf(__("Your site is not yet configured for the %sHSTS preload list.%s Read the documentation carefully before you do!", "really-simple-ssl-pro"),$link_start, $link_close );
      }
    ?>
  </td>
</tr>
<?php }
/*
      httponly configuration

*/
?>
<?php
if (!is_multisite() || (is_multisite() && RSSSL()->rsssl_multisite->ssl_enabled_networkwide) ) { ?>
<tr>
  <td>
    <?php echo $this->contains_secure_cookie_settings() ? RSSSL()->really_simple_ssl->img("success") : RSSSL()->really_simple_ssl->img("warning");?>
  </td>
  <td>
    <?php
        if ($this->contains_secure_cookie_settings()) {
          _e("Secure cookies set","really-simple-ssl")."&nbsp;";
        } else {
          _e('Secure cookie settings not enabled.',"really-simple-ssl");
        }
      ?>
    </td>
</tr>
<?php
}
    /*  Display the current settings for the admin mixed content. */
    $admin_mixed_content_fixer = get_option("rsssl_admin_mixed_content_fixer");
  ?>
  <tr>
    <td><?php echo $admin_mixed_content_fixer ? RSSSL()->really_simple_ssl->img("success") :"-";?></td>
    <td>
    <?php if ($admin_mixed_content_fixer){
      _e("You have the mixed content fixer activated on your admin panel.","really-simple-ssl-pro");
    } else{
      _e("The mixed content fixer is not active on the admin panel. Enable this feature only when you have mixed content on the admin panel.","really_simple_ssl-pro");
     }?>
    </td>
  </tr>
  </table>
    <?php
}

  /**
   * Insert option into settings form
   * @since  1.0.3
   *
   * @access public
   *
   */

  public function get_option_hsts() {

      ?>
      <label class="rsssl-switch">
          <input id="rlrsssl_options" name="rlrsssl_options[hsts]" size="40" value="1"
                 type="checkbox" <?php checked(1, RSSSL()->really_simple_ssl->hsts, true) ?> />
          <span class="rsssl-slider rsssl-round"></span>
      </label>
      <?php
      RSSSL()->rsssl_help->get_help_tip(__("HSTS, HTTP Strict Transport Security improves your security by forcing all your visitors to go to the SSL version of your website for at least a year.", "really-simple-ssl")." ".__("It is recommended to enable this feature as soon as your site is running smoothly on SSL, as it improves your security.", "really-simple-ssl"));
  }

    /*
     * This header ensures that even remote content will be forced over https by the server.
     *
     *
     * */

    public function get_option_upgrade_insecure_requests_header() {

        $upgrade_insecure_requests_header = get_option('rsssl_upgrade_insecure_requests_header', true);
        $disabled = "";
        $comment = "";

        if (RSSSL()->really_simple_ssl->do_not_edit_htaccess || !is_writable(RSSSL()->really_simple_ssl->ABSpath.".htaccess")){
            $disabled = "disabled";
            $upgrade_insecure_requests_header = false;
            $comment = __( "The .htaccess file is not writable. Give 644 writing permissions to enable this option.", "really-simple-ssl" );
        }

        if(is_multisite() && !RSSSL()->rsssl_multisite->ssl_enabled_networkwide){
            $disabled = "disabled";
            $upgrade_insecure_requests_header = false;
            $comment = __( "This option is only available if all sites on the network are on SSL.", "really-simple-ssl" );
        }

        //actual .htaccess file contents overrides all
        if ($this->contains_upgrade_insecure_content_header()){
            $upgrade_insecure_requests_header = true;
        }

        ?>
        <label class="rsssl-switch">
            <input id="rlrsssl_options" name="rsssl_upgrade_insecure_requests_header" size="40" value="1"
                   type="checkbox" <?php checked(1, $upgrade_insecure_requests_header, true) ?> />
            <span class="rsssl-slider rsssl-round"></span>
        </label>
        <?php
        RSSSL()->rsssl_help->get_help_tip(
            __("Disable this option if you need certain requests over http, and use the mixed content fixer filter to exclude certain requests from the mixed content filter.", "really-simple-ssl-pro")
        );
        echo $comment;
    }

  public function get_option_cert_expiration_warning() {

    $cert_expiration_warning = get_option('rsssl_cert_expiration_warning');
    $disabled = "";
    $comment = "";

    if (is_multisite() && RSSSL()->rsssl_multisite->cert_expiration_warning) {
      $disabled = "disabled";
      $cert_expiration_warning = TRUE;
      $comment = __( "This option is enabled on the network menu.", "really-simple-ssl" );
    }

      ?>
      <label class="rsssl-switch">
          <input id="rlrsssl_options" name="rsssl_cert_expiration_warning" size="40" value="1"
                 type="checkbox" <?php checked(1, $cert_expiration_warning, true) ?> />
          <span class="rsssl-slider rsssl-round"></span>
      </label>
      <?php
      RSSSL()->rsssl_help->get_help_tip(
        __("If your hosting company renews the certificate for you, you probably don't need to enable this setting.", "really-simple-ssl-pro")." ".
        __("If your certificate expires, your site goes offline. Uptime robots don't alert you when this happens.", "really-simple-ssl-pro")." ".
        __("If you enable this option you will receive an email when your certificate is about to expire within 2 weeks.", "really-simple-ssl-pro")
    );
    echo $comment;
  }

  public function get_option_admin_mixed_content_fixer() {
    $admin_mixed_content_fixer = get_option('rsssl_admin_mixed_content_fixer');
    $disabled = "";
    $comment = "";

    if (is_multisite() && RSSSL()->rsssl_multisite->mixed_content_admin) {
      $disabled = "disabled";
      $admin_mixed_content_fixer = TRUE;
      $comment = __( "This option is enabled on the network menu.", "really-simple-ssl" );
    }

      ?>
      <label class="rsssl-switch">
          <input id="rlrsssl_options" name="rsssl_admin_mixed_content_fixer" size="40" value="1"
                 type="checkbox" <?php checked(1, $admin_mixed_content_fixer, true) ?> />
          <span class="rsssl-slider rsssl-round"></span>
      </label>
      <?php
      RSSSL()->rsssl_help->get_help_tip(__("Use this option if you do not have the green lock in the WordPress admin.", "really-simple-ssl-pro"));
    echo $comment;
  }


  public function get_option_hsts_preload() {
    $enabled = get_option('rsssl_hsts_preload');

      ?>
      <label class="rsssl-switch">
          <input id="rlrsssl_options" name="rsssl_hsts_preload" size="40" value="1"
                 type="checkbox" <?php checked(1, $enabled, true) ?> />
          <span class="rsssl-slider rsssl-round"></span>
      </label>
      <?php
      RSSSL()->rsssl_help->get_help_tip(
        __("The preload list offers even more security, as browsers already will know to load your site over SSL before a user ever visits it. This is very hard to undo!", "really-simple-ssl-pro")." ".
        __("Please note that all subdomains, and both www and non-www domain need to be https!", "really-simple-ssl-pro")." ".
        __('Before submitting, please read the information on hstspreload.appspot.com', "really-simple-ssl-pro")
    );
      $link_start ='<a target="_blank" href="https://hstspreload.appspot.com/?domain='.$this->non_www_domain().'">';
      $link_close = "</a> ";
      echo sprintf(__("After enabling this option, you have to %ssubmit%s your site", "really-simple-ssl-pro"),$link_start, $link_close );
  }



  /*

    Get the non www domain.

  */

  public function non_www_domain(){
    $domain = get_home_url();
    $domain = str_replace(array("https://", "http://", "https://www.", "http://www.", "www."), "", $domain);
    return $domain;
  }


/**
 * Add settings link on plugins overview page
 *
 * @since  1.0.27
 *
 * @access public
 *
 */

public function plugin_settings_link($links) {

  $settings_link = '<a href="options-general.php?page=rlrsssl_really_simple_ssl">'.__("Settings","really-simple-ssl").'</a>';
  array_unshift($links, $settings_link);
  return $links;

}

/*

    Replace the generic redirect with a redirect to the homeurl, so it will always redirect directly to
    the homeurl, not using to redirects

    As it redirects hardcoded to the home_url, thus including www or not www, this is not suitable for multisite.

*/


// public function htaccess_bypass_redirect($rule){
//
//   if (!is_multisite()) {
//     $parse_url = parse_url(home_url());
//     $host = $parse_url["host"];
//     $current_redirect = "RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI}";
//     $bypass_redirect = "RewriteRule ^(.*)$ https://". $host ."%{REQUEST_URI}";
//     $rule = str_replace($current_redirect, $bypass_redirect, $rule);
//   }
//
//   return $rule;
// }

public function insert_hsts_header_in_htaccess($oldvalue, $newvalue, $option){

    if (!current_user_can('manage_options')) return;

    //does it exist?
    if (!file_exists(RSSSL()->really_simple_ssl->ABSpath.".htaccess")) return;

    //check if editing is blocked.
    if (RSSSL()->really_simple_ssl->do_not_edit_htaccess) return;

    $hsts = RSSSL()->really_simple_ssl->hsts;
    $hsts_preload = get_option("rsssl_hsts_preload");

    //on multisite, always use the network setting.
    if (is_multisite()) {

      $hsts = RSSSL()->rsssl_multisite->hsts;

      $options = get_site_option('rlrsssl_network_options');
      if (RSSSL()->rsssl_multisite->ssl_enabled_networkwide) $hsts_preload = isset($options["hsts_preload"]) ? $options["hsts_preload"] : false;
    }

    $htaccess = file_get_contents(RSSSL()->really_simple_ssl->ABSpath.".htaccess");
    if (!is_writable(RSSSL()->really_simple_ssl->ABSpath.".htaccess")) return;

    //remove current rules from file, if any.
    $htaccess = preg_replace("/#\s?BEGIN\s?Really_Simple_SSL_HSTS.*?#\s?END\s?Really_Simple_SSL_HSTS/s", "", $htaccess);
    $htaccess = preg_replace("/\n+/","\n", $htaccess);
    $rule = "";

    if ($hsts) {
      //owasp security best practice https://www.owasp.org/index.php/HTTP_Strict_Transport_Security
      $rule = "\n"."# BEGIN Really_Simple_SSL_HSTS"."\n";
      $rule .= "<IfModule mod_headers.c>"."\n";
      if ($hsts_preload){
        $rule .= 'Header always set Strict-Transport-Security: "max-age=63072000; includeSubDomains; preload" env=HTTPS'."\n";
      } else {
        $rule .= 'Header always set Strict-Transport-Security: "max-age=31536000" env=HTTPS'."\n";
      }
      $rule .= "</IfModule>"."\n";
      $rule .= "# END Really_Simple_SSL_HSTS"."\n";
      $rule = preg_replace("/\n+/","\n", $rule);
    }

    $wptag = "# BEGIN WordPress";
    if (strpos($htaccess, $wptag)!==false) {
        $htaccess = str_replace($wptag, $rule.$wptag, $htaccess);
    } else {
        $htaccess = $htaccess.$rule;
    }

    file_put_contents(RSSSL()->really_simple_ssl->ABSpath.".htaccess", $htaccess);

}

    public function show_scan_buttons_before_activation()
    {

        $result = RSSSL_PRO()->rsssl_scan->scan_completed_no_errors();

        if ($result == "COMPLETED") { ?>
            <div class="rsssl-scan-text-in-activate-notice"><?php _e("You finished a scan without errors.", "really-simple-ssl-pro") ?></div>
        <?php } elseif ($result == "NEVER") { ?>
            <div class="rsssl-scan-text-in-activate-notice">
                <p>
                    <?php
                    $link_start = '<a href="options-general.php?page=rlrsssl_really_simple_ssl&tab=scan">';
                    $link_close = "</a> ";
                    echo sprintf(__("No scan completed yet. Before migrating to SSL, you should do a %sscan%s", "really-simple-ssl-pro"), $link_start, $link_close);
                    ?>
                </p>
            </div>
        <?php } else { ?>
            <div class="rsssl-scan-text-in-activate-notice">
                <p><?php _e("Previous scan completed with issues", "really-simple-ssl-pro"); ?></p></div>
        <?php } ?>
        <div class="rsssl-scan-button"
        <form action="" method="post">
        <?php if ($result != "NEVER") {
        $link_start = '<a href="options-general.php?page=rlrsssl_really_simple_ssl&tab=scan" class=\'button button-primary\'>';
        $link_close = "</a> ";
        echo sprintf(__("%sScan again%s", "really-simple-ssl-pro"), $link_start, $link_close);
    } else {
        $link_start = '<a href="options-general.php?page=rlrsssl_really_simple_ssl&tab=scan" class=\'button button-primary\'>';
        $link_close = "</a> ";
        echo sprintf(__("%sScan for issues%s", "really-simple-ssl-pro"), $link_start, $link_close);
        wp_nonce_field('rsssl_nonce', 'rsssl_nonce'); ?>
        </form>
        </div>
        <?php
    }
    }


/**
 * removes the added redirect to https rules to the .htaccess file.
 *
 * @since  2.0
 *
 * @access public
 *
 */

public function remove_HSTS() {
    $abspath = RSSSL()->really_simple_ssl->ABSpath;
    if(file_exists($abspath.".htaccess") && is_writable($abspath.".htaccess")){
      $htaccess = file_get_contents($abspath.".htaccess");

      $htaccess = preg_replace("/#\s?BEGIN\s?Really_Simple_SSL_HSTS.*?#\s?END\s?Really_Simple_SSL_HSTS/s", "", $htaccess);
      $htaccess = preg_replace("/\n+/","\n", $htaccess);

      file_put_contents($abspath.".htaccess", $htaccess);
    }
}

public function insert_secure_cookie_settings(){
  if (!current_user_can("activate_plugins")) return;

  //do not set on per page installations
  if (defined('rsssl_pp_version')) return;

  //only if this site has SSL activated.
  if (!RSSSL()->really_simple_ssl->ssl_enabled) return;

  //if multisite, only on network wide activated setups
  if(is_multisite() && !RSSSL()->rsssl_multisite->ssl_enabled_networkwide) return;

  $wpconfig_path = RSSSL()->really_simple_ssl->find_wp_config_path();
  if (empty($wpconfig_path)) return;
  $wpconfig = file_get_contents($wpconfig_path);

  //only if cookie settings were not inserted yet
  if ((strpos($wpconfig, "//Begin Really Simple SSL session cookie settings")===FALSE) && (strpos($wpconfig, "cookie_httponly")===FALSE) ) {
    if (is_writable($wpconfig_path)) {
      $rule  = "\n"."//Begin Really Simple SSL session cookie settings"."\n";
      $rule .= "@ini_set('session.cookie_httponly', true);"."\n";
      $rule .= "@ini_set('session.cookie_secure', true);"."\n";
      $rule .= "@ini_set('session.use_only_cookies', true);"."\n";
      $rule .= "//END Really Simple SSL"."\n";

      $insert_after = "<?php";
      $pos = strpos($wpconfig, $insert_after);
      if ($pos !== false) {
          $wpconfig = substr_replace($wpconfig,$rule,$pos+1+strlen($insert_after),0);
      }

      file_put_contents($wpconfig_path, $wpconfig);
    }
  }
}

public function maybe_remove_secure_cookie_settings(){
  if (!current_user_can("activate_plugins")) return;

  if (is_multisite() && (!RSSSL()->rsssl_multisite->ssl_enabled_networkwide || !RSSSL()->really_simple_ssl->ssl_enabled)) {
    $this->remove_secure_cookie_settings();
  }
}

/**
 * remove secure cookie settings
 *
 * @since  2.1
 *
 * @access public
 *
 */

public function remove_secure_cookie_settings() {
    if (!current_user_can("activate_plugins")) return;

    if (!$this->contains_secure_cookie_settings()) return;

    $wpconfig_path = RSSSL()->really_simple_ssl->find_wp_config_path();
    if (!empty($wpconfig_path)) {

        $wpconfig = file_get_contents($wpconfig_path);
        $wpconfig = preg_replace("/\/\/Begin\s?Really\s?Simple\s?SSL\s?session\s?cookie\s?settings.*?\/\/END\s?Really\s?Simple\s?SSL/s", "", $wpconfig);
        $wpconfig = preg_replace("/\n+/","\n", $wpconfig);
        file_put_contents($wpconfig_path, $wpconfig);
    }
}

//Show notice for the cookie settings

public function show_notice_wpconfig_not_writable(){
  if (!current_user_can("activate_plugins")) return;

  //only if this site has SSL activated.
  if (!RSSSL()->really_simple_ssl->ssl_enabled) return;

  //if multisite, only on network wide activated setups
  if(is_multisite() && !RSSSL()->rsssl_multisite->ssl_enabled_networkwide) return;

  //on multistie, only show this message on the network admin.
  if (is_multisite() && !is_network_admin()) return;

  //do not set on per page installations
  if (defined('rsssl_pp_version')) return;

  $wpconfig_path = RSSSL()->really_simple_ssl->find_wp_config_path();
  if (empty($wpconfig_path)) return;

  $wpconfig = file_get_contents($wpconfig_path);
  if ((strpos($wpconfig, "//Begin Really Simple SSL session cookie settings")===FALSE) && (!is_writable($wpconfig_path)) && (strpos($wpconfig, "cookie_httponly")===FALSE)) {

    ?>
      <div id="message" class="error fade notice">
      <h1><?php echo __("Could not insert httponly secure cookie settings.","really-simple-ssl-pro");?></h1>

      <?php

        ?>
          <p><?php echo __("To set the httponly secure cookie settings, your wp-config.php has to be edited, but the file is not writable.","really-simple-ssl-pro");?></p>
          <p><?php echo __("Add the following lines of code to your wp-config.php.","really-simple-ssl-pro");?>

        <br><br><code>
            //Begin Really Simple SSL session cookie settings <br>
            &nbsp;&nbsp;@ini_set('session.cookie_httponly', true); <br>
            &nbsp;&nbsp;@ini_set('session.cookie_secure', true); <br>
            &nbsp;&nbsp;@ini_set('session.use_only_cookies', true); <br>
            //END Really Simple SSL cookie settings <br>
        </code><br>
        </p>
        <p><?php echo __("Or set your wp-config.php to writable and reload this page.", "really-simple-ssl-pro");?></p>
      </div>
  <?php
    }
}

public function contains_secure_cookie_settings() {
  $wpconfig_path = RSSSL()->really_simple_ssl->find_wp_config_path();

  if (!$wpconfig_path) return false;

  $wpconfig = file_get_contents($wpconfig_path);
  if ( (strpos($wpconfig, "//Begin Really Simple SSL session cookie settings")===FALSE) && (strpos($wpconfig, "cookie_httponly")===FALSE) ) {
    return false;
  }

  return true;
}

    /*
     *
     * Inserts header to upgrade insecure links.
     * Does not insert in case of the per page plugin
     *
     *
     * */


    public function insert_upgrade_insecure_content_header_in_htaccess($oldvalue, $newvalue, $option){
        if (!current_user_can("activate_plugins")) return;

        //not for per page
        if (defined('rsssl_pp_version') ) return;

        if (!current_user_can("activate_plugins")) return;

        //does it exist?
        if (!file_exists(RSSSL()->really_simple_ssl->ABSpath.".htaccess")) return;

        //check if editing is blocked.
        if (RSSSL()->really_simple_ssl->do_not_edit_htaccess) return;

        //not if multisite and not networkwide
        if (is_multisite() && !RSSSL()->rsssl_multisite->ssl_enabled_networkwide) return;

        $htaccess = file_get_contents(RSSSL()->really_simple_ssl->ABSpath.".htaccess");
        if (!is_writable(RSSSL()->really_simple_ssl->ABSpath.".htaccess")) return;

        //remove current rules from file, if any.
        $htaccess = preg_replace("/#\s?BEGIN\s?Really_Simple_SSL_UPGRADE_INSECURE_REQUESTS.*?#\s?END\s?Really_Simple_SSL_UPGRADE_INSECURE_REQUESTS/s", "", $htaccess);
        $htaccess = preg_replace("/\n+/","\n", $htaccess);

        $rule = "\n"."# BEGIN Really_Simple_SSL_UPGRADE_INSECURE_REQUESTS"."\n";
        $rule .= "<IfModule mod_headers.c>"."\n";
        $rule .= 'Header always set Content-Security-Policy "upgrade-insecure-requests;"'."\n";
        $rule .= "</IfModule>"."\n";
        $rule .= "# END Really_Simple_SSL_UPGRADE_INSECURE_REQUESTS"."\n";
        $rule = preg_replace("/\n+/","\n", $rule);

        //if this is disabled, remove the rule
        if (!get_option('rsssl_upgrade_insecure_requests_header', true)) $rule = "";

        $wptag = "# BEGIN WordPress";
        if (strpos($htaccess, $wptag)!==false) {
            $htaccess = str_replace($wptag, $rule.$wptag, $htaccess);
        } else {
            $htaccess = $htaccess.$rule;
        }

        file_put_contents(RSSSL()->really_simple_ssl->ABSpath.".htaccess", $htaccess);

    }



    /**
     * removes the added redirect to https rules to the .htaccess file.
     *
     * @since  2.0
     *
     * @access public
     *
     */

    public function remove_upgrade_insecure_content_header() {
        if (!current_user_can("activate_plugins")) return;
        $abspath = RSSSL()->really_simple_ssl->ABSpath;
        if(file_exists($abspath.".htaccess") && is_writable($abspath.".htaccess")){
            $htaccess = file_get_contents($abspath.".htaccess");

            $htaccess = preg_replace("/#\s?BEGIN\s?Really_Simple_SSL_UPGRADE_INSECURE_REQUESTS.*?#\s?END\s?Really_Simple_SSL_UPGRADE_INSECURE_REQUESTS/s", "", $htaccess);
            $htaccess = preg_replace("/\n+/","\n", $htaccess);

            file_put_contents($abspath.".htaccess", $htaccess);
        }
    }

    /**
     * Checks if the contains_upgrade_insecure_content_header rule is already in the htaccess file
     *
     * @since  2.1
     *
     * @access public
     *
     */

    public function contains_upgrade_insecure_content_header() {
        if (file_exists(RSSSL()->really_simple_ssl->ABSpath.".htaccess")) {
            $htaccess = file_get_contents(RSSSL()->really_simple_ssl->ABSpath.".htaccess");

            preg_match("/upgrade-insecure-requests/", $htaccess, $check);
            if(count($check) === 0){
                return false;
            } else {
                return true;
            }
        }

        return false;
    }

}//class closure
