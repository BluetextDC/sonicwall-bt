<?php
namespace ACP\Helper\Select\Value;

use ACP\Helper\Select\Value;

final class PostStatus
	implements Value {

	/**
	 * @param object $status
	 *
	 * @return string
	 */
	public function get_value( $status ) {
		return $status->name;
	}
}