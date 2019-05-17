<?php

namespace ACP\Editing;

use AC;
use AC\Preferences\Site;
use ACP;
use ACP\Editing\Ajax\EditableRowsFactory;
use ACP\Editing\Ajax\TableRowsFactory;
use ACP\Editing\Controller;

class Addon extends AC\Addon
	implements AC\Registrable {

	/** @var AC\Request */
	private $request;

	public function __construct() {
		$this->request = new AC\Request();
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
		$table_screen = new TableScreen( $list_screen, $this );
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
	 * @return bool
	 */
	public function is_editing_active( AC\ListScreen $list_screen ) {
		$state = new Site( 'editability_state' );

		return 1 == $state->get( $list_screen->get_key() );
	}

	protected function get_file() {
		return ACP_EDITING_FILE;
	}

	/**
	 * @since 4.0
	 */
	public function get_version() {
		return ACP()->get_version();
	}

	public function helper() {
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