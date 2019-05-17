<?php

namespace ACP\Search\Helper\MetaQuery;

use ACP\Search\Operators;
use ACP\Search\Value;
use LogicException;

final class ComparisonFactory {

	/**
	 * @param string $key
	 * @param string $operator
	 * @param Value  $value
	 *
	 * @return Comparison
	 */
	public static function create( $key, $operator, Value $value ) {
		$operators = array(
			Operators::EQ           => '=',
			Operators::NEQ          => '!=',
			Operators::CONTAINS     => 'LIKE',
			Operators::NOT_CONTAINS => 'NOT LIKE',
			Operators::BETWEEN      => 'BETWEEN',
			Operators::GT           => '>',
			Operators::LT           => '<',
		);

		if ( array_key_exists( $operator, $operators ) ) {
			return new Comparison( $key, $operators[ $operator ], $value );
		}

		$operators = array(
			Operators::BEGINS_WITH  => 'BeginsWith',
			Operators::ENDS_WITH    => 'EndsWith',
			Operators::IS_EMPTY     => 'IsEmpty',
			Operators::NOT_IS_EMPTY => 'NotEmpty',
		);

		if ( ! array_key_exists( $operator, $operators ) ) {
			throw new LogicException( 'Invalid operator found.' );
		}

		$class = __NAMESPACE__ . '\Comparison\\' . $operators[ $operator ];

		return new $class( $key, $value );
	}

}