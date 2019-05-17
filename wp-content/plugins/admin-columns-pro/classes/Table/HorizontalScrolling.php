<?php

namespace ACP\Table;

use AC;
use AC\ListScreen;

/**
 * @since 4.0
 */
class HorizontalScrolling implements AC\Registrable {

	public function register() {
		add_action( 'ac/table', array( $this, 'register_screen_option' ) );
		add_action( 'ac/table_scripts', array( $this, 'scripts' ) );
		add_filter( 'ac/table/body_class', array( $this, 'add_horizontal_scrollable_class' ), 10, 2 );
		add_action( 'wp_ajax_acp_update_table_option_overflow', array( $this, 'update_table_option_overflow' ) );
	}

	/**
	 * @return AC\Preferences
	 */
	public function preferences() {
		return new AC\Preferences\Site( 'show_overflow_table' );
	}

	/**
	 * Handle ajax request
	 */
	public function update_table_option_overflow() {
		check_ajax_referer( 'ac-ajax' );

		$list_screen = AC\ListScreenFactory::create( filter_input( INPUT_POST, 'list_screen' ), filter_input( INPUT_POST, 'layout' ) );

		if ( ! $list_screen ) {
			wp_die();
		}

		$this->preferences()->set( $list_screen->get_storage_key(), 'true' === filter_input( INPUT_POST, 'value' ) );

		exit;
	}

	/**
	 * @param ListScreen $list_screen
	 *
	 * @return bool
	 */
	private function is_overflow_table( $list_screen ) {
		$preference = $this->preferences()->get( $list_screen->get_storage_key() );

		return (bool) apply_filters( 'acp/horizontal_scrolling/enable', $preference, $list_screen );
	}

	/**
	 * @param ListScreen $list_screen
	 */
	public function delete_overflow_preference( $list_screen ) {
		$this->preferences()->delete( $list_screen->get_storage_key() );
	}

	/**
	 * @param AC\Table\Screen $table
	 */
	public function register_screen_option( $table ) {
		$check_box = new AC\Form\Element\Checkbox( 'acp_overflow_list_screen_table' );

		$check_box->set_id( 'acp_overflow_list_screen_table' )
		          ->set_options( array( 'yes' => __( 'Horizontal Scrolling', 'codepress-admin-columns' ) ) )
		          ->set_value( $this->is_overflow_table( $table->get_list_screen() ) ? 'yes' : '' );

		$table->register_screen_option( $check_box );
	}

	/**
	 * Load scripts
	 */
	public function scripts() {
		wp_enqueue_style( 'ac-table-screen-option', ACP()->get_url() . 'assets/core/css/table-screen-options.css', array(), ACP()->get_version() );
		wp_enqueue_script( 'ac-table-screen-option', ACP()->get_url() . 'assets/core/js/table-screen-options.js', array(), ACP()->get_version() );
	}

	/**
	 * @param string          $classes
	 * @param AC\Table\Screen $table
	 *
	 * @return string
	 */
	public function add_horizontal_scrollable_class( $classes, $table ) {
		if ( $this->is_overflow_table( $table->get_list_screen() ) ) {
			$classes .= ' acp-overflow-table';
		}

		return $classes;
	}

}