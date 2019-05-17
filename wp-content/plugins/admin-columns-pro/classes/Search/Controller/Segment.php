<?php

namespace ACP\Search\Controller;

use AC;
use AC\Exception;
use AC\Preferences;
use AC\Request;
use AC\Response;
use ACP\Controller;
use ACP\Search;
use ACP\Search\Segments;

class Segment extends Controller {

	/**
	 * @var AC\ListScreen;
	 */
	protected $list_screen;

	/**
	 * @var Search\Middleware\Rules
	 */
	protected $rules;

	/**
	 * @var Segments
	 */
	protected $segments;

	/**
	 * @param Request                 $request
	 * @param Search\Middleware\Rules $rules
	 */
	public function __construct( Request $request, Search\Middleware\Rules $rules ) {
		parent::__construct( $request );

		$this->list_screen = AC\ListScreenFactory::create_from_request( $this->request );

		if ( ! $this->list_screen instanceof AC\ListScreen ) {
			throw Exception\Request::from_invalid_parameters();
		}

		$this->rules = $rules;
		$this->segments = new Segments(
			new Preferences\User( 'search_segments_' . $this->list_screen->get_layout_id() )
		);
	}

	/**
	 * @param array $data
	 */
	protected function handle_segments_response( $data = array() ) {
		$response = new Response\Json();

		$errors = array(
			Segments::ERROR_DUPLICATE_NAME => __( 'A segment with this name already exists.', 'codepress-admin-columns' ),
			Segments::ERROR_NAME_NOT_FOUND => __( 'Could not find current segment.', 'codepress-admin-columns' ),
			Segments::ERROR_SAVING         => __( 'Could save the segment.', 'codepress-admin-columns' ),
		);

		if ( $this->segments->has_errors() ) {
			$response
				->set_parameter( 'error', $errors[ $this->segments->get_first_error() ] )
				->error();
		}

		$response
			->set_parameters( $data )
			->success();
	}

	/**
	 * @param Segments\Segment $segment
	 *
	 * @return array
	 */
	protected function get_segment_response( Segments\Segment $segment ) {
		$rules = $this->rules;
		$url = add_query_arg(
			array(
				'ac-rules'   => urlencode( json_encode( $rules( $segment->get_value( 'rules' ) ) ) ),
				'order'      => $segment->get_value( 'order' ),
				'orderby'    => $segment->get_value( 'orderby' ),
				'ac-segment' => urlencode( $segment->get_name() ),
			),
			$this->list_screen->get_screen_link()
		);

		return array(
			'name' => $segment->get_name(),
			'url'  => $url,
		);
	}

	public function read_action() {
		$response = new Response\Json();
		$data = array();

		foreach ( $this->segments->get_segments() as $segment ) {
			$data[] = $this->get_segment_response( $segment );
		}

		$response
			->set_parameters( $data )
			->success();
	}

	public function create_action() {
		$data = filter_var_array(
			$this->request->get_parameters()->all(),
			array(
				'name'    => FILTER_SANITIZE_STRING,
				'rules'   => array(
					'filter' => FILTER_DEFAULT,
					'flags'  => FILTER_REQUIRE_ARRAY,
				),
				'order'   => FILTER_SANITIZE_STRING,
				'orderby' => FILTER_SANITIZE_STRING,
			)
		);

		$segment = new Segments\Segment(
			$data['name'],
			array(
				'rules'   => $data['rules'],
				'order'   => $data['order'],
				'orderby' => $data['orderby'],
			)
		);

		$this->segments
			->add_segment( $segment )
			->save();

		$this->handle_segments_response( array(
			'segment' => $this->get_segment_response( $segment ),
		) );
	}

	public function delete_action() {
		$name = $this->request->filter( 'name', FILTER_SANITIZE_STRING );

		$this->segments
			->remove_segment( $name )
			->save();

		$this->handle_segments_response();
	}

}