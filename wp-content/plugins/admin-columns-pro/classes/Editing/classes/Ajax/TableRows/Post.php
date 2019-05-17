<?php

namespace ACP\Editing\Ajax\TableRows;

use ACP\Editing\Ajax\TableRows;

final class Post extends TableRows {

	public function register() {
		add_action( 'edit_posts_per_page', array( $this, 'handle_request' ) );
	}

}