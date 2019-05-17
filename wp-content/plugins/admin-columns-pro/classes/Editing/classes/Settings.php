<?php

namespace ACP\Editing;

use AC;
use AC\View;

class Settings extends AC\Settings\Column
	implements AC\Settings\Header {

	private $edit;

	protected function define_options() {
		return array( 'edit' => 'off' );
	}

	public function create_header_view() {
		$filter = $this->get_edit();

		$view = new View( array(
			'title'    => __( 'Enable Editing', 'codepress-admin-columns' ),
			'dashicon' => 'dashicons-edit',
			'state'    => $filter,
		) );

		$view->set_template( 'settings/header-icon' );

		return $view;
	}

	public function create_view() {
		$edit = $this->create_element( 'radio', 'edit' );
		$edit
			->set_options( array(
				'on'  => __( 'Yes' ),
				'off' => __( 'No' ),
			) );

		$view = new View();
		$view->set( 'label', __( 'Inline Editing', 'codepress-admin-columns' ) )
		     ->set( 'tooltip', __( 'This will make the column support inline editing.', 'codepress-admin-columns' ) )
		     ->set( 'setting', $edit );

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_edit() {
		return $this->edit;
	}

	/**
	 * @param string $edit
	 *
	 * @return $this
	 */
	public function set_edit( $edit ) {
		$this->edit = $edit;

		return $this;
	}

	public function is_active() {
		return 'on' === $this->get_edit();
	}

}