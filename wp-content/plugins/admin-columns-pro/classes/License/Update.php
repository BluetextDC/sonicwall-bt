<?php

namespace ACP\License;

use AC\Transient;
use ACP\License;
use Exception;
use WP_Error;

class Update {

	/**
	 * @var string Plugin basename e.g. plugin/plugin.php
	 */
	private $plugin_name;

	/**
	 * @var string
	 */
	private $plugin_version;

	/**
	 * @var API;
	 */
	private $api;

	/**
	 * @param string $plugin_basename
	 */
	public function __construct( $plugin_basename ) {
		$this->plugin_name = dirname( $plugin_basename );
	}

	/**
	 * @param string $plugin_version
	 *
	 * @return $this
	 */
	public function set_plugin_version( $plugin_version ) {
		$this->plugin_version = $plugin_version;

		return $this;
	}

	/**
	 * @param API $api
	 *
	 * @return $this
	 */
	public function set_api( API $api ) {
		$this->api = $api;

		return $this;
	}

	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 * @return bool|object
	 * @throws Exception
	 */
	public function check_for_update() {
		if ( null === $this->api ) {
			throw new Exception( 'Missing License API dependency.' );
		}

		if ( null === $this->plugin_version ) {
			throw new Exception( 'Missing Plugin Version.' );
		}

		$plugin_data = $this->get_update_data();

		if ( is_wp_error( $plugin_data ) ) {
			return false;
		}

		if ( empty( $plugin_data->new_version ) || version_compare( $plugin_data->new_version, $this->plugin_version ) < 1 ) {
			return false;
		}

		return $plugin_data;
	}

	/**
	 * @return Transient
	 */
	private function get_cache() {
		return new Transient( 'acpplugin_update_' . $this->plugin_name );
	}

	/**
	 * Clear cache
	 */
	public function delete_cache() {
		$this->get_cache()->delete();
	}

	/**
	 * @return object|WP_Error Plugin data
	 * @throws Exception
	 */
	private function get_update_data() {
		$cache = $this->get_cache();

		if ( $cache->is_expired() ) {
			$license = new License();

			$request = new Request();
			$request->set_body( array(
				'request'     => 'pluginupdate',
				'licence_key' => $license->get_key(),
				'plugin_name' => $this->plugin_name,
				'version'     => $this->plugin_version,
				'site_url'    => site_url(),
				'php_version' => phpversion(),
			) );

			$response = $this->api->request( $request );

			$result = $response->has_error()
				? $response->get_error()->get_error_message()
				: $response->get_body();

			if ( isset( $result->icon_svg ) && $result->icon_svg ) {
				$result->icons = array(
					'svg' => $result->icon_svg,
				);

				unset( $result->icon_svg );
			}

			$cache->save( $result, HOUR_IN_SECONDS );
		}

		$plugin_data = $cache->get();

		if ( is_string( $plugin_data ) ) {
			return new WP_Error( 'error', $plugin_data );
		}

		return $plugin_data;
	}

}