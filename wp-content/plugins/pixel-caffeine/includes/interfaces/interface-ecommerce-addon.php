<?php

namespace PixelCaffeine\Interfaces;


use AEPC_Addon_Product_Item;
use PixelCaffeine\ProductCatalog\Admin\Metaboxes;
use PixelCaffeine\ProductCatalog\ProductCatalogManager;

interface ECommerceAddOnInterface {

	/**
	 * Returns the checkout URL where the items may be purcahsed
	 *
	 * @return string
	 */
	public function get_checkout_url();

	/**
	 * Returns the array of all term objects id=>name for all categories of the shop
	 *
	 * @return array
	 */
	public function get_product_categories();

	/**
	 * Returns the array of all term objects id=>name for all tags of the shop
	 *
	 * @return array
	 */
	public function get_product_tags();

	/**
	 * Return the array with all AEPC_Addon_Product_item instances for the products to include inside the XML feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 * @param Metaboxes $metaboxes
	 *
	 * @return AEPC_Addon_Product_Item[]
	 */
	public function get_feed_entries( ProductCatalogManager $product_catalog, Metaboxes $metaboxes );

	/**
	 * Get the feed entries to save into the feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 * @param Metaboxes $metaboxes
	 *
	 * @return AEPC_Addon_Product_item[]
	 */
	public function get_feed_entries_to_save( ProductCatalogManager $product_catalog, Metaboxes $metaboxes );

	/**
	 * Get the feed entries to edit in the feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 * @param Metaboxes $metaboxes
	 *
	 * @return AEPC_Addon_Product_item[]
	 */
	public function get_feed_entries_to_edit( ProductCatalogManager $product_catalog, Metaboxes $metaboxes );

	/**
	 * Save a meta in the product post that set the product as saved in the product feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 * @param \AEPC_Addon_Product_Item $item
	 */
	public function set_product_saved_in_feed( ProductCatalogManager $product_catalog, AEPC_Addon_Product_Item $item );

	/**
	 * Save the meta in the product post that set the product as edited in the product feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 * @param \AEPC_Addon_Product_Item $item
	 */
	public function set_product_edited_in_feed( ProductCatalogManager $product_catalog, AEPC_Addon_Product_Item $item );

	/**
	 * Delete the meta in the product post that set the product as saved in the product feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 * @param \AEPC_Addon_Product_Item $item
	 */
	public function set_product_not_saved_in_feed( ProductCatalogManager $product_catalog, AEPC_Addon_Product_Item $item );

	/**
	 * Perform a global delete in one query ideally for all feed status associated to the product catalog
	 *
	 * @param ProductCatalogManager $product_catalog
	 */
	public function remove_all_feed_status( ProductCatalogManager $product_catalog );

	/**
	 * Detect if there are items marked as not saved in the feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 *
	 * @return bool
	 */
	public function there_are_items_to_save( ProductCatalogManager $product_catalog );

}
