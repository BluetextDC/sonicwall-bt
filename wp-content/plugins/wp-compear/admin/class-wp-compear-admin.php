<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://davenicosia.com
 * @since      1.0.0
 *
 * @package    WP_Compear
 * @subpackage WP_Compear/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Compear
 * @subpackage WP_Compear/admin
 * @author     Dave Nicosia <email@davenicosia.com>
 */
class WP_Compear_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $WP_Compear    The ID of this plugin.
	 */
	private $WP_Compear;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $WP_Compear       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $WP_Compear, $version ) {

		$this->WP_Compear = $WP_Compear;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Compear_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Compear_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->WP_Compear, plugin_dir_url( __FILE__ ) . 'css/wp-compear-admin.css', array(), $this->version, 'all' );

		wp_enqueue_media();

		$handle = 'wpcompear-fontello.css';
   		$list = 'enqueued';

   		if (wp_style_is( $handle, $list )) {
			return;
		} else {
			wp_enqueue_style( 'wpcompear-fontello.css', plugins_url() . '/wp-compear/includes/fontello-bf24cd3f/css/wpcompear-fontello.css', array(), $this->version, 'all' );
		}

	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Compear_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Compear_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script('jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_enqueue_script( $this->WP_Compear, plugin_dir_url( __FILE__ ) . 'js/wp-compear-admin.js', array( 'jquery' ), $this->version, false );

		$handle = 'TweenMax.min.js';
   		$list = 'enqueued';

   		if (wp_script_is( $handle, $list )) {
			return;
		} else {
			wp_enqueue_script( 'TweenMax.min.js', plugins_url() . '/wp-compear/includes/greensock-js/src/minified/TweenMax.min.js', array( 'jquery' ), $this->version, false );
		}

		$handle = 'jquery.gsap.min.js';
   		$list = 'enqueued';

   		if (wp_script_is( $handle, $list )) {
			return;
		} else {
			wp_enqueue_script( 'jquery.gsap.min.js', plugins_url() . '/wp-compear/includes/greensock-js/src/minified/jquery.gsap.min.js', array( 'jquery' ), $this->version, false );
		}

	}





	public function create_wp_compear_lists() {

    	register_post_type( 'wp-compear-lists',
	        array(
	            'labels' => array(
	                'name' => __('ComPEAR Lists', 'wp-compear'),
	                'singular_name' => __('ComPEAR List', 'wp-compear'),
	                'add_new' => __('Add New', 'wp-compear'),
	                'add_new_item' => __('Add New ComPEAR List', 'wp-compear'),
	                'edit' => __('Edit', 'wp-compear'),
	                'edit_item' => __('Edit ComPEAR List', 'wp-compear'),
	                'new_item' => __('New ComPEAR List', 'wp-compear'),
	                'view' => __('View', 'wp-compear'),
	                'view_item' => __('View ComPEAR List', 'wp-compear'),
	                'search_items' => __('Search ComPEAR Lists', 'wp-compear'),
	                'not_found' => __('No ComPEAR List', 'wp-compear'),
	                'not_found_in_trash' => __('No ComPEAR List found in Trash', 'wp-compear'),
	                'parent' => __('Parent ComPEAR List', 'wp-compear')
	            ),
	 
	            'public' => true,
	            'menu_position' => 6,
	            'rewrite' => true,
	            'supports' => array( 'title' ),
	            'taxonomies' => array( '' ),
	            'menu_icon' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxNi4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSIxNjcuNXB4Ig0KCSBoZWlnaHQ9IjE2Ny41cHgiIHZpZXdCb3g9IjAgMCAxNjcuNSAxNjcuNSIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTY3LjUgMTY3LjUiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPGcgaWQ9IkxheWVyXzEiPg0KCTxnPg0KCQk8cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZmlsbD0iI0YyRjJGMiIgZD0iTTEzMC4yMjksMjQuODY2YzUuMjE2LTIuNzYsMTAuOTYtMy41MywxNi43MTctNC4yMjENCgkJCWMyLjYwOC0wLjMxMyw1LjIwOS0wLjY4OSw3LjgxNC0xLjAzM2MxLjA0Mi0wLjEzNywxLjMxOC0wLjc2MiwwLjg2OS0xLjYxYy0xLjAyMy0xLjkzMi0yLjA0Ni0zLjg3My0zLjIyMS01LjcxMw0KCQkJYy0xLjY3OS0yLjYzLTEuMDc1LTIuMjUtNC4yODQtMC45NzhjLTMuNzQ0LDEuNDg1LTcuNDE1LDMuMTYxLTExLjE0NSw0LjY4NmMtMC4zMzksMC4xMzktMC44MjUsMC4xNjgtMS4yMjksMC4wODINCgkJCWMtMS41NzYsMS4zMzgtMy4yNDksMi41NDEtNC44NTgsMy44NjdjLTIuNjg4LDIuNTk4LTUuNzA0LDQuNzcxLTkuMTUxLDYuMzY4Yy0wLjMzOCwwLjE1Ny0wLjg2OCwwLjA5OC0xLjIzNS0wLjA0OA0KCQkJYy0wLjctMC4yNzctMS4zMy0wLjcyOC0yLjAyLTEuMDM4Yy0zLjE0My0xLjQxNS02LjMzMS0yLjU5NS05Ljg1NC0yLjcyNmMtMy45NjMtMC4xNDctNy44MDksMC4zODUtMTEuNTQ1LDEuNjQ1DQoJCQljLTUuMTQ2LDEuNzM2LTkuNzEzLDQuMzctMTMuMjMyLDguNjQ2Yy0xLjcxNSwyLjA4Ni0zLjQwOSw0LjI1Ni01LjQ0NSw2LjAwMWMtNC4xNzEsMy41NzUtOC40MDQsNy4xMjItMTIuOTA4LDEwLjI1Ng0KCQkJYy02LjUxMiw0LjUzMi0xMy4yNjYsOC43MTktMTkuOTU0LDEyLjk5NmMtNy4zNjUsNC43MS0xNC43MDIsOS40NzctMjAuNDg0LDE2LjE1Yy0yLjM4OSwyLjc1Ni00LjU5OSw1LjczNi02LjQ0OCw4Ljg3NA0KCQkJYy0xLjU2NSwyLjY1NS0yLjU3Niw1LjY1NC0zLjY4Niw4LjU1OGMtMS43NzUsNC42NDEtMi40MTcsOS41MTctMi4zMzgsMTQuNDQzYzAuMTEyLDYuOTQyLDEuNjYyLDEzLjU4MSw0LjcwMSwxOS44NjkNCgkJCWMyLjIzMSw0LjYxNCw0LjY0Niw5LjExOCw3LjY3OSwxMy4yNTFjMi4zNTksMy4yMTcsNC45Myw2LjI2OSw4LjM1Myw4LjQ1NWMzLjAxNiwxLjkyOCw1LjkyNSw0LjA1OCw5LjA4NSw1LjcwOQ0KCQkJYzIuMzgxLDEuMjQ0LDUuMDg1LDEuODczLDcuNjQ0LDIuNzc1Yy0wLjA0OCwwLjE1NS0wLjA5NiwwLjMxMi0wLjE0NSwwLjQ2NmM0LjE5LDEuMTM3LDguMzU0LDIuMzg2LDEyLjU4MiwzLjM2Mg0KCQkJYzIuMTksMC41MDYsNC40OTIsMC43MTUsNi43NDYsMC43M2M0LjEwOSwwLjAyNyw4LjE4MS0wLjQ4NywxMi4xOTgtMS40MjNjNS4xMzctMS4xOTcsOS45NjYtMy4xNTgsMTQuNDg3LTUuODQNCgkJCWMzLjg5Ni0yLjMxMiw3LjQzNi01LjA5NywxMC40MTQtOC41NDNjMi42MTctMy4wMjgsNC45NzktNi4yMzYsNi40NDQtOS45OTVjMS41NjctNC4wMiwyLjkwNS04LjEwMywzLjQwNS0xMi40MzENCgkJCWMwLjQ3OS00LjE1LDEuMTc2LTguMjc2LDEuNjUzLTEyLjQyOGMwLjUyLTQuNSwwLjgyNy05LjAyNSwxLjM1NS0xMy41MjRjMC40Ni0zLjkxLDEuMDYxLTcuODA1LDEuNjY4LTExLjY5NQ0KCQkJYzAuNDc4LTMuMDYsMC44MTItNi4xNzQsMS42NTItOS4xMzljMS4zMzgtNC43MTYsMy4wMTktOS4zMzcsNC42MTMtMTMuOTc4YzAuNC0xLjE2NCwwLjk2MS0yLjI4NCwxLjU1NC0zLjM2Nw0KCQkJYzIuMDMzLTMuNzExLDMuOTg4LTcuMzc2LDQuMDQyLTExLjgzMmMwLjA1NC00LjM3Ni0wLjU2Mi04LjU4Ny0yLjA4LTEyLjY0OWMtMS4wMzQtMi43NjktMi4zNTQtNS40MjUtNC4zMTgtNy42OTUNCgkJCWMtMC43NzctMC45LTAuODgyLTEuODA3LDAuMTItMi41ODJDMTI3LjY3MywyNi42MiwxMjguODcxLDI1LjU4NSwxMzAuMjI5LDI0Ljg2NnoiLz4NCgk8L2c+DQo8L2c+DQo8ZyBpZD0iTGF5ZXJfMiI+DQo8L2c+DQo8L3N2Zz4NCg==',
	            'has_archive' => true
	        )
	    );
	}





	/**
	 * Register the options page mune item for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function WP_Compear_add_menu() {

		add_submenu_page( 'options-general.php', __('WP ComPEAR Plugin', 'wp-compear'), 'WP ComPEAR', 'manage_options', 'wp-compear-options', 'WP_Compear_options_page' );

		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		//add_menu_page( 'WP Compear Plugin', 'WP Compear', 'manage_options', 'wp-compear-options', 'WP_Compear_options_page', '',  );

		/**
		 * Function to include admin display file
		 *
		 * @since    1.0.0
		 */
		function WP_Compear_options_page() {
				include('partials/wp-compear-admin-display.php');
		}

	}


	public function WP_Compear_options() {

	    register_setting( 
	        'WP_Compear_options', // Option group
	        'WP_Compear_options',  // Option name
	        'WP_Compear_options_validate' // Sanitize
	    );

	    // plugin license section
	    add_settings_section(
	        'WP_Compear_general_section', // ID
	        '', // Title
	        'WP_Compear_general_callback', // Callback
	        'wp-compear-options' // Page
	    );
	        // license key field
	        add_settings_field(
	            'WP_Compear_license_field', // ID
	            'License Key', // Title
	            'WP_Compear_license_field_callback', // Callback
	            'wp-compear-options', // Page
	            'WP_Compear_general_section' // Section
	        );


        function WP_Compear_general_callback() {
			//echo '<p>This section is for general settings.</p>';
		}

		function WP_Compear_license_field_callback() {

			$wpcompear_options = get_option('WP_Compear_options');
			$license = $wpcompear_options['wpcompear_license_key'];
			$status = $wpcompear_options['wpcompear_license_status'];

			echo '<span class="description">'.__('Enter your license key here', 'wp-compear').'</span><br />';

			echo '<input name="WP_Compear_options[wpcompear_license_key]" id="" type="text" value="'.$license.'" class="regular-text" />';
			
			wp_nonce_field( 'edd_sample_nonce', 'edd_sample_nonce' );
			?>


			<?php if( false !== $license ) { ?>

				<?php //_e('Activate License'); ?>

				<?php if( $status !== false && $status == 'valid' ) { ?>

					<input type="submit" class="button-secondary" name="edd_license_deactivate" value="<?php _e('Deactivate License', 'wp-compear'); ?>"/>
					<br /><span style="color:green;"><?php _e('Your license is active', 'wp-compear'); ?></span>
					<br /><br />
					<input type="submit" class="button-secondary" name="edd_license_clear" value="<?php _e('Clear License', 'wp-compear'); ?>"/>

				<?php } elseif ( $status !== false && $status == 'invalid' ) { ?>

					<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License', 'wp-compear'); ?>"/>
					<br /><span style="color:red;"><?php _e('Your license is invalid', 'wp-compear'); ?></span>
					<br /><br />
					<input type="submit" class="button-secondary" name="edd_license_clear" value="<?php _e('Clear License', 'wp-compear'); ?>"/>


				<?php } elseif ( $status !== false && $status == 'deactivated' ) { ?>

					<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License', 'wp-compear'); ?>"/>

					<br /><span style="color:red;"><?php _e('Your license has been deactivated', 'wp-compear'); ?></span>
					<br /><br />
					<input type="submit" class="button-secondary" name="edd_license_clear" value="<?php _e('Clear License', 'wp-compear'); ?>"/>

				<?php } else { ?>

					<input type="submit" class="button-secondary" name="edd_license_activate" value="<?php _e('Activate License', 'wp-compear'); ?>"/>

				<?php } ?>

			<?php } ?>

		<?php
		}




		function WP_Compear_options_validate($input) {

			if( ! check_admin_referer( 'edd_sample_nonce', 'edd_sample_nonce' ) )
			return; // get out if we didn't click the Activate button

			$wpcompear_options = get_option('WP_Compear_options');
			$license = $wpcompear_options['wpcompear_license_key'];
			$status = $wpcompear_options['wpcompear_license_status'];

			$clean_field = trim( $input['wpcompear_license_key'] );
	        $license_new = strip_tags( stripslashes( $clean_field ) );

			if( isset( $_POST['edd_license_activate'] ) ) {

				// data to send in our API request
				$api_params = array(
					'edd_action'=> 'activate_license',
					'license' 	=> $license_new,
					'item_name' => urlencode( WPCOMPEAR_ITEM_NAME ), // the name of our product in EDD
					'url'       => home_url()
				);

				// Call the custom API.
				$response = wp_remote_post( WPCOMPEAR_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

				// make sure the response came back okay
				if ( is_wp_error( $response ) )
					return false;

				// decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				$license_check = $license_data->license;


				if($license_check=='valid') {
					delete_option( 'WP_Compear_options' );
					$valid_input['wpcompear_license_key'] = $license_new;
					$valid_input['wpcompear_license_status'] = 'valid';
				}

				else {
					delete_option( 'WP_Compear_options' );
					$valid_input['wpcompear_license_key'] = $license_new;
					$valid_input['wpcompear_license_status'] = 'invalid';
				}

			}


			elseif( isset( $_POST['edd_license_deactivate'] ) ) {

				// data to send in our API request
				$api_params = array(
					'edd_action'=> 'deactivate_license',
					'license' 	=> $license_new,
					'item_name' => urlencode( WPCOMPEAR_ITEM_NAME ), // the name of our product in EDD
					'url'       => home_url()
				);

				// Call the custom API.
				$response = wp_remote_post( WPCOMPEAR_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

				// make sure the response came back okay
				if ( is_wp_error( $response ) )
					return false;

				// decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				$license_check = $license_data->license;

				// $license_data->license will be either "deactivated" or "failed"
				if( $license_check == 'deactivated' ) {
					delete_option( 'WP_Compear_options' );
					$valid_input['wpcompear_license_key'] = $license_new;
					$valid_input['wpcompear_license_status'] = 'deactivated';
				}

			}



			elseif( isset( $_POST['edd_license_clear'] ) ) {
				
				// data to send in our API request
				$api_params = array(
					'edd_action'=> 'check_license',
					'license' 	=> $license_new,
					'item_name' => urlencode( WPCOMPEAR_ITEM_NAME ), // the name of our product in EDD
					'url'       => home_url()
				);

				// Call the custom API.
				$response = wp_remote_post( WPCOMPEAR_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

				// make sure the response came back okay
				if ( is_wp_error( $response ) )
					return false;

				// decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
				$license_check = $license_data->license;

				if($license_check=='valid') {

					// data to send in our API request
					$api_params = array(
						'edd_action'=> 'deactivate_license',
						'license' 	=> $license_new,
						'item_name' => urlencode( WPCOMPEAR_ITEM_NAME ), // the name of our product in EDD
						'url'       => home_url()
					);

					// Call the custom API.
					$response = wp_remote_post( WPCOMPEAR_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

					// make sure the response came back okay
					if ( is_wp_error( $response ) )
						return false;


					// $license_data->license will be either "deactivated" or "failed"
					if( $license_data->license == 'deactivated' ) {
						delete_option( 'WP_Compear_options' );
						$valid_input['wpcompear_license_key'] = '';
						$valid_input['wpcompear_license_status'] = '';
					}

				}

				else {
					delete_option( 'WP_Compear_options' );
					$valid_input['wpcompear_license_key'] = '';
					$valid_input['wpcompear_license_status'] = '';
				}

				
			}


			return $valid_input;

		}

	}





	// set up SEO fields meta boxes
	function wpcompear_create_meta_boxes() {
	    
	    add_meta_box(
	        'wpcompear-basic-settings', // ID
	        'Basic Settings', // Title
	        'wp_compear_basic_callback', // callback
	        'wp-compear-lists', // post type
	        'normal', // context
	        'low' // priority
	    );

	    function wp_compear_basic_callback($post) {

	    	//retrieve the metadata values if they exist
    		$wpcompear_list_type = get_post_meta( $post->ID, '_wpcompear_list_type', true );
    		$wpcompear_list_theme = get_post_meta( $post->ID, '_wpcompear_list_theme', true );

    		$wpcompear_list_id = $post->ID;
			$wpcompear_list_name = $post->title;
			$wpcompear_list_shortcode = '[wp-compear id="'.$wpcompear_list_id.'"]';

			wp_nonce_field( 'wp-compear-callback', 'wp-compear-callback_nonce' );
	    	?>


			<div class="wpcompear-clear"></div>

			<div class="wpcompear-meta-box">

				<?php if($wpcompear_list_type != '') : ?>

					<div class="wpcompear-meta-box--left"><span class="wp-compear-shortcode-copy js-copy-shortcode" data-shortcode='<?php echo $wpcompear_list_shortcode; ?>' data-shortcode-id="<?php echo $wpcompear_list_id; ?>"><?php _e('copy shortcode', 'wp-compear'); ?></span>
						
					</div>
					<div class="wpcompear-meta-box--right">
						<span class="wp-compear-shortcode-text">[wp-compear id="<?php echo $wpcompear_list_id; ?>"]</span>
					</div>

				<?php else: ?>

					<p><?php _e('Your shortcode will appear here after you save a draft or publish this page.', 'wp-compear'); ?></p>

				<?php endif; ?>

				<div class="wpcompear-clear"></div>

			</div>


			<div class="wpcompear-meta-box">

				<div class="wpcompear-meta-box--left">


					<?php if($wpcompear_list_type != '') : ?>

						<select name="wpcompear_list_type" class="js-shortcode-switcher" data-shortcode-id="<?php echo $wpcompear_list_id; ?>">
							<option value="slider" <?php selected( $wpcompear_list_type, 'slider' ); ?>>Slider</option>
							<option value="table" <?php selected( $wpcompear_list_type, 'table' ); ?>>Sortable Table</option>
							<option value="draganddrop" <?php selected( $wpcompear_list_type, 'draganddrop' ); ?>>Drag and Drop Tool</option>
						</select>

					<?php else: ?>

						<select name="wpcompear_list_type" class="js-shortcode-switcher" data-shortcode-id="<?php echo $wpcompear_list_id; ?>">
							<option class="js-shortcode-switcher" value="slider" selected="selected">Slider</option>
							<option class="js-shortcode-switcher" value="table">Sortable Table</option>
							<option class="js-shortcode-switcher" value="draganddrop">Drag and Drop Tool</option>
						</select>

					<?php endif; ?>
					
				</div>
				<div class="wpcompear-meta-box--right">
					<h3><?php _e('WP ComPEAR List Type', 'wp-compear'); ?></h3>
				</div>
				<div class="wpcompear-clear"></div>

			</div>

			<div class="wpcompear-meta-box">

				<div class="wpcompear-meta-box--left">

					<?php if($wpcompear_list_theme != '') : ?>

						<select name="wpcompear_list_theme">
							<option value="wpcompear-theme-light" <?php selected( $wpcompear_list_theme, 'wpcompear-theme-light' ); ?>>Light</option>
							<option value="wpcompear-theme-dark" <?php selected( $wpcompear_list_theme, 'wpcompear-theme-dark' ); ?>>Dark</option>
						</select>

					<?php else: ?>

						<select name="wpcompear_list_theme">
							<option class="js-shortcode-switcher" value="wpcompear-theme-light" selected="selected">Light</option>
							<option class="js-shortcode-switcher" value="wpcompear-theme-dark">Dark</option>
						</select>

					<?php endif; ?>
					
				</div>
				<div class="wpcompear-meta-box--right">
					<h3><?php _e('WP ComPEAR List Theme', 'wp-compear'); ?></h3>
				</div>
				<div class="wpcompear-clear"></div>

			</div>

	    	<?php

	    }

	    
		

	    add_meta_box(
	        'wpcompear-specifications', // ID
	        'Manage Item Specifications', // Title
	        'wp_compear_specifications_callback', // callback
	        'wp-compear-lists', // post type
	        'normal', // context
	        'low' // priority
	    );

	    function wp_compear_specifications_callback($post) {

	    	$current_custom_specs_serialized = get_post_meta( $post->ID, '_wpcompear_custom_specs', true );
	    	$current_custom_specs_unserialized = maybe_unserialize( $current_custom_specs_serialized );
	    	?>

	    	<div class="wpcompear-meta-box">

	    		<p class="wpcompear-alert-text"><?php _e('Create or edit your custom specifications first, then publish or save a draft of this page. After that you will see your new specifications in the "Products" section below. After making any changes here, update this page to see them reflected in the products below.', 'wp-compear'); ?></p>

				<table id="wp-compear-table_specs" class="wp-compear-table" cellspacing="0">

					<thead>
						<tr>
							<th class="spec-name"><span><?php _e('Name', 'wp-compear'); ?></span></th>
							<th class="spec-type"><span><?php _e('Type', 'wp-compear'); ?></span></th>
							<th class="th-sort"><span><?php _e('Actions', 'wp-compear'); ?></span></th>
						</tr>
					</thead>

					<tbody id="wpcompear-specs-tbody" class="ui-sortable">

					<?php if(is_array($current_custom_specs_unserialized)) : // loop through all custom specs if they exist ?>


						<?php foreach($current_custom_specs_unserialized as $current_custom_spec) : ?>

							<tr>

								<td>
									<input name="wpcompear_custom_specs[spec_id][]" id="" type="hidden" value="<?php echo $current_custom_spec['spec_id']; ?>" class="regular-text" value="">
									<input name="wpcompear_custom_specs[spec_name][]" id="" type="text" value="<?php echo $current_custom_spec['spec_name']; ?>" class="regular-text" value="">
								</td>

								<td>

									<?php 

									$spec_type = $current_custom_spec['spec_type'];

									if($spec_type=='text-field') {
										$spec_type_chosen = __('Plain Text', 'wp-compear');
									}
									else if($spec_type=='text-paragraph') {
										$spec_type_chosen = __('Paragraph', 'wp-compear');
									}
									else if($spec_type=='image') {
										$spec_type_chosen = __('Image', 'wp-compear');
									}
									else if($spec_type=='star-rating') {
										$spec_type_chosen = __('Star Rating', 'wp-compear');
									}
									else if($spec_type=='wysiwyg') {
										$spec_type_chosen = __('WYSIWYG', 'wp-compear');
									}
									else if($spec_type=='yes-no') {
										$spec_type_chosen = __('Yes/No', 'wp-compear');
									}

									?>

									<input name="wpcompear_custom_specs[spec_type][]" id="" type="hidden" value="<?php echo $spec_type; ?>" class="regular-text" value="">

									<span class="spec-type">( <?php echo $spec_type_chosen; ?> )</span>
						
								</td>

								<td class="actions-td">
									<span class="js-sort-handle">&#8645;</span>
									<i class="fi-x-circle js-delete-spec" data-spec-id="<?php echo $current_custom_spec['spec_id']; ?>"></i>
								</td>

							</tr>

						<?php endforeach; ?>


					<?php else: // give one default row to begin with if new post ?>

							<tr>

								<td>
									<input name="wpcompear_custom_specs[spec_id][]" id="" type="hidden" value="1272509100" class="regular-text" value="">
									<input name="wpcompear_custom_specs[spec_name][]" id="" type="text" value="Custom Product Spec 1" class="regular-text" value="">
								</td>

								<td>
									<select name="wpcompear_custom_specs[spec_type][]" id="">
										<option value="text-field" selected="selected">Simple Text</option>
										<option value="text-paragraph">Paragraph Text</option>
										<option value="image">Image</option>
										<option value="wysiwyg">WYSIWYG</option>
										<option value="star-rating">Star Rating</option>
										<option value="yes-no">Yes/No</option>
									</select>
								</td>

								<td class="actions-td">
									<span class="js-sort-handle">&#8645;</span>
									<i class="fi-x-circle js-delete-spec" data-spec-id="1272509100"></i>
								</td>

							</tr>

					<?php endif; ?>

						
					</tbody>

				</table>

				<div class="wpcompear-meta-box-footer">
					<input type="button" name="" id="js-add-spec" class="button button-seconary" value="Add Another Specification">
				</div>

			</div>

	    	<?php

	    }



	    add_meta_box(
	        'wpcompear-products', // ID
	        'Manage ComPEAR Items', // Title
	        'wp_compear_products_callback', // callback
	        'wp-compear-lists', // post type
	        'normal', // context
	        'low' // priority
	    );

	    function wp_compear_products_callback($post) {

	    	$current_custom_specs_serialized = get_post_meta( $post->ID, '_wpcompear_custom_specs', true );
	    	$current_custom_specs_unserialized = maybe_unserialize( $current_custom_specs_serialized );

	    	?>

	    	<div class="wpcompear-meta-box">

		    	<p><?php _e('Create or edit your custom specifications above first, then publish or save a draft of this page. After that you will see your new specifications in this section.', 'wp-compear'); ?></p>
		    	<p>To see more columns in one view, click the "screen options" tab in the upper left and set this page to 1 column display.</p>

		    	<div class="wpcompear-table-wrapper">

		    	<input type="checkbox" value="close-to-sort" id="js-collapse-expand-row" /> <label>Check here to collapse rows for easy sorting</label>
		    	<br />
		    	<br />

		    	<table id="wp-compear-table_products" class="wp-compear-table" cellspacing="0">


		    	<?php if(is_array($current_custom_specs_unserialized)) : // loop through all custom specs if they exist ?>

		    		<thead>
		    			<tr>
		    				<th><span><?php _e('Actions', 'wp-compear'); ?></span></th>
							<?php foreach($current_custom_specs_unserialized as $current_custom_spec) : ?>
								<th class="product-spec-name<?php if($current_custom_spec['spec_type']=='wysiwyg'){echo ' wysiwyg-width';} ?>" data-spec-id="<?php echo $current_custom_spec['spec_id']; ?>"><span><?php echo $current_custom_spec['spec_name']; ?></span></th>
							<?php endforeach; ?>
						</tr>
		    		</thead>

				<?php endif; ?>

		    		<?php
	    			$current_custom_products_serialized = get_post_meta( $post->ID, '_wpcompear_list_products', true );
    				$current_custom_products_unserialized = maybe_unserialize( base64_decode( $current_custom_products_serialized ) );
    				$prods_count = count($current_custom_products_unserialized);

    				//echo '<pre>$current_custom_products_unserialized = '.print_r($current_custom_products_unserialized, true).'</pre><hr /><hr /><br />';
    				?>

		    		<tbody id="wpcompear-products-tbody" class="ui-sortable" data-product-counter="<?php echo $prods_count; ?>">

	    				<?php if(is_array($current_custom_products_unserialized)) : // loop through all custom specs if they exist ?>

							<?php 

							$m=0;

							foreach($current_custom_products_unserialized as $current_custom_product) : ?>

									<tr<?php if($m==0){echo' class="hidden-clone"';} ?>>

									<?php //echo '<pre>$current_custom_products_unserialized = '.print_r($current_custom_products_unserialized, true).'</pre>'; ?>

										<?php if(is_array($current_custom_specs_unserialized)) : // loop through all custom specs if they exist ?>


											<td class="actions-td">
												<span class="js-sort-handle" title="Sort Rows">&#8645;</span>
												<i class="fi-x-circle js-delete-product"></i>
											</td>

											<?php $n=0; $z=1; foreach($current_custom_specs_unserialized as $current_custom_spec) : ?>


												<?php 

												//echo '<pre>'.print_r($current_custom_spec,1).'<pre>';

												$spec_type = $current_custom_spec['spec_type'];
												$spec_name = $current_custom_spec['spec_name'];
												$spec_id = $current_custom_spec['spec_id'];
												?>

												<?php if($spec_type=='text-field') : ?>

													<?php 
													if( isset( $current_custom_products_unserialized[$m][$spec_id] ) ) {
														$spec_value = stripslashes($current_custom_products_unserialized[$m][$spec_id]); 
													}
													else {
														$spec_value = '';
													}
													?>

													<td class="product-spec-value" data-spec-id="<?php echo $spec_id ?>">
													<div class="product-spec-value-inner">
														<input name="wpcompear_list_products[<?php echo $spec_id; ?>][]" id="" type="text" value="<?php echo $spec_value; ?>" class="regular-text">
													</div>
													</td>

												<?php elseif($spec_type=='text-paragraph') : ?>

													<?php 
													if( isset( $current_custom_products_unserialized[$m][$spec_id] ) ) {
														$spec_value = stripslashes($current_custom_products_unserialized[$m][$spec_id]); 
													}
													else {
														$spec_value = '';
													}
													?>

													<td class="product-spec-value" data-spec-id="<?php echo $spec_id ?>">
													<div class="product-spec-value-inner">
														<textarea name="wpcompear_list_products[<?php echo $spec_id; ?>][]" id="" type="text" class="regular-text"><?php echo $spec_value; ?></textarea>
													</div>
													</td>

												<?php elseif($spec_type=='image') : ?>

													<td class="product-spec-value" data-spec-id="<?php echo $spec_id ?>">
													<div class="product-spec-value-inner">

														<div class="slide-img-prev-wrapper">

														<?php if( isset( $current_custom_products_unserialized[$m][$spec_id] ) && $m != 0 ): ?>
															<span class="dashicons dashicons-sos wpcompear-prev-img active"></span>
															<div class="img-preview">
																<img src="<?php echo $current_custom_products_unserialized[$m][$spec_id]; ?>" alt="" />
															</div>
														<?php else: ?>

															<span class="dashicons dashicons-sos wpcompear-prev-img inactive"></span>
															<div class="img-preview">
																<img src="" alt="" />
															</div>


														<?php endif; ?>
															<div class="wp-compear-clear"></div>
														</div>

														<?php
														if( isset( $current_custom_products_unserialized[$m][$spec_id] ) && $m != 0 ) {
															$image_value = $current_custom_products_unserialized[$m][$spec_id];
														}
														else {
															$image_value = '';
														}

														?>

														<input name="wpcompear_list_products[<?php echo $spec_id; ?>][]" id="" type="hidden" value="<?php echo $image_value; ?>" class="regular-text hidden-image-url">
														<input type="button" name="submit" id="submit" class="button button-secondary upload_wpcompear_image_button" value="Add Image">
													</div>
													</td>

												<?php elseif($spec_type=='star-rating') : ?>

													<td class="product-spec-value" data-spec-id="<?php echo $spec_id ?>">
													<div class="product-spec-value-inner">

														<?php 
														if( isset( $current_custom_products_unserialized[$m][$spec_id] ) ) {
															$spec_value = stripslashes($current_custom_products_unserialized[$m][$spec_id]); 
														}
														else {
															$spec_value = '';
														}
														?>

														<select name="wpcompear_list_products[<?php echo $spec_id; ?>][]" id="">
															<option value="no-rating" <?php selected( $spec_value, 'no-rating' ); ?>><?php _e('No Ratings Yet', 'wp-compear'); ?></option>
															<option value="0" <?php selected( $spec_value, '0' ); ?>>0</option>
															<option value="0.1" <?php selected( $spec_value, '0.1' ); ?>>0.1</option>
															<option value="0.2" <?php selected( $spec_value, '0.2' ); ?>>0.2</option>
															<option value="0.3" <?php selected( $spec_value, '0.3' ); ?>>0.3</option>
															<option value="0.4" <?php selected( $spec_value, '0.4' ); ?>>0.4</option>
															<option value="0.5" <?php selected( $spec_value, '0.5' ); ?>>0.5</option>
															<option value="0.6" <?php selected( $spec_value, '0.6' ); ?>>0.6</option>
															<option value="0.7" <?php selected( $spec_value, '0.7' ); ?>>0.7</option>
															<option value="0.8" <?php selected( $spec_value, '0.8' ); ?>>0.8</option>
															<option value="0.9" <?php selected( $spec_value, '0.9' ); ?>>0.9</option>
															<option value="1" <?php selected( $spec_value, '1' ); ?>>1</option>
															<option value="1.1" <?php selected( $spec_value, '1.1' ); ?>>1.1</option>
															<option value="1.2" <?php selected( $spec_value, '1.2' ); ?>>1.2</option>
															<option value="1.3" <?php selected( $spec_value, '1.3' ); ?>>1.3</option>
															<option value="1.4" <?php selected( $spec_value, '1.4' ); ?>>1.4</option>
															<option value="1.5" <?php selected( $spec_value, '1.5' ); ?>>1.5</option>
															<option value="1.6" <?php selected( $spec_value, '1.6' ); ?>>1.6</option>
															<option value="1.7" <?php selected( $spec_value, '1.7' ); ?>>1.7</option>
															<option value="1.8" <?php selected( $spec_value, '1.8' ); ?>>1.8</option>
															<option value="1.9" <?php selected( $spec_value, '1.9' ); ?>>1.9</option>
															<option value="2" <?php selected( $spec_value, '2' ); ?>>2</option>
															<option value="2.1" <?php selected( $spec_value, '2.1' ); ?>>2.1</option>
															<option value="2.2" <?php selected( $spec_value, '2.2' ); ?>>2.2</option>
															<option value="2.3" <?php selected( $spec_value, '2.3' ); ?>>2.3</option>
															<option value="2.4" <?php selected( $spec_value, '2.4' ); ?>>2.4</option>
															<option value="2.5" <?php selected( $spec_value, '2.5' ); ?>>2.5</option>
															<option value="2.6" <?php selected( $spec_value, '2.6' ); ?>>2.6</option>
															<option value="2.7" <?php selected( $spec_value, '2.7' ); ?>>2.7</option>
															<option value="2.8" <?php selected( $spec_value, '2.8' ); ?>>2.8</option>
															<option value="2.9" <?php selected( $spec_value, '2.9' ); ?>>2.9</option>
															<option value="3" <?php selected( $spec_value, '3' ); ?>>3</option>
															<option value="3.1" <?php selected( $spec_value, '3.1' ); ?>>3.1</option>
															<option value="3.2" <?php selected( $spec_value, '3.2' ); ?>>3.2</option>
															<option value="3.3" <?php selected( $spec_value, '3.3' ); ?>>3.3</option>
															<option value="3.4" <?php selected( $spec_value, '3.4' ); ?>>3.4</option>
															<option value="3.5" <?php selected( $spec_value, '3.5' ); ?>>3.5</option>
															<option value="3.6" <?php selected( $spec_value, '3.6' ); ?>>3.6</option>
															<option value="3.7" <?php selected( $spec_value, '3.7' ); ?>>3.7</option>
															<option value="3.8" <?php selected( $spec_value, '3.8' ); ?>>3.8</option>
															<option value="3.9" <?php selected( $spec_value, '3.9' ); ?>>3.9</option>
															<option value="4" <?php selected( $spec_value, '4' ); ?>>4</option>
															<option value="4.1" <?php selected( $spec_value, '4.1' ); ?>>4.1</option>
															<option value="4.2" <?php selected( $spec_value, '4.2' ); ?>>4.2</option>
															<option value="4.3" <?php selected( $spec_value, '4.3' ); ?>>4.3</option>
															<option value="4.4" <?php selected( $spec_value, '4.4' ); ?>>4.4</option>
															<option value="4.5" <?php selected( $spec_value, '4.5' ); ?>>4.5</option>
															<option value="4.6" <?php selected( $spec_value, '4.6' ); ?>>4.6</option>
															<option value="4.7" <?php selected( $spec_value, '4.7' ); ?>>4.7</option>
															<option value="4.8" <?php selected( $spec_value, '4.8' ); ?>>4.8</option>
															<option value="4.9" <?php selected( $spec_value, '4.9' ); ?>>4.9</option>
															<option value="5" <?php selected( $spec_value, '5' ); ?>>5</option>
														</select>
													</div>
													</td>


												<?php elseif($spec_type=='wysiwyg') : ?>

													<td>
													<div class="product-spec-value-inner">

														<?php
														           // wpcompear_prod_editor_COLUMN_ROW
												        $editor_id = 'wpcompear_prod_editor_'.$z.'_'.$m;

														if( isset( $current_custom_products_unserialized[$m][$spec_id] ) ) {
															$editor_content = stripslashes($current_custom_products_unserialized[$m][$spec_id]);
														}
														else {
															$editor_content = '';
														}
												        
												        $settings = array( 
												        	'media_buttons' => true,
												        	'editor_height' => 20,
												        	'textarea_name' => 'wpcompear_list_products['.$spec_id.'][]'
												        );
												        wp_editor( $editor_content, $editor_id, $settings );
												        ?>

												    </div>
													</td>


												<?php elseif($spec_type=='yes-no') : ?>

													<?php 
													if( isset( $current_custom_products_unserialized[$m][$spec_id] ) ) {
														$spec_value = stripslashes($current_custom_products_unserialized[$m][$spec_id]); 
													}
													else {
														$spec_value = '';
													}
													?>


													<td class="product-spec-value" data-spec-id="<?php echo $spec_id ?>">
													<div class="product-spec-value-inner">

														<select name="wpcompear_list_products[<?php echo $spec_id; ?>][]" class="select-field">
															<option value="yes" <?php selected( $spec_value, 'yes' ); ?>><?php _e('Yes', 'wp-compear'); ?></option>
															<option value="no" <?php selected( $spec_value, 'no' ); ?>><?php _e('No', 'wp-compear'); ?></option>
														</select>

													</div>
													</td>


												<?php endif; $n++; $z++; ?>

											<?php endforeach; ?>

										<?php endif; ?>

									</tr>


							<?php $m++; endforeach; ?>


						<?php else: ?>

							<!-- hidden row for cloning in .js only -->
							<tr class="hidden-clone">

								<td class="product-spec-value" data-spec-id="1272509100">
									<input name="wpcompear_list_products[1272509100][]" id="" type="text" value="" class="regular-text">
								</td>

								<td class="actions-td">
									<span class="js-sort-handle">&#8645;</span>
									<i class="fi-x-circle js-delete-product"></i>
								</td>


							</tr>
							<!-- end hidden row for cloning in .js only -->

						<?php endif; ?>


		    		</tbody>

		    	</table>

		    	<!-- <div class="slider-side-shadow"></div> -->

		    	</div>


		    	<?php if(is_array($current_custom_products_unserialized)) : // loop through all custom specs if they exist ?>
					<div class="wpcompear-meta-box-footer">
						<input type="button" name="" id="js-add-product" class="button button-seconary" value="Add Another Product">
					</div>
				<?php endif; ?>

			</div>


	    	<?php

	    }



	    add_meta_box(
	        'wpcompear-slider-settings', // ID
	        'Slider Settings', // Title
	        'wp_compear_slider_callback', // callback
	        'wp-compear-lists', // post type
	        'normal', // context
	        'low' // priority
	    );

	    function wp_compear_slider_callback($post) {

	    	$wpcompear_list_slider_show_lg = get_post_meta( $post->ID, '_wpcompear_list_slider_show_lg', true );
	    	$wpcompear_list_slider_scroll_lg = get_post_meta( $post->ID, '_wpcompear_list_slider_scroll_lg', true );
			$wpcompear_list_slider_prevnext_lg = get_post_meta( $post->ID, '_wpcompear_list_slider_prevnext_lg', true );
			$wpcompear_list_slider_counter_lg = get_post_meta( $post->ID, '_wpcompear_list_slider_counter_lg', true );

	    	$wpcompear_list_slider_show_md = get_post_meta( $post->ID, '_wpcompear_list_slider_show_md', true );
	    	$wpcompear_list_slider_scroll_md = get_post_meta( $post->ID, '_wpcompear_list_slider_scroll_md', true );
	    	$wpcompear_list_slider_prevnext_md = get_post_meta( $post->ID, '_wpcompear_list_slider_prevnext_md', true );
	    	$wpcompear_list_slider_counter_md = get_post_meta( $post->ID, '_wpcompear_list_slider_counter_md', true );

	    	$wpcompear_list_slider_specname_show = get_post_meta( $post->ID, '_wpcompear_list_slider_specname_show', true );

	    	$wpcompear_list_col_alignment_slider = get_post_meta( $post->ID, '_wpcompear_list_col_alignment_slider', true );
	    	?>


	    	<div class="wpcompear-meta-box wpcompear-slider-settings">

	    		<p><?php _e('Settings for All Screen Sizes', 'wp-compear'); ?></p>

	    		<div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_slider_prevnext_lg">
						<option value="on" <?php selected( $wpcompear_list_slider_prevnext_lg, 'on' ); ?>><?php _e('On', 'wp-compear'); ?></option>
						<option value="off" <?php selected( $wpcompear_list_slider_prevnext_lg, 'off' ); ?>><?php _e('Off', 'wp-compear'); ?></option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3><?php _e('Previous &amp; Next Buttons', 'wp-compear'); ?></h3>
				</div>

				<div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_slider_counter_lg">
						<option value="on" <?php selected( $wpcompear_list_slider_counter_lg, 'on' ); ?>><?php _e('On', 'wp-compear'); ?></option>
						<option value="off" <?php selected( $wpcompear_list_slider_counter_lg, 'off' ); ?>><?php _e('Off', 'wp-compear'); ?></option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3><?php _e('Slide Counter', 'wp-compear'); ?></h3>
				</div>

				<div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_slider_specname_show">
						<option value="yes" <?php selected( $wpcompear_list_slider_specname_show, 'yes' ); ?>><?php _e('Yes', 'wp-compear'); ?></option>
						<option value="no" <?php selected( $wpcompear_list_slider_specname_show, 'no' ); ?>><?php _e('No', 'wp-compear'); ?></option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3><?php _e('Show Specification Name in Slider', 'wp-compear'); ?></h3>
				</div>

				<div class="wpcompear-clear"></div>

				<div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_col_alignment_slider">
						<option value="wpcompear_col_left" <?php selected( $wpcompear_list_col_alignment_slider, 'wpcompear_col_left' ); ?>><?php _e('Left', 'wp-compear'); ?></option>
						<option value="wpcompear_col_center" <?php selected( $wpcompear_list_col_alignment_slider, 'wpcompear_col_center' ); ?>><?php _e('Center', 'wp-compear'); ?></option>
						<option value="wpcompear_col_right" <?php selected( $wpcompear_list_col_alignment_slider, 'wpcompear_col_right' ); ?>><?php _e('Right', 'wp-compear'); ?></option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3><?php _e('Slide Alignment', 'wp-compear'); ?></h3>
				</div>

				<div class="wpcompear-clear"></div>
				
			</div>

			<div class="wpcompear-meta-box wpcompear-slider-settings">

	    		<p><?php _e('Settings for Large Screen Sizes (laptops &amp; desktop screens)', 'wp-compear'); ?></p>

	    		<div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_slider_show_lg">
						<option value="1" <?php selected( $wpcompear_list_slider_show_lg, '1' ); ?>>1</option>
						<option value="2" <?php selected( $wpcompear_list_slider_show_lg, '2' ); ?>>2</option>
						<option value="3" <?php selected( $wpcompear_list_slider_show_lg, '3' ); ?>>3</option>
						<option value="4" <?php selected( $wpcompear_list_slider_show_lg, '4' ); ?>>4</option>
						<option value="5" <?php selected( $wpcompear_list_slider_show_lg, '5' ); ?>>5</option>
						<option value="6" <?php selected( $wpcompear_list_slider_show_lg, '6' ); ?>>6</option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3><?php _e('Number of Slides to Show', 'wp-compear'); ?></h3>
				</div>

				<div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_slider_scroll_lg">
						<option value="1" <?php selected( $wpcompear_list_slider_scroll_lg, '1' ); ?>>1</option>
						<option value="2" <?php selected( $wpcompear_list_slider_scroll_lg, '2' ); ?>>2</option>
						<option value="3" <?php selected( $wpcompear_list_slider_scroll_lg, '3' ); ?>>3</option>
						<option value="4" <?php selected( $wpcompear_list_slider_scroll_lg, '4' ); ?>>4</option>
						<option value="5" <?php selected( $wpcompear_list_slider_scroll_lg, '5' ); ?>>5</option>
						<option value="6" <?php selected( $wpcompear_list_slider_scroll_lg, '6' ); ?>>6</option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3><?php _e('Number of Slides to Scroll', 'wp-compear'); ?></h3>
				</div>

				<!-- <div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_slider_prevnext_lg">
						<option value="on" <?php selected( $wpcompear_list_slider_prevnext_lg, 'on' ); ?>>On</option>
						<option value="off" <?php selected( $wpcompear_list_slider_prevnext_lg, 'off' ); ?>>Off</option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3>Previous &amp; Next Buttons</h3>
				</div>

				<div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_slider_counter_lg">
						<option value="on" <?php selected( $wpcompear_list_slider_counter_lg, 'on' ); ?>>On</option>
						<option value="off" <?php selected( $wpcompear_list_slider_counter_lg, 'off' ); ?>>Off</option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3>Slide Counter</h3>
				</div> -->

				<div class="wpcompear-clear"></div>

			</div>



			<div class="wpcompear-meta-box wpcompear-slider-settings">

				<p><?php _e('Settings for Medium Screen Sizes (tablets)', 'wp-compear'); ?></p>

	    		<div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_slider_show_md">
						<option value="1" <?php selected( $wpcompear_list_slider_show_md, '1' ); ?>>1</option>
						<option value="2" <?php selected( $wpcompear_list_slider_show_md, '2' ); ?>>2</option>
						<option value="3" <?php selected( $wpcompear_list_slider_show_md, '3' ); ?>>3</option>
						<option value="4" <?php selected( $wpcompear_list_slider_show_md, '4' ); ?>>4</option>
						<option value="5" <?php selected( $wpcompear_list_slider_show_md, '5' ); ?>>5</option>
						<option value="6" <?php selected( $wpcompear_list_slider_show_md, '6' ); ?>>6</option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3><?php _e('Number of Slides to Show', 'wp-compear'); ?></h3>
				</div>

				<div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_slider_scroll_md">
						<option value="1" <?php selected( $wpcompear_list_slider_scroll_md, '1' ); ?>>1</option>
						<option value="2" <?php selected( $wpcompear_list_slider_scroll_md, '2' ); ?>>2</option>
						<option value="3" <?php selected( $wpcompear_list_slider_scroll_md, '3' ); ?>>3</option>
						<option value="4" <?php selected( $wpcompear_list_slider_scroll_md, '4' ); ?>>4</option>
						<option value="5" <?php selected( $wpcompear_list_slider_scroll_md, '5' ); ?>>5</option>
						<option value="6" <?php selected( $wpcompear_list_slider_scroll_md, '6' ); ?>>6</option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3><?php _e('Number of Slides to Scroll', 'wp-compear'); ?></h3>
				</div>

				<!-- <div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_slider_prevnext_md">
						<option value="on" <?php selected( $wpcompear_list_slider_prevnext_md, 'on' ); ?>>On</option>
						<option value="off" <?php selected( $wpcompear_list_slider_prevnext_md, 'off' ); ?>>Off</option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3>Previous &amp; Next Buttons</h3>
				</div>

				<div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_slider_counter_md">
						<option value="on" <?php selected( $wpcompear_list_slider_counter_md, 'on' ); ?>>On</option>
						<option value="off" <?php selected( $wpcompear_list_slider_counter_md, 'off' ); ?>>Off</option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3>Slide Counter</h3>
				</div> -->

				<div class="wpcompear-clear"></div>
				
			</div>



			<div class="wpcompear-meta-box wpcompear-slider-settings">

				<p><?php _e('Settings for Small Screen Sizes (mobile devices)', 'wp-compear'); ?></p>
				<p class="small-text">* <?php _e('There are not currently any settings supported for smaller screen sizes. The slider is already optimized for this.', 'wp-compear'); ?></p>

	    		<!-- <div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_slider_mobile">
						<option value="on" <?php selected( $wpcompear_list_slider_show_lg, 'on' ); ?>>On</option>
						<option value="off" <?php selected( $wpcompear_list_slider_show_lg, 'off' ); ?>>Off</option>
					</select>
	    		</div>
						
				<div class="wpcompear-meta-box--right">
					<h3>Turn the Slider On and Off</h3>
				</div> -->

				<div class="wpcompear-clear"></div>

	    	</div>

	    <?php

	    }



	    add_meta_box(
	        'wpcompear-table-settings', // ID
	        'Sortable Table Settings', // Title
	        'wp_compear_table_callback', // callback
	        'wp-compear-lists', // post type
	        'normal', // context
	        'low' // priority
	    );


	    function wp_compear_table_callback($post) {

	    	$wpcompear_list_sortable_check = get_post_meta( $post->ID, '_wpcompear_list_sortable_check', true );

	    	$wpcompear_list_hover_check = get_post_meta( $post->ID, '_wpcompear_list_hover_check', true );

	    	$current_custom_specs_serialized = get_post_meta( $post->ID, '_wpcompear_custom_specs', true );
	    	$current_custom_specs_unserialized = maybe_unserialize( $current_custom_specs_serialized );

	    	$current_wpcompear_list_col_width = get_post_meta( $post->ID, '_wpcompear_list_col_width', true );
	    	$current_wpcompear_list_col_width_unserialized = maybe_unserialize( $current_wpcompear_list_col_width );

	    	$current_wpcompear_list_col_alignment = get_post_meta( $post->ID, '_wpcompear_list_col_alignment', true );
	    	$current_wpcompear_list_col_alignment_unserialized = maybe_unserialize( $current_wpcompear_list_col_alignment );

	    	$current_wpcompear_list_col_alignment_vert = get_post_meta( $post->ID, '_wpcompear_list_col_alignment_vert', true );
	    	$current_wpcompear_list_col_alignment_vert_unserialized = maybe_unserialize( $current_wpcompear_list_col_alignment_vert );
	    	?>

	    	<div class="wpcompear-meta-box wpcompear-table-settings">

	    		<!-- <p><?php _e('Choose whether or not this table is sortable', 'wp-compear'); ?></p> -->

	    		<div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_sortable_check">
						<option value="yes" <?php selected( $wpcompear_list_sortable_check, 'yes' ); ?>><?php _e('Yes', 'wp-compear'); ?></option>
						<option value="no" <?php selected( $wpcompear_list_sortable_check, 'no' ); ?>><?php _e('No', 'wp-compear'); ?></option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3><?php _e('Make Table Sortable', 'wp-compear'); ?></h3>
				</div>

				<div class="wpcompear-clear"></div>

				<div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_hover_check">
						<option value="yes" <?php selected( $wpcompear_list_hover_check, 'yes' ); ?>><?php _e('Yes', 'wp-compear'); ?></option>
						<option value="no" <?php selected( $wpcompear_list_hover_check, 'no' ); ?>><?php _e('No', 'wp-compear'); ?></option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3><?php _e('Give Table Rows Background Color on Hover', 'wp-compear'); ?></h3>
				</div>

				<div class="wpcompear-clear"></div>
				
			</div>



			<div class="wpcompear-meta-box wpcompear-table-settings">

				<p><?php _e('Set the width of each column in your table. Make sure they add up to 100% or your table will not work properly.', 'wp-compear'); ?></p>

				<table id="wp-compear-table_set_widths" class="wp-compear-table" cellspacing="0">

					<thead>
						<tr>
							<th class="spec-name"><span><?php _e('Column Title', 'wp-compear'); ?></span></th>
							<th class="spec-type"><span><?php _e('Width', 'wp-compear'); ?></span></th>
							<th class="spec-type"><span><?php _e('Horizontal Alignment', 'wp-compear'); ?></span></th>
							<th class="spec-type"><span><?php _e('Vertical Alignment', 'wp-compear'); ?></span></th>
						</tr>
					</thead>

					<tbody id="wpcompear-specs-tbody" class="ui-sortable">

					<?php if(is_array($current_custom_specs_unserialized)) : // loop through all custom specs if they exist ?>


						<?php foreach($current_custom_specs_unserialized as $current_custom_spec) : ?>


							<tr>

								<td>
									<span><?php echo $current_custom_spec['spec_name']; ?></span>
								</td>

								<td>

									<?php 
									$spec_id = $current_custom_spec['spec_id']; 

									if( isset( $current_wpcompear_list_col_width_unserialized[$spec_id] ) ) {
										$current_spec_id = $current_wpcompear_list_col_width_unserialized[$spec_id];
									}
									else {
										$current_spec_id = '';
									}
									?>

									<select name="wpcompear_list_col_width[<?php echo $current_custom_spec['spec_id']; ?>]">
										<option value="wpcompear_col_10" <?php selected( $current_spec_id, 'wpcompear_col_10' ); ?>>10%</option>
										<option value="wpcompear_col_15" <?php selected( $current_spec_id, 'wpcompear_col_15' ); ?>>15%</option>
										<option value="wpcompear_col_20" <?php selected( $current_spec_id, 'wpcompear_col_20' ); ?>>20%</option>
										<option value="wpcompear_col_25" <?php selected( $current_spec_id, 'wpcompear_col_25' ); ?>>25%</option>
										<option value="wpcompear_col_30" <?php selected( $current_spec_id, 'wpcompear_col_30' ); ?>>30%</option>
										<option value="wpcompear_col_35" <?php selected( $current_spec_id, 'wpcompear_col_35' ); ?>>35%</option>
										<option value="wpcompear_col_40" <?php selected( $current_spec_id, 'wpcompear_col_40' ); ?>>40%</option>
										<option value="wpcompear_col_45" <?php selected( $current_spec_id, 'wpcompear_col_45' ); ?>>45%</option>
										<option value="wpcompear_col_50" <?php selected( $current_spec_id, 'wpcompear_col_50' ); ?>>50%</option>
										<option value="wpcompear_col_55" <?php selected( $current_spec_id, 'wpcompear_col_55' ); ?>>55%</option>
										<option value="wpcompear_col_60" <?php selected( $current_spec_id, 'wpcompear_col_60' ); ?>>60%</option>
										<option value="wpcompear_col_65" <?php selected( $current_spec_id, 'wpcompear_col_65' ); ?>>65%</option>
										<option value="wpcompear_col_70" <?php selected( $current_spec_id, 'wpcompear_col_70' ); ?>>70%</option>
										<option value="wpcompear_col_75" <?php selected( $current_spec_id, 'wpcompear_col_75' ); ?>>75%</option>
										<option value="wpcompear_col_80" <?php selected( $current_spec_id, 'wpcompear_col_80' ); ?>>80%</option>
										<option value="wpcompear_col_85" <?php selected( $current_spec_id, 'wpcompear_col_85' ); ?>>85%</option>
										<option value="wpcompear_col_90" <?php selected( $current_spec_id, 'wpcompear_col_90' ); ?>>90%</option>
										<option value="wpcompear_col_95" <?php selected( $current_spec_id, 'wpcompear_col_95' ); ?>>95%</option>
										<option value="wpcompear_col_100" <?php selected( $current_spec_id, 'wpcompear_col_100' ); ?>>100%</option>
									</select>
						
								</td>

								<td>

									<?php $spec_id = $current_custom_spec['spec_id']; ?>

									<?php 
									if($current_wpcompear_list_col_alignment_unserialized!=''): // check to see if value exists because this is an added option 

										if( isset( $current_wpcompear_list_col_alignment_unserialized[$spec_id] ) ) {
											$current_spec_alignment = $current_wpcompear_list_col_alignment_unserialized[$spec_id];
										}
										else {
											$current_spec_alignment = 'wpcompear_col_center';
										}
										?>

										<select name="wpcompear_list_col_alignment[<?php echo $current_custom_spec['spec_id']; ?>]">
											<option value="wpcompear_col_left" <?php selected( $current_spec_alignment, 'wpcompear_col_left' ); ?>><?php _e('Left', 'wp-compear'); ?></option>
											<option value="wpcompear_col_center" <?php selected( $current_spec_alignment, 'wpcompear_col_center' ); ?>><?php _e('Center', 'wp-compear'); ?></option>
											<option value="wpcompear_col_right" <?php selected( $current_spec_alignment, 'wpcompear_col_right' ); ?>><?php _e('Right', 'wp-compear'); ?></option>
										</select>

									<?php else: ?>


										<select name="wpcompear_list_col_alignment[<?php echo $current_custom_spec['spec_id']; ?>]">
											<option value="wpcompear_col_left"><?php _e('Left', 'wp-compear'); ?></option>
											<option value="wpcompear_col_center" selected="selected"><?php _e('Center', 'wp-compear'); ?></option>
											<option value="wpcompear_col_right"><?php _e('Right', 'wp-compear'); ?></option>
										</select>

									<?php endif; ?>
						
								</td>



								<td>

									<?php $spec_id = $current_custom_spec['spec_id']; ?>

									<?php 
									if( isset( $current_wpcompear_list_col_alignment_vert_unserialized[$spec_id] ) ): // check to see if value exists because this is an added option 

										if( isset( $current_wpcompear_list_col_alignment_vert_unserialized[$spec_id] ) ) {
											$current_spec_alignment_vert = $current_wpcompear_list_col_alignment_vert_unserialized[$spec_id];
										}
										else {
											$current_spec_alignment_vert = 'wpcompear_col_middle';
										}
										?>

										<select name="wpcompear_list_col_alignment_vert[<?php echo $current_custom_spec['spec_id']; ?>]">
											<option value="wpcompear_col_top" <?php selected( $current_spec_alignment_vert, 'wpcompear_col_top' ); ?>><?php _e('Top', 'wp-compear'); ?></option>
											<option value="wpcompear_col_middle" <?php selected( $current_spec_alignment_vert, 'wpcompear_col_middle' ); ?>><?php _e('Middle', 'wp-compear'); ?></option>
											<option value="wpcompear_col_bottom" <?php selected( $current_spec_alignment_vert, 'wpcompear_col_bottom' ); ?>><?php _e('Bottom', 'wp-compear'); ?></option>
										</select>

									<?php else: ?>


										<select name="wpcompear_list_col_alignment_vert[<?php echo $current_custom_spec['spec_id']; ?>]">
											<option value="wpcompear_col_top"><?php _e('Top', 'wp-compear'); ?></option>
											<option value="wpcompear_col_middle" selected="selected"><?php _e('Middle', 'wp-compear'); ?></option>
											<option value="wpcompear_col_bottom"><?php _e('Bottom', 'wp-compear'); ?></option>
										</select>

									<?php endif; ?>
						
								</td>



							</tr>

						<?php endforeach; ?>





					<?php else: ?>


						<tr>

								<td>
									<span>Custom Product Spec 1</span>
								</td>

								<td>

									<?php //$spec_id = $current_custom_spec['spec_id']; ?>

									<select name="wpcompear_list_col_width[1272509100]" style="visibility:hidden;">
										<option value="wpcompear_col_10"selected="selected">10%</option>
										<option value="wpcompear_col_15">15%</option>
										<option value="wpcompear_col_20">20%</option>
										<option value="wpcompear_col_25">25%</option>
										<option value="wpcompear_col_30">30%</option>
										<option value="wpcompear_col_35">35%</option>
										<option value="wpcompear_col_40">40%</option>
										<option value="wpcompear_col_45">45%</option>
										<option value="wpcompear_col_50">50%</option>
										<option value="wpcompear_col_55">55%</option>
										<option value="wpcompear_col_60">60%</option>
										<option value="wpcompear_col_65">65%</option>
										<option value="wpcompear_col_70">70%</option>
										<option value="wpcompear_col_75">75%</option>
										<option value="wpcompear_col_80">80%</option>
										<option value="wpcompear_col_85">85%</option>
										<option value="wpcompear_col_90">90%</option>
										<option value="wpcompear_col_95">95%</option>
										<option value="wpcompear_col_100">100%</option>
									</select>
						
								</td>

								<td>

									<?php //$spec_id = $current_custom_spec['spec_id']; ?>

									<select name="wpcompear_list_col_alignment[1272509100]" style="visibility:hidden;">
										<option value="wpcompear_col_left"><?php _e('Left', 'wp-compear'); ?></option>
										<option value="wpcompear_col_center" selected="selected"><?php _e('Center', 'wp-compear'); ?></option>
										<option value="wpcompear_col_right"><?php _e('Right', 'wp-compear'); ?></option>
									</select>
						
								</td>

								<td>

									<?php //$spec_id = $current_custom_spec['spec_id']; ?>

									<select name="wpcompear_list_col_alignment_vert[1272509100]" style="visibility:hidden;">
										<option value="wpcompear_col_top"><?php _e('Top', 'wp-compear'); ?></option>
										<option value="wpcompear_col_middle" selected="selected"><?php _e('Middle', 'wp-compear'); ?></option>
										<option value="wpcompear_col_bottom"><?php _e('Bottom', 'wp-compear'); ?></option>
									</select>
						
								</td>


							</tr>


					
					<?php endif; ?>

					</tbody>

				</table>


	    	
	    	</div>

	    <?php

	    }


	    add_meta_box(
	        'wpcompear-draganddrop-settings', // ID
	        'Drag & Drop Tool Settings', // Title
	        'wp_compear_draganddrop_callback', // callback
	        'wp-compear-lists', // post type
	        'normal', // context
	        'low' // priority
	    );

	    function wp_compear_draganddrop_callback($post) {

	    	$current_custom_specs_serialized = get_post_meta( $post->ID, '_wpcompear_custom_specs', true );
	    	$current_custom_specs_unserialized = maybe_unserialize( $current_custom_specs_serialized );

	    	$wpcompear_list_dragable_spec = get_post_meta( $post->ID, '_wpcompear_list_dragable_spec', true );

	    	$wpcompear_list_col_alignment_dragndrop = get_post_meta( $post->ID, '_wpcompear_list_col_alignment_dragndrop', true );

	    	?>


	    	<div class="wpcompear-meta-box wpcompear-draganddrop-settings">

	    		<P><?php _e('The drag and drop comparison tool will work with up to 20 custom specifications, but likely will be too tall. I recommend using no more than 8-10 specifications.', 'wp-compear'); ?></P>

	    		<P><?php _e('Choose which custom specification you would like to use as the draggable item list.', 'wp-compear'); ?></P>



	    		<?php if(is_array($current_custom_specs_unserialized)) : // loop through all custom specs if they exist ?>

					<?php foreach($current_custom_specs_unserialized as $current_custom_spec) : ?>

						<input type="radio" name="wpcompear_list_dragable_spec" value="<?php echo $current_custom_spec['spec_name']; ?>" <?php checked( $current_custom_spec['spec_name'], $wpcompear_list_dragable_spec ); ?>>
						<span><?php echo $current_custom_spec['spec_name']; ?></span><br />

					<?php endforeach; ?>

				<?php else: ?>

					<input type="radio" name="wpcompear_list_dragable_spec" value="Custom Product Spec 1" checked="checked">
						<span>Custom Product Spec 1


				<?php endif; ?>

			</div>

			<div class="wpcompear-meta-box wpcompear-draganddrop-settings">

				<div class="wpcompear-meta-box--left">
	    			<select name="wpcompear_list_col_alignment_dragndrop">
						<option value="wpcompear_col_left" <?php selected( $wpcompear_list_col_alignment_dragndrop, 'wpcompear_col_left' ); ?>><?php _e('Left', 'wp-compear'); ?></option>
						<option value="wpcompear_col_center" <?php selected( $wpcompear_list_col_alignment_dragndrop, 'wpcompear_col_center' ); ?>><?php _e('Center', 'wp-compear'); ?></option>
						<option value="wpcompear_col_right" <?php selected( $wpcompear_list_col_alignment_dragndrop, 'wpcompear_col_right' ); ?>><?php _e('Right', 'wp-compear'); ?></option>
					</select>
	    		</div>
				<div class="wpcompear-meta-box--right">
					<h3><?php _e('Drag and Drop Alignment', 'wp-compear'); ?></h3>
				</div>

				<div class="wpcompear-clear"></div>


	    	
	    	</div>




	    <?php

	    }


	}






	////////////// save SEO fields meta data
	public function wp_compear_save_data($post_id) {

	 
	    /*
	     * We need to verify this came from the our screen and with proper authorization,
	     * because save_post can be triggered at other times.
	     */
	 
	    // Check if our nonce is set.
	    if ( ! isset( $_POST['wp-compear-callback_nonce'] ) )
	        return $post_id;
	 
	    $nonce = $_POST['wp-compear-callback_nonce'];
	 
	    // Verify that the nonce is valid.
	    if ( ! wp_verify_nonce( $nonce, 'wp-compear-callback' ) )
	        return $post_id;
	 
	    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
	    if ( defined( 'DOING_AUTOSAVE') && DOING_AUTOSAVE )
	        return $post_id;
	 
	    // Check the user's permissions.
	    if ( 'page' == $_POST['post_type'] ) {
	 
	        if ( ! current_user_can( 'edit_page', $post_id ) )
	            return $post_id;
	    } else {
	 
	        if ( ! current_user_can( 'edit_post', $post_id ) )
	            return $post_id;
	    }
	 

	    $old_list_type = get_post_meta( $post_id, '_wpcompear_list_type', true );
	    // Sanitize user input.
	    $new_list_type = sanitize_text_field( $_POST['wpcompear_list_type'] );
	    // Update the meta field in the database.
	    update_post_meta( $post_id, '_wpcompear_list_type', $new_list_type, $old_list_type );


	    $old_list_theme = get_post_meta( $post_id, '_wpcompear_list_theme', true );
	    // Sanitize user input.
	    $new_list_theme = sanitize_text_field( $_POST['wpcompear_list_theme'] );
	    // Update the meta field in the database.
	    update_post_meta( $post_id, '_wpcompear_list_theme', $new_list_theme, $old_list_theme );

	    

	    $old_custom_specs_serialized = get_post_meta( $post_id, '_wpcompear_custom_specs', true );
	    $old_custom_specs_unserialized = maybe_unserialize( $old_custom_specs_serialized );

	    $new_custom_specs =  $_POST['wpcompear_custom_specs'];


	    $_groupings = $new_custom_specs;
	    $groupings = array();

	    if (is_array($_groupings)) {
	        foreach ($_groupings as $field => $group) {
	            for ($i=0; $i<count($group); $i++) {
	                $groupings[$i][$field] = $group[$i];
	            }
	        }
	    }

	    $user_specs_serialized = maybe_serialize($groupings);
    	update_post_meta( $post_id, '_wpcompear_custom_specs', $user_specs_serialized, $old_custom_specs_serialized );



	    $old_list_products_serialized = get_post_meta( $post_id, '_wpcompear_list_products', true );
	    $old_list_products_unserialized = maybe_unserialize( $old_list_products_serialized );

	    $new_list_products = $_POST['wpcompear_list_products'];


	    $_groupings = $new_list_products;
	    $groupings = array();

	    if (is_array($_groupings)) {
	        foreach ($_groupings as $field => $group) {
	            for ($i=0; $i<count($group); $i++) {

	            	$groupings[$i][$field] = $group[$i];
	                
	            }
	        }
	    }

	    $user_products_serialized = base64_encode(maybe_serialize($groupings));
    	update_post_meta( $post_id, '_wpcompear_list_products', $user_products_serialized, $old_list_products_serialized );

    	global $post;


    	$wpcompear_list_slider_prevnext_lg = get_post_meta( $post->ID, '_wpcompear_list_slider_prevnext_lg', true );
    	// Sanitize user input.
	    $new_wpcompear_list_slider_prevnext_lg = sanitize_text_field( $_POST['wpcompear_list_slider_prevnext_lg'] );
	    // Update the meta field in the database.
	    update_post_meta( $post_id, '_wpcompear_list_slider_prevnext_lg', $new_wpcompear_list_slider_prevnext_lg, $wpcompear_list_slider_prevnext_lg );


	    $wpcompear_list_slider_counter_lg = get_post_meta( $post->ID, '_wpcompear_list_slider_counter_lg', true );
    	// Sanitize user input.
	    $new_wpcompear_list_slider_counter_lg = sanitize_text_field( $_POST['wpcompear_list_slider_counter_lg'] );
	    // Update the meta field in the database.
	    update_post_meta( $post_id, '_wpcompear_list_slider_counter_lg', $new_wpcompear_list_slider_counter_lg, $wpcompear_list_slider_counter_lg );

	    

	    $wpcompear_list_slider_show_lg = get_post_meta( $post->ID, '_wpcompear_list_slider_show_lg', true );
	    // Sanitize user input.
	    $new_wpcompear_list_slider_show_lg = sanitize_text_field( $_POST['wpcompear_list_slider_show_lg'] );
	    // Update the meta field in the database.
	    update_post_meta( $post_id, '_wpcompear_list_slider_show_lg', $new_wpcompear_list_slider_show_lg, $wpcompear_list_slider_show_lg );


	    $wpcompear_list_slider_scroll_lg = get_post_meta( $post->ID, '_wpcompear_list_slider_scroll_lg', true );
    	// Sanitize user input.
	    $new_wpcompear_list_slider_scroll_lg = sanitize_text_field( $_POST['wpcompear_list_slider_scroll_lg'] );
	    // Update the meta field in the database.
	    update_post_meta( $post_id, '_wpcompear_list_slider_scroll_lg', $new_wpcompear_list_slider_scroll_lg, $wpcompear_list_slider_scroll_lg );


	    

    	$wpcompear_list_slider_show_md = get_post_meta( $post->ID, '_wpcompear_list_slider_show_md', true );
    	// Sanitize user input.
	    $new_wpcompear_list_slider_show_md = sanitize_text_field( $_POST['wpcompear_list_slider_show_md'] );
	    // Update the meta field in the database.
	    update_post_meta( $post_id, '_wpcompear_list_slider_show_md', $new_wpcompear_list_slider_show_md, $wpcompear_list_slider_show_md );


    	$wpcompear_list_slider_scroll_md = get_post_meta( $post->ID, '_wpcompear_list_slider_scroll_md', true );
    	// Sanitize user input.
	    $new_wpcompear_list_slider_scroll_md = sanitize_text_field( $_POST['wpcompear_list_slider_scroll_md'] );
	    // Update the meta field in the database.
	    update_post_meta( $post_id, '_wpcompear_list_slider_scroll_md', $new_wpcompear_list_slider_scroll_md, $wpcompear_list_slider_show_md );




	    $wpcompear_list_slider_specname_show = get_post_meta( $post->ID, '_wpcompear_list_slider_specname_show', true );
	    // Sanitize user input.
	    $new_wpcompear_list_slider_specname_show = sanitize_text_field( $_POST['wpcompear_list_slider_specname_show'] );
	    // Update the meta field in the database.
	    update_post_meta( $post_id, '_wpcompear_list_slider_specname_show', $new_wpcompear_list_slider_specname_show, $wpcompear_list_slider_specname_show );


	    $wpcompear_list_sortable_check = get_post_meta( $post->ID, '_wpcompear_list_sortable_check', true );
	    // Sanitize user input.
	    $new_wpcompear_list_sortable_check = sanitize_text_field( $_POST['wpcompear_list_sortable_check'] );
	    // Update the meta field in the database.
	    update_post_meta( $post_id, '_wpcompear_list_sortable_check', $new_wpcompear_list_sortable_check, $wpcompear_list_sortable_check );


	    $wpcompear_list_hover_check = get_post_meta( $post->ID, '_wpcompear_list_hover_check', true );
	    // Sanitize user input.
	    $new_wpcompear_list_hover_check = sanitize_text_field( $_POST['wpcompear_list_hover_check'] );
	    // Update the meta field in the database.
	    update_post_meta( $post_id, '_wpcompear_list_hover_check', $new_wpcompear_list_hover_check, $wpcompear_list_hover_check );



	    $old_wpcompear_list_col_width = get_post_meta( $post->ID, '_wpcompear_list_col_width', true );
	    $old_list_products_unserialized = maybe_unserialize( $old_wpcompear_list_col_width );

	    $new_wpcompear_list_col_width =  $_POST['wpcompear_list_col_width'];
	    $new_wpcompear_list_col_width_serialized = maybe_serialize($new_wpcompear_list_col_width);

    	update_post_meta( $post_id, '_wpcompear_list_col_width', $new_wpcompear_list_col_width_serialized, $old_wpcompear_list_col_width );




	    $wpcompear_list_col_alignment_slider = get_post_meta( $post->ID, '_wpcompear_list_col_alignment_slider', true );
    	// Sanitize user input.
	    $new_wpcompear_list_col_alignment_slider = sanitize_text_field( $_POST['wpcompear_list_col_alignment_slider'] );
	    // Update the meta field in the database.
	    update_post_meta( $post_id, '_wpcompear_list_col_alignment_slider', $new_wpcompear_list_col_alignment_slider, $wpcompear_list_col_alignment_slider );


	    $wpcompear_list_col_alignment_dragndrop = get_post_meta( $post->ID, '_wpcompear_list_col_alignment_dragndrop', true );
    	// Sanitize user input.
	    $new_wpcompear_list_col_alignment_dragndrop = sanitize_text_field( $_POST['wpcompear_list_col_alignment_dragndrop'] );
	    // Update the meta field in the database.
	    update_post_meta( $post_id, '_wpcompear_list_col_alignment_dragndrop', $new_wpcompear_list_col_alignment_dragndrop, $wpcompear_list_col_alignment_dragndrop );




	    $old_wpcompear_list_col_alignment = get_post_meta( $post->ID, '_wpcompear_list_col_alignment', true );
	    $old_list_products_unserialized = maybe_unserialize( $old_wpcompear_list_col_alignment );

	    $new_wpcompear_list_col_alignment =  $_POST['wpcompear_list_col_alignment'];
	    $new_wpcompear_list_col_alignment_serialized = maybe_serialize($new_wpcompear_list_col_alignment);

    	update_post_meta( $post_id, '_wpcompear_list_col_alignment', $new_wpcompear_list_col_alignment_serialized, $old_wpcompear_list_col_alignment );


    	$old_wpcompear_list_col_alignment_vert = get_post_meta( $post->ID, '_wpcompear_list_col_alignment_vert', true );
	    $old_list_products_vert_unserialized = maybe_unserialize( $old_wpcompear_list_col_alignment_vert );

	    $new_wpcompear_list_col_alignment_vert =  $_POST['wpcompear_list_col_alignment_vert'];
	    $new_wpcompear_list_col_alignment_vert_serialized = maybe_serialize($new_wpcompear_list_col_alignment_vert);

    	update_post_meta( $post_id, '_wpcompear_list_col_alignment_vert', $new_wpcompear_list_col_alignment_vert_serialized, $old_wpcompear_list_col_alignment_vert );




    	$wpcompear_list_dragable_spec = get_post_meta( $post->ID, '_wpcompear_list_dragable_spec', true );
    	// Sanitize user input.
		$new_wpcompear_list_dragable_spec = sanitize_text_field( $_POST['wpcompear_list_dragable_spec'] );
    	// Update the meta field in the database.
    	update_post_meta( $post_id, '_wpcompear_list_dragable_spec', $new_wpcompear_list_dragable_spec, $wpcompear_list_dragable_spec );
	    


	}



	/**
	 * Create a dulicate post link
	 *
	 * @since    1.0.0
	 */
	public function duplicate_compear_post() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Compear_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Compear_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		global $wpdb;
		if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'duplicate_compear_post' == $_REQUEST['action'] ) ) ) {
			wp_die('No post to duplicate has been supplied!');
		}
	 
		/*
		 * get the original post id
		 */
		$_post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);

		$post_id_check = preg_match('/^([0-9]*)$/',$_post_id);

		if($post_id_check===1) {
			$post_id = $_post_id;
		}

		/*
		 * and all the original post data then
		 */
		$post = get_post( $post_id );
	 
		/*
		 * if you don't want current user to be the new post author,
		 * then change next couple of lines to this: $new_post_author = $post->post_author;
		 */
		$current_user = wp_get_current_user();
		$new_post_author = $current_user->ID;
	 
		/*
		 * if post data exists, create the post duplicate
		 */
		if (isset( $post ) && $post != null) {
	 
			/*
			 * new post data array
			 */
			$args = array(
				'comment_status' => $post->comment_status,
				'ping_status'    => $post->ping_status,
				'post_author'    => $new_post_author,
				'post_content'   => $post->post_content,
				'post_excerpt'   => $post->post_excerpt,
				'post_name'      => $post->post_name,
				'post_parent'    => $post->post_parent,
				'post_password'  => $post->post_password,
				'post_status'    => 'draft',
				'post_title'     => $post->post_title.' (copy)',
				'post_type'      => $post->post_type,
				'to_ping'        => $post->to_ping,
				'menu_order'     => $post->menu_order
			);
	 
			/*
			 * insert the post by wp_insert_post() function
			 */
			$new_post_id = wp_insert_post( $args );
	 
			/*
			 * get all current post terms ad set them to the new post draft
			 */
			$taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			foreach ($taxonomies as $taxonomy) {
				$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
				wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
			}
	 
			/*
			 * duplicate all post meta
			 */
			// $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
			// if (count($post_meta_infos)!=0) {
			// 	$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			// 	foreach ($post_meta_infos as $meta_info) {
			// 		$meta_key = $meta_info->meta_key;
			// 		$meta_value = addslashes($meta_info->meta_value);
			// 		$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
			// 	}
			// 	$sql_query.= implode(" UNION ALL ", $sql_query_sel);
			// 	$wpdb->query($sql_query);
			// }


			// _edit_last
			// _edit_lock
			// _wpcompear_list_type
			// _wpcompear_list_theme
			// _wpcompear_custom_specs
			// _wpcompear_list_products
			// _wpcompear_list_slider_prevnext_lg
			// _wpcompear_list_slider_counter_lg
			// _wpcompear_list_slider_show_lg
			// _wpcompear_list_slider_scroll_lg
			// _wpcompear_list_slider_show_md
			// _wpcompear_list_slider_scroll_md
			// _wpcompear_list_slider_specname_show
			// _wp_old_slug
			// _wpcompear_list_sortable_check
			// _wpcompear_list_col_width
			// _wpcompear_list_dragable_spec

			// forced to do each post meta seperately instead of a loop due to bug in serialized array in add_post_meta


			$wpcompear_post_meta = get_post_meta( $post_id, '_edit_last', true );
		    add_post_meta( $new_post_id, '_edit_last', $wpcompear_post_meta );

		    $wpcompear_post_meta = get_post_meta( $post_id, '_edit_lock', true );
		    add_post_meta( $new_post_id, '_edit_lock', $wpcompear_post_meta );

		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_list_type', true );
		    add_post_meta( $new_post_id, '_wpcompear_list_type', $wpcompear_post_meta );

		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_list_theme', true );
		    add_post_meta( $new_post_id, '_wpcompear_list_theme', $wpcompear_post_meta );




		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_custom_specs', true );
		    $wpcompear_post_meta_unserialized = maybe_unserialize($wpcompear_post_meta) ;
		    add_post_meta( $new_post_id, '_wpcompear_custom_specs', $wpcompear_post_meta_unserialized );




		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_list_products', true );
			$wpcompear_post_meta_unserialized = maybe_unserialize($wpcompear_post_meta) ;
		    add_post_meta( $new_post_id, '_wpcompear_list_products', $wpcompear_post_meta_unserialized );




		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_list_slider_prevnext_lg', true );
		    add_post_meta( $new_post_id, '_wpcompear_list_slider_prevnext_lg', $wpcompear_post_meta );

		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_list_slider_counter_lg', true );
		    add_post_meta( $new_post_id, '_wpcompear_list_slider_counter_lg', $wpcompear_post_meta );

		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_list_slider_show_lg', true );
		    add_post_meta( $new_post_id, '_wpcompear_list_slider_show_lg', $wpcompear_post_meta );

		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_list_slider_scroll_lg', true );
		    add_post_meta( $new_post_id, '_wpcompear_list_slider_scroll_lg', $wpcompear_post_meta );

		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_list_slider_show_md', true );
		    add_post_meta( $new_post_id, '_wpcompear_list_slider_show_md', $wpcompear_post_meta );

		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_list_slider_scroll_md', true );
		    add_post_meta( $new_post_id, '_wpcompear_list_slider_scroll_md', $wpcompear_post_meta );

		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_list_slider_specname_show', true );
		    add_post_meta( $new_post_id, '_wpcompear_list_slider_specname_show', $wpcompear_post_meta );

		    $wpcompear_post_meta = get_post_meta( $post_id, '_wp_old_slug', true );
		    add_post_meta( $new_post_id, '_wp_old_slug', $wpcompear_post_meta );

		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_list_sortable_check', true );
		    add_post_meta( $new_post_id, '_wpcompear_list_sortable_check', $wpcompear_post_meta );




		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_list_col_width', true );
		    $wpcompear_post_meta_unserialized = maybe_unserialize($wpcompear_post_meta) ;
		    add_post_meta( $new_post_id, '_wpcompear_list_col_width', $wpcompear_post_meta_unserialized );




		    $wpcompear_post_meta = get_post_meta( $post_id, '_wpcompear_list_dragable_spec', true );
		    add_post_meta( $new_post_id, '_wpcompear_list_dragable_spec', $wpcompear_post_meta );

	 
	 
			/*
			 * finally, redirect to the edit post screen for the new draft
			 */
			wp_redirect( admin_url( 'edit.php?post_type=wp-compear-lists' ) );
			exit;
		} else {
			wp_die('Post creation failed, could not find original post: ' . $post_id);
		}

		

	}


	/**
	 * Create a dulicate post link
	 *
	 * @since    1.0.0
	 */
	public function wpcompear_duplicate_post_link( $actions, $post ) {
		if (current_user_can('edit_posts')) {

			if ($post->post_type=='wp-compear-lists') {
				$actions['duplicate'] = '<a href="admin.php?action=duplicate_compear_post&amp;post=' . $post->ID . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
			}
		}
		return $actions;
	}



	

	/**
	 * Create a dulicate post link
	 *
	 * @since    1.0.0
	 */
	public function wp_compear_insert_shortcode_btn() {
		//the id of the container I want to show in the popup
		$container_id = 'wp_compear_popup_container';
		$title = __('Insert WP ComPEAR Shortcode', 'wp-compear');
		$button = '<img src="'.home_url().'/wp-content/plugins/wp-compear/admin/img/wpcompear-icon-small.png" style="margin-top: -3px;"">';
		//append the icon
		$context = "<a class='button add_media thickbox' data-editor='content' title='{$title}' href='#TB_inline?width=400&inlineId={$container_id}'>".$button."</a>";
  		return $context;
	}



	/**
	 * Choose WP ComPEAR list to embed
	 *
	 * @since    1.0.0
	 */
	public function wp_compear_add_inline_popup_content() {
	?>

		<div id="wp_compear_popup_container" style="display:none;">

			<?php
			$args = array(
		        'post_type' => 'wp-compear-lists',
		        'post_status' => 'publish',
		        'posts_per_page' => -1
		    );
		    ?>

		    <?php $djnquery = get_posts( $args ); ?>

		    <?php if ( $djnquery ) : ?>

		    	<h3><?php _e('Choose a WP ComPEAR List to embed', 'wp-compear'); ?></h3>

				<select class="wp_compear_shortcode_selector" name="wp_compear_shortcode_selector">
					<option value="0">- select -</option>
				    <?php foreach ( $djnquery as $post ) : ?>
						<option value='<?php echo $post->ID; ?>'><?php echo $post->post_title; ?></option>
				    <?php endforeach; //wp_reset_query(); ?>
				</select>

			<?php 

			else:
				echo '<p>'.__('You have not created any WP ComPEAR Lists yet. Go ahead and add one!', 'wp-compear').'</p>';
			endif; 
			?>
			<br /><br />
			<input id="insert_wpcompear_slider_shortcode" name="insert_slider_shortcode" type="button" class="button-primary" value="<?php _e('Insert WP ComPEAR Shortcode', 'wp-compear'); ?>" />
		</div>


	<?php
	}




	/**
	 * Add Admin Column to List of CompEAR Lists
	 *
	 * @since    1.0.0
	 */
	public function revealid_add_id_column( $columns ) {

		$screen = get_current_screen();
		$screentype = $screen->post_type;

		if( $screentype == 'wp-compear-lists' ) {
			$columns['revealid_id'] = 'Shortcode';
		}
		return $columns;

	}



	/**
	 * Add content to Admin Column to List of CompEAR Lists
	 *
	 * @since    1.0.0
	 */
	public function revealid_id_column_content( $column, $id ) {

		$screen = get_current_screen();
		$screentype = $screen->post_type;

		if( 'revealid_id' == $column && $screentype == 'wp-compear-lists' ) {
			echo '<b>[wp-compear id="'.$id.'"]</b>';
		}

	}
 


}

