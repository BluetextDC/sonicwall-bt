<?php

namespace ACP\Search\Comparison\Meta;

use ACP\Helper\Select\Options;
use ACP\Search\Comparison\Meta;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;
use ACP\Search\Value;

class Checkmark extends Meta
	implements Values {

	public function __construct( $meta_key, $meta_type ) {
		$operators = new Operators( array(
			Operators::EQ,
		) );

		parent::__construct( $operators, $meta_key, $meta_type );
	}

	public function get_values() {
		return Options::create_from_array( array(
			'1' => __( 'True', 'codepress-admin-columns' ),
			'0' => __( 'False', 'codepress-admin-columns' ),
		) );
	}

	public function get_meta_query( $operator, Value $value ) {
		$meta_query = array();

		switch ( $value->get_value() ) {

			case '1' :
				$meta_query = array(
					'key'     => $this->get_meta_key(),
					'value'   => array( '0', 'no', 'false', 'off' ),
					'compare' => 'NOT IN',
				);

				break;
			case '0' :
				$meta_query = array(
					'relation' => 'OR',
					array(
						'key'     => $this->get_meta_key(),
						'compare' => 'NOT EXISTS',
					),
					array(
						'key'   => $this->get_meta_key(),
						'value' => array( '0', 'no', 'false', 'off', '' ),
					),
				);

				break;
		}

		return $meta_query;
	}

}