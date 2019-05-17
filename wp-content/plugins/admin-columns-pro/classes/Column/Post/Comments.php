<?php

namespace ACP\Column\Post;

use AC;
use ACP\Export;

/**
 * @since 4.0
 */
class Comments extends AC\Column\Post\Comments
	implements Export\Exportable {

	public function export() {
		return new Export\Model\Post\Comments( $this );
	}

}