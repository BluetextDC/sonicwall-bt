<?php

namespace PixelCaffeine\ProductCatalog;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use AEPC_Facebook_Adapter as Facebook;
use PixelCaffeine\ProductCatalog\Admin\Metaboxes;
use PixelCaffeine\ProductCatalog\Cron\RefreshFeed;
use PixelCaffeine\ProductCatalog\Dictionary\FeedSaver;
use PixelCaffeine\ProductCatalog\Entity\ProductCatalog as Entity;
use PixelCaffeine\ProductCatalog\Exception\FeedException;
use PixelCaffeine\ProductCatalog\Exception\GoogleTaxonomyException;

/**
 * Manager class of Product Catalog feature
 *
 * @class ProductCatalog
 */
final class ProductCatalogs {

	/**
	 * @var Facebook
	 */
	private $fbApi;

	/**
	 * @var DbProvider
	 */
	private $dbProvider;

	/**
	 * @var ConfigurationDefaults
	 */
	protected $defaultConfiguration;

	/**
	 * @var Metaboxes
	 */
	protected $metaboxes;

	/**
	 * @var FeedSaverInterface
	 */
	protected $feedSaver;

	/**
	 * @var BackgroundFeedSaverProcess
	 */
	protected $backgroundSaverProcess;

	/**
	 * @var ProductCatalogManager[] List of all product catalogs created
	 */
	protected $product_catalogs;

	/**
	 * ProductCatalog constructor.
	 *
	 * @param Facebook $fbApi
	 * @param DbProvider $dbProvider
	 */
	public function __construct(
		Facebook $fbApi,
		DbProvider $dbProvider,
		ConfigurationDefaults $defaultConfiguration,
		Metaboxes $metaboxes
	) {
		$this->fbApi = $fbApi;
		$this->dbProvider = $dbProvider;
		$this->defaultConfiguration = $defaultConfiguration;
		$this->metaboxes = $metaboxes;
	}

	/**
	 * Setup the necessary hooks
	 */
	public function setup() {
		// Badly the WP_Async_Request add some hooks inside the constructor, so I need to instantiate the object separately
		$this->backgroundSaverProcess = new BackgroundFeedSaverProcess;
	}

	/**
	 * Returns the default configuration of a product catalog
	 *
	 * @return ConfigurationDefaults
	 */
	public function getDefaults() {
		return $this->defaultConfiguration;
	}

	/**
	 * Get the product catalogs from DB
	 *
	 * @return ProductCatalogManager[]
	 */
	public function get_product_catalogs() {
		// No cache please
		return array_map( array( $this, 'map_manager_instance' ), $this->dbProvider->get_product_catalogs() );
	}

	/**
	 * Get the product catalog instance
	 *
	 * @param $id
	 *
	 * @return ProductCatalogManager
	 */
	public function get_product_catalog( $id ) {
		$product_catalogs = $this->get_product_catalogs();
		return $product_catalogs[ $id ];
	}

	/**
	 * Detect if at least one product catalog is created
	 *
	 * @return bool
	 */
	public function is_product_catalog_created() {
		return count( $this->get_product_catalogs() ) > 0;
	}

	/**
	 * Returns the ProductCatalogManager instance of the entity
	 *
	 * @param Entity $entity
	 *
	 * @return ProductCatalogManager
	 */
	protected function map_manager_instance( Entity $entity ) {
		return new ProductCatalogManager(
			$entity->getId(),
			$this->dbProvider,
			$this->defaultConfiguration,
			$this->metaboxes,
			$this->fbApi
		);
	}

