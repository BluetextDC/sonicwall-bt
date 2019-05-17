<?php

namespace PixelCaffeine\ProductCatalog;


use PixelCaffeine\ProductCatalog\Dictionary\FeedSaver;
use PixelCaffeine\ProductCatalog\Exception\FeedException;

class BackgroundFeedSaver implements FeedSaverInterface {

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
	 * @throws FeedException
	 */
	public function save( $context ) {
		$backgroundSaver = \AEPC_Admin::$product_catalogs_service->getBackgroundSaverProcess();
		$entity = $this->product_catalog->get_entity();

		$response = $backgroundSaver
		     ->data( array(
			     array(
				     FeedSaver::CONTEXT_FIELD => $context,
				     FeedSaver::ID_FIELD => $entity->getId(),
				     FeedSaver::PREV_COUNTER_FIELD => $entity->getProductsCount()
			     )
		     ) )
		     ->save()
		     ->dispatch();

		if ( is_wp_error( $response ) ) {
			throw FeedException::feedCannotBeSaved( $response );
		}

		return $response;
	}

}
