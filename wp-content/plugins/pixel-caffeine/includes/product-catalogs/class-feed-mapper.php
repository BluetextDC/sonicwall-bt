<?php
/**
 * Class mapper between the Product Item object and what Facebook wants as entries
 */

namespace PixelCaffeine\ProductCatalog;


use PixelCaffeine\ProductCatalog\Exception\FeedException;

class FeedMapper {

	/**
	 * Map the availability values from the product item to what facebook wants
	 *
	 * @var array
	 */
	protected $availability_map = array(
		\AEPC_Addon_Product_Item::IN_STOCK => 'in stock',
		\AEPC_Addon_Product_Item::OUT_OF_STOCK => 'out of stock',
		\AEPC_Addon_Product_Item::PREORDER => 'preorder',
		\AEPC_Addon_Product_Item::AVAILABLE_FOR_ORDER => 'available for order',
		\AEPC_Addon_Product_Item::DISCONTINUED => 'discontinued',
	);

	/**
	 * @var \AEPC_Addon_Product_Item
	 */
	protected $item;

	/**
	 * @var Configuration
	 */
	protected $configuration;

	/**
	 * FeedMapper constructor.
	 *
	 * @param \AEPC_Addon_Product_Item $item
	 * @param Configuration $configuration
	 */
	public function __construct( \AEPC_Addon_Product_Item $item, Configuration $configuration ) {
		$this->item = $item;
		$this->configuration = $configuration;
	}

	/**
	 * @return \AEPC_Addon_Product_Item
	 */
	public function get_item() {
		return $this->item;
	}

	/**
	 * Get the ID
	 *
	 * @return string|int
	 */
	public function get_id() {
		$sku = $this->item->get_sku();
		return $this->configuration->get( Configuration::OPTION_SKU_FOR_ID ) && $sku ? $sku : $this->item->get_id();
	}

	/**
	 * Get the item group ID
	 *
	 * @return string|int
	 */
	public function get_item_group_id() {
		return $this->item->get_group_id();
	}

	/**
	 * Get the SKU
	 *
	 * @return string
	 */
	public function get_sku() {
		return $this->item->get_sku();
	}

	/**
	 * Get the slug
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->item->get_slug();
	}

	/**
	 * Get the permalink
	 *
	 * @return string
	 */
	public function get_permalink() {
		return $this->item->get_permalink();
	}

	/**
	 * Get the admin URL
	 *
	 * @return string
	 */
	public function get_admin_url() {
		return $this->item->get_admin_url();
	}

	/**
	 * Get the title
	 *
	 * @return string
	 * @throws FeedException
	 */
	public function get_title() {
		$value = apply_filters( 'aepc_feed_item_title', $this->item->get_title(), $this );

		if ( empty( $value ) ) {
			throw FeedException::mandatoryField( 'title', $this->item );
		}

		return $value;
	}

	/**
	 * Get the description
	 *
	 * @return string
	 * @throws FeedException
	 */
	public function get_description() {
		if ( $this->configuration->get( Configuration::OPTION_MAP_DESCRIPTION ) === 'short-description' ) {
			$description = $this->item->get_short_description();
		} else {
			$description = $this->item->get_description();
		}

		$value = apply_filters( 'aepc_feed_item_description', $description, $this );

		if ( empty( $value ) ) {
			throw FeedException::mandatoryField( 'description', $this->item );
		}

		return $value;
	}

	/**
	 * Get the short description
	 *
	 * @return string
	 */
	public function get_short_description() {
		return $this->item->get_short_description();
	}

	/**
	 * Get the link
	 *
	 * @return string
	 */
	public function get_link() {
		return $this->item->get_link();
	}

	/**
	 * Get the image URL
	 *
	 * @return string
	 * @throws FeedException
	 */
	public function get_image_url() {
		$image_link = $this->item->get_image_url();
		$image_link = set_url_scheme( $image_link );

		// Force absolute
		if ( preg_match( '/^\/?wp-content\/(.*)$/', $image_link, $match ) ) {
			$image_link = content_url( $match[1] );
		} else if ( preg_match( '/^\/([^\/].*)/', $image_link, $match ) ) {
			$image_link = home_url( $match[1] );
		}

		$value = apply_filters( 'aepc_feed_item_image_link', $image_link, $this );

		if ( empty( $value ) ) {
			throw FeedException::mandatoryField( 'image_link', $this->item );
		}

		return $value;
	}

	/**
	 * Get the additional image URLs (facebook supports max of 10 URLs)
	 *
	 * @return array
	 */
	public function get_additional_image_urls() {
		return implode( ', ', array_slice( $this->item->get_additional_image_urls(), 0, 10 ) );
	}

	/**
	 * Get the availability
	 *
	 * @return string
	 */
	public function get_availability() {
		return $this->availability_map[ $this->item->get_availability() ];
	}

	/**
	 * Get the categories as id=>parent
	 *
	 * @return string
	 */
	public function get_categories() {
		return $this->get_stringified_categories( $this->item->get_categories() );
	}

