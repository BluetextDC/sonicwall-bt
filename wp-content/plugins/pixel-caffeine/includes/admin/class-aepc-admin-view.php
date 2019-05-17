<?php

use PixelCaffeine\ProductCatalog\Admin\Metaboxes;
use PixelCaffeine\ProductCatalog\Configuration;
use PixelCaffeine\ProductCatalog\ConfigurationDefaults;
use PixelCaffeine\ProductCatalog\DbProvider;
use PixelCaffeine\ProductCatalog\ProductCatalogManager;
use PixelCaffeine\ProductCatalog\ProductCatalogs;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @class AEPC_Admin_General
 */
class AEPC_Admin_View {

	/** @var string The slug of page */
	public $id = null;

	/** @var array All settings arguments of page */
	public $settings = array();

	/** @var array All script templates register that must be printed out with footer scripts */
	protected $script_templates = array();

	/** @var ProductCatalogs */
	protected $product_catalogs_service;

	/** @var  ConfigurationDefaults */
	protected $feedDefaults;

	/**
	 * AEPC_Admin_View constructor.
	 *
	 * @param $id
	 */
	public function __construct( $id ) {
		$this->id = $id;

		// Get settings from file
		$this->get_settings();

		// Add hooks
		add_action( 'admin_print_footer_scripts', array( $this, 'print_script_templates' ) );
	}

	/**
	 * Return the page title
	 *
	 * @return string
	 */
	public function get_title() {
		$titles = AEPC_Admin_Menu::get_page_titles();
		return AEPC_Admin::PLUGIN_NAME . ' - ' . $titles[ $this->id ];
	}

	/**
	 * Print out the page title
	 */
	public function the_title() {
		echo self::get_title();
	}

	/**
	 * Get settings of tab
	 */
	public function get_settings() {
		if ( ! empty( $this->settings ) ) {
			return $this->settings;
		}

		if ( file_exists( dirname(__FILE__) . '/settings/' . $this->id . '.php' ) ) {
			$this->settings = include_once( 'settings/' . $this->id . '.php' );
		}

		return $this->settings;
	}

	/**
	 * AEPC_Admin_General Constructor.
	 */
	public function output() {
		ob_start();
		AEPC_Admin::get_template( $this->id . '.php', array( 'page' => $this ) );
		$output = ob_get_clean();

		if ( empty( $output ) ) {
			wp_redirect( add_query_arg( 'page', AEPC_Admin_Menu::$page_id, admin_url() ) );
			exit();
		}

		echo $output;
	}

	// HELPERS

	/**
	 * Return the proper string for field name for the option
	 *
	 * @param $option_id
	 *
	 * @return string
	 */
	public function get_field_name( $option_id ) {
		return $option_id;
	}

	/**
	 * Print the proper string for field name for the option
	 *
	 * @param $option_id
	 *
	 * @return string
	 */
	public function field_name( $option_id ) {
		echo esc_attr( $this->get_field_name( $option_id ) );
	}

	/**
	 * Return the proper string for field id for the option
	 *
	 * @param $option_id
	 *
	 * @return string
	 */
	public function get_field_id( $option_id ) {
		return $option_id;
	}

	/**
	 * Print the proper string for field id for the option
	 *
	 * @param $option_id
	 *
	 * @return string
	 */
	public function field_id( $option_id ) {
		echo esc_attr( $this->get_field_id( $option_id ) );
	}

	/**
	 * Print out the classes for the option, by checking if 'active' class necessary and also has-error.
	 *
	 * Usually printed out on .form-group element, that wraps the field elements and not only input element
	 *
	 * @param string $option_id
	 * @param array|string $classes
	 */
	public function field_class( $option_id, $classes = '' ) {
		if ( ! is_array( $classes ) ) {
			$classes = array( $classes );
		}

		// Add active class
		if ( '' !== $this->get_value( $option_id ) && ! $this->has_error( $option_id ) ) {
			$classes[] = 'active';
		}

		// Add has error class
		if ( $this->has_error( $option_id ) ) {
			$classes[] = 'has-error';
		}

		// Remove some empty value
		$classes = array_filter( $classes );

		// Print out only if there is some class to print
		if ( ! empty( $classes ) ) {
			echo ' ' . implode( ' ', $classes );
		}

	}

	/**
	 * Print 'has-error' class if any error occurred in the field
	 *
	 * @param $option_id
	 *
	 * @return bool
	 */
	public function has_error( $option_id ) {
		return AEPC_Admin_Notices::has_notice( 'error', $option_id );
	}

