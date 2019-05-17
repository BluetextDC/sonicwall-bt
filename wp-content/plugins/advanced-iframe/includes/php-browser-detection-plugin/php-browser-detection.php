<?php
/*
Plugin Name: PHP Browser Detection
Plugin URI: http://wordpress.org/extend/plugins/php-browser-detection/
Description: Use PHP to detect browsers for conditional CSS or to detect mobile phones.
Version: 3.2.0
Author: Mindshare Labs, Inc.
Author URI: https://mind.sh/are
License: GNU General Public License v3
License URI: LICENSE
Text Domain: php-browser-detection

*/

/**
 * Copyright 2009-2017 Mindshare Labs, Inc.
 * Based on code originally by Marty Thornley
 * Since version 3 making use of the BROWSCAP-PHP library by Garet Jax / asgrim
 *
 * Please note: This is a slighly modified and enhanced version that it can be included into other plugins directly 
 *
 * @link https://github.com/browscap/browscap-php
 *       This program is free software; you can redistribute it and/or modify
 *       it under the terms of the GNU General Public License, version 3, as
 *       published by the Free Software Foundation.
 *       This program is distributed in the hope that it will be useful,
 *       but WITHOUT ANY WARRANTY; without even the implied warranty of
 *       MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *       GNU General Public License for more details.
 *       You should have received a copy of the GNU General Public License
 *       along with this program; if not, write to the Free Software
 *       Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// TODO refactor into class using object oriented code, filters aren't currently working

if (!defined('AI_PBD_DIR_PATH')) {
	define('AI_PBD_DIR_PATH', dirname(__FILE__)); 
}



/**
 * PHP Browser Detection initialization
 *
 * @private
 */
require_once('lib/Browscap.php');

if (!function_exists('ai_php_browser_info')) :
	/**
	 * Returns array of all browser info.
	 *
	 * @usage global $browser_info;
	 * @return array
	 */
	function ai_php_browser_info() {
    $browscap = new \aibrowscap\Browscap( AI_PBD_DIR_PATH . '/cache');
		$browscap->doAutoUpdate =  TRUE;
		$browscap->updateInterval = 2592000;  // 30 days, default is 5
		$browscap->remoteIniUrl = "http://browscap.org/stream?q=Lite_PHP_BrowsCapINI";

		return $browscap->getBrowser(NULL, TRUE);
	}
endif;

if (!function_exists('ai_get_browser_name')) :
	/**
	 * Returns the name of the browser.
	 *
	 * @return string
	 */
	function ai_get_browser_name() {
		global $browser_info;

		return $browser_info[ 'Browser' ];
	}
endif;

if (!function_exists('ai_get_browser_version')) :
	/**
	 * Returns the browser version number.
	 *
	 * @return mixed
	 */
	function ai_get_browser_version() {
		global $browser_info;

		return $browser_info[ 'Version' ];
	}
endif;

