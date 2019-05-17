<?php

namespace ACP\Editing\Model\Comment;

use ACP\Editing\Model;

class AuthorName extends Model\Comment {

	public function save( $id, $value ) {
		return $this->update_comment( $id, array( 'comment_author' => $value ) );
	}

}