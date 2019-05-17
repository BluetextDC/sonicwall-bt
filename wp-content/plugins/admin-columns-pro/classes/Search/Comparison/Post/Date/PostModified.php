<?php

namespace ACP\Search\Comparison\Post\Date;

use ACP\Search\Comparison\Post;

class PostModified extends Post\Date {

	public function get_field() {
		return 'post_modified';
	}

}