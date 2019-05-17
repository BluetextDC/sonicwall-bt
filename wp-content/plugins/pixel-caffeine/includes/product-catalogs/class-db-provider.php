<?php
/**
 * Connect to the DB
 */

namespace PixelCaffeine\ProductCatalog;

use PixelCaffeine\ProductCatalog\Entity\ProductCatalog as Entity;
use PixelCaffeine\ProductCatalog\Exception\EntityException;

class DbProvider {

	const OPTION_NAME = 'aepc_product_catalogs';

	const FEED_STATUS_SAVED = 'SAVED';
	const FEED_STATUS_EDITED = 'EDITED';

	const ID_FIELD = 'id';
	const FORMAT_FIELD = 'format';
	const CONFIG_FIELD = 'config';
	const PRODUCTS_COUNT_FIELD = 'products_count';
	const LAST_UPDATE_DATE = 'last_update_date';
	const LAST_ERROR_MESSAGE = 'last_error_message';

	/**
	 * Get all product catalogs from the DB
	 *
	 * @return array
	 */
	public function get_raw_data() {
		return get_option( self::OPTION_NAME, array() );
	}

	/**
	 * Get all product catalogs from the DB
	 *
	 * @return Entity[]
	 */
	public function get_product_catalogs() {
		return array_map( array( $this, 'map_entity_data' ), $this->get_raw_data() );
	}

	/**
	 * Get a record from the DB by the specified ID
	 *
	 * @param $id
	 *
	 * @return Entity
	 * @throws EntityException
	 */
	public function get_product_catalog( $id ) {
		$product_catalogs = $this->get_raw_data();

		if ( ! isset( $product_catalogs[ $id ] ) ) {
			throw EntityException::doesNotExist( $id );
		}

		return $this->map_entity_data( $product_catalogs[ $id ] );
	}

	/**
	 * Add a new product catalog into Database
	 *
	 * @param Entity $entity
	 *
	 * @throws EntityException
	 */
	public function create_product_catalog( Entity $entity ) {
		if ( $this->is_product_catalog_exists( $entity ) ) {
			throw EntityException::isAlreadyExisting( $entity );
		}

		$id = $entity->getId();
		$date = new \DateTime();

		if ( empty( $id ) ) {
			throw EntityException::nameIsEmpty();
		}

		update_option( self::OPTION_NAME, array_merge( $this->get_raw_data(), array(
			$entity->getId() => array(
				self::ID_FIELD => $entity->getId(),
				self::FORMAT_FIELD => $entity->getFormat(),
				self::CONFIG_FIELD => $entity->getConfig(),
				self::PRODUCTS_COUNT_FIELD => $entity->getProductsCount(),
				self::LAST_UPDATE_DATE => $date->format( \DateTime::ISO8601 ),
				self::LAST_ERROR_MESSAGE => $entity->getLastErrorMessage()
			)
		) ) );
	}

	/**
	 * @param Entity $entity
	 *
	 * @throws EntityException
	 */
	public function update_product_catalog( Entity $entity ) {
		if ( ! $this->is_product_catalog_exists( $entity ) ) {
			throw EntityException::doesNotExist( $entity->getId() );
		}

		$product_catalogs = $this->get_raw_data();
		$product_catalogs[ $entity->getId() ] = array(
			self::ID_FIELD => $entity->getId(),
			self::FORMAT_FIELD => $entity->getFormat(),
			self::CONFIG_FIELD => $entity->getConfig(),
			self::PRODUCTS_COUNT_FIELD => $entity->getProductsCount(),
			self::LAST_UPDATE_DATE => $entity->getLastUpdateDate()->format( \DateTime::ISO8601 ),
			self::LAST_ERROR_MESSAGE => $entity->getLastErrorMessage()
		);

		update_option( self::OPTION_NAME, $product_catalogs );
	}

	/**
	 * Delete an Entity from DB
	 *
	 * @param Entity $entity
	 *
	 * @throws EntityException
	 */
	public function delete_product_catalog( Entity $entity ) {
		if ( ! $this->is_product_catalog_exists( $entity ) ) {
			throw EntityException::doesNotExist( $entity->getId() );
		}

		$product_catalogs = $this->get_raw_data();
		unset( $product_catalogs[ $entity->getId() ] );

		update_option( self::OPTION_NAME, $product_catalogs );
	}

	/**
	 * Detect if the product catalog is already created with the same name
	 *
	 * @param Entity $entity
	 *
	 * @return bool
	 */
	public function is_product_catalog_exists( Entity $entity ) {
		return in_array( $entity->getId(), array_keys( $this->get_raw_data() ) );
	}

	/**
	 * Map the raw data from DB into Entity
	 *
	 * @param array $data
	 *
	 * @return Entity
	 */
	protected function map_entity_data( array $data ) {
		$entity = new Entity();

		$data = wp_parse_args( $data, array(
			self::PRODUCTS_COUNT_FIELD => 0,
			self::LAST_ERROR_MESSAGE => ''
		) );

		// Set data
		$entity->setId( $data[ self::ID_FIELD ] );
		$entity->setFormat( $data[ self::FORMAT_FIELD ] );
		$entity->setConfig( $data[ self::CONFIG_FIELD ] );
		$entity->setProductsCount( $data[ self::PRODUCTS_COUNT_FIELD ] );
		$entity->setLastUpdateDate( new \DateTime( $data[ self::LAST_UPDATE_DATE ] ) );
		$entity->setLastErrorMessage( $data[ self::LAST_ERROR_MESSAGE ] );

		return $entity;
	}

	/**
	 * Set as saved all products from the items of the current chunk
	 *
	 * This method will be called after the XML is saved
	 *
	 * @param FeedMapper[] $items
	 * @param ProductCatalogManager $product_catalog
	 */
	public function set_items_saved_in_feed( $items, ProductCatalogManager $product_catalog ) {
		foreach ( $items as $entry ) {
			$addon = $entry->get_item()->get_addon();
			$addon->set_product_saved_in_feed( $product_catalog, $entry->get_item() );
		}
	}

	/**
	 * Returns the transient key of the feed status flag
	 *
	 * @param ProductCatalogManager $product_catalog
	 *
	 * @return string
	 */
	protected function feed_saving_status_transdient_key( ProductCatalogManager $product_catalog ) {
		return $product_catalog->get_entity()->getId() . '_saving';
	}

	/**
	 * Mark the product catalog feed in saving
	 *
	 * @param ProductCatalogManager $product_catalog
	 */
	public function mark_feed_in_saving( ProductCatalogManager $product_catalog ) {
		set_transient( $this->feed_saving_status_transdient_key( $product_catalog ), true );
	}

	/**
	 * Mark the product catalog feed saving as complete
	 *
	 * @param ProductCatalogManager $product_catalog
	 */
	public function mark_feed_saving_complete( ProductCatalogManager $product_catalog ) {
		delete_transient( $this->feed_saving_status_transdient_key( $product_catalog ) );
	}

	/**
	 * Detect if the product feed is saving in background
	 *
	 * @param ProductCatalogManager $product_catalog
	 *
	 * @return bool
	 */
	public function is_feed_saving( ProductCatalogManager $product_catalog ) {
		return get_transient( $this->feed_saving_status_transdient_key( $product_catalog ) );
	}

}
