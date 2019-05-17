<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @class AEPC_Admin_CA_Manager
 */
class AEPC_Admin_CA_Manager {

	// Save the number of all records, useful for pagination
	protected static $_audiences_count = false;

	/**
	 * AEPC_Admin_CA_Manager Constructor.
	 */
	public static function init() {

		// Add php notice
		add_action( 'admin_init', array( __CLASS__, 'add_notice_for_facebook_debug' ), 99 );

		// Add custom audience warning for the bug fixed
		add_action( 'admin_init', array( __CLASS__, 'add_custom_audience_bug_warning' ), 99 );
	}

	/**
	 * Add a notice message that inform the user that can't do anything without facebook connection.
	 *
	 * This notice will be shown only on CA page
	 */
	public static function add_notice_for_facebook_debug() {
		if (
			( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
			&& AEPC_Admin::$api->is_debug()
			&& ! empty( $_GET['page'] )
			&& AEPC_Admin_Menu::$page_id == $_GET['page']
			&& ! empty( $_GET['tab'] )
			&& 'custom-audiences' == $_GET['tab']
		) {
			AEPC_Admin_Notices::add_notice( 'info', 'main', __( '<strong>Development mode</strong> via the AEPC_DEACTIVE_FB_REQUESTS constant being defined in wp-config.php or elsewhere. In this mode any facebook api request will be done.', 'pixel-caffeine' ) );
		}
	}

	/**
	 * Add a notice message inform the user that they need to create again the custom audience having taxonomy values
	 * as filter because of a bug fixed.
	 */
	public static function add_custom_audience_bug_warning() {
		if (
			( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
			&& ! empty( $_GET['page'] )
			&& AEPC_Admin_Menu::$page_id == $_GET['page']
			&& ! empty( $_GET['tab'] )
			&& 'custom-audiences' == $_GET['tab']
			&& get_option( 'aepc_show_warning_ca_bug' )
		) {
			AEPC_Admin_Notices::add_notice(
				'warning',
				'main',
				__( '<strong>WARNING</strong>: previously a bug occurred in the the creation of custom audiences. This has now been fixed however any existing custom audiences that had CATEGORY or TAG filters <strong>need to be deleted</strong> and <strong>then created again</strong> to ensure the custom audiences are accurate. Other custom audiences are unaffected.', 'pixel-caffeine' ),
				'ca_bug_warning'
			);
		}
	}

	/**
	 * Save the CA with form data
	 *
	 * @param $post_data
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function save( $post_data ) {

		// Create arguments
		$args = self::ca_post_data_adapter( 'add', $post_data );

		// Init a new CA instance
		$ca = new AEPC_Admin_CA();

		// Save first on Facebook and then on DB
		$ca->create( $args );

		return true;
	}

	/**
	 * Edit the CA with form data
	 *
	 * @param $post_data
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function edit( $post_data ) {

		// Create arguments
		$args = self::ca_post_data_adapter( 'edit', $post_data );

		// Init a new CA instance
		$ca = new AEPC_Admin_CA( $post_data['ca_id'] );

		// Save first on Facebook and then on DB
		$ca->update( $args );

		return true;
	}

	/**
	 * Delete the CA
	 *
	 * @param $ca_id
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function delete( $ca_id ) {

		// Init a new CA instance
		$ca = new AEPC_Admin_CA( $ca_id );

		// Remove first on Facebook and then on DB
		$ca->delete();

		return true;
	}

	/**
	 * Convert the input data from request in structured array to save, used on save and edit actions
	 *
	 * @param string $action 'add' or 'edit'
	 * @param array $post_data The raw data from request
	 *
	 * @return array
	 */
	private static function ca_post_data_adapter( $action, $post_data = array() ) {
		$raw_data = array(
			'name'        => sanitize_text_field( $post_data['ca_name'] ),
			'description' => sanitize_text_field( $post_data['ca_description'] ),
			'prefill'     => ! empty( $post_data['ca_prefill'] ),
			'retention'   => intval( $post_data['ca_retention'] ),
			'include_url' => sanitize_text_field( $post_data['ca_include_url'] ),
			'exclude_url' => sanitize_text_field( $post_data['ca_exclude_url'] ),
			'include_url_condition' => sanitize_text_field( $post_data['ca_include_url_condition'] ),
			'exclude_url_condition' => sanitize_text_field( $post_data['ca_exclude_url_condition'] ),
			'rule'        => isset( $post_data['ca_rule'] ) ? $post_data['ca_rule'] : array(),
		);

		// Add include URL into rule
		if ( ! empty( $raw_data['include_url'] ) ) {
			$raw_data['rule'] = array_merge( array( array(
				'main_condition' => 'include',
				'event_type' => 'url',
				'event' => 'url',
				'conditions' => array(
					array(
						'operator' => $raw_data['include_url_condition'],
						'value' => $raw_data['include_url']
					)
				)
			) ), $raw_data['rule'] );
		}

		// Add exclude URL into rule
		if ( ! empty( $raw_data['exclude_url'] ) ) {
			$raw_data['rule'] = array_merge( array( array(
				'main_condition' => 'exclude',
				'event_type' => 'url',
				'event' => 'url',
				'conditions' => array(
					array(
						'operator' => $raw_data['exclude_url_condition'],
						'value' => $raw_data['exclude_url']
					)
				)
			) ), $raw_data['rule'] );
		}

		// Remove empty conditions
		foreach ( $raw_data['rule'] as $kr => &$rule ) {

			// Force to add conditions key if it doesn't exist
			if ( ! isset( $rule['conditions'] ) ) {
				$rule['conditions'] = array();
			}

			foreach ( $rule['conditions'] as $kc => $condition ) {
				if (
					isset( $condition['key'] ) && empty( $condition['key'] )
					|| ! isset( $condition['key'] ) && empty( $condition['value'] )
				) {
					unset( $raw_data['rule'][ $kr ]['conditions'][ $kc ] );
				}
			}
		}

		$args = array(
			'name' => $raw_data['name'],
			'description' => $raw_data['description'],
			'prefill' => $raw_data['prefill'],
			'retention' => $raw_data['retention'],
			'rule' => $raw_data['rule']
		);

		// Remove prefill field when edit a custom audience
		if ( 'edit' == $action ) {
			unset( $args['prefill'] );
		}

		return $args;
	}

	/**
	 * Save a new CA indentically to other already created
	 *
	 * @param $post_data
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function duplicate( $post_data ) {

		$ca = new AEPC_Admin_CA( $post_data['ca_id'] );

		// Exit if ca is not existing
		if ( ! $ca->exists() ) {
			throw new Exception( __( '<strong>Custom audience cannot duplicated</strong> The cluster you selected does not exist.', 'pixel-caffeine' ), 10 );
		}

		// Exit if no name is defined
		if ( empty( $post_data['ca_name'] ) ) {
			throw new Exception( __( '<strong>Custom audience cannot duplicated</strong> You have to define a name for the new custom audience.', 'pixel-caffeine' ), 11 );
		}

		// Clone
		$ca->duplicate( $post_data['ca_name'] );

		return true;
	}

	/**
	 * Refresh the approximate counts of all custom audiences
	 */
	public static function refresh_approximate_counts() {
		try {
			$audiences = AEPC_Admin::$api->get_audiences( 'approximate_count' );
		} catch ( Exception $e ) {
			return;
		}

		// Set approximate count for each audience on DB
		foreach ( $audiences as $audience ) {
			$ca = new AEPC_Admin_CA();
			$ca->populate_by_fb_id( $audience->id );
			$ca->set_size( $audience->approximate_count );
			$ca->update();
		}
 	}

	/**
	 * Get the audiences saved on Database
	 *
	 * @param array $args
	 *
	 * @return array|mixed|void|AEPC_Admin_CA[]
	 */
	public static function get_audiences( $args = array() ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'aepc_custom_audiences';

		/**
		 * Allowed arguments to configure pagination
		 *
		 * @var int $per_page
		 * @var int $paged
		 * @var string $order 'newest' or 'oldest'
		 */
		$q = wp_parse_args( $args, array(
			'per_page' => 5,
			'paged' => isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1,
			'orderby' => 'ID',
			'order' => 'DESC',
			'return' => 'objects'
		) );

		// Get audiences from db
		$where = '';
		$limits = '';

		// Paging
		if ( $q['paged'] > 0 ) {
			$page = absint($q['paged']);
			if ( !$page )
				$page = 1;

			// If 'offset' is provided, it takes precedence over 'paged'.
			if ( isset( $q['offset'] ) && is_numeric( $q['offset'] ) ) {
				$q['offset'] = absint( $q['offset'] );
				$pgstrt = $q['offset'] . ', ';
			} else {
				$pgstrt = absint( ( $page - 1 ) * $q['per_page'] ) . ', ';
			}
			$limits = 'LIMIT ' . $pgstrt . $q['per_page'];
		}

		// Order
		if ( ! empty( $q['orderby'] ) ) {
			$orderby_array = array();
			if ( is_array( $q['orderby'] ) ) {
				foreach ( $q['orderby'] as $_orderby => $order ) {
					$orderby = addslashes_gpc( urldecode( $_orderby ) );
					$parsed  = "$table_name." . sanitize_key( $orderby );

					if ( ! $parsed ) {
						continue;
					}

					$orderby_array[] = $parsed . ' ' . sanitize_key( $orderby );
				}
				$orderby = implode( ', ', $orderby_array );

			} else {
				$q['orderby'] = urldecode( $q['orderby'] );
				$q['orderby'] = addslashes_gpc( $q['orderby'] );

				foreach ( explode( ' ', $q['orderby'] ) as $i => $orderby ) {
					$parsed = sanitize_key( $orderby );
					// Only allow certain values for safety.
					if ( ! $parsed ) {
						continue;
					}

					$orderby_array[] = $parsed;
				}
				$orderby = implode( ' ' . $q['order'] . ', ', $orderby_array );

				if ( empty( $orderby ) ) {
					$orderby = "$table_name.ID " . $q['order'];
				} elseif ( ! empty( $q['order'] ) ) {
					$orderby .= " {$q['order']}";
				}
			}
		}

		if ( !empty( $orderby ) )
			$orderby = 'ORDER BY ' . $orderby;

		// Query
		$audiences = $wpdb->get_results( "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}aepc_custom_audiences WHERE 1=1 $where $orderby $limits" );

		// Save the number of all records, useful for pagination
		self::$_audiences_count = $wpdb->get_var( 'SELECT FOUND_ROWS()' );

		// Return raw_objects if requested
		if ( 'raw' == $q['return'] ) {
			return $audiences;
		}

		// Get instances
		foreach ( $audiences as &$audience ) {
			$audience = new AEPC_Admin_CA( $audience->ID );
		}

		return $audiences;
	}

	/**
	 * Return the number of all audiences
	 *
	 * @return int
	 */
	public static function get_all_audiences_count() {
		return self::$_audiences_count;
	}

}
