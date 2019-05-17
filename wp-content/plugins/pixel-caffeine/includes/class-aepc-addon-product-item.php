<?php

use PixelCaffeine\Interfaces\ECommerceAddOnInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Universal class for the product data from any external addon supported
 *
 * Used in the product feed
 *
 * @class AEPC_Addon_Product_Item
 */
class AEPC_Addon_Product_Item {

	const IN_STOCK = 'in stock';
	const OUT_OF_STOCK = 'out of stock';
	const PREORDER = 'preorder';
	const AVAILABLE_FOR_ORDER = 'available for order';
	const DISCONTINUED = 'discontinued';

	const ON_SALE = 'on sale';
	const NO_SALE = 'no sale';

	/**
	 * @var ECommerceAddOnInterface
	 */
	protected $addon;

	/**
	 * @var array The data arguments of the product item
	 */
	protected $data = array();

	/**
	 * AEPC_Addon_Product_Item constructor.
	 *
	 * @param ECommerceAddOnInterface $addon
	 */
	public function __construct( ECommerceAddOnInterface $addon ) {
		$this->addon = $addon;
	}

	/**
	 * Return the addon instance
	 *
	 * @return ECommerceAddOnInterface
	 */
	public function get_addon() {
		return $this->addon;
	}

	/**
	 * Get the ID
	 *
	 * @return string|int
	 */
	public function get_id() {
		return $this->data['id'];
	}

	/**
	 * Set the ID
	 *
	 * @param string|int $id
	 *
	 * @return self $this
	 */
	public function set_id( $id ) {
		$this->data['id'] = $id;
		return $this;
	}

	/**
	 * Get the SKU
	 *
	 * @return string
	 */
	public function get_sku() {
		return $this->data['sku'];
	}

	/**
	 * Set the ID
	 *
	 * @param string|int $sku
	 *
	 * @return self $this
	 */
	public function set_sku( $sku ) {
		$this->data['sku'] = $sku;
		return $this;
	}

	/**
	 * Get the slug
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->data['slug'];
	}

	/**
	 * Set the slug
	 *
	 * @param string $slug
	 *
	 * @return self $this
	 */
	public function set_slug( $slug ) {
		$this->data['slug'] = $slug;
		return $this;
	}

	/**
	 * Get the permalink
	 *
	 * @return string
	 */
	public function get_permalink() {
		return $this->data['permalink'];
	}

	/**
	 * Set the permalink
	 *
	 * @param string $permalink
	 *
	 * @return self $this
	 */
	public function set_permalink( $permalink ) {
		$this->data['permalink'] = $permalink;
		return $this;
	}

	/**
	 * Get the admin_url
	 *
	 * @return string
	 */
	public function get_admin_url() {
		return $this->data['admin_url'];
	}

	/**
	 * Set the admin_url
	 *
	 * @param string $admin_url
	 *
	 * @return self $this
	 */
	public function set_admin_url( $admin_url ) {
		$this->data['admin_url'] = $admin_url;
		return $this;
	}

	/**
	 * Get the parent_admin_url
	 *
	 * @return string
	 */
	public function get_parent_admin_url() {
		return $this->data['parent_admin_url'];
	}

	/**
	 * Set the parent_admin_url
	 *
	 * @param string $parent_admin_url
	 *
	 * @return self $this
	 */
	public function set_parent_admin_url( $parent_admin_url ) {
		$this->data['parent_admin_url'] = $parent_admin_url;
		return $this;
	}

	/**
	 * Get the title
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->data['title'];
	}

	/**
	 * Set the title
	 *
	 * @param string $title
	 *
	 * @return self $this
	 */
	public function set_title( $title ) {
		$this->data['title'] = $title;
		return $this;
	}

	/**
	 * Get the description
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->data['description'];
	}

	/**
	 * Set the description
	 *
	 * @param string $description
	 *
	 * @return self $this
	 */
	public function set_description( $description ) {
		$this->data['description'] = $description;
		return $this;
	}

	/**
	 * Get the short_description
	 *
	 * @return string
	 */
	public function get_short_description() {
		return $this->data['short_description'];
	}

	/**
	 * Set the short_description
	 *
	 * @param string $short_description
	 *
	 * @return self $this
	 */
	public function set_short_description( $short_description ) {
		$this->data['short_description'] = $short_description;
		return $this;
	}

