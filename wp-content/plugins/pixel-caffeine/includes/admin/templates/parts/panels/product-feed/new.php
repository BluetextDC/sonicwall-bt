<?php
/**
 * @var AEPC_Admin_View $page
 * @var null $product_catalog
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

$action = 'save';

?>

<!-- Subheader -->
<div class="jumbotron intro-dashboard intro-dude dude-prd-catalog intro-product-catalog upgraded-product-catalog">
	<div class="jumbotron-body">
		<h2 class="tit"><?php _e( 'Create the product catalog!', 'pixel-caffeine' ) ?></h2>
		<p class="text"><?php printf( __( 'The Product Catalog is a must have for anyone in eCommerce! It lets you create %sDynamic Product Ads%s on Facebook!', 'pixel-caffeine' ), '<a href="https://adespresso.com/blog/facebook-dynamic-product-ads/" target="_blank">', '</a>' ) ?></p>
		<p class="text"><?php _e( 'In just a few words you can automatically promote all of the products in your store (or just some of them!) to new potential customers or to visitors who checked out a specific product but didn\'t buy it. With Pixel Caffeine, you can create your product catalog with just <strong>one click</strong> and have it constantly updated with the latest products, prices, and availability!', 'pixel-caffeine' ) ?></p>
	</div>
</div>

<div class="panel panel-prd-catalog form-horizontal js-product-feed-info<?php echo ! AEPC_Admin::$product_catalogs_service->is_product_catalog_enabled() ? ' disabled-box' : '' ?>">
	<div class="panel-heading">
		<h2 class="tit"><?php _e( 'Generate Product Feed', 'pixel-caffeine' ) ?></h2>
	</div>
	<div class="panel-body">
		<?php $page->get_form_fields( 'product-catalog', array(
			'action' => $action,
			'product_catalog' => $product_catalog
		) ) ?>
	</div>
</div>
