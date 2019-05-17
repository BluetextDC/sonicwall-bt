<?php
namespace ACP\Editing;

use ACP\Helper\Select\Options\Paginated;

interface PaginatedOptions {

	/**
	 * @param string $search
	 * @param int    $page
	 * @param int    null
	 *
	 * @return Paginated
	 */
	public function get_paginated_options( $search, $page, $id = null );

}