<?php

use PixelCaffeine\Model\Job;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @class AEPC_Cron
 */
class AEPC_Cron {

	/**
	 * @var Job[]
	 */
	public static $jobs = array();

	/**
	 * Register the job instances
	 */
	protected static function bootstrap_jobs() {
		self::$jobs = array(
			new \PixelCaffeine\Job\RefreshAudiencesSize(),
			new \PixelCaffeine\ProductCatalog\Cron\RefreshFeed(),
		);
	}

	/**
	 * AEPC_Cron Constructor.
	 */
	public static function init() {
		self::bootstrap_jobs();

		foreach ( self::$jobs as $job ) {
			$job->init();
		}
	}

}
