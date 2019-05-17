<?php

namespace ACP\Sorting;

interface Sortable {

	/**
	 * Return the sortable model for this column
	 * @return Model
	 */
	public function sorting();

}