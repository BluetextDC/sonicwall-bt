<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;
use ACP\Sorting;

class Order extends AC\Column\Post\Order
	implements Sorting\Sortable, Editing\Editable {

	public function sorting() {
		$model = new Sorting\Model( $this );
		$model->set_orderby( 'menu_order' );

		return $model;
	}

	public function editing() {
		return new Editing\Model\Post\Order( $this );
	}

}