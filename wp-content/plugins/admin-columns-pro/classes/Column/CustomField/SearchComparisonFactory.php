<?php
namespace ACP\Column\CustomField;

use ACP\Search\Comparison\Meta;

class SearchComparisonFactory {

	/**
	 * @param string $type
	 * @param string $meta_key
	 * @param string $meta_type
	 *
	 * @return Meta|false
	 */
	public static function create( $type, $meta_key, $meta_type ) {

		switch ( $type ) {

			case 'array' :
				return new Meta\Serialized( $meta_key, $meta_type );

			case 'checkmark' :
				return new Meta\Checkmark( $meta_key, $meta_type );

			case 'color' :
				return new Meta\Text( $meta_key, $meta_type );

			case 'count' :
				return false;

			case 'date' :
				return new Meta\Text( $meta_key, $meta_type );

			case 'excerpt' :
				return new Meta\Text( $meta_key, $meta_type );

			case 'has_content' :
				return new Meta\EmptyNotEmpty( $meta_key, $meta_type );

			case 'image' :
				return new Meta\Media( $meta_key, $meta_type );

			case 'library_id' :
				return new Meta\Media( $meta_key, $meta_type );

			case 'link' :
				return new Meta\Text( $meta_key, $meta_type );

			case 'numeric' :
				return new Meta\Numeric( $meta_key, $meta_type );

			case 'title_by_id' :
				return new Meta\Post( $meta_key, $meta_type );

			case 'user_by_id' :
				return new Meta\User( $meta_key, $meta_type );

			default :
				return new Meta\Text( $meta_key, $meta_type );
		}
	}

}