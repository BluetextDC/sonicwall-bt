<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Model;

/**
 * @since 4.1
 */
class ChildPages extends Model {

	public function get_value( $id ) {
		$titles = array();

		foreach ( $this->get_column()->get_raw_value( $id ) as $id ) {
			$titles[] = get_post_field( 'post_title', $id );
		}

		return implode( ',', $titles );
	}

}