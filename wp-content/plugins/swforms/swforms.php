<?php

/*
 * Plugin Name: Sw Forms
 * Plugin URI: https://sonicgit.eng.sonicwall.com/sonicostools/swforms
 * Description: Custom forms plugin by sonicwall
 * Author: Sonicwall
 * Author URI: https://www.sonicwall.com/
 * Version: 1.0
 */
session_start();
if (!defined("ABSPATH"))
    exit;
if (!defined("SW_FORMS_PLUGIN_DIR_PATH"))
    define("SW_FORMS_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));      // C:\xampp\htdocs\my_first_plugin\wordpress\wp-content\plugins\swforms
if (!defined("SW_FORMS_PLUGIN_URL"))
    define("SW_FORMS_PLUGIN_URL", plugins_url() . "/swforms");          // "/swforms" is the plugin folder name
if (!defined("SITE_URL"))
    define("SITE_URL", site_url());                                     // http://localhost/my_first_plugin/wordpress

if(file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}
/*
if(file_exists(dirname(__FILE__) . '/wp-load.php')) {
    require_once( dirname(__FILE__) . '/wp-load.php' );                 
}
*/

function sw_forms_include_assets() {

    $slug = '';
    $pages_includes = array("frontendpage","form-list","add-new","form-settings","callback-page","eloqua-form-settings", "form-entries", "form-entry-detail", "email-notification");

    $currentPage = $_GET['page'];       // form-list

    //$_SERVER[REQUEST_URI] 
    ///$_SERVER[HTTP_HOST]: http://, https://

    if(empty($currentPage)){
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            if (preg_match("/sw_form/", $actual_link)) {
                $currentPage = "frontendpage";
            }
    }

    wp_enqueue_script('jquery');
	if(in_array($currentPage,$pages_includes)){         // styles & script within this condition are affected only inside admin pannel
        //styles
        wp_enqueue_style("bootstrap", SW_FORMS_PLUGIN_URL . "/assets/css/bootstrap.css");   // name, path, dependency(array()), version(null), media('all')
        wp_enqueue_style("datatable", SW_FORMS_PLUGIN_URL . "/assets/css/jquery.dataTables.min.css");
		wp_enqueue_style("notifybar", SW_FORMS_PLUGIN_URL . "/assets/css/jquery.notifyBar.css");
        wp_enqueue_style("sw-admin-style", SW_FORMS_PLUGIN_URL . "/assets/css/sw-admin-style.css");
        
		//scripts
        wp_register_script('prefix_jqueryui_js', SW_FORMS_PLUGIN_URL . '/assets/js/jquery-ui.min.js', array('jquery'));     // name, path, dependency(array()), version(string|bool|null)/default:false, header(false)default/footer(true)
        wp_register_script('prefix_jquery_formbuilder_js', SW_FORMS_PLUGIN_URL . '/assets/js/form-builder.min.js', array('jquery', 'prefix_jqueryui_js'));
        wp_register_script('prefix_jquery_formrender_js', SW_FORMS_PLUGIN_URL . '/assets/js/form-render.min.js', array('jquery', 'prefix_jqueryui_js'));

        wp_enqueue_script('bootstrap.min.js', SW_FORMS_PLUGIN_URL . '/assets/js/bootstrap.min.js', array(), false, true);           // name, path, dependency(array()), version(string|bool|null)/default:false, header(false)default/footer(true)
        
        wp_register_script('validation.min.js', SW_FORMS_PLUGIN_URL . '/assets/js/jquery.validate.min.js', array('jquery'));
        wp_register_script('datatable.min.js', SW_FORMS_PLUGIN_URL . '/assets/js/jquery.dataTables.min.js', array('jquery'));
        wp_register_script('jquery.notifyBar.js', SW_FORMS_PLUGIN_URL . '/assets/js/jquery.notifyBar.js', array('jquery'));
        
        wp_register_script('sw-admin-script', SW_FORMS_PLUGIN_URL . '/assets/js/sw-admin-script.js', array('jquery', 'prefix_jqueryui_js', 'prefix_jquery_formbuilder_js', 'prefix_jquery_formrender_js', 'validation.min.js', 'datatable.min.js', 'jquery.notifyBar.js'));
        wp_enqueue_script('sw-admin-script');
        wp_localize_script("sw-admin-script", "swformsajaxurl", admin_url("admin-ajax.php"));
    }
    wp_enqueue_style("sw-theme-style", SW_FORMS_PLUGIN_URL . "/assets/css/sw-theme-style.css");
    wp_enqueue_style("notifybar", SW_FORMS_PLUGIN_URL . "/assets/css/jquery.notifyBar.css");
    if(!in_array($currentPage,$pages_includes)){  
        wp_register_script('sw-country-script', SW_FORMS_PLUGIN_URL . '/assets/js/countries.js', array('jquery'));
        wp_enqueue_script('sw-country-script');
    }
    wp_register_script('sw-theme-script', SW_FORMS_PLUGIN_URL . '/assets/js/sw-theme-script.js', array('jquery'));
    wp_register_script('sw-theme-script', SW_FORMS_PLUGIN_URL . '/assets/js/jquery.notifyBar.js', array('jquery'));
    wp_enqueue_script('sw-theme-script');
    wp_localize_script("sw-theme-script", "swsiteurl", SITE_URL);
    wp_localize_script("sw-theme-script", "swformsajaxurl", admin_url("admin-ajax.php"));      // to make the ajax request we need to include this file from wp-admin folder

    // $_SESSION['access_token'] = 'MTEwMzg0MzM1MDoxamFPQ0p2ZVVua3VzTzdFcXB3R0poYTg3VUY0Rm5QTm03T0VGNjh0RkZPcFk4TVBkNEF5RjIzbTlma3k5ajNoTW9mfkFrU2JHZWVUdmpNNENwSVFFN1BmUE1NNjVNYnowSTRw';
    if(!$_SESSION['access_token']) {
        if(get_eloqua_access_token_from_db()[0]['access_token']) {
            $_SESSION['access_token'] = get_eloqua_access_token_from_db()[0]['access_token'];
        } else {
            $_SESSION['access_token'] = '';
        }
    }
    if(!$_SESSION['data_center_url']) {
        if(get_eloqua_data_center_url_from_db()[0]['data_center_url']) {
            $_SESSION['data_center_url'] = get_eloqua_data_center_url_from_db()[0]['data_center_url'];
        } else {
            $_SESSION['data_center_url'] = '';
        }
    }
}

function get_eloqua_access_token_from_db() {
    global $wpdb;
	$access_token = $wpdb->get_results( "SELECT `access_token` from ".eloqua_token(), ARRAY_A );
	if ($access_token) {
		return $access_token;
	} else {
		return false;
	}
}
function get_eloqua_data_center_url_from_db() {
    global $wpdb;
	$data_center_url = $wpdb->get_results( "SELECT `data_center_url` from ".eloqua_token(), ARRAY_A );
	if ($data_center_url) {
		return $data_center_url;
	} else {
		return false;
	}
}

add_action("init", "sw_forms_include_assets");

function sw_form_plugin_menus() {

    add_menu_page("Sw Forms", "Sw Forms", "manage_options", "form-list", "sw_form_list", "dashicons-book-alt", 30);     // page title, menu title, admin capabilities, Page Slug, callback, icon, menu position
    add_submenu_page("form-list", "All Forms", "All Forms", "manage_options", "form-list", "sw_form_list");             // Parent Slug, page title, menu title, admin capabilities, Page Slug, callback
    add_submenu_page("form-list", "Add New", "Add New", "manage_options", "add-new", "sw_add_new");
    add_submenu_page("form-list", "Settings", "Settings", "manage_options", "form-settings", "my_form_settings");
    add_submenu_page("form-list", "Eloqua Form Settings", "Eloqua Form Settings", "manage_options", "eloqua-form-settings", "eloqua_form_settings");
    add_submenu_page("form-list", "Entries", "Entries", "manage_options", "form-entries", "form_entries");
    add_submenu_page("form-list", "Notification", "Notification", "manage_options", "email-notification", "email_notification");
    add_submenu_page("form-list", "", "", "manage_options", "form-entry-detail", "form_entry_detail");
    //end section
    add_submenu_page("form-list", "", "", "manage_options", "callback-page", "sw_callback");
}

function sw_form_list() {
    include_once SW_FORMS_PLUGIN_DIR_PATH . "/views/all-forms.php";
}
function sw_add_new(){
  include_once SW_FORMS_PLUGIN_DIR_PATH . "/views/add-new.php";
}
function my_form_settings(){
   include_once SW_FORMS_PLUGIN_DIR_PATH . "/views/settings.php";
}
function sw_callback() {
    include_once SW_FORMS_PLUGIN_DIR_PATH . "/views/callback.php";
}
function eloqua_form_settings() {
    include_once SW_FORMS_PLUGIN_DIR_PATH . "/views/eloqua-form-settings.php";
}
function form_entries() {
    include_once SW_FORMS_PLUGIN_DIR_PATH . "/views/entries.php";
}
function form_entry_detail() {
    include_once SW_FORMS_PLUGIN_DIR_PATH . "/views/entry-detail.php";
}
function email_notification() {
    include_once SW_FORMS_PLUGIN_DIR_PATH . "/views/notification.php";
}

add_action("admin_menu", "sw_form_plugin_menus");

function my_form_table() {
    global $wpdb;
    return $wpdb->prefix . "sw_forms";
}
function sw_forms_entry() {
    global $wpdb;
    return $wpdb->prefix . "sw_forms_entry";
}
function sw_mail_forms_entry() {
    global $wpdb;
    return $wpdb->prefix . "sw_mail_forms_entry";
}
function eloqua_token() {
    global $wpdb;
    return $wpdb->prefix . "sw_eloqua_token";
}
function sw_mail_notification() {
    global $wpdb;
    return $wpdb->prefix . "sw_mail_notification";
}

function sw_form_generates_table_script() {

    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $my_form_table_sql = "  CREATE TABLE `" . my_form_table() . "` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(150) DEFAULT NULL,
        `short_code` varchar(150) DEFAULT NULL,
        `author` varchar(150) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `views` int(11) DEFAULT NULL,
        `comments` varchar(300) DEFAULT NULL,
        `eloqua_submit` varchar(20) DEFAULT NULL,
        `form_json` varchar(25000) DEFAULT NULL,
        `form_html` varchar(30000) DEFAULT NULL,
        `eloqua_form_id` varchar(150) DEFAULT NULL,
        `eloqua_form_name` varchar(150) DEFAULT NULL,
        `field_map` varchar(5000) DEFAULT NULL,
        PRIMARY KEY (`id`)
       ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
    dbDelta($my_form_table_sql);

    $sw_forms_entry_sql = "  CREATE TABLE `" . sw_forms_entry() . "` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `sw_form_id`int not null,
        `eloqua_form_id` int not null,
        `form_submit_job_id` varchar(50) DEFAULT NULL,
        `sw_form_user_entry` varchar(5000) DEFAULT NULL,
        `eloqua_status` varchar(50) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
    dbDelta($sw_forms_entry_sql);

    $sw_mail_forms_entry_sql = "  CREATE TABLE `" . sw_mail_forms_entry() . "` (
        `id` INT NOT NULL AUTO_INCREMENT , 
        `my_form_table_id` INT NOT NULL , 
        `form_submit_mail_id` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL , 
        `sw_form_entry_subject` VARCHAR(500) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
        `sw_form_user_entry` VARCHAR(5000) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL , 
        `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
        `mail_submit_status` varchar(20) DEFAULT NULL,
        PRIMARY KEY (`id`)) ENGINE = InnoDB;";
    dbDelta($sw_mail_forms_entry_sql);

    $eloqua_token_sql = "  CREATE TABLE `" . eloqua_token() . "` (
        `id` int(11) NOT NULL,
        `access_token` varchar(500) DEFAULT NULL,
        `token_type` varchar(500) DEFAULT NULL,
        `expires_in` int DEFAULT null,
        `refresh_token` varchar(500) DEFAULT NULL,
        `data_center_url` varchar(500) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `eloqua_notification_mail` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,
        PRIMARY KEY (`id`)
       ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
    dbDelta($eloqua_token_sql);
    $wpdb->insert(eloqua_token(), array(
        "id" => 1,
        "access_token" => '',
        "token_type" => '',
        "expires_in" => 0,
        "refresh_token" => ''
    ));

    $sw_mail_notification_sql = "  CREATE TABLE `" . sw_mail_notification() . "` (
        `id` INT NOT NULL AUTO_INCREMENT ,
        `my_form_table_id` INT NOT NULL ,
        `form_to_mail_id` VARCHAR(100) DEFAULT NULL ,
        `form_cc_mail_id` VARCHAR(100) DEFAULT NULL ,
        `form_bcc_mail_id` VARCHAR(100) DEFAULT NULL ,
        `form_subject` VARCHAR(1000) DEFAULT NULL ,
        `form_after_body` VARCHAR(2000) DEFAULT NULL ,
        `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
        PRIMARY KEY (`id`)) ENGINE = InnoDB;";
    dbDelta($sw_mail_notification_sql);
}

