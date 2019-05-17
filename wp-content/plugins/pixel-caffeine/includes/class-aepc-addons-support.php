<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @class AEPC_Addons_Support
 */
class AEPC_Addons_Support {

	/**
	 * List of eCommerce plugin supported
	 *
	 * @var array
	 */
	protected static $supports = array( 'woocommerce', 'edd' );

	/**
	 * Folder with all classes for each plugin supported
	 *
	 * @var string
	 */
	protected static $addons_dir = 'includes/supports/';

	/**
	 * Save the instances, so you don't need to re-instantiate again
	 *
	 * @var array
	 */
	protected static $instances = array();

	/**
	 * Initialize the class
	 */
	public static function init() {
		foreach ( self::get_detected_addons() as $addon ) {
			$addon->setup();
		}
	}

	/**
	 * Get the instance of object that manage the ecommerce plugin support
	 *
	 * @param string $plugin_name The plugin slug to identify the object instance to get.
	 *
	 * @return AEPC_Edd_Addon_Support|AEPC_Woocommerce_Addon_Support
	 */
	public static function get_support_instance( $plugin_name ) {
//		include_once( PixelCaffeine()->plugin_path() . '/' . self::$addons_dir . 'class-aepc-' . $plugin_name . '-addon-support.php' );
		$classname = sprintf( 'AEPC_%s_Addon_Support', ucfirst( $plugin_name ) );
		return new $classname();
	}

	/**
	 * Get the instances of supported addons
	 *
	 * @return AEPC_Edd_Addon_Support[]|AEPC_Woocommerce_Addon_Support[]
	 */
	public static function get_supported_addons() {
		foreach ( apply_filters( 'aepc_supported_addons', self::$supports ) as $addon ) {
			if ( isset( self::$instances[ $addon ] ) ) {
				continue;
			}

			self::$instances[ $addon ] = self::get_support_instance( $addon );
		}

		return self::$instances;
	}

	/**
	 * Get an array with only names of the supported addons
	 *
	 * @return array|string
	 */
	public static function get_supported_addon_names() {
		$names = array();
		foreach ( self::get_supported_addons() as $addon ) {
			$names[] = $addon->get_name();
		}
		return $names;
	}

	/**
	 * Get the instances of supported addons detected activated
	 *
	 * @return AEPC_Edd_Addon_Support[]|AEPC_Woocommerce_Addon_Support[]
	 */
	public static function get_detected_addons() {
		$detected = array();

		foreach ( self::get_supported_addons() as $addon ) {
			if ( $addon->is_active() ) {
				$detected[] = $addon;
			}
		}

		return $detected;
	}

	/**
	 * Say if there are addon supported active
	 *
	 * @return bool
	 */
	public static function are_detected_addons() {
		return count( self::get_detected_addons() ) > 0;
	}

	/**
	 * Extend the init params with the extra info about the customer of shop
	 *
	 * @param array $params Params to extend.
	 *
	 * @return array
	 */
	public static function extend_customer_parameters( $params ) {
		foreach ( self::get_detected_addons() as $addon ) {
			$user_info = $addon->get_customer_info();
			if ( ! empty( $user_info ) ) {
				$params = array_merge( $user_info, array_filter( $params ) );
			}
		}

		return $params;
	}

	/**
	 * Check if the event passe din parameter is supported in one of addon active detected
	 *
	 * @param string $event One of the standard event
	 *
	 * @return bool
	 */
	public static function is_event_supported( $event ) {
		foreach ( self::get_detected_addons() as $addon ) {
			if ( $addon->supports_event( $event ) ) {
				return true;
			}
		}

		return false;
	}
}
