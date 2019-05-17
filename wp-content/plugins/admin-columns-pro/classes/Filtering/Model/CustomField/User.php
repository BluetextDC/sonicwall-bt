<?php

namespace ACP\Filtering\Model\CustomField;

use ACP\Filtering\Model;

class User extends Model\CustomField {

	public function get_filtering_data() {
		$options = array();

		foreach ( $this->get_meta_values() as $value ) {
			$options[ $value ] = $this->column->get_setting( 'user' )->format( $value, $value );
		}

		return array(
			'options'      => $options,
			'empty_option' => true,
		);
	}

}