	/**
	 * Print the error of field
	 *
	 * @param $option_id
	 * @param string $before
	 * @param string $after
	 * @param string $separator
	 */
	public function print_field_error( $option_id, $before = '', $after = '', $separator = ' ' ) {
		if ( ! AEPC_Admin_Notices::has_notice( 'error', $option_id ) ) {
			return;
		}

		echo $before . implode( $separator, wp_list_pluck( AEPC_Admin_Notices::get_notices( 'error', $option_id ), 'text' ) ) . $after;

		// Reset error messages
		AEPC_Admin_Notices::remove_notices( 'error', $option_id );
	}

	/**
	 * Print out the main notices of page
	 */
	public function print_notices() {
		$notices = AEPC_Admin_Notices::get_notices( 'any', 'main' );

		foreach ( $notices as $notice_type => $ids ) {
			foreach ( $ids as $message_id => $messages ) {
				foreach ( $messages as $message ) {
					$this->get_template_part( 'notices/' . $notice_type, array(
						'message' => $message['text'],
						'dismiss_action' => $message['dismiss_action']
					) );
				}
			}
		}

		// Reset error messages
		AEPC_Admin_Notices::remove_notices( 'any', 'main' );
	}

	/**
	 * Print a notice defined on fly by parameters
	 *
	 * @param $notice_type
	 * @param $message
	 */
	public function print_notice( $notice_type, $message ) {
		if ( ! is_array( $message ) ) {
			$message = array(
				'text' => $message,
				'dismiss_action' => ''
			);
		}

		$this->get_template_part( 'notices/' . $notice_type, array(
			'message' => $message['text'],
			'dismiss_action' => $message['dismiss_action']
		) );
	}

	/**
	 * Print the classes for the loading status if the two values in parameter are the same
	 *
	 * @param $value
	 * @param bool $check
	 */
	public function loading_class( $value, $check = true ) {
		echo $value === $check ? ' loading-data loading-box' : '';
	}

	/**
	 * Return the value for option from database. If not exists, return the default one.
	 *
	 * @param $option_id
	 *
	 * @return string
	 */
	public function get_value( $option_id ) {
		if ( ! isset( $this->settings[ $option_id ] ) ) {
			return '';
		}

		if ( isset( $_POST[ self::get_field_name( $option_id ) ] ) && $this->has_error( $option_id ) ) {
			$value = $_POST[ self::get_field_name( $option_id ) ];

		} else {
			$value = get_option( $option_id, $this->settings[ $option_id ]['default'] );

			if ( is_array( $value ) ) {
				$value = implode( ',', $value );
			}
		}

		return (string) $value;
	}

	/**
	 * Return a field value if it exists in post request, used for the add/edit forms
	 *
	 * @param $field
	 * @param string $default
	 *
	 * @return string|array
	 */
	public function get_field_value( $field, $default = '' ) {
		if ( empty( $_POST ) ) {
			return $default;
		}

		if ( isset( $_POST[ $field ] ) ) {
			$value = $_POST[ $field ];
		} else {
			$value = 'no';
		}

		// If ca_rule, return a specific structure
		if ( 'ca_rule' == $field ) {

			if ( ! is_array( $value ) || empty( $value ) ) {
				$value = array();
			}

			foreach ( $value as $k => $v ) {
				if ( ! isset( $value[ $v['main_condition'] ] ) ) {
					$value[ $v['main_condition'] ] = array();
				}

				$value[ $v['main_condition'] ][] = $v;
				unset( $value[ $k ] );
			}
		}

		return $value;
	}

	/**
	 * Print the HTML formatted list of options for a select view
	 *
	 * @param $option_id
	 * @param mixed $selected
	 */
	public function select_options_of( $option_id, $selected = false ) {
		if ( ! isset( $this->settings[ $option_id ]['options'] ) ) {
			return;
		}

		foreach ( $this->settings[ $option_id ]['options'] as $value => $label ) {
			?><option value="<?php echo esc_attr( $value ) ?>"<?php selected( $value, $selected ) ?>><?php echo esc_html( $label ) ?></option><?php
		}
	}

	/**
	 * Return the current tab
	 */
	public function get_current_tab() {
		return isset( $_GET['tab'] ) ? $_GET['tab'] : 'dashboard';
	}

	/**
	 * Load a template part
	 *
	 * @param $part
	 * @param array $args
	 */
	public function get_template_part( $part, $args = array() ) {
		ob_start();
		AEPC_Admin::get_template( 'parts/' . $part . '.php', wp_parse_args( $args, array( 'page' => $this ) ) );
		echo ob_get_clean();
	}

