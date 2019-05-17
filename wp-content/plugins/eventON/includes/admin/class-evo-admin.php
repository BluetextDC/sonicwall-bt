<?php
/**
 * all wp-admin functions for admin side of eventon
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon/Admin
 * @version     2.4.9
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'evo_admin' ) ) :

/** evo_admin Class */
class evo_admin {

	private $class_name;
	/** Constructor */
	public function __construct() {
		$this->opt = evo_get_options('1');

		add_action('admin_menu', array($this,'eventon_admin_menu'), 5);
		add_action( 'admin_head', array($this,'eventon_admin_menu_highlight'), 5);
		add_action('admin_init', array($this,'eventon_admin_init'));
		
		add_action('admin_action_duplicate_event', array($this,'eventon_duplicate_event_action'));
		add_filter("plugin_action_links_".AJDE_EVCAL_BASENAME, array($this,'eventon_plugin_links') );

		add_action('media_buttons_context',  array($this,'eventon_shortcode_button'));
		add_filter( 'tiny_mce_version', array($this,'eventon_refresh_mce') ); 

		add_filter('display_post_states', array($this,'post_state'),10,2);

		//add_action( 'admin_enqueue_scripts', array($this,'eventon_admin_scripts') );
		//add_action( 'admin_enqueue_scripts', array($this,'eventon_all_backend_files') );
	}

