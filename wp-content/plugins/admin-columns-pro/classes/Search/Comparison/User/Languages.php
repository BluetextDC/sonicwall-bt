<?php

namespace ACP\Search\Comparison\User;

use AC\MetaType;
use ACP\Helper\Select\Options;
use ACP\Search\Comparison;
use ACP\Search\Operators;

class Languages extends Comparison\Meta
	implements Comparison\Values {

	/** @var array */
	private $languages;

	public function __construct( $languages ) {
		$operators = new Operators( array(
			Operators::EQ,
			Operators::IS_EMPTY,
		) );

		$this->languages = $languages;

		parent::__construct( $operators, 'locale', MetaType::USER );
	}

	/**
	 * @inheritdoc
	 */
	public function get_values() {
		return Options::create_from_array( $this->languages );
	}

}