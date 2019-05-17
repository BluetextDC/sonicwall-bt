<?php
/* 100% match ms */
defined('ABSPATH') or die("you do not have acces to this page!");

if (!class_exists('rsssl_pro_multisite')) {

    class rsssl_pro_multisite
    {
        private static $_this;
        public $hsts_preload;


        function __construct()
        {
            if (isset(self::$_this))
                wp_die(sprintf(__('%s is a singleton class and you cannot create a second instance.', 'really-simple-ssl'), get_class($this)));

            self::$_this = $this;
            if (is_network_admin()) {

                add_action('admin_init', array($this, 'add_pro_settings'), 60);
                //add_action('admin_init', array($this, 'process_switch'), 70);
                add_action("rsssl_show_network_tab_sites", array($this, "show_sites_tab"));
                add_filter('rsssl_network_tabs', array($this, 'add_sites_tab'));
                add_action('network_admin_edit_rsssl_update_network_settings', array($this, 'update_network_options'), 1); //should run before the main plugin rsssl

                add_action("network_admin_notices", array($this, 'show_nginx_hsts_notice'), 20);
                //Nessecary to dismiss the nginx notice
                add_action('admin_print_footer_scripts', array($this, 'insert_nginx_dismiss_success'));

            }
            add_action('wp_ajax_dismiss_success_message_nginx', array($this, 'dismiss_nginx_message_callback'));

            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

            add_action('admin_print_footer_scripts-settings_page_really-simple-ssl', array($this, 'inline_scripts'));
            add_action('wp_ajax_rsssl_site_switch', array($this, 'ajax_site_switch'));
            //update_site_option('rsssl_nginx_message_shown', false);
        }

        static function this()
        {
            return self::$_this;
        }

    public function inline_scripts()
    {
    ?>
        <script>
            jQuery(document).ready(function($) {

                function rsssl_switch(row, action){
                    var img = row.find('.rsssl-icons');
                    var src = img.attr('src');
                    var btn = row.find('button');
                    var name = row.find('.rsssl-name').html();
                    if (action==='deactivate'){
                        src = src.replace('check', 'cross');
                        btn.html('activate');
                        name = name.replace('https://', 'http://');
                        status = '';
                    }

                    if (action === 'activate'){
                        src = src.replace('cross', 'check');
                        btn.html('deactivate');
                        name = name.replace('http://', 'https://');
                        status = 'SSL';
                    }
                    row.find('.rsssl-name').html(name);
                    img.attr('src', src);
                    row.find('.rsssl-status').html(status);
                }

                //handle ajax site switch
                $(document).on("click", ".rsssl-switch", function () {
                    var btn = $(this);
                    var blog_id = btn.data('blog_id');
                    var nonce = btn.data('nonce');
                    var switch_action = btn.data('action');
                    $.post(
                        '<?php echo admin_url( 'admin-ajax.php')?>',
                        {
                            blog_id: blog_id,
                            nonce: nonce,
                            switch_action: switch_action,
                            action : 'rsssl_site_switch'
                        },
                        function (response) {
                            var row = btn.closest('tr');
                            rsssl_switch(row, switch_action);
                        }
                    );

                });

                $('#rsssl_sites_overview').DataTable({
                    language: {
                        search: "<?php _e("Search", "really-simple-ssl-pro")?>&nbsp;:",
                        sLengthMenu: "<?php printf(__("Show %s results", "really-simple-ssl-pro"), '_MENU_')?>",
                        sZeroRecords: "<?php _e("No results found", "really-simple-ssl-pro")?>",
                        sInfo:  "<?php printf(__("%s to %s of %s results", "really-simple-ssl-pro"), '_START_', '_END_', '_TOTAL_')?>",
                        sInfoEmpty: "<?php _e("No results to show", "really-simple-ssl-pro")?>",
                        sInfoFiltered: "<?php printf(__("(filtered from %s results)", "really-simple-ssl-pro"), '_MAX_')?>",
                        InfoPostFix: "",
                        EmptyTable: "<?php _e("No results found in the table", "really-simple-ssl-pro")?>",
                        InfoThousands: ".",
                        paginate: {
                            first: "<?php _e("First", "really-simple-ssl-pro")?>",
                            previous: "<?php _e("Previous", "really-simple-ssl-pro")?>",
                            next: "<?php _e("Next", "really-simple-ssl-pro")?>",
                            last: "<?php _e("Last", "really-simple-ssl-pro")?>",
                        },
                    },
                });
            });
        </script>
        <?php
    }

    public function ajax_site_switch(){
        $error = false;
        $response = json_encode(array('success' => false));
        if ( isset($_POST['action']) && isset($_POST["blog_id"]) && isset($_POST["nonce"]) && isset($_POST["nonce"]) && wp_verify_nonce($_POST["nonce"], "rsssl_switch_blog")) {
            if (!current_user_can("manage_network_plugins")) $error = true;
            if (($_POST['switch_action'] !== "activate") && ($_POST['switch_action'] != "deactivate")) $error =true;

            $action = $_POST['switch_action'];
            $blog_id = intval($_POST['blog_id']);

            if (!$error) {
                switch_to_blog($blog_id);
                if ($action == "deactivate") {
                    RSSSL()->really_simple_ssl->deactivate_ssl();
                } else {
                    RSSSL()->really_simple_ssl->activate_ssl();
                }
                restore_current_blog();
            }

            $response = json_encode(array('success' => !$error));
        }
        header("Content-Type: application/json");
        echo $response;
        exit;
    }

    public
    function enqueue_scripts($hook)
    {

        if ($hook!='settings_page_really-simple-ssl') return;
        wp_register_style('rsssl-pro-datatables', rsssl_pro_url . 'css/datatables.min.css', "", rsssl_pro_ms_version);
        wp_enqueue_style('rsssl-pro-datatables');
        wp_register_style('rsssl-pro-table-css', rsssl_pro_url . 'css/jquery-table.css', "", rsssl_pro_ms_version);
        wp_enqueue_style('rsssl-pro-table-css');

       wp_enqueue_script('rsssl-pro-datatables', rsssl_pro_url . "js/datatables.min.js", array('jquery'), rsssl_pro_ms_version, false);
       // wp_enqueue_script('rsssl-pro-datatables', "//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js", array('jquery'), rsssl_pro_ms_version, false);


        //
        //wp_enqueue_script('rsssl-pro-admin', rsssl_pro_url . "js/admin-multisite.js", array(), rsssl_pro_ms_version, true);
//      wp_localize_script(
//          'rsssl-pro-datatables',
//          'rsssl-pro',
//      );

    }

    /**
     *      Save network settings
     */

    public
    function update_network_options()
    {

        check_admin_referer('rsssl_network_options-options');

        if (isset($_POST["rlrsssl_network_options"])) {
            $saved_options = array_map(array($this, "sanitize_boolean"), $_POST["rlrsssl_network_options"]);
            $db_options = get_site_option("rlrsssl_network_options");
            if (!is_array($saved_options)) $saved_options = array();

            $hsts_preload_new = isset($db_options["hsts_preload"]) ? $db_options["hsts_preload"] : FALSE;
            $hsts_preload_old = isset($saved_options['hsts_preload']) ? $saved_options['hsts_preload'] : FALSE;

            if ($hsts_preload_new != $hsts_preload_old) update_site_option("rsssl_nginx_message_shown", false);

            $hsts_new = isset($db_options['hsts']) ? $db_options['hsts'] : FALSE;
            $hsts_old = isset($saved_options['hsts']) ? $saved_options['hsts'] : FALSE;

            if ($hsts_new != $hsts_old) update_site_option("rsssl_nginx_message_shown", false);

            if (isset($saved_options["hsts_preload"])) $db_options["hsts_preload"] = $saved_options["hsts_preload"];

            update_site_option("rlrsssl_network_options", $db_options);

        }

        //primary plugin will take care of redirects
    }


    // public function load_options(){
    //   $options = get_site_option('rlrsssl_network_options');
    //   $this->hsts_preload = isset($options["hsts_preload"]) ? $options["hsts_preload"] : false;
    // }


//    public
//    function process_switch()
//    {
//        if (!current_user_can("manage_network_plugins")) return;
//
//        if (!isset($_GET['rsssl_switch_blog_nonce']) || !wp_verify_nonce($_GET['rsssl_switch_blog_nonce'], 'rsssl_switch_blog')) return;
//
//        if (isset($_GET['blog_id']) && isset($_GET['action'])) {
//            $action = false;
//            if ($_GET['action'] == "activate") $action = "activate";
//            if ($_GET['action'] == "deactivate") $action = "deactivate";
//
//            $blog_id = intval($_GET['blog_id']);
//            if (!$action) return;
//
//            switch_to_blog($blog_id);
//
//            if ($action == "deactivate") {
//                RSSSL()->really_simple_ssl->deactivate_ssl();
//            } else {
//                RSSSL()->really_simple_ssl->activate_ssl();
//            }
//            restore_current_blog();
//        }
//    }


    public
    function add_sites_tab($tabs)
    {
        $tabs['sites'] = __("Sites overview", "really-simple-ssl-pro");
        return $tabs;
    }


    public
    function show_sites_tab()
    {
        ?>
        <p><?php _e("Here you can see the current status of the sites in your network.", "really-simple-ssl-pro") ?><p>
        <?php

        global $wp_version;
        if ($wp_version < 4.6) {
            echo "this feature needs WordPress 4.6 or higher";
            return;
        }

        $html = "";
        $enabled = '<img class="rsssl-icons" src="' . rsssl_pro_url . "img/check-icon.png" . '">';
        $disabled = '<img class="rsssl-icons" src="' . rsssl_pro_url . "img/cross-icon.png" . '">';

        $args = array(
            'number' => get_blog_count(),//$sites_per_page,
            //'offset' => $p * $sites_per_page,
        );

        $sites = get_sites($args);

        if (RSSSL()->rsssl_multisite->ssl_enabled_networkwide) {
            $snippet = '<tr><td>[ACTIVE]</td><td></td><td>[NAME]</td><td></td></tr>';
        } else {
            $snippet = '<tr><td>[ACTIVE]</td><td class="rsssl-status">[STATUS]</td><td class="rsssl-name">[NAME]</td><td><button class="rsssl-switch button" data-nonce="[NONCE]" data-blog_id="[BLOG_ID]" data-action="[ACTION]">[SWITCH]</button></td></tr>';
        }

        ?>

        <?php
        foreach ($sites as $site) {
            switch_to_blog($site->blog_id);

            //$site->blog_id, domain, path.
            $options = get_option('rlrsssl_options');
            if (isset($options)) {
                $ssl_enabled = isset($options['ssl_enabled']) ? $options['ssl_enabled'] : FALSE;
            }
            $active = $ssl_enabled ? $enabled : $disabled;
            $status = $ssl_enabled ? "SSL" : "";
            $action = $ssl_enabled ? "deactivate" : "activate";
            $switch = $ssl_enabled ? __("deactivate", "really-simple-ssl-pro") : __("activate", "really-simple-ssl-pro");
            //$url = wp_nonce_url(network_admin_url("settings.php?page=really-simple-ssl&tab=sites&p=" . $p . "&action=" . $action . "&blog_id=" . $site->blog_id), "rsssl_switch_blog", "rsssl_switch_blog_nonce");
            $nonce = wp_create_nonce("rsssl_switch_blog");
            $html .= str_replace(array("[STATUS]","[ACTIVE]", "[NAME]", "[BLOG_ID]","[ACTION]", "[SWITCH]", "[NONCE]"), array($status, $active, home_url(), $site->blog_id, $action, $switch, $nonce), $snippet);
            restore_current_blog(); //switches back to previous blog, not current, so we have to do it each loop

        }
        ?>
        <p>
        <table id="rsssl_sites_overview">
            <thead>
            <tr>
                <th></th>
                <th><?php _e("Status", "really-simple-ssl-pro") ?></th>
                <th><?php _e("Site", "really-simple-ssl-pro") ?></th>
                <th></th>
            </tr>
            </thead>
            <?php echo $html; ?>
        </table>
        </p>

        <?php
    }


    public
    function add_pro_settings()
    {
        if (!RSSSL()->rsssl_multisite->plugin_network_wide_active()) return;

        // register_setting( RSSSL()->rsssl_multisite->option_group, 'rsssl_options');
        // add_settings_section('rsssl_network_settings', __("Settings","really-simple-ssl"), array($this,'section_text'), RSSSL()->rsssl_multisite->page_slug);

        if (RSSSL()->really_simple_ssl->site_has_ssl) {
            add_settings_field('id_autoreplace_mixed_content', __("Auto replace mixed content", "really-simple-ssl"), array($this, 'get_option_autoreplace_mixed_content'), RSSSL()->rsssl_multisite->page_slug, 'rsssl_network_settings');
            add_settings_field('id_hide_menu_for_subsites', __("Hide menu for subsites", "really-simple-ssl"), array($this, 'get_option_hide_menu_for_subsites'), RSSSL()->rsssl_multisite->page_slug, 'rsssl_network_settings');

            add_settings_field('id_301_redirect', __("Enable WordPress 301 redirection to SSL for all SSL sites", "really-simple-ssl"), array($this, 'get_option_wp_redirect'), RSSSL()->rsssl_multisite->page_slug, 'rsssl_network_settings');
            add_settings_field('id_javascript_redirect', __("Enable javascript redirection to SSL", "really-simple-ssl"), array($this, 'get_option_javascript_redirect'), RSSSL()->rsssl_multisite->page_slug, 'rsssl_network_settings');
            add_settings_field('id_cert_expiration_warning', __("Receive an email when your certificate is about to expire", "really-simple-ssl"), array($this, 'get_option_cert_expiration_warning'), RSSSL()->rsssl_multisite->page_slug, 'rsssl_network_settings');
            add_settings_field('id_mixed_content_admin', __("Enable the mixed content fixer on the WordPress back-end", "really-simple-ssl"), array($this, 'get_option_mixed_content_admin'), RSSSL()->rsssl_multisite->page_slug, 'rsssl_network_settings');

            if (RSSSL()->rsssl_multisite->selected_networkwide_or_per_site && RSSSL()->rsssl_server->uses_htaccess()) {
                add_settings_field('id_htaccess_redirect', __("Enable htacces redirection to SSL on the network", "really-simple-ssl"), array($this, 'get_option_htaccess_redirect'), RSSSL()->rsssl_multisite->page_slug, 'rsssl_network_settings');
                add_settings_field('id_do_not_edit_htaccess', __("Stop editing the .htaccess file", "really-simple-ssl"), array($this, 'get_option_do_not_edit_htaccess'), RSSSL()->rsssl_multisite->page_slug, 'rsssl_network_settings');
            }

            if (RSSSL()->rsssl_multisite->ssl_enabled_networkwide || !RSSSL()->rsssl_multisite->is_multisite_subfolder_install()){
                add_settings_field('id_hsts', __("Turn HTTP Strict Transport Security on","really-simple-ssl"), array($this,'get_option_hsts'), RSSSL()->rsssl_multisite->page_slug, 'rsssl_network_settings');
            }

            if (RSSSL()->rsssl_multisite->ssl_enabled_networkwide) {

                if (RSSSL()->rsssl_multisite->hsts)
                    add_settings_field('id_hsts_preload', __("Configure your site for the HSTS preload list", "really-simple-ssl-pro"), array($this, 'get_option_hsts_preload'), RSSSL()->rsssl_multisite->page_slug, 'rsssl_network_settings');
            }
        }
    }


    public
    function get_option_htaccess_redirect()
    {
        ?>
        <label class="rsssl-switch">
            <input id="rlrsssl_options" name="rlrsssl_network_options[htaccess_redirect]" size="40" value="1"
                   type="checkbox" <?php checked(1, RSSSL()->rsssl_multisite->htaccess_redirect, true) ?> />
            <span class="rsssl-slider rsssl-round"></span>
        </label>
        <?php

        if (RSSSL()->rsssl_multisite->ssl_enabled_networkwide) {
            rsssl_help::this()->get_help_tip(__("Enable this if you want to redirect ALL websites to SSL using .htaccess", "really-simple-ssl"));
        } else {
            rsssl_help::this()->get_help_tip(__("Enable this if you want to redirect SSL websites using .htaccess. ", "really-simple-ssl"));
        }
    }

    public
    function get_option_wp_redirect()
    {
        ?>
        <label class="rsssl-switch">
            <input id="rlrsssl_options" name="rlrsssl_network_options[wp_redirect]" size="40" value="1"
                   type="checkbox" <?php checked(1, RSSSL()->rsssl_multisite->wp_redirect, true) ?> />
            <span class="rsssl-slider rsssl-round"></span>
        </label>
        <?php
        rsssl_help::this()->get_help_tip(__("Enable this if you want to use the internal WordPress 301 redirect for all SSL websites. Needed on NGINX servers, or if the .htaccess redirect cannot be used.", "really-simple-ssl"));

    }

    public
    function get_option_autoreplace_mixed_content()
    {
        ?>
        <label class="rsssl-switch">
            <input id="rlrsssl_options" name="rlrsssl_network_options[autoreplace_mixed_content]" size="40" value="1"
                   type="checkbox" <?php checked(1, RSSSL()->rsssl_multisite->autoreplace_mixed_content, true) ?> />
            <span class="rsssl-slider rsssl-round"></span>
        </label>
        <?php
        rsssl_help::this()->get_help_tip(__("Enable this if you want to automatically replace mixed content.", "really-simple-ssl"));
    }

    public
    function get_option_javascript_redirect()
    {
        ?>
        <label class="rsssl-switch">
            <input id="rlrsssl_options" name="rlrsssl_network_options[javascript_redirect]" size="40" value="1"
                   type="checkbox" <?php checked(1, RSSSL()->rsssl_multisite->javascript_redirect, true) ?> />
            <span class="rsssl-slider rsssl-round"></span>
        </label>
        <?php
        rsssl_help::this()->get_help_tip(__("Enable this if you want to enable javascript redirection.", "really-simple-ssl"));
    }

    public
    function get_option_hsts()
    {
        ?>
        <label class="rsssl-switch">
            <input id="rlrsssl_options" name="rlrsssl_network_options[hsts]" size="40" value="1"
                   type="checkbox" <?php checked(1, RSSSL()->rsssl_multisite->hsts, true) ?> />
            <span class="rsssl-slider rsssl-round"></span>
        </label>
        <?php
        rsssl_help::this()->get_help_tip(__("Enable this if you want to enable HSTS.", "really-simple-ssl"));
    }

    public
    function get_option_mixed_content_admin()
    {
        ?>
        <label class="rsssl-switch">
            <input id="rlrsssl_options" name="rlrsssl_network_options[mixed_content_admin]" size="40" value="1"
                   type="checkbox" <?php checked(1, RSSSL()->rsssl_multisite->mixed_content_admin, true) ?> />
            <span class="rsssl-slider rsssl-round"></span>
        </label>
        <?php
        rsssl_help::this()->get_help_tip(__("Enable this if you want the mixed content fixer for admin.", "really-simple-ssl"));
    }

    public
    function get_option_cert_expiration_warning()
    {
        ?>
        <label class="rsssl-switch">
            <input id="rlrsssl_options" name="rlrsssl_network_options[cert_expiration_warning]" size="40" value="1"
                   type="checkbox" <?php checked(1, RSSSL()->rsssl_multisite->cert_expiration_warning, true) ?> />
            <span class="rsssl-slider rsssl-round"></span>
        </label>
        <?php
        rsssl_help::this()->get_help_tip(__("Enable this if you want to enable certificate expiration notices.", "really-simple-ssl"));

    }

    public
    function get_option_hide_menu_for_subsites()
    {
        ?>
        <label class="rsssl-switch">
            <input id="rlrsssl_options" name="rlrsssl_network_options[hide_menu_for_subsites]" size="40" value="1"
                   type="checkbox" <?php checked(1, RSSSL()->rsssl_multisite->hide_menu_for_subsites, true) ?> />
            <span class="rsssl-slider rsssl-round"></span>
        </label>
        <?php
        rsssl_help::this()->get_help_tip(__("Enable this if you want to hide menus on subsites.", "really-simple-ssl"));
    }

    public
    function get_hsts_preload()
    {
        $options = get_site_option('rlrsssl_network_options');
        return isset($options["hsts_preload"]) ? $options["hsts_preload"] : false;
    }

    public
    function get_option_hsts_preload()
    {

        $enabled = $this->get_hsts_preload();

        ?>
        <label class="rsssl-switch">
            <input id="rlrsssl_options" name="rlrsssl_network_options[hsts_preload]" size="40" value="1"
                   type="checkbox" <?php checked(1, $enabled, true) ?> />
            <span class="rsssl-slider rsssl-round"></span>
        </label>
        <?php
        rsssl_help::this()->get_help_tip(
            __("The preload list offers even more security, as browsers already will know to load your site over SSL before a user ever visits it. This is very hard to undo!", "really-simple-ssl-pro") . " " .
            __("Please note that all subdomains, and both www and non-www domain need to be https!", "really-simple-ssl-pro") . " " .
            __('Before submitting, please read the information on hstspreload.appspot.com', "really-simple-ssl-pro")
        );
        echo __("After enabling this option, you have to ", "really-simple-ssl-pro") .
            '<a target="_blank" href="https://hstspreload.org/">' . __("submit", "really-simple-ssl-pro") . "</a> " .
            __("your site.", "really-simple-ssl-pro");
    }


    public
    function get_option_do_not_edit_htaccess()
    {
        ?>
        <label class="rsssl-switch">
            <input id="rlrsssl_options" name="rlrsssl_network_options[do_not_edit_htaccess]" size="40" value="1"
                   type="checkbox" <?php checked(1, RSSSL()->rsssl_multisite->do_not_edit_htaccess, true) ?> />
            <span class="rsssl-slider rsssl-round"></span>
        </label>
        <?php
        rsssl_help::this()->get_help_tip(__("Enable this if you want to block the htaccess file from being edited.", "really-simple-ssl"));
    }

    /*
    *
    * Shows a notice when HSTS is enabled while NGINX is detected as webserver
    *
    */

    public
    function show_nginx_hsts_notice()
    {
        if (!is_multisite() || RSSSL()->rsssl_multisite->ssl_enabled_networkwide) {
            if (RSSSL()->rsssl_server->get_server() === 'nginx' && !get_site_option("rsssl_nginx_message_shown")) {
                $preload = $this->get_hsts_preload();
                if (!RSSSL()->rsssl_multisite->hsts && !$preload) return;
                ?>
                <div id="message" class="notice updated is-dismissible">
                    <p>
                        <?php _e("Really Simple SSL has detected NGINX as webserver. The HSTS header is set using PHP which can cause issues with caching. To enable HSTS directly in NGINX add the following line to the NGINX server block within your NGINX configuration:"); ?>
                        <br> <br>
                        <?php if ((RSSSL()->rsssl_multisite->hsts) && (!$preload)) { ?>
                            <code>add_header Strict-Transport-Security: max-age=31536000</code> <br> <br>
                        <?php }
                        if ($preload) { ?>
                            <code>add_header Strict-Transport-Security "max-age=31536000; includeSubDomains"
                                always;</code> <br> <br>
                        <?php }
                        _e("For more information about NGINX and HSTS see:&nbsp", "really-simple-ssl-pro");
                        echo __('<a href="https://www.nginx.com/blog/http-strict-transport-security-hsts-and-nginx" target="_blank">HTTP Strict Transport Security and NGINX</a>', "really-simple-ssl-pro"); ?>
                    </p>
                </div>
                <?php
            }
        }
    }

    /*
    * Dissmiss NGINX notice callback
    */

    public
    function dismiss_nginx_message_callback()
    {
        //nonce check fails if url is changed to ssl.
        //check_ajax_referer( 'really-simple-ssl-dismiss', 'security' );
        update_site_option("rsssl_nginx_message_shown", true);
        wp_die();
    }

    /*
    * Ajax call for the NGINX notice
    */

    public
    function insert_nginx_dismiss_success()
    {
        if (!get_site_option("rsssl_nginx_message_shown")) {
            $ajax_nonce = wp_create_nonce("really-simple-ssl-dismiss");
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function ($) {
                    $(".notice.updated.is-dismissible").on("click", ".notice-dismiss", function (event) {
                        var data = {
                            'action': 'dismiss_success_message_nginx',
                            'security': '<?php echo $ajax_nonce; ?>'
                        };

                        $.post(ajaxurl, data, function (response) {

                        });
                    });
                });
            </script>
            <?php
        }
    }


    public
    function sanitize_boolean($value)
    {
        if ($value == true) {
            return true;
        } else {
            return false;
        }
    }

    /*

      Get the non www domain.

    */

    public
    function non_www_domain()
    {
        $domain = get_home_url();
        $domain = str_replace(array("https://", "http://", "https://www.", "http://www.", "www."), "", $domain);
        return $domain;
    }

} //class closure
}