if (!function_exists('is_browser')) :
	/**
	 * Conditional to test for any browser.
	 *
	 * @param string $name
	 * @param string $version
	 *
	 * @return bool
	 */
	function is_browser($name = '', $version = '') {
		global $browser_info;

		$name = ucwords(trim($name));

    if (isset($browser_info[ 'Browser' ]) && (strpos($browser_info[ 'Browser' ], $name) !== FALSE)) {
			if ($version == '') {
				return TRUE;
				// check MajorVer from full browscap first
			} elseif (isset($browser_info[ 'MajorVer' ]) && $browser_info[ 'MajorVer' ] == $version) {
				return TRUE;
				// fallback to Version in lite version
			} elseif (isset($browser_info[ 'Version' ]) && $browser_info[ 'Version' ] == $version) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
endif;

if (!function_exists('ai_is_os')) :
	/**
	 * Conditional to test for OS platform.
	 *
	 * @param string $platform
	 *
	 * @return bool
	 */
	function ai_is_os($platform = '') {
		global $browser_info;

		$platform = ucwords(trim($platform));

		if (isset($browser_info[ 'Platform' ]) && (strpos($browser_info[ 'Platform' ], $platform) !== FALSE)) {
			if ($platform == '') {
				return TRUE;
			} elseif (isset($browser_info[ 'Platform' ]) && $browser_info[ 'Platform' ] == $platform) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
endif;

if (!function_exists('ai_is_firefox')) :
	/**
	 * Conditional to test for Firefox.
	 *
	 * @param string $version
	 *
	 * @return bool
	 */
	function ai_is_firefox($version = '') {
    return is_browser('Firefox', $version);
	}
endif;

if (!function_exists('ai_is_safari')) :
	/**
	 * Conditional to test for Safari.
	 *
	 * @param string $version
	 *
	 * @return bool
	 */
	function ai_is_safari($version = '') {
		return is_browser('Safari', $version);
	}
endif;

if (!function_exists('ai_is_chrome')) :
	/**
	 * Conditional to test for Chrome.
	 *
	 * @param string $version
	 *
	 * @return bool
	 */
	function ai_is_chrome($version = '') {
		return is_browser('Chrome', $version);
	}
endif;

if (!function_exists('ai_is_opera')) :
	/**
	 * Conditional to test for Opera.
	 *
	 * @param string $version
	 *
	 * @return bool
	 */
	function ai_is_opera($version = '') {
		return is_browser('Opera', $version);
	}
endif;

if (!function_exists('ai_is_ie')) :
	/**
	 * Conditional to test for IE.
	 *
	 * @param string $version
	 *
	 * @return bool
	 */
	function ai_is_ie($version = '') {
		return is_browser('IE', $version);
	}
endif;

if (!function_exists('ai_is_desktop')) :
	/**
	 * Conditional to test for desktop devices.
	 *
	 * @return bool
	 */
	function ai_is_desktop() {
		global $browser_info;

		// pre 3.1 version
		if (isset($browser_info[ 'Device_Type' ]) && strpos($browser_info[ 'Device_Type' ], "Desktop") !== FALSE) {
			return TRUE;
		}

		return FALSE;
	}
endif;

if (!function_exists('ai_is_tablet')) :
	/**
	 * Conditional to test for tablet devices.
	 *
	 * @return bool
	 */
	function ai_is_tablet() {
		global $browser_info;
		if (isset($browser_info[ 'isTablet' ])) {
			if ($browser_info[ 'isTablet' ] == 1 || $browser_info[ 'isTablet' ] == "true" || isset($browser_info[ 'Device_Type' ]) && strpos($browser_info[ 'Device_Type' ], "Tablet") !== FALSE) {
				return TRUE;
			}
		}

		return FALSE;
	}
endif;

if (!function_exists('ai_is_mobile')) :
	/**
	 * Conditional to test for mobile devices.
	 *
	 * @return bool
	 */
	function ai_is_mobile() {
		global $browser_info;
		if (isset($browser_info[ 'isMobileDevice' ])) {
			if ($browser_info[ 'isMobileDevice' ] == 1 || $browser_info[ 'isMobileDevice' ] == "true" || isset($browser_info[ 'Device_Type' ]) && strpos($browser_info[ 'Device_Type' ], "Mobile") !== FALSE) {
				return TRUE;
			}
		}

		return FALSE;
	}
endif;

if (!function_exists('ai_is_iphone')) :
	/**
	 * Conditional to test for iPhone.
	 *
	 * @param string $version
	 *
	 * @return bool
	 */
	function ai_is_iphone($version = '') {
		global $browser_info;
		if ((isset($browser_info[ 'Browser' ]) && $browser_info[ 'Browser' ] == 'iPhone') || strpos($_SERVER[ 'HTTP_USER_AGENT' ], 'iPhone')) {
			if ($version == '') {
				return TRUE;
			} elseif ($browser_info[ 'MajorVer' ] == $version) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
endif;

if (!function_exists('ai_is_ipad')) :
	/**
	 * Conditional to test for iPad.
	 *
	 * @param string $version
	 *
	 * @return bool
	 */
	function ai_is_ipad($version = '') {
		global $browser_info;
		if (preg_match("/iPad/", $browser_info[ 'browser_name_pattern' ], $matches) || strpos($_SERVER[ 'HTTP_USER_AGENT' ], 'iPad')) {
			if ($version == '') {
				return TRUE;
			} elseif ($browser_info[ 'MajorVer' ] == $version) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
endif;

if (!function_exists('ai_is_ipod')) :
	/**
	 * Conditional to test for iPod.
	 *
	 * @param string $version
	 *
	 * @return bool
	 */
	function ai_is_ipod($version = '') {
		global $browser_info;
		if (preg_match("/iPod/", $browser_info[ 'browser_name_pattern' ], $matches)) {
			if ($version == '') {
				return TRUE;
			} elseif ($browser_info[ 'MajorVer' ] == $version) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
endif;

if (!function_exists('pbd_is_true')) :

	/**
	 * Evaluates natural language strings to boolean equivalent
	 * All values defined as TRUE will return TRUE, anything else is FALSE.
	 * Boolean values will be passed through.
	 *
	 * @since  3.1.1
	 *
	 * @param string $string        The natural language value
	 * @param array  $true_synonyms A list strings that are TRUE
	 *
	 * @return boolean The boolean value of the provided text
	 **/
	function pbd_is_true($string, $true_synonyms = array('yes', 'y', 'true', '1', 'on', 'open', 'affirmative', '+', 'positive')) {
		if (is_array($string)) {
			return FALSE;
		}
		if (is_bool($string)) {
			return $string;
		}

		return in_array(strtolower(trim($string)), $true_synonyms);
	}
endif;

/**
 *  Additional aip stuff
 */
 
/**
 * Conditional to test for android devices.
 *
 * @return bool
 */
function ai_is_ios() {
	global $browser_info;
	if(isset($browser_info['Platform'])) {
		if($browser_info['Platform'] == 'iOS') {
			return TRUE;
		}
	}
	return FALSE;
}

/**
 * Conditional to test for android tablet devices.
 *
 * @return bool
 */
function ai_is_androidtablet() {
  return ai_is_android() && ai_is_tablet();
} 
 
 /**
 * Conditional to test for desktop devices.
 *
 * @return bool
 */
function ai_is_desktop() {
	global $browser_info;
    
  if(isset($browser_info['Device_Type']) && strpos($browser_info['Device_Type'], "Desktop") !== FALSE) {
		return TRUE;
	}
	return FALSE;
}

/**
 *  Test if a browser = now crawler/bot or something like that.
 */ 
function ai_is_browser() {
  return ai_is_desktop() || ai_is_tablet() ||  ai_is_mobile();
}

/**
 * Main PHP Browser Detection instance
 */
if (empty($GLOBALS['browser_info'])) {
  $GLOBALS['browser_info'] = ai_php_browser_info();
}