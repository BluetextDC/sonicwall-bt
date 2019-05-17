<?php

namespace ACP\Search;

use AC;
use ACP\Search\Controller\Comparison;
use ACP\Search\Controller\Segment;
use ACP\Search\Middleware;

final class Addon extends AC\Addon
	implements AC\Registrable {

	/**
	 * @var AC\Request
	 */
	private $request;

	public function __construct() {
		$this->request = new AC\Request();
		$this->request->add_middleware( new Middleware\Request() );
	}

	public function register() {
		$settings = new Settings( $this );
		$settings->register();

		$table_screen_options = new TableScreenOptions( $this );
		$table_screen_options->register();

		add_action( 'ac/screen', array( $this, 'table_screen_request' ), 5 );
		add_action( 'wp_ajax_acp_search_segment_request', array( $this, 'segment_request' ) );
		add_action( 'wp_ajax_acp_search_comparison_request', array( $this, 'comparison_request' ) );
	}

	public function segment_request() {
		$segment = new Segment(
			$this->request,
			new Middleware\Rules()
		);

		$segment->dispatch( $this->request->get( 'method' ) );
	}

	public function comparison_request() {
		$comparison = new Comparison(
			$this->request
		);

		$comparison->dispatch( $this->request->get( 'method' ) );
	}

	/**
	 * @param AC\Screen $screen
	 */
	public function table_screen_request( AC\Screen $screen ) {
		$list_screen = $screen->get_list_screen();
		$table_options = new TableScreenOptions( $this );

		if ( ! $list_screen ) {
			return;
		}

		if ( ! $table_options->is_active( $list_screen ) ) {
			return;
		}

		$table_screen = TableScreenFactory::create( $this, $list_screen, $this->request );

		if ( ! $table_screen ) {
			return;
		}

		$table_screen->register();
	}

	/**
	 * @return string
	 */
	protected function get_file() {
		return ACP_SEARCH_FILE;
	}

	/**
	 * @return string
	 */
	public function get_version() {
		return ACP()->get_version();
	}

}