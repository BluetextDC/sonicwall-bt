<?php

use PixelCaffeine\Interfaces\ECommerceAddOnInterface;
use PixelCaffeine\ProductCatalog\Admin\Metaboxes;
use PixelCaffeine\ProductCatalog\Configuration;
use PixelCaffeine\ProductCatalog\DbProvider;
use PixelCaffeine\ProductCatalog\ProductCatalogManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @class AEPC_Woocommerce_Addon_Support
 */
class AEPC_Woocommerce_Addon_Support extends AEPC_Addon_Factory implements ECommerceAddOnInterface {

	const FEED_STATUS_META = '_product_feed_status';
	const ALREADY_TRACKED_POSTMETA = '_aepc_puchase_tracked';

	/**
	 * The slug of addon, useful to identify some common resources
	 *
	 * @var string
	 */
	protected $addon_slug = 'woocommerce';

	/**
	 * Store the name of addon. It doesn't need a translate.
	 *
	 * @var string
	 */
	protected $addon_name = 'WooCommerce';

	/**
	 * Store the main file of rthe plugin
	 *
	 * @var string
	 */
	protected $main_file = 'woocommerce/woocommerce.php';

	/**
	 * Store the URL of plugin website
	 *
	 * @var string
	 */
	protected $website_url = 'https://wordpress.org/plugins/woocommerce/';

	/**
	 * List of standard events supported for pixel firing by PHP (it's not included the events managed by JS)
	 *
	 * @var array
	 */
	protected $events_support = array( 'ViewContent', 'AddToCart', 'Purchase', 'InitiateCheckout', 'AddPaymentInfo', 'CompleteRegistration' );

	/**
	 * Temporary save the product catalog for the current query, needed for the woocommerce filters
	 *
	 * @var ProductCatalogManager
	 */
	private $_current_query_product_catalog = null;

	/**
	 * Save temporary the product query in order to access to special parameters (like feed status key)
	 * from the WP_Query filter
	 *
	 * @var array
	 */
	private $_current_query = null;

	/**
	 * Method where set all necessary hooks launched from 'init' action
	 */
	public function setup() {
		// Hooks when pixel is enabled.
		if ( version_compare( WC()->version, '3.3', '<' ) ) {
			add_filter( 'woocommerce_params', array( $this, 'add_currency_param' ) );
		} else {
			add_filter( 'woocommerce_get_script_data', array( $this, 'add_currency_param' ), 10, 2 );
		}
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_content_category_meta' ), 99 );
		add_action( 'woocommerce_registration_redirect', array( $this, 'save_registration_data' ), 5 );
		add_action( 'wp_footer', array( $this, 'register_add_to_cart_params' ), 10 );
		add_action( 'wp_footer', array( $this, 'register_add_payment_info_params' ), 10 );
	}

	/**
	 * Check if the plugin is active by checking the main function is existing
	 *
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'WC' );
	}

	/**
	 * Check if we are in a place to fire the ViewContent event
	 *
	 * @return bool
	 */
	protected function can_fire_view_content() {
		return is_product();
	}

	/**
	 * Check if we are in a place to fire the AddToCart event
	 *
	 * @return bool
	 */
	protected function can_fire_add_to_cart() {
		return false; // AddToCart is entirely managed by JS because WooCommerce applies some redirects after add to cart action
	}

	/**
	 * Check if we are in a place to fire the InitiateCheckout event
	 *
	 * @return bool
	 */
	protected function can_fire_initiate_checkout() {
		return is_checkout() && ! is_order_received_page();
	}

	/**
	 * Check if we are in a place to fire the Purchase event
	 *
	 * @return bool
	 */
	protected function can_fire_purchase() {
		if ( ! is_order_received_page() ) {
			return false;
		}

		global $wp;
		$order_id = ! empty( $wp->query_vars['order-received'] ) ? intval( $wp->query_vars['order-received'] ) : intval( $_GET['order-received'] );

		return !get_post_meta($order_id, self::ALREADY_TRACKED_POSTMETA, true);
	}

