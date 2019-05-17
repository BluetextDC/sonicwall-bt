<?php

namespace PixelCaffeine\ProductCatalog;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use PixelCaffeine\ProductCatalog\Admin\Metaboxes;
use PixelCaffeine\ProductCatalog\Cron\RefreshFeed;
use PixelCaffeine\ProductCatalog\Dictionary\FeedSaver;
use PixelCaffeine\ProductCatalog\Entity\ProductCatalog as Entity;
use PixelCaffeine\ProductCatalog\Entity\ProductCatalog;
use PixelCaffeine\ProductCatalog\Exception\FeedException;
use PixelCaffeine\ProductCatalog\Feed\WriterInterface;
use PixelCaffeine\ProductCatalog\Feed\XMLWriter;
use PixelCaffeine\ProductCatalog\Helper\FeedDirectoryHelper;

/**
 * Product catalog entity manager
 *
 * @class Manager
 */
class ProductCatalogManager {

	const FILTER_ALL = 'all';
	const FILTER_SAVED = 'saved';
	const FILTER_EDITED = 'edited';
	const FILTER_NOT_SAVED = 'not-saved';

	/**
	 * @var array
	 */
	private $allowed_feed_formats = array( 'xml' );

	/**
	 * @var string
	 */
    protected $id;

	/**
	 * @var Entity
	 */
    protected $entity;

	/**
	 * @var DbProvider
	 */
	protected $dbProvider;

	/**
	 * @var ConfigurationDefaults
	 */
	protected $defaultConfiguration;

	/**
	 * @var Metaboxes
	 */
	protected $metaboxes;

	/**
	 * @var FeedDirectoryHelper
	 */
	protected $directoryHelper;

	/**
	 * @var WriterInterface
	 */
	protected $feedWriter;

	/**
	 * @var Configuration
	 */
	protected $configuration;

	/**
	 * @var \AEPC_Facebook_Adapter
	 */
	protected $fbApi;

	/**
	 * ProductCatalog constructor.
	 *
	 * @param string $id
	 * @param DbProvider $dbProvider
	 * @param ConfigurationDefaults $defaultConfiguration
	 * @param Metaboxes $metaboxes
	 * @param \AEPC_Facebook_Adapter $fbApi
	 *
	 * @throws Exception\EntityException
	 */
	public function __construct(
		$id,
		DbProvider $dbProvider,
		ConfigurationDefaults $defaultConfiguration,
		Metaboxes $metaboxes,
		\AEPC_Facebook_Adapter $fbApi
	) {
		$this->id = $id;
		$this->dbProvider = $dbProvider;
		$this->defaultConfiguration = $defaultConfiguration;
		$this->metaboxes = $metaboxes;
		$this->fbApi = $fbApi;

		// Load data from the database
		$this->load();
	}

	/**
	 * Load the entity from the DB
	 * @throws Exception\EntityException
	 */
	public function load() {
		$this->entity = $this->dbProvider->get_product_catalog( $this->id );
	}

	/**
	 * Delete the product catalog
	 * @throws FeedException
	 * @throws Exception\EntityException
	 */
	public function delete() {
		$this->getFeedWriter()->delete( FeedSaver::DELETE_CONTEXT );
		$this->dbProvider->delete_product_catalog( $this->entity );
	}

	/**
	 * Edit the product catalog
	 * @throws Exception\EntityException
	 */
	public function update() {
		$this->entity->setConfig( $this->configuration()->get_configuration_data() );
		$this->dbProvider->update_product_catalog( $this->entity );
	}

	/**
	 * Unschedule the job for this product feed
	 */
	public function unschedule_job() {
		$job = new RefreshFeed();
		$job->unschedule( $this->entity->getId() );
	}

	/**
	 * @param ProductCatalog $entity
	 */
	public function set_entity( ProductCatalog $entity ) {
		$this->entity = $entity;
	}

	/**
	 * @return Entity
	 */
	public function get_entity() {
		return $this->entity;
	}

	/**
	 * Returns the URL of the feed
	 *
	 * @return string
	 */
	public function get_url() {
		return $this->getFeedDirectoryHelper()->getFeedURL();
	}

	/**
	 * Returns the URL of the product catalog in the business manager page
	 *
	 * @return string
	 */
	public function get_fb_url() {
		$product_catalog_id = $this->configuration()->get( Configuration::OPTION_FB_PRODUCT_CATALOG_ID );
		return sprintf( 'https://www.facebook.com/products/catalogs/%d/diagnostics#', $product_catalog_id );
	}

