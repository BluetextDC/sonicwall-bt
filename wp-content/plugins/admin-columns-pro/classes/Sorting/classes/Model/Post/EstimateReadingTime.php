<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\Model;

class EstimateReadingTime extends Model {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
	}

	public function get_sorting_vars() {
		$ids = array();

		foreach ( $this->strategy->get_results() as $id ) {
			$ids[ $id ] = ac_helper()->string->word_count( $this->column->get_raw_value( $id ) );
		}

		return array(
			'ids' => $this->sort( $ids ),
		);
	}

}