	function post_state( $states, $post){
		//print_r($post);
		if (  'page' == get_post_type( $post->ID ) &&  $post->post_name == 'event-directory'){
	        $states[] = __('Events Page'); 
	    } 

	    return $states;
	}

// admin init
	function eventon_admin_init() {
		
		//$tt = get_site_option('_site_transient_update_plugins');
		//$transient_package = isset($tt->response[ 'eventon-action-user/eventon-action-user.php']->package)?	$tt->response[ 'eventon-action-user/eventon-action-user.php']->package: false;
		//print_r($transient_package);
		//print_r($tt);
		
		// Includes
			require_once(AJDE_EVCAL_PATH.'/includes/updates/class-evo_plugins_api_data.php');

		global $pagenow, $typenow, $wpdb, $post;	

		$postType = !empty($_GET['post_type'])? $_GET['post_type']: false;
	   
	    if(!$postType && !empty($_GET['post']))   	$postType = get_post_type($_GET['post']);
		
		if ( $postType && $postType == "ajde_events" ) {		
			// Event Post Only
			$print_css_on = array( 'post-new.php', 'post.php' );

			foreach ( $print_css_on as $page ){
				add_action( 'admin_print_styles-'. $page, array($this,'eventon_admin_post_css') );
				add_action( 'admin_print_scripts-'. $page, array($this,'eventon_admin_post_script') );			
			}
						
			// taxonomy only page
			if($pagenow =='edit-tags.php' || $pagenow == 'term.php'){
				$this->eventon_load_colorpicker();
				wp_enqueue_script('taxonomy',AJDE_EVCAL_URL.'/assets/js/admin/taxonomy.js' ,array('jquery'),'1.0', true);
			}
		}

		// event edit page content
			include_once(  AJDE_EVCAL_PATH.'/includes/admin/post_types/class-meta_boxes.php' );
			$this->metaboxes = new evo_event_metaboxes();

		// Includes for admin
			if(defined('DOING_AJAX')){	include_once( 'class-admin-ajax.php' );		}			

		// evneton settings only 
			if($pagenow =='admin.php' && isset($_GET['page']) && ($_GET['page']=='eventon' || $_GET['page']=='action_user')){
				global $ajde;
				$ajde->load_styles_to_page();
			}

		// all eventon wp-admin pages
			$this->wp_admin_scripts_styles();
					
		// create necessary pages	
			$_eventon_create_pages = get_option('_eventon_create_pages'); // get saved status for creating pages
			if(empty($_eventon_create_pages) || $_eventon_create_pages!= 1){
				evo_install::create_pages();
			}

		// force update checking on wp-admin
			if($pagenow =='update-core.php' && isset($_REQUEST['force-check']) && $_REQUEST['force-check']==1){
				EVO_Prods()->get_remote_prods_data();
			}

		// when an addon is updated or installed - since 2.5
			add_action('evo_addon_version_change', array($this, 'update_addon_styles'), 10);

		// Deactivate single events addon
			deactivate_plugins('eventon-single-event/eventon-single-event.php');
			deactivate_plugins('eventon-search/eventon-search.php');
	}
	
// admin menus
	function eventon_admin_menu() {
	    global $menu, $pagenow;

	    if ( current_user_can( 'manage_eventon' ) )
	    $menu[] = array( '', 'read', 'separator-eventon', '', 'wp-menu-separator eventon' );
				
		// Create admin menu page 
		$main_page = add_menu_page(
			__('EventON - Event Calendar','eventon'), 
			'myEventON',
			'manage_eventon',
			'eventon',
			array($this,'eventon_settings_page'), 
			AJDE_EVCAL_URL.'/assets/images/eventon_menu_icon.png'
		);

	    add_action( 'load-' . $main_page, array($this,'eventon_admin_help_tab') );	
		
		
		// add submenus to the eventon menu
		add_submenu_page( 'eventon', 'Language', 'Language', 'manage_eventon', 'admin.php?page=eventon&tab=evcal_2', '' );
		add_submenu_page( 'eventon', 'Styles', 'Styles', 'manage_eventon', 'admin.php?page=eventon&tab=evcal_3', '' );
		add_submenu_page( 'eventon', 'Addons & Licenses', 'Addons & Licenses', 'manage_eventon', 'admin.php?page=eventon&tab=evcal_4', '' );
		add_submenu_page( 'eventon', 'Support', 'Support', 'manage_eventon', 'admin.php?page=eventon&tab=evcal_5', '' );
	}
	/** Include and display the settings page. */
		function eventon_settings_page() {
			include_once(  AJDE_EVCAL_PATH.'/ajde/class-ajde_plugin_settings.php' );
			include_once(  AJDE_EVCAL_PATH.'/includes/admin/settings/eventon-admin-settings.php' );
			include_once(  AJDE_EVCAL_PATH.'/includes/admin/settings/class-settings-appearance.php' );
			eventon_settings();
		}
// correct menu highlight
	function eventon_admin_menu_highlight() {
		global $submenu;

		//print_r($submenu);

		if ( isset( $submenu['eventon'] )  )  {
			$submenu['eventon'][0][0] = 'Settings';
			//unset( $submenu['eventon'][2] );
		}
		ob_start();
		?>
			<style>
				.evo_yn_btn .btn_inner:before{content:"<?php _e('NO','eventon');?>";}
				.evo_yn_btn .btn_inner:after{content:"<?php _e('YES','eventon');?>";}
			</style>
		<?php
		echo ob_get_clean();
	}
// admin styles and scripts
	// for event posts
		function eventon_admin_post_css() {
			global $wp_scripts, $eventon;
			$protocol = is_ssl() ? 'https' : 'http';

			// JQ UI styles
			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.10.4';		
			
			wp_enqueue_style("jquery-ui-css", $protocol."://ajax.googleapis.com/ajax/libs/jqueryui/{$jquery_version}/themes/smoothness/jquery-ui.min.css");
			
			wp_enqueue_style( 'backend_evcal_post',AJDE_EVCAL_URL.'/assets/css/admin/backend_evcal_post.css', array(), $eventon->version );
			wp_enqueue_style( 'select2',AJDE_EVCAL_URL.'/assets/css/select2.css',array(), $eventon->version);

			// RTL styles for wp-admin
			if( is_rtl() ){
				wp_enqueue_style( 'rtl_styles',AJDE_EVCAL_URL.'/assets/css/admin/wp_admin_rtl.css',array(), $eventon->version);
			}
		}
		function eventon_admin_post_script() {
			global $pagenow, $typenow, $post, $eventon, $ajde;	
			
			if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
				$typenow = $post->post_type;
			} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
		        $post = get_post( $_GET['post'] );
		        $typenow = $post->post_type;
		    }
			
			if ( $typenow == '' || $typenow == "ajde_events" ) {

				// load color picker files
				$ajde->load_colorpicker();

				$eventon_JQ_UI_tp = AJDE_EVCAL_URL.'/assets/css/jquery.timepicker.css';
				wp_enqueue_style( 'eventon_JQ_UI_tp',$eventon_JQ_UI_tp);
			
				// other scripts 
				wp_enqueue_script('select2',AJDE_EVCAL_URL.'/assets/js/select2.min.js');
				wp_enqueue_script('evcal_backend_post_timepicker',AJDE_EVCAL_URL.'/assets/js/jquery.timepicker.js');
				wp_enqueue_script('evcal_backend_post',AJDE_EVCAL_URL.'/assets/js/admin/eventon_backend_post.js', array('jquery','jquery-ui-core','jquery-ui-datepicker'), $eventon->version, true );
				wp_enqueue_script("jquery-ui-core");
				
				wp_localize_script( 'evcal_backend_post', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));	
				
				// hook for plugins
				do_action('eventon_admin_post_script');
			}
		}

	// scripts and styles for wp-admin
		function wp_admin_scripts_styles(){
			global $eventon, $pagenow, $wp_version;

			if($pagenow == 'term.php')
				wp_enqueue_media();
			wp_enqueue_script('evo_wp_admin',AJDE_EVCAL_URL.'/assets/js/admin/wp_admin.js',array('jquery'),$eventon->version,true);
			wp_localize_script( 
				'evo_wp_admin', 
				'evo_admin_ajax_handle', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ), 
					'postnonce' => wp_create_nonce( 'eventon_admin_nonce' )
				)
			);

			if( (!empty($pagenow) && $pagenow=='admin.php')
			 && (isset($_GET['page']) && ($_GET['page']=='eventon'|| $_GET['page']=='action_user'|| $_GET['page']=='evo-sync') ) 
			){

				// only addons page
			 	if(!empty($_GET['tab']) && $_GET['tab']=='evcal_4'){
			 		wp_enqueue_script('evcal_addons',AJDE_EVCAL_URL.'/assets/js/admin/settings_addons_licenses.js',array('jquery'),$eventon->version,true);
			 	}
			 	// only troubleshoot page
			 	if(!empty($_GET['tab']) && $_GET['tab']=='evcal_5'){
			 		wp_enqueue_script('evcal_troubleshoot',AJDE_EVCAL_URL.'/assets/js/admin/settings_troubleshoot.js',array('jquery'),$eventon->version,true);
			 	}
			 	
			 	// wp-admin script			 		
			 		wp_localize_script( 'evo_wp_admin', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));			 		

			 	// LOAD thickbox
					if(isset($_GET['tab']) && ( $_GET['tab']=='evcal_5' || $_GET['tab']=='evcal_4') ){
						wp_enqueue_script('thickbox');
						wp_enqueue_style('thickbox');
					}

				// LOAD custom google fonts for skins		
					$gfont="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400,300";
					wp_register_style( 'evcal_google_fonts', $gfont, '', '', 'screen' );
			}
			
			// ALL wp-admin
			wp_register_style('evo_font_icons',AJDE_EVCAL_URL.'/assets/fonts/font-awesome.css',array(), $eventon->version);
			wp_enqueue_style( 'evo_font_icons' );

			// wp-admin styles
			 	wp_enqueue_style( 'evo_wp_admin',AJDE_EVCAL_URL.'/assets/css/admin/wp_admin.css',array(), $eventon->version);


			// styles for WP>=3.8
			if($wp_version>=3.8)
				wp_enqueue_style( 'newwp',AJDE_EVCAL_URL.'/assets/css/admin/wp3.8.css',array(), $eventon->version);
			// styles for WP<3.8
			if($wp_version<3.8)
				wp_enqueue_style( 'newwp',AJDE_EVCAL_URL.'/assets/css/admin/wp_old.css',array(), $eventon->version);

			

		}

