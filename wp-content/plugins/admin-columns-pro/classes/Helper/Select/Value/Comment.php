<?php

namespace ACP\Helper\Select\Value;

use ACP\Helper\Select;

final class Comment
	implements Select\Value {

	public function get_value( $comment ) {
		return $comment->comment_ID;
	}

}