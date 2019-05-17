<?php
namespace ACP\Search\Comparison\Comment;

use ACP\Helper\Select;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class User extends Field
	implements SearchableValues {

	public function __construct() {
		$operators = new Operators( array(
			Operators::EQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		) );

		parent::__construct( $operators );
	}

	/**
	 * @return string
	 */
	protected function get_field() {
		return 'user_id';
	}

	public function get_values( $search, $paged ) {
		$entities = new Select\Entities\User( compact( 'search', 'paged' ) );

		return new Select\Options\Paginated(
			$entities,
			new Select\Group\UserRole(
				new Select\Formatter\UserName( $entities )
			)
		);
	}

}