	/**
	 * Check if we are in a place to fire the CompleteRegistration event
	 *
	 * @return bool
	 */
	protected function can_fire_complete_registration() {
		return get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' && false !== WC()->session->get( 'aepc_complete_registration_data', false );
	}

	/**
	 * This is an alternative method that register the add to cart parameters in a JS variable
	 *
	 * Because of AddToCart is managed by JS, we pass all product parameters to JS with all info of products. If the
	 * product is variable there are also info of all variations
	 */
	public function register_add_to_cart_params() {
		global $post;

		if ( is_product() ) {
			$product = wc_get_product();
		} elseif ( ! empty( $post->post_content ) && preg_match( '/\[product_page id=["]?([0-9]+)/', $post->post_content, $matches ) ) {
			$product = wc_get_product( get_post( intval( $matches[1] ) ) );
		} else {
			$product = null;
		}

		if ( empty( $product ) ) {
			return;
		}

		$args = array();

		$product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
		$args[ $product_id ] = AEPC_Track::check_event_parameters( 'AddToCart', array(
			'content_type' => 'product',
			'content_ids'  => array( $this->maybe_sku( $product_id ) ),
			'content_category'  => AEPC_Pixel_Scripts::content_category_list( $product_id ),
			'value' => floatval( $product->get_price() ),
			'currency' => get_woocommerce_currency()
		) );

		foreach ( $product->get_children() as $child_id ) {
			$variation = wc_get_product( $child_id );
			if (empty($variation)) {
				continue;
			}
			$variation_id = method_exists( $variation, 'get_id' ) ? $variation->get_id() : $variation->id;
			$args[ $variation_id ] = AEPC_Track::check_event_parameters( 'AddToCart', array(
				'content_type' => 'product',
				'content_ids'  => array( $this->maybe_sku( $variation_id ) ),
				'content_category'  => AEPC_Pixel_Scripts::content_category_list( $product_id ),
				'value' => floatval( $variation->get_price() ),
				'currency' => get_woocommerce_currency()
			) );
		}

		wp_localize_script( 'aepc-pixel-events', 'aepc_wc_add_to_cart', $args );
	}

	/**
	 * Register the AddPaymentInfo parameters fired by JS when the checkout is submitted
	 */
	public function register_add_payment_info_params() {
		if ( ! is_checkout() ) {
			return;
		}

		$args = AEPC_Track::check_event_parameters( 'AddPaymentInfo', $this->get_initiate_checkout_params() );
		wp_localize_script( 'aepc-pixel-events', 'aepc_add_payment_info_params', $args );
	}

	/**
	 * Get product info from single page for ViewContent event
	 *
	 * @return array
	 */
	protected function get_view_content_params() {
		$product = wc_get_product();
		$product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;

		if ( $product->is_type( 'variable' ) && AEPC_Track::can_use_product_group() ) {
			$children_id = method_exists( $product, 'get_visible_children' ) ? $product->get_visible_children() : $product->get_children('visible');

			foreach ( $children_id as &$child_id ) {
				$child_id = $this->maybe_sku( $child_id );
			}

			$params = array(
				'content_type' => 'product_group',
				'content_ids'  => $children_id,
			);

		} else {
			$params = array(
				'content_type' => 'product',
				'content_ids'  => array( $this->maybe_sku( $product_id ) ),
			);
		}

		$params['content_name'] = $this->get_product_name( $product );
		$params['content_category']  = AEPC_Pixel_Scripts::content_category_list( $product_id );
		$params['value'] = floatval( $product->get_price() );
		$params['currency'] = get_woocommerce_currency();

		return $params;
	}

