<?php

namespace ACP\Column\Media;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Search;

/**
 * @since 4.0
 */
class Title extends AC\Column\Media\Title
	implements Editing\Editable, Export\Exportable, Search\Searchable {

	public function editing() {
		return new Editing\Model\Media\Title( $this );
	}

	public function export() {
		return new Export\Model\Media\Title( $this );
	}

	public function search() {
		// TODO use case for meta & sql search (title and filename)
		return new Search\Comparison\Post\Title();
	}

}