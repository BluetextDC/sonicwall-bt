<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class for admin pages
 *
 * @class AEPC_Admin_Menu
 */
class AEPC_Admin_Menu {

	public static $page_id = 'aepc-settings';
	public static $hook_page = '';

	/**
	 * AEPC_Admin_Menu Constructor.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
	}

	/**
	 * Define here the page titles
	 *
	 * @return array
	 */
	public static function get_page_titles() {
		return array(
			'welcome'          => _x( 'Welcome to the new version of Pixel Caffeine', 'page title', 'pixel-caffeine' ),
			'dashboard'        => _x( 'Dashboard', 'page title', 'pixel-caffeine' ),
			'custom-audiences' => _x( 'Custom Audiences', 'page title', 'pixel-caffeine' ),
			'conversions'      => _x( 'Conversions/Events', 'page title', 'pixel-caffeine' ),
			'general-settings' => _x( 'General Settings', 'page title', 'pixel-caffeine' ),
			'product-catalog'  => _x( 'Product Catalog', 'page title', 'pixel-caffeine' ),
			'logs'             => _x( 'Logs', 'page title', 'pixel-caffeine' ),
		);
	}

	/**
	 * Add the menu page
	 */
	public static function add_menu() {

		// Titles
		$titles = self::get_page_titles();
		$page = isset( $_GET['tab'] ) ? $_GET['tab'] : 'dashboard';

		// Detect page title
		$page_title = AEPC_Admin::PLUGIN_NAME;
		if ( isset( $titles[ $page ] ) ) {
			$page_title .= ' - ' . $titles[ $page ];
		}

		self::$hook_page = add_menu_page(
			$page_title,
			AEPC_Admin::PLUGIN_NAME,
			'manage_ads',
			self::$page_id,
			array( __CLASS__, 'view' ),
			null
		);
	}

	/**
	 * Show template for the dashboard
	 */
	public static function view() {
		$page = isset( $_GET['tab'] ) ? $_GET['tab'] : 'dashboard';
		AEPC_Admin::get_page( $page )->output();
	}
}
