<?php

namespace ACP\Column\User;

use AC;
use ACP\Export;
use ACP\Sorting;

/**
 * @since 4.0.7
 */
class Name extends AC\Column\User\Name
	implements Sorting\Sortable, Export\Exportable {

	public function sorting() {
		return new Sorting\Model\User\Name( $this );
	}

	public function export() {
		return new Export\Model\User\Name( $this );
	}

}