register_activation_hook(__FILE__, "sw_form_generates_table_script");

add_filter('wp_mail_from','yoursite_wp_mail_from');
function yoursite_wp_mail_from($content_type) {
  return 'wp@swmail.nguyenle.me';
}
add_filter('wp_mail_from_name','yoursite_wp_mail_from_name');
function yoursite_wp_mail_from_name($name) {
  return 'SonicWall Website';
}

function sw_form_page_functions($params){      // $params, $content, $tag
    $values = shortcode_atts(
        array(
            'id'=>'1'
        ),
        $params,
        'sw_form'
    );

    $formTemplateWrapper = "<div id='sw-form-wrap'>".getFormTemplate($values['id'])['form_html']."<input type='hidden' id='form_id' value=".$values['id']."/>
    <input type='hidden'  id='eloqua_form_id' value=".getFormTemplate($values['id'])['eloqua_form_id']."/>
    </div>
    <div id='swforms-loading' class='swforms-loading sw-hide'>
      <div class='swforms-loading-content'>
      <div class='lds-spinner'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
      </div>
    </div>
    <script src='https://www.google.com/recaptcha/api.js' async defer></script>
    ";
    return $formTemplateWrapper;
}

function getFormTemplate($id) {
    global $wpdb;
    $form_detail = $wpdb->get_row(
        $wpdb->prepare(
                    "SELECT form_html,eloqua_form_id from ".my_form_table()." WHERE id = %d ",$id
                ),ARRAY_A
        );
    $temp_form_data = $form_detail;
    return $temp_form_data;
}
add_shortcode("sw_form","sw_form_page_functions");

