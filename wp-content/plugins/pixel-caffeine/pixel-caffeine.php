<?php
/**
 * Plugin Name:     Pixel Caffeine
 * Plugin URI:      https://adespresso.com/
 * Description:     The simplest and easiest way to manage your Facebook Pixel. Create laser focused custom audiences on WordPress for 100% free.
 * Author:          AdEspresso
 * Author URI:      https://adespresso.com/
 * Text Domain:     pixel-caffeine
 * Domain Path:     /languages
 * Version:         2.0.7
 * WC requires at least: 3.0.0
 * WC tested up to: 3.5.2
 *
 * @package         PixelCaffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'PixelCaffeine' ) ) :

	/**
	 * Main PixelCaffeine Class.
	 *
	 * @class PixelCaffeine
	 * @version	2.0.7
	 */
	final class PixelCaffeine {

		/** @var string PixelCaffeine version. */
		public $version = '2.0.7';

		/** @var PixelCaffeine The single instance of the class. */
		protected static $_instance = null;

		/**
		 * Main PixelCaffeine Instance.
		 *
		 * Ensures only one instance of PixelCaffeine is loaded or can be loaded.
		 *
		 * @static
		 * @see PixelCaffeine()
		 * @return PixelCaffeine - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
				self::$_instance->setup();
			}
			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'pixel-caffeine' ), '1.0.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'pixel-caffeine' ), '1.0.0' );
		}

		/**
		 * PixelCaffeine Constructor.
		 */
		public function __construct() {
			define( 'AEPC_PLUGIN_FILE', __FILE__ );
			define( 'AEPC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			define( 'AEPC_PIXEL_VERSION', $this->version );
			define( 'AEPC_WOO_VERSION_REQUIREMENT', '3.0.0' );

			if ( ! defined('AEPC_PIXEL_DEBUG') ) {
				define( 'AEPC_PIXEL_DEBUG', false );
			}

			do_action( 'pixel_caffeine_loaded' );
		}

		/**
		 * Hook into actions and filters.
		 */
		private function setup() {

			// Register tasks on register_plugin_activation
			register_activation_hook( __FILE__, array( 'AEPC_Admin', 'register_plugin_activation' ) );

			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'init', array( 'AEPC_Addons_Support', 'init' ), 5 ); // priority 5 is for EDD.
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {
			require_once( dirname(__FILE__) . '/vendor/autoload.php' );

			// Include
			include_once( 'includes/functions-helpers.php' );

			// Admin includes.
			if ( is_admin() || defined('DOING_CRON') && DOING_CRON ) {
				add_action( 'plugins_loaded', array( 'AEPC_Admin', 'init' ) );
				add_action( 'plugins_loaded', array( 'AEPC_Cron', 'init' ) );
			}

			// Frontend inclusions.
			if ( ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) ) {
				// Hook to 'wp' because we need to check the current user
				add_action( 'init', array( $this, 'frontend_includes' ), 1 );
			}
		}

		/**
		 * Include required frontend files.
		 */
		public function frontend_includes() {
			if ( $this->is_pixel_enabled() ) {
				AEPC_Pixel_Scripts::init();
			}
		}

		/**
		 * Check option to check the pixel is enabled or not
		 */
		public function is_pixel_enabled() {
			return 'yes' == get_option( 'aepc_enable_pixel' )
			       && '' != $this->get_pixel_id()
			       && $this->is_pixel_enabled_for_the_user();
		}

		/**
		 * Check if the pixel could be fired for the current user
		 */
		public function is_pixel_enabled_for_the_user() {
			// In admin track this as always true, in order to view the options properly
			if ( is_admin() ) {
				return true;
			}

			if ( 'yes' == get_option( 'aepc_no_pixel_when_logged_in' ) ) {

				// Retrieve the user roles the admin has chosen in the option
				$not_allowed_roles = get_option( 'aepc_no_pixel_if_user_is' );

				foreach ( $not_allowed_roles as $role ) {
					if ( current_user_can( $role ) ) {
						return false;
					}
				}

			}

			// If we arrive here it means the user has a role listed in the option
			return true;
		}

		/**
		 * Init PixelCaffeine when WordPress Initialises.
		 */
		public function init() {
			// Before init action.
			do_action( 'before_pixel_caffeine_init' );

			// Set up localisation.
			$this->load_plugin_textdomain();

			// Init action.
			do_action( 'pixel_caffeine_init' );
		}

		/**
		 * Load Localisation files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
		 *
		 * Locales found in:
		 *      - WP_LANG_DIR/pixel-caffeine/pixel-caffeine-LOCALE.mo
		 *      - WP_LANG_DIR/plugins/pixel-caffeine-LOCALE.mo
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'pixel-caffeine' );

			load_textdomain( 'pixel-caffeine', WP_LANG_DIR . '/pixel-caffeine/pixel-caffeine-' . $locale . '.mo' );
			load_plugin_textdomain( 'pixel-caffeine', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Get Ajax URL.
		 *
		 * @return string
		 */
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function build_url( $path ) {
			return untrailingslashit( plugins_url( '/build/' . $path, __FILE__ ) );
		}

		/**
		 * Helper to get the pixel ID
		 */
		public function get_pixel_id() {
			return (string) get_option( 'aepc_pixel_id' );
		}

		/**
		 * Debug mode enabled
		 *
		 * @return bool
		 */
		public function is_debug_mode() {
			return 'yes' === get_option( 'aepc_enable_debug_mode' ) || ( defined( 'AEPC_PIXEL_DEBUG' ) && AEPC_PIXEL_DEBUG );
		}
	}

endif;

/**
 * Main instance of PixelCaffeine.
 *
 * @return PixelCaffeine
 */
function PixelCaffeine() {
	return PixelCaffeine::instance();
}

PixelCaffeine();
