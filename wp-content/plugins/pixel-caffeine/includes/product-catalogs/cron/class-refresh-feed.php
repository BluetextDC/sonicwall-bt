<?php

namespace PixelCaffeine\ProductCatalog\Cron;


use PixelCaffeine\Model\Job;
use PixelCaffeine\ProductCatalog\BackgroundFeedSaver;
use PixelCaffeine\ProductCatalog\Configuration;
use PixelCaffeine\ProductCatalog\Dictionary\FeedSaver;
use PixelCaffeine\ProductCatalog\Exception\FeedException;
use PixelCaffeine\ProductCatalog\ProductCatalogManager;

class RefreshFeed extends Job {

	const HOOK_NAME = 'aepc_refresh_product_feed';

	/**
	 * Register the recurrences in the cron_schedules hook
	 *
	 * @var array $recurrences
	 *
	 * @return array {
	 *     @type int $interval
	 *     @type string $display
	 * }
	 */
	public function recurrences( $recurrences ) {
		$product_catalogs = \AEPC_Admin::$product_catalogs_service->get_product_catalogs();

		foreach ( $product_catalogs as $product_catalog ) {
			$recurrence_id = $this->get_recurrence_id( $product_catalog );
			if ( isset( $recurrences[ $recurrence_id ] ) ) {
				continue;
			}

			$cycle = $product_catalog->configuration()->get( Configuration::OPTION_REFRESH_CYCLE );
			$cycle_type = $product_catalog->configuration()->get( Configuration::OPTION_REFRESH_CYCLE_TYPE );
			$recurrences[ $recurrence_id ] = array(
				'interval' => $cycle * constant( strtoupper( $cycle_type ) . '_IN_SECONDS' ),
				'display' => sprintf( __( 'Feed Refresh Every %s %s', 'pixel-caffeine' ), $cycle, $cycle_type )
			);
		}

		return $recurrences;
	}

	public function tasks() {
		$tasks = array();
		$product_catalogs = \AEPC_Admin::$product_catalogs_service->get_product_catalogs();

		foreach ( $product_catalogs as $product_catalog ) {
			$tasks[ $this->get_recurrence_id( $product_catalog ) ] = array(
				'hook' => self::HOOK_NAME,
				'callback' => array( $this, 'task' ),
				'callback_args' => array( $product_catalog->get_entity()->getId() )
			);
		}

		return $tasks;
	}

	/**
	 * The product catalog refresh task
	 *
	 * @param string $product_catalog_id
	 */
	public function task( $product_catalog_id ) {
		$service = \AEPC_Admin::$product_catalogs_service;
		$product_catalog = $service->get_product_catalog( $product_catalog_id );

		// Firstly delete
		try {
			$service->generate_feed( $product_catalog, FeedSaver::REFRESH_CONTEXT );
		} catch ( FeedException $e ) {
		}
	}

	/**
	 * Get the recurrence ID for the product catalog
	 *
	 * @param ProductCatalogManager $product_catalog
	 *
	 * @return string
	 */
	protected function get_recurrence_id( ProductCatalogManager $product_catalog ) {
		$cycle = $product_catalog->configuration()->get( Configuration::OPTION_REFRESH_CYCLE );
		$cycle_type = $product_catalog->configuration()->get( Configuration::OPTION_REFRESH_CYCLE_TYPE );
		return 'aepc-feed-' . $product_catalog->get_entity()->getId() . '-' . $cycle . '-' . $cycle_type;
	}

	/**
	 * Unschedule a job for a specific product catalog
	 *
	 * @param string $product_catalog_id
	 */
	public function unschedule( $product_catalog_id = '' ) {
		if ( ! empty( $product_catalog_id ) ) {
			$timestamp = wp_next_scheduled( self::HOOK_NAME, array( $product_catalog_id ) );
			wp_unschedule_event( $timestamp, self::HOOK_NAME, array( $product_catalog_id ) );
		} else {
			parent::unschedule();
		}
	}

}
