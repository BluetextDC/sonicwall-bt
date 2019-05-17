<?php

namespace ACP\Column\Taxonomy;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Sorting;

class Menu extends AC\Column\Menu
	implements Sorting\Sortable, Editing\Editable, Export\Exportable {

	public function get_item_type() {
		return 'taxonomy';
	}

	public function get_object_type() {
		return $this->get_taxonomy();
	}

	public function sorting() {
		return new Sorting\Model( $this );
	}

	public function editing() {
		return new Editing\Model\Taxonomy\Menu( $this );
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

}