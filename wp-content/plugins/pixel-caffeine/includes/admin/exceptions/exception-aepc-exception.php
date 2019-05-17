<?php

namespace PixelCaffeine\Admin\Exception;

class AEPCException extends \Exception {

	/**
	 * Construct the exception. Note: The message is NOT binary safe.
	 * @link http://php.net/manual/en/exception.construct.php
	 *
	 * @param string $message [optional] The Exception message to throw.
	 * @param int $code [optional] The Exception code.
	 * @param array $context
	 * @param \Throwable $previous [optional] The previous throwable used for the exception chaining.
	 *
	 * @since 5.1.0
	 */
	public function __construct( $message = "", $code = 0, $context = array(), \Throwable $previous = null ) {
		\AEPC_Admin::$logger->log( $message, array_merge( array(
			'code' => $code,
			'exception' => get_class( $this ),
			'$_REQUEST' => isset( $_REQUEST ) ? $_REQUEST : array()
		), $context ) );
		parent::__construct( $message, $code, $previous );
	}

}
