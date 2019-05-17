<?php

namespace ACP\Column\Media;

use AC;
use ACP\Sorting;

class FileName extends AC\Column\Media\FileName
	implements Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model\Media\FileName( $this );
	}

}