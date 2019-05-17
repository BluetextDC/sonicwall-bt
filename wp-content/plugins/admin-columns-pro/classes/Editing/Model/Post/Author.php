<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;
use ACP\Editing\PaginatedOptions;
use ACP\Helper\Select;

class Author extends Model\Post implements PaginatedOptions {

	public function get_edit_value( $id ) {
		$user = get_userdata( ac_helper()->post->get_raw_field( 'post_author', $id ) );

		if ( ! $user ) {
			return false;
		}

		return array(
			$user->ID => $user->display_name,
		);
	}

	public function get_view_settings() {
		return array(
			'type'               => 'select2_dropdown',
			'ajax_populate'      => true,
			'store_single_value' => true,
		);
	}

	public function get_paginated_options( $search, $paged, $id = null ) {
		$entities = new Select\Entities\User( compact( 'search', 'paged' ) );

		return new Select\Options\Paginated(
			$entities,
			new Select\Group\UserRole(
				new Select\Formatter\UserName( $entities )
			)
		);
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, array( 'post_author' => $value ) );
	}

}