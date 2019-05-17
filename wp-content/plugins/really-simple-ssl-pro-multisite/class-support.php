<?php

defined('ABSPATH') or die("you do not have access to this page!");

if ( ! class_exists( 'rsssl_support' ) ) {
    class rsssl_support
    {
        private static $_this;

        public $error_message = "";
        public $success_message = "";

        function __construct()
        {

            add_filter('rsssl_tabs', array($this,'add_support_tab'),10,4 );
            add_action('show_tab_support', array($this, 'add_support_page'));
            add_action('admin_init', array($this, 'process_support_request'));

            if (isset(self::$_this))
                wp_die(sprintf(__('%s is a singleton class and you cannot create a second instance.', 'really-simple-ssl'), get_class($this)));

            self::$_this = $this;

        }

        static function this()
        {
            return self::$_this;
        }

        public function add_support_tab($tabs)
        {
            $tabs['support'] = __("Support","really-simple-ssl-pro");
            return $tabs;
        }

        function add_support_page()
        {
            //Required to put e-mail and name placeholders in fields.
            $user_info = get_userdata(get_current_user_id());
            ?>
            <div id="rsssl">
                <div class="support">
                    <h2><?php _e('Enter your support request' , 'really-simple-ssl-pro') ?></h2>

                    <?php
                    $link_start ='<a href="https://really-simple-ssl.com/knowledge-base-overview/" target="_blank">';
                    $link_close = "</a> ";
                    echo sprintf(__("A lot of issues are already described in the %sdocumentation.%s Please check if your issue is already in the knowledge base before submitting a ticket.", "really-simple-ssl-pro"),$link_start, $link_close );
                    ?>

                    <p><?php _e('Please provide a short description, your e-mail address and a summary of the issue(s) you are experiencing.' , 'really-simple-ssl-pro') ?>
                        <?php _e('The following information is automatically added to your ticket to provide better service:', 'really-simple-ssl-pro')?></p>
                    <ul class="support-list">
                        <li><?php _e('license key' , 'really-simple-ssl-pro') ?></li>
                        <li><?php _e('scan results' , 'really-simple-ssl-pro') ?></li>
                        <li><?php _e('your domain' , 'really-simple-ssl-pro') ?></li>
                        <li><?php _e('.htaccess file', 'really-simple-ssl-pro') ?> </li>
                        <li><?php _e('debug log contents' , 'really-simple-ssl-pro') ?></li>
                        <li><?php _e('list of active plugins' , 'really-simple-ssl-pro') ?></li>
                    </ul>

                    <?php if (!empty($this->error_message)) {
                        ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $this->error_message ?>
                        </div>
                        <?php
                    } elseif (!empty($this->success_message)) {
                        ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $this->success_message ?>
                        </div>
                        <?php
                    } ?>
                    <form method="POST" class="support-form" action="">
                        <?php wp_nonce_field('rsssl_support', 'rsssl_nonce') ?>
                        <input type="text" required name="rsssl_support_name"
                               required placeholder="<?php _e("Your name", "really-simple-ssl-pro") ?>"
                               value = <?php echo $user_info->user_firstname; ?>>
                        <input type="text" name="rsssl_support_subject"
                               required placeholder="<?php _e("Summarize your issue in a few words", "really-simple-ssl-pro") ?>">
                        <input type="email" name="rsssl_support_email" required placeholder="<?php _e("Your email address",'really-simple-ssl-pro')?>"
                               value="<?php echo $user_info->user_email; ?>">
                        <textarea placeholder="<?php _e("Describe your issue", "really-simple-ssl-pro") ?>" name="rsssl_support_request" required></textarea>
                        <input type="submit" class="btn btn-primary button-submit-ticket"
                               value="<?php _e('Submit ticket', 'really-simple-ssl-pro') ?>">
                    </form>
                </div>
            </div>
            <?php
        }


        public function process_support_request()
        {
            if (isset($_POST['rsssl_support_request']) && isset($_POST['rsssl_support_email'])) {
                if (!is_email($_POST['rsssl_support_email'])) {
                    $this->error_message = __('Email address not valid', 'really-simple-ssl-pro');
                    return;
                }

                if (!wp_verify_nonce($_POST['rsssl_nonce'], 'rsssl_support')) return;

                $email = sanitize_email($_POST['rsssl_support_email']);
                $subject = sanitize_text_field($_POST['rsssl_support_subject']);
                $name = sanitize_text_field($_POST['rsssl_support_name']);

                $allowed_tags = wp_kses_allowed_html('post');
                $support_request = wp_kses($_POST['rsssl_support_request'], $allowed_tags);

                $license = get_option('rsssl_pro_license_key');

                //Get ABSpath for .htaccess location
                $ABSpath = $this->get_ABSPATH();

                //If there is an .htaccess file, add it as attachment to improve readability
                if (file_exists($ABSpath . ".htaccess"))  {
                    $attachment = array($ABSpath . ".htaccess");
                }

                //Check if debugging has been enabled
                if(RSSSL()->really_simple_ssl->debug){
                    $debug_log_contents = RSSSL()->really_simple_ssl->debug_log;
                } else {
                    $debug_log_contents = "Debugging not enabled";
                }

                //Retrieve the domain
                $domain = site_url();
                //Retrieve active plugins
                $plugins = get_option('active_plugins');

                if (is_multisite()) {
                    $networkwide_activated_plugins = get_site_option('active_sitewide_plugins');
                }

                //Get scan results from transient
                $scan_results = get_transient("rlrsssl_scan");

                $headers[] = "Reply-to: <$email>" . "\r\n";

                $to = "support@really-simple-ssl.com";

                //Uses non-translatable strings because users will never see this.
                $message  = "License: $license <br><br>";
                $message .= "From: $name <br><br>";
                $message .= "Domain: $domain <br><br>";
                $message .= "Support request: $support_request <br><br>";
                $message .= "<hr />";
                $message .= "Debug log contents: $debug_log_contents <br><br>";
                //Use <pre> tags for better array formatting in HTML
                $message .= "Active plugins: " . "<pre>" . print_r($plugins, true) . "</pre>" ."<br>";

                if (is_multisite()) {
                    $message .= "Networkwide active plugins: ". "<pre>" . print_r($networkwide_activated_plugins, true) . "</pre>" . "<br><br>";
                }

                $message .= "Scan results:" . "<pre>";

                if (!empty($scan_results['posts_with_blocked_resources'])) {
                    $message .= ("Posts with blocked resources" . "<br>");
                    $message .= print_r($scan_results['posts_with_blocked_resources'], true);
                }

                if (!empty($scan_results['postmeta_with_blocked_resources'])) {
                    $message .= ("Postmeta with blocked resources" . "<br>");
                    $message .= print_r($scan_results['postmeta_with_blocked_resources'], true);
                }

                if (!empty($scan_results['tables_with_blocked_resources'])) {
                    $message .= ("Tables with blocked resources" . "<br>");
                    $message .= print_r($scan_results['tables_with_blocked_resources'], true);
                }

                if (!empty($scan_results['widgets_with_blocked_resources'])) {
                    $message .= ("Widgets with blocked resources" . "<br>");
                    $message .= print_r($scan_results['widgets_with_blocked_resources'], true);
                };

                "</pre>" . "<br><br>";

                add_filter('wp_mail_content_type', function ($content_type) {
                    return 'text/html';
                });

                //Only send an e-mail with .htaccess attachment if there is an attachment
                if (!empty($attachment)) {
                    $success = wp_mail($to, $subject, $message, $headers, $attachment);
                } else {
                    $success = wp_mail($to, $subject, $message, $headers);
                }

                // Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
                remove_filter('wp_mail_content_type', 'set_html_content_type');
                if ($success) {
                    $this->success_message = __("Your support request has been received. We will reply shortly.", "really-simple-ssl-pro");

                } else {
                    $this->error_message = __("Something went wrong while submitting your support request. Please try again or contact us directly.", "really-simple-ssl-pro");

                }
            }
        }

        /**
         * Find if this WordPress installation is installed in a subdirectory
         *
         * @since  2.0
         *
         * @access protected
         *
         */

        protected function is_subdirectory_install()
        {
            if (strlen(site_url()) > strlen(home_url())) {
                return true;
            }
            return false;
        }

        //Get ABSPATH, required for retrieval of .htaccess file
        public function get_ABSPATH(){
            $path = ABSPATH;
            if($this->is_subdirectory_install()){
                $siteUrl = site_url();
                $homeUrl = home_url();
                $diff = str_replace($homeUrl, "", $siteUrl);
                $diff = trim($diff,"/");
                $pos = strrpos($path, $diff);
                if($pos !== false){
                    $path = substr_replace($path, "", $pos, strlen($diff));
                    $path = trim($path,"/");
                    $path = "/".$path."/";
                }
            }
            return $path;
        }

    } //class closure
}