	/**
	 * Get the link
	 *
	 * @return string
	 */
	public function get_link() {
		return $this->data['link'];
	}

	/**
	 * Set the link
	 *
	 * @param string $link
	 *
	 * @return self $this
	 */
	public function set_link( $link ) {
		$this->data['link'] = $link;
		return $this;
	}

	/**
	 * Get the image URL
	 *
	 * @return string
	 */
	public function get_image_url() {
		return $this->data['image_url'];
	}

	/**
	 * Set the image URL
	 *
	 * @param string $image_url
	 *
	 * @return self $this
	 */
	public function set_image_url( $image_url ) {
		$this->data['image_url'] = $image_url;
		return $this;
	}

	/**
	 * Get the additional image URLs
	 *
	 * @return array
	 */
	public function get_additional_image_urls() {
		return $this->data['additional_image_urls'];
	}

	/**
	 * Set the additional image URLs
	 *
	 * @param array $additional_image_urls
	 *
	 * @return self $this
	 */
	public function set_additional_image_urls( $additional_image_urls ) {
		$this->data['additional_image_urls'] = $additional_image_urls;
		return $this;
	}

	/**
	 * Get the availability
	 *
	 * @return string
	 */
	public function get_availability() {
		return $this->data['availability'];
	}

	/**
	 * Set the availability as in stock
	 *
	 * @return self $this
	 */
	public function set_in_stock() {
		$this->data['availability'] = self::IN_STOCK;
		return $this;
	}

	/**
	 * Set the availability as out of stock
	 *
	 * @return self $this
	 */
	public function set_out_of_stock() {
		$this->data['availability'] = self::OUT_OF_STOCK;
		return $this;
	}

	/**
	 * Set the availability as preorder
	 *
	 * @return self $this
	 */
	public function set_in_preorder() {
		$this->data['availability'] = self::PREORDER;
		return $this;
	}

	/**
	 * Set the availability as available for order
	 *
	 * @return self $this
	 */
	public function set_available_for_order() {
		$this->data['availability'] = self::AVAILABLE_FOR_ORDER;
		return $this;
	}

	/**
	 * Set the availability as discontinued
	 *
	 * @return self $this
	 */
	public function set_discontinued() {
		$this->data['availability'] = self::DISCONTINUED;
		return $this;
	}

	/**
	 * Get the categories as id=>parent
	 *
	 * @return array ID=>parent
	 */
	public function get_categories() {
		return $this->data['categories'];
	}

	/**
	 * Set the categories as id=>parent
	 *
	 * @param array $categories ID=>parent
	 *
	 * @return self $this
	 */
	public function set_categories( $categories ) {
		$this->data['categories'] = $categories;
		return $this;
	}

	/**
	 * Get the google category for the product
	 *
	 * @return array
	 */
	public function get_google_category() {
		return $this->data['google_category'];
	}

	/**
	 * Set the google google_category
	 *
	 * @param array $google_category The structure must be: [ 'id' => 12345, 'category' => 'Animals & Pet Supplies' ]
	 *
	 * @return self $this
	 */
	public function set_google_category( $google_category ) {
		$this->data['google_category'] = $google_category;
		return $this;
	}

	/**
	 * Get the weight value for shipping
	 *
	 * @return string
	 */
	public function get_shipping_weight() {
		return $this->data['shipping_weight'];
	}

	/**
	 * Set the weight value for shipping
	 *
	 * @param string $shipping_weight
	 *
	 * @return self $this
	 */
	public function set_shipping_weight( $shipping_weight ) {
		$this->data['shipping_weight'] = $shipping_weight;
		return $this;
	}

	/**
	 * Get the weight unit for shipping
	 *
	 * @return string
	 */
	public function get_shipping_weight_unit() {
		return $this->data['shipping_weight_unit'];
	}

	/**
	 * Set the weight unit for shipping
	 *
	 * @param string $shipping_weight_unit
	 *
	 * @return self $this
	 */
	public function set_shipping_weight_unit( $shipping_weight_unit ) {
		$this->data['shipping_weight_unit'] = $shipping_weight_unit;
		return $this;
	}

	/**
	 * Get the eventual currency specific for the product
	 *
	 * @return string
	 */
	public function get_currency() {
		return $this->data['currency'];
	}

