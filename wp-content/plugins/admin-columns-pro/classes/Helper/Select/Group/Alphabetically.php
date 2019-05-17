<?php

namespace ACP\Helper\Select\Group;

use ACP\Helper\Select\Group;
use ACP\Helper\Select\Option;

class Alphabetically extends Group {

	/**
	 * @param        $entity
	 * @param Option $option
	 *
	 * @return string
	 */
	public function get_label( $entity, Option $option ) {
		return strtoupper( substr( $option->get_label(), 0, 1 ) );
	}

}