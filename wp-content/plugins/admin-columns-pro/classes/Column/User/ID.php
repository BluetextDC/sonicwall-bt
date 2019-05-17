<?php

namespace ACP\Column\User;

use AC;
use ACP\Search;
use ACP\Sorting;

/**
 * @since 4.0
 */
class ID extends AC\Column\User\ID
	implements Sorting\Sortable, Search\Searchable {

	public function sorting() {
		$model = new Sorting\Model( $this );
		$model->set_orderby( 'ID' );

		return $model;
	}

	public function search() {
		return new Search\Comparison\User\ID();
	}

}