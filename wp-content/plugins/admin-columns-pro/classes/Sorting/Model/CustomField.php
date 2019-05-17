<?php

namespace ACP\Sorting\Model;

use AC;
use ACP\Sorting\Model;

/**
 * @property AC\Column\CustomField $column
 */
class CustomField extends Model\Meta {

	public function __construct( AC\Column\CustomField $column ) {
		parent::__construct( $column );
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
			$values[ $result->id ] = maybe_unserialize( $result->meta_value );
		}

		return array(
			'ids' => $this->sort( $values ),
		);
	}

}