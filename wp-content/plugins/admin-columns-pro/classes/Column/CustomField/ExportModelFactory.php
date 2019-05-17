<?php
namespace ACP\Column\CustomField;

use AC\Column;
use ACP\Export\Model;

class ExportModelFactory {

	/**
	 * @param string             $type
	 * @param Column\CustomField $column
	 *
	 * @return Model
	 */
	public static function create( $type, Column\CustomField $column ) {

		switch ( $type ) {

			case 'array' :
				return new Model\Value( $column );

			case 'checkmark' :
				return new Model\RawValue( $column );

			case 'color' :
				return new Model\RawValue( $column );

			case 'count' :
				return new Model\Value( $column );

			case 'date' :
				return new Model\CustomField\Date( $column );

			case 'excerpt' :
				return new Model\RawValue( $column );

			case 'has_content' :
				return new Model\Value( $column );

			case 'image' :
				return new Model\CustomField\Image( $column );

			case 'library_id' :
				return new Model\CustomField\Image( $column );

			case 'link' :
				return new Model\RawValue( $column );

			case 'numeric' :
				return new Model\RawValue( $column );

			case 'title_by_id' :
				return new Model\StrippedValue( $column );

			case 'user_by_id' :
				return new Model\StrippedValue( $column );

			default :
				return new Model\RawValue( $column );
		}
	}

}