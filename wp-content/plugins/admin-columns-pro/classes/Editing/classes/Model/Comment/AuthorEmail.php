<?php

namespace ACP\Editing\Model\Comment;

use ACP\Editing\Model;

class AuthorEmail extends Model\Comment {

	public function get_view_settings() {
		return array( 'type' => 'email' );
	}

	public function save( $id, $value ) {
		return $this->update_comment( $id, array( 'comment_author_email' => $value ) );
	}

}