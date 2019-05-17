<?php

namespace ACP\Search\Comparison\Post;

use ACP\Helper\Select;
use ACP\Search\Comparison\RemoteValues;
use ACP\Search\Operators;

class Status extends PostField
	implements RemoteValues {

	/** @var string */
	private $post_type;

	public function __construct( $post_type ) {
		$operators = new Operators( array(
			Operators::EQ,
			Operators::NEQ,
		) );

		$this->post_type = $post_type;

		parent::__construct( $operators );
	}

	/**
	 * @return string
	 */
	protected function get_field() {
		return 'post_status';
	}

	/**
	 * @return Select\Options
	 */
	public function get_values() {
		$entities = new Select\Entities\PostStatus( array(
			'post_type' => $this->post_type,
		) );

		$results = array();
		foreach ( $entities as $value => $entity ) {
			$results[] = new Select\Option( $value, $entity->label );
		}

		return new Select\Options( $results );
	}

}