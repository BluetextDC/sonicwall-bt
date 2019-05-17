<?php

namespace ACP\Filtering\Model\CustomField;

use ACP\Filtering\Model;

class Link extends Model\CustomField {

	public function get_filtering_data() {
		$options = array();

		foreach ( $this->get_meta_values() as $value ) {
			$options[ $value ] = $value;
		}

		return array(
			'options'      => $options,
			'empty_option' => true,
		);
	}

}