<?php

namespace PixelCaffeine\Logs;


use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use PixelCaffeine\Logs\Entity\Log;

class LogDBHandler extends AbstractProcessingHandler {

	/**
	 * @var \wpdb
	 */
	protected $wpdb;

	/**
	 * @param \wpdb $wpdb
	 * @param bool|int $level The minimum logging level at which this handler will be triggered
	 * @param Boolean $bubble Whether the messages that are handled can bubble up the stack or not
	 */
	public function __construct( \wpdb $wpdb, $level = Logger::DEBUG, $bubble = true ) {
		$this->wpdb = $wpdb;
		parent::__construct( $level, $bubble );
	}

	/**
	 * Writes the record down to the log of the implementing handler
	 *
	 * @param  array $record
	 *
	 * @return void
	 */
	protected function write( array $record ) {
		$exception = $record['context']['exception'];
		unset( $record['context']['exception'] );

		$log = new Log(
			$exception,
			$record['message'],
			$record['datetime'],
			$record['context']
		);

		$repository = new LogRepository();
		$repository->save( $log );
	}
}
