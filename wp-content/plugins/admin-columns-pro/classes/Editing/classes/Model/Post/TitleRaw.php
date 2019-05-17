<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\Model;

class TitleRaw extends Model\Post {

	public function get_edit_value( $id ) {
		return get_post_field( 'post_title', $id );
	}

	public function save( $id, $value ) {
		return $this->update_post( $id, array( 'post_title' => $value ) );
	}

}