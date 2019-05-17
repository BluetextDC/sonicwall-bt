<?php

use PixelCaffeine\Admin\Response;
use PixelCaffeine\Logs\LogRepository;
use PixelCaffeine\ProductCatalog\Admin\Metaboxes;
use PixelCaffeine\ProductCatalog\BackgroundFeedSaver;
use PixelCaffeine\ProductCatalog\Configuration;
use PixelCaffeine\ProductCatalog\ConfigurationDefaults;
use PixelCaffeine\ProductCatalog\DbProvider;
use PixelCaffeine\ProductCatalog\Dictionary\FeedSaver;
use PixelCaffeine\ProductCatalog\Entity\ProductCatalog;
use PixelCaffeine\ProductCatalog\ProductCatalogManager;
use PixelCaffeine\ProductCatalog\ProductCatalogs;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @class AEPC_Admin_Handlers
 */
class AEPC_Admin_Handlers {

	/**
	 * AEPC_Admin_Handlers Constructor.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'admin_hooks' ) );
	}

	/**
	 * Hook actions on admin_init
	 */
	public static function admin_hooks() {
		// Fb connect/disconnect - Must be run before connect of Facebook adapter
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'pixel_disconnect' ), 4 );
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'save_facebook_options' ), 4 );

		// Conversions/events
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'save_settings' ), 5 );
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'save_events' ), 5 );
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'edit_event' ), 5 );
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'delete_event' ), 5 );

		// CA management
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'save_audience' ), 5 );
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'edit_audience' ), 5 );
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'duplicate_audience' ), 5 );
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'delete_audience' ), 5 );

		// Product Catalogs
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'save_product_catalog' ), 5 );
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'update_product_catalog' ), 5 );
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'delete_product_catalog_feed' ), 5 );
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'refresh_product_catalog_feed' ), 5 );
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'save_product_feed_refresh_interval' ), 5 );

		// Tools
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'clear_transients' ), 5 );

		// Logs
		add_action( 'load-' . AEPC_Admin_Menu::$hook_page, array( __CLASS__, 'download_log_report' ), 5 );
	}

	/**
	 * Simply delete the option saved with pixel ID
	 */
	public static function pixel_disconnect() {
		$screen = get_current_screen();

		if (
			empty( $screen->id )
			|| AEPC_Admin_Menu::$hook_page != $screen->id
			|| empty( $_GET['action'] )
			|| 'pixel-disconnect' != $_GET['action']
			|| empty( $_GET['_wpnonce'] )
			|| ! current_user_can( 'manage_ads' )
			|| ! wp_verify_nonce( $_GET['_wpnonce'], 'pixel_disconnect' )
		) {
			return;
		}

		// Delete the option
		delete_option( 'aepc_pixel_id' );

		// Send success notice
		AEPC_Admin_Notices::add_notice( 'success', 'main', __( 'Pixel ID disconnected.', 'pixel-caffeine' ) );

		// If all good, redirect in the same page
		self::redirect_to( remove_query_arg( array( 'action', '_wpnonce' ) ) );
	}

	/**
	 * Save the account id and pixel id
	 *
	 * @return bool
	 */
	public static function save_facebook_options() {
		if (
			empty( $_POST['action'] )
			|| 'aepc_save_facebook_options' != $_POST['action']
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'save_facebook_options' )
		) {
			return false;
		}

		try {

			if ( empty( $_POST['aepc_account_id'] ) ) {
				AEPC_Admin_Notices::add_notice( 'error', 'account_id', __( 'Set the account ID', 'pixel-caffeine' ) );
			}

			if ( empty( $_POST['aepc_pixel_id'] ) ) {
				AEPC_Admin_Notices::add_notice( 'error', 'pixel_id', __( 'Set the pixel ID', 'pixel-caffeine' ) );
			}

			if ( AEPC_Admin_Notices::has_notice( 'error' ) ) {
				AEPC_Admin_Notices::add_notice( 'error', 'main', __( 'Please, check again all fields value.', 'pixel-caffeine' ) );
				return false;
			}

			AEPC_Admin::save_facebook_options( stripslashes_deep( $_POST ) );

			// Send success notice
			AEPC_Admin_Notices::add_notice( 'success', 'main', __( 'Facebook Ad Account connected successfully.', 'pixel-caffeine' ) );

			// If all good, redirect in the same page
			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
				self::redirect_to( remove_query_arg( 'ref' ) );
			}

			return true;
		}

		catch( Exception $e ) {
			AEPC_Admin_Notices::add_notice( 'error', 'main', $e->getMessage() );
		}

		return false;
	}

	/**
	 * General method for all standard settings, defined on "settings" directory, triggered when a page form is submitted
	 */
	public static function save_settings() {
		$screen = get_current_screen();

		if (
			empty( $screen->id )
			|| AEPC_Admin_Menu::$hook_page != $screen->id
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_POST )
			|| empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'save_general_settings' )
		) {
			return;
		}

		try {

			// Save
			AEPC_Admin::save_settings( wp_unslash( $_POST ) );

			// Send success notice
			AEPC_Admin_Notices::add_notice( 'success', 'main', __( 'Settings saved properly.', 'pixel-caffeine' ) );

			// If all good, redirect in the same page
			self::redirect_to( remove_query_arg( 'ref' ) );
		}

		catch( Exception $e ) {
			AEPC_Admin_Notices::add_notice( 'error', 'main', __( 'Please, check again all fields value.', 'pixel-caffeine' ) );
		}
	}

	/**
	 * Save the conversions events added by user in admin page
	 *
	 * @return bool
	 */
	public static function save_events() {
		if (
			empty( $_POST )
			|| empty( $_POST['action'] )
			|| $_POST['action'] != 'aepc_save_tracking_conversion'
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'save_tracking_conversion' )
		) {
			return false;
		}

		try {

			// Save events
			AEPC_Admin::save_events( wp_unslash( $_POST ) );

			// Send success notice
			AEPC_Admin_Notices::add_notice( 'success', 'main', sprintf( __( '<strong>Conversion event added properly!</strong> Follow the instructions on %sthis link%s to verify if the pixel tracking event you added works properly.', 'pixel-caffeine' ), '<a href="https://developers.facebook.com/docs/facebook-pixel/using-the-pixel#verify">', '</a>' ) );

			return true;
		}

		catch ( Exception $e ) {
			AEPC_Admin_Notices::add_notice( 'error', 'main', $e->getMessage() );
			return false;
		}
	}

	/**
	 * Edit a conversion event
	 *
	 * @return bool
	 */
	public static function edit_event() {
		if (
			empty( $_POST )
			|| empty( $_POST['action'] )
			|| $_POST['action'] != 'aepc_edit_tracking_conversion'
			|| ! isset( $_POST['event_id'] )
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'edit_tracking_conversion' )
		) {
			return false;
		}

		try {

			// Edit event
			AEPC_Admin::edit_event( wp_unslash( $_POST ) );

			// Send success notice
			AEPC_Admin_Notices::add_notice( 'success', 'main', __( 'Conversion changed successfully.', 'pixel-caffeine' ) );

			return true;
		}

		catch ( Exception $e ) {
			AEPC_Admin_Notices::add_notice( 'error', 'main', $e->getMessage() );
			return false;
		}
	}

	/**
	 * Delete conversion event
	 */
	public static function delete_event() {
		$screen = get_current_screen();

		if (
			empty( $screen->id )
			|| AEPC_Admin_Menu::$hook_page != $screen->id
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_GET['_wpnonce'] )
			|| ! wp_verify_nonce( $_GET['_wpnonce'], 'delete_tracking_conversion' )
		) {
			return;
		}

		// Delete event
		AEPC_Admin::delete_event( intval( $_GET['id'] ) );

		// Send success notice
		AEPC_Admin_Notices::add_notice( 'success', 'main', __( 'Configuration removed properly!!', 'pixel-caffeine' ) );

		// Redirect to the same page
		self::redirect_to( remove_query_arg( array( 'id', '_wpnonce' ) ) );
	}

	/**
	 * CA MAnagement
	 */

	/**
	 * Add new custom audience
	 *
	 * @return bool
	 */
	public static function save_audience() {
		if (
			empty( $_POST['action'] )
			|| $_POST['action'] != 'aepc_add_custom_audience'
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'add_custom_audience' )
		) {
			return false;
		}

		try {

			// Save custom audience
			AEPC_Admin_CA_Manager::save( wp_unslash( $_POST ) );

			// Send success notice
			AEPC_Admin_Notices::add_notice( 'success', 'main', sprintf( __( '<strong>New custom audience added!</strong> You will find this new custom audience also in %syour facebook ad account%s.', 'pixel-caffeine' ), '<a href="https://www.facebook.com/ads/manager/audiences/manage/?act=' . AEPC_Admin::$api->get_account_id() . '" target="_blank">', '</a>' ) );

			// If all good, redirect in the same page
			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
				self::redirect_to( remove_query_arg( 'paged' ) );
			}

			return true;
		}

		catch ( Exception $e ) {
			AEPC_Admin_Notices::add_notice( 'error', 'main', $e->getMessage() );
		}

		return false;
	}

	/**
	 * Edit a conversion event
	 *
	 * @return bool
	 */
	public static function edit_audience() {
		if (
			! isset( $_POST['ca_id'] )
			|| empty( $_POST['action'] )
			|| $_POST['action'] != 'aepc_edit_custom_audience'
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'edit_custom_audience' )
		) {
			return false;
		}

		try {

			// Edit event
			AEPC_Admin_CA_Manager::edit( wp_unslash( $_POST ) );

			// Send success notice
			AEPC_Admin_Notices::add_notice( 'success', 'main', __( 'Custom audience changed successfully.', 'pixel-caffeine' ) );

			// Redirect to the same page
			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
				self::redirect_to( add_query_arg( null, null ) );
			}

			return true;
		}

		catch ( Exception $e ) {
			AEPC_Admin_Notices::add_notice( 'error', 'main', $e->getMessage() );
		}

		return false;
	}

	/**
	 * Duplicate custom audience event
	 *
	 * @return bool
	 */
	public static function duplicate_audience() {
		if (
			empty( $_POST['action'] )
			|| 'aepc_duplicate_custom_audience' != $_POST['action']
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'duplicate_custom_audience' )
		) {
			return false;
		}

		try {

			// Delete event
			AEPC_Admin_CA_Manager::duplicate( wp_unslash( $_POST ) );

			// Send success notice
			AEPC_Admin_Notices::add_notice( 'success', 'main', __( '<strong>Custom audience duplicated</strong> It is duplicated also on your facebook Ad account.', 'pixel-caffeine' ) );

			// Redirect to the same page
			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
				self::redirect_to( add_query_arg( null, null ) );
			}

			return true;
		}

		catch ( Exception $e ) {
			AEPC_Admin_Notices::add_notice( 'error', 'main', $e->getMessage() );

			return false;
		}
	}

	/**
	 * Delete custom audience event
	 */
	public static function delete_audience() {
		$screen = get_current_screen();

		if (
			empty( $screen->id )
			|| AEPC_Admin_Menu::$hook_page != $screen->id
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_GET['_wpnonce'] )
			|| ! wp_verify_nonce( $_GET['_wpnonce'], 'delete_custom_audience' )
			|| empty( $_GET['id'] )
		) {
			return;
		}

		try {
			// Delete event
			AEPC_Admin_CA_Manager::delete( intval( $_GET['id'] ) );

			// Send success notice
			AEPC_Admin_Notices::add_notice( 'success', 'main', __( '<strong>Custom audience removed</strong> It was removed also on your facebook Ad account.', 'pixel-caffeine' ) );
		}

		catch ( Exception $e ) {
			AEPC_Admin_Notices::add_notice( 'error', 'main', '<strong>' . __( 'Unable to delete', 'pixel-caffeine' ) . '</strong> ' . $e->getMessage() );
		}

		// Redirect to the same page
		self::redirect_to( remove_query_arg( array( 'id', '_wpnonce' ) ) );
	}

	/**
	 * Product Catalogs
	 */

	/**
	 * Adjust values for DB
	 *
	 * @param array $post_data
	 *
	 * @return array
	 */
	protected static function process_product_catalog_post_data( array $post_data ) {
		$service = AEPC_Admin::$product_catalogs_service;
		$defaults = $service->getDefaults();

		$post_data = wp_parse_args( wp_unslash( $post_data ), array(
			Configuration::OPTION_FEED_NAME => $defaults->get( Configuration::OPTION_FEED_NAME ),
			Configuration::OPTION_FEED_FORMAT => $defaults->get( Configuration::OPTION_FEED_FORMAT ),
			Configuration::OPTION_FEED_CONFIG => array()
		) );

		// Convert all array types
		foreach ( $post_data[ Configuration::OPTION_FEED_CONFIG ] as $key => &$value ) {
			if ( is_string( $value ) && strpos( $value, ',' ) !== false ) {
				$value = explode( ',', $value );
			}
		}

		// Check for checkboxes
		foreach ( array(
			Configuration::OPTION_FEED_CONFIG => array(
				Configuration::OPTION_ENABLE_BACKGROUND_SAVE,
				Configuration::OPTION_SKU_FOR_ID,
				Configuration::OPTION_FILTER_ON_SALE,
				Configuration::OPTION_NO_VARIATIONS,
				Configuration::OPTION_FB_ENABLE,
			)
		) as $group => $options ) {
			if ( ! is_array( $options ) ) {
				$post_data[ $options ] = isset( $post_data[ $options ] ) && 'yes' == $post_data[ $options ];
			}

			foreach ( $options as $option ) {
				$post_data[ $group ][ $option ] = isset( $post_data[ $group ][ $option ] ) && 'yes' == $post_data[ $group ][ $option ];
			}
		}

		// Remove eventual empty item in the google category option value
		$post_data[ Configuration::OPTION_FEED_CONFIG ][ Configuration::OPTION_GOOGLE_CATEGORY ] = array_filter( $post_data[ Configuration::OPTION_FEED_CONFIG ][ Configuration::OPTION_GOOGLE_CATEGORY ] );

		// Get schedule options in base of the action
		$schedule_action = $post_data[ Configuration::OPTION_FEED_CONFIG ][ Configuration::OPTION_FB_ACTION ];
		$post_data[ Configuration::OPTION_FEED_CONFIG ] = array_merge(
			$post_data[ Configuration::OPTION_FEED_CONFIG ],
			$post_data[ Configuration::OPTION_FEED_CONFIG ][ $schedule_action ]
		);

		return $post_data;
	}

	/**
	 * Add new product catalog
	 *
	 * @return Response
	 */
	public static function save_product_catalog() {
		if (
			empty( $_POST['action'] )
			|| $_POST['action'] != 'aepc_save_product_catalog'
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'save_product_catalog' )
		) {
			return new Response( false );
		}

		try {
			$service = AEPC_Admin::$product_catalogs_service;
			$post_data = self::process_product_catalog_post_data( $_POST['product_catalog'] );

			// Create entity object
 			$entity = new ProductCatalog();
			$entity->setId( $post_data[ Configuration::OPTION_FEED_NAME ] );
			$entity->setFormat( $post_data[ Configuration::OPTION_FEED_FORMAT ] );
			$entity->setConfig( $post_data[ Configuration::OPTION_FEED_CONFIG ] );

			// Save create catalog and start feed file saving
			$response = $service->create_product_catalog( $entity );

			if ( is_wp_error( $response ) ) {
				throw new Exception( sprintf( __( 'Unable to generate the feed: %s', 'pixel-caffeine' ), $response->get_error_message() ) );
			}

			if ( $service->get_product_catalog( $entity->getId() )->mustBeSavedInBackground() ) {
				AEPC_Admin_Notices::add_notice( 'success', 'main', __( '<strong>Product catalog saved!</strong> The system is saving your feed in background. Feel free to navigate away and we will keep you updated in the box below.', 'pixel-caffeine' ) );
			} else {
				AEPC_Admin_Notices::add_notice( 'success', 'main', __( '<strong>Product catalog saved!</strong>', 'pixel-caffeine' ) );
			}

			// If all good, redirect in the same page
			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
				self::redirect_to( remove_query_arg( 'paged' ) );
			}

			return new Response( true, array(
				'background_saving' => $service->get_product_catalog( $entity->getId() )->mustBeSavedInBackground()
			) );
		}

		catch ( Exception $e ) {
			AEPC_Admin_Notices::add_notice( 'error', 'main', $e->getMessage() );
			return new Response( false );
		}
	}

	/**
	 * Add new product catalog
	 *
	 * @return bool
	 */
	public static function save_product_feed_refresh_interval() {
		if (
			empty( $_POST['action'] )
			|| $_POST['action'] != 'aepc_save_product_feed_refresh_interval'
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'save_product_feed_refresh_interval' )
		) {
			return false;
		}

		try {
			$service = AEPC_Admin::$product_catalogs_service;

			// Create entity object
			$product_catalog = $service->get_product_catalog( $_POST['product_catalog_id'] );
			$product_catalog->configuration()->set( Configuration::OPTION_REFRESH_CYCLE, intval( $_POST['cycle'] ) );
			$product_catalog->configuration()->set( Configuration::OPTION_REFRESH_CYCLE_TYPE, $_POST['cycle_type'] );

			// Update DB and feed
			$product_catalog->update();
			$product_catalog->unschedule_job();

			AEPC_Admin_Notices::add_notice( 'success', 'main', __( 'Refresh option updated!', 'pixel-caffeine' ) );

			// If all good, redirect in the same page
			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
				self::redirect_to( remove_query_arg( 'paged' ) );
			}

			return true;
		}

		catch ( Exception $e ) {
			AEPC_Admin_Notices::add_notice( 'error', 'main', $e->getMessage() );
		}

		return false;
	}

	/**
	 * Update a product catalog
	 *
	 * @return Response
	 */
	public static function update_product_catalog() {
		if (
			empty( $_POST['action'] )
			|| $_POST['action'] != 'aepc_update_product_catalog'
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'update_product_catalog' )
		) {
			return new Response( false );
		}

		try {
			$service = AEPC_Admin::$product_catalogs_service;
			$post_data = self::process_product_catalog_post_data( $_POST['product_catalog'] );

			// Get product catalog
			$product_catalog = $service->get_product_catalog( $post_data['name'] );

			// Update entity
			$product_catalog->get_entity()->setFormat( $post_data[ Configuration::OPTION_FEED_FORMAT ] );
			$product_catalog->get_entity()->setConfig( $post_data[ Configuration::OPTION_FEED_CONFIG ] );

			// Save product catalog
			$service->update_product_catalog( $product_catalog );

			// Send success notice
			AEPC_Admin_Notices::add_notice( 'success', 'main', __( '<strong>Product catalog updated!</strong>', 'pixel-caffeine' ) );

			// If all good, redirect in the same page
			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
				self::redirect_to( remove_query_arg( 'paged' ) );
			}

			return new Response( true, array(
				'background_saving' => $product_catalog->mustBeSavedInBackground()
			) );
		}

		catch ( Exception $e ) {
			AEPC_Admin_Notices::add_notice( 'error', 'main', $e->getMessage() );
			return new Response( false );
		}
	}

	/**
	 * Refresh the product catalog feed
	 *
	 * @return Response
	 */
	public static function refresh_product_catalog_feed() {
		if (
			empty( $_POST['action'] )
			|| $_POST['action'] != 'aepc_refresh_product_catalog_feed'
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'refresh_product_catalog_feed' )
		) {
			return new Response( false );
		}

		try {
			$service = AEPC_Admin::$product_catalogs_service;
			$defaults = $service->getDefaults();

			$post_data = wp_parse_args( wp_unslash( $_POST ), array(
				Configuration::OPTION_FEED_NAME => $defaults->get( Configuration::OPTION_FEED_NAME ),
			) );

			$product_catalog = AEPC_Admin::$product_catalogs_service->get_product_catalog( $post_data['name'] );

			// Then generate again with background processing
			$service->generate_feed( $product_catalog, FeedSaver::REFRESH_CONTEXT );

			if ( $product_catalog->mustBeSavedInBackground() ) {
				AEPC_Admin_Notices::add_notice( 'success', 'main', __( '<strong>Product catalog saved!</strong> The system is saving your feed in background. Feel free to navigate away and we will keep you updated in the box below.', 'pixel-caffeine' ) );
			} else {
				AEPC_Admin_Notices::add_notice( 'success', 'main', __( '<strong>Product catalog saved!</strong>', 'pixel-caffeine' ) );
			}

			return new Response( true, array(
				'background_saving' => $product_catalog->mustBeSavedInBackground()
			) );
		}

		catch ( Throwable $e ) {
			AEPC_Admin_Notices::add_notice( 'error', 'main', $e->getMessage() );
		}

		return new Response( false );
	}

	/**
	 * Delete the product catalog feed
	 *
	 * @return Response
	 */
	public static function delete_product_catalog_feed() {
		if (
			empty( $_POST['action'] )
			|| $_POST['action'] != 'aepc_delete_product_catalog_feed'
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'delete_product_catalog_feed' )
		) {
			return new Response( false );
		}

		try {
			$defaults = AEPC_Admin::$product_catalogs_service->getDefaults();

			$post_data = wp_parse_args( wp_unslash( $_POST ), array(
				Configuration::OPTION_FEED_NAME => $defaults->get( Configuration::OPTION_FEED_NAME ),
			) );

			$service = AEPC_Admin::$product_catalogs_service;
			$product_catalog = $service->get_product_catalog( $post_data['name'] );
			$service->delete_product_catalog( $product_catalog );

			AEPC_Admin_Notices::add_notice( 'success', 'main', __( '<strong>Feed deleted correctly!</strong>', 'pixel-caffeine' ) );

			return new Response( true );
		}

		catch ( Throwable $e ) {
			AEPC_Admin_Notices::add_notice( 'error', 'main', $e->getMessage() );
		}

		return new Response( false );
	}

	/**
	 * Clear transients used for facebook api requests
	 */
	public static function clear_transients() {
		if (
			empty( $_GET['action'] )
			|| 'aepc_clear_transients' != $_GET['action']
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_GET['_wpnonce'] )
			|| ! wp_verify_nonce( $_GET['_wpnonce'], 'clear_transients' )
		) {
			return;
		}

		// Clear the transients
		AEPC_Admin::clear_transients();

		// Redirect to the same page
		self::redirect_to( remove_query_arg( array( 'action', '_wpnonce' ) ) );
	}

	/**
	 * Download the log file with the log report
	 */
	public static function download_log_report() {
		if (
			empty( $_GET['action'] )
			|| 'aepc_download_log_report' != $_GET['action']
			|| ! current_user_can( 'manage_ads' )
			|| empty( $_GET['_wpnonce'] )
			|| ! wp_verify_nonce( $_GET['_wpnonce'], 'aepc_download_log_report' )
		) {
			return;
		}

		$logRepository = new LogRepository();
		$log = $logRepository->findByID( intval( $_GET['log'] ) );

		ob_start();

		printf( "---- Report: %s ----\n\n", $log->getDate()->format('c') );
		printf( "Exception: %s\n\n", $log->getException() );
		printf( "--------------------\n\n" );
		printf( "Message: %s\n\n", $log->getMessage() );
		printf( "---- Context ----\n\n" );
		print_r( $log->getContext() );
		printf( "\n\n" );
		printf( "---- Serialized Context ----\n\n" );
		echo( serialize( $log->getContext() ) );

		$content = ob_get_clean();

		header('Content-Description: File Transfer');
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename=report.log');
		header('Content-Transfer-Encoding: binary');
		header('Connection: Keep-Alive');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . strlen( $content ) );
		echo $content;

		exit;
	}

	/**
	 * Used on requests, to redirect to a page after endi request
	 *
	 * @param $to
	 */
	protected static function redirect_to( $to ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX || isset( $_GET['ajax'] ) && 1 == $_GET['ajax'] ) {
			wp_send_json_success();
		}

		wp_redirect( $to );
		exit();
	}

}
