<?php

use Monolog\Logger;
use PixelCaffeine\Logs\LogDBHandler;
use Psr\Log\LoggerInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @class AEPC_Admin_Logger
 */
class AEPC_Admin_Logger {

	const LOG_NAME = 'aepc-fb-api';
	const LOGS_DIR = '/pixel-caffeine';
	const LOG_FILENAME = 'logs-%s.log';

	/** @var LoggerInterface */
	protected $logger;

	/**
	 * Setup the logger
	 */
	public function setup() {
		global $wpdb;

		$this->logger = new Logger( self::LOG_NAME );
		$this->logger->pushHandler( new LogDBHandler( $wpdb ) );
	}

	/**
	 * Get the path to log
	 *
	 * @return string
	 */
	protected function get_log_path() {
		$upload_dir = wp_upload_dir();
		$filename = sprintf( self::LOG_FILENAME, sanitize_file_name( wp_hash( self::LOG_FILENAME ) ) );
		return $upload_dir['basedir'] . self::LOGS_DIR . '/' . $filename;
	}

	/**
	 * Log the message
	 *
	 * The message MAY contain placeholders in the form: {foo} where foo
	 * will be replaced by the context data in key "foo".
	 *
	 * The context array can contain arbitrary data. The only assumption that
	 * can be made by implementors is that if an Exception instance is given
	 * to produce a stack trace, it MUST be in a key named "exception".
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function log( $message, array $context = array() ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$context = wp_parse_args( $context, array(
			'wp_version' => $GLOBALS['wp_version'],
			'plugins' => get_plugins()
		) );
		$this->logger->debug( $message, $context );
	}

}
