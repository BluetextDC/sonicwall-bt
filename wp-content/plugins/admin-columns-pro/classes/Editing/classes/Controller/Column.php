<?php

namespace ACP\Editing\Controller;

use AC;
use AC\ListScreenFactory;
use AC\Response\Json;
use ACP\Controller;
use ACP\Editing\Editable;
use ACP\Editing\ListScreen;
use ACP\Editing\Model;
use ACP\Editing\PaginatedOptions;
use ACP\Helper\Select;

abstract class Column extends Controller {

	/**
	 * @return AC\ListScreen|ListScreen|false
	 */
	protected function get_list_screen_from_request() {
		$list_screen = ListScreenFactory::create_from_request( $this->request );

		if ( ! $list_screen || ! $list_screen instanceof ListScreen ) {
			return false;
		}

		return $list_screen;
	}

	/**
	 * @return AC\Column|Editable|false
	 */
	protected function get_column_from_request() {
		$list_screen = $this->get_list_screen_from_request();

		if ( ! $list_screen ) {
			return false;
		}

		$column = $list_screen->get_column_by_name( $this->request->get( 'column' ) );

		if ( ! $column instanceof Editable ) {
			return false;
		}

		return $column;
	}

	/**
	 * @return Model|false
	 */
	protected function get_model_from_request() {
		$column = $this->get_column_from_request();

		if ( ! $column ) {
			return false;
		}

		$model = $column->editing();

		if ( ! $model || ! $model->is_active() ) {
			return false;
		}

		return $model;
	}

	public function get_select_values_action() {
		$response = new Json();

		$model = $this->get_model_from_request();

		if ( ! $model instanceof PaginatedOptions ) {
			$response->error();
		}

		$options = $model->get_paginated_options(
			$this->request->filter( 'searchterm' ),
			$this->request->filter( 'page', 1, FILTER_SANITIZE_NUMBER_INT ),
			$this->request->filter( 'item_id', null, FILTER_SANITIZE_NUMBER_INT )
		);

		$select = new Select\Response( $options, ! $options->is_last_page() );

		$response
			->set_parameters( $select() )
			->success();
	}

}