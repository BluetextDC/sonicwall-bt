<?php
defined('ABSPATH') or die("you do not have acces to this page!");

if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater.php' );
}

if (!class_exists("rsssl_licensing_ms")) {
class rsssl_licensing_ms {
private static $_this;

function __construct() {
	if ( isset( self::$_this ) )
			wp_die( sprintf( __( '%s is a singleton class and you cannot create a second instance.','really-simple-ssl' ), get_class( $this ) ) );

	self::$_this = $this;
	add_action('admin_init', array($this, 'plugin_updater'), 0 );
	add_action('admin_init', array($this, 'activate_license'), 10,3);
	add_action('admin_init', array($this, 'register_option'), 20,3);
	add_action('admin_init', array($this, 'deactivate_license'),30,3);
	add_action( 'admin_notices', array ($this, 'error_messages' ));

	add_action( 'admin_init', array ($this, 'update_license_option' ));

	add_action("rsssl_show_network_tab_license", array($this, "show_license_tab"));
	add_filter('rsssl_network_tabs', array($this, 'add_license_tab'));
	add_action('wp_ajax_rsssl_pro_dismiss_license_notice', array($this,'dismiss_license_notice') );
	add_action("network_admin_notices", array($this, 'show_notice_license'));	
}

static function this() {
	return self::$_this;
}

public function add_license_tab($tabs){
$tabs['license'] = __("License","really-simple-ssl-pro");
return $tabs;
}


public function show_license_tab(){
$license 	= get_site_option( 'rsssl_pro_license_key' );
$status 	= get_site_option( 'rsssl_pro_license_status' );

?>
	<form method="post" action="edit.php?action=rsssl_update_network_license_settings">
		<?php wp_nonce_field( 'rsssl_pro_ms_nonce', 'rsssl_pro_ms_nonce' ); ?>
		<?php settings_fields('rsssl_pro_license'); ?>

		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e('Really Simple SSL pro multisite license key'); ?>
					</th>
					<td>
						<input id="rsssl_pro_license_key" name="rsssl_pro_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
						<?php if( false !== $license ) { ?>
									<?php if( $status && $status == 'valid' ) { ?>
										<span style="color:green;"><?php _e('active'); ?></span>
										<input type="submit" class="button-secondary" name="rsssl_pro_ms_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
									<?php } else {?>
										<input type="submit" class="button-secondary" name="rsssl_pro_ms_license_activate" value="<?php _e('Activate License'); ?>"/>

									<?php } ?>
								</td>
							</tr>
						<?php } else {
							?>
							<label class="description" for="rsssl_pro_license_key"><?php _e('Enter your license key'); ?></label>
							<?php
						}?>


					</td>
				</tr>

			</tbody>
		</table>
		<?php submit_button(); ?>

	</form>

<?php
}

public function update_license_option(){
	//only execut when save button is clicked.
	if (!isset($_POST['submit'])) return;
	if (!isset($_POST["rsssl_pro_license_key"])) return;



	if( !check_admin_referer( 'rsssl_pro_ms_nonce', 'rsssl_pro_ms_nonce' ) )return;

	$license = sanitize_text_field($_POST["rsssl_pro_license_key"]);
	$license = $this->sanitize_license($license);

	update_site_option("rsssl_pro_license_key", $license);

	wp_redirect(add_query_arg(array('page' => 'really-simple-ssl','tab'=>'license'), network_admin_url('settings.php')));
	exit();
}



/**
 * Process the ajax dismissal of the success message.
 *
 * @since  2.0
 *
 * @access public
 *
 */

public function dismiss_license_notice() {
	check_ajax_referer( 'rsssl-pro-dismiss-license-notice', 'nonce' );
	update_site_option( 'rsssl_pro_license_notice_dismissed', true);
	wp_die();
}

public function dismiss_license_notice_script() {
  $ajax_nonce = wp_create_nonce( "rsssl-pro-dismiss-license-notice" );
  ?>
  <script type='text/javascript'>
    jQuery(document).ready(function($) {

      $(".rsssl-pro-dismiss-notice.notice.is-dismissible").on("click", ".notice-dismiss", function(event){
            var data = {
              'action': 'rsssl_pro_dismiss_license_notice',
              'nonce': '<?php echo $ajax_nonce; ?>'
            };

            $.post(ajaxurl, data, function(response) {

            });
        });
    });
  </script>
  <?php
}

public function show_notice_license(){
		 add_action('admin_print_footer_scripts', array($this, 'dismiss_license_notice_script'));
		 $dismissed	= get_site_option( 'rsssl_pro_license_notice_dismissed' );

 		 if (!$dismissed && !$this->license_is_valid()) { ?>

		  <div id="message" class="error fade notice is-dismissible rsssl-pro-dismiss-notice">
		    <p>
		      <?php echo __("You haven't activated your Really Simple SSL pro multisite license yet. To get all future updates, enter your license on the network settings page.","really-simple-ssl-pro");?>
					<a href="<?php echo network_admin_url("settings.php?page=really-simple-ssl&tab=license")?>"><?php echo __("Go to the settings page","really-simple-ssl-pro");?></a>
					or <a target="blank" href="https://www.really-simple-ssl.com/pro-multisite">purchase a license</a>
				</p>
			</div>
		<?php } ?>
 <?php
}



public function plugin_updater() {
	// retrieve our license key from the DB
	$license_key = trim( get_site_option( 'rsssl_pro_license_key' ) );

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater( REALLY_SIMPLE_SSL_URL, rsssl_pro_plugin_file, array(
			'version' 	=> rsssl_pro_version, 				// current version number
			'license' 	=> $license_key, 		// license key (used get_site_option above to retrieve from DB)
			'item_name' => REALLY_SIMPLE_SSL_PRO, 	// name of this plugin
			'author' 	=> 'Rogier Lankhorst'  // author of this plugin
		)
	);

}

public function register_option() {
	// creates our settings in the options table
	register_setting('rsssl_pro_license', 'rsssl_pro_license_key', array($this, 'sanitize_license') );
}

public function sanitize_license( $new ) {
	$old = get_site_option( 'rsssl_pro_license_key' );
	if( $old && $old != $new ) {
		delete_site_option( 'rsssl_pro_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}



/************************************
* this illustrates how to activate
* a license key
*************************************/

public function activate_license() {
	// listen for our activate button to be clicked
	if(isset($_POST['rsssl_pro_ms_license_activate']) ) {
		// run a quick security check
	 	if( ! check_admin_referer( 'rsssl_pro_ms_nonce', 'rsssl_pro_ms_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = sanitize_key(trim( $_POST['rsssl_pro_license_key']));
		update_site_option('rsssl_pro_license_key', $license);
		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'activate_license',
			'license' 	=> $license,
			'item_name' => urlencode( REALLY_SIMPLE_SSL_PRO ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$args = array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params );
		$args = apply_filters('rsssl_license_verification_args', $args );

/*
			//If you need to set sslverify to true, add this in your functions.php
			add_filter('rsssl_license_verification_args', 'rsssl_verification_filter' );
			function rsssl_verification_filter($args){
			  //set ssl verify to true
			  $args['sslverify'] = true;
			  return $args;
			}
*/

		$response = wp_remote_post( REALLY_SIMPLE_SSL_URL, $args );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				$error_message = $response->get_error_message();
				$message =  ( is_wp_error( $response ) && ! empty( $error_message ) ) ? $error_message : __( 'An error occurred, please try again.' );

			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( false === $license_data->success ) {

					switch( $license_data->error ) {

						case 'expired' :

							$message = sprintf(
								__( 'Your license key expired on %s.' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;

						case 'revoked' :

							$message = __( 'Your license key has been disabled.' );
							break;

						case 'missing' :

							$message = __( 'Invalid license.' );
							break;

						case 'invalid' :
						case 'site_inactive' :

							$message = __( 'Your license is not active for this URL.' );
							break;

						case 'item_name_mismatch' :

							$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), REALLY_SIMPLE_SSL_PRO );
							break;

						case 'no_activations_left':

							$message = __( 'Your license key has reached its activation limit.' );
							break;

						default :

							$message = __( 'An error occurred, please try again.' );
							break;
					}

				}

			}

			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				$base_url = network_admin_url( 'settings.php?page=really-simple-ssl&tab=license');
				//$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );
				delete_site_option( 'rsssl_pro_license_status' );
				//wp_redirect( $redirect );
				wp_redirect(add_query_arg(array('page' => 'really-simple-ssl','tab'=>'license', 'sl_activation' => 'false', 'message' => urlencode( $message )), network_admin_url('settings.php')));

				exit();
			}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "valid" or "invalid"
		update_site_option( 'rsssl_pro_license_status', $license_data->license);
		// $license_data->license will be either "deactivated" or "failed"
		if($license_data->license == 'deactivated' ) {
			delete_site_option( 'rsssl_pro_license_status' );
		}
		wp_redirect(add_query_arg(array('page' => 'really-simple-ssl','tab'=>'license'), network_admin_url('settings.php')));

		//wp_redirect( network_admin_url( 'settings.php?page=really-simple-ssl&tab=license') );
		exit();

	}
}

/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
public function error_messages() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

		switch( $_GET['sl_activation'] ) {

			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo $message; ?></p>
				</div>
				<?php
				break;

			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;

		}
	}
}

/***********************************************
* Illustrates how to deactivate a license key.
* This will descrease the site count
***********************************************/


public function deactivate_license() {

	// listen for our deactivate button to be clicked
	if( isset( $_POST['rsssl_pro_ms_license_deactivate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'rsssl_pro_ms_nonce', 'rsssl_pro_ms_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_site_option( 'rsssl_pro_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'deactivate_license',
			'license' 	=> $license,
			'item_name' => urlencode( REALLY_SIMPLE_SSL_PRO ), // the name of our product in EDD
			'url'       => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( REALLY_SIMPLE_SSL_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' ) {

			delete_site_option('rsssl_pro_license_status' );
			delete_site_option('rsssl_pro_license_notice_dismissed');
		}
	}
}


public function license_is_valid() {
	$status	= get_site_option( 'rsssl_pro_license_status' );
	if ($status=="valid") return true;

	return false;

}

}} //class closure