	/**
	 * Get the configurator class of this product catalog
	 *
	 * @return Configuration
	 */
	public function configuration() {
		if ( ! $this->configuration instanceof Configuration ) {
			$this->set_configurator();
		}

		return $this->configuration;
	}

	/**
	 * Set the configurator into this product catalog
	 */
	protected function set_configurator() {
		$this->configuration = new Configuration( $this->entity, $this->defaultConfiguration );
	}

	/**
	 * Returns the only selected addons, from the detected ones
	 *
	 * @return \AEPC_Edd_Addon_Support[]|\AEPC_Woocommerce_Addon_Support[]
	 */
	protected function get_addon_selected() {
		$addons = array();
		$selected_addons = (array) $this->configuration()->get( Configuration::OPTION_SELECTED_ADDON );

		foreach ( \AEPC_Addons_Support::get_detected_addons() as $addon ) {
			if ( in_array( $addon->get_slug(), $selected_addons ) ) {
				$addons[] = $addon;
			}
		}

		return $addons;
	}

	/**
	 * Get the items filtered by feed status
	 *
	 * @param string $filter
	 *
	 * @return FeedMapper[]
	 */
	public function get_items( $filter ) {
		$items = array();
		foreach ( $this->get_addon_selected() as $addon ) {
			if ( self::FILTER_ALL === $filter ) {
				$feed_entries = $addon->get_feed_entries( $this, $this->metaboxes );
			} elseif ( self::FILTER_NOT_SAVED === $filter ) {
				$feed_entries = $addon->get_feed_entries_to_save( $this, $this->metaboxes );
			} elseif ( self::FILTER_EDITED === $filter ) {
				$feed_entries = $addon->get_feed_entries_to_edit( $this, $this->metaboxes );
			} else {
				continue;
			}

			$items = array_merge( $items, $feed_entries );
		}

		// Assign to each the FeedMapper instance
		$configuration = $this->configuration();
		$items = array_map( function( \AEPC_Addon_Product_Item $item ) use ( $configuration ) {
			return new FeedMapper( $item, $configuration );
		}, $items );

		return $items;
	}

	/**
	 * Remove all feed status flag associated to this product catalog from all products in each addon
	 */
	public function remove_all_feed_status_flags() {
		foreach ( $this->get_addon_selected() as $addon ) {
			$addon->remove_all_feed_status( $this );
		}
	}

	/**
	 * Get the allowed feed formats.
	 *
	 * Give ability to external developers to specify own new format and define the specific class
	 *
	 * @return array
	 */
	public function get_allowed_feed_formats() {
		return apply_filters( 'aepc_allowed_feed_formats', $this->allowed_feed_formats );
	}

	/**
	 * Let know if the format specified is supported
	 *
	 * @param $format
	 *
	 * @return bool
	 */
	public function is_feed_format_allowed( $format ) {
		return in_array( $format, $this->get_allowed_feed_formats() );
	}

	/**
	 * Return the directory helper of the feed
	 *
	 * @return FeedDirectoryHelper
	 */
	public function getFeedDirectoryHelper() {
		if ( ! $this->directoryHelper instanceof FeedDirectoryHelper ) {
			$this->setFeedDirectoryHelper( new FeedDirectoryHelper( $this ) );
		}

		return $this->directoryHelper;
	}

	/**
	 * Sets the output writer manually
	 *
	 * @param FeedDirectoryHelper $directoryHelper
	 */
	public function setFeedDirectoryHelper( FeedDirectoryHelper $directoryHelper ) {
		$this->directoryHelper = $directoryHelper;
	}

	/**
	 * Return the output writer
	 *
	 * @return WriterInterface
	 * @throws FeedException
	 */
	public function getFeedWriter() {
		if ( ! $this->feedWriter instanceof WriterInterface ) {
			$this->setFeedWriterByFormat( $this->get_entity()->getFormat() );
		}

		return $this->feedWriter;
	}

	/**
	 * Sets the output writer manually
	 *
	 * @param WriterInterface $feedWriter
	 */
	public function setFeedWriter( WriterInterface $feedWriter ) {
		$this->feedWriter = $feedWriter;
	}

