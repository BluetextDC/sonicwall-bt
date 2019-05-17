<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://davenicosia.com
 * @since      1.0.0
 *
 * @package    WP_Compear
 * @subpackage WP_Compear/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_Compear
 * @subpackage WP_Compear/includes
 * @author     Dave Nicosia <email@davenicosia.com>
 */
class WP_Compear {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WP_Compear_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $WP_Compear    The string used to uniquely identify this plugin.
	 */
	protected $WP_Compear;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->WP_Compear = 'wp-compear';
		$this->version = '1.1.1';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_Compear_Loader. Orchestrates the hooks of the plugin.
	 * - WP_Compear_i18n. Defines internationalization functionality.
	 * - WP_Compear_Admin. Defines all hooks for the admin area.
	 * - WP_Compear_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-compear-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-compear-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-compear-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-compear-public.php';



		// // this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
		// define( 'WPCOMPEAR_URL', 'http://wpcompear.com' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

		// // the name of your product. This should match the download name in EDD exactly
		// define( 'WPCOMPEAR_ITEM_NAME', 'WP ComPEAR Unlimited Site License' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file

		if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
			// load our custom updater
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/EDD_SL_Plugin_Updater.php';
		}

		$this->loader = new WP_Compear_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Compear_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WP_Compear_i18n();
		$plugin_i18n->set_domain( $this->get_WP_Compear() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WP_Compear_Admin( $this->get_WP_Compear(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'WP_Compear_add_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'WP_Compear_options' );
		$this->loader->add_action( 'init', $plugin_admin, 'create_wp_compear_lists' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'wpcompear_create_meta_boxes' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'wp_compear_save_data' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'wp_compear_save_data' );

		$this->loader->add_action( 'admin_action_duplicate_compear_post', $plugin_admin, 'duplicate_compear_post' );

		$this->loader->add_action( 'post_row_actions', $plugin_admin, 'wpcompear_duplicate_post_link', 10, 2 );

		$this->loader->add_action( 'media_buttons_context', $plugin_admin, 'wp_compear_insert_shortcode_btn' );
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'wp_compear_add_inline_popup_content' );

		$this->loader->add_filter( 'manage_posts_columns', $plugin_admin, 'revealid_add_id_column', 5 );

		$this->loader->add_action( 'manage_posts_custom_column', $plugin_admin, 'revealid_id_column_content',5,2 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WP_Compear_Public( $this->get_WP_Compear(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'init', $plugin_public, 'WP_Compear_create_shortcode' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_WP_Compear() {
		return $this->WP_Compear;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WP_Compear_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
