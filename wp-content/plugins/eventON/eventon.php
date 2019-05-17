<?php
/**
 * Plugin Name: EventON
 * Plugin URI: http://www.myeventon.com/
 * Description: A beautifully crafted minimal calendar experience
 * Version: 2.6.5
 * Author: AshanJay
 * Author URI: http://www.ashanjay.com
 * Requires at least: 4.0
 * Tested up to: 4.9.1
 * 
 * Text Domain: eventon
 * Domain Path: /lang/languages/
 * 
 * @package EventON
 * @category Core
 * @author AJDE
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// main eventon class
if ( ! class_exists( 'EventON' ) ) {

class EventON {
	public $version = '2.6.5';
	/**
	 * @var evo_generator
	 */
	public $evo_generator;	
	
	public $template_url;
	public $print_scripts=false;

	public $lang = '';

	protected static $_instance = null;

	// setup one instance of eventon
	public static function instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function template_path(){
		return $this->template_url;
	}

	/** Constructor. */
	public function __construct() {

		// Define constants
		$this->define_constants();	
		
		// Installation
		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		add_filter('cron_schedules',array($this,'new_schedule'));	
		
		// Include required files
		$this->includes();
				
		// Hooks
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
		
		// Deactivation
		register_deactivation_hook( AJDE_EVCAL_FILE, array($this,'deactivate'));
	}

	/**
	 * Define EVO Constants
	 */
	public function define_constants() {
		if(!defined('EVO_VERSION'))
			define('EVO_VERSION', $this->version);

		define( "AJDE_EVCAL_DIR", WP_PLUGIN_DIR ); //E:\xampp\htdocs\WP/wp-content/plugins
		define( "AJDE_EVCAL_PATH", dirname( __FILE__ ) );// E:\xampp\htdocs\WP/wp-content/plugins/eventON/
		define( "AJDE_EVCAL_FILE", ( __FILE__ ) ); //E:\xampp\htdocs\WP\wp-content\plugins\eventON\eventon.php
		define( "AJDE_EVCAL_URL", path_join(plugins_url(), basename(dirname(__FILE__))) );
		define( "AJDE_EVCAL_BASENAME", plugin_basename(__FILE__) ); //eventON/eventon.php
		define( "EVENTON_BASE", basename(dirname(__FILE__)) ); //eventON
		define( "BACKEND_URL", get_bloginfo('url').'/wp-admin/' ); 
		$this->assets_path = str_replace(array('http:','https:'), '',AJDE_EVCAL_URL).'/assets/';
		
	}
		
	/**
	 * Include required files
	 * 
	 * @access private
	 * @return void
	 * @since  0.1
	 */
	private function includes(){		

		// post types
		include_once( 'includes/class-evo-addons.php' );
		include_once('includes/admin/class-evo-products.php' );					
		include_once('includes/admin/class-evo-product.php' );
		include_once( 'includes/helpers/helper_factory.php' );
		include_once( 'includes/class-evo-post-types.php' );
		include_once( 'includes/class-multi-data-types.php' );
		include_once( 'includes/class-search.php' );
		include_once( 'includes/class-evo-datetime.php' );
		include_once( 'includes/class-evo-helper.php' );
		include_once( 'includes/class-evo-install.php' );
		include_once( 'includes/class-cronjobs.php' );		
		
		include_once('ajde/ajde.php' );
			
		include_once( 'includes/class-environment.php' );
		include_once( 'includes/class-calendar.php' );
		include_once( 'includes/eventon-core-functions.php' );
		include_once( 'includes/class-calendar-functions.php' );
		include_once( 'includes/class-event.php' );
		include_once( 'includes/class-frontend.php' );		
		include_once( 'includes/class-map-styles.php' );		
		include_once( 'includes/class-calendar-shell.php' );
		include_once( 'includes/class-calendar-body.php' );		
		include_once( 'includes/class-calendar-helper.php' );
		include_once( 'includes/class-calendar_generator.php' );
		include_once( 'includes/class-deprecations.php' );	


		if ( is_admin() ){	
			include_once('includes/admin/settings/class-addon-details.php' );	
			include_once('includes/class-intergration-visualcomposer.php' );
			include_once('includes/admin/class-views.php' );
			include_once('includes/admin/eventon-admin-functions.php' );
			include_once('includes/admin/eventon-admin-html.php' );
			include_once('includes/admin/eventon-admin-taxonomies.php' );
			include_once('includes/admin/post_types/ajde_events.php' );
			include_once('includes/admin/welcome.php' );				
			include_once('includes/admin/class-evo-event.php' );
			include_once('includes/admin/class-evo-admin.php' );	
			include_once('includes/admin/class-licenses.php' );						
			include_once('includes/admin/class-evo-errors.php' );					
		}
		if ( ! is_admin() || defined('DOING_AJAX') ){
			// Functions
			include_once( 'includes/eventon-functions.php' );
			include_once( 'includes/class-evo-shortcodes.php' );
			include_once( 'includes/class-evo-template-loader.php' );
		}
		if ( defined('DOING_AJAX') ){
			include_once( 'includes/class-evo-ajax.php' );	
		}
		
	}	
	
	/** Init eventON when WordPress Initialises.	 */
	public function init() {

		// $this->rewrite_tag();
		
		// Set up localisation
		$this->load_plugin_textdomain();
		
		$this->template_url = apply_filters('eventon_template_url','eventon/');
		
		$this->evo_generator	= new EVO_generator();	
		$this->frontend			= new evo_frontend();
		$this->mdt				= new evo_mdt();
		//$this->helper			= new evo_helper();

		// Classes/actions loaded for the frontend and for ajax requests
		if ( ! is_admin() || defined('DOING_AJAX') ) {
			// Class instances		
			$this->shortcodes	= new EVO_Shortcodes();	
		}
		if(is_admin()){
			$this->evo_event_item 	= new evo_event_item();
			$this->evo_admin 	= new evo_admin();
			$this->taxonomies	= new eventon_taxonomies();	
		}
		
		// roles and capabilities
		eventon_init_caps();
		
		global $pagenow;
		
		// Initiate eventon 
		$this->init_evo_product();
		
		// Init action
		do_action( 'eventon_init' );

	}

	// URL Reqrite
		function rewrite_tag(){
			global $wp,$wp_rewrite;
			$wp->add_query_var('ri');

			add_rewrite_tag('%ri%','([0-9]+)');
			//add_rewrite_rule( '^ri/[0-9]+)/?','&ri=$matches[1]', 'top' );
			//add_rewrite_rule('^/ri/([0-9]+)/', '?ri=$matches[1]', 'top');
			add_rewrite_rule(
				'events/([^/]+)/ri/([^/]+)/?', 
				'index.php?pagename=$1&post_type=ajde_events&ri=$2', 
				'top'
			);
			//flush_rewrite_rules(false);
		}

	// Initiate evo product
		function init_evo_product(){
			$ADDON = new evo_addons(array(
				'ID'=> 'EVO',
				'version'=> $this->version,
				'slug'=>strtolower(EVENTON_BASE),
				'plugin_slug'=>AJDE_EVCAL_BASENAME,
				'name'=>EVENTON_BASE
			));
		}

	/** register_widgets function. */
		function register_widgets() {
			include_once( 'includes/class-evo-widget-main.php' );
		}

	// CRON
		function new_schedule(){
			 if(!isset($schedules["weekly"])){
		        $schedules["weekly"] = array(
		            'interval' => 60*60*24*7,
		            'display' => __('Once every week'));
		    }
		    if(!isset($schedules["3days"])){
		        $schedules["3days"] = array(
		            'interval' => 60*60*24*3,
		            'display' => __('Every three days'));
		    }
		    return $schedules;
		}
	
	// MOVED functions
		/*** output the inpage popup window for eventon	 */
			public function output_eventon_pop_window($arg){
				global $ajde;
				$ajde->wp_admin->lightbox_content($arg);			
			}
		/*	Legend popup box across wp-admin	*/
			public function throw_guide($content, $position='', $echo=true){
				global $ajde;  
				if(!is_admin()) return false;
				$content = $ajde->wp_admin->tooltips($content, $position);
				if($echo){ echo $content;  }else{ return $content; }			
			}
		/* EMAIL functions */
			public function get_email_part($part){
				return $this->frontend->get_email_part($part);
			}
		/**
		 * body part of the email template loading
		 * @update 2.2.24
		 */
			public function get_email_body($part, $def_location, $args='', $paths=''){
				return $this->frontend->get_email_body($part, $def_location, $args='', $paths='');
			}
		
	/** Activate function to store version.	 */
		public function activate(){
			set_transient( '_evo_activation_redirect', 1, 60 * 60 );		
			do_action('eventon_activate');
		}	
		// update function
			public function update(){
				//set_transient( '_evo_activation_redirect', 1, 60 * 60 );		
			}

	// deactivate eventon
		public function deactivate(){	
			do_action('eventon_deactivate');
		}
	
	/** Ensure theme and server variable compatibility and setup image sizes.. */
		public function setup_environment() {
			// Post thumbnail support
			if ( ! current_theme_supports( 'post-thumbnails', 'ajde_events' ) ) {
				add_theme_support( 'post-thumbnails' );
				remove_post_type_support( 'post', 'thumbnail' );
				remove_post_type_support( 'page', 'thumbnail' );
			} else {
				add_post_type_support( 'ajde_events', 'thumbnail' );
			}
		}
		
	/** LOAD Backender UI and functionalities for settings. */
		public function load_ajde_backender(){			
			global $ajde;
			$ajde->load_ajde_backender();
		}	
		public function enqueue_backender_styles(){
			global $ajde;
			$ajde->load_ajde_backender();
		}
		public function register_backender_scripts(){
			global $ajde;
			$ajde->load_ajde_backender();			
		}
		
	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 * Admin Locale. Looks in:
	 * - WP_LANG_DIR/eventon/eventon-admin-LOCALE.mo
	 * - WP_LANG_DIR/plugins/eventon-admin-LOCALE.mo
	 *
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'eventon' );
		
		load_textdomain( 'eventon', WP_LANG_DIR . "/eventon/eventon-admin-".$locale.".mo" );
		load_textdomain( 'eventon', WP_LANG_DIR . "/plugins/eventon-admin-".$locale.".mo" );		
		
		if ( is_admin() ) {
			load_plugin_textdomain( 'eventon', false, plugin_basename( dirname( __FILE__ ) ) . "/lang" );
		}

		// frontend - translations are controlled by myeventon settings> language	
	}
	
	public function get_current_version(){
		return $this->version;
	}	
	
	// return eventon option settings values **/
		public function evo_get_options($field, $array_field=''){
			if(!empty($array_field)){
				$options = get_option($field);
				$options = $options[$array_field];
			}else{
				$options = get_option($field);
			}		
			return !empty($options)?$options:null;
		}

	// deprecated function after 2.2.12
		public function addon_has_new_version($values){}

	// template locator function to use for addons
		function template_locator($file, $default_locations, $append='', $args=''){
			$childThemePath = get_stylesheet_directory();

			// Paths to check
			$paths = apply_filters('evo_file_template_paths', array(
				1=>TEMPLATEPATH.'/'.$this->template_url. $append, // TEMPLATEPATH/eventon/--append--
				2=>$childThemePath.'/',
				3=>$childThemePath.'/'.$this->template_url. $append,
			));

			$location = $default_locations .$file;
			// FILE Exist
			if ( $file ) {			
				// each path
				foreach($paths as $path){				
					if(file_exists($path.$file) ){	
						$location = $path.$file;	
						break;
					}
				}
			}

			return $location;
		}

	// Events archive page content
		function archive_page(){
			$evOpt = evo_get_options('1');

			$archive_page_id = evo_get_event_page_id($evOpt);

			// check whether archieve post id passed
			if($archive_page_id){

				$archive_page  = get_page($archive_page_id);	
				echo "<div class='wrapper'>";
				echo apply_filters('the_content', $archive_page->post_content);
				echo "</div>";

			}else{
				echo "<p>ERROR: Please select a event archive page in eventON Settings > Events Paging > Select Events Page</p>";
			}
		}

	
}

}// class exists


function EVO(){
	return EventON::instance();
}
/** Init eventon class */
$GLOBALS['eventon'] = EVO();

//include_once('admin/update-notifier.php');	
?>