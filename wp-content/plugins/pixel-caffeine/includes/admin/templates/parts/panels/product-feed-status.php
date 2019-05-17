<?php
/**
 * General admin settings page
 *
 * This is the HTML of the panel for product feed status
 *
 * @var AEPC_Admin_View $page
 * @var string $action
 * @var ProductCatalogManager $product_catalog
 *
 * @package Pixel Caffeine
 */

use PixelCaffeine\ProductCatalog\ProductCatalogManager;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

// This flag could be passed in the template to forcing to show the updating status (used in the ajax call)
if ( ! isset( $force_updating ) ) {
	$force_updating = false;
}

// This flag could be passed in the template to forcing to show the refreshing status (used in the ajax call)
if ( ! isset( $force_refreshing ) ) {
	$force_refreshing = false;
}

if ( empty( $product_catalog ) ) {
	$page->get_template_part( 'panels/product-feed/new', array(
		'product_catalog' => null
	) );

} elseif ( $force_updating ) {
	$page->get_template_part( 'panels/product-feed/saving', array(
		'product_catalog' => $product_catalog
	) );

} else {
	$page->get_template_part( 'panels/product-feed/created', array(
		'product_catalog' => $product_catalog,
		'is_refreshing' => $force_refreshing || AEPC_Admin::$product_catalogs_service->is_feed_saving( $product_catalog )
	) );
}