// Dynamic Style Related
	/*	Dynamic styles generation */
		function generate_dynamic_styles_file($newdata='') {
		 
			/** Define some vars **/
			$data = $newdata; 
			$uploads = wp_upload_dir();
			
			//$css_dir = get_template_directory() . '/css/'; // Shorten code, save 1 call
			$css_dir = AJDE_EVCAL_DIR . '/'. EVENTON_BASE.  '/assets/css/'; 
			//$css_dir = plugin_dir_path( __FILE__ ).  '/assets/css/'; 
			
			//echo $css_dir;

			/** Save on different directory if on multisite **/
			if(is_multisite()) {
				$aq_uploads_dir = trailingslashit($uploads['basedir']);
			} else {
				$aq_uploads_dir = $css_dir;
			}
			
			/** Capture CSS output **/
			ob_start();
			require($css_dir . 'dynamic_styles.php');
			$css = ob_get_clean();

			//print_r($css);
			
			/** Write to options.css file **/
			WP_Filesystem();
			global $wp_filesystem;
			if ( ! $wp_filesystem->put_contents( $aq_uploads_dir . 'eventon_dynamic_styles.css', $css, 0777) ) {
			    return true;
			}	

			// also update concatenated addon styles
				$this->update_addon_styles();	
		}

	/**
	 * Update and save addon styles passed via pluggable function
	 * @since   2.5
	 */
		function update_addon_styles(){
			// check if enabled via eventon settings
			if( evo_settings_val('evcal_concat_styles',$this->opt, true)) return false;
			
			/** Define some vars **/
			//$data = $newdata; 
			$uploads = wp_upload_dir();
			
			//$css_dir = get_template_directory() . '/css/'; // Shorten code, save 1 call
			$css_dir = AJDE_EVCAL_DIR . '/'. EVENTON_BASE.  '/assets/css/'; 
			//$css_dir = plugin_dir_path( __FILE__ ).  '/assets/css/'; 
			
			//echo $css_dir;

			/** Save on different directory if on multisite **/
			if(is_multisite()) {
				$aq_uploads_dir = trailingslashit($uploads['basedir']);
			} else {
				$aq_uploads_dir = $css_dir;
			}
			
			/** Capture CSS output **/
			ob_start();
			require($css_dir . 'styles_evo_addons.php');
			$css = ob_get_clean();
				
			// if there is nothing on css
			if(empty($css)) return false;

			// save a version number for this
				$ver = get_option('eventon_addon_styles_version');
				(empty($ver))? 
					add_option('eventon_addon_styles_version', 1.00001):
					update_option('eventon_addon_styles_version', ($ver+0.00001));

			
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			/** Write to options.css file **/
			WP_Filesystem();
			global $wp_filesystem;
			if ( ! $wp_filesystem->put_contents( $aq_uploads_dir . 'eventon_addon_styles.css', $css, 0777) ) {
			    return true;
			}		
		}

	// update the dynamic styles file with updates styles val
	// @ 2.5
		function update_dynamic_styles(){
			ob_start();
			include(AJDE_EVCAL_PATH.'/assets/css/dynamic_styles.php');
			$evo_dyn_css = ob_get_clean();						
			update_option('evo_dyn_css', $evo_dyn_css);
		}	

