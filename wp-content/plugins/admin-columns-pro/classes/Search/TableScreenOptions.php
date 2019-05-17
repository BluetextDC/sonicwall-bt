<?php

namespace ACP\Search;

use AC;
use AC\Preferences;
use ACP\Asset\Enqueueable;

class TableScreenOptions {

	const INPUT_NAME = 'acp_enable_smart_filtering_button';

	/** @var Enqueueable $assets */
	private $assets;

	public function __construct( array $assets ) {
		$this->assets = $assets;
	}

	public function register() {
		add_action( 'ac/table_scripts', array( $this, 'scripts' ) );
		add_action( 'ac/table', array( $this, 'register_screen_option' ) );
		add_action( 'wp_ajax_' . self::INPUT_NAME, array( $this, 'update_smart_filtering_preference' ) );
	}

	public function is_active( AC\ListScreen $list_screen ) {
		$value = $this->get_smart_filtering_setting( $list_screen ) === 1;

		return apply_filters( 'acp/search/is_active', $value, $list_screen );
	}

	/**
	 * @return Preferences\Site
	 */
	public function preferences() {
		return new Preferences\Site( 'enable_smart_filtering' );
	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @return int
	 */
	private function get_smart_filtering_setting( $list_screen ) {
		$setting = $this->preferences()->get( $list_screen->get_key() );

		if ( $setting === false ) {
			$setting = 1;
		}

		return $setting;
	}

	/**
	 * @param AC\ListScreen $list_screen
	 * @param bool          $value
	 */
	private function set_smart_filtering_setting( $list_screen, $value ) {
		$this->preferences()->set( $list_screen->get_key(), (int) $value );
	}

	public function update_smart_filtering_preference() {
		check_ajax_referer( 'ac-ajax' );

		$list_screen = AC\ListScreenFactory::create( filter_input( INPUT_POST, 'list_screen' ) );

		if ( ! $list_screen ) {
			wp_die();
		}

		$this->set_smart_filtering_setting( $list_screen, ( 'true' === filter_input( INPUT_POST, 'value' ) ) ? 1 : 0 );
	}

	/**
	 * @param AC\Table\Screen $table
	 */
	public function register_screen_option( $table ) {
		$check_box = new AC\Form\Element\Checkbox( self::INPUT_NAME );

		$check_box->set_options( array( 1 => __( 'Enable Smart Filtering', 'codepress-admin-columns' ) ) )
		          ->set_value( $this->get_smart_filtering_setting( $table->get_list_screen() ) === 1 ? 1 : 0 );

		$table->register_screen_option( $check_box );
	}

	public function scripts() {
		foreach ( $this->assets as $asset ) {
			$asset->enqueue();
		}
	}

}