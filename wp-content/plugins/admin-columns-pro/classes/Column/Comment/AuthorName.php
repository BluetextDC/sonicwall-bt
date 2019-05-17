<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Editing;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class AuthorName extends AC\Column\Comment\AuthorName
	implements Editing\Editable, Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		$model = new Sorting\Model( $this );
		$model->set_orderby( 'comment_author' );

		return $model;
	}

	public function editing() {
		return new Editing\Model\Comment\AuthorName( $this );
	}

	public function filtering() {
		return new Filtering\Model\Comment\AuthorName( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\Author();
	}

}