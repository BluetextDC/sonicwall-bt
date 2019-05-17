<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;

class Slug extends Model\Post {

	public function get_view_settings() {
		return array(
			'type'                   => 'text',
			'placeholder'            => $this->column->get_label(),
			self::VIEW_BULK_EDITABLE => false,
		);
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, array( 'post_name' => $value ) );
	}

}