<?php

namespace ACP\License;

use ACP\License;
use WP_Error;

class Install {

	/**
	 * @var string Plugin name e.g. ac-plugin
	 */
	private $plugin_name;

	/**
	 * @var API;
	 */
	private $api;

	/**
	 * @param string $plugin_name
	 * @param API    $api
	 */
	public function __construct( $plugin_name, API $api ) {
		$this->plugin_name = $plugin_name;
		$this->api = $api;
	}

	/**
	 * @return object|WP_Error
	 */
	public function get_install_data() {
		$license = new License();

		$request = new Request( array(
			'request'     => 'plugininstall',
			'licence_key' => $license->get_key(),
			'plugin_name' => $this->plugin_name,
			'site_url'    => site_url(),
			'php_version' => phpversion(),
		) );

		$response = $this->api->request( $request );

		if ( $response->has_error() ) {
			return new WP_Error( 'install-error', $response->get_error()->get_error_message() );
		}

		return $response->get_body();
	}

}