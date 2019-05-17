<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class Roles extends Model {

	public function get_filtering_vars( $vars ) {
		$vars['author'] = implode( ',', get_users( array( 'role' => $this->get_filter_value(), 'fields' => 'id' ) ) );

		return $vars;
	}

	public function get_filtering_data() {
		return array(
			'options' => ac_helper()->user->get_roles(),
		);
	}

}