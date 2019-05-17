<?php

namespace ACP\Column\User;

use AC;
use ACP\Sorting;

/**
 * @since 4.0
 */
class PostCount extends AC\Column\User\PostCount
	implements Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model\User\PostCount( $this );
	}

}