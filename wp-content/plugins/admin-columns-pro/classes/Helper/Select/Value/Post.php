<?php

namespace ACP\Helper\Select\Value;

use ACP\Helper\Select;
use WP_Post;

final class Post
	implements Select\Value {

	/**
	 * @param WP_Post $post
	 *
	 * @return int
	 */
	public function get_value( $post ) {
		return $post->ID;
	}

}