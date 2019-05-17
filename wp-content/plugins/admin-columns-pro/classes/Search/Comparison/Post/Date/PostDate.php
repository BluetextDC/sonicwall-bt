<?php

namespace ACP\Search\Comparison\Post\Date;

use ACP\Search\Comparison\Post;

class PostDate extends Post\Date {

	public function get_field() {
		return 'post_date';
	}

}