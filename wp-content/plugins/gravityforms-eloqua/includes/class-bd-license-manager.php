<?php
/**
 * Auto Update and License validation Class
 *
 * @package bd-license-manager
 */

class BD_License_Manager {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	private static $_instance = null;

	/**
	 * License key storage
	 *
	 * @var string
	 */
	private $license;

	/**
	 * Path to this plugin
	 *
	 * @var string
	 */
	private $plugin_path;

	/**
	 * Slug of plugin (folder name)
	 *
	 * @var string
	 */
	private $plugin_slug;

	/**
	 * Main plugin filename
	 *
	 * @var string
	 */
	private $plugin_filename;

	/**
	 * Sku of Plugin to verify
	 *
	 * @var string
	 */
	private $plugin_sku;

	/**
	 * Access key for private repo (for updates)
	 *
	 * @var string
	 */
	private $repo_access_key;

	/**
	 * URL to private repo (for updates)
	 *
	 * @var string
	 */
	private $repo_url;

	/**
	 * Stores the last error.
	 *
	 * @var string
	 */
	private $error;

	/**
	 * Stores the last debug message.
	 *
	 * @var string
	 */
	private $debug;

	/**
	 * Stores the last response object in array based on context.
	 *
	 * @var object
	 */
	private $last_response = array();

	/**
	 * Auto-updaters storage
	 *
	 * @var array
	 */
	private $updaters = array();

	/**
	 * Used for better feedback on License check failures.
	 *
	 * @var array
	 */
	private $license_error_codes = array(
		10 => 'CREATE_FAILED',
		20 => 'LICENSE_BLOCKED',
		30 => 'LICENSE_EXPIRED',
		40 => 'LICENSE_IN_USE',
		50 => 'REACHED_MAX_DOMAINS',
		60 => 'LICENSE_INVALID',
		70 => 'DOMAIN_MISSING',
		80 => 'DOMAIN_ALREADY_INACTIVE',
		90 => 'VERIFY_KEY_INVALID',
		100 => 'CREATE_KEY_INVALID',
		110 => 'LICENSE_IN_USE_ON_DOMAIN_AND_MAX_REACHED',
	);

	/**
	 * Initialize plugin constants, check for stored license.
	 *
	 * @param string $license  Initialize with License Key.
	 */
	public function __construct( $license = '', $plugin = array() ) {
		if ( ! defined( 'BDLM_SERVER' ) ) {
			define( 'BDLM_SERVER', 'https://briandichiara.com/' );
		}

		if ( ! defined( 'BDLM_VERIFIER_KEY' ) ) {
			define( 'BDLM_VERIFIER_KEY', '5a24c69bd2a688.20728532' );
		}

		if ( isset( $_POST['slm_action'] ) ) {
			// Don't do anything else if we're hitting slm_action, causes an infinite loop.
			// Unfortunately this is necessary for other sites using Software License Manager, specifically my own.
			return;
		}

		if ( $plugin ) {
			$this->setup_plugin( $plugin );
		}
		if ( ! empty( $license ) ) {
			$this->set_license( $license );
		}

		add_action( 'admin_init', array( $this, 'update' ) );
	}

	/**
	 * Get an instance of this class.
	 *
	 * @return object  BD_License_Manager object.
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new BD_License_Manager();
		}

		return self::$_instance;
	}

	/**
	 * Store plugin params to use with updater
	 *
	 * @param array $plugin  Plugin array with path, slug, filename, and sku.
	 */
	public function setup_plugin( $plugin = array() ) {
		if ( isset( $plugin['path'] ) ) {
			$this->plugin_path = $plugin['path'];
		}
		if ( isset( $plugin['slug'] ) ) {
			$this->plugin_slug = $plugin['slug'];
		}
		if ( isset( $plugin['filename'] ) ) {
			$this->plugin_filename = $plugin['filename'];
		}
		if ( isset( $plugin['sku'] ) ) {
			$this->plugin_sku = $plugin['sku'];
		}
	}

	/**
	 * Set license parameter, used throughout class
	 *
	 * @param string $license  Licence to be used.
	 */
	public function set_license( $license ) {
		$this->license = $license;
	}

	/**
	 * Get license
	 *
	 * @return string  License key.
	 */
	public function get_license() {
		return $this->license;
	}

