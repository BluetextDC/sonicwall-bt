<?php

namespace ACP\Sorting\Model\CustomField;

use AC;
use ACP\Sorting\Model;

class Date extends Model\Meta {

	public function __construct( AC\Column\CustomField $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
	}

	public function get_sorting_vars() {
		$ids = $this->strategy->get_results( parent::get_sorting_vars() );

		$query = new AC\Meta\QueryColumn( $this->column );
		$query->select( 'id, meta_value' )
		      ->where_in( $ids );

		if ( acp_sorting()->show_all_results() ) {
			$query->left_join();
		}

		$values = array();

		foreach ( $query->get() as $result ) {
			$timestamp = ac_helper()->date->strtotime( maybe_unserialize( $result->meta_value ) );

			if ( $timestamp ) {
				$values[ $result->id ] = $timestamp;
			}
		}

		return array(
			'ids' => $this->sort( $values ),
		);
	}

}