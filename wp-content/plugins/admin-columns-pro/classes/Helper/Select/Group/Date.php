<?php

namespace ACP\Helper\Select\Group;

use ACP\Helper\Select\Group;
use ACP\Helper\Select\Option;
use WP_Post;

class Date extends Group {

	/**
	 * @param WP_Post $post
	 * @param Option  $option
	 *
	 * @return string
	 */
	public function get_label( $post, Option $option ) {
		return date_i18n( 'F Y', strtotime( $post->post_date_gmt ) );
	}

	protected function sort( array $groups ) {
		return $groups;
	}

}