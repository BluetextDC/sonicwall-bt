<?php

namespace ACP\Search\Comparison\Comment;

use ACP\Helper\Select\Options;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;

class Approved extends Field
	implements Values {

	public function __construct() {
		$operators = new Operators( array(
			Operators::EQ,
		) );

		parent::__construct( $operators );
	}

	public function get_values() {
		return Options::create_from_array( array(
			__( 'Unapproved' ),
			_x( 'Approved', 'comment status' ),
		) );
	}

	protected function get_field() {
		return 'comment_approved';
	}

}