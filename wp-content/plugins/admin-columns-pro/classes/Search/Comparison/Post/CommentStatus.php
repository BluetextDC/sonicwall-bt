<?php

namespace ACP\Search\Comparison\Post;

use ACP\Helper\Select\Options;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;

class CommentStatus extends PostField
	implements Values {

	public function __construct() {
		$operators = new Operators( array(
			Operators::EQ,
		) );

		parent::__construct( $operators );
	}

	public function get_values() {
		return Options::create_from_array( array(
			'open'   => __( 'Open', 'codepress-admin-columns' ),
			'closed' => __( 'Closed', 'codepress-admin-columns' ),
		) );
	}

	protected function get_field() {
		return 'comment_status';
	}

}