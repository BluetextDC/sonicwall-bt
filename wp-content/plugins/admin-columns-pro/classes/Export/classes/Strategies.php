<?php

namespace ACP\Export;

/**
 * Contains all available ACP_Export_Strategy instances
 */
class Strategies {

	/**
	 * Registered list screens supporting export functionality
	 * @since 1.0
	 * @var Strategy[]
	 */
	protected static $strategies;

	/**
	 * @since 1.0
	 *
	 * @param Strategy $strategy
	 */
	public static function register_strategy( Strategy $strategy ) {
		self::$strategies[ $strategy->get_list_screen()->get_key() ] = $strategy;
	}

	/**
	 * @since 1.0
	 *
	 * @param $key
	 *
	 * @return Strategy|null
	 */
	public static function get_strategy( $key ) {
		if ( isset( self::$strategies[ $key ] ) ) {
			return self::$strategies[ $key ];
		}

		return null;
	}

}