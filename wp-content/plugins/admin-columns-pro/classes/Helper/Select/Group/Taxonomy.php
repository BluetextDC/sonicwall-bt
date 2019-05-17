<?php

namespace ACP\Helper\Select\Group;

use ACP\Helper\Select\Group;
use ACP\Helper\Select\Option;

class Taxonomy extends Group {

	/**
	 * @param /WP_Term $term
	 * @param Option $option
	 *
	 * @return string
	 */
	public function get_label( $term, Option $option ) {
		$taxonomy = get_taxonomy( $term->taxonomy );

		return $taxonomy->label;
	}

}