	/**
	 * @param $format
	 *
	 * @throws FeedException
	 */
	protected function setFeedWriterByFormat( $format ) {
		if ( ! $this->is_feed_format_allowed( $format ) ) {
			throw FeedException::formatNotSupported( $format );
		}

		switch ( $format ) {

			case 'xml':
				$this->feedWriter = new XMLWriter( $this, $this->dbProvider, $this->getFeedDirectoryHelper() );
				break;

			default :
				$this->feedWriter = apply_filters( 'aepc_feed_writer', null, $format, $this );

				if ( ! $this->feedWriter instanceof WriterInterface ) {
					throw FeedException::writerNotInitialized( $format );
				}

				break;
		}
	}

	/**
	 * @return FeedSaverInterface
	 */
	public function getFeedSaver() {
		if ( $this->mustBeSavedInBackground() ) {
			return new BackgroundFeedSaver( $this );
		} else {
			return new ForegroundFeedSaver( $this );
		}
	}

	/**
	 * Detect if the product catalog is configured to be saved with background process
	 *
	 * @return bool
	 */
	public function mustBeSavedInBackground() {
		return $this->configuration()->get( Configuration::OPTION_ENABLE_BACKGROUND_SAVE );
	}

	/**
	 * Detect if there are items to save in the feed yet
	 *
	 * @return bool
	 */
	public function there_are_items_to_save() {
		foreach ( $this->get_addon_selected() as $addon ) {
			if ( $addon->there_are_items_to_save( $this ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Mark the product catalog feed in saving
	 */
	public function mark_feed_in_saving() {
		$this->dbProvider->mark_feed_in_saving( $this );
	}

	/**
	 * Mark the product catalog saving as complete
	 */
	public function mark_feed_saving_complete() {
		$this->dbProvider->mark_feed_saving_complete( $this );
	}

	/**
	 * @param \AEPC_Facebook_Adapter $fbApi
	 */
	public function setFbApi( \AEPC_Facebook_Adapter $fbApi ) {
		$this->fbApi = $fbApi;
	}

	/**
	 * Create the product catalog in the Facebook account and also create add the product feed inside with XML associated
	 * @throws \Exception
	 */
	public function push_to_fb() {
		$product_catalog_id = $this->configuration()->get( Configuration::OPTION_FB_PRODUCT_CATALOG_ID );
		$product_feed_id = $this->configuration()->get( Configuration::OPTION_FB_PRODUCT_FEED_ID );
		$product_catalog_name = $this->configuration()->get( Configuration::OPTION_FB_PRODUCT_CATALOG_NAME );
		$product_feed_name = sprintf( 'Automatic product feed from %s', untrailingslashit( preg_replace( '/http(s)?:\/\//', '', home_url() ) ) );
		$schedule_options = array(
			'url' => esc_url( $this->get_url() ),
			'interval' => $this->configuration()->get( Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL ),
			'interval_count' => $this->configuration()->get( Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL_COUNT ),
			'day_of_week' => $this->configuration()->get( Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_DAY_OF_WEEK ),
			'hour' => $this->configuration()->get( Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_HOUR ),
			'minute' => $this->configuration()->get( Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_MINUTE ),
			'timezone' => date_default_timezone_get()
		);

		// Create a new product catalog if any
		if ( empty( $product_catalog_id ) ) {
			$product_catalog_id = $this->fbApi->create_product_catalog( $product_catalog_name );
			$this->configuration()->set( Configuration::OPTION_FB_ACTION, Configuration::VALUE_FB_ACTION_UPDATE );
			$this->configuration()->set( Configuration::OPTION_FB_PRODUCT_CATALOG_ID, $product_catalog_id );
		}

		// Create product feed if any
		if ( empty( $product_feed_id ) || 'new' === $product_feed_id ) {
			$product_feed_id = $this->fbApi->add_product_feed( $product_catalog_id, $product_feed_name, $schedule_options );
			$this->configuration()->set( Configuration::OPTION_FB_PRODUCT_FEED_ID, $product_feed_id );
			$this->configuration()->set( Configuration::OPTION_FB_PRODUCT_FEED_NAME, $product_feed_name );
		}

		// Update schedule options in an existing feed
		else {
			$product_feed_id = $this->fbApi->update_product_feed( $product_feed_id, $schedule_options );
		}

		// Associate pixel to
		$this->fbApi->associate_pixel_to_product_catalog( $product_catalog_id );

		// Save product catalog in the db
		$this->update();
	}

}
