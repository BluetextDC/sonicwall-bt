<?php

namespace ACP\Column\Post;

use AC;
use ACP\Sorting;

class Permalink extends AC\Column\Post\Permalink
	implements Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model( $this );
	}

}