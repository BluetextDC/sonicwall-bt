<?php

namespace ACP\Export;

use AC;
use AC\Preferences;
use ACP;
use ACP\Asset\Enqueueable;

/**
 * @since 1.0
 */
class TableScreenOptions {

	/**
	 * @var Enqueueable[]
	 */
	protected $assets;

	/**
	 * @param array $assets
	 */
	public function __construct( array $assets ) {
		$this->assets = $assets;

		add_action( 'ac/table', array( $this, 'register_screen_option' ) );
		add_action( 'ac/table_scripts', array( $this, 'scripts' ) );
		add_filter( 'ac/table/body_class', array( $this, 'add_hide_export_button_class' ), 10, 2 );

		add_action( 'wp_ajax_acp_export_show_export_button', array( $this, 'update_table_option_show_export_button' ) );
	}

	/**
	 * @param AC\Table\Screen $table
	 */
	public function register_screen_option( AC\Table\Screen $table ) {
		if ( ! ( $table->get_list_screen() instanceof ACP\Export\ListScreen ) ) {
			return;
		}

		$check_box = new AC\Form\Element\Checkbox( 'acp_export_show_export_button' );
		$check_box->set_options( array( 1 => __( 'Show Export Button', 'codepress-admin-columns' ) ) )
		          ->set_value( $this->get_export_button_setting( $table->get_list_screen() ) === 1 ? 1 : 0 );

		$table->register_screen_option( $check_box );

		$button = new AC\Table\Button( 'export' );
		$button->set_label( __( 'Export to CSV', 'codepress-admin-columns' ) )
		       ->set_text( __( 'Export', 'codepress-admin-columns' ) )
		       ->set_dashicon( 'migrate' )
		       ->set_url( '#' );

		$table->register_button( $button );
	}

	/**
	 * @return Preferences\Site
	 */
	public function preferences() {
		return new Preferences\Site( 'show_export_button' );
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return bool
	 */
	private function get_export_button_setting( $list_screen ) {
		$setting = $this->preferences()->get( $list_screen->get_key() );

		// No setting found, enable export
		if ( $setting === false ) {
			$setting = 1;
		}

		return $setting;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 * @param bool          $value
	 */
	private function set_export_button_setting( $list_screen, $value ) {
		$this->preferences()->set( $list_screen->get_key(), (int) $value );
	}

	public function update_table_option_show_export_button() {
		check_ajax_referer( 'ac-ajax' );

		$list_screen = AC\ListScreenFactory::create( filter_input( INPUT_POST, 'list_screen' ) );

		if ( ! $list_screen ) {
			wp_die();
		}

		$this->set_export_button_setting( $list_screen, ( 'true' === filter_input( INPUT_POST, 'value' ) ) ? 1 : 0 );

		exit;
	}

	/**
	 * Load scripts
	 */
	public function scripts() {
		foreach ( $this->assets as $asset ) {
			$asset->enqueue();
		}
	}

	/**
	 * @param string          $classes
	 * @param AC\Table\Screen $table
	 *
	 * @return string
	 */
	public function add_hide_export_button_class( $classes, $table ) {
		if ( ! $this->get_export_button_setting( $table->get_list_screen() ) ) {
			$classes .= ' ac-hide-export-button';
		}

		return $classes;
	}

}