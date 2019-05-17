<?php

namespace ACP\Sorting\Table;

use AC;
use AC\Table;
use ACP;
use ACP\Asset\Location;
use ACP\Sorting\ListScreen;
use ACP\Sorting\ModelFactory;
use ACP\Sorting\Asset\Script;

class Screen {

	/**
	 * @var AC\ListScreen
	 */
	private $list_screen;

	/**
	 * @var Location\Absolute $location
	 */
	private $location;

	/**
	 * @param AC\ListScreen     $list_screen
	 * @param Location\Absolute $location
	 */
	public function __construct( AC\ListScreen $list_screen, Location\Absolute $location ) {
		$this->list_screen = $list_screen;
		$this->location = $location;
	}

	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'ac/table', array( $this, 'register_reset_button' ) );

		/**
		 * @see WP_List_Table::get_column_info
		 */
		add_filter( 'manage_' . $this->list_screen->get_screen_id() . '_sortable_columns', array( $this, 'add_sortable_headings' ) );

		// After filtering
		$this->init_sorting();
		$this->handle_sorting();
		$this->save_preference();
	}

	/**
	 * @return Sorted
	 */
	private function sorted() {
		return new Sorted( $this->list_screen, $this->preference(), $_GET );
	}

	/**
	 * @return Preference
	 */
	public function preference() {
		return new Preference\SortedBy( $this->list_screen->get_storage_key() );
	}

	/**
	 * @param AC\Table\Screen $table
	 */
	public function register_reset_button( AC\Table\Screen $table ) {
		$column = $this->sorted()->get_column();

		if ( ! $column ) {
			return;
		}

		$label = strip_tags( $column->get_custom_label() );

		if ( empty( $label ) ) {
			$label = $column->get_label();
		}

		$button = new Table\Button( 'edit-columns' );
		$button->set_label( __( 'Sorted by ', 'codepress-admin-columns' ) . $label )
		       ->set_url( '#' )
		       ->set_text( __( 'Reset Sorting', 'codepress-admin-columns' ) )
		       ->set_attribute( 'class', 'ac-table-button reset-sorting' );

		$table->register_button( $button, 10 );
	}

	/**
	 * When you revisit a page, set the orderby variable so WordPress prints the columns headers properly
	 * @since 4.0
	 */
	public function init_sorting() {

		// Do not apply any preferences when no columns are stored
		if ( ! $this->list_screen->get_settings() ) {
			return;
		}

		if ( filter_input( INPUT_GET, 'orderby' ) ) {
			return;
		}

		// Ignore media grid
		if ( 'grid' === filter_input( INPUT_GET, 'mode' ) ) {
			return;
		}

		$sorted = $this->sorted();

		if ( ! $sorted->get_column() ) {
			return;
		}

		// Set $_GET and $_REQUEST (used by WP_User_Query)
		$_REQUEST['orderby'] = $_GET['orderby'] = $sorted->get_order_by();
		$_REQUEST['order'] = $_GET['order'] = $sorted->get_order();
	}

	/**
	 * @since 4.0
	 */
	public function handle_sorting() {
		if ( ! $this->list_screen instanceof ListScreen ) {
			return;
		}

		$column = $this->sorted()->get_column();

		if ( ! $column ) {
			return;
		}

		$model = ModelFactory::create( $column );

		if ( ! $model ) {
			return;
		}

		if ( ! $model->is_active() ) {
			return;
		}

		$model->get_strategy()->manage_sorting();
	}

	/**
	 * When the orderby (and order) are set, save the preference
	 * @since 4.0
	 */
	public function save_preference() {

		if ( ! isset( $_GET['orderby'], $_GET['order'] ) ) {
			return;
		}

		$this->preference()
		     ->set_order( $_GET['order'] )
		     ->set_order_by( $_GET['orderby'] )
		     ->save();
	}

	/**
	 * @since 1.0
	 *
	 * @param array $sortable_columns Column name or label
	 *
	 * @return array Column name or Sanitized Label
	 */
	public function add_sortable_headings( $sortable_columns ) {

		// Stores the default columns on the listings screen
		if ( ! AC()->is_doing_ajax() && current_user_can( AC\Capabilities::MANAGE ) ) {

			$native = new ACP\Sorting\NativeSortables( $this->list_screen );
			$native->store( $sortable_columns );
		}

		if ( ! $this->list_screen->get_settings() ) {
			return $sortable_columns;
		}

		$columns = $this->list_screen->get_columns();

		if ( ! $columns ) {
			return $sortable_columns;
		}

		// Columns that are active and have enabled sort will be added to the sortable headings.
		foreach ( $columns as $column ) {

			if ( $model = ModelFactory::create( $column ) ) {

				// Custom column
				if ( $model->is_active() ) {
					$sortable_columns[ $column->get_name() ] = $column->get_name();
				}
			} elseif ( isset( $sortable_columns[ $column->get_name() ] ) ) {

				// Native column
				$setting = $column->get_setting( 'sort' );

				if ( $setting instanceof ACP\Sorting\Settings && ! $setting->is_active() ) {
					unset( $sortable_columns[ $column->get_name() ] );
				}
			}
		}

		return $sortable_columns;
	}

	/**
	 * @return bool
	 */
	public function reset_sorting() {
		return $this->preference()->delete();
	}

	/**
	 * Scripts
	 */
	public function scripts() {
		$assets = array(
			new Script\Table( 'acp-sorting', $this->location->with_suffix( 'assets/sorting/js/table.js' ), $this->preference() ),
			new ACP\Asset\Style( 'acp-sorting', $this->location->with_suffix( 'assets/sorting/css/table.css' ) ),
		);

		foreach ( $assets as $asset ) {
			$asset->enqueue();
		}
	}

}