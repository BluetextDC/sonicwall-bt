<?php

namespace ACP\Sorting;

interface ListScreen {

	/**
	 * @param Model $model
	 *
	 * @return Strategy
	 */
	public function sorting( $model );

}