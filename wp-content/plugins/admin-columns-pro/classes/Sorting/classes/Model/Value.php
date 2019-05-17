<?php

namespace ACP\Sorting\Model;

use ACP\Sorting\Model;

class Value extends Model {

	public function get_sorting_vars() {
		$ids = array();

		foreach ( $this->strategy->get_results() as $id ) {
			$ids[ $id ] = strip_tags( $this->column->get_value( $id ) );
		}

		return array( 'ids' => $this->sort( $ids ) );
	}

}