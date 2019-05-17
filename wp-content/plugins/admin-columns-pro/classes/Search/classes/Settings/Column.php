<?php

namespace ACP\Search\Settings;

use AC;
use AC\View;
use ACP;

class Column extends AC\Settings\Column {

	/**
	 * @return array
	 */
	protected function define_options() {
		return array(
			'search',
		);
	}

	private function get_tooltip() {
		return new ACP\Search\Tooltip\SmartFiltering( $this->column->get_name() );
	}

	/**
	 * @return View
	 */
	public function create_view() {
		$view = new View();
		$view->set( 'label', __( 'Smart Filtering', 'codepress-admin-columns' ) )
		     ->set( 'tooltip', __( 'Smart filtering is always enabled.', 'codepress-admin-columns' ) )
		     ->set( 'setting',
			     sprintf( '<em>%s</em> %s', __( 'Enabled.', 'codepress-admin-columns' ), $this->get_tooltip()->get_label() . $this->get_tooltip()->get_instructions() )
		     );

		return $view;
	}

	/**
	 * @return bool True when search is selected
	 */
	public function is_active() {
		return apply_filters( 'acp/search/smart-filtering-active', true, $this );
	}

}