<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\Model;

class LastModifiedAuthor extends Model {

	public function get_sorting_vars() {
		$ids = array();

		$setting = $this->column->get_setting( 'user' );

		foreach ( $this->strategy->get_results() as $id ) {
			$ids[ $id ] = $setting->get_user_name( $this->column->get_raw_value( $id ) );
		}

		return array(
			'ids' => $this->sort( $ids ),
		);
	}

}