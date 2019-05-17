<?php

namespace ACP\Sorting\Admin;

use AC\Form\Element\Checkbox;
use AC\Settings\Admin;
use AC\Settings\General;

class ShowAllResults extends Admin\General {

	public function __construct() {
		parent::__construct( 'show_all_results' );
	}

	private function get_label() {
		return sprintf( '%s %s',
			__( "Show all results when sorting.", 'codepress-admin-columns' ),
			sprintf( __( "Default is %s.", 'codepress-admin-columns' ), '<code>' . __( 'off', 'codepress-admin-columns' ) . '</code>' )
		);
	}

	/**
	 * @return bool
	 */
	public function is_enabled() {
		return '1' === $this->get_value();
	}

	/**
	 * @return string
	 */
	protected function get_value() {
		return $this->settings->is_empty() ? '0' : parent::get_value();
	}

	public function render() {
		$name = sprintf( '%s[%s]', General::SETTINGS_NAME, $this->name );

		$checkbox = new Checkbox( $name );

		$checkbox->set_options( array( '1' => $this->get_label() ) )
		         ->set_value( $this->get_value() );

		return $checkbox->render();
	}

}