<?php

namespace ACP\Helper\Select\Value;

use ACP\Helper\Select;
use WP_User;

final class User
	implements Select\Value {

	/**
	 * @param WP_User $user
	 *
	 * @return int
	 */
	public function get_value( $user ) {
		return $user->ID;
	}
}