// shortcode generator
	function eventon_shortcode_button($context) {	
		global $pagenow, $typenow, $post;	
		
		if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
			$typenow = $post->post_type;
		} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
	        $post = get_post( $_GET['post'] );
	        $typenow = $post->post_type;
	    }
		
		if ( $typenow == '' || $typenow == "ajde_events" ) return;

		if(evo_settings_check_yn($this->opt, 'evo_hide_shortcode_btn') ) return;

		//our popup's title
	  	$text = '[ ] ADD EVENTON';
	  	$title = __('eventON Shortcode generator','eventon');

	  	//append the icon
	  	$context .= "<a id='evo_shortcode_btn' class='ajde_popup_trig evo_admin_btn btn_prime' data-popc='eventon_shortcode' title='{$title}' href='#'>{$text}</a>";
		
		$this->eventon_shortcode_pop_content();
		
	  	return $context;
	}
	function eventon_shortcode_pop_content(){		
		global $evo_shortcode_box, $eventon, $ajde;
		$content='';
		
		require_once(AJDE_EVCAL_PATH.'/includes/admin/class-shortcode_box_generator.php');
		
		$content = $evo_shortcode_box->get_content();
		
		// eventon popup box
		echo $ajde->wp_admin->lightbox_content(array(
			'content'=>$content, 
			'class'=>'eventon_shortcode', 
			'attr'=>'clear="false"', 
			'title'=> __('Shortcode Generator','eventon'),			
			//'subtitle'=>'Select option to customize shortcode variable values'
		));
	}

