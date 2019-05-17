<?php

namespace ACP\Export\Utility;

/**
 * Utility functions for encryption
 * @since 1.0
 */
class Encryption {

	/**
	 * Generate a random encryption key
	 * @since 1.0
	 * @return string Generated encryption key
	 */
	public static function generate_key() {
		return md5( microtime( true ) . wp_rand() );
	}

}