	/**
	 * Set the eventual currency specific for the product
	 *
	 * @param string $currency
	 *
	 * @return self $this
	 */
	public function set_currency( $currency ) {
		$this->data['currency'] = $currency;
		return $this;
	}

	/**
	 * Get the price
	 *
	 * @return float
	 */
	public function get_price() {
		return $this->data['price'];
	}

	/**
	 * Set the price
	 *
	 * @param float $price
	 *
	 * @return self $this
	 */
	public function set_price( $price ) {
		$this->data['price'] = $price;
		return $this;
	}

	/**
	 * Get the sale_price
	 *
	 * @return string
	 */
	public function get_sale_price() {
		return $this->data['sale_price'];
	}

	/**
	 * Set the sale_price
	 *
	 * @param string $sale_price
	 *
	 * @return self $this
	 */
	public function set_sale_price( $sale_price ) {
		$this->data['sale_price'] = $sale_price;
		return $this;
	}

	/**
	 * Get the sale_price_effective_date
	 *
	 * @return Datetime[]|null
	 */
	public function get_sale_price_effective_date() {
		return isset( $this->data['sale_price_effective_date'] ) ? $this->data['sale_price_effective_date'] : null;
	}

	/**
	 * Set the sale_price_effective_date
	 *
	 * @param Datetime $from
	 * @param Datetime $to
	 *
	 * @return AEPC_Addon_Product_Item $this
	 */
	public function set_sale_price_effective_date( Datetime $from, Datetime $to ) {
		$this->data['sale_price_effective_date'] = array(
			'from' => $from,
			'to' => $to
		);
		return $this;
	}

	/**
	 * Get the tax amount
	 *
	 * @return float
	 */
	public function get_price_tax() {
		return $this->data['price_tax'];
	}

	/**
	 * Set the tax amount
	 *
	 * @param string $price_tax
	 *
	 * @return self $this
	 */
	public function set_price_tax( $price_tax ) {
		$this->data['price_tax'] = $price_tax;
		return $this;
	}

	/**
	 * Get the tax amount
	 *
	 * @return float
	 */
	public function get_sale_price_tax() {
		return $this->data['sale_price_tax'];
	}

	/**
	 * Set the tax amount
	 *
	 * @param string $sale_price_tax
	 *
	 * @return self $this
	 */
	public function set_sale_price_tax( $sale_price_tax ) {
		$this->data['sale_price_tax'] = $sale_price_tax;
		return $this;
	}

	/**
	 * Get if the product needs shipping
	 *
	 * @return bool
	 */
	public function needs_shipping() {
		return $this->data['needs_shipping'];
	}

	/**
	 * Set if the product needs shipping
	 *
	 * @param bool $needs_shipping
	 *
	 * @return self $this
	 */
	public function set_if_needs_shipping( $needs_shipping ) {
		$this->data['needs_shipping'] = $needs_shipping;
		return $this;
	}

	/**
	 * Get if the product is a variation of another product
	 *
	 * @return bool
	 */
	public function is_variation() {
		return $this->data['is_variation'];
	}

	/**
	 * Set if the product is a variation of another product
	 *
	 * @param bool $is_variation
	 *
	 * @return self $this
	 */
	public function set_if_variation( $is_variation ) {
		$this->data['is_variation'] = $is_variation;
		return $this;
	}

	/**
	 * Set the group ID, useful when the product is a variation
	 *
	 * @param string $group_id
	 *
	 * @return self $this
	 */
	public function set_group_id( $group_id ) {
		$this->data['group_id'] = $group_id;
		return $this;
	}

	/**
	 * Get the group ID, useful when the product is a variation
	 *
	 * @return string
	 */
	public function get_group_id() {
		return isset( $this->data['group_id'] ) ? $this->data['group_id'] : null;
	}

	/**
	 * Set the checkout URL where the item may be purchased
	 *
	 * @param string $checkout_url
	 *
	 * @return self $this
	 */
	public function set_checkout_url( $checkout_url ) {
		$this->data['checkout_url'] = $checkout_url;
		return $this;
	}

	/**
	 * Get the checkout URL where the item may be purchased
	 *
	 * @return string
	 */
	public function get_checkout_url() {
		return isset( $this->data['checkout_url'] ) ? $this->data['checkout_url'] : null;
	}

}
