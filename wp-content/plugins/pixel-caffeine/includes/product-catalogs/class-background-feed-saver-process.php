<?php

namespace PixelCaffeine\ProductCatalog;

use AEPC_Admin_Notices;
use PixelCaffeine\ProductCatalog\Dictionary\FeedSaver;

class BackgroundFeedSaverProcess extends \WP_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'product_catalog_saving';

	/**
	 * @var array
	 */
	protected $current_item;

	/**
	 * @var ProductCatalogManager
	 */
	protected $product_catalog;

	/**
	 * @var \Throwable
	 */
	protected $exception;

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param array $product_catalog {
	 *     The product catalog to generate
	 *
	 *     @type string $id The name of product catalog
	 *     @type string $mode One of 'start' or 'continue'
	 * }
	 *
	 * @return mixed
	 *
	 */
	protected function task( $product_catalog ) {
		try {
			$this->current_item    = wp_parse_args( $product_catalog, array(
				FeedSaver::MODE_FIELD => FeedSaver::START_MODE,
				FeedSaver::CONTEXT_FIELD => FeedSaver::NEW_CONTEXT,
				FeedSaver::PREV_COUNTER_FIELD => 0,
			) );

			$product_catalog_id = $this->current_item[ FeedSaver::ID_FIELD ];
			$context            = $this->current_item[ FeedSaver::CONTEXT_FIELD ];

			$service               = \AEPC_Admin::$product_catalogs_service;
			$this->product_catalog = $service->get_product_catalog( $product_catalog_id );

			// If we are in the first step, launch the starting method
			if ( FeedSaver::START_MODE === $this->current_item[ FeedSaver::MODE_FIELD ] ) {
				$this->product_catalog->getFeedWriter()->uploadStart( $context );
			}

			// Save
			$this->product_catalog->getFeedWriter()->saveChunk( $context );

			// Restart again with new chunk if any
			if ( $this->product_catalog->there_are_items_to_save() ) {
				$this->push_to_queue( array_merge( $this->current_item, array(
					FeedSaver::MODE_FIELD => FeedSaver::CONTINUE_MODE
				) ) );
				$this->save();
			}
		}

		catch ( \Throwable $e ) {
			$this->exception = $e;
			$this->product_catalog->get_entity()->setLastErrorMessage( $this->exception->getMessage() );
			$this->product_catalog->get_entity()->setProductsCount( $this->current_item[ FeedSaver::PREV_COUNTER_FIELD ]  );
			$this->product_catalog->update();
			AEPC_Admin_Notices::add_notice( 'error', 'main', $this->exception->getMessage() );
		}

		return false;
	}

	/**
	 * Complete.
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 *
	 * @throws Exception\FeedException
	 */
	protected function complete() {
		$context = $this->current_item[ FeedSaver::CONTEXT_FIELD ];

		if ( $this->exception ) {
			\AEPC_Admin::$logger->log( $this->exception->getMessage(), array(
				'code' => $this->exception->getCode(),
				'exception' => get_class( $this->exception ),
				'current_item' => $this->current_item
			) );

			$this->product_catalog->getFeedWriter()->uploadFailure( $context );
		} else {
			$this->product_catalog->getFeedWriter()->uploadSuccess( $context );
			$this->product_catalog->get_entity()->clearLastErrorMessage();
			$this->product_catalog->update();
			AEPC_Admin_Notices::add_notice(
				'success',
				'main',
				make_clickable( sprintf( __( 'The Product Catalog Feed is saved. This is the URL: %s', 'pixel-caffeine' ), $this->product_catalog->get_url() ) )
			);

			if ( ! $this->product_catalog->getFeedDirectoryHelper()->isFeedExisting() ) {
				\AEPC_Admin::$logger->log( 'Saving process complete successfully but the file is not created in the filesystem.', array(
					'exception' => 'FeedCreationException'
				) );
			}
		}

		parent::complete();
	}


	/**
	 * Dispatch updater.
	 *
	 * Updater will still run via cron job if this fails for any reason.
	 *
	 * @return \WP_Error|array
	 */
	public function dispatch() {
		return parent::dispatch();
	}

	/**
	 * Is the updater running?
	 *
	 * @param ProductCatalogManager $product_catalog
	 *
	 * @return bool
	 * @throws Exception\FeedException
	 */
	public function is_updating( ProductCatalogManager $product_catalog ) {
		return $product_catalog->getFeedWriter()->isSaving();
	}

}
