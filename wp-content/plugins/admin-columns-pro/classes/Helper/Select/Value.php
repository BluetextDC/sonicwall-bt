<?php

namespace ACP\Helper\Select;

interface Value {

	/**
	 * @param $entity
	 *
	 * @return string
	 */
	public function get_value( $entity );

}