<?php

namespace ACP\Search\Comparison\Media;

use ACP\Helper\Select;
use ACP\Search\Comparison;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

class MimeType extends Comparison
	implements Comparison\RemoteValues {

	public function __construct() {
		$operators = new Operators( array(
			Operators::EQ,
		) );

		parent::__construct( $operators );
	}

	protected function create_query_bindings( $operator, Value $value ) {
		$bindings = new Bindings\Media();

		return $bindings->mime_types( $value->get_value() );
	}

	public function get_values() {
		$entities = new Select\Entities\MimeType( array(
			'post_type' => 'attachment',
		) );

		$mime_types = $entities->get_copy();

		return Select\Options::create_from_array( array_combine( $mime_types, $mime_types ) );
	}

}