function drop_table_plugin_forms() {
    /*
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS " . my_form_table());
    $wpdb->query("DROP TABLE IF EXISTS " . sw_forms_entry());
    $wpdb->query("DROP TABLE IF EXISTS " . sw_mail_forms_entry());
    $wpdb->query("DROP TABLE IF EXISTS " . eloqua_token());
    */
}

register_deactivation_hook(__FILE__, "drop_table_plugin_forms");
// register_uninstall_hook(__FILE__,"drop_table_plugin_forms");

add_action("wp_ajax_swformlibrary", "swformlibrary");
function swformlibrary() {
    global $wpdb;
    include_once SW_FORMS_PLUGIN_DIR_PATH . '/library/sw_formlibrary.php';
    wp_die();
}

add_action("wp_ajax_swformthemelibrary", "swformthemelibrary");
add_action('wp_ajax_nopriv_swformthemelibrary', 'swformthemelibrary');
function swformthemelibrary() {
    global $wpdb;
    include_once SW_FORMS_PLUGIN_DIR_PATH . '/library/sw_formthemelibrary.php';
    wp_die();
}


function sw_trigger_mail($to, $subject, $body, $headers) {
    $mail_submit_response = wp_mail( $to, $subject, $body, $headers );      // A true return value does not automatically mean that the user received the email successfully. It just only means that the method used was able to process the request without any errors.
    // $mail_submit_response type boolean (true or false)
    return $mail_submit_response;
}

function eloqua_notification_trigger($to, $subject, $body, $headers) {
    wp_mail( $to, $subject, $body, $headers );      // A true return value does not automatically mean that the user received the email successfully. It just only means that the method used was able to process the request without any errors.
}

?>
