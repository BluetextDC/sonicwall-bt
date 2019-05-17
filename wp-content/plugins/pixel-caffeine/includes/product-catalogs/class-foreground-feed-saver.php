<?php

namespace PixelCaffeine\ProductCatalog;


class ForegroundFeedSaver implements FeedSaverInterface {

	/**
	 * @var ProductCatalogManager
	 */
	protected $product_catalog;

	/**
	 * BackgroundFeedSaver constructor.
	 *
	 * @param ProductCatalogManager $product_catalog
	 */
	public function __construct( ProductCatalogManager $product_catalog ) {
		$this->product_catalog = $product_catalog;
	}

	/**
	 * Run the save process of the feed
	 *
	 * @param string $context
	 *
	 * @return mixed
	 * @throws Exception\EntityException
	 * @throws Exception\FeedException
	 * @throws \Throwable
	 */
	public function save( $context ) {
		$entity = $this->product_catalog->get_entity();
		$prev_counter = $entity->getProductsCount();

		try {
			$this->product_catalog->getFeedWriter()->uploadStart( $context );

			// Save
			do {
				$this->product_catalog->getFeedWriter()->saveChunk( $context );
			} while ( $this->product_catalog->there_are_items_to_save() );

			// Success
			$this->product_catalog->getFeedWriter()->uploadSuccess( $context );
			$this->product_catalog->get_entity()->clearLastErrorMessage();
			$this->product_catalog->update();

			if ( ! $this->product_catalog->getFeedDirectoryHelper()->isFeedExisting() ) {
				\AEPC_Admin::$logger->log( 'Saving process complete successfully but the file is not created in the filesystem.', array(
					'exception' => 'FeedCreationException'
				) );
			}

			return true;
		}

		catch ( \Throwable $e ) {
			$this->product_catalog->getFeedWriter()->uploadFailure( $context );
			$this->product_catalog->get_entity()->setLastErrorMessage( $e->getMessage() );
			$this->product_catalog->get_entity()->setProductsCount( $prev_counter );
			$this->product_catalog->update();

			throw $e;
		}
	}

}
