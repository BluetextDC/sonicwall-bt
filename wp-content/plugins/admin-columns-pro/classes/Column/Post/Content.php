<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Export;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 2.4
 */
class Content extends AC\Column\Post\Content
	implements Editing\Editable, Sorting\Sortable, Filtering\Filterable, Export\Exportable, Search\Searchable {

	public function editing() {
		return new Editing\Model\Post\Content( $this );
	}

	public function filtering() {
		return new Filtering\Model\Post\Content( $this );
	}

	public function sorting() {
		$model = new Sorting\Model\Post\Field( $this );
		$model->set_field( 'post_content' );

		return $model;
	}

	public function export() {
		return new Export\Model\RawValue( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Content();
	}

}