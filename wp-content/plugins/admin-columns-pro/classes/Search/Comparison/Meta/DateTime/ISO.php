<?php

namespace ACP\Search\Comparison\Meta\DateTime;

use ACP\Search\Comparison\Meta;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class ISO extends Meta {

	public function __construct( $meta_key, $type ) {
		$operators = new Operators( array(
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		) );

		parent::__construct( $operators, $meta_key, $type, Value::DATE, new Labels\Date() );
	}

	protected function get_meta_query( $operator, Value $value ) {
		if ( Operators::EQ === $operator ) {
			$value = new Value(
				array(
					$value->get_value() . ' 00:00:00',
					$value->get_value() . ' 23:59:59',
				),
				Value::DATE
			);
			$operator = Operators::BETWEEN;
		}

		return parent::get_meta_query( $operator, $value );
	}

}