	/**
	 * Load a template part
	 *
	 * @param $part
	 * @param array $args
	 * @param bool $echo
	 *
	 * @return mixed|string
	 */
	public function get_form_fields( $part, $args = array(), $echo = true ) {
		$args = wp_parse_args( $args, array( 'page' => $this, 'action' => 'add' ) );

		ob_start();
		AEPC_Admin::get_template( 'parts/forms/' . $part . '.php', $args );
		$output = ob_get_clean();

		if ( 'add' == $args['action'] ) {
			$output = preg_replace( '/#>\n*\s*\t*.*\n*\s*\t*<#/m', '', $output );
			$output = preg_replace( '/<#\n*\s*\t*.*\n*\s*\t*#>/m', '', $output );
			$output = preg_replace( '/\{\{? index \}?\}?/', '0', $output );
			$output = preg_replace( '/\{\{?\{? data.pass_advanced_params \}?\}?\}?/', 'no', $output );
			$output = preg_replace( '/\{\{?\{?[^}]*\}\}?\}?/', '', $output );
		}

		if ( $echo ) {
			echo $output;
		}

		return $output;
	}

	/**
	 * Return an array with all standard events and with all fields the user can define for each standard event
	 */
	public function get_standard_events() {
		return AEPC_Track::$standard_events;
	}

	/**
	 * Return the content_type values
	 */
	public function get_content_types() {
		$content_types = array();

		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
			$content_types[ $post_type->name ] = $post_type->labels->singular_name;
		}

