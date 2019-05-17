<?php

namespace ACP\Sorting;

use AC;
use AC\View;

class Settings extends AC\Settings\Column
	implements AC\Settings\Header {

	private $sort;

	protected function define_options() {
		return array( 'sort' => 'on' );
	}

	public function create_header_view() {
		$sort = $this->get_sort();

		$view = new View( array(
			'title'    => __( 'Enable Sorting', 'codepress-admin-columns' ),
			'dashicon' => 'dashicons-sort',
			'state'    => $sort,
		) );

		$view->set_template( 'settings/header-icon' );

		return $view;
	}

	public function create_view() {
		$sort = $this->create_element( 'radio', 'sort' )
		             ->set_options( array(
			             'on'  => __( 'Yes' ),
			             'off' => __( 'No' ),
		             ) );

		$view = new View();
		$view->set( 'label', __( 'Sorting', 'codepress-admin-columns' ) )
		     ->set( 'tooltip', __( 'This will make the column support sorting.', 'codepress-admin-columns' ) )
		     ->set( 'setting', $sort );

		return $view;
	}

	/**
	 * @return string
	 */
	public function get_sort() {
		return $this->sort;
	}

	/**
	 * @param string $sort
	 *
	 * @return $this
	 */
	public function set_sort( $sort ) {
		$this->sort = $sort;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return 'on' === $this->get_sort();
	}

}