	/**
	 * Get the product category list from google, it's necessary for the product feed requirements
	 *
	 * @throws GoogleTaxonomyException
	 */
	public function get_google_categories() {
		$cache_key = 'aepc_google_taxonomy_list';
		if ( ( $categories = get_transient( $cache_key ) ) === false ) {
			$response = wp_remote_get( 'https://www.google.com/basepages/producttype/taxonomy-with-ids.en-GB.txt' );

			if ( is_wp_error( $response ) ) {
				throw new GoogleTaxonomyException( $response->get_error_message() );
			}

			$remote_list = wp_remote_retrieve_body( $response );
			$lines = explode( "\n", trim( $remote_list ) );

			// Remove the first line that is a comment
			array_shift( $lines );

			$categories = array();
			foreach ( $lines as $line ) {
				list( $id, $hierarchy ) = explode( ' - ', trim( $line ) );
				$terms = explode( ' > ', $hierarchy );
				$hierarchy = array( array_pop( $terms ) => array() );
				foreach ( array_reverse( $terms ) as $term ) {
					$hierarchy = array( $term => $hierarchy );
				}
				$categories = array_merge_recursive( $categories, $hierarchy );
			}

			set_transient( $cache_key, $categories, MONTH_IN_SECONDS );
		}

		return $categories;
	}

	/**
	 * Call the background saver service to generate the feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 * @param string $context
	 *
	 * @return array|\WP_Error
	 */
	public function generate_feed( ProductCatalogManager $product_catalog, $context ) {
		return $product_catalog->getFeedSaver()->save( $context );
	}

	/**
	 * Save a new product catalog
	 *
	 * @param Entity $entity
	 *
	 * @return array|\WP_Error
	 * @throws \Exception
	 */
	public function create_product_catalog( Entity $entity ) {

		// Save into the db
		$this->dbProvider->create_product_catalog( $entity );

		// Get product manager instance
		$product_catalog = $this->get_product_catalog( $entity->getId() );

		try {
			$response = $this->generate_feed( $product_catalog, FeedSaver::NEW_CONTEXT );

			// Save product catalog in FB
			if ( $product_catalog->configuration()->get( Configuration::OPTION_FB_ENABLE ) ) {
				$product_catalog->push_to_fb();
			}

			return $response;
		} catch ( \Exception $e ) {
			$this->dbProvider->delete_product_catalog( $entity );
			throw $e;
		}
	}

	/**
	 * Update a product catalog
	 *
	 * @param ProductCatalogManager $product_catalog
	 *
	 * @return array|\WP_Error
	 * @throws FeedException
	 * @throws \Exception
	 */
	public function update_product_catalog( ProductCatalogManager $product_catalog ) {

		// Save new entity
		$product_catalog->update();

		// Unschedule cron jobs
		$product_catalog->unschedule_job();

		// Save product catalog in FB
		if ( $product_catalog->configuration()->get( Configuration::OPTION_FB_ENABLE ) ) {
			$product_catalog->push_to_fb();
		}

		return $this->generate_feed( $product_catalog, FeedSaver::REFRESH_CONTEXT );
	}

	/**
	 * Delete a product catalog
	 *
	 * @param ProductCatalogManager $product_catalog
	 *
	 * @throws Exception\EntityException
	 * @throws FeedException
	 */
	public function delete_product_catalog( ProductCatalogManager $product_catalog ) {
		$product_catalog->delete();

		// Unschedule cron jobs
		$product_catalog->unschedule_job();
	}

	/**
	 * Get the background saver instance
	 *
	 * We have to instantiate this class before when it must be used because the class has badly some hooks inside the
	 * constructor that might be added early
	 *
	 * @return BackgroundFeedSaverProcess
	 */
	public function getBackgroundSaverProcess() {
		return $this->backgroundSaverProcess;
	}

	/**
	 * Detect if the product feed is saving in background
	 *
	 * @param ProductCatalogManager $product_catalog
	 *
	 * @return bool
	 */
	public function is_feed_saving( ProductCatalogManager $product_catalog ) {
		try {
			return $this->backgroundSaverProcess->is_updating( $product_catalog );
		} catch ( FeedException $e ) {
			return false;
		}
	}

	/**
	 * Check if the product catalog feature can work
	 *
	 * @return bool
	 */
	public function is_product_catalog_enabled() {
		return \AEPC_Addons_Support::are_detected_addons();
	}

}
