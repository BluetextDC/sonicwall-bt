<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Monolog\Logger;
use PixelCaffeine\ProductCatalog\Admin\Metaboxes;
use PixelCaffeine\ProductCatalog\ConfigurationDefaults;
use PixelCaffeine\ProductCatalog\DbProvider;
use PixelCaffeine\ProductCatalog\ProductCatalogs;

/**
 * @class AEPC_Admin
 */
class AEPC_Admin {

	/** Plugin name used on menu and page titles */
	const PLUGIN_NAME = 'Pixel Caffeine';

	/** @var array List of instances for each admin page */
	static $pages = array();

	/** @var array The full list of settings, divided by pages */
	static $settings = array();

	/** @var AEPC_Facebook_Adapter */
	static $api = null;

	/** @var ProductCatalogs */
	static $product_catalogs_service = null;

	/** @var AEPC_Admin_Logger */
	static $logger = null;

	/**
	 * AEPC_Admin Constructor.
	 */
	public static function init() {
		AEPC_Admin_Install::init();
		AEPC_Admin_Menu::init();
		AEPC_Admin_CA_Manager::init();
		AEPC_Admin_Notices::init();
		AEPC_Admin_Handlers::init();
		AEPC_Admin_Ajax::init();

		self::$api = new AEPC_Facebook_Adapter();
		self::$product_catalogs_service = new ProductCatalogs( AEPC_Admin::$api, new DbProvider, new ConfigurationDefaults, new Metaboxes );
		self::$logger = new AEPC_Admin_Logger();

		self::init_hooks();
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public static function init_hooks() {
		add_action( 'admin_init', array( __CLASS__, 'redirect_to_dashboard_on_activation' ), 1 );
		add_action( 'admin_init', array( __CLASS__, 'redirect_to_dashboard_on_update' ), 1 );
		add_filter( 'plugin_action_links_' . plugin_basename( AEPC_PLUGIN_FILE ), array( __CLASS__, 'admin_plugin_settings_link' ) );
		add_filter( 'admin_body_class', array( __CLASS__, 'add_body_class' ), 99 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

		// Define some hooks of background processing class
		self::$product_catalogs_service->setup();
		self::$logger->setup();
	}

	/**
	 * Include admin files conditionally.
	 */
	public static function conditional_includes() {
		if ( ! $screen = get_current_screen() ) {
			return;
		}

		switch ( $screen->id ) {
			default:
				break;
		}
	}

	/**
	 * Register a flag useful for redirect to dashboard after activation
	 */
	public static function register_plugin_activation() {
		add_option( 'aepc_just_activated', true );
	}

	/**
	 * Redirect to dashboard on plugin activation, if plugin isn't configured yet
	 */
	public static function redirect_to_dashboard_on_activation() {
		if ( get_option( 'aepc_just_activated' ) && ! self::is_plugin_configured() ) {
			delete_option( 'aepc_just_activated' );
			wp_redirect( self::get_page('dashboard')->get_view_url() );
			exit();
		}
	}

	/**
	 * Redirect to dashboard on plugin update
	 */
	public static function redirect_to_dashboard_on_update() {
		if ( ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && $version = get_option( 'aepc_updated' ) ) {
			delete_option( 'aepc_updated' );
			wp_redirect( self::get_page('welcome')->get_view_url( array(
				'back_to' => add_query_arg( null, null ),
				'version' => $version
			) ) );
			exit();
		}
	}

	/**
	 * Add settings link into the actions of plugins list
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	public static function admin_plugin_settings_link( $links ) {
		$dashboard_link = '<a href="'.esc_url( self::get_page('dashboard')->get_view_url() ).'">'.__('Dashboard', 'pixel-caffeine').'</a>';
		$settings_link = '<a href="'.esc_url( self::get_page('general-settings')->get_view_url() ).'">'.__('Settings', 'pixel-caffeine').'</a>';
		array_unshift( $links, $settings_link );
		array_unshift( $links, $dashboard_link );

		return $links;
	}

	/**
	 * Return an instance of a single page
	 *
	 * @param string $page
	 *
	 * @return AEPC_Admin_View
	 */
	public static function get_page( $page = 'dashboard' ) {
		if ( ! isset( self::$pages[ $page ] ) ) {
			self::$pages[ $page ] = new AEPC_Admin_View( $page );
		}

		return self::$pages[ $page ];
	}

	/**
	 * Get the setting of a tab
	 *
	 * @param string $tab
	 *
	 * @return array
	 */
	public static function get_settings_of( $tab ) {
		if ( isset( self::$settings[ $tab ] ) ) {
			return self::$settings[ $tab ];
		}

		$page = self::get_page( $tab );
		self::$settings[ $tab ] = empty( $page ) ? array() : $page->get_settings();

		return self::$settings[ $tab ];
	}

	/**
	 * Get all id of all fields
	 *
	 * @param string $tab
	 *
	 * @return array
	 */
	public static function get_setting_ids( $tab = '' ) {
		$settings = self::get_settings_of( $tab );
		return array_keys( $settings );
	}

	/**
	 * Return array of all fields and own defaults
	 *
	 * @param string $tab
	 *
	 * @return array
	 */
	public static function get_setting_defaults( $tab = '' ) {
		$settings = self::get_settings_of( $tab );
		$defaults = array();

		foreach ( $settings as $option_id => $option ) {
			$defaults[ $option_id ] = ! empty( $option['default'] ) ? $option['default'] : '';
		}

		return $defaults;
	}

	/**
	 * Locate admin template file
	 *
	 * @param string $template The name of template.
	 *
	 * @return string
	 */
	public static function locate_template( $template ) {
		return PixelCaffeine()->plugin_path() . '/includes/admin/templates/' . $template;
	}

	/**
	 * Load the admin template
	 *
	 * @param $template string The name of template.
	 * @param array $args
	 */
	public static function get_template( $template, $args = array() ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		$located = self::locate_template( $template );

		file_exists( $located ) && include( $located );
	}

	/**
	 * Save settings
	 *
	 * Only settings of the actual tab will be saved, settings of other tab will be ignored
	 * All checkboxes will have 'yes' or 'no' value, instead the value of other types if not passed, will be ignored.
	 *
	 * This method must be called by a bind method, triggered on some event
	 *
	 * @param array $post_data
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function save_settings( $post_data ) {

		// Get settings for page
		$tab_args = self::get_settings_of( $post_data['tab'] );

		// Save facebook options if defined both account id and pixel id
		if ( ! empty( $post_data['aepc_account_id'] ) && ! empty( $post_data['aepc_pixel_id'] ) ) {
			self::save_facebook_options( $post_data );

			// Remove two keys, to exclude them from general saving below
			unset( $post_data['aepc_account_id'], $post_data['aepc_pixel_id'] );
		}

		// Save data
		foreach ( $tab_args as $option_id => $option ) {
			if ( 'checkbox' == $option['type'] ) {
				$post_data[ $option_id ] = isset( $post_data[ $option_id ] ) ? 'yes' : 'no';
			} // Leave unchanged if not existing
			elseif ( ! isset( $post_data[ $option_id ] ) ) {
				continue;
			}

			// validation
			if ( 'array' == $option['type'] ) {
				if ( ! is_array( $post_data[ $option_id ] ) ) {
					$post_data[ $option_id ] = explode( ',', $post_data[ $option_id ] );
				}
				$value = array_map( 'trim', array_filter( (array) $post_data[ $option_id ] ) );
			} else {
				$value = sanitize_text_field( $post_data[ $option_id ] );
			}

			// Check pixel id format
			if ( 'aepc_pixel_id' === $option_id && ! AEPC_Track::validate_pixel_id( $post_data[ $option_id ] ) ) {
				AEPC_Admin_Notices::add_notice( 'error', $option_id, __( 'The Pixel ID value must contains only numbers and must be 15 digits length.', 'pixel-caffeine' ) );
				continue;
			}

			update_option( $option_id, $value );
		}

		// Throw exception if any error occurred
		if ( AEPC_Admin_Notices::has_notice( 'error' ) ) {
			throw new Exception( __( '<strong>Some option cannot be saved</strong> Please, check errors below.', 'pixel-caffeine' ) );
		}

		// If at least once the user save the settings, set the plugin configured
		self::set_plugin_configured();
	}

	/**
	 * Save the business ID into the DB
	 *
	 * @param $business_id
	 */
	public static function save_business_id( $business_id ) {
		update_option( 'aepc_business_id', $business_id );
		self::$api->set_business_id( $business_id );
	}

	/**
	 * Save the account and pixel ID defined when the user is logged in facebook
	 *
	 * @param array $post_data The $_POST data containing aepc_account_id and aepc_pixel_id
	 */
	public static function save_facebook_options( $post_data ) {
		$account = json_decode( $post_data['aepc_account_id'] );
		$pixel = json_decode( $post_data['aepc_pixel_id'] );

		update_option( 'aepc_account_id', $account->id );
		set_transient( 'aepc_account_name_' . $account->id, $account->name, WEEK_IN_SECONDS );
		update_option( 'aepc_pixel_id', $pixel->id );
		set_transient( 'aepc_pixel_name_' . $pixel->id, $pixel->name, WEEK_IN_SECONDS );

		// Save the associated Business ID
		self::$api->set_account_id( $account->id );
		self::$api->set_pixel_id( $pixel->id );
		self::save_business_id( self::$api->get_business_id_from_account_id() );

		// Enable pixel
		update_option( 'aepc_enable_pixel', 'yes' );

		// Set the plugin as configured when the plugin is just installed
		self::set_plugin_configured();
	}

	/**
	 * Convert the input data from request in structured array to save, used on save and edit actions
	 *
	 * @param array $post_data The raw data from request
	 *
	 * @return array
	 * @throws Exception
	 */
	protected static function conversion_post_data_adapter( $post_data = array() ) {
		$post_data = wp_parse_args( $post_data, array(
			'event_name' => '',
			'event_trigger_on' => '',
			'event_url' => '',
			'event_css' => '',
			'event_standard_events' => '',
			'event_fire_delay' => '',
			'event_name_custom' => '',
			'event_enable_advanced_data' => '',
			'event_custom_params' => array(),
		) );

		$raw_data = array(
			'name'               => sanitize_text_field( $post_data['event_name'] ),
			'trigger'            => sanitize_text_field( $post_data['event_trigger_on'] ),
			'url'                => sanitize_text_field( $post_data['event_url'] ),
			'css'                => sanitize_text_field( $post_data['event_css'] ),
			'event'              => sanitize_text_field( $post_data['event_standard_events'] ),
			'delay'              => sanitize_text_field( $post_data['event_fire_delay'] ),
			'custom_event_name'  => sanitize_text_field( $post_data['event_name_custom'] ),
			'pass_advanced_data' => ! empty( $post_data['event_enable_advanced_data'] ),
			'value'              => sanitize_text_field( $post_data['event_field_value'] ),
			'currency'           => sanitize_text_field( $post_data['event_field_currency'] ),
			'content_name'       => sanitize_text_field( $post_data['event_field_content_name'] ),
			'content_category'   => sanitize_text_field( $post_data['event_field_content_category'] ),
			'content_ids'        => sanitize_text_field( $post_data['event_field_content_ids'] ),
			'content_type'       => sanitize_text_field( $post_data['event_field_content_type'] ),
			'num_items'          => sanitize_text_field( $post_data['event_field_num_items'] ),
			'search_string'      => sanitize_text_field( $post_data['event_field_search_string'] ),
			'status'             => sanitize_text_field( $post_data['event_field_status'] ),
			'custom_params'      => $post_data['event_custom_params'],
		);

		// Throw exception if any error occurred
		if ( AEPC_Admin_Notices::has_notice( 'error' ) ) {
			throw new Exception( __( 'Please, check fields errors below.', 'pixel-caffeine' ) );
		}

		// Structure data
		$track = array(
			'name'          => $raw_data['name'],
			'trigger'       => $raw_data['trigger'],
			'url'           => $raw_data['url'],
			'css'           => $raw_data['css'],
			'event'         => $raw_data['event'],
			'delay'         => $raw_data['delay'],
			'params'        => array(),
			'custom_params' => array(),
		);

		// Get custom name for the event name if it is custom one
		if ( AEPC_Track::is( 'custom', $raw_data['event'] ) ) {
			$track['event'] = $raw_data['custom_event_name'];
		}

		// data
		if ( $raw_data['pass_advanced_data'] ) {

			// Set data for standard events
			if ( $fields = AEPC_Track::get_standard_event_fields( $raw_data['event'] ) ) {

				foreach ( $fields as $field ) {
					if ( 'content_ids' == $field && ! empty( $raw_data[ $field ] ) ) {
						$raw_data[ $field ] = array_map( 'trim', explode( ',', $raw_data[ $field ] ) );
					}

					if ( ! empty( $raw_data[ $field ] ) ) {
						$track['params'][ $field ] = $raw_data[ $field ];
					}
				}
			}

			// Add custom parameters
			if ( ! empty( $raw_data['custom_params'] ) ) {
				foreach ( $raw_data['custom_params'] as $param ) {
					if ( ! empty( $param['key'] ) && ! empty( $param['value'] ) ) {
						$track['custom_params'][ $param['key'] ] = $param['value'];
					}
				}
			}

		}

		return $track;
	}

	/**
	 * Save the conversions events added by user in admin page
	 *
	 * This method must be called by a bind method, triggered on some event
	 *
	 * @param array $post_data
	 */
	public static function save_events( $post_data ) {
		$events = AEPC_Track::get_conversions_events();
		update_option( 'aepc_conversions_events', array_merge( $events, array( self::conversion_post_data_adapter( $post_data ) ) ) );
	}

	/**
	 * Edit a conversion event
	 *
	 * This method must be called by a bind method, triggered on some event
	 *
	 * @param array $post_data
	 */
	public static function edit_event( $post_data ) {
		$event_id = intval( $post_data['event_id'] );
		$events   = AEPC_Track::get_conversions_events();

		if ( isset( $events[ $event_id ] ) ) {
			$events[ $event_id ] = self::conversion_post_data_adapter( $post_data );

			// Fix value of url/css fields
			if ( 'css_selector' === $events[ $event_id ]['trigger'] ) {
				$events[ $event_id ]['url'] = '';
			} else {
				$events[ $event_id ]['css'] = '';
			}

			update_option( 'aepc_conversions_events', $events );
		}
	}

	/**
	 * Delete conversion event
	 *
	 * @param int $id
	 */
	public static function delete_event( $id ) {
		$events = AEPC_Track::get_conversions_events();
		if ( ! empty( $events[ $id ] ) ) {
			unset( $events[ $id ] );
			update_option( 'aepc_conversions_events', $events );
		}
	}

	/**
	 * Check if plugin is been configured by user or not
	 *
	 * @return bool
	 */
	public static function is_plugin_configured() {
		return 'yes' === get_option( 'aepc_configured' );
	}

	/**
	 * Set plugin configured by adding an option set to yes
	 */
	public static function set_plugin_configured() {
		add_option( 'aepc_configured', 'yes' );
	}

	/**
	 * Delete the flag option set if plugin is configured or not
	 */
	public static function set_plugin_not_configured() {
		delete_option( 'aepc_configured' );
	}

	/**
	 * Enqueue styles and scripts in admin
	 */
	public static function enqueue_assets( $hook_suffix ) {
		$suffix = SCRIPT_DEBUG ? '' : '.min';

		// Register common assets
		wp_register_style( 'aepc-menu', PixelCaffeine()->build_url( 'wpcommon.css' ) );
		wp_enqueue_style( 'aepc-menu' );

		// General settings admin page.
		if ( in_array( $hook_suffix, array( AEPC_Admin_Menu::$hook_page ) ) ) {

			// Add wp thickbox library to launch plugin information popup on ecommerce box
			add_thickbox();

			wp_register_script( 'aepc-admin-settings', PixelCaffeine()->build_url( 'admin.js' ), array( 'jquery', 'wp-util' ), PixelCaffeine()->version, true );
			wp_enqueue_script( 'aepc-admin-settings' );

			// Script util arguments
			$script_args = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'unsaved' => __( 'You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?', 'pixel-caffeine' ),
				'switch_unsaved' => __( 'You need to save!', 'pixel-caffeine' ),

				'ajax_form_saving' => __( 'Saving...', 'pixel-caffeine' ),

				'tooltip_clipboard_copied' => __( 'Copied!', 'pixel-caffeine' ),
				'tooltip_clipboard_no_support' => __( 'No support :(', 'pixel-caffeine' ),
				'tooltip_clipboard_mac_copy_suggestion' => __( 'Press ⌘-{{{ key }}} to copy', 'pixel-caffeine' ),
				'tooltip_clipboard_win_copy_suggestion' => __( 'Press Ctrl-{{{ key }}} to copy', 'pixel-caffeine' ),
				'tooltip_clipboard_mac_cut_suggestion' => __( 'Press ⌘-{{{ key }}} to cut', 'pixel-caffeine' ),
				'tooltip_clipboard_win_cut_suggestion' => __( 'Press Ctrl-{{{ key }}} to cut', 'pixel-caffeine' ),

				'filter_any' => __( 'any', 'pixel-caffeine' ),
				'filter_custom_field_placeholder' => __( 'Write the key or select from below', 'pixel-caffeine' ),
				'filter_saving' => __( 'Saving...', 'pixel-caffeine' ),
				'filter_no_data_error' => __( '<strong>Can\'t add filter</strong> You have to select an event type', 'pixel-caffeine' ),
				'filter_no_condition_error' => __( '<strong>Can\'t add filter</strong> You have to define at least one condition', 'pixel-caffeine' ),

				'fb_option_account_id_placeholder' => __( 'Select an account ID', 'pixel-caffeine' ),
				'fb_option_no_account' => __( 'No Ad account found', 'pixel-caffeine' ),
				'fb_option_no_pixel' => __( 'No pixel found', 'pixel-caffeine' ),
				'fb_option_no_product_feeds' => __( 'No products feeds found', 'pixel-caffeine' ),

				'highcharts_range_today' => __( 'Today', 'pixel-caffeine' ),
				'highcharts_range_yesterday' => __( 'Yesterday', 'pixel-caffeine' ),
				'highcharts_range_2days' => __( '2 Days', 'pixel-caffeine' ),
				'highcharts_range_7days' => __( '7 Days', 'pixel-caffeine' ),
				'highcharts_range_14days' => __( '14 Days', 'pixel-caffeine' ),
			);

			// Add ajax utils
			foreach( AEPC_Admin_Ajax::$ajax_actions as $action ) {
				$tab = ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'dashboard';

				// Hooks for pages
				$page_hooks = array(
					'dashboard' => array(
						'get_pixel_stats'
					),

					'custom-audiences' => array(
						'get_custom_fields',
						'get_languages',
						'get_device_types',
						'get_categories',
						'get_tags',
						'get_posts',
						'get_dpa_params',
						'get_filter_statement',
						'get_currencies'
					),

					'conversions' => array(),

					'general-settings' => array(
						'get_account_ids',
						'get_pixel_ids',
						'get_custom_fields',
					),

					'product-catalog' => array(
						'get_posts',
					),
				);

				if (
					in_array( $action, array_merge( $page_hooks['dashboard'], $page_hooks['conversions'], $page_hooks['custom-audiences'], $page_hooks['general-settings'] ) )
					&& ( ! isset( $page_hooks[ $tab ] ) || ! in_array( $action, $page_hooks[ $tab ] ) )
				) {
					continue;
				}

				$script_args['actions'][ $action ] = array(
					'name' => 'aepc_' . $action,
					'nonce' => wp_create_nonce( $action )
				);
			}

			wp_localize_script( 'aepc-admin-settings', 'aepc_admin', $script_args );

			// Register assents for the views
			wp_register_style( 'aepc-admin', PixelCaffeine()->build_url( 'admin.css' ) );
			wp_enqueue_style( 'aepc-admin' );

		}
	}

	/**
	 * Add a general class to the body
	 *
	 * @param $classes
	 *
	 * @return string
	 */
	public static function add_body_class( $classes ) {
		return $classes . ' pixel-caffeine ';
	}

	/**
	 * Get the pixel statistics for all hours between today and two weeks ago
	 *
	 * @param string $interval
	 *
	 * @return array|WP_Error
	 */
	public static function get_pixel_stats_sets( $interval = 'hourly' ) {
		try {
			/// Get pixel stats from facebook
			if ( false === ( $stats = get_transient( 'aepc_pixel_stats' ) ) ) {
				$fb    = AEPC_Admin::$api;
				$stats = $fb->get_pixel_stats();
				set_transient( 'aepc_pixel_stats', $stats, HOUR_IN_SECONDS );
			}

			// Convert array to work better after
			foreach ( $stats as $i => $stat ) {
				$stat->timestamp = new DateTime( $stat->timestamp );
				$stat->timestamp->setTime(0, 0);
				$stats[ $stat->timestamp->format( DATE_ISO8601 ) ] = $stat->data[0]->count;
				unset( $stats[ $i ] );
			}

			$counter = new DateTime( '2 weeks ago' );
			$counter->setTime( 0, 0 );
			$tomorrow = new DateTime( 'tomorrow' );
			$counters = array();

			do {
				//			$counters[] = isset( $stats[ $counter->format( DATE_ISO8601 ) ] ) ? $stats[ $counter->format( DATE_ISO8601 ) ] : 0;
				$counters[] = array(
					$counter->getTimestamp() * 1000,
					isset( $stats[ $counter->format( DATE_ISO8601 ) ] ) ? $stats[ $counter->format( DATE_ISO8601 ) ] : 0
				);

				$counter->modify( '+1 hour' );
			} while ( $counter < $tomorrow );

			return $counters;

		} catch ( Exception $e ) {
			return new WP_Error( $e->getCode(), $e->getMessage() );
		}
	}

	/**
	 * SIDEBAR TOOLS
	 */

	/**
	 * Fetch the banners for the sidebar from a remote JSON file
	 */
	public static function fetch_sidebar() {
		if ( false === ( $sidebar = get_transient( 'aepc_sidebar' ) ) ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$response = wp_remote_get( 'https://adespresso.com/wp-content/uploads/pixel-caffeine-sidebar.json' );

				if ( is_wp_error( $response ) ) {
					return false;
				}

				$body = wp_remote_retrieve_body( $response );
				$sidebar = json_decode( $body );

				set_transient( 'aepc_sidebar', $sidebar, 6 * HOUR_IN_SECONDS );
			} else {
				return array();
			}
		}

		return $sidebar;
	}

