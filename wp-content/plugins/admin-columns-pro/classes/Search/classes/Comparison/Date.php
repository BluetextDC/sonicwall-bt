<?php

namespace ACP\Search\Comparison;

use ACP\Search\Comparison;
use ACP\Search\Helper\Sql\ComparisonFactory;
use ACP\Search\Labels;
use ACP\Search\Operators;
use ACP\Search\Query\Bindings;
use ACP\Search\Value;

abstract class Date extends Comparison {

	/**
	 * DB column for SQL clause
	 * @return string
	 */
	abstract protected function get_column();

	public function __construct() {
		$operators = new Operators( array(
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
		) );

		parent::__construct( $operators, Value::DATE, new Labels\Date() );
	}

	/**
	 * @inheritDoc
	 */
	protected function create_query_bindings( $operator, Value $value ) {
		if ( Operators::EQ === $operator ) {
			$value = new Value(
				array(
					$value->get_value() . ' 00:00:00',
					$value->get_value() . ' 23:59:59',
				),
				$value->get_type()
			);
			$operator = Operators::BETWEEN;
		}

		$where = ComparisonFactory::create(
			$this->get_column(),
			$operator,
			$value
		)->prepare();

		$bindings = new Bindings();
		$bindings->where( $where );

		return $bindings;
	}

}