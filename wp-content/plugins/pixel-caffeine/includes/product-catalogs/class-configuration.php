<?php
/**
 * Map the product catalog configuration for the products query from the DB
 */

namespace PixelCaffeine\ProductCatalog;


use PixelCaffeine\ProductCatalog\Entity\ProductCatalog;

class Configuration {

	const OPTION_FEED_NAME = 'name';
	const OPTION_FEED_FORMAT = 'format';
	const OPTION_FEED_CONFIG = 'config';

	const OPTION_ENABLE_BACKGROUND_SAVE = 'enable_background_save';
	const OPTION_GOOGLE_CATEGORY = 'google_category';
	const OPTION_FILE_NAME = 'file_name';
	const OPTION_DIRECTORY_PATH = 'directory_path';
	const OPTION_DIRECTORY_URL = 'directory_url';
	const OPTION_CHUNK_LIMIT = 'chunk_limit';
	const OPTION_SKU_FOR_ID = 'sku_instead_id';
	const OPTION_IMAGE_SIZE = 'image_size';
	const OPTION_SELECTED_ADDON = 'selected_addon';
	const OPTION_FILTER_BY_TYPE = 'filter_by_type';
	const OPTION_FILTER_BY_CATEGORY = 'filter_by_category';
	const OPTION_FILTER_BY_TAG = 'filter_by_tags';
	const OPTION_FILTER_BY_STOCK = 'filter_by_stock';
	const OPTION_FILTER_ON_SALE = 'filter_on_sale';
	const OPTION_NO_VARIATIONS = 'no_variations';
	const OPTION_REFRESH_CYCLE = 'refresh_cycle';
	const OPTION_REFRESH_CYCLE_TYPE = 'refresh_cycle_type';
	const OPTION_PRODUCTS_COUNT = 'products_count';

	const OPTION_MAP_BRAND = 'map_brand_field';
	const OPTION_MAP_CONDITION = 'map_condition_field';
	const OPTION_MAP_DESCRIPTION = 'map_description';
	const OPTION_MAP_PRICE = 'map_price';
	const OPTION_MAP_CUSTOM_LABEL_0 = 'map_custom_label_0_field';
	const OPTION_MAP_CUSTOM_LABEL_1 = 'map_custom_label_1_field';
	const OPTION_MAP_CUSTOM_LABEL_2 = 'map_custom_label_2_field';
	const OPTION_MAP_CUSTOM_LABEL_3 = 'map_custom_label_3_field';
	const OPTION_MAP_CUSTOM_LABEL_4 = 'map_custom_label_4_field';

	const OPTION_FB_ENABLE = 'fb_enable';
	const OPTION_FB_ACTION = 'fb_action';
	const OPTION_FB_PRODUCT_CATALOG_ID = 'fb_product_catalog_id';
	const OPTION_FB_PRODUCT_CATALOG_NAME = 'fb_product_catalog_name';
	const OPTION_FB_PRODUCT_FEED_ID = 'fb_product_feed_id';
	const OPTION_FB_PRODUCT_FEED_NAME = 'fb_product_feed_name';
	const OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL = 'fb_product_feed_schedule_interval';
	const OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL_COUNT = 'fb_product_feed_schedule_interval_count';
	const OPTION_FB_PRODUCT_FEED_SCHEDULE_DAY_OF_MONTH = 'fb_product_feed_schedule_day_of_month';
	const OPTION_FB_PRODUCT_FEED_SCHEDULE_DAY_OF_WEEK = 'fb_product_feed_schedule_day_of_week';
	const OPTION_FB_PRODUCT_FEED_SCHEDULE_HOUR = 'fb_product_feed_schedule_hour';
	const OPTION_FB_PRODUCT_FEED_SCHEDULE_MINUTE = 'fb_product_feed_schedule_minute';

	const VALUE_FB_ACTION_NEW = 'schedule-new';
	const VALUE_FB_ACTION_UPDATE = 'schedule-update';

	/**
	 * @var ProductCatalog
	 */
	protected $entity;

	/**
	 * @var ConfigurationDefaults
	 */
	protected $defaults;

	/**
	 * @var array The raw data (from DB) of the configuration for the specific Product Catalog entity
	 */
	protected $raw_data = array();

	/**
	 * Configuration constructor.
	 *
	 * @param ProductCatalog $entity
	 * @param ConfigurationDefaults $defaults
	 */
	public function __construct( ProductCatalog $entity, ConfigurationDefaults $defaults ) {
		$this->entity = $entity;
		$this->defaults = $defaults;
		$this->raw_data = $this->entity->getConfig();
	}

	/**
	 * Get the value of a specific
	 *
	 * @param $key
	 * @param null $default
	 *
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		if ( isset( $this->raw_data[ $key ] ) ) {
			return $this->raw_data[ $key ];
		}

		if ( $default === null ) {
			$default = $this->defaults->get( $key );
		}

		return $default;
	}

	/**
	 * Set the value in the configuration array
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return $this
	 */
	public function set( $key, $value ) {
		$this->raw_data[ $key ] = $value;
		return $this;
	}

	/**
	 * Remove a key from the configuration
	 *
	 * @param $key
	 *
	 * @return $this
	 */
	public function remove( $key ) {
		if ( isset( $this->raw_data[ $key ] ) ) {
			unset( $this->raw_data[ $key ] );
		}

		return $this;
	}

	/**
	 * Returns the configuration data set as should be saved into the DB
	 *
	 * @return array
	 */
	public function get_configuration_data() {
		return $this->raw_data;
	}

	/**
	 * Check that the optional configuration is changed
	 *
	 * @return bool
	 */
	public function defaults_changed() {
		foreach ( $this->get_configuration_data() as $key => $value ) {
			if ( in_array( $key, array(
				Configuration::OPTION_GOOGLE_CATEGORY
			) ) ) {
				continue;
			}

			$value = $value ?: null;
			$default = $this->defaults->get( $key );
			settype( $value, gettype( $default ) );

			if ( $default !== $value ) {
				return true;
			}
		}

		return false;
	}

}
