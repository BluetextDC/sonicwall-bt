<?php
namespace ACP\Search\Comparison;

use ACP\Helper\Select\Options;

interface FilterOptions {

	/**
	 * @return Options
	 */
	public function get_options();

}