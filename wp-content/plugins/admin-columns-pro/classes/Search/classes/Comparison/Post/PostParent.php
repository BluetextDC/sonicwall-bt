<?php

namespace ACP\Search\Comparison\Post;

use ACP\Helper\Select;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class PostParent extends PostField
	implements SearchableValues {

	/** @var string */
	private $post_type;

	public function __construct( $post_type ) {
		$operators = new Operators( array(
			Operators::EQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		) );

		$this->post_type = $post_type;

		parent::__construct( $operators );
	}

	protected function get_field() {
		return 'post_parent';
	}

	public function get_values( $s, $paged ) {
		$entities = new Select\Entities\Post( array(
			's'         => $s,
			'paged'     => $paged,
			'post_type' => $this->post_type,
		) );

		return new Select\Options\Paginated(
			$entities,
			new Select\Formatter\PostTitle( $entities )
		);
	}

}