<?php

namespace ACP\Search\Comparison\Meta\DateTime;

use ACP\Search\Comparison\Meta;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Value;

class Timestamp extends Meta {

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
		$time = is_array( $value->get_value() )
			? array_map( array( $this, 'to_time' ), $value->get_value() )
			: $this->to_time( $value->get_value() );

		switch ( $operator ) {
			case Operators::EQ:
				$operator = Operators::BETWEEN;
				$value = new Value(
					array(
						$time,
						$time + DAY_IN_SECONDS - 1,
					),
					Value::INT
				);

				break;
			default:
				$value = new Value( $time, Value::INT );
		}

		return parent::get_meta_query(
			$operator,
			$value
		);
	}

	/**
	 * @param string $value
	 *
	 * @return int
	 */
	private function to_time( $value ) {
		return (int) strtotime( $value );
	}
}