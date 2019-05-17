<?php
/**
 * For older version of Gravity Forms Support.
 *
 * @package gfeloqua
 */

/**
 * Override the required get_columns method for older versions of Gravity Forms.
 */
class GFAddOnFeedsTableLegacy extends GFAddOnFeedsTable {

	/**
	 * Default functionality or legacy support
	 *
	 * @return array  Column headers.
	 */
	function get_columns() {
		if ( method_exists( 'parent', 'get_columns' ) ) {
			return parent::get_columns();
		} else {
			return $this->_column_headers[0];
		}
	}
}
