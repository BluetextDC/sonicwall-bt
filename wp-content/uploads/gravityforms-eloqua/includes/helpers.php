<?php
/**
 * GFEloqua Helper functions
 *
 * @package gfeloqua
 */

/**
 * Get an instance of GFEloqua Object
 *
 * @return object  GFEloqua Object
 */
function gfeloqua() {
	if ( ! class_exists( 'GFEloqua' ) ) {
		return false;
	}

	return GFEloqua::get_instance();
}

/**
 * Deprecated function, use gfeloqua()
 *
 * @return object  GFEloqua Object
 */
function gf_eloqua() {
	__doing_it_wrong( 'gf_eloqua', 'As of version 1.5.2, gfeloqua() should be used to replace gf_eloqua().', '1.5.2' );
	return gfeloqua();
}

/**
 * Get an instance of GFEloqua_Extensions Object
 *
 * @return object  GFEloqua_Extensions Object
 */
function gfeloqua_extensions() {
	if ( ! class_exists( 'GFEloqua_Extensions' ) ) {
		return false;
	}

	return GFEloqua_Extensions::get_instance();
}

/**
 * Get an instance of BD_License_Manager Object
 *
 * @return object  BD_License_Manager Object
 */
function bd_license_manager() {
	if ( ! class_exists( 'BD_License_Manager' ) ) {
		return false;
	}

	return BD_License_Manager::get_instance();
}

if ( ! function_exists( 'ctype_alnum' ) ) {
	/**
	 * Check for alphanumeric character(s)
	 * See: http://php.net/manual/en/function.ctype-alnum.php
	 * May already be supported depending on PHP version
	 *
	 * @param string $text  Text for testing.
	 *
	 * @return bool  If string is alphanumeric.
	 */
	function ctype_alnum( $text ) {
		return ( preg_match( '~^[0-9a-z]*$~iu', $text ) > 0 );
	}
}

add_action( 'http_api_curl', 'gfeloqua_adjust_curl_sslversion' );

/**
 * Adjust CURL SSLVERSION to use TLS 1.2
 *
 * @param object $curl  CURL object.
 */
function gfeloqua_adjust_curl_sslversion( $curl ) {
	if ( ! $curl ) {
		return;
	}

	if ( OPENSSL_VERSION_NUMBER >= 0x1000100f ) {
		// Constant not defined in PHP < 5.5.
		if ( ! defined( 'CURL_SSLVERSION_TLSv1_2' ) ) {
			// Note the value 6 comes from its position in the enum that
			// defines it in cURL's source code.
			define( 'CURL_SSLVERSION_TLSv1_2', 6 ); // @codingStandardsIgnoreLine: ok.
		}
		curl_setopt( $curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2 );
	} else {
		// Constant not defined in PHP < 5.5.
		if ( ! defined( 'CURL_SSLVERSION_TLSv1' ) ) {
			define( 'CURL_SSLVERSION_TLSv1', 1 ); // @codingStandardsIgnoreLine: ok.
		}
		curl_setopt( $curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1 );
	}
}

