<?php

namespace ACP\Search\Helper\Sql;

use ACP\Search\Operators;
use ACP\Search\Value;
use LogicException;

final class ComparisonFactory {

	/**
	 * @param string $column
	 * @param string $operator
	 * @param Value  $value
	 *
	 * @return Comparison
	 */
	public static function create( $column, $operator, Value $value ) {
		$operators = array(
			Operators::EQ           => '=',
			Operators::NEQ          => '!=',
			Operators::LT           => '<',
			Operators::LTE          => '<=',
			Operators::GT           => '>',
			Operators::GTE          => '>=',
			Operators::IS_EMPTY     => '=',
			Operators::NOT_IS_EMPTY => '!=',
		);

		if ( array_key_exists( $operator, $operators ) ) {
			return new Comparison( $column, $operators[ $operator ], $value );
		}

		$operators = array(
			Operators::CONTAINS     => 'Contains',
			Operators::NOT_CONTAINS => 'NotContains',
			Operators::BEGINS_WITH  => 'BeginsWith',
			Operators::ENDS_WITH    => 'EndsWith',
			Operators::IN           => 'In',
			Operators::NOT_IN       => 'NotIn',
			Operators::BETWEEN      => 'Between',
		);

		if ( ! array_key_exists( $operator, $operators ) ) {
			throw new LogicException( 'Invalid operator found.' );
		}

		$class = __NAMESPACE__ . '\Comparison\\' . $operators[ $operator ];

		return new $class( $column, $value );
	}

}