		return $content_types;
	}

	/**
	 * Return the URL of current view for actions and others
	 *
	 * @param array $query_str Query string parameters to add to the url
	 *
	 * @return string
	 */
	public function get_view_url( $query_str = array() ) {
		return add_query_arg( wp_parse_args( $query_str, array(
			'page' => AEPC_Admin_Menu::$page_id,
			'tab' => $this->id
		) ), admin_url( 'admin.php' ) );
	}

	public function get_pixel_status() {
		$pixel_id = PixelCaffeine()->get_pixel_id();
		$status = '<em>' . __( 'No pixel set', 'pixel-caffeine' ) . '</em>';

		if ( ! empty( $pixel_id ) ) {
			$status = $pixel_id;

			if ( AEPC_Admin::$api->is_logged_in() ) {
				$status .= ' - ' . __( 'Automatic facebook connection', 'pixel-caffeine' );
			} else {
				$status .= ' - ' . __( 'Manual facebook connection', 'pixel-caffeine' );
			}
		}

		return $status;
	}

	/**
	 * Return an array with three arguments for code showing of fbq javascript function
	 *
	 * @param array $track The array with all event data
	 *
	 * @return array
	 */
	public function get_track_code( $track ) {
		$track = wp_parse_args( $track, array(
			'event' => '',
			'params' => array(),
			'custom_params' => array()
		) );

		$code = AEPC_Track::track( $track['event'], $track['params'], $track['custom_params'] );
		$code = preg_replace( '/aepc_extend_args\((\{[^\{]*\})\)/', '$1', $code );
		$code = str_replace( ', {}', '', $code );

		return $code;
	}

	/**
	 * Get the list of supported addons
	 *
	 * @return AEPC_Edd_Addon_Support[]|AEPC_Woocommerce_Addon_Support[]
	 */
	public function get_addons_supported() {
		return AEPC_Addons_Support::get_supported_addons();
	}

	/**
	 * Get the supported addon active
	 *
	 * @return AEPC_Edd_Addon_Support[]|AEPC_Woocommerce_Addon_Support[]
	 */
	public function get_addons_detected() {
		return AEPC_Addons_Support::get_detected_addons();
	}

	/**
	 * Get the detected addon slugs
	 *
	 * @return array
	 */
	public function get_addons_detected_select2() {
		$addons = array();
		foreach ( $this->get_addons_detected() as $addon ) {
			$addons[ $addon->get_slug() ] = $addon->get_name();
		}
		return $this->array_to_select2( $addons );
	}

	/**
	 * Return the array of conversions paged
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_conversions( $args = array() ) {

		/**
		 * Allowed arguments to configure pagination
		 *
		 * @var int $per_page
		 * @var int $paged
		 * @var string $order 'newest' or 'oldest'
		 */
		extract( wp_parse_args( $args, array(
			'per_page' => 5,
			'paged' => isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1,
			'order' => 'newest'
		) ) );

		$conversions = AEPC_Track::get_conversions_events();

		// reverse order if should be shown from 'newest'
		if ( 'newest' == $order ) {
			$conversions = array_reverse( $conversions, true );
		}

		$conversions = array_slice( $conversions, ( $per_page * ( $paged - 1 ) ), $per_page, true );

		return $conversions;
	}

	/**
	 * Return the number of conversion events defined by user
	 *
	 * @return int
	 */
	public function get_conversions_count() {
		return count( AEPC_Track::get_conversions_events() );
	}

	/**
	 * Print out the number of conversion events defined by user, setting also the label
	 *
	 * @param string $single_label Define %d to replace the number
	 * @param string $plural_label Define %d to replace the number
	 */
	public function conversions_count( $single_label = '', $plural_label = '' ) {
		$num = self::get_conversions_count();

		if ( ! empty( $single_label ) && ! empty( $plural_label ) ) {
			$num = sprintf( 1 == $num ? $single_label : $plural_label, $num );
		}

		echo $num;
	}

	/**
	 * Return the array of conversions paged
	 *
	 * @param array $args
	 *
	 * @return AEPC_Admin_CA[]
	 */
	public function get_audiences( $args = array() ) {
		$audiences = AEPC_Admin_CA_Manager::get_audiences( wp_parse_args( $args, array(
			'per_page' => 5,
			'paged' => isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1,
			'orderby' => 'date',
			'order' => 'DESC'
		) ) );

		return $audiences;
	}

	/**
	 * Generate a pagination
	 *
	 * @param $nitems
	 * @param array $args
	 *
	 * @return string
	 */
	public function get_pagination( $nitems, $args = array() ) {

		/**
		 * Allowed arguments to configure pagination
		 *
		 * @var int $per_page
		 * @var int $paged
		 * @var string $list_wrap
		 * @var string $item_wrap
		 * @var string $item_wrap_active
		 * @var string $item_wrap_disabled
		 * @var string $url_param
		 * @var int $visible_pages
		 */
		extract( wp_parse_args( $args, array(
			'per_page' => 5,
			'paged' => ! empty( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1,
			'list_wrap' => '<ul>%1$s</ul>',
			'item_wrap' => '<li>%1$s</li>',
			'item_wrap_active' => '<li class="active">%1$s</li>',
			'item_wrap_disabled' => '<li class="disabled">%1$s</li>',
			'url_param' => 'paged',
			'visible_pages' => 5
		) ) );

		// Init
		$pages_links = array();

		$last       = ceil( $nitems / $per_page );

		if ( $last == 1 ) {
			return null;
		}

		$start      = ( ( $paged - $visible_pages ) > 0 ) ? $paged - $visible_pages : 1;
		$end        = ( ( $paged + $visible_pages ) < $last ) ? $paged + $visible_pages : $last;

		// Previous link
		if ( $paged > 1 ) {
			$pages_links[] = sprintf( $item_wrap, '<a href="' . $this->get_view_url( 'paged=' . ( $paged - 1 ) ) . '">&laquo;</a>' );
		}

		// Hide pages out of range
		if ( $start > 1 ) {
			$pages_links[] = sprintf( $item_wrap, '<a href="' . $this->get_view_url( 'paged=1' ) . '">1</a>' );

			if ( $start > 2 ) {
				$pages_links[] = sprintf( $item_wrap_disabled, '<span>...</span>' );
			}
		}

		// Get page links
		for ( $i = $start ; $i <= $end; $i++ ) {
			$pages_links[] = sprintf( ( $paged == $i ) ? $item_wrap_active : $item_wrap, '<a href="' . $this->get_view_url( 'paged=' . $i ) . '">' . $i . '</a>' );
		}

		// Hide pages out of range
		if ( $end < $last ) {
			if ( $end < $last - 1 ) {
				$pages_links[] = sprintf( $item_wrap_disabled, '<span>...</span>' );
			}

			$pages_links[] = sprintf( $item_wrap, '<a href="' . $this->get_view_url( 'paged=' . $last ) . '">' . $last . '</a>' );
		}

		// Next link
		if ( $paged < $last ) {
			$pages_links[] = sprintf( $item_wrap, '<a href="' . $this->get_view_url( 'paged=' . ( $paged + 1 ) ) . '">&raquo;</a>' );
		}

		// Wrap list
		$html = sprintf( $list_wrap, implode( '', $pages_links ) );

		return $html;
	}

	/**
	 * Return the pagination for conversions table
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function conversions_pagination( $args = array() ) {
		echo $this->get_pagination( count( AEPC_Track::get_conversions_events() ), $args );
	}

	/**
	 * Return the pagination for conversions table
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function audiences_pagination( $args = array() ) {
		echo $this->get_pagination( AEPC_Admin_CA_Manager::get_all_audiences_count(), $args );
	}

	/**
	 * Return the options list for the currency dropdown
	 *
	 * @param string $selected If some option must be selected
	 *
	 * @return string
	 */
	public function get_currency_dropdown( $selected = '' ) {
		$options = array();

		foreach ( AEPC_Currency::get_currencies() as $currency => $args ) {
			$selected = $selected === $currency ? ' selected="selected"' : '';
			$options[] = sprintf( '<option value="%s"%s>%s</option>', esc_attr( $currency ), $selected, esc_html( $args->symbol . ' (' . $args->name . ')' ) );
		}

		return implode( "\n", $options );
	}

	/**
	 * Print each field value for the conversion, useful for edit modal
	 *
	 * @param $id
	 */
	public function conversion_data_values( $id ) {
		$events = AEPC_Track::get_conversions_events();

		// Nothing if not existing
		if ( empty( $events[ $id ] ) ) {
			return;
		}

		// Init
		$data = $events[ $id ];

		// Integrate not existing parameters
		$data = wp_parse_args( $data, array(
			'name'          => '',
			'trigger'       => '',
			'url'           => '',
			'css'           => '',
			'event'         => '',
			'params'        => array(),
			'custom_params' => array(),
		) );

		// Add event ID to add it on the form as input hidden
		$data = array_merge( array( 'event_id' => $id ), $data );

		if ( AEPC_Track::is( 'custom', $data['event'] ) ) {
			$data['custom_event_name'] = $data['event'];
			$data['event'] = 'CustomEvent';
		} else {
			$data['custom_event_name'] = '';
		}

		$data['pass_advanced_params'] = empty( $data['params'] ) && empty( $data['custom_params'] ) ? 'no' : 'yes';

		// Fix arrays
		foreach ( $data['params'] as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}
		}

		// Format custom params
		foreach ( $data['custom_params'] as $key => &$value ) {
			$value = array( 'key' => $key, 'value' => $value );
		}
		$data['custom_params'] = array_values( $data['custom_params'] );

		// If any custom params, add an empty one useful on frontend
		if ( empty( $data['custom_params'] ) ) {
			$data['custom_params'][] = array( 'key' => '', 'value' => '' );
		}

		// Generate output
		echo ' data-config="' . esc_attr( json_encode( $data ) ) . '"';
	}

	/**
	 * Print each field value for the custom audience, useful for clone and edit modal
	 *
	 * @param $id
	 */
	public function audience_data_values( $id ) {
		$ca = new AEPC_Admin_CA( $id );

		if ( ! $ca->exists() ) {
			return;
		}

		$data = array(
			'id' => $ca->get_id(),
			'name' => $ca->get_name(),
			'description' => $ca->get_description(),
			'retention' => $ca->get_retention(),
			'include_url' => $ca->get_rule( 'include_url' ),
			'exclude_url' => $ca->get_rule( 'exclude_url' ),
			'include_url_condition' => $ca->get_url_condition( 'include_url' ),
			'exclude_url_condition' => $ca->get_url_condition( 'exclude_url' ),
			'include_filters' => $ca->get_filters( 'include' ),
			'exclude_filters' => $ca->get_filters( 'exclude' ),
		);

		// Add statement for each rule
		foreach ( array( 'include', 'exclude' ) as $condition ) {
			foreach ( $data[ $condition . '_filters' ] as &$rule ) {
				$rule['statement'] = $ca->get_human_filter( $rule, '<em>', '</em>' );
			}
		}

		// Generate output
		echo ' data-config="' . esc_attr( json_encode( $data ) ) . '"';
	}

	/**
	 * Print out a list of <option> for the available operators for ca filter
	 *
	 * @param array $include
	 * @param string $selected
	 */
	public function ca_operators_list( $include = array(), $selected = '' ) {
		$operators = array(
			'i_contains' => __( 'Contains', 'pixel-caffeine' ),
			'i_not_contains' => __( 'Not Contains', 'pixel-caffeine' ),
			'eq' => __( 'Is', 'pixel-caffeine' ),
			'neq' => __( 'Not equal', 'pixel-caffeine' ),
			'lt' => __( 'Less than', 'pixel-caffeine' ),
			'lte' => __( 'Less than or equal to', 'pixel-caffeine' ),
			'gt' => __( 'Greater than or equal to', 'pixel-caffeine' ),
			'gte' => __( 'Greater than or equal to', 'pixel-caffeine' ),
		);

		// Intersect with parameter if you want specify what return exactly
		if ( ! empty( $include ) ) {
			$operators = array_intersect_key( $operators, array_flip( $include ) );
		}

		// Print out options
		foreach ( $operators as $operator => $label ) {
			?><option value="<?php echo esc_attr( $operator ) ?>"<?php selected( $operator, $selected ) ?>><?php echo esc_html( $label ) ?></option><?php
		}
	}

	/**
	 * Print out a list of <option> for the available operators for ca filter
	 *
	 * @param string $selected
	 */
	public function taxonomies_dropdown( $selected = '' ) {
		$taxonomies = get_taxonomies( array(
			'public'   => true
		), 'objects' );

		// Print out options
		foreach ( $taxonomies as $taxonomy => $the ) {

			// system taxes to skip
			$skip_categories = array(
				'nav_menu',
				'link_category',
				'post_format',
				'post_tag',
				'product_tag',
				'product_shipping_class',
			);

			if( in_array( $the->name, $skip_categories ) )
				continue;

			// Exception for WooCommerce Product category label
			if ( 'product_cat' === $taxonomy ) {
				$the->labels->singular_name = __( 'Product Category', 'pixel-caffeine' );
			}

			?><option value="tax_<?php echo esc_attr( $taxonomy ) ?>"<?php selected( $taxonomy, $selected ) ?>><?php echo esc_html( $the->labels->singular_name ) ?></option><?php
		}
	}

	/**
	 * Print out a list of <option> for the available operators for ca filter
	 *
	 * @param string $selected
	 */
	public function tags_dropdown( $selected = '' ) {
		$taxonomies = get_taxonomies( array(
			'public'   => true
		), 'objects' );

		// Print out options
		foreach ( $taxonomies as $taxonomy => $the ) {

			// system taxes to skip
			$print_only = array(
				'post_tag',
				'product_tag'
			);

			if( ! in_array( $the->name, $print_only ) )
				continue;

			// Exception for WooCommerce Product category label
			if ( 'product_tag' === $taxonomy ) {
				$the->labels->singular_name = __( 'Product Tag', 'pixel-caffeine' );
			}

			?><option value="tax_<?php echo esc_attr( $taxonomy ) ?>"<?php selected( $taxonomy, $selected ) ?>><?php echo esc_html( $the->labels->singular_name ) ?></option><?php
		}
	}

	/**
	 * Print out a list of <option> for the available post types
	 *
	 * @param string $selected
	 */
	public function post_types_dropdown( $selected = '' ) {
		$post_types = get_post_types( array(
			'public'   => true
		), 'objects' );

		// Print out options
		foreach ( $post_types as $post_type => $the ) {

			// system taxes to skip
			$print_only = array(
				'attachment',
				'page'
			);

			if( in_array( $the->name, $print_only ) )
				continue;

			?><option value="<?php echo esc_attr( $post_type ) ?>"<?php selected( $post_type, $selected ) ?>><?php echo esc_html( $the->labels->singular_name ) ?></option><?php
		}
	}

	/**
	 * Register script template that will be printed out with footer scripts
	 *
	 * @param $id
	 * @param $html
	 */
	public function register_script_template( $id, $html ) {
		if ( isset( $this->script_templates[ $id ] ) ) {
			return;
		}

		$this->script_templates[ $id ] = $html;
	}

	/**
	 * Print out the registered script templates with other footer scripts
	 */
	public function print_script_templates() {
		foreach ( $this->script_templates as $id => $html ) {
			?>

			<script type="text/html" id="tmpl-<?php echo $id ?>"><?php echo $html ?></script>

			<?php
		}
	}

	/**
	 * PRODUCT CATALOGS
	 */

	/**
	 * Detect if product catalog is been created or not
	 *
	 * @return bool
	 */
	public function is_product_catalog_created() {
		return AEPC_Admin::$product_catalogs_service->is_product_catalog_created();
	}

	/**
	 * Return the product catalog created if any
	 *
	 * @return null|ProductCatalogManager
	 */
	public function get_product_catalog() {
		$product_catalogs = AEPC_Admin::$product_catalogs_service->get_product_catalogs();
		return ! empty( $product_catalogs ) ? array_shift( $product_catalogs ) : null;
	}

	/**
	 * Get the ID attribute of the field of a product category configuration field
	 *
	 * @param string $group
	 * @param string $field_name
	 * @param string $subkey
	 *
	 * @return string
	 */
	public function get_feed_field_id( $group, $field_name = '', $subkey = '' ) {
		$field_name = $field_name ? '_' . $field_name : '';
		$subkey = $subkey ? '_' . $subkey : '';
		return sprintf( 'product_catalog_%s%s%s', $group, $field_name, $subkey );
	}

	/**
	 * Print the get_feed_field_id value
	 *
	 * @param $group
	 * @param string $field_name
	 * @param string $subkey
	 */
	public function feed_field_id( $group, $field_name = '', $subkey = '' ) {
		echo esc_attr( $this->get_feed_field_id( $group, $field_name, $subkey ) );
	}

	/**
	 * Get the name attribute of the field of a product category configuration field
	 *
	 * @param string $group
	 * @param string $field_name
	 * @param string $subkey
	 *
	 * @return string
	 */
	public function get_feed_field_name( $group, $field_name = '', $subkey = '' ) {
		$group = $group ? sprintf( '[%s]', $group ) : '';
		$field_name = $field_name ? sprintf( '[%s]', $field_name ) : '';
		$subkey = $subkey ? sprintf( '[%s]', $subkey ) : '';

		return sprintf( 'product_catalog%s%s%s', $group, $field_name, $subkey );
	}

	/**
	 * Print the get_feed_field_name value
	 *
	 * @param $group
	 * @param string $field_name
	 * @param string $subkey
	 */
	public function feed_field_name( $group, $field_name = '', $subkey = '' ) {
		echo esc_attr( $this->get_feed_field_name( $group, $field_name, $subkey ) );
	}

	/**
	 * Get the ConfigurationDefaults instance
	 *
	 * @return ConfigurationDefaults
	 *
	 */
	protected function get_feed_defaults() {
		if ( ! $this->feedDefaults instanceof ConfigurationDefaults ) {
			$this->feedDefaults = new ConfigurationDefaults;
		}

		return $this->feedDefaults;
	}

	/**
	 * Get the name attribute of the field of a product category configuration field
	 *
	 * @param null|ProductCatalogManager $product_catalog
	 * @param string $group
	 * @param string $field_name
	 * @param string $subkey
	 *
	 * @return mixed
	 */
	public function get_feed_field_value( $product_catalog, $group, $field_name = '', $subkey = '' ) {
		$value = '';

		if ( $product_catalog ) {

			switch ( $group ) {
				case Configuration::OPTION_FILE_NAME :
					$value = $product_catalog->get_entity()->getId();
					break;
				case Configuration::OPTION_FEED_FORMAT :
					$value = $product_catalog->get_entity()->getFormat();
					break;
				case Configuration::OPTION_FEED_CONFIG :
					$value = $product_catalog->configuration()->get( $field_name );
					if ( ! empty( $subkey ) ) {
						$value = isset( $value[ $subkey ] ) ? $value[ $subkey ] : '';
					}
					break;
			}

		}

		// Defaults
		if ( empty( $value ) ) {

			switch ( $group ) {
				case Configuration::OPTION_FILE_NAME :
					$value = $this->get_feed_defaults()->get( Configuration::OPTION_FILE_NAME );
					break;
				case Configuration::OPTION_FEED_FORMAT :
					$value = $this->get_feed_defaults()->get( Configuration::OPTION_FEED_FORMAT );
					break;
				case Configuration::OPTION_FEED_CONFIG :
					$value = $this->get_feed_defaults()->get( $field_name );
					if ( ! empty( $subkey ) ) {
						$value = isset( $value[ $subkey ] ) ? $value[ $subkey ] : '';
					}
					break;
			}

		}

		return $value;
	}

	/**
	 * Print the get_feed_field_value value
	 *
	 * @param null|ProductCatalogManager $product_catalog
	 * @param $group
	 * @param string $field_name
	 * @param string $subkey
	 */
	public function feed_field_value( $product_catalog, $group, $field_name = '', $subkey = '' ) {
		$value = $this->get_feed_field_value( $product_catalog, $group, $field_name, $subkey );
		$value = is_array( $value ) ? $this->array_to_commas( $value ) : $value;
		echo esc_html( $value );
	}

	/**
	 * Retrieve all product types from all addons
	 *
	 * @return array
	 */
	public function get_product_types_array() {
		$types = array();
		foreach ( \AEPC_Addons_Support::get_detected_addons() as $addon ) {
			$types = array_merge( $types, array_keys( $addon->get_product_types() ) );
		}
		return $types;
	}

	/**
	 * Retrieve all product categories
	 *
	 * @return array
	 */
	public function get_product_categories_array() {
		$categories = array();
		foreach ( \AEPC_Addons_Support::get_detected_addons() as $addon ) {
			$addon_categories = $addon->get_product_categories();

			foreach ( $addon_categories as $category_id => $category_name ) {
				$categories[] = array(
					'id' => $category_id,
					'text' => $category_name
				);
			}
		}
		return $categories;
	}

	/**
	 * Retrieve all product tags
	 *
	 * @return array
	 */
	public function get_product_tags_array() {
		$tags = array();
		foreach ( \AEPC_Addons_Support::get_detected_addons() as $addon ) {
			$addon_tags = $addon->get_product_tags();

			foreach ( $addon_tags as $tag_id => $tag_name ) {
				$tags[] = array(
					'id' => $tag_id,
					'text' => $tag_name
				);
			}
		}
		return $tags;
	}

	/**
	 * Search for the value of the DB and append the children of last element. If empty, it returns the children of the
	 * first level.
	 *
	 * @param ProductCatalogManager $product_catalog
	 *
	 * @return array
	 */
	public function get_google_categories_dropdown_lists( $product_catalog = null ) {
		$returns           = array();
		$google_categories = AEPC_Admin::$product_catalogs_service->get_google_categories();
		$selected          = $product_catalog ? (array) $this->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_GOOGLE_CATEGORY ) : array();

		// Add last level empty to fill it out with next level not selected yet
		$selected[] = '';

		foreach ( $selected as $i => $term ) {

			// Put all terms of the level and set the selected status
			foreach ( array_keys( $google_categories ) as $google_term ) {
				$returns[ $i ][ $google_term ] = $google_term === $term;
			}

			// Leave only the selected level in google_categories in order to get the next level for the next cycle
			$google_categories = empty( $term ) || ! isset( $google_categories[ $term ] ) ? array() : $google_categories[ $term ];
		}

		return $returns;
	}

	/**
	 * Returns the options for the condition field of a product feed
	 *
	 * @return array
	 */
	public function get_feed_description_options() {
		return array(
			'full-description' => __( 'Full Description', 'pixel-caffeine' ),
			'short-description' => __( 'Short Description', 'pixel-caffeine' ),
		);
	}

	/**
	 * Returns the options for the condition field of a product feed
	 *
	 * @return array
	 */
	public function get_feed_price_options() {
		return array(
			'price-no-tax' => __( 'Price excluding tax', 'pixel-caffeine' ),
			'price-including-tax' => __( 'Price including tax', 'pixel-caffeine' ),
		);
	}

	/**
	 * Returns the options for the condition field of a product feed
	 *
	 * @return array
	 */
	public function get_feed_condition_options() {
		return array(
			'new' => __( 'New', 'pixel-caffeine' ),
			'refurbished' => __( 'Refurbished', 'pixel-caffeine' ),
			'used' => __( 'Used', 'pixel-caffeine' )
		);
	}

	/**
	 * Returns the options for image size available in the website
	 *
	 * @return array
	 */
	public function get_image_size_options() {
		$sizes = array(
			'thumbnail' => __( 'Thumbnail', 'pixel-caffeine' ),
			'medium'    => __( 'Medium', 'pixel-caffeine' ),
			'large'     => __( 'Large', 'pixel-caffeine' ),
			'full'      => __( 'Full Size', 'pixel-caffeine' ),
		);

		foreach ( wp_get_additional_image_sizes() as $name => $size ) {
			$sizes[ $name ] = ucfirst( str_replace( array( '-', '_' ), ' ', $name ) );
		}

		return $sizes;
	}

	public function get_feed_weekly_options() {
		return array(
			AEPC_Facebook_Adapter::FEED_SCHEDULE_WEEK_DAY_SUNDAY => __( 'Every Sunday', 'pixel-caffeine' ),
			AEPC_Facebook_Adapter::FEED_SCHEDULE_WEEK_DAY_MONDAY => __( 'Every Monday', 'pixel-caffeine' ),
			AEPC_Facebook_Adapter::FEED_SCHEDULE_WEEK_DAY_TUESDAY => __( 'Every Tuesday', 'pixel-caffeine' ),
			AEPC_Facebook_Adapter::FEED_SCHEDULE_WEEK_DAY_WEDNESDAY => __( 'Every Wednesday', 'pixel-caffeine' ),
			AEPC_Facebook_Adapter::FEED_SCHEDULE_WEEK_DAY_THURSDAY => __( 'Every Thursday', 'pixel-caffeine' ),
			AEPC_Facebook_Adapter::FEED_SCHEDULE_WEEK_DAY_FRIDAY => __( 'Every Friday', 'pixel-caffeine' ),
			AEPC_Facebook_Adapter::FEED_SCHEDULE_WEEK_DAY_SATURDAY => __( 'Every Saturday', 'pixel-caffeine' ),
		);
	}

	/**
	 * Convert an array key=>value to the format supported by select2
	 *
	 * @param array $haystack
	 *
	 * @return array
	 */
	public function array_to_select2( array $haystack ) {
		foreach ( $haystack as $id => &$value ) {
			$value = array(
				'id' => $id,
				'text' => $value
			);
		}

		return array_values( $haystack );
	}

	public function array_to_commas( array $haystack ) {
		return implode( ',', $haystack );
	}

	/**
	 * Get the time for print
	 *
	 * @param DateTime $date
	 * @param string $what You can return a specific date with a specific format - 't_time' for only time - 'h_time' for human date
	 *
	 * @return array|string
	 */
	public function get_human_date( DateTime $date, $what = '' ) {
		$t_time = $date->format( get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ) );
		$time = $date->format('U');

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

}
