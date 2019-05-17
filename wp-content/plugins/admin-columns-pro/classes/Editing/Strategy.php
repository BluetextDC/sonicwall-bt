<?php

namespace ACP\Editing;

use ACP;

interface Strategy {

	/**
	 * @since 4.0
	 *
	 * @param $object_id
	 *
	 * @return bool True when user can edit object.
	 */
	public function user_has_write_permission( $object_id );

}