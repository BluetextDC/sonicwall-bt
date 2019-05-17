<?php

namespace ACP\Sorting;

use AC;
use ACP\Sorting;

class ModelFactory {

	/**
	 * @param AC\Column $column
	 *
	 * @return Sorting\Model|false
	 */
	public static function create( AC\Column $column ) {
		if ( ! $column instanceof Sorting\Sortable ) {
			return false;
		}

		$list_screen = $column->get_list_screen();

		if ( ! $list_screen instanceof Sorting\ListScreen ) {
			return false;
		}

		$model = $column->sorting();

		$model->set_strategy( $list_screen->sorting( $model ) );

		return $model;
	}

}