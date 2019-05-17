<?php

namespace ACP\Search\Comparison;

use ACP\Helper\Select\Options;

interface Values {

	/**
	 * @return Options
	 */
	public function get_values();

}