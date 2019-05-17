<?php

namespace ACP\Search\Controller;

use AC;
use AC\Exception;
use AC\Request;
use AC\Response;
use ACP\Controller;
use ACP\Helper\Select;
use ACP\Search;
use ACP\Search\Searchable;
use DomainException;

class Comparison extends Controller {

	/**
	 * @var AC\ListScreen;
	 */
	protected $list_screen;

	/**
	 * @param Request $request
	 */
	public function __construct( Request $request ) {
		parent::__construct( $request );

		$this->list_screen = AC\ListScreenFactory::create_from_request( $request );

		if ( ! $this->list_screen instanceof AC\ListScreen ) {
			throw Exception\Request::from_invalid_parameters();
		}
	}

	public function get_options_action() {
		$column = $this->list_screen->get_column_by_name(
			$this->request->filter( 'column', null, FILTER_SANITIZE_STRING )
		);

		$response = new Response\Json();

		if ( ! $column instanceof Searchable ) {
			$response->error();
		}

		$comparison = $column->search();

		switch ( true ) {
			case $comparison instanceof Search\Comparison\RemoteValues :
				$options = $comparison->get_values();
				$has_more = false;

				break;
			case $comparison instanceof Search\Comparison\SearchableValues :
				$options = $comparison->get_values(
					$this->request->filter( 'searchterm' ),
					$this->request->filter( 'page', null, FILTER_SANITIZE_NUMBER_INT )
				);
				$has_more = ! $options->is_last_page();

				break;
			default :
				throw new DomainException( 'Invalid Comparison type found.' );
		}

		$select = new Select\Response( $options, $has_more );

		$response
			->set_parameters( $select() )
			->success();
	}

}