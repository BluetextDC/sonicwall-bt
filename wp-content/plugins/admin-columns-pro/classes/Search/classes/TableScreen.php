<?php

namespace ACP\Search;

use AC;
use AC\Registrable;
use AC\Request;
use ACP;
use ACP\Search\Middleware;

abstract class TableScreen
	implements Registrable {

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var AC\ListScreen
	 */
	protected $list_screen;

	/**
	 * @var Addon
	 */
	protected $addon;

	/**
	 * @param Addon         $addon
	 * @param AC\ListScreen $list_screen
	 * @param Request       $request
	 */
	public function __construct( Addon $addon, AC\ListScreen $list_screen, Request $request ) {
		$this->addon = $addon;
		$this->list_screen = $list_screen;
		$this->request = $request;
	}

	public function register() {

		add_action( 'ac/table_scripts', array( $this, 'scripts' ) );
		add_action( 'ac/table', array( $this, 'register_segment_button' ) );
		add_action( 'admin_footer', array( $this, 'add_segment_modal' ) );
		add_action( 'ac/table/list_screen', array( $this, 'register_query' ) );
	}

	public function register_query() {
		$rules = $this->request->filter( 'ac-rules', array(), FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( ! $rules ) {
			return;
		}

		$bindings = array();

		foreach ( $rules as $rule ) {
			$column = $this->list_screen->get_column_by_name( $rule['name'] );

			if ( ! $column ) {
				continue;
			}

			$comparison = $this->get_comparison( $column );

			if ( ! $comparison ) {
				continue;
			}

			$bindings[] = $comparison->get_query_bindings(
				$rule['operator'],
				new Value( $rule['value'], $rule['value_type'] )
			);
		}

		QueryFactory::create(
			$this->list_screen->get_meta_type(),
			$bindings
		)->register();
	}

	public function scripts() {
		$version = $this->addon->get_version();
		$url = $this->addon->get_url();

		wp_enqueue_style( 'aca-search-table', $url . 'assets/css/table.css', array(), $version );
		wp_enqueue_script( 'aca-search-moment', $url . 'assets/js/moment.min.js', array(), $version );
		wp_enqueue_script( 'aca-search-querybuilder', $url . 'assets/js/query-builder.standalone.min.js', array( 'jquery', 'jquery-ui-datepicker' ), $version );
		wp_enqueue_script( 'aca-search-table', $url . 'assets/js/table.bundle.js', array( 'aca-search-querybuilder', 'wp-pointer' ), $version, true );
		wp_localize_script( 'aca-search-table', 'ac_search', array(
			'rules'   => json_decode( $this->request->get( 'ac-rules-raw' ) ),
			'filters' => $this->get_filters(),
			'i18n'    => array(
				'select' => _x( 'Select', 'select placeholder', 'codepress-admin-columns' ),
			),
		) );

		wp_enqueue_script( 'ac-select2' );
		wp_enqueue_style( 'ac-select2' );
		wp_enqueue_style( 'ac-jquery-ui' );
		wp_enqueue_style( 'wp-pointer' );
	}

	/**
	 * @return array
	 */
	public function get_filters() {
		$filters = array();

		foreach ( $this->list_screen->get_columns() as $column ) {
			$setting = $column->get_setting( 'search' );

			if ( ! $setting instanceof ACP\Search\Settings\Column || ! $setting->is_active() ) {
				continue;
			}

			$comparison = $this->get_comparison( $column );

			if ( ! $comparison ) {
				continue;
			}

			$filter = new Middleware\Filter(
				$column->get_name(),
				$comparison,
				$this->get_filter_label( $column )
			);

			$filters[] = $filter();
		}

		return $filters;
	}

	/**
	 * @param AC\Column $column
	 *
	 * @return string
	 */
	private function get_filter_label( AC\Column $column ) {
		$label = $this->sanitize_label( $column->get_custom_label() );

		if ( ! $label ) {
			$label = $this->sanitize_label( $column->get_label() );
		}

		if ( ! $label ) {
			$label = $column->get_type();
		}

		return $label;
	}

	/**
	 * Allow dashicons as label, all the rest is parsed by 'strip_tags'
	 *
	 * @param string $label
	 *
	 * @return string
	 */
	private function sanitize_label( $label ) {
		if ( false === strpos( $label, 'dashicons' ) ) {
			$label = strip_tags( $label );
		}

		return trim( $label );
	}

	/**
	 * @param AC\Column $column
	 *
	 * @return Comparison|false
	 */
	private function get_comparison( AC\Column $column ) {
		if ( ! $column instanceof Searchable || ! $column->search() ) {
			return false;
		}

		return $column->search();
	}

	/**
	 * @param AC\Table\Screen $screen
	 */
	public function register_segment_button( AC\Table\Screen $screen ) {
		$segment = $this->request->get( 'ac-segment' );

		$button = new AC\Table\Button( 'edit-columns' );
		$button
			->set_url( '#' )
			->set_label( __( 'Segments', 'codepress-admin-columns' ) )
			->set_text( '
					<span class="ac-table-button__segment__icon cpacicon-segment"></span>
					<span class="ac-table-button__segment__current">' . $segment . '</span>
					<span class="ac-table-button__caret"></span>' )
			->set_attribute( 'class', 'ac-table-button -segments' )
			->set_attribute( 'data-dropdown', '1' );

		$screen->register_button( $button, 9 );
	}

	/**
	 * Display the markup on the current list screen
	 */
	public function filters_markup() {
		$filters = $this->get_filters();

		if ( empty( $filters ) ) {
			return;
		}

		?>

		<div id="ac-s"></div>

		<?php
	}

	public function add_segment_modal() {
		?>
		<div class="ac-segments">
			<div class="ac-segments__create">
				<span class="cpac_icons-segment"></span>
				<button class="button button-primary">
					<?php _e( 'Create new Segment', 'codepress-admin-columns' ); ?>
				</button>
			</div>
			<div class="ac-segments__list">
			</div>
			<div class="ac-segments__instructions" rel="pointer-segments">
				<?php _e( 'Instructions', 'codepress-admin-columns' ); ?>
				<div id="ac-segments-instructions" style="display:none;">
					<h3><?php _e( 'Instructions', 'codepress-admin-columns' ); ?></h3>
					<p>
						<?php _e( 'Save a set of custom smart filters for later use.', 'codepress-admin-columns' ); ?>
					</p>
					<p>
						<?php _e( 'This can be useful to group your WordPress content based on different criteria. Click on a segment in the list to load the segmented list.', 'codepress-admin-columns' ); ?>
					</p>
				</div>
			</div>

		</div>
		<div class="ac-modal" id="ac-modal-create-segment">
			<div class="ac-modal__dialog -create-segment">
				<form id="frm_create_segment">
					<div class="ac-modal__dialog__header">
						<?php _e( 'Create New Segment', 'codepress-admin-columns' ); ?>
						<button class="ac-modal__dialog__close">
							<span class="dashicons dashicons-no"></span>
						</button>
					</div>
					<div class="ac-modal__dialog__content">
						<label for="inp_segment_name"><?php _e( 'Name', 'codepress-admin-columns' ); ?></label>
						<input type="text" name="segment_name" id="inp_segment_name" required autocomplete="off">
						<div class="ac-modal__error">
						</div>
					</div>
					<div class="ac-modal__dialog__footer">
						<div class="ac-modal__loading">
							<span class="dashicons dashicons-update"></span>
						</div>
						<button class="button button" data-dismiss="modal"><?php _e( 'Cancel' ); ?></button>
						<button type="submit" class="button button-primary"><?php _e( 'Save segment', 'codepress-admin-columns' ); ?></button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

}