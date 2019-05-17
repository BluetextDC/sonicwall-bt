<?php

namespace ACP;

use AC\Request;
use ACP\Exception;

abstract class Controller {

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @param Request $request
	 */
	public function __construct( Request $request ) {
		$this->request = $request;
	}

	/**
	 * @param $action
	 */
	public function dispatch( $action ) {
		$method = $action . '_action';

		if ( ! is_callable( array( $this, $method ) ) ) {
			throw Exception\Controller::from_invalid_action( $action );
		}

		$this->$method();
	}

}