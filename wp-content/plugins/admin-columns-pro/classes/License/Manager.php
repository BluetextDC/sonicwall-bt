<?php

namespace ACP\License;

use AC\Capabilities;
use AC\IntegrationFactory;
use AC\Integrations;
use AC\PluginInformation;
use AC\Registrable;
use AC\Storage;
use ACP\License;
use ACP\LicenseUpdate;
use WP_Error;

class Manager
	implements Registrable {

	/**
	 * @var API
	 */
	protected $api;

	/**
	 * @param API $api
	 */
	public function __construct( API $api ) {
		$this->api = $api;
	}

	public function register() {
		// Hook into WP update process
		add_action( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_updates' ) );

		// Seen when the user clicks "view details" on the plugin listing page
		add_action( 'install_plugins_pre_plugin-information', array( $this, 'display_changelog' ), 8 );

		// Inject add-on resource when user clicks "download & install" on add-ons page
		add_filter( 'plugins_api', array( $this, 'inject_addon_install_resource' ), 10, 3 );

		// Do check before installing add-on
		add_filter( 'ac/addons/install_request/maybe_error', array( $this, 'maybe_install_addon_error' ), 10, 2 );

		// Check subscription renewal status on a scheduled
		add_action( 'shutdown', array( $this, 'do_weekly_renewal_check' ) );

		// Forces update check when user clicks "Check again" on dashboard page.
		add_action( 'init', array( $this, 'force_plugin_update_check_on_request' ) );

		// Clear cache on license activation
		add_action( 'acp/license/activated', array( $this, 'force_plugin_update_check' ) );
	}

	/**
	 * Hook plugin into update process
	 *
	 * @param object $transient Update array build by Wordpress.
	 *
	 * @return object
	 * @throws \Exception
	 */
	public function check_for_updates( $transient ) {
		foreach ( $this->get_plugins() as $basename => $version ) {
			$update = new Update( $basename );

			$update->set_plugin_version( $version )
			       ->set_api( $this->api );

			$plugin_data = $update->check_for_update();

			if ( $plugin_data ) {
				$transient->response[ $basename ] = $plugin_data;
			}
		}

		return $transient;
	}

	/**
	 * Display changelog
	 * @throws \Exception
	 */
	public function display_changelog() {
		$plugin = filter_input( INPUT_GET, 'plugin' );

		foreach ( $this->get_plugins() as $basename => $version ) {
			if ( $plugin !== dirname( $basename ) ) {
				continue;
			}

			$request = new Request();
			$request->set_format( 'html' )
			        ->set_body( array(
				        'request'     => 'pluginchangelog',
				        'plugin_name' => $plugin,
			        ) );

			$response = $this->api->request( $request );

			if ( $response->has_error() ) {
				$response = $response->get_error()->get_error_message();
			}

			echo $response->get_body();
			exit;
		}
	}

	/**
	 * Add addons to install process, not the update process. Used by add-ons page.
	 *
	 * @param array  $result
	 * @param string $action
	 * @param object $args
	 *
	 * @return array|WP_Error
	 * @throws \Exception
	 */
	public function inject_addon_install_resource( $result, $action, $args ) {
		if ( 'plugin_information' !== $action
		     || empty( $args->slug )
		     || ! filter_input( INPUT_GET, 'ac-redirect' )
		) {
			return $result;
		}

		$integration = IntegrationFactory::create_by_dirname( $args->slug );

		if ( ! $integration ) {
			return $result;
		}

		$data = $this->get_install_data( $args->slug );

		if ( is_wp_error( $data ) ) {
			return $result;
		}

		return $data;
	}

	/**
	 * @param string $error
	 * @param string $plugin_name
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function maybe_install_addon_error( $error, $plugin_name ) {
		$license = new License();

		if ( ! $license->is_active() ) {
			$error = sprintf( __( "Licence not active. Enter your license key on <a href='%s'>the settings page</a>.", 'codepress-admin-columns' ), $this->get_license_page_url() );
		}

		$data = $this->get_install_data( $plugin_name );

		if ( is_wp_error( $data ) ) {
			$error = $data->get_error_message();
		}

		return $error;
	}

	/**
	 * @param string $plugin
	 *
	 * @return object|WP_Error
	 */
	private function get_install_data( $plugin ) {
		$install = new Install( $plugin, $this->api );

		return $install->get_install_data();
	}

	/**
	 * @since 3.4.3
	 * @throws \Exception
	 */
	public function do_weekly_renewal_check() {
		$cache = new Storage\Timestamp(
			new Storage\Option( 'acp_renewal_check' )
		);

		if ( $cache->is_expired() ) {

			$updater = new LicenseUpdate( new License(), $this->api );
			$updater->update();

			$cache->save( time() + WEEK_IN_SECONDS );
		}
	}

	/**
	 * Deletes the various transients
	 * If we're on the update-core.php?force-check=1 page
	 */
	public function force_plugin_update_check_on_request() {
		global $pagenow;

		if ( current_user_can( Capabilities::MANAGE ) && $pagenow === 'update-core.php' && '1' === filter_input( INPUT_GET, 'force-check' ) ) {
			$this->clear_update_cache();
		}
	}

	/**
	 * Force a plugin update checks by purging transients/cache.
	 */
	public function force_plugin_update_check() {
		delete_site_transient( 'update_plugins' );

		$this->clear_update_cache();
	}

	/**
	 * Clear API cache
	 */
	private function clear_update_cache() {
		foreach ( $this->get_plugins() as $basename => $version ) {
			$update = new Update( $basename );
			$update->delete_cache();
		}
	}

	/**
	 * @return array
	 */
	private function get_plugins() {
		$plugins = array(
			ACP()->get_basename() => ACP()->get_version(),
		);

		foreach ( new Integrations() as $integration ) {
			$plugin_info = new PluginInformation( $integration->get_basename() );

			if ( $plugin_info->is_installed() ) {
				$plugins[ $plugin_info->get_basename() ] = $plugin_info->get_version();
			}
		}

		// Check for deprecated add-ons
		$deprecated_addons = array(
			'cac-addon-acf/cac-addon-acf.php',
			'cac-addon-woocommerce/cac-addon-woocommerce.php',
		);

		foreach ( $deprecated_addons as $basename ) {
			$plugin = new PluginInformation( $basename );

			if ( $plugin->is_installed() ) {
				$plugins[ $plugin->get_basename() ] = $plugin->get_version();
			}
		}

		return $plugins;
	}

	/**
	 * Check if the license for this plugin is managed per site or network
	 * @since 3.6
	 * @return boolean
	 */
	private function is_network_managed_license() {
		return is_multisite() && ACP()->is_network_active();
	}

	/**
	 * Get the URL to manage your license based on network or site managed license
	 * @return string
	 */
	private function get_license_page_url() {
		$url = ac_get_admin_url( 'settings' );

		if ( $this->is_network_managed_license() ) {
			$url = ACP()->network_admin()->get_url( 'settings' );
		}

		return $url;
	}

}