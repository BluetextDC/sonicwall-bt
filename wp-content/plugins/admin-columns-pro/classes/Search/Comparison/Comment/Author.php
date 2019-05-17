<?php

namespace ACP\Search\Comparison\Comment;

use ACP\Helper\Select;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Author extends Field
	implements SearchableValues {

	public function __construct() {
		$operators = new Operators( array(
			Operators::EQ,
			Operators::CONTAINS,
			Operators::NOT_CONTAINS,
			Operators::BEGINS_WITH,
			Operators::ENDS_WITH,
		) );

		parent::__construct( $operators );
	}

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