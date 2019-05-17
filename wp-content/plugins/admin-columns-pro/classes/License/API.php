<?php

namespace ACP\License;

use WP_Error;

/**
 * Admin Columns Pro website connection API
 * @since 3.0
 */
class API {

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $proxy;

	/**
	 * @var bool
	 */
	protected $use_proxy = true;

	/**
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * @param string $url
	 *
	 * @return $this
	 */
	public function set_url( $url ) {
		$this->url = $url;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_proxy() {
		return $this->proxy;
	}

	/**
	 * @param string $proxy
	 *
	 * @return $this
	 */
	public function set_proxy( $proxy ) {
		$this->proxy = $proxy;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function is_use_proxy() {
		return $this->use_proxy;
	}

	/**
	 * @param bool $use_proxy
	 *
	 * @return $this
	 */
	public function set_use_proxy( $use_proxy ) {
		$this->use_proxy = $use_proxy;

		return $this;
	}

	/**
	 * Get the URL to call
	 * @return string
	 */
	private function get_request_url() {
		if ( $this->use_proxy ) {
			return $this->proxy;
		}

		return $this->url;
	}

	/**
	 * API Ajax
	 * @since 1.1
	 *
	 * @param Request $request
	 *
	 * @return Response API Response
	 */
	public function request( Request $request ) {
		$request->set_body( array_merge( $request->get_body(), array(
			'wc-api' => 'software-licence-api',
		) ) );

		$data = wp_remote_post( $this->get_request_url(), $request->get_args() );

		$response = new Response();
		$response = $response->with_body( wp_remote_retrieve_body( $data ) );

		// retry with proxy disabled
		if ( ! $response->get_body() && $this->use_proxy ) {
			$this->use_proxy = false;

			return $this->request( $request );
		}

		switch ( $request->get_format() ) {
			case 'json':
				$response = $response->with_body( json_decode( $response->get_body() ) );

				if ( $response->get( 'error' ) ) {
					$response = $response->with_error( new WP_Error( $response->get( 'code' ), $response->get( 'message' ) ) );
				}
		}

		if ( ! $response->get_body() ) {
			return $response->with_error( new WP_Error( 'empty_response', __( 'Empty response from API.', 'codepress-admin-columns' ) ) );
		}

		return $response;
	}

}