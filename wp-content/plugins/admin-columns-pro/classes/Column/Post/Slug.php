<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Search;
use ACP\Sorting;

class Slug extends AC\Column\Post\Slug
	implements Sorting\Sortable, Editing\Editable, Search\Searchable {

	public function sorting() {
		$model = new Sorting\Model\Post\Field( $this );
		$model->set_field( 'post_name' );

		return $model;
	}

	public function editing() {
		return new Editing\Model\Post\Slug( $this );
	}

	public function search() {
		return new Search\Comparison\Post\PostName( );
	}

}