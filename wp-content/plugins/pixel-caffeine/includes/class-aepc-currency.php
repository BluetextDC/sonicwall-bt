<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @class AEPC_Currency
 */
class AEPC_Currency {

	private static $currencies = null;

	/**
	 * Get all currencies supported by facebook
	 *
	 * @return array
	 */
	public static function get_currencies() {
		if ( is_null( self::$currencies ) ) {
			self::$currencies = (array) json_decode( file_get_contents( dirname(__FILE__) . '/resources/currencies.json' ) );
			wp_cache_set( 'aepc_fb_currencies', self::$currencies );
		}

		return self::$currencies;
	}

	/**
	 * Return amount with eventual offset, in base of currency
	 *
	 * @param $amount
	 * @param $currency
	 *
	 * @return float
	 */
	public static function get_amount( $amount, $currency ) {
		// It doesn't need anymore
		/*if ( in_array( $currency, array_keys( self::get_currencies() ) ) ) {
			$amount *= self::get_offset( $currency );
		}*/

		return $amount;
	}

	/**
	 * Return the offset for a currency
	 *
	 * @param $currency
	 *
	 * @return integer
	 */
	public static function get_offset( $currency ) {
		$currencies = self::get_currencies();
		return isset( $currencies[ $currency ] ) ? $currencies[ $currency ]->offset : 1;
	}
}
