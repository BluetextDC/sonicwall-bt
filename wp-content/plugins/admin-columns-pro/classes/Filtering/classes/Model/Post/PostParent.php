<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class PostParent extends Model {

	public function get_filtering_vars( $vars ) {
		$vars['post_parent'] = $this->get_filter_value();

		return $vars;
	}

	public function get_filtering_data() {
		$parents = $this->strategy->get_values_by_db_field( 'post_parent' );

		return array(
			'options' => acp_filtering()->helper()->get_post_titles( $parents ),
		);
	}

}