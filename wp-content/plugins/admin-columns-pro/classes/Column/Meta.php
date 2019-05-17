<?php

namespace ACP\Column;

use AC;
use ACP\Editing;
use ACP\Filtering;
use ACP\Sorting;

/**
 * @since 4.0
 */
abstract class Meta extends AC\Column\Meta
	implements Sorting\Sortable, Editing\Editable, Filtering\Filterable {

	/**
	 * @return Sorting\Model\Meta|Sorting\Model\Disabled
	 */
	public function sorting() {
		if ( ! $this->get_meta_key() ) {
			return new Sorting\Model\Disabled( $this );
		}

		return new Sorting\Model\Meta( $this );
	}

	/**
	 * @return Editing\Model\Meta
	 */
	public function editing() {
		return new Editing\Model\Meta( $this );
	}

	/**
	 * @return Filtering\Model\Meta|Filtering\Model\Disabled
	 */
	public function filtering() {
		if ( ! $this->get_meta_key() ) {
			return new Filtering\Model\Disabled( $this );
		}

		return new Filtering\Model\Meta( $this );
	}

}