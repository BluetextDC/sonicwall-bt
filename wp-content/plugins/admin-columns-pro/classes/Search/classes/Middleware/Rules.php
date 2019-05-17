<?php

namespace ACP\Search\Middleware;

class Rules {

	/**
	 * @inheritDoc
	 */
	public function __invoke( array $rules ) {
		$mapping = (object) array(
			'operator'   => new Mapping\Operator(),
			'value_type' => new Mapping\ValueType(),
			'rule'       => new Mapping\Rule(),
		);

		$response_rules = array();

		foreach ( $rules as $rule ) {
			$response_rules[] = (object) array(
				$mapping->rule->name        => $rule['name'],
				$mapping->rule->operator    => $mapping->operator->{$rule['operator']},
				$mapping->rule->value       => $rule['value'],
				$mapping->rule->value_type  => $mapping->value_type->{$rule['value_type']},
				$mapping->rule->value_label => $rule['value_label'],
			);
		}

		return (object) array(
			'condition' => 'AND',
			'rules'     => $response_rules,
			'valid'     => true,
		);
	}

}