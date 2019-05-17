<?php

namespace ACP\Sorting\Model\Post;

use AC;
use ACP\Sorting\Model;

class CommentCount extends Model {

	public function get_sorting_vars() {
		$ids = array();

		/* @var AC\Settings\Column\CommentCount $setting */
		$setting = $this->column->get_setting( 'comment_count' );

		foreach ( $this->strategy->get_results() as $id ) {
			$ids[ $id ] = $setting->get_comment_count( $id );
		}

		return array(
			'ids' => $this->sort( $ids ),
		);
	}

}