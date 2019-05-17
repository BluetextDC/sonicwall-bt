<?php

namespace ACP\Search\TableScreen;

use ACP\Search\TableScreen;

class Comment extends TableScreen {

	public function register() {
		add_action( 'restrict_manage_comments', array( $this, 'filters_markup' ) );

		parent::register();
	}

}