	/**
	 * Fetch the blog posts from official site for sidebar
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public static function fetch_sidebar_posts( $args = array() ) {
		$args = wp_parse_args( (array) $args, array(
			'feed' => '',
			'items' => 3
		) );

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $args['feed'] ) ) {
			$rss = fetch_feed( $args['feed'] );

			if ( is_wp_error( $rss ) ) {
				if ( is_admin() || current_user_can('manage_ads') ) {
					return array(
						'error' => '<p>' . sprintf( __( '<strong>RSS Error</strong>: %s', 'pixel-caffeine' ), $rss->get_error_message() ) . '</p>'
					);
				}

				return array();
			}

			if ( !$rss->get_item_quantity() ) {
				$rss->__destruct();
				unset($rss);

				return array(
					'error' => '<p>' . __( 'An error has occurred, which probably means the feed is down. Try again later.', 'pixel-caffeine' ) . '</p>'
				);
			}

			$posts = array();

			foreach ( $rss->get_items( 0, $args['items'] ) as $item ) {
				/** @var SimplePie_Item $item */

				$posts[] = array(
					'link' => $item->get_link(),
					'title' => $item->get_title(),
					'description' => $item->get_description(),
					'date' => $item->get_date()
				);
			}

			set_transient( 'aepc_rss_posts', $posts, 12 * HOUR_IN_SECONDS );

		} else {
			$posts = (array) get_transient( 'aepc_rss_posts' );
		}

		return $posts;
	}

	/**
	 * Clear the transients saved on db
	 */
	public static function clear_transients() {
		global $wpdb;

		$tranients_to_clear = array(
			'aepc_account_name_%',
			'aepc_pixel_name_%',
			'aepc_pixel_stats',
			'aepc_sidebar',
			'aepc_rss_posts',
			'aepc_fb_users',
			'aepc_account_name_%',
			'aepc_pixel_name_%',
		);

		// Convert wildcard in complete transient name, in order to use the function delete_transient after.
		foreach ( $tranients_to_clear as $i => &$transient ) {
			if ( false !== strpos( $transient, '%' ) ) {
				$transients_found = $wpdb->get_col( $wpdb->prepare( "
SELECT DISTINCT REPLACE( REPLACE( option_name, '_transient_timeout_', '' ), '_transient_', '' ) 
FROM {$wpdb->options} 
WHERE option_name LIKE %s
", '%' . $transient ) );

				unset( $tranients_to_clear[ $i ] );

				foreach ( $transients_found as $transient_found ) {
					$tranients_to_clear[] = $transient_found;
				}
			}
		}

		// Delete transients.
		foreach ( $tranients_to_clear as $transient ) {
			delete_transient( $transient );
		}

		do_action( 'aepc_delete_transients' );
	}

	/**
	 * Clear the transients saved on db
	 */
	public static function reset_fb_connection() {
		delete_option( 'aepc_fb_access_token' );
		delete_option( 'aepc_fb_access_expired' );
		delete_option( 'aepc_fb_uuid' );
		delete_transient( 'aepc_fb_user' );

		do_action( 'aepc_reset_fb_connection' );
	}

}
