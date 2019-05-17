<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class AuthorIP extends AC\Column\Comment\AuthorIP
	implements Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		$model = new Sorting\Model( $this );
		$model->set_orderby( 'comment_author_IP' );

		return $model;
	}

	public function filtering() {
		return new Filtering\Model\Comment\AuthorIP( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\IP();
	}

}