	/**
	 * Get the categories as id=>parent
	 *
	 * @return string
	 * @throws FeedException
	 */
	public function get_google_category() {
		$cat = apply_filters( 'aepc_feed_item_google_category', $this->item->get_google_category(), $this );

		if ( empty( $cat ) ) {
			$cat = $this->configuration->get( Configuration::OPTION_GOOGLE_CATEGORY );
		}

		if ( empty( $cat ) ) {
			throw FeedException::googleCategoryMandatory( $this->item );
		}

		return implode( ' > ', $cat );
	}

	/**
	 * Get the weight for the shipping
	 *
	 * @return string
	 * @throws FeedException
	 */
	public function get_shipping_weight() {
		$weight = $this->item->get_shipping_weight();
		$weight_unit = $this->item->get_shipping_weight_unit();

		// Adjust 'lbs' unit to 'lb' as Facebook wants
		$weight_unit = str_replace( 'lbs', 'lb', $weight_unit );

		if ( empty( $weight ) ) {
			return null;
		}

		if ( ! in_array( $weight_unit, array( 'lb', 'oz', 'g', 'kg' ) ) ) {
			throw FeedException::weightUnitNotSupported( $weight_unit, $this->item );
		}

		return $weight . ' ' . $weight_unit;
	}

	/**
	 * Get the eventual currency specific for the product
	 *
	 * @return string
	 */
	public function get_currency() {
		return $this->item->get_currency();
	}

	/**
	 * Get the price
	 *
	 * @return string
	 */
	public function get_price() {
		$price = $this->item->get_price();
		$price_tax = $this->item->get_price_tax();

		if ( $this->configuration->get( Configuration::OPTION_MAP_PRICE ) === 'price-including-tax' ) {
			$price += $price_tax;
		}

		return $this->esc_price( $price );
	}

	/**
	 * Get the sale price
	 *
	 * @return string
	 */
	public function get_sale_price() {
		$sale_price = $this->item->get_sale_price();

		if ( $sale_price > 0 ) {
			$sale_price_tax = $this->item->get_sale_price_tax();

			if ( $this->configuration->get( Configuration::OPTION_MAP_PRICE ) === 'price-including-tax' ) {
				$sale_price += $sale_price_tax;
			}

			return $this->esc_price( $sale_price );
		}

		return null;
	}

	/**
	 * Get the effective sale price date
	 *
	 * @return string
	 */
	public function get_sale_price_effective_date() {
		$date = $this->item->get_sale_price_effective_date();

		if ( ! isset( $date['from'] ) || ! isset( $date['to'] ) ) {
			return null;
		}

		return $date['from']->format(\Datetime::ISO8601 ) . '/' . $date['to']->format(\Datetime::ISO8601 );
	}

	/**
	 * Return store name with sanitized apostrophe
	 *
	 * @return string
	 */
	public function get_brand() {
		return $this->configuration->get( Configuration::OPTION_MAP_BRAND );
	}

	/**
	 * Return store name with sanitized apostrophe
	 *
	 * @return string
	 */
	public function get_condition() {
		return $this->configuration->get( Configuration::OPTION_MAP_CONDITION );
	}

	/**
	 * Returns the checkout URL where the item can be purchased
	 *
	 * @return string
	 */
	public function get_checkout_url() {
		return $this->item->get_checkout_url();
	}

	/**
	 * Return the custom_label_0 value
	 *
	 * @return string
	 */
	public function get_custom_label_0() {
		return $this->configuration->get( Configuration::OPTION_MAP_CUSTOM_LABEL_0 );
	}

	/**
	 * Return the custom_label_1 value
	 *
	 * @return string
	 */
	public function get_custom_label_1() {
		return $this->configuration->get( Configuration::OPTION_MAP_CUSTOM_LABEL_1 );
	}

	/**
	 * Return the custom_label_2 value
	 *
	 * @return string
	 */
	public function get_custom_label_2() {
		return $this->configuration->get( Configuration::OPTION_MAP_CUSTOM_LABEL_2 );
	}

	/**
	 * Return the custom_label_3 value
	 *
	 * @return string
	 */
	public function get_custom_label_3() {
		return $this->configuration->get( Configuration::OPTION_MAP_CUSTOM_LABEL_3 );
	}

	/**
	 * Return the custom_label_4 value
	 *
	 * @return string
	 */
	public function get_custom_label_4() {
		return $this->configuration->get( Configuration::OPTION_MAP_CUSTOM_LABEL_4 );
	}

	/**
	 * Return categories list as Apparel & Accessories > Clothing > Dresses
	 *
	 * @param array $terms id=>parent
	 *
	 * @return string
	 */
	protected function get_stringified_categories( $terms ) {
		foreach ( $terms as $term_id => &$parent ) {
			if ( empty( $parent ) ) {
				$parent = get_term( $term_id )->name;
			} else {
				$parent = get_term( $parent )->name . ' > ' . get_term( $term_id )->name;
			}
		}

		return implode( ', ', array_values( $terms ) );
	}

	/**
	 * Returns the correct price format, with currency appended
	 *
	 * @param $price
	 *
	 * @return string
	 */
	protected function esc_price( $price ) {
		return floatval( $price ) . ' ' . $this->get_currency();
	}

}
