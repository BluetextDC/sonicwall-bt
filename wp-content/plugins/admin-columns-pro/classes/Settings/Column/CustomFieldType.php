<?php

namespace ACP\Settings\Column;

use AC;
use AC\Collection;

class CustomFieldType extends AC\Settings\Column\CustomFieldType {

	public function get_dependent_settings() {
		$settings = parent::get_dependent_settings();

		switch ( $this->get_field_type() ) {

			case 'title_by_id' :
				$settings[] = new AC\Settings\Column\Post( $this->column );

				break;
			case 'user_by_id' :
				$settings[] = new AC\Settings\Column\User( $this->column );

				break;
			case 'image' :
			case 'library_id' :
				$settings[] = new AC\Settings\Column\NumberOfItems( $this->column );

				break;
		}

		return $settings;
	}

	public function format( $value, $original_value ) {

		switch ( $this->get_field_type() ) {

			case 'title_by_id' :
			case 'user_by_id' :
				$string = ac_helper()->array->implode_recursive( ',', $value );
				$ids = ac_helper()->string->string_to_array_integers( $string );

				$value = new Collection( $ids );

				break;
			case 'image':
			case 'library_id' :
				$value = parent::format( $value, $original_value );
				$value->limit( $this->column->get_setting( 'number_of_items' )->get_value() );

				break;

			default :
				$value = parent::format( $value, $original_value );
		}

		return $value;
	}

	/**
	 * Get possible field types
	 * @return array
	 */
	protected function get_field_type_options() {
		$field_types = parent::get_field_type_options();

		$field_types['relational']['title_by_id'] = __( 'Post', 'codepress-admin-columns' );
		$field_types['relational']['user_by_id'] = __( 'User', 'codepress-admin-columns' );

		return $field_types;
	}

}