	/**
	 * Checks to see if we have an active license.
	 *
	 * @param string $license  License Key
	 *
	 * @return bool  If license is active.
	 */
	public function is_active_license( $license = '' ) {
		if ( ! empty( $license ) ) {
			$this->set_license( $license );
		}

		if ( empty( $this->get_license() ) ) {
			$this->error = __( 'License key missing.', 'bdlm' );
			$this->debug = __METHOD__ . '(): ' . $this->error . '[' . $license . '/' . $this->get_license() . ']';
			return false;
		}

		$check = $this->check_license();

		if ( ! $check ) {
			$this->error = __( 'License check failed.', 'bdlm' );
			$this->debug = __METHOD__ . '(): ' . $this->error;
			return false;
		}

		if ( 'active' !== $check->status ) {
			$this->error = __( 'License status is not active.', 'bdlm' );
			$this->debug = __METHOD__ . '(): ' . $this->error;
			return false;
		}

		return true;
	}

	/**
	 * Runs License Check API Call.
	 *
	 * @return object  License Manager API Response Object
	 */
	public function validate_license() {
		$slm_check = array(
			'secret_key' => BDLM_VERIFIER_KEY,
			'slm_action' => 'slm_check',
			'license_key' => $this->get_license(),
			'registered_domain' => get_site_url(),
			'item_reference' => $this->plugin_sku,
		);

		$validation = $this->call_api( $slm_check );

		if ( ! $validation || ! is_object( $validation ) ) {
			$this->error = __( 'Validation Failed.', 'bdlm' );
			$this->debug = __METHOD__ . '(): ' . $this->error . ' ' . print_r( $validation, true );
			return false;
		}

		if ( 'error' === $validation->result ) {
			$this->error = $validation->message;
			$this->debug = __METHOD__ . '(): ' . $this->error;
			return false;
		}

		return $validation;
	}

	/**
	 * Performs a license check to ensure validity.
	 * Also retrieves necessary vars for performing updates.
	 *
	 * @param string $license  License Key.
	 *
	 * @return bool  If license is valid.
	 */
	public function check_license( $license = '' ) {
		if ( isset( $_POST['slm_action'] ) ) {
			$this->error = __( 'Aborting. SLM Action found.', 'bdlm' );
			$this->debug = __METHOD__ . '(): ' . $this->error;
			return false;
		}

		if ( ! empty( $license ) ) {
			$this->set_license( $license );
		}

		if ( ! $this->get_license() ) {
			$this->error = __( 'License key missing.', 'bdlm' );
			$this->debug = __METHOD__ . '(): ' . $this->error;
			return false;
		}

		if ( ! $this->plugin_sku ) {
			return false;
		}

		$transient = get_transient( 'bdlm_license_check_' . $this->plugin_sku );

		if ( $transient ) {
			if ( ! isset( $transient->repo_url ) || ! isset( $transient->repo_access_key ) ) {
				delete_transient( 'bdlm_license_check_' . $this->plugin_sku );
			} else {
				$this->repo_url = $transient->repo_url;
				$this->repo_access_key = $transient->repo_access_key;
				$this->set_last_response( $transient, $this->plugin_sku );
				return $transient;
			}
		}

		$validation = $this->validate_license();

		if ( ! $validation ) {
			return false;
		}

		if ( false === strpos( $validation->product_ref, $this->plugin_sku ) ) {
			$this->error = __( 'Invalid license key for this plugin.', 'bdlm' );
			$this->debug = __METHOD__ . '(): ' . $this->error;
			return false;
		}

		if ( ! isset( $validation->repo_url ) || ! isset( $validation->repo_access_key ) ) {
			$this->debug = __METHOD__ . '(): ' . __( 'Failed to retrieve repo credentials.', 'bdlm' );
			return false;
		}

		$this->repo_url = $validation->repo_url;
		$this->repo_access_key = $validation->repo_access_key;

		set_transient( 'bdlm_license_check_' . $this->plugin_sku, $validation, DAY_IN_SECONDS * 5 );

		return $validation;
	}

	/**
	 * Processes remote post to SLM server API
	 *
	 * @param array $params  POST vars.
	 *
	 * @return object  JSON decoded response object.
	 */
	public function call_api( $params ) {
		$response = wp_remote_post( BDLM_SERVER, array(
			'timeout' => 5,
			'sslverify' => false,
			'body' => $params,
		));

		return $this->handle_api_response( $response );
	}

