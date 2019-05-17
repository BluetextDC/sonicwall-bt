<?php

namespace ACP\Column\Media;

use AC;
use ACP\Sorting;

class Dimensions extends AC\Column\Media\Dimensions
	implements Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model\Media\Dimensions( $this );
	}

}