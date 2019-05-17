<?php

namespace ACP\Filtering;

use AC;
use ACP;
use ACP\Asset\Location;

/**
 * @since 4.0
 */
class Addon implements AC\Registrable {

	/**
	 * @var string
	 */
	private $plugin_file;

	public function __construct() {
		$this->plugin_file = ACP_FILE;
	}

	public function register() {
		add_action( 'ac/column/settings', array( $this, 'settings' ) );
		add_action( 'ac/settings/scripts', array( $this, 'settings_scripts' ) );
		add_action( 'ac/table/list_screen', array( $this, 'table_screen' ) );
		add_action( 'ac/table/list_screen', array( $this, 'handle_filtering' ) );
		add_action( 'wp_ajax_acp_update_filtering_cache', array( $this, 'ajax_update_dropdown_cache' ) );
	}

	public function ajax_update_dropdown_cache() {
		check_ajax_referer( 'ac-ajax' );

		$input = (object) filter_input_array( INPUT_POST, array(
			'list_screen' => FILTER_SANITIZE_STRING,
			'layout'      => FILTER_SANITIZE_STRING,
		) );

		$list_screen = AC\ListScreenFactory::create( $input->list_screen, $input->layout );

		if ( ! $list_screen ) {
			wp_die();
		}

		$table_screen = $this->table_screen( $list_screen );

		if ( ! $table_screen ) {
			wp_die();
		}

		wp_send_json_success( $table_screen->update_dropdown_cache() );
	}

	/**
	 * @return Helper
	 */
	public function helper() {
		return new Helper();
	}

	/**
	 * @param AC\Column $column
	 *
	 * @return Model|false
	 */
	public function get_filtering_model( $column ) {
		if ( ! $column instanceof Filterable ) {
			return false;
		}

		$list_screen = $column->get_list_screen();

		if ( ! $list_screen instanceof ListScreen ) {
			return false;
		}

		$model = $column->filtering();
		$model->set_strategy( $list_screen->filtering( $model ) );

		return $model;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return array|false
	 */
	private function get_models( AC\ListScreen $list_screen ) {
		if ( ! $list_screen instanceof ListScreen ) {
			return false;
		}

		$models = array();

		foreach ( $list_screen->get_columns() as $column ) {
			$model = $this->get_filtering_model( $column );

			if ( $model ) {
				$models[] = $model;
			}
		}

		return $models;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return TableScreen|false
	 */
	public function table_screen( AC\ListScreen $list_screen ) {
		$models = $this->get_models( $list_screen );

		if ( ! $models ) {
			return false;
		}

		$location = new Location\Absolute(
			plugin_dir_url( $this->plugin_file ),
			plugin_dir_path( $this->plugin_file )
		);
		$assets[] = new ACP\Asset\Style( 'acp-filtering-table', $location->with_suffix( 'assets/filtering/css/table.css' ) );
		$assets[] = new ACP\Asset\Script( 'acp-filtering-table', $location->with_suffix( 'assets/filtering/js/table.js' ), array( 'jquery', 'jquery-ui-datepicker' ) );

		switch ( true ) {
			case $list_screen instanceof ACP\ListScreen\MSUser :
				return new TableScreen\MSUser( $models, $assets );

			case $list_screen instanceof ACP\ListScreen\User :
				return new TableScreen\User( $models, $assets );

			case $list_screen instanceof ACP\ListScreen\Post :
			case $list_screen instanceof ACP\ListScreen\Media :
				return new TableScreen\Post( $models, $assets );

			case $list_screen instanceof ACP\ListScreen\Comment :
				return new TableScreen\Comment( $models, $assets );

			case $list_screen instanceof ACP\ListScreen\Taxonomy :
				return new TableScreen\Taxonomy( $models, $assets );
		}

		return false;
	}

	public function settings_scripts() {
		$location = new Location\Absolute(
			plugin_dir_url( $this->plugin_file ),
			plugin_dir_path( $this->plugin_file )
		);

		$script = new ACP\Asset\Script( 'acp-filtering-settings', $location->with_suffix( 'assets/filtering/js/settings.js' ), array( 'jquery' ) );
		$script->enqueue();
	}

	/**
	 * Register field settings for filtering
	 *
	 * @param AC\Column $column
	 */
	public function settings( $column ) {
		$model = $this->get_filtering_model( $column );

		if ( $model ) {
			$model->register_settings();
		}
	}

	/**
	 * Handle filtering request
	 *
	 * @param AC\ListScreen $list_screen
	 */
	public function handle_filtering( AC\ListScreen $list_screen ) {
		foreach ( $list_screen->get_columns() as $column ) {
			$model = $this->get_filtering_model( $column );

			if ( $model && $model->is_active() && false !== $model->get_filter_value() ) {
				$model->get_strategy()->handle_request();
			}
		}
	}

}