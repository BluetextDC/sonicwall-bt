<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * CRUD class for custom audience record
 *
 * @class AEPC_Admin_CA
 */
class AEPC_Admin_CA {

	/** @var array */
	protected $_data = array(
		'ID' => 0,
		'fb_id' => 0,
		'date' => '',
		'date_gmt' => '',
		'modified_date' => '',
		'modified_date_gmt' => '',
		'name' => '',
		'description' => '',
		'prefill' => true,
		'retention' => 14,
		'rule' => array(),
		'approximate_count' => 0
	);

	/** @var array Save here if some error occurred per each field */
	protected $errors = array();

	/** @var bool Flag indicates if CA exists or not */
	protected $exists = false;

	/** @var array Save the available translations to go speedy */
	private static $translations = array();

	/**
	 * AEPC_Admin_CA constructor.
	 *
	 * Initialize the instance in base of ID. If ID is zero, it preparse the instance for a new CA to save
	 *
	 * @param int $id
	 */
	public function __construct( $id = 0 ) {
		if ( ! empty( $id ) ) {
			$this->populate( $id );
		}
	}

	/**
	 * Populate the data from DB
	 *
	 * @param $id
	 */
	public function populate( $id ) {
		if ( empty( $id ) || ! ( $ca_object = $this->read( $id ) ) ) {
			return;
		}

		$this->_data = $ca_object;
		$this->exists = true;
	}

