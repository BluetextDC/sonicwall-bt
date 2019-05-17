<?php

namespace PixelCaffeine\Admin\Exception;

class FBAPIException extends AEPCException {

	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * @link http://php.net/manual/en/exception.construct.php
	 *
	 * @param string $message [optional] The Exception message to throw.
	 * @param int $code [optional] The Exception code.
	 * @param \WP_Error|array $response
	 * @param \Throwable $previous [optional] The previous throwable used for the exception chaining.
	 *
	 * @since 5.1.0
	 */
	public function __construct( $message = "", $code = 0, $response = null, \Throwable $previous = null ) {
		parent::__construct( $message, $code, array(
			'response' => $response,
		), $previous );
	}

}
