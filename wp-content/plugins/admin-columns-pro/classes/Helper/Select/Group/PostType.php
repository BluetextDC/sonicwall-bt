<?php

namespace ACP\Helper\Select\Group;

use ACP\Helper\Select\Group;
use ACP\Helper\Select\Option;
use WP_Post;

class PostType extends Group {

	/**
	 * @param WP_Post $post
	 * @param Option  $option
	 *
	 * @return string
	 */
	public function get_label( $post, Option $option ) {
		return get_post_type_object( $post->post_type )->labels->singular_name;
	}

}