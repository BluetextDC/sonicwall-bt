<?php

namespace ACP\Search\Comparison;

use ACP\Helper\Select\Options;

interface RemoteValues {

	/**
	 * @return Options
	 */
	public function get_values();

}