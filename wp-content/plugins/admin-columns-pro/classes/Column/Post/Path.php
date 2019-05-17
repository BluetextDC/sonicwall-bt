<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Sorting;

class Path extends AC\Column\Post\Path
	implements Sorting\Sortable, Editing\Editable {

	public function sorting() {
		return new Sorting\Model( $this );
	}

	public function editing() {
		return new Editing\Model\Post\Slug( $this );
	}

}