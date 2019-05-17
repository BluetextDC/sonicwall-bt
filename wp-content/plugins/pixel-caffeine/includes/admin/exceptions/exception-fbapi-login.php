<?php

namespace PixelCaffeine\Admin\Exception;

class FBAPILoginException extends AEPCException {
	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * @link http://php.net/manual/en/exception.construct.php
	 *
	 * @param string $message [optional] The Exception message to throw.
	 * @param int $code [optional] The Exception code.
	 * @param \Throwable $previous [optional] The previous throwable used for the exception chaining.
	 *
	 * @since 5.1.0
	 */
	public function __construct( $message = "", $code = 0, \Throwable $previous = null ) {
		parent::__construct( $message, $code, array(), $previous );
	}

}
