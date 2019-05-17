<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Filtering;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 2.0
 */
class DateGmt extends AC\Column\Comment\DateGmt
	implements Filtering\Filterable, Sorting\Sortable, Search\Searchable {

	public function sorting() {
		$model = new Sorting\Model( $this );
		$model->set_orderby( 'comment_date_gmt' );

		return $model;
	}

	public function filtering() {
		return new Filtering\Model\Comment\DateGmt( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\Date\Gmt();
	}

}