	/**
	 * Handles responses from the SLM API
	 *
	 * @param array $response  wp_remote_post array.
	 *
	 * @return object  JSON decoded object or false upon failure.
	 */
	public function handle_api_response( $response ) {
		if ( is_wp_error( $response ) ) {
			$this->error = $response->get_error_message();
			$this->debug = __METHOD__ . '(): ' . $this->error;
			return false;
		}

		if ( is_array( $response ) ) {
			$body = $response['body'];
			$body = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', utf8_encode( $body ) );
			$json = json_decode( $body );
			$this->set_last_response( $json );
			return $json;
		}
		return false;
	}

	/**
	 * Sets the last Response Object
	 *
	 * @return object  JSON Decoded response object
	 */
	public function set_last_response( $last_response, $context = 'default' ) {
		if ( ! $context ) {
			$context = 'default';
		}

		$this->last_response[ $context ] = $last_response;
	}

	/**
	 * Returns the last Response Object
	 *
	 * @return object  JSON Decoded response object
	 */
	public function get_last_response( $context = 'default' ) {
		if ( isset( $this->last_response[ $context ] ) ) {
			return $this->last_response[ $context ];
		}
		if ( 'default' === $context || ! isset( $this->last_response['default'] ) ) {
			return false;
		}

		return $this->last_response['default'];
	}

	/**
	 * Returns the last error message
	 *
	 * @return string  Last error message
	 */
	public function get_error_message() {
		return $this->error;
	}

	/**
	 * Returns the last debug message
	 *
	 * @return string  Last debug message
	 */
	public function get_debug_message() {
		return $this->debug;
	}

	/**
	 * For Multisite, gets the URL host of the current site.
	 *
	 * @return string  Host (domain) of the current site.
	 */
	public function get_current_blog_domain() {
		$current_site = get_site_url( get_current_blog_id() );
		$current_url = parse_url( $current_site );
		return $current_url['scheme'] . '://' . $current_url['host'];
	}

	/**
	 * Checks current site domain to match against registered domain
	 *
	 * @param string $registered_domain  Registered Domain (with http://).
	 *
	 * @return bool  If domain matches registered domain.
	 */
	public function check_domain( $registered_domain ) {
		if ( is_multisite() ) {
			return $this->get_current_blog_domain() === $registered_domain;
		}

		return get_site_url() === $registered_domain;
	}

	/**
	 * send query to server and try to active lisence
	 * @return boolean
	 */
	public function activate( $license = false ) {
		if ( ! empty( $license ) ) {
			$this->set_license( $license );
		}

		if ( ! $this->get_license() ) {
			return false;
		}

		$validation = $this->validate_license();

		if ( ! $validation ) {
			return false;
		}

		// Check if license is already activated for this domain.
		if ( 'active' === $validation->status ) {
			foreach ( $validation->registered_domains as $domain ) {
				if ( $this->check_domain( $domain->registered_domain ) && $this->plugin_sku == $domain->item_reference ) {
					delete_transient( 'bdlm_license_check_' . $this->plugin_sku );
					return true;
				}
			}
		}

		$slm_activate = array(
			'secret_key' => BDLM_VERIFIER_KEY,
			'slm_action' => 'slm_activate',
			'license_key' => $this->get_license(),
			'registered_domain' => get_site_url(),
			'item_reference' => $this->plugin_sku,
		);

		$license_data = $this->call_api( $slm_activate );

		if ( ! $license_data || ! is_object( $license_data ) ) {
			return false;
		}

		if ( 'success' === $license_data->result ) {
			delete_transient( 'bdlm_license_check_' . $this->plugin_sku );
			return true;
		} elseif ( 'error' === $license_data->result ) {
			$this->set_license( false );
			$this->error = $license_data->message;
			$this->debug = __METHOD__ . '(): ' . $this->error;
			return false;
		}

		// something else happened...
		return false;
	}

	/**
	 * Check for new versions.
	 */
	public function update() {

		if ( ! $this->get_license() ) {
			return false;
		}

		if ( ! $this->is_active_license() ) {
			return false;
		}

		if ( ! $this->plugin_path || ! $this->repo_url || ! $this->repo_access_key ) {
			return false;
		}

		// Gotta keep this GFELOQUA_PATH to allow GFEloqua to handle all auto-updates
		require( GFELOQUA_PATH . 'lib/plugin-update-checker/plugin-update-checker.php' );

		$this->updaters[ $this->plugin_slug ] = Puc_v4_Factory::buildUpdateChecker(
			$this->repo_url,
			$this->plugin_path . $this->plugin_filename,
			$this->plugin_slug,
			12,
			$this->plugin_slug
		);
		$this->updaters[ $this->plugin_slug ]->setAuthentication( $this->repo_access_key );
	}
}
