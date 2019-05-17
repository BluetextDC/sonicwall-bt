<?php
/*
Plugin Name: HandL UTM Grabber
Plugin URI: http://www.haktansuren.com/handl-utm-grabber
Description: The easiest way to capture UTMs on your (optin) forms.
Author: Haktan Suren
Version: 2.6.3
Author URI: http://www.haktansuren.com/
*/

add_filter('widget_text', 'do_shortcode');

add_action('init', 'CaptureUTMs');
function CaptureUTMs(){
       
	if (!isset($_COOKIE['handl_original_ref'])) 
		$_COOKIE['handl_original_ref'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''; 

	if (!isset($_COOKIE['handl_landing_page'])) 
		$_COOKIE['handl_landing_page'] = ( isset($_SERVER["HTTPS"]) ? 'https://' : 'http://' ) . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	
	if(isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"] != "")
		$_COOKIE['handl_ip'] = $_SERVER["HTTP_X_FORWARDED_FOR"];
	else
		$_COOKIE['handl_ip'] = $_SERVER["REMOTE_ADDR"];
	
	$_COOKIE['handl_ref'] =  isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''; 
	$_COOKIE['handl_url'] =  ( isset($_SERVER["HTTPS"]) ? 'https://' : 'http://' ) . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	
	$fields = array('elqCampaignId', 'elqTrackId','sfc','utm_source','utm_medium','utm_term', 'utm_content', 'utm_campaign', 'gclid', 'handl_original_ref', 'handl_landing_page', 'handl_ip', 'handl_ref', 'handl_url', 'email', 'username');
       
    $cookie_field = '';
	foreach ($fields as $id=>$field){
		//Lowecase hack
		$_GET_lower = array_change_key_case($_GET, CASE_LOWER);
		$field_lower = strtolower($field);

		if (isset($_GET[$field]) && $_GET[$field] != '')
			$cookie_field = htmlspecialchars($_GET[$field],ENT_QUOTES, 'UTF-8');
		elseif(isset($_GET_lower[$field_lower]) && $_GET_lower[$field_lower] != ''){
			$cookie_field = htmlspecialchars($_GET_lower[$field_lower],ENT_QUOTES, 'UTF-8');
		}
		elseif(isset($_COOKIE[$field]) && $_COOKIE[$field] != ''){ 
			$cookie_field = $_COOKIE[$field];
		}else{
			$cookie_field = '';
		}
		
		$domain = $_SERVER["SERVER_NAME"];
		if ( strtolower( substr($domain, 0, 4) ) == 'www.' ) $domain = substr($domain, 4); 
        if ( substr($domain, 0, 1) != '.' && $domain != "localhost" ) $domain = '.'.$domain; 
		
		setcookie($field, $cookie_field , time()+60*60*24*30, '/', $domain );

		$_COOKIE[$field] = $cookie_field;

		add_shortcode($field, function() use ($field) {return urldecode($_COOKIE[$field]);});
		add_shortcode($field."_i", function($atts,$content) use ($field) {return sprintf($content,urldecode($_COOKIE[preg_replace("/_i$/","",$field)]));});
		
		//This is for Gravity Forms
		add_filter( 'gform_field_value_'.$field, function() use ($field) {return urldecode($_COOKIE[$field]); } );
	}
}

function handl_utm_grabber_enqueue(){
	wp_enqueue_script( 'js.cookie', plugins_url( '/js/js.cookie.js' , __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'handl-utm-grabber', plugins_url( '/js/handl-utm-grabber.js' , __FILE__ ), array( 'jquery','js.cookie' ) );
	wp_localize_script( 'handl-utm-grabber', 'handl_utm', HUGGenerateUTMsForURL() );
}
add_action( 'wp_enqueue_scripts', 'handl_utm_grabber_enqueue' );

function handl_utm_grabber_enable_shortcode($val){
	return do_shortcode($val);
}
add_filter('salesforce_w2l_field_value', 'handl_utm_grabber_enable_shortcode');
add_filter( 'wpcf7_form_elements', 'handl_utm_grabber_enable_shortcode' );

function handl_utm_grabber_couponhunt_theme_support($value, $post_id, $field){
	if ( get_option( 'hug_append_all' ) == 1 )
		return add_query_arg( HUGGenerateUTMsForURL(), $value );
	else
		return $value;
}
add_filter( "acf/load_value/name=url", "handl_utm_grabber_couponhunt_theme_support", 10, 3);

function handl_utm_grabber_menu() {
	add_options_page( 
		'HandL UTM Grabber',
		'HandL UTM Grabber',
		'manage_options',
		'handl-utm-grabber.php',
		'handl_utm_grabber_menu_page'
	);
	add_action( 'admin_init', 'register_handl_utm_grabber_settings' );
}
add_action( 'admin_menu', 'handl_utm_grabber_menu' );

function register_handl_utm_grabber_settings() {
	register_setting( 'handl-utm-grabber-settings-group', 'hug_append_all' );
}

function handl_utm_grabber_menu_page(){
?>
	<div class='wrap'>
		<h2><span class="dashicons dashicons-admin-settings" style='line-height: 1.1;font-size: 30px; padding-right: 10px;'></span> HandL UTM Grabber</h2>
		<form method='post' action='options.php'>
			<?php settings_fields( 'handl-utm-grabber-settings-group' ); ?>
			<?php do_settings_sections( 'handl-utm-grabber-settings-group' ); ?>
			<table class='form-table'>
				<tr>
					<th scope='row'>Append UTM</th>
					<td>
						<fieldset>
							<legend class='screen-reader-text'>
								<span>Append UTM</span>
							</legend>
							<label for='hug_append_all'>
								<input name='hug_append_all' id='hug_append_all' type='checkbox' value='1' <?php print checked( '1', get_option( 'hug_append_all' ) ) ?> />
								Append UTM variables to all the links automatically (BETA)
							</label>
							<p class='description' id='handl-utm-grabber-append-all-description'>This feature is still in BETA, please give us feedback <a target='blank' href='https://www.haktansuren.com/handl-utm-grabber/?utm_campaign=HandL+UTM+Grabber+Feedback&utm_content=Append+All+Feedback#reply-title'>here</a></p>
						</fieldset>
					</td>
					
				</tr>
			</table>

			<p>
			<?php submit_button(); ?>
		</form>
	
	</div>
<?php
}

function HUG_Append_All($content) {  
  if ($content != '' && get_option( 'hug_append_all' ) == 1 ){
    if (!function_exists('str_get_html'))
      require_once('simple_html_dom.php');
    $html = str_get_html($content);
    
    $as = $html->find('a');
    
    $search = array();
    $replace = array();
    foreach ($as as $a){

      $a_original = $a->href;
      
      if ($a_original == '') continue;
      if (preg_match('/javascript:void/',$a_original)) continue;
      if (preg_match('/^#/',$a_original)) continue;
      
      $search[] = "/['\"]".preg_quote($a_original,'/')."['\"]/";
      $replace[] = add_query_arg( HUGGenerateUTMsForURL(), html_entity_decode($a_original) );
    }
    $content = preg_replace($search, $replace, $content);
  }
  return $content;
}
add_filter( 'the_content', 'HUG_Append_All', 999 );

function HUGGenerateUTMsForURL(){
  $fields = array('elqCampaignId','elqTrackId','sfc','utm_source','utm_medium','utm_term', 'utm_content', 'utm_campaign', 'gclid');
  $utms = array();
  foreach ($fields as $id=>$field){
    if (isset($_COOKIE[$field]) && $_COOKIE[$field] != '')
      $utms[$field] = $_COOKIE[$field];
  }
  return $utms;
}

function HandLUTMGrabberWooCommerceUpdateOrderMeta( $order_id ) {
	$fields = array('elqCampaignId','elqTrackId','sfc','utm_source','utm_medium','utm_term', 'utm_content', 'utm_campaign', 'gclid', 'handl_original_ref', 'handl_landing_page', 'handl_ip', 'handl_ref', 'handl_url');
	foreach ($fields as $field){
		if (isset($_COOKIE[$field]) && $_COOKIE[$field] != '')
		update_post_meta( $order_id, $field, esc_attr($_COOKIE[$field]));
	}
}
add_action('woocommerce_checkout_update_order_meta', 'HandLUTMGrabberWooCommerceUpdateOrderMeta');

//ConvertPlug UTM Support
//function handl_utm_grabber_setting($a){
//	return do_shortcode($a); 
//}
//add_filter('smile_render_setting', 'handl_utm_grabber_setting',10,1);

function handl_utm_nav_menu_link_attributes($atts, $item, $args){
	if (isset($atts['href']) && $atts['href'] != '' && get_option( 'hug_append_all' ) == 1){
		$atts['href'] = add_query_arg( HUGGenerateUTMsForURL(), $atts['href'] );
	}
	return $atts; 
}
add_filter('nav_menu_link_attributes', 'handl_utm_nav_menu_link_attributes', 10 ,3);

function handl_admin_notice__success() {
    $field = 'check_v252_doc';
    if (!get_option($field)) {
    ?>
    <style>
    .handl-notice-dismiss{
	border-color: #ED494D;
	display: block;
	background-color: #FFF8D7;
    }
    
    .handl-notice-title{
	font-size: 24px;
    }
    
    .handl-notice-list li{
	float: left;
	margin-right: 20px;
    }
    
    .handl-notice-list li a{
	color: #ED494D;
	text-decoration: none;
    }
    
    .handl-notice-list:after{
	clear: both;
	content: "";
	display: block;
    }
    
    .handl-notice-dismiss .new-plugin{
	font-size: 20px;
	line-height: 1;
    }
    
    .handl-notice-dismiss .new-plugin a{
	text-decoration: none;
    }
    </style>
    <div class="notice notice-warning handl-notice-dismiss is-dismissible">
        <p class='handl-notice-title'>HandL UTM Grabber has some new features...</p>
	<ul class='handl-notice-list'>
		<li><span class="dashicons dashicons-clipboard"></span> <a href="https://www.haktansuren.com/handl-utm-grabber/?utm_medium=referral&utm_source=<?php print $_SERVER["SERVER_NAME"]?>&utm_campaign=HandL+UTM+Grabber&utm_content=New+Features" target="_blank">Check out documentations</a></li>
		<li><span class="dashicons dashicons-sos"></span> <a href="https://wordpress.org/support/plugin/handl-utm-grabber" target="_blank">Get Some Help</a></li>
		<li><span class="dashicons dashicons-heart"></span> <a href="https://wordpress.org/support/view/plugin-reviews/handl-utm-grabber" target="_blank">Like Us!</a></li>
		<li><span class="dashicons dashicons-smiley"></span> <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SS93TW4NEHHNG" target="_blank">Donate</a></li>
	</ul>
	<p><span class="dashicons dashicons-info"></span> <a href="options-general.php?page=handl-utm-grabber.php">Would you like to append UTM variables to all URLs on your site?</a></p>
	<p class='new-plugin'><span class="dashicons dashicons-video-alt3"></span> <a href="https://www.haktansuren.com/handl-youtube-extra/?utm_medium=referral&utm_source=<?php print $_SERVER["SERVER_NAME"]?>&utm_campaign=HandL+UTM+Grabber&utm_content=New+Plugin+HandL+YouTube+Extra" target="_blank">New Plugin! Track your YouTube videos</a></p>
    </div>
    <script>
    jQuery(document).on( 'click', '.handl-notice-dismiss>.notice-dismiss', function() {
	
	jQuery.post(
		ajaxurl, 
		{
		    'action': 'handl_notice_dismiss',
		    'field':   '<?php print $field;?>'
		}
	);
    
    })
    </script>
    <?php
	}
}
add_action( 'admin_notices', 'handl_admin_notice__success' );

function handl_notice_dismiss(){
	add_option( $_POST['field'], '1', '', 'yes' ) or update_option($_POST['field'], '1'); 
	die();
}
add_action( 'wp_ajax_handl_notice_dismiss', 'handl_notice_dismiss' );

function handl_utm_grabber_merge_tags(){
  require_once 'external/ninja.php';
  Ninja_Forms()->merge_tags[ 'handl_utm_merge_tags' ] = new HandLUTM_MergeTags();
}
add_action( 'ninja_forms_loaded', 'handl_utm_grabber_merge_tags' );
