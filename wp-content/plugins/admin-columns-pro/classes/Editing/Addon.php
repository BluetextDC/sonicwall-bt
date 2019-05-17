<?php

namespace ACP\Editing;

use AC;
use AC\Preferences\Site;
use ACP;
use ACP\Asset\Location;
use ACP\Asset\Style;
use ACP\Editing\Ajax\EditableRowsFactory;
use ACP\Editing\Ajax\TableRowsFactory;
use ACP\Editing\Asset\Script;
use ACP\Editing\Controller;

class Addon implements AC\Registrable {

	/** @var AC\Request */
	private $request;

	/**
	 * @var string
	 */
	private $plugin_file;

	public function __construct() {
		$this->request = new AC\Request();
		$this->plugin_file = ACP_FILE;
	}

	public function register() {
		add_action( 'ac/column/settings', array( $this, 'register_column_settings' ) );
		add_action( 'ac/table/list_screen', array( $this, 'register_table_screen' ) );
		add_action( 'wp_ajax_acp_editing_single_request', array( $this, 'ajax_single_request' ) );
		add_action( 'wp_ajax_acp_editing_bulk_request', array( $this, 'ajax_bulk_request' ) );
	}

	public function ajax_single_request() {
		check_ajax_referer( 'ac-ajax' );

		$controller = new Controller\Single( $this->request );
		$controller->dispatch( $this->request->get( 'method' ) );
	}

	public function ajax_bulk_request() {
		check_ajax_referer( 'ac-ajax' );

		$controller = new Controller\Bulk( $this->request );
		$controller->dispatch( $this->request->get( 'method' ) );
	}

	/**
	 * @param AC\ListScreen $list_screen
	 */
	public function register_table_screen( $list_screen ) {
		$location = new Location\Absolute(
			plugin_dir_url( $this->plugin_file ),
			plugin_dir_path( $this->plugin_file )
		);

		$editable_columns = $this->get_editable_columns( $list_screen );

		// Don't register anything when no column in configured to be editable
		if ( empty( $editable_columns ) ) {
			return;
		}

		$editability_state = new Site( 'editability_state' );

		$assets = array(
			new Style( 'acp-editing-table', $location->with_suffix( 'assets/editing/css/table.css' ) ),
			new Script\Table(
				'acp-editing-table',
				$location->with_suffix( 'assets/editing/js/table.js' ),
				$list_screen,
				$editable_columns,
				$editability_state,
				$this->is_bulk_editing_active( $list_screen )
			),
		);

		$table_screen = new TableScreen( $list_screen, $assets, $editability_state );
		$table_screen->register();

		$table_rows = TableRowsFactory::create( $this->request, $list_screen );

		if ( $table_rows && $table_rows->is_request() ) {
			$table_rows->register();
		}

		$editable_rows = EditableRowsFactory::create( $this->request, $list_screen );

		if ( $editable_rows && $editable_rows->is_request() ) {
			$editable_rows->register();
		}
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return array
	 */
	private function get_editable_columns( AC\ListScreen $list_screen ) {
		$editable_columns = array();

		foreach ( $list_screen->get_columns() as $column ) {
			if ( ! $column instanceof Editable ) {
				continue;
			}

			$model = $column->editing();

			if ( ! $model || ! $model->is_active() ) {
				continue;
			}

			$editable_columns[ $column->get_name() ] = $column;
		}

		return $editable_columns;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return bool
	 */
	public function is_bulk_editing_active( AC\ListScreen $list_screen ) {
		return apply_filters( 'acp/editing/bulk/active', true, $list_screen );
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return bool
	 */
	public function is_editing_active( AC\ListScreen $list_screen ) {
		$state = new Site( 'editability_state' );

		return 1 == $state->get( $list_screen->get_key() );
	}

	public function helper() {
		_deprecated_function( __METHOD__, '4.5.4' );

		return new Helper();
	}

	/**
	 * Register setting for editing
	 *
	 * @param AC\Column $column
	 */
	public function register_column_settings( $column ) {
		if ( $column instanceof Editable ) {
			$column->editing()->register_settings();
		}
	}

}