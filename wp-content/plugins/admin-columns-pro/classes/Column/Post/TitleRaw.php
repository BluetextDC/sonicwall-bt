<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class TitleRaw extends AC\Column\Post\TitleRaw
	implements Sorting\Sortable, Editing\Editable, Search\Searchable {

	public function sorting() {
		$model = new Sorting\Model( $this );
		$model->set_orderby( 'title' );

		return $model;
	}

	public function editing() {
		return new Editing\Model\Post\TitleRaw( $this );
	}

	public function search() {
		return new Search\Comparison\Post\Title( );
	}

}