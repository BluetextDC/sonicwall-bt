<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;
use ACP\Editing\PaginatedOptions;
use ACP\Helper\Select;

class PostParent extends Model\Post implements PaginatedOptions {

	public function get_view_settings() {
		return array(
			'type'               => 'select2_dropdown',
			'ajax_populate'      => true,
			'multiple'           => false,
			'clear_button'       => true,
			'store_single_value' => true,
		);
	}

	public function get_paginated_options( $s, $paged, $id = null ) {
		$entities = new Select\Entities\Post( array(
			's'         => $s,
			'paged'     => $paged,
			'post_type' => $this->column->get_post_type(),
		) );

		return new Select\Options\Paginated(
			$entities,
			new Select\Formatter\PostTitle( $entities )
		);
	}

	public function get_edit_value( $id ) {
		$post = get_post( parent::get_edit_value( $id ) );

		if ( ! $post ) {
			return false;
		}

		return array(
			$post->ID => $post->post_title,
		);
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, array( 'post_parent' => $value ) );
	}

}