	/**
	 * Get info from checkout page for InitiateCheckout event
	 *
	 * @return array
	 */
	protected function get_initiate_checkout_params() {
		$product_ids = array();
		$num_items = 0;

		foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];
			$product_ids[] = $this->maybe_sku( method_exists( $_product, 'get_id' ) ? $_product->get_id() : $_product->id );
			$num_items += $values['quantity'];
		}

		// Order value
		$cart_total = WC()->cart->total;

		// Remove shipping costs
		if ( ! AEPC_Track::can_track_shipping_costs() ) {
			$cart_total -= WC()->cart->shipping_total;
		}

		return array(
			'content_type' => 'product',
			'content_ids' => $product_ids,
			'num_items' => $num_items,
			'value' => $cart_total,
			'currency' => get_woocommerce_currency()
		);
	}

	/**
	 * Get product info from purchase succeeded page for Purchase event
	 *
	 * @return array
	 */
	protected function get_purchase_params() {
		global $wp;

		$product_ids = array();
		$order = wc_get_order( ! empty( $wp->query_vars['order-received'] ) ? intval( $wp->query_vars['order-received'] ) : intval( $_GET['order-received'] ) );

		if ( empty( $order ) ) {
			return array();
		}

		foreach ( $order->get_items() as $item_key => $item ) {
			$_product = is_object( $item ) ? $item->get_product() : $order->get_product_from_item( $item );
			$_product_id = method_exists( $_product, 'get_id' ) ? $_product->get_id() : $_product->id;

			if ( ! empty( $_product ) ) {
				$product_ids[] = $this->maybe_sku( $_product_id );
			} else {
				$product_ids[] = $item['product_id'];
			}
		}

		// Order value
		$order_value = $order->get_total();

		// Remove shipping costs
		if ( ! AEPC_Track::can_track_shipping_costs() ) {
			$order_value -= method_exists( $order, 'get_shipping_total' ) ? $order->get_shipping_total() : $order->get_total_shipping();
		}

		add_post_meta($order->get_id(), self::ALREADY_TRACKED_POSTMETA, true);

		return array(
			'content_ids' => $product_ids,
			'content_type' => 'product',
			'value' => $order_value,
			'currency' => method_exists( $order, 'get_currency' ) ? $order->get_currency() : $order->get_order_currency()
		);
	}

	/**
	 * Save CompleteRegistration data event in session, becase of redirect after woo registration
	 */
	public function save_registration_data( $redirect ) {
	    if ( ! AEPC_Track::is_completeregistration_active() ) {
	        return $redirect;
        }

		$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
		WC()->session = new $session_class();
		WC()->session->set( 'aepc_complete_registration_data', apply_filters( 'aepc_complete_registration', array() ) );

		// I had to hook into the filter for decide what URL use for redirect after registration. I need to pass it.
		return $redirect;
	}

	/**
	 * Get info from when a registration form is completed, such as signup for a service, for CompleteRegistration event
	 *
	 * @return array
	 */
	protected function get_complete_registration_params() {
		$params = WC()->session->get( 'aepc_complete_registration_data', false );

		// Delete session key
		unset( WC()->session->aepc_complete_registration_data );

		return $params;
	}

	/**
	 * Add currency value on params list on woocommerce localize
	 *
	 * @param $data
	 * @param string $handle
	 *
	 * @return array
	 */
	public function add_currency_param( $data, $handle = 'woocommerce' ) {
		if (
			$handle != 'woocommerce'
			|| ! function_exists('get_woocommerce_currency')
			|| ! PixelCaffeine()->is_pixel_enabled()
		) {
			return $data;
		}

		return array_merge( $data, array(
			'currency' => get_woocommerce_currency()
		) );
	}

	/**
	 * Add a meta info inside each product of loop, to have content_category for each product
	 */
	public function add_content_category_meta() {
	    if ( is_admin() || ! PixelCaffeine()->is_pixel_enabled() ) {
	    	// is_admin is necessary in order to avoid that this function is called by admin pages from some extension
	        return;
        }

		$product = wc_get_product();
		$product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
		?><span data-content_category="<?php echo esc_attr( wp_json_encode( AEPC_Pixel_Scripts::content_category_list( $product_id ) ) ) ?>" style="display:none;"></span><?php
	}

	/**
	 * Get the info about the customer
	 *
	 * @return array
	 */
	public function get_customer_info() {
		$user = wp_get_current_user();

		return array(
			'ph' => $user->billing_phone,
			'ct' => $user->billing_city,
			'st' => $user->billing_state,
			'zp' => $user->billing_postcode
		);
	}

	/**
	 * Returns SKU if exists, otherwise the product ID
	 *
	 * @return string|int
	 */
	protected function maybe_sku( $product_id ) {
		if ( AEPC_Track::can_use_sku() && $sku = get_post_meta( $product_id, '_sku', true ) ) {
			return (string) $sku;
		}

		return (string) $product_id;
	}

	/**
	 * Retrieve the product name
	 *
	 * @param int|WC_Product $product The ID of product or the product woo object where get its name.
	 *
	 * @return string
	 */
	public function get_product_name( $product ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		return $product->get_title();
	}

	/**
	 * Says if the product is of addon type
	 *
	 * @param int $product_id The product ID.
	 *
	 * @return bool
	 */
	public function is_product_of_this_addon( $product_id ) {
		return 'product' === get_post_type( $product_id );
	}

	/**
	 * Returns the types supported by this plugin
	 *
	 * @return array
	 */
	public function get_product_types() {
		return wc_get_product_types();
	}

	/**
	 * Returns the checkout URL where the items may be purcahsed
	 *
	 * @return string
	 */
	public function get_checkout_url() {
		return function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : wc_get_page_permalink('checkout');
	}

	/**
	 * Helper method to get the description from a product by checking first description and then short one if the full
	 * one is empty
	 *
	 * @param $product
	 *
	 * @return string
	 */
	protected function get_description_from_product( $product ) {
		$product_description = method_exists( $product, 'get_description' ) ? $product->get_description() : $product->post->post_content;

		// Get excerpt if description is empty
		if ( empty( $product_description ) ) {
			$product_description = method_exists( $product, 'get_short_description' ) ? $product->get_short_description() : '';;
		}

		return $product_description;
	}

	/**
	 * Return the AEPC_Addon_Product_item instance for the product
	 *
	 * @param WC_Product|WC_Product_Simple|WC_Product_Variable|WC_Product_Variation $product
	 * @param Metaboxes $metaboxes
	 * @param Configuration $configuration
	 *
	 * @return AEPC_Addon_Product_item
	 */
	public function get_product_item( $product, Metaboxes $metaboxes, Configuration $configuration ) {
		$product_item = new AEPC_Addon_Product_Item( $this );
		preg_match( '/src="([^"]+)"/', $product->get_image( $configuration->get( Configuration::OPTION_IMAGE_SIZE ) ), $image_parts );

		// Backwards helper variables
		$product_is_variation = $product->is_type( 'variation' );
		$product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : ( $product_is_variation ? $product->variation_id : $product->id );
		$product_slug = method_exists( $product, 'get_slug' ) ? $product->get_slug() : $product->post->post_name;
		$product_description = $this->get_description_from_product( $product );
		$product_short_description = method_exists( $product, 'get_short_description' ) ? $product->get_short_description() : '';
		$product_additional_image_ids = array_map( 'wp_get_attachment_url', method_exists( $product, 'get_gallery_image_ids' ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids() );
		$product_parent_id = method_exists( $product, 'get_parent_id' ) ? $product->get_parent_id() : ( isset( $product->parent->id ) ? $product->parent->id : 0 );
		$product_image_link = isset( $image_parts[1] ) ? $image_parts[1] : null;
		$product_price = floatval( $product->is_type('variable') ? $product->get_variation_price() : $product->get_regular_price() );
		$product_sale_price = floatval( $product->get_sale_price() );

		if ( wc_prices_include_tax() ) {
			$product_price_tax      = $product_price;
			$product_sale_price_tax = $product_sale_price;
			$product_price      = wc_get_price_excluding_tax($product, ['price' => $product_price]);
			$product_sale_price = wc_get_price_excluding_tax($product, ['price' => $product_sale_price]);
			$product_price_tax      -= $product_price;
			$product_sale_price_tax -= $product_sale_price;
		} else {
			$product_price_tax      = wc_get_price_including_tax($product, ['price' => $product_price]) - $product_price;
			$product_sale_price_tax = wc_get_price_including_tax($product, ['price' => $product_sale_price]) - $product_sale_price;
		}

		// If variation description is empty get it from parent
		if ( $product_is_variation && empty( $product_description ) ) {
			$product_description = $this->get_description_from_product( wc_get_product( $product_parent_id ) );
		}

		if ( method_exists( $product, 'get_date_on_sale_from' ) && method_exists( $product, 'get_date_on_sale_to' ) ) {
			$product_date_on_sale_from = $product->get_date_on_sale_from();
			$product_date_on_sale_to = $product->get_date_on_sale_to();
		} else {
			$product_date_on_sale_from = new DateTime();
			$product_date_on_sale_to = new DateTime();
			$product_date_on_sale_from = ( $date_sale_from = get_post_meta( $product_id, '_sale_price_dates_from', true ) ) ? $product_date_on_sale_from->setTimestamp( $date_sale_from ) : null;
			$product_date_on_sale_to = ( $date_sale_to = get_post_meta( $product_id, '_sale_price_dates_to', true ) ) ? $product_date_on_sale_to->setTimestamp( $date_sale_to ) : null;
		}

		$product_item
			->set_id( $product_id )
			->set_sku( $product->get_sku() )
			->set_slug( $product_slug )
			->set_permalink( $product->get_permalink() )
			->set_admin_url( add_query_arg( array( 'post' => $product_id, 'action' => 'edit' ), admin_url( 'post.php' ) ) )
			->set_parent_admin_url( add_query_arg( array( 'post' => $product_parent_id, 'action' => 'edit' ), admin_url( 'post.php' ) ) )
			->set_title( $product->get_title() )
			->set_description( $product_description )
			->set_short_description( $product_short_description )
			->set_link( $product->get_permalink() )
			->set_image_url( $product_image_link )
			->set_additional_image_urls( array_filter( $product_additional_image_ids ) )
			->set_currency( get_woocommerce_currency() )
			->set_price( $this->format_price( $product_price ) )
			->set_price_tax( $this->format_price( $product_price_tax ) )
			->set_sale_price( $this->format_price( $product_sale_price ) )
			->set_sale_price_tax( $this->format_price( $product_sale_price_tax ) )
			->set_checkout_url( $product_is_variation ? add_query_arg( [ 'variation_id' => $product_id, 'add-to-cart' => $product_parent_id ], $product->get_permalink() ) : add_query_arg( 'add-to-cart', $product_id, $product->get_permalink() ) )
			->set_if_needs_shipping( $product->needs_shipping() )
			->set_shipping_weight( $product->get_weight() )
			->set_shipping_weight_unit( get_option( 'woocommerce_weight_unit' ) )
			->set_if_variation( $product_is_variation )
			->set_group_id( $product_parent_id )
			->set_google_category(
				$metaboxes->get_google_category(
					$product_is_variation ? $product_parent_id : $product_id
				)
			);

		// Set sale date if defined
		if ( $product_date_on_sale_from instanceof Datetime && $product_date_on_sale_to instanceof Datetime ) {
			$product_item->set_sale_price_effective_date( $product_date_on_sale_from, $product_date_on_sale_to );
		}

		// Set availability
		$availability = $product->get_availability();
		switch ( $availability['class'] ) {
			case 'in-stock' :
				$product_item->set_in_stock();
				break;
			case 'out-of-stock' :
				$product_item->set_out_of_stock();
				break;
			case 'available-on-backorder' :
				$product_item->set_in_preorder();
				break;
			default :
				$product_item->set_in_stock();
				break;
		}

		// Get categories
		$terms = get_terms( array(
			'object_ids' => $product_item->is_variation() ? $product_parent_id : $product_id,
			'taxonomy' => 'product_cat',
			'hierarchical' => true,
			'fields' => 'id=>parent'
		) );
		$product_item->set_categories( $terms );

		return $product_item;
	}

	/**
	 * Customize the WP Query in wc_get_products
	 *
	 * @param WP_Query $wp_query
	 */
	public function customize_wp_query( \WP_Query &$wp_query ) {
		$products_query = $this->_current_query;
		$product_catalog = $this->_current_query_product_catalog;
		$meta_query = $wp_query->get('meta_query', array());

		// Add meta query manually for versions before of 3.1, when no 'stock_status' was available
		if ( isset( $products_query['stock_status'] ) && version_compare( WC()->version, '3.1.0', '<' ) ) {
			$meta_query[] = array(
				'key' => '_stock_status',
				'compare' => 'IN',
				'value' => $products_query['stock_status']
			);
		}

		// Change compare condition in _stock_status meta query for newest WOO versions that don't allow values in a array
		foreach ( $meta_query as &$query ) {
			if ( $query['key'] === '_stock_status' && is_array( $query['value'] ) ) {
				$query['compare'] = 'IN';
			}
		}

		// Include variation items manually for 3.0.x version of Woo
		if ( in_array( 'variation', $products_query['type'] ) && ! is_array( $wp_query->get('post_type') ) ) {
			$wp_query->set('post_type', array( 'product', 'product_variation' ) );
		}

		// Add feed status meta query
		$key = $this->get_feed_status_meta_key( $product_catalog );
		if ( isset( $products_query[ $key ] ) ) {
			$meta_query[] = array(
				'key' => $this->get_feed_status_meta_key( $product_catalog ),
				'compare' => $products_query[ $key ] ? '=' : 'NOT EXISTS',
				'value' => $products_query[ $key ]
			);
		}

		$wp_query->set('meta_query', $meta_query );
	}

	/**
	 * Get the arguments of the items query
	 *
	 * @param string $filter
	 * @param ProductCatalogManager $product_catalog
	 *
	 * @return array
	 */
	protected function query_items_args( $filter, ProductCatalogManager $product_catalog ) {
		$products_query = array(
			'status'   => array( 'publish' ),
			'limit'    => $product_catalog->configuration()->get( Configuration::OPTION_CHUNK_LIMIT ),
			'orderby'  => 'ID',
			'order'    => 'ASC',
			'category' => array(),
			'tag'      => array(),
			'include'  => array(),
			'exclude'  => array(),
		);

		// Collect the product types to use in the query
		if ( $product_types = array_filter( (array) $product_catalog->configuration()->get( Configuration::OPTION_FILTER_BY_TYPE ) ) ) {
			$products_query['type'] = $product_types;
		} else {
			$products_query['type'] = array_merge( array_keys( wc_get_product_types() ) );
		}

		// Add variations if the option is disabled
		if ( in_array( 'variable', $products_query['type'] ) && ! $product_catalog->configuration()->get( Configuration::OPTION_NO_VARIATIONS ) ) {
			$products_query['type'][] = 'variation';
		}

		$filter_cat = array_map( 'intval', array_filter( (array) $product_catalog->configuration()->get( Configuration::OPTION_FILTER_BY_CATEGORY ) ) );
		if ( ! empty( $filter_cat ) ) {
			foreach ( $filter_cat as $cat_id ) {
				$term = get_term( $cat_id );
				$products_query['category'][] = $term->slug;
			}
		}

		$filter_tag = array_map( 'intval', array_filter( (array) $product_catalog->configuration()->get( Configuration::OPTION_FILTER_BY_TAG ) ) );
		if ( ! empty( $filter_tag ) ) {
			foreach ( $filter_tag as $tag_id ) {
				$term = get_term( $tag_id );
				$products_query['tag'][] = $term->slug;
			}
		}

		if ( $product_catalog->configuration()->get( Configuration::OPTION_FILTER_ON_SALE ) ) {
			$products_query['include'] = wc_get_product_ids_on_sale();
		}

		$filter_stock = array_filter( (array) $product_catalog->configuration()->get( Configuration::OPTION_FILTER_BY_STOCK ) );
		if ( ! empty( $filter_stock ) ) {
			$filter_stock = array_map( function( $status ){
				$stock_map = array(
					AEPC_Addon_Product_Item::IN_STOCK => 'instock',
					AEPC_Addon_Product_Item::OUT_OF_STOCK => 'outofstock'
				);
				return $stock_map[ $status ];
			}, $filter_stock );

			$products_query['stock_status'] = $filter_stock;
		}

		return $this->filter_items_query( $filter, $products_query, $product_catalog );
	}

	/**
	 * Query the items from DB in base of if get edited, saved or all
	 *
	 * @param string $filter One of 'all', 'edited' or 'saved'
	 * @param ProductCatalogManager $product_catalog
	 * @param Metaboxes $metaboxes
	 *
	 * @return AEPC_Addon_Product_Item[]
	 */
	protected function query_items( $filter, ProductCatalogManager $product_catalog, Metaboxes $metaboxes ) {
		$products_query = $this->query_items_args( $filter, $product_catalog );
		return $this->do_query( $products_query, $product_catalog, $metaboxes );
	}

	/**
	 * Perform the query from the array of arguments for wc_get_products()
	 *
	 * @param $products_query
	 * @param ProductCatalogManager $product_catalog
	 * @param Metaboxes $metaboxes
	 *
	 * @return AEPC_Addon_Product_Item[]
	 */
	protected function do_query( $products_query, ProductCatalogManager $product_catalog, Metaboxes $metaboxes ) {
		$this->_current_query = $products_query;
		$this->_current_query_product_catalog = $product_catalog;

		// Add hook to customize the query
		add_action( 'pre_get_posts', array( $this, 'customize_wp_query' ) );

		// Map WC objects
		$products = wc_get_products( $products_query );

		// Remove previous hook
		remove_action( 'pre_get_posts', array( $this, 'customize_wp_query' ) );

		// Map the product item object
		foreach ( $products as $i => &$item ) {
			$item = $this->get_product_item( $item, $metaboxes, $product_catalog->configuration() );

			// If variant and parent is 0, go ahead
			if ( $item->is_variation() && $item->get_group_id() === 0 ) {
				unset( $products[$i] );
			}
		}

		$this->_current_query_product_catalog = null;
		$this->_current_query = null;
		return $products;
	}

	/**
	 * Filter the WP_Query arguments with the necessary arguments in order to filter the query in base of the status
	 * of the product in the feed
	 *
	 * @param string $filter
	 * @param array $products_query
	 * @param ProductCatalogManager $product_catalog
	 *
	 * @return array
	 */
	protected function filter_items_query( $filter, array $products_query, ProductCatalogManager $product_catalog ) {
		$key = $this->get_feed_status_meta_key( $product_catalog );

		switch ( $filter ) {

			case 'not-saved' :
				$products_query[ $key ] = false;
				break;

			case 'saved' :
				$products_query[ $key ] = DbProvider::FEED_STATUS_SAVED;
				break;

			case 'edited' :
				$products_query[ $key ] = DbProvider::FEED_STATUS_EDITED;
				break;

		}

		return $products_query;
	}

	/**
	 * Returns the array of all term objects id=>name for all categories of the shop
	 *
	 * @return array
	 */
	public function get_product_categories() {
		$categories = array();

		foreach ( get_terms( array(
			'taxonomy' => 'product_cat',
			'hide_empty' => false
		) ) as $term ) {
			$categories[ $term->term_id ] = $term->name;
		}

		return $categories;
	}

	/**
	 * Returns the array of all term objects id=>name for all tags of the shop
	 *
	 * @return array
	 */
	public function get_product_tags() {
		$categories = array();

		foreach ( get_terms( array(
			'taxonomy' => 'product_tag',
			'hide_empty' => false
		) ) as $term ) {
			$categories[ $term->term_id ] = $term->name;
		}

		return $categories;
	}

	/**
	 * Return the array with all AEPC_Addon_Product_item instances for the products to include inside the XML feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 * @param Metaboxes $metaboxes
	 *
	 * @return AEPC_Addon_Product_Item[]
	 */
	public function get_feed_entries( ProductCatalogManager $product_catalog, Metaboxes $metaboxes ) {
		return $this->query_items( ProductCatalogManager::FILTER_ALL, $product_catalog, $metaboxes );
	}

	/**
	 * Get the feed entries to save into the feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 * @param Metaboxes $metaboxes
	 *
	 * @return AEPC_Addon_Product_item[]
	 */
	public function get_feed_entries_to_save( ProductCatalogManager $product_catalog, Metaboxes $metaboxes ) {
		return $this->query_items( ProductCatalogManager::FILTER_NOT_SAVED, $product_catalog, $metaboxes );
	}

	/**
	 * Get the feed entries to edit in the feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 * @param Metaboxes $metaboxes
	 *
	 * @return AEPC_Addon_Product_item[]
	 */
	public function get_feed_entries_to_edit( ProductCatalogManager $product_catalog, Metaboxes $metaboxes ) {
		return $this->query_items( ProductCatalogManager::FILTER_EDITED, $product_catalog, $metaboxes );
	}

	/**
	 * Get the key associated to the product catalog
	 *
	 * @param ProductCatalogManager $product_catalog
	 *
	 * @return string
	 */
	protected function get_feed_status_meta_key( ProductCatalogManager $product_catalog ) {
		return sprintf( '%s_%s', self::FEED_STATUS_META, $product_catalog->get_entity()->getId() );
	}

	/**
	 * Save a meta in the product post that set the product as saved in the product feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 * @param \AEPC_Addon_Product_Item $item
	 */
	public function set_product_saved_in_feed( ProductCatalogManager $product_catalog, \AEPC_Addon_Product_Item $item ) {
		update_post_meta( $item->get_id(), $this->get_feed_status_meta_key( $product_catalog ), DbProvider::FEED_STATUS_SAVED );
	}

	/**
	 * Save the meta in the product post that set the product as edited in the product feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 * @param \AEPC_Addon_Product_Item $item
	 */
	public function set_product_edited_in_feed( ProductCatalogManager $product_catalog, \AEPC_Addon_Product_Item $item ) {
		update_post_meta( $item->get_id(), $this->get_feed_status_meta_key( $product_catalog ), DbProvider::FEED_STATUS_EDITED );
	}

	/**
	 * Delete the meta in the product post that set the product as saved in the product feed
	 *
	 * @param ProductCatalogManager $product_catalog
	 * @param \AEPC_Addon_Product_Item $item
	 */
	public function set_product_not_saved_in_feed( ProductCatalogManager $product_catalog, \AEPC_Addon_Product_Item $item ) {
		delete_post_meta( $item->get_id(), $this->get_feed_status_meta_key( $product_catalog ) );
	}

	/**
	 * Perform a global delete in one query ideally for all feed status associated to the product catalog
	 *
	 * @param ProductCatalogManager $product_catalog
	 */
	public function remove_all_feed_status( ProductCatalogManager $product_catalog ) {
		delete_post_meta_by_key( $this->get_feed_status_meta_key( $product_catalog ) );
	}

	/**
	 * Detect if there are items to save yet or not
	 *
	 * @param ProductCatalogManager $product_catalog
	 *
	 * @return bool
	 */
	public function there_are_items_to_save( ProductCatalogManager $product_catalog ) {
		$products_query = $this->query_items_args( ProductCatalogManager::FILTER_NOT_SAVED, $product_catalog );

		// Get only counter
		$products_query['limit'] = 1;

		// Query
		$products = $this->do_query( $products_query, $product_catalog, new Metaboxes() );

		return ! empty( $products );
	}

	/**
	 * Format the price with fixed decimals following the WooCommerce settings
	 *
	 * @param $price
	 *
	 * @return string
	 */
	protected function format_price( $price ) {
		$decimals = wc_get_price_decimals();
		$negative = $price < 0;
		$price    = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
		$price    = round( $price, $decimals );

		if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
			$price = wc_trim_zeros( $price );
		}

		return $price;
	}
}
