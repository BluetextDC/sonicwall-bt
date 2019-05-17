<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Sorting;

class Menu extends AC\Column\Post\Menu
	implements Sorting\Sortable, Editing\Editable, Export\Exportable {

	public function sorting() {
		return new Sorting\Model( $this );
	}

	public function editing() {
		return new Editing\Model\Post\Menu( $this );
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

}