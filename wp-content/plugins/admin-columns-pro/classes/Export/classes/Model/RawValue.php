<?php

namespace ACP\Export\Model;

use ACP\Export\Model;

/**
 * Exportability model for outputting the column's raw value
 * @since 4.1
 */
class RawValue extends Model {

	public function get_value( $id ) {
		$raw_value = $this->get_column()->get_raw_value( $id );

		if ( ! is_scalar( $raw_value ) ) {
			return false;
		}

		return $raw_value;
	}

}