// Supporting functions
	function get_image($size='', $placeholder=true){
		global $postid;

		$size = (!empty($size))? $size: 'thumbnail';

		$thumb = get_post_thumbnail_id($postid);

		if(!empty($thumb)){
			$img = wp_get_attachment_image_src($thumb, $size);
			return $img[0];
		}else if($placeholder){
			return AJDE_EVCAL_URL.'/assets/images/placeholder.png';
		}else{
			return false;
		}
	}

	function get_color($pmv=''){
		if(!empty($pmv['evcal_event_color'])){
			if( strpos($pmv['evcal_event_color'][0], '#') !== false ){
				return $pmv['evcal_event_color'][0];
			}else{
				return '#'.$pmv['evcal_event_color'][0];
			}
		}else{
			$opt = get_option('evcal_options_evcal_1');
			$cl = (!empty($opt['evcal_hexcode']))? $opt['evcal_hexcode']: '206177';
			return '#'.$cl;
		}
	}

	public function addon_exists($slug){
		$addon = new EVO_Product($slug);
		return $addon->is_installed();
	}
	function eventon_load_colorpicker(){
		global $ajde;
		/** COLOR PICKER **/
		//wp_enqueue_script('color_picker',AJDE_EVCAL_URL.'/assets/js/colorpicker.js' ,array('jquery'),'1.0', true);
		//wp_enqueue_style( 'ajde_backender_colorpicker_styles',AJDE_EVCAL_URL.'/assets/css/colorpicker_styles.css');
		$ajde->load_colorpicker();
	}

	// help dropdown
		function eventon_admin_help_tab() {
			include_once( AJDE_EVCAL_PATH.'/includes/admin/eventon-admin-content.php' );
			eventon_admin_help_tab_content();
		}
	// duplicate events action
		function eventon_duplicate_event_action() {
			
			include_once( AJDE_EVCAL_PATH.'/includes/admin/post_types/duplicate_event.php');
			eventon_duplicate_event();
		}

	// plugin settings page additional links
		function eventon_plugin_links($links) { 
		  	$settings_link = '<a href="admin.php?page=eventon">'.__('Settings','eventon').'</a>'; 	  
		  	$docs_link = '<a href="http://www.myeventon.com/documentation/" target="_blank">'.__('Docs','eventon').'</a>';
		  	$news_link = '<a href="http://www.myeventon.com/news/" target="_blank">'.__('News','eventon').'</a>'; 
		  	array_unshift($links, $settings_link, $docs_link, $news_link); 
		  	return $links; 
		}

	// form mc refresh
	function eventon_refresh_mce( $ver ) {
		$ver += 3;
		return $ver;
	}


		
}

endif;