	/**
	 * Populate the data from DB by getting the record by Facebook ID, instead of record ID
	 *
	 * @param $fb_id
	 */
	public function populate_by_fb_id( $fb_id ) {
		if ( empty( $fb_id ) ) {
			return;
		}

		global $wpdb;

		if ( $ca = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}aepc_custom_audiences WHERE fb_id = %d", absint( $fb_id ) ), ARRAY_A ) ) {
			$this->_data = array_map( 'maybe_unserialize', $ca );
			$this->exists = true;
		}
	}

	/**
	 * Retrieve the record from DB
	 *
	 * @param $id
	 *
	 * @return array|null|object|void
	 */
	public function read( $id ) {
		global $wpdb;

		$ca = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}aepc_custom_audiences WHERE ID = %d", $id ), ARRAY_A );
		return is_array( $ca ) ? array_map( 'maybe_unserialize', $ca ) : $ca;
	}

	/**
	 * Create the record in DB
	 *
	 * @param array $args
	 *
	 * @return bool|int
	 */
	public function create( $args = array() ) {
		$args = array_intersect_key( $args, $this->_data );
		$data = wp_parse_args( $args, $this->_data );

		if ( $this->exists() ) {
			return false;
		}

		// Fields validation
		$data = $this->validate_fields( $data );

		// Save in Facebook Ad account
		if ( ! AEPC_Admin::$api->is_debug() ) {
			$res = AEPC_Admin::$api->create_audience( $data );

			// Add Facebook ID in arguments
			$data['fb_id'] = isset( $res->id ) ? $res->id : 0;
		}

		// Sanitize values for database
		$this->_data = $data;
		foreach ( $data as $key => &$val ) {
			if ( is_array( $val ) ) {
				$val = serialize( $val );
			}
		}

		// Save the values
		global $wpdb;

		// Add datatime
		$data['date'] = $data['modified_date'];
		$data['date_gmt'] = $data['modified_date_gmt'];

		$wpdb->insert( $wpdb->prefix . 'aepc_custom_audiences', $data );
		$this->set_id( $wpdb->insert_id );
		$this->exists = true;

		return $this->get_id();
	}

	/**
	 * Update a record in DB. Must be defined an ID, to select what record you have to edit
	 *
	 * @param array $args
	 *
	 * @return bool|int
	 */
	public function update( $args = array() ) {
		if ( ! $this->exists() ) {
			return false;
		}

		$original_values = $this->_data;
		$args = array_intersect_key( $args, $this->_data );
		$to_update = wp_parse_args( $args, $this->_data );

		if ( empty( $this->_data['ID'] ) || ! ( $ca_object = $this->read( $this->_data['ID'] ) ) ) {
			return false;
		}

		// Fields validation
		$to_update = $this->validate_fields( $to_update );
		$this->_data = $to_update;

		// Save in Facebook Ad account
		if ( ! AEPC_Admin::$api->is_debug() ) {
			AEPC_Admin::$api->update_audience( $this->get_facebook_id(), $to_update );
		}

		// Sanitize values for database
		foreach ( $to_update as $key => &$val ) {
			if ( is_array( $val ) ) {
				$val = serialize( $val );
			}

			if ( $val === $original_values[ $key ] ) {
				unset( $val );
			}
		}

		// Do not update if all values are unchanged
		if ( empty( $to_update ) ) {
			return false;
		}

		// Save the values
		global $wpdb;
		$wpdb->update( $wpdb->prefix . 'aepc_custom_audiences', $to_update, array( 'ID' => $this->get_id() ) );
		return true;
	}

	/**
	 * Refresh the size of audience, getting it from Facebook API
	 */
	public function refresh_size() {
		$ca = AEPC_Admin::$api->get_audience( $this->get_facebook_id(), 'approximate_count' );
		$this->update( array(
			'approximate_count' => absint( $ca->approximate_count )
		) );
	}

	/**
	 * Refresh the data audience, getting it from Facebook API
	 */
	public function refresh_facebook_data() {
		$ca = AEPC_Admin::$api->get_audience( $this->get_facebook_id() );
		$this->update( array(
			'name' => $ca->name,
			'description' => $ca->description,
			'approximate_count' => absint( $ca->approximate_count )
		) );
	}

	/**
	 * Fields validation, useful for create and update method
	 *
	 * @throws Exception
	 */
	protected function validate_fields( $args = array() ) {
		$this->reset_errors();

		$args = wp_parse_args( $args, $this->_data );

		if ( empty( $args['name'] ) ) {
			AEPC_Admin_Notices::add_notice( 'error', 'ca_name', __( 'The name for the cluster is required.', 'pixel-caffeine' ) );
		}

		if ( empty( $args['rule'] ) ) {
			AEPC_Admin_Notices::add_notice( 'error', 'ca_include_url', __( 'You have to define one of included or excluded URL.', 'pixel-caffeine' ) );
			AEPC_Admin_Notices::add_notice( 'error', 'ca_exclude_url', __( 'You have to define one of included or excluded URL.', 'pixel-caffeine' ) );
			AEPC_Admin_Notices::add_notice( 'error', 'ca_rule', __( 'A custom audience from a website must contain at least one audience rule.', 'pixel-caffeine' ) );
		}

		if ( empty( $args['retention'] ) ) {
			AEPC_Admin_Notices::add_notice( 'error', 'ca_retention', __( 'You have to define the number of days to keep the user in this cluster.', 'pixel-caffeine' ) );
		}

		$args['retention'] = intval( $args['retention'] );
		$args['modified_date'] = current_time( 'mysql', false );
		$args['modified_date_gmt'] = current_time( 'mysql', true );
		$args['approximate_count'] = absint( $args['approximate_count'] );

		if ( $args['retention'] < 1 || $args['retention'] > 180 ) {
			AEPC_Admin_Notices::add_notice( 'error', 'ca_retention', __( 'The retention value must be beetwen 1 and 180 days value.', 'pixel-caffeine' ) );
		}

		// Remove the prefill field, because it's useful only for facebook request and it's useless for future
		unset( $args['prefill'] );

		// Throw exception if error
		if ( $this->have_errors() ) {
			throw new Exception( __( '<strong>Cannot save custom audience</strong> Please, check fields errors below.', 'pixel-caffeine' ) );
		}

		return $args;
	}

	/**
	 * Update a record in DB. Must be defined an ID, to select what record you have to edit
	 *
	 * @return bool|int
	 */
	public function delete() {
		if ( ! $this->exists() ) {
			return false;
		}

		// Save in Facebook Ad account
		if ( ! AEPC_Admin::$api->is_debug() ) {
			AEPC_Admin::$api->delete_audience( $this->get_facebook_id() );
		}

		// Save the values
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'aepc_custom_audiences', array( 'ID' => $this->get_id() ) );
		$this->exists = false;
		return true;
	}

	/**
	 * Create an identical CA in a new record with a new ID
	 *
	 * @param bool|string $name
	 *
	 * @return AEPC_Admin_CA
	 */
	public function duplicate( $name = false ) {
		$new = clone $this;

		$new->set_id( 0 );
		$new->exists = false;

		// Change name if defined
		if ( false !== $name ) {
			$new->set_name( $name );
		}

		$new->create();

		return $new;
	}

	/**
	 * Check if the CA exists
	 */
	public function exists() {
		return (bool) $this->exists;
	}

	/**
	 * Get the ID of record of this instance
	 *
	 * @return int
	 */
	public function get_id() {
		return absint( $this->_data['ID'] );
	}

	/**
	 * Set the ID of record of this instance and also populate data by ID
	 *
	 * @param int $id
	 *
	 * @return int
	 */
	public function set_id( $id ) {
		$this->_data['ID'] = $id;
		$this->populate( $id );
	}

	/**
	 * Get the Facebook ID for this CA
	 *
	 * @return string
	 */
	public function get_facebook_id() {
		return $this->_data['fb_id'];
	}

	/**
	 * Set the Facebook ID for this CA
	 *
	 * @param string $fb_id
	 *
	 * @return string
	 */
	public function set_facebook_id( $fb_id ) {
		return $this->_data['fb_id'] = $fb_id;
	}

	/**
	 * Get the date of record of this instance
	 *
	 * @param bool $gmt
	 *
	 * @return string
	 */
	public function get_date( $gmt = false ) {
		return $this->_data[ 'date' . ( $gmt ? '_gmt' : '' ) ];
	}

	/**
	 * Set the date of record of this instance
	 *
	 * @param string $date
	 *
	 * @return string
	 */
	public function set_date( $date ) {
		$this->_data['date'] = $date;
		$this->_data['date_gmt'] = gmdate( 'Y-m-d H:i:s', ( strtotime( $date ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) );
	}

	/**
	 * Get the time for print
	 *
	 * @param string $what You can return a specific date with a specific format - 't_time' for only time - 'h_time' for human date
	 *
	 * @return string
	 */
	public function get_human_date( $what = '' ) {
		$t_time = mysql2date( get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ), $this->get_date(), true );
		$time = mysql2date( 'G', $this->get_date( true ) );

		$time_diff = time() - $time;

		if ( $time_diff < MINUTE_IN_SECONDS ) {
			$h_time = __( 'Now', 'pixel-caffeine' );
		} else {
			$h_time = sprintf( __( '%s ago', 'pixel-caffeine' ), human_time_diff( $time ) );
		}

		if ( ! empty( $what ) && isset( ${$what} ) ) {
			return ${$what};
		}

		return array(
			't_time' => $t_time,
			'h_time' => $h_time,
		);
	}

	/**
	 * Print out the human date
	 *
	 * @param string $what You can return a specific date with a specific format - 't_time' for only time - 'h_time' for human date
	 */
	public function human_date( $what = '' ) {
		echo $this->get_human_date( $what );
	}

	/**
	 * Get the modified_date of record of this instance
	 *
	 * @param bool $gmt
	 *
	 * @return string
	 */
	public function get_modified_date( $gmt = false ) {
		return $this->_data[ 'modified_date' . ( $gmt ? '_gmt' : '' ) ];
	}

	/**
	 * Set the modified_date of record of this instance
	 *
	 * @param string $modified_date
	 *
	 * @return string
	 */
	public function set_modified_date( $modified_date ) {
		$this->_data['modified_date'] = $modified_date;
		$this->_data['modified_date_gmt'] = gmdate( 'Y-m-d H:i:s', ( strtotime( $modified_date ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) );
	}

	/**
	 * Get the name of record of this instance
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->_data['name'];
	}

	/**
	 * Set the name of record of this instance
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function set_name( $name ) {
		$this->_data['name'] = $name;
	}

	/**
	 * Get the description of record of this instance
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->_data['description'];
	}

	/**
	 * Set the ID of record of this instance
	 *
	 * @param string $description
	 *
	 * @return string
	 */
	public function set_description( $description ) {
		$this->_data['description'] = $description;
	}

	/**
	 * Get if the custom audience must include website traffic recorded prior to the audience creation.
	 *
	 * @return bool
	 */
	public function get_prefill() {
		return (bool) $this->_data['prefill'];
	}

	/**
	 * Set if the custom audience must include website traffic recorded prior to the audience creation.
	 *
	 * @param bool $prefill
	 *
	 * @return bool
	 */
	public function set_prefill( $prefill ) {
		$this->_data['prefill'] = $prefill;
	}

	/**
	 * Get number of days to keep the user in this cluster. You can use any value between 1 and 180 days.
	 * Defaults to 14 days if not specified.
	 *
	 * @return int
	 */
	public function get_retention() {
		return intval( $this->_data['retention'] );
	}

	/**
	 * Set number of days to keep the user in this cluster. You can use any value between 1 and 180 days.
	 *
	 * @param int $retention
	 *
	 * @return int
	 */
	public function set_retention( $retention ) {
		$this->_data['retention'] = $retention;
	}

	/**
	 * Get Audience rules to be applied on the referrer URL.
	 *
	 * @param string $what You can have a specific rule, as "include_url" or "exclude_url"
	 *
	 * @return array
	 */
	public function get_rule( $what = '' ) {
		$rules = maybe_unserialize( $this->_data['rule'] );

		if ( 'include_url' == $what ) {
			foreach ( $rules as $rule ) {
				if ( 'include' == $rule['main_condition'] && 'url' === $rule['event_type'] && isset( $rule['conditions'][0]['value'] ) ) {
					return $rule['conditions'][0]['value'];
				}
			}
		}

		elseif ( 'exclude_url' == $what ) {
			foreach ( $rules as $rule ) {
				if ( 'exclude' == $rule['main_condition'] && 'url' === $rule['event_type'] && isset( $rule['conditions'][0]['value'] ) ) {
					return ! empty( $rule['conditions'][0]['value'] ) ? $rule['conditions'][0]['value'] : '';
				}
			}
		}

		else {
			return $rules;
		}

		return array();
	}

	/**
	 * Get the condition used for the URL field
	 *
	 * @param string $what You can have a specific rule, as "include_url" or "exclude_url"
	 *
	 * @return array
	 */
	public function get_url_condition( $what = '' ) {
		$rules = maybe_unserialize( $this->_data['rule'] );

		if ( 'include_url' == $what ) {
			foreach ( $rules as $rule ) {
				if ( 'include' == $rule['main_condition'] && 'url' === $rule['event_type'] && isset( $rule['conditions'][0]['operator'] ) ) {
					return $rule['conditions'][0]['operator'];
				}
			}
		}

		elseif ( 'exclude_url' == $what ) {
			foreach ( $rules as $rule ) {
				if ( 'exclude' == $rule['main_condition'] && 'url' === $rule['event_type'] && isset( $rule['conditions'][0]['operator'] ) ) {
					return ! empty( $rule['conditions'][0]['operator'] ) ? $rule['conditions'][0]['operator'] : '';
				}
			}
		}

		else {
			return $rules;
		}

		return array();
	}

	/**
	 * Get Audience rules to be applied on the referrer URL.
	 *
	 * @param array $rules
	 *
	 * @return array
	 */
	public function set_rule( array $rules ) {
		$this->_data['rule'] = $rules;
	}

	/**
	 * Get the rule filters, excluding URL filter
	 *
	 * @param string $condition Could be 'include' or 'exclude' to check specifically for what rule we want to get
	 *
	 * @return array
	 */
	public function get_filters( $condition = '' ) {
		$filters = $this->get_rule();

		// Exclude URL from filters
		foreach ( $filters as $k => $f ) {
			if (
				'url' === $f['event_type']
				|| ! empty( $condition ) && (
					'include' == $condition && 'exclude' == $f['main_condition']
					|| 'exclude' == $condition && 'include' == $f['main_condition']
				)
			) {
				unset( $filters[ $k ] );
			}
		}

		return array_values( $filters );
	}

	/**
	 * Check if there are some filters in CA
	 *
	 * @param string $condition Could be 'include' or 'exclude' to check specifically for what rule we want to check
	 *
	 * @return bool
	 */
	public function has_filters( $condition = '' ) {
		$filters = $this->get_filters();

		// Check for specific condition
		if ( ! empty( $condition ) ) {
			foreach ( $filters as $filter ) {
				if (
					'include' === $condition && 'include' == $filter['main_condition']
					|| 'exclude' === $condition && 'exclude' == $filter['main_condition']
				) {
					return true;
				}
			}
		}

		else {
			return ! empty( $filters );
		}

		return false;
	}

	/**
	 * Get the size of audience
	 *
	 * @return int
	 */
	public function get_size() {
		return absint( $this->_data['approximate_count'] );
	}

	/**
	 * Translate the configuration array of a filter into a readable statement to print out on screen
	 *
	 * @param $rule
	 * @param string $highlight_before
	 * @param string $highlight_after
	 *
	 * @return string|void
	 */
	public function get_human_filter( $rule, $highlight_before = '[', $highlight_after = ']' ) {

		// Standard statements for the filter rows, they may be changed in some condition
		$translate_words = array(

			// Specific cases
			'attributes' => array(
				'login_status' => array(
					'eq'  => _x( 'is %2$s', '%2$s is the value', 'pixel-caffeine' ),
					'neq' => _x( 'is not %2$s', '%2$s is the value', 'pixel-caffeine' ),
				),
				'referrer' => array(
					'i_contains'     => _x( 'come from %2$s', '%2$s is the value', 'pixel-caffeine' ),
					'i_not_contains' => _x( 'don\'t come from %2$s', '%2$s is the value', 'pixel-caffeine' ),
				),
				'device_type' => array(
					'i_contains'     => _x( 'use %2$s', '%2$s is the value', 'pixel-caffeine' ),
					'i_not_contains' => _x( 'don\'t use %2$s', '%2$s is the value', 'pixel-caffeine' ),
					'eq'     => _x( 'use %2$s', '%2$s is the value', 'pixel-caffeine' ),
					'neq'    => _x( 'don\'t use %2$s', '%2$s is the value', 'pixel-caffeine' ),
				),
			),

			'blog' => array(
				'categories' => array(
					'i_contains'     => _x( 'read posts from %2$s %1$s', '%1$s is the taxonomy and %2$s is the term of that taxonomy', 'pixel-caffeine' ),
					'i_not_contains' => _x( 'don\'t read posts from %2$s %1$s', '%1$s is the taxonomy and %2$s is the term of that taxonomy', 'pixel-caffeine' ),
				),
				'tax_post_tag' => array(
					'i_contains'     => _x( 'read posts from %2$s %1$s', '%1$s is the taxonomy and %2$s is the term of that taxonomy', 'pixel-caffeine' ),
					'i_not_contains' => _x( 'don\'t read posts from %2$s %1$s', '%1$s is the taxonomy and %2$s is the term of that taxonomy', 'pixel-caffeine' ),
				),
				'posts' => array(
					'i_contains'     => _x( 'read %2$s from %1$s', '%1$s is the post type or blog and %2$s should be "the post(s) <post title>" or "any post" if all', 'pixel-caffeine' ),
					'i_not_contains' => _x( 'don\'t read %2$s from %1$s', '%1$s is the post type or blog and %2$s should be "the post(s) <post title>" or "any post" if all', 'pixel-caffeine' ),
				),
				'pages' => array(
					'i_contains'     => _x( 'visit %2$s %1$s', '%1$s is "page" or "pages" and %2$s is the page title', 'pixel-caffeine' ),
					'i_not_contains' => _x( 'don\'t visit %2$s %1$s', '%1$s is "page" or "pages" and %2$s is the page title', 'pixel-caffeine' ),
				),
				'custom_fields' => array(
					'i_contains'     => _x( 'read a post contains %1$s %2$s', '%1$s is the custom field key and %2$s is the value. Complete statement: "read a post contains [field_key] custom field with [value] and [value2] as value"', 'pixel-caffeine' ),
					'i_not_contains' => _x( 'don\'t read a post contains %1$s %2$s', '%1$s is the custom field key and %2$s is the value. Complete statement: "don\'t read a post contains [field_key] custom field with [value] and [value2] as value"', 'pixel-caffeine' ),
				)
			),

			'ecommerce' => array(

				'ViewContent' => array(
					'generic' => __( 'visit a product page', 'pixel-caffeine' ),
					'specific' => array(
						'singular' => _x( 'visit %s product page', '%s is the product title', 'pixel-caffeine' ),
						'plural'   => _x( 'visit %s product pages', '%s are the product titles', 'pixel-caffeine' ),
					)
				),

				'Search' => array(
					'generic' => _x( 'search', 'it is followed by "something" or a specific string searched', 'pixel-caffeine' )
				),

				'AddToCart' => array(
					'generic' => __( 'add to cart a product', 'pixel-caffeine' ),
					'specific' => array(
						'singular' => _x( 'add to cart %2$s product', '%2$s is the product title', 'pixel-caffeine' ),
						'plural'   => _x( 'add to cart %2$s products', '%2$s are the product titles', 'pixel-caffeine' ),
					)
				),

				'AddToWishlist' => array(
					'generic' => __( 'add to wishlist a product', 'pixel-caffeine' ),
					'specific' => array(
						'singular' => _x( 'add to wishlist %2$s product', '%2$s is the product title', 'pixel-caffeine' ),
						'plural'   => _x( 'add to wishlist %2$s products', '%2$s are the product titles', 'pixel-caffeine' ),
					)
				),

				'InitiateCheckout' => array(
					'generic' => __( 'enter the checkout flow', 'pixel-caffeine' ),
					'specific' => array(
						'singular' => _x( 'enter the checkout flow containing %2$s product', '%2$s is the product title', 'pixel-caffeine' ),
						'plural'   => _x( 'enter the checkout flow containing %2$s products', '%2$s are the product titles', 'pixel-caffeine' ),
					)
				),

				'AddPaymentInfo' => array(
					'generic' => __( 'add payment information in the checkout flow', 'pixel-caffeine' ),
					'specific' => array(
						'singular' => _x( 'add payment information in the checkout flow containing %2$s product', '%2$s is the product title', 'pixel-caffeine' ),
						'plural'   => _x( 'add payment information in the checkout flow containing %2$s products', '%2$s are the product titles', 'pixel-caffeine' ),
					)
				),

				'Purchase' => array(
					'generic' => __( 'make a purchase', 'pixel-caffeine' ),
					'specific' => array(
						'singular' => _x( 'purchase %2$s product', '%2$s is the product title', 'pixel-caffeine' ),
						'plural'   => _x( 'purchase %2$s products', '%2$s are the product titles', 'pixel-caffeine' ),
					)
				),

				'Lead' => array(
					'generic' => __( 'sign up for something', 'pixel-caffeine' ),
					'specific' => array(
						'singular' => _x( 'sign up for %2$s product', '%2$s is the product title', 'pixel-caffeine' ),
						'plural'   => _x( 'sign up for %2$s products', '%2$s are the product titles', 'pixel-caffeine' ),
					)
				),

				'CompleteRegistration' => array(
					'generic' => __( 'complete registration for a service', 'pixel-caffeine' ),
					'specific' => array(
						'singular' => _x( 'complete registration for %2$s product', '%2$s is the product title', 'pixel-caffeine' ),
						'plural'   => _x( 'complete registration for %2$s products', '%2$s are the product titles', 'pixel-caffeine' ),
					)
				),
			),

			// Standard statements
			'i_contains'     => _x( '%1$s contains %2$s', '%1$s is the parameter and %2$s is the value', 'pixel-caffeine' ),
			'i_not_contains' => _x( '%1$s not contains %2$s', '%1$s is the parameter and %2$s is the value', 'pixel-caffeine' ),
			'eq'             => _x( 'have set %2$s as %1$s', '%1$s is the parameter and %2$s is the value', 'pixel-caffeine' ),
			'neq'            => _x( 'have not set %2$s as %1$s', '%1$s is the parameter and %2$s is the value', 'pixel-caffeine' ),
			'gte'            => _x( '%1$s greater than or equal to %2$s', '%1$s is the parameter and %2$s is the value', 'pixel-caffeine' ),
			'gt'             => _x( '%1$s greater than %2$s', '%1$s is the parameter and %2$s is the value', 'pixel-caffeine' ),
			'lte'            => _x( '%1$s lower than or equal to %2$s', '%1$s is the parameter and %2$s is the value', 'pixel-caffeine' ),
			'lt'             => _x( '%1$s lower than %2$s', '%1$s is the parameter and %2$s is the value', 'pixel-caffeine' ),
		);

		// Don't add any statement for URL filter
		if ( 'url' == $rule['event_type'] && 'url' == $rule['event'] ) {
			return '';
		}

		$conditions = array();
		$values_count = 0;
		$prepend = '';

		// Force to add conditions key when it doesn't exist
		if ( ! isset( $rule['conditions'] ) ) {
			$rule['conditions'] = array();
		}

		foreach ( $rule['conditions'] as $k => $condition ) {
			$statement = $parameter = $value = '';

			// Remove condition if it's not allowed empty key and empty value
			if ( in_array( $rule['event_type'], array( 'attributes', 'blog' ) ) && ( isset( $condition['key'] ) && empty( $condition['key'] ) || empty( $condition['value'] ) ) ) {
				unset( $rule['conditions'][$k] );
				continue;
			}

			// Define the value and parameters in specific cases
			if ( ! empty( $condition['value'] ) ) {
				$condition['value'] = array_map( 'trim', explode( ',', $condition['value'] ) );

				// Save the count of values useful for parameter, to choose between singular and plural
				$values_count = count( $condition['value'] );

				// Use this to set some text after and before the value, by replacing the value of variable with a localized string and %s for the value
				$value_wrapper = '%s';

				// Sanitize all values
				foreach ( $condition['value'] as &$v ) {

					// Get language english name
					if ( 'attributes' == $rule['event_type'] && 'language' == $rule['event'] ) {
						if ( 'en-US' == $v ) {
							$v = __( 'English (American)', 'pixel-caffeine' );
						}

						else {
							if ( empty( self::$translations ) ) {
								require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
								self::$translations = wp_get_available_translations();
							}

							foreach ( self::$translations as $translation ) {
								if ( $v == str_replace( '_', '-', $translation['language'] ) ) {
									$v = $translation['english_name'];
								}
							}
						}
					}

					// Get label of taxonomy
					elseif ( 'blog' == $rule['event_type'] && in_array( $rule['event'], array( 'categories', 'tax_post_tag' ) ) ) {
						if ( '[[any]]' === $v ) {
							$v = _x( 'any', 'Sentence like: "read posts from any category"', 'pixel-caffeine' );
						} elseif ( $term = get_term_by( 'slug', $v, str_replace( 'tax_', '', ( ! empty( $condition['key'] ) ? $condition['key'] : $rule['event'] ) ) ) ) {
							$v = $term->name;
						}

						// Set now parameter
						if ( ! empty( $condition['key'] ) && 'tax_category' == $condition['key'] ) {
							$parameter = _n( __( 'category', 'pixel-caffeine' ), __( 'categories', 'pixel-caffeine' ), $values_count );
						} elseif ( 'tax_post_tag' == $rule['event'] && 'tax_post_tag' == $condition['key'] ) {
							$parameter = _n( __( 'tag', 'pixel-caffeine' ), __( 'tags', 'pixel-caffeine' ), $values_count );
						} elseif ( function_exists( 'WC' ) && 'tax_post_tag' == $rule['event'] && 'tax_product_tag' == $condition['key'] ) {
							$parameter = _n( __( 'product tag', 'pixel-caffeine' ), __( 'product tags', 'pixel-caffeine' ), $values_count );
						} else {
							if ( '[[any]]' === $v ) {
								$v = __( 'any term', 'pixel-caffeine' );
							}
							if ( $taxonomy = get_taxonomy( str_replace( 'tax_', '', $condition['key'] ) ) ) {
								$label = $taxonomy->label;
							} else {
								$label = str_replace( 'tax_', '', $condition['key'] );
							}
							$parameter = sprintf( __( 'of %s custom taxonomy', 'pixel-caffeine' ), $highlight_before . $label . $highlight_after );
						}
					}

					// Get post title
					elseif ( 'blog' == $rule['event_type'] && 'posts' == $rule['event'] ) {
						if ( '[[any]]' == $v ) {
							$v = __( 'any post', 'pixel-caffeine' );
						} else {
							$value_wrapper = _n( 'the post %s', 'the posts %s', $values_count, 'pixel-caffeine' );
							if ( $post_title = get_the_title( $v ) ) {
								$v = $post_title;
							}
						}

						// Set now parameter
						if ( 'post' == $condition['key'] ) {
							$parameter = 'blog';
						} else {
							$key = _x( '%s post type', 'The complete statement is "read the posts [Post Title 1] and [Post Title 2] from [Post Type Name] post type"', 'pixel-caffeine' );
							$post_type = get_post_type_object( $condition['key'] );
							if ( $post_type ) {
								$post_type_labels = get_post_type_labels( $post_type );
								$condition['key'] = $post_type_labels->singular_name;
							}
							$parameter = sprintf( $key, $highlight_before . ucfirst( $condition['key'] ) . $highlight_after );
						}
					}

					// Get page title
					elseif ( 'blog' == $rule['event_type'] && 'pages' == $rule['event'] ) {
						if ( '[[any]]' == $v ) {
							$v = __( 'any', 'pixel-caffeine' );
						} elseif ( ! in_array( $v, array( 'home', 'blog' ) ) ) {
							$v = get_the_title( $v );
						}

						// Set now parameter
						$parameter = _n( __( 'page', 'pixel-caffeine' ), __( 'pages', 'pixel-caffeine' ), $values_count );
					}

					// Exception for custom fields
					elseif ( 'blog' == $rule['event_type'] && 'custom_fields' == $rule['event'] ) {
						$value_wrapper = _n( 'with %s value', 'with %s values', $values_count, 'pixel-caffeine' );
						if ( '[[any]]' == $v ) {
							$v = __( 'any', 'pixel-caffeine' );
						}

						// Set now parameter
						if ( '[[any]]' == $condition['key'] ) {
							$parameter = __( 'the custom fields defined on \'Track Custom Fields Based Events\' option on General Settings tab', 'pixel-caffeine' );
						}
					}

					// Exception search event
					elseif ( 'ecommerce' == $rule['event_type'] && 'Search' == $rule['event'] ) {
						if ( '[[any]]' == $v ) {
							$v = __( 'something', 'pixel-caffeine' );
						}

						$statement = '%2$s';
					}

					// Exception search event
					elseif ( 'ecommerce' == $rule['event_type'] ) {
						if ( '[[any]]' == $v ) {
							$v = __( 'any', 'pixel-caffeine' );
						}

						// Replace IDs with product title, if a store plugin installed
						if ( 'content_ids' == $condition['key'] ) {
							foreach ( AEPC_Addons_Support::get_detected_addons() as $addon ) {
								if ( $addon->is_product_of_this_addon( intval( $v ) ) ) {
									$v = $addon->get_product_name( intval( $v ) );
								}
							}
						}
					}

					// Translate underscores into spaces
					if ( empty( $condition['key'] ) || ! in_array( $condition['key'], array( 'content_type' ) ) ) {
						$v = str_replace( '_', ' ', $v );
					}

					$v = ! empty( $v ) ? $highlight_before . $v . $highlight_after : '';
				}

				// Format array list
				if ( 1 == $values_count ) {
					$value = $condition['value'][0];
				}

				else {
					$last_condition = array_pop( $condition['value'] );
					$value = implode( ', ', $condition['value'] ) . ' ' . __( 'or', 'pixel-caffeine' ) . ' ' . $last_condition;
				}

				// Wrap the value list with some text defined in some cases
				$value = sprintf( $value_wrapper, $value );

			}

			// Define the parameter, for cases not covered above
			if ( empty( $parameter ) ) {
				if ( 'attributes' == $rule['event_type'] && 'language' == $rule['event'] ) {
					$parameter = __( 'browser language', 'pixel-caffeine' );

				} elseif ( 'blog' == $rule['event_type'] && 'custom_fields' == $rule['event'] && '[[any]]' != $condition['key'] ) {
					$parameter = sprintf( __( '%s custom field', 'pixel-caffeine' ), $highlight_before . $condition['key'] . $highlight_after );

				} elseif ( 'ecommerce' == $rule['event_type'] ) {
					$parameter = sprintf( __( '%s parameter', 'pixel-caffeine' ), $highlight_before . $condition['key'] . $highlight_after );

				} elseif ( ! empty( $condition['key'] ) ) {
					$parameter = $condition['key'];
				}
			}

			// Set by default the statement to use for this row, it could be changed in some cases above
			if ( empty( $statement ) ) {
				if ( isset( $translate_words[ $rule['event_type'] ][ $rule['event'] ][ $condition['operator'] ] ) ) {
					$statement = ' ' . $translate_words[ $rule['event_type'] ][ $rule['event'] ][ $condition['operator'] ];
				} elseif ( isset( $condition['key'] ) && 'content_ids' == $condition['key'] && isset( $translate_words[ $rule['event_type'] ][ $rule['event'] ]['specific'] ) ) {
					$statement = $translate_words[ $rule['event_type'] ][ $rule['event'] ]['specific'];

					if ( is_array( $statement ) ) {
						$statement = $statement[ $values_count <= 1 ? 'singular' : 'plural' ];
					}

					$statement = ' ' . $statement;
				} else {
					$statement = ' ' . $translate_words[ $condition['operator'] ];
				}
			}

			if ( empty( $value ) ) {
				$value = __( 'nothing', 'pixel-caffeine' );
			}

			// Decide what statement use
			if ( ! empty( $condition['key'] ) && 'content_ids' == $condition['key'] ) {
				$prepend = sprintf( trim( $statement ), $parameter, $value ) . ' ';
			} else {
				$conditions[] = sprintf( trim( $statement ), $parameter, $value );
			}
		}

		// Set some statement to prepend to above generated
		if ( empty( $prepend ) && isset( $translate_words[ $rule['event_type'] ][ $rule['event'] ]['generic'] ) ) {
			$prepend = $translate_words[ $rule['event_type'] ][ $rule['event'] ]['generic'] . ' ';
		}

		// Add conditions if any
		if ( ! empty( $prepend ) && ! empty( $conditions ) && ! in_array( $rule['event'], array( 'Search' ) ) ) {
			$prepend .= __( 'with', 'pixel-caffeine' ) . ' ';
		}

		// Save final text
		if ( empty( $conditions ) ) {
			$final = '';
		} elseif ( 1 == count( $conditions ) ) {
			$final = $conditions[0];
		} else {
			$last_condition = array_pop( $conditions );
			$final = implode( ', ', $conditions ) . ' ' . __( 'and', 'pixel-caffeine' ) . ' ' . $last_condition;
		}

		// Save final statement
		return trim( $prepend . $final );
	}

	/**
	 * Get a list of all rule formatted for human reading to print out on frontend
	 *
	 * @param string $highlight_before What put before the highlighted word
	 * @param string $highlight_after What put after the highlighted word
	 *
	 * @return array
	 */
	public function get_human_rule_list( $highlight_before = '[', $highlight_after = ']' ) {
		$filters = $this->get_rule();

		// Change each condition into text readable
		foreach ( $filters as $filter_id => &$rule ) {

			// Don't add any statement for URL filter
			if ( 'url' == $rule['event_type'] && 'url' == $rule['event'] ) {
				unset( $filters[ $filter_id ] );
				continue;
			}

			// Save final statement
			$rule = $this->get_human_filter( $rule, $highlight_before, $highlight_after );
		}

		return array_filter( $filters );
	}

	/**
	 * Set the size of audience
	 *
	 * @param int $size
	 *
	 * @return int
	 */
	public function set_size( $size ) {
		return $this->_data['approximate_count'] = absint( $size );
	}

	/**
	 * Add new filter to rules already existing with AND condition
	 *
	 * @param array $rule
	 */
	public function add_filter( array $rule ) {

		// Remove conditions with emptu value and key
		foreach ( $rule['conditions'] as $k => $condition ) {
			if (
				isset( $condition['key'] ) && empty( $condition['key'] )
				|| ! isset( $condition['key'] ) && empty( $condition['value'] )
			) {
				unset( $rule['conditions'][ $k ] );
			}
		}

		$this->set_rule( array_merge( $this->get_rule(), array( $rule ) ) );
	}

	/**
	 * Get error message if any
	 *
	 * @param $field
	 *
	 * @return bool|mixed
	 */
	public function get_error( $field ) {
		return AEPC_Admin_Notices::get_notices( 'error', 'ca_' . $field );
	}

	/**
	 * Return if the CA have some errors
	 */
	public function have_errors() {
		return AEPC_Admin_Notices::has_notice( 'error' );
	}

	/**
	 * Get all error messages
	 *
	 * @return array
	 */
	public function get_errors() {
		return AEPC_Admin_Notices::get_notices( 'error' );
	}

	/**
	 * Delete an error for a field
	 *
	 * @param $field
	 */
	public function remove_error( $field ) {
		AEPC_Admin_Notices::remove_notices( 'error', 'ca_' . $field );

		if ( 'rule' == $field ) {
			AEPC_Admin_Notices::remove_notices( 'error', 'ca_include_url' );
			AEPC_Admin_Notices::remove_notices( 'error', 'ca_exclude_url' );
		}
	}

	/**
	 * Reset all errors
	 */
	public function reset_errors() {
		AEPC_Admin_Notices::remove_notices( 'error' );
	}

}
