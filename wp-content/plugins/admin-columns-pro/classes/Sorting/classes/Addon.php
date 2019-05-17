<?php

namespace ACP\Sorting;

use AC;
use ACP\Sorting\Admin\ShowAllResults;

/**
 * Sorting Addon class
 * @since 1.0
 */
class Addon extends AC\Addon
	implements AC\Registrable {

	public function register() {
		add_action( 'ac/table/list_screen', array( $this, 'init_table' ), 11 ); // After filtering
		add_action( 'ac/column/settings', array( $this, 'register_column_settings' ) );
		add_action( 'wp_ajax_acp_reset_sorting', array( $this, 'ajax_reset_sorting' ) );
	}

	/**
	 * @param AC\ListScreen $list_screen
	 */
	public function init_table( AC\ListScreen $list_screen ) {
		$table = new Table\Screen( $list_screen );
		$table->register();
	}

	/**
	 * Ajax reset sorting
	 */
	public function ajax_reset_sorting() {
		check_ajax_referer( 'ac-ajax' );

		$list_screen = AC\ListScreenFactory::create( filter_input( INPUT_POST, 'list_screen' ), filter_input( INPUT_POST, 'layout' ) );

		if ( ! $list_screen ) {
			wp_die();
		}

		$table = new Table\Screen( $list_screen );

		wp_send_json_success( $table->reset_sorting() );
	}

	/**
	 * @return string
	 */
	protected function get_file() {
		return ACP_SORTING_FILE;
	}

	/**
	 * Hide or show empty results
	 * @since 4.0
	 * @return boolean
	 */
	public function show_all_results() {
		$setting = new ShowAllResults();

		return $setting->is_enabled();
	}

	/**
	 * Register field settings for sorting
	 *
	 * @param AC\Column $column
	 */
	public function register_column_settings( $column ) {

		// Custom columns
		if ( $column instanceof Sortable ) {
			$column->sorting()->register_settings();
		}

		// Native columns
		$native = new NativeSortables( $column->get_list_screen() );

		if ( $native->is_sortable( $column->get_type() ) ) {

			$setting = new Settings( $column );
			$setting->set_default( 'on' );

			$column->add_setting( $setting );
		}
	}

}