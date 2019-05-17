<?php

namespace ACP\Column\User;

use AC;
use ACP\Sorting;

/**
 * @since 2.0
 */
class DisplayName extends AC\Column\User\DisplayName
	implements Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model( $this );
	}

}