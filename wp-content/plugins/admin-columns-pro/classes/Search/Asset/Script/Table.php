<?php

namespace ACP\Search\Asset\Script;

use AC\Request;
use ACP\Asset\Location;
use ACP\Asset\Script;

final class Table extends Script {

	/**
	 * @var array
	 */
	protected $filters;

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @param string   $handle
	 * @param Location $location
	 * @param array    $filters
	 * @param Request  $request
	 */
	public function __construct( $handle, Location $location, array $filters, Request $request ) {
		parent::__construct( $handle, $location, array( 'aca-search-querybuilder', 'wp-pointer' ) );

		$this->filters = $filters;
		$this->request = $request;
	}

	public function register() {
		parent::register();

		wp_localize_script( 'aca-search-table', 'ac_search', array(
			'rules'   => json_decode( $this->request->get( 'ac-rules-raw' ) ),
			'filters' => $this->filters,
			'i18n'    => array(
				'select' => _x( 'Select', 'select placeholder', 'codepress-admin-columns' ),
			),
		) );
	}

}