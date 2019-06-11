<?php
/**
 * GFEloqua Main Plugin class
 *
 * @package gfeloqua
 */

if ( class_exists( 'GFEloqua' ) ) {
	return;
}

/**
 * GFEloqua class to extend GFFeedAddOn class
 */
class GFEloqua extends GFFeedAddOn {

	/**
	 * Current Plugin Version
	 *
	 * @var string
	 */
	protected $_version = GFELOQUA_VERSION;

	/**
	 * Minimum Gravity Forms Version
	 *
	 * @var string
	 */
	protected $_min_gravityforms_version = '1.8.17';

	/**
	 * Plugin Slug
	 *
	 * @var string
	 */
	protected $_slug = 'gravityformseloqua';

	/**
	 * Path to plugin file.
	 *
	 * @var string
	 */
	protected $_path = 'gravityforms-eloqua/gravityforms-eloqua.plugin.php';

	/**
	 * Full path to this class.
	 *
	 * @var string
	 */
	protected $_full_path = __FILE__;

	/**
	 * Plugin Website URL.
	 *
	 * @var string
	 */
	protected $_url = 'https://briandichiara.com/product/gravityforms-eloqua/';

	/**
	 * Title of Plugin
	 *
	 * @var string
	 */
	protected $_title = 'Gravity Forms Eloqua';

	/**
	 * Short title of Plugin
	 *
	 * @var string
	 */
	protected $_short_title = 'Eloqua';

	/**
	 * Supported capabilities
	 *
	 * @var array
	 */
	protected $_capabilities = array( 'gravityformseloqua', 'gravityformseloqua_uninstall' );

	/**
	 * Settings Page capability.
	 *
	 * @var string
	 */
	protected $_capabilities_settings_page = 'gravityformseloqua';

	/**
	 * Form Settings capability.
	 *
	 * @var string
	 */
	protected $_capabilities_form_settings = 'gravityformseloqua';

	/**
	 * Uninstall capability.
	 *
	 * @var string
	 */
	protected $_capabilities_uninstall = 'gravityformseloqua_uninstall';

	/**
	 * Rocket Genius Autoupgrade feature.
	 *
	 * @var bool
	 */
	protected $_enable_rg_autoupgrade = false;

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	private static $_instance = null;

	/**
	 * Instance of Eloqua API Class
	 *
	 * @var object
	 */
	protected $eloqua;

	/**
	 * Has token been auto-generated?
	 *
	 * @var bool
	 */
	protected $token_auto_generated = false;

	/**
	 * Has OAuth token been retrieved?
	 *
	 * @var bool
	 */
	protected $oauth_token_retrieved = false;

	/**
	 * Has license key been stored?
	 *
	 * @var bool
	 */
	protected $license_key_stored = false;

	/**
	 * Check if activation failed.
	 *
	 * @var bool
	 */
	protected $activation_failed = false;

	/**
	 * Check if activated.
	 *
	 * @var bool
	 */
	protected $is_activated = false;

	/**
	 * Folder storage array.
	 *
	 * @var array
	 */
	private $folders = array();

	/**
	 * Deprecated: Init Counter for legacy form restoration.
	 *
	 * @var int
	 */
	private $init_counter = 0;

	/**
	 * String used for Successful submission to Eloqua.
	 *
	 * @var string
	 */
	private $text_for_success = 'Success!';

	/**
	 * String used for Failed submission to Eloqua.
	 *
	 * @var string
	 */
	private $text_for_failed = 'Failed.';

	/**
	 * Last response from Eloqua.
	 *
	 * @var string
	 */
	private $last_response = false;

	/**
	 * Timeout for API calls
	 *
	 * @var int
	 */
	private $timeout;

	/**
	 * Prevents disconnect notices on timeouts.
	 *
	 * @var bool
	 */
	private $eloqua_timeout = false;

	/**
	 * Entry notes storage
	 *
	 * @var array
	 */
	private $entry_notes;

	/**
	 * Get an instance of this class.
	 *
	 * @return object  GFEloqua object.
	 */
	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new GFEloqua();
		}

		return self::$_instance;
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {
		parent::init();

		$this->_maybe_store_settings();
		$this->_maybe_clear_settings();

		$this->entry_notes = new GFEloqua_Entry_Notes();

		add_action( 'wp_ajax_gfeloqua_clear_transient', array( $this, 'clear_eloqua_transient' ) );
		add_action( 'wp_ajax_gfeloqua_resubmit_entry', array( $this, 'resubmit_entry' ) );
		add_action( 'wp_ajax_gfeloqua_reset_entry', array( $this, 'reset_entry' ) );
		add_action( 'wp_ajax_gfeloqua_connection_test', array( $this, 'ajax_connection_test' ) );

		if ( $this->is_detail_page() ) {
			wp_enqueue_script( 'gform_conditional_logic' );
			wp_enqueue_script( 'gform_gravityforms' );
			wp_enqueue_script( 'gform_form_admin' );
		}

		// this fixes the update notice on the plugins page.
		add_action( 'admin_init', array( $this, 'insert_version_data' ) );

		// GDPR Compliance data exporter and eraser.
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_data_exporter' ), 10 );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_data_eraser' ), 10 );

		add_action( 'admin_init', array( $this, 'privacy_policy_content' ) );

		// oauth actions.
		add_action( 'template_redirect', array( $this, 'handle_oauth_code' ), 2 );
		add_action( 'template_redirect', array( $this, 'close_oauth_window' ), 3 );

		// Disconnect Notice.
		add_action( 'admin_notices', array( $this, 'disconnect_notice' ) );

		// cron actions.
		add_filter( 'cron_schedules', array( $this, 'add_extra_cron_schedule' ) );

		add_action( 'gfeloqua_disconnect_notification', array( $this, 'disconnect_notification' ) );
		if ( ! wp_next_scheduled( 'gfeloqua_disconnect_notification' ) ) {
			wp_schedule_event( time(), 'hourly', 'gfeloqua_disconnect_notification' );
		}

		$retry_interval = $this->get_plugin_setting( 'retry_interval' );
		if ( ! $retry_interval ) {
			$retry_interval = '5min';
		}
		add_action( 'gfeloqua_process_failed_submissions', array( $this, 'retry_failed_submissions' ) );
		if ( ! wp_next_scheduled( 'gfeloqua_process_failed_submissions' ) ) {
			wp_schedule_event( time(), $retry_interval, 'gfeloqua_process_failed_submissions' );
		}

		// entry detail.
		add_action( 'gform_entry_detail', array( $this, 'display_entry_notes' ), 10, 2 );

		global $gfeloqua_license_manager;
		$license_key = $this->get_license_key();
		$gfeloqua_license_manager->set_license( $license_key );
	}

	/**
	 * Add our Exporter Hook
	 *
	 * @param array $exporters  Array of current exporters.
	 *
	 * @return array  Modified array of exporters.
	 */
	public function register_data_exporter( $exporters ) {
		$exporters[ $this->_slug ] = array(
			'exporter_friendly_name' => __( 'Gravity Forms Eloqua Plugin', 'gfeloqua' ),
			'callback'               => array( $this, 'gfeloqua_data_exporter' ),
		);
		return $exporters;
	}

	/**
	 * Add our Eraser Hook
	 *
	 * @param array $erasers  Array of current erasers.
	 *
	 * @return array  Modified array of erasers.
	 */
	public function register_data_eraser( $erasers ) {
		$erasers[ $this->_slug ] = array(
			'eraser_friendly_name' => __( 'Gravity Forms Eloqua Plugin', 'gfeloqua' ),
			'callback'             => array( $this, 'gfeloqua_data_eraser' ),
		);
		return $erasers;
	}

	/**
	 * Mask email address
	 *
	 * @param string $email      Email address.
	 * @param string $mask_char  Characater to use to mask (default: *).
	 * @param int    $percent    Percentage of email to mask.
	 *
	 * @return string  Masked email address.
	 */
	public function mask_email( $email, $mask_char = '*', $percent = 80 ) {
		list( $user, $domain ) = preg_split( '/@/', $email );

		$len        = strlen( $user );
		$mask_count = floor( $len * $percent / 100 );
		$offset     = floor( ( $len - $mask_count ) / 2 );
		$masked     = substr( $user, 0, $offset ) . str_repeat( $mask_char, $mask_count ) . substr( $user, $mask_count + $offset );

		return( $masked . '@' . $domain );
	}

	/**
	 * Add Personal data items to data exporter.
	 *
	 * @param string $email_address  Requested email address.
	 * @param int    $page           Page number for multiple passes.
	 *
	 * @return array  Requested data based on email address.
	 */
	public function gfeloqua_data_exporter( $email_address, $page = 1 ) {
		$export_items = array();
		$data         = array();

		$disconnect_notification_recipient = $this->get_plugin_setting( 'disconnect_alert_email' );
		$retry_limit_recipient             = $this->get_plugin_setting( 'retry_limit_alert_email' );

		if ( stripos( $disconnect_notification_recipient, $email_address ) !== false ) {
			if ( strpos( $disconnect_notification_recipient, ',' ) !== false ) {
				$data_value = '';
				$emails     = explode( ',', $disconnect_notification_recipient );
				foreach ( $emails as $email ) {
					if ( ! empty( $data_value ) ) {
						$data_value .= ', ';
					}
					if ( trim( $email ) == $email_address ) {
						$data_value .= $email;
					} else {
						$data_value .= $this->mask_email( $email );
					}
				}
			} else {
				$data_value = $disconnect_notification_recipient;
			}

			$data[] = array(
				'name'  => __( 'Disconnect Notification Email Address' ),
				'value' => $data_value,
			);
		}

		if ( stripos( $retry_limit_recipient, $email_address ) !== false ) {
			if ( strpos( $retry_limit_recipient, ',' ) !== false ) {
				$data_value = '';
				$emails     = explode( ',', $retry_limit_recipient );
				foreach ( $emails as $email ) {
					if ( ! empty( $data_value ) ) {
						$data_value .= ', ';
					}
					if ( trim( $email ) == $email_address ) {
						$data_value .= $email;
					} else {
						$data_value .= $this->mask_email( $email );
					}
				}
			} else {
				$data_value = $retry_limit_recipient;
			}

			$data[] = array(
				'name'  => __( 'Retry Limit Notification Email Address' ),
				'value' => $data_value,
			);
		}

		if ( count( $data ) > 0 ) {
			global $wpdb;
			$setting_id = 'gravityformsaddon_' . $this->_slug . '_settings';
			$row        = $wpdb->get_row( $wpdb->prepare( "SELECT option_id FROM $wpdb->options WHERE option_name = %s LIMIT 1", $setting_id ) );
			if ( is_object( $row ) ) {
				$setting_id = $row->option_id;
			}
			$export_items[] = array(
				'group_id'    => 'gravityforms-eloqua',
				'group_label' => __( 'Gravity Forms Eloqua Settings', 'gfeloqua' ),
				'item_id'     => $setting_id,
				'data'        => $data,
			);
		}

		return array(
			'data' => $export_items,
			'done' => true,
		);
	}

	/**
	 * Remove Personal data using data eraser.
	 *
	 * @param string $email_address  Requested email address.
	 * @param int    $page           Page number for multiple passes.
	 *
	 * @return array  Results of erasure.
	 */
	public function gfeloqua_data_eraser( $email_address, $page = 1 ) {
		$settings      = $this->get_plugin_settings();
		$items_removed = 0;
		$replacements  = array(
			',' . $email_address,
			', ' . $email_address,
			' ' . $email_address,
			$email_address,
		);

		if ( stripos( $settings['disconnect_alert_email'], $email_address ) !== false ) {
			$settings['disconnect_alert_email'] = str_ireplace( $replacements, '', $settings['disconnect_alert_email'] );
			$items_removed++;
		}

		if ( stripos( $settings['retry_limit_alert_email'], $email_address ) !== false ) {
			$settings['retry_limit_alert_email'] = str_ireplace( $replacements, '', $settings['retry_limit_alert_email'] );
			$items_removed++;
		}

		if ( $items_removed > 0 ) {
			$this->update_plugin_settings( $settings );
		}

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}

	/**
	 * Insert Privacy Policy information to WordPress privacy guide.
	 */
	public function privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		ob_start();
		include GFELOQUA_PATH . 'views/privacy.php';
		$content = ob_get_clean();

		wp_add_privacy_policy_content( __( 'Gravity Forms Eloqua', 'gfeloqua' ), wp_kses_post( $content ) );
	}

	/**
	 * Initialize the Eloqua API object
	 *
	 * @since 2.1.2
	 *
	 * @param string $connection_string  oAuth or Basic Auth string.
	 * @param string $auth_type          Type of Auth (basic or oauth).
	 * @param int    $timeout            Timeout for API calls.
	 *
	 * @return Eloqua_API|void  If $connection_string is passed, returns Eloqua_API object, otherwise nothing.
	 */
	public function init_eloqua( $connection_string = '', $auth_type = null, $timeout = null ) {
		// Don't re-init unless $connection_string is provided.
		if ( $this->eloqua && empty( $connection_string ) ) {
			return;
		}
		if ( empty( $connection_string ) ) {
			$connection_string = $this->get_connection_string();
		}

		if ( null === $timeout ) {
			$legacy_timeout = $this->get_plugin_setting( 'gfeloqua_timeout' ) ? $this->get_plugin_setting( 'gfeloqua_timeout' ) : 5;
			$timeout        = $this->get_plugin_setting( 'timeout' ) ? $this->get_plugin_setting( 'timeout' ) : $legacy_timeout;
		}
		$this->timeout = apply_filters( 'gfeloqua_api_timeout', $timeout );

		if ( null === $auth_type ) {
			$auth_type = $this->get_authentication_type();
		}

		$this->eloqua = new Eloqua_API( $connection_string, 'oauth' === $auth_type, $this->timeout );
		return $this->eloqua;
	}

	/**
	 * This disables the update message for GFEloqua Plugin on the plugin screen
	 */
	public function insert_version_data() {
		$update_info = get_transient( 'gform_update_info' );

		if ( ! $update_info || ! isset( $update_info['body'] ) ) {
			return;
		}

		$body = json_decode( $update_info['body'] );

		if ( isset( $body->offerings->{ $this->_slug } ) ) {
			return;
		}

		// add gfeloqua to the list.
		$gfeloqua_plugin = new stdClass();

		$gfeloqua_plugin->is_available = true;
		$gfeloqua_plugin->version      = $this->_version;
		$gfeloqua_plugin->url          = $this->_url;

		if ( is_array( $body->offerings ) ) {
			if ( ! isset( $body->offerings[ $this->_slug ] ) ) {
				$body->offerings[ $this->_slug ] = $gfeloqua_plugin;
			}
		} elseif ( is_object( $body->offerings ) ) {
			if ( ! isset( $body->offerings->{ $this->_slug } ) ) {
				$body->offerings->{ $this->_slug } = new stdClass();
			}
			$body->offerings->{ $this->_slug } = $gfeloqua_plugin;
		} else {
			return;
		}

		$update_info['body'] = json_encode( $body );

		set_transient( 'gform_update_info', $update_info, DAY_IN_SECONDS );
	}

	/**
	 * Settings fields for Eloqua Feed
	 *
	 * @return array  feed settings
	 */
	public function feed_settings_fields() {
		$feed = $this->get_current_feed();

		return array(
			array(
				'fields' => array(
					array(
						'name'     => 'feed_name',
						'label'    => __( 'Name', 'gfeloqua' ),
						'type'     => 'text',
						'required' => true,
						'class'    => 'medium',
						'tooltip'  => '<h6>' . __( 'Name', 'gfeloqua' ) . '</h6>' . __( 'Enter a feed name to uniquely identify this setup.', 'gfeloqua' ),
					),
					array(
						'label'    => __( 'Eloqua Form', 'gfeloqua' ) . ' <a href="#gfe-forms-refresh" class="gfe-refresh">Refresh</a>',
						'type'     => 'eloqua_forms',
						'onchange' => 'jQuery(this).parents("form").submit();',
						'name'     => GFELOQUA_OPT_PREFIX . 'form',
					),
					array(
						'name'       => 'mapped_fields',
						'label'      => __( 'Map Fields', 'gfeloqua' ) . ' <a href="#gfe-form-fields-refresh" class="gfe-refresh">Refresh</a>',
						'type'       => 'field_map',
						'field_map'  => $this->get_form_fields_map(),
						'dependency' => GFELOQUA_OPT_PREFIX . 'form',
						'tooltip'    => '<h6>' . __( 'Map Fields', 'gfeloqua' ) . '</h6>' . __( 'Associate your Eloqua custom fields to the appropriate Gravity Form fields by selecting the appropriate form field from the list.', 'gfeloqua' ),
					),
					array(
						'name'    => 'cookie_data',
						'label'   => __( 'Cookie Data', 'gfeloqua' ),
						'type'    => 'checkbox',
						'choices' => array(
							array(
								'label' => __( 'Send Eloqua Cookie Data with form submission.', 'gfeloqua' ),
								'name'  => GFELOQUA_OPT_PREFIX . 'use_cookie_data',
							),
						),
						'tooltip' => __( 'This will automatically send any Eloqua cookies along with the form submission.', 'gfeloqua' ),
					),
					array(
						'name'       => 'optin',
						'label'      => __( 'Opt In', 'gfeloqua' ),
						'type'       => 'feed_condition',
						'dependency' => GFELOQUA_OPT_PREFIX . 'form',
						'tooltip'    => '<h6>' . __( 'Opt-In Condition', 'gfeloqua' ) . '</h6>' . __( 'When the opt-in condition is enabled, form submissions will only be exported to Eloqua when the condition is met. When disabled all form submissions will be exported.', 'gfeloqua' ),
					),
				),
			),
		);
	}

	/**
	 * Requirements to enqueue scripts/styles
	 *
	 * @return array  enqueue requirements
	 */
	public function enqueue_conditions() {
		return array(
			array(
				'query' => 'page=gf_edit_forms&view=settings&subview=' . $this->_slug,
			),
			array(
				'query' => 'page=gf_settings&subview=' . $this->_slug,
			),
			array(
				'query' => 'page=gf_entries&view=entry',
			),
		);
	}

	/**
	 * Plugin Styles
	 *
	 * @return array  styles
	 */
	public function styles() {
		$styles = array(
			array(
				'handle'  => 'select2_eloqua',
				'src'     => GFELOQUA_URL . 'lib/select2/select2.min.css',
				'version' => '4.0.6-rc.1',
				'enqueue' => $this->enqueue_conditions(),
			),
			array(
				'handle'  => 'gfeloqua',
				'src'     => GFELOQUA_URL . 'dist/css/gfeloqua.min.css',
				'version' => $this->_version,
				'deps'    => array( 'thickbox' ),
				'enqueue' => $this->enqueue_conditions(),
			),
		);

		return array_merge( parent::styles(), $styles );
	}

	/**
	 * Plugin scripts
	 *
	 * @return array  scripts
	 */
	public function scripts() {
		$scripts = array(
			array(
				'handle'  => 'select2_eloqua',
				'src'     => GFELOQUA_URL . 'lib/select2/select2.min.js',
				'version' => '4.0.6-rc.1',
				'deps'    => array( 'jquery' ),
				'enqueue' => $this->enqueue_conditions(),
			),
			array(
				'handle'  => 'gfeloqua',
				'src'     => GFELOQUA_URL . 'dist/js/gfeloqua.min.js',
				'version' => $this->_version,
				'deps'    => array( 'jquery', 'select2_eloqua', 'thickbox' ),
				'strings' => array(
					'ajax_url'       => admin_url( 'admin-ajax.php' ),
					'prefix'         => GFELOQUA_OPT_PREFIX,
					'confirm_reset'  => __( 'Are you sure you want to reset the status of this entry?', 'gfeloqua' ),
					'oauth_complete' => __( 'Standby: OAuth finished. Reloading current page...', 'gfeloqua' ),
				),
				'enqueue' => $this->enqueue_conditions(),
			),

		);

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Throw in a few custom items into the feed edit page if the plugin isn't setup yet.
	 *
	 * @param array $form     GF Form.
	 * @param int   $feed_id  GF Feed ID.
	 */
	public function feed_edit_page( $form, $feed_id ) {
		global $gfeloqua_license_manager;
		$license_key = $this->get_license_key();
		// ensures valid credentials were entered in the settings page.
		if ( ! $gfeloqua_license_manager->is_active_license( $license_key ) || ! $this->get_connection_string() ) {
			$settings_page = $this->get_plugin_settings_url();
			$view          = GFELOQUA_PATH . 'views/needs-setup.php';
			include( $view );
			return;
		}

		echo '<script type="text/javascript">var form = ' . GFCommon::json_encode( $form ) . ';</script>';

		parent::feed_edit_page( $form, $feed_id );
	}

	/**
	 * Throw in a few custom items into the feed edit page if the plugin isn't setup yet.
	 *
	 * @param array $form  GF Form
	 */
	public function feed_list_page( $form = null ) {
		global $gfeloqua_license_manager;
		$license_key = $this->get_license_key();
		if ( ! $gfeloqua_license_manager->is_active_license( $license_key ) || ! $this->get_connection_string() ) {
			$settings_page = $this->get_plugin_settings_url();
			$view          = GFELOQUA_PATH . 'views/needs-setup.php';
			include( $view );
			return;
		}

		parent::feed_list_page( $form );
	}

	/**
	 * Displayed on feed list, custom columns showing Feed Name and Eloqua Form Name
	 *
	 * @return array  list of columns
	 */
	public function feed_list_columns() {
		return array(
			'feed_name'                  => __( 'Feed Name', 'gfeloqua' ),
			GFELOQUA_OPT_PREFIX . 'form' => __( 'Eloqua Form Name', 'gfeloqua' ),
		);
	}

	/**
	 * Display the Feed Name value
	 *
	 * @param array $feed  GF Feed.
	 *
	 * @return string  Feed Name
	 */
	public function get_column_value_feed_name( $feed ) {
		return $feed['meta']['feed_name'];
	}

	/**
	 * Display the Eloqua Form Name
	 *
	 * @param array $feed  GF Feed.
	 *
	 * @return string  Eloqua Form Name
	 */
	public function get_column_value_gfeloqua_form( $feed ) {
		if ( ! $this->eloqua ) {
			$this->init_eloqua();
		}

		$form_name = '';
		$form      = $this->eloqua->get_form( $feed['meta'][ GFELOQUA_OPT_PREFIX . 'form' ] );

		if ( is_object( $form ) ) {
			$form_name = $form->name;
		}

		$form_name .= $form_name ? ' (ID: ' . $feed['meta'][ GFELOQUA_OPT_PREFIX . 'form' ] . ')' : 'ID: ' . $feed['meta'][ GFELOQUA_OPT_PREFIX . 'form' ];

		return $form_name;
	}

	/**
	 * If we have a valid set of credentials or OAuth token, test it
	 *
	 * @param string $authstring  Pass in the authstring directly.
	 *
	 * @return bool  Did testing connect successfully?
	 */
	public function test_authentication( $authstring = null ) {
		$this->log_debug( __METHOD__ . '() => Testing Authentication.' );
		if ( ! empty( $authstring ) ) {
			$connection_string = $authstring;
		} else {
			$connection_string = $this->get_connection_string();
		}

		if ( empty( $connection_string ) ) {
			$this->log_debug( __METHOD__ . '() => Connection String Missing.' );
			return false;
		}

		$this->log_debug( __METHOD__ . '() => Connection string found!' );

		if ( ! empty( $authstring ) ) {
			$auth_type = 'basic';
		} else {
			$auth_type = $this->get_authentication_type();
		}

		$this->log_debug( __METHOD__ . '() => Using Authentication type: ' . $auth_type );

		$test = $this->init_eloqua( $connection_string, $auth_type );

		if ( ! $test->connect() ) {

			if ( $test->is_timeout() ) {
				$this->eloqua_timeout = true;

				$test = wp_json_encode( $test );
				$this->log_error( __METHOD__ . '() => Connection Timeout: ' . print_r( $test, true ) );
				return false;
			}

			$this->log_error( __METHOD__ . '() => Connection Failure: ' . print_r( $test->errors, true ) );

			$refresh_token = $this->get_oauth_refresh_token();

			if ( 'oauth' === $auth_type && $refresh_token ) {
				// Try the refresh token.
				$this->log_debug( __METHOD__ . '() => Attempting to use Refresh Token.' );
				return $this->refresh_token( $refresh_token );
			}

			return false;
		}

		$this->log_debug( __METHOD__ . '() => Connection tested successfully!' );
		return true;
	}

	/**
	 * Refresh OAuth token
	 *
	 * @param string $refresh_token  Refresh token from Eloqua.
	 *
	 * @return bool  If refresh was successful
	 */
	public function refresh_token( $refresh_token = false ) {
		if ( ! $refresh_token ) {
			$this->log_debug( __METHOD__ . '() => No refresh token found.' );
			$this->clear_connection();
			return false;
		}

		$this->log_debug( __METHOD__ . '() => Attempting to refresh OAuth token.' );

		$this->init_eloqua();

		$client_id     = $this->eloqua->_oauth_client_id;
		$client_secret = $this->eloqua->_oauth_client_secret;
		$token_url     = $this->eloqua->_oauth_token_url;

		$basic_auth = $client_id . ':' . $client_secret . '@';

		$url = str_replace( array( 'http://', 'https://' ), 'https://' . $basic_auth, $token_url );

		$args = array(
			'refresh_token' => $refresh_token,
			'grant_type'    => 'refresh_token',
			'scope'         => $this->eloqua->_oauth_scope,
			'redirect_uri'  => $this->eloqua->_oauth_redirect_uri,
		);

		$args_string = json_encode( $args );

		$response = wp_remote_post(
			$url, array(
				'headers'   => array(
					'Content-Type' => 'application/json',
				),
				'body'      => $args_string,
				'sslverify' => false,
				'timeout'   => $this->timeout,
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->log_error( __METHOD__ . '() => OAUTH WP Error: ' . $response->get_error_message() );
			$this->clear_connection();
			return false;
		}

		$json = isset( $response['body'] ) ? json_decode( $response['body'] ) : false;

		return $this->handle_oauth_json( $json, __METHOD__ );
	}

	/**
	 * Check for errors, then store oauth tokens.
	 *
	 * @param object $json  OAuth response JSON object.
	 *
	 * @return bool  If successful.
	 */
	public function handle_oauth_json( $json, $context = '' ) {
		if ( ! $json ) {
			// When $json is empty, something went wrong, log it.
			$this->log_error( __METHOD__ . '() => OAUTH JSON Empty: ' . print_r( $args, true ) . ' / Probably an issue with cURL. (context: ' . $context . ')' );
			$this->clear_connection();
			return false;
		}

		if ( isset( $json->error ) ) {
			$this->log_error( __METHOD__ . '() => OAUTH JSON Error: ' . print_r( $json->error, true ) . ' / Possibly invalid grant. (context: ' . $context . ')' );
			$this->log_error( __METHOD__ . '() => JSON: ' . print_r( $json, true ) );
			$this->clear_connection();
			return false;
		}

		if ( ! isset( $json->access_token ) ) {
			// no idea, logging json object.
			$this->log_error( __METHOD__ . '() => OAUTH JSON Error: ' . print_r( $json, true ) . ' / No access token present. (context: ' . $context . ')' );
			return false;
		}

		/**
		 * Example Response:
		 *
		 * public 'access_token' => string
		 * public 'expires_in' => int 28800
		 * public 'token_type' => string 'bearer' (length=6)
		 * public 'refresh_token' => string
		 */
		$oauth_token = $json->access_token;
		update_option( GFELOQUA_OPT_PREFIX . 'oauth_token', $oauth_token );
		update_option( GFELOQUA_OPT_PREFIX . 'authentication_timestamp', date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) );

		$this->log_debug( __METHOD__ . '() => Storing OAuth Token: (context: ' . $context . ')' );

		if ( isset( $json->token_type ) && $json->token_type ) {
			update_option( GFELOQUA_OPT_PREFIX . 'oauth_token_type', $json->token_type );
		}
		if ( isset( $json->refresh_token ) && $json->refresh_token ) {
			$this->log_debug( __METHOD__ . '() => Updating OAuth Refresh Token: (context: ' . $context . ')' );
			update_option( GFELOQUA_OPT_PREFIX . 'oauth_refresh_token', $json->refresh_token );
		}

		// Refresh the eloqua object.
		$this->eloqua = $this->init_eloqua( $oauth_token, true );

		return true;
	}

	/**
	 * The OAuth API will send back the oauth code. This will retrieve it, store it and prepare the page for a window close
	 *
	 * @return void
	 */
	public function handle_oauth_code() {
		if ( empty( $_GET['gfeloqua-oauth-code'] ) ) {
			return;
		}

		$oauth_code = sanitize_text_field( $_GET['gfeloqua-oauth-code'] );

		if ( empty( $oauth_code ) ) {
			return;
		}

		// Fix spaces in Auth Token.
		$oauth_code = str_replace( ' ', '+', $oauth_code );

		$generated = $this->generate_oauth_token( $oauth_code );

		if ( $generated ) {
			// make a note we generated the token successfully.
			$this->token_auto_generated = true;
		}

		// set the flag to close the window.
		$this->oauth_token_retrieved = true;
	}

	/**
	 * If we grabbed the OAuth token, try to automatically close the window
	 */
	public function close_oauth_window() {
		if ( ! $this->oauth_token_retrieved ) {
			return;
		}

		echo '<script>window.close();</script>';
		echo '<p style="text-align:center; padding:20px;">' . __( 'If this window does not close automatically, close it to continue.', 'gfeloqua' ) . '</p>';
		exit();
	}

	/**
	 * Get whichever connection string is stored for use
	 *
	 * @return string  authstring or oauth token
	 */
	public function get_connection_string() {
		$auth_type = $this->get_authentication_type();

		$connection_string = 'oauth' === $auth_type ? $this->get_oauth_token() : $this->get_authstring();

		return $connection_string;
	}

	/**
	 * Get last time authenticated.
	 *
	 * @return string  MySQL formatted date/timestamp.
	 */
	public function get_authentication_timestamp() {
		return get_option( GFELOQUA_OPT_PREFIX . 'authentication_timestamp' );
	}

	/**
	 * Determines which authentication method to use.
	 *
	 * @return string  Either oauth or basic.
	 */
	public function get_authentication_type() {
		$use_basic = get_option( GFELOQUA_OPT_PREFIX . 'auth_basic' );
		$use_oauth = (bool) get_option( GFELOQUA_OPT_PREFIX . 'use_oauth', ! $use_basic );

		if ( $use_oauth ) {
			$type = 'oauth';
		} else {
			$type = 'basic';
			$this->log_debug( __METHOD__ . '() => Using Basic Authentication.' );
		}

		return $type;
	}

	/**
	 * Detect if the user is trying to activate the plugin
	 *
	 * @return boolean  is_unactivated
	 */
	public function is_unactivated() {
		if ( $this->activation_failed ) {
			return true;
		}

		if ( $this->is_activated ) {
			return false;
		}

		global $gfeloqua_license_manager;

		$is_unactivated = true;

		if ( $this->is_save_postback() ) {

			$posted = $this->get_posted_settings();

			$this->log_debug( __METHOD__ . '() => Checking License Key.' );

			$license_key = isset( $posted['license_key'] ) && ! empty( $posted['license_key'] ) ? $posted['license_key'] : false;

			if ( ! $license_key ) {
				$license_key = $this->get_license_key();
				if ( $gfeloqua_license_manager->is_active_license( $license_key ) ) {
					$is_unactivated     = false;
					$this->is_activated = true;
				}
			} else {
				$this->log_debug( __METHOD__ . '() => Activating license.' );
				if ( $gfeloqua_license_manager->activate( $license_key ) ) {
					$this->log_debug( __METHOD__ . '() => License Activated!' );
					$is_unactivated     = false;
					$this->is_activated = true;
					$this->store_license_key( $license_key );
				} else {
					$license_response = wp_json_encode( $gfeloqua_license_manager->get_last_response() );
					$this->log_debug( __METHOD__ . '() => License (' . $license_key . ') Activation Failed: ' . print_r( $license_response, true ) );
				}
			}
			$this->last_response = $gfeloqua_license_manager->get_last_response();
		} else {
			$this->log_debug( __METHOD__ . '() => Validating license.' );
			$license_key = $this->get_license_key();
			if ( $gfeloqua_license_manager->is_active_license( $license_key ) ) {
				$is_unactivated     = false;
				$this->is_activated = true;
			} else {
				$this->last_response = $gfeloqua_license_manager->get_last_response();
			}
		}

		return $is_unactivated;
	}

	/**
	 * Detect if the user is switching authentication methods
	 * @return boolean  is_switch
	 */
	public function is_switch() {
		$is_switch = false;
		if ( $this->is_save_postback() ) {
			$posted = $this->get_posted_settings();

			$switch_to_basic = isset( $posted[ GFELOQUA_OPT_PREFIX . 'use_basic' ] ) && '1' === $posted[ GFELOQUA_OPT_PREFIX . 'use_basic' ];
			$switch_to_oauth = isset( $posted[ GFELOQUA_OPT_PREFIX . 'use_oauth' ] ) && '1' === $posted[ GFELOQUA_OPT_PREFIX . 'use_oauth' ];

			if ( $switch_to_basic || $switch_to_oauth ) {
				$is_switch = true;
			}
		}
		return $is_switch;
	}

	/**
	 * Detect if the user is switching disconnecting from Eloqua
	 *
	 * @return boolean  is_disconnect
	 */
	public function is_disconnect() {
		$is_disconnect = false;

		if ( $this->is_save_postback() ) {
			$posted        = $this->get_posted_settings();
			$is_disconnect = isset( $posted['eloqua_disconnect'] ) && '1' === $posted['eloqua_disconnect'];
		}

		return $is_disconnect;
	}

	/**
	 * Detect if the user tried to setup the authentication credentials
	 *
	 * @return boolean  tried
	 */
	public function tried_to_setup() {
		$tried = false;

		if ( $this->is_save_postback() ) {
			$posted = $this->get_posted_settings();
			$tried  = isset( $posted['sitename'] ) && isset( $posted['username'] ) && isset( $posted['password'] ) &&
				$posted['sitename'] && $posted['username'] && $posted['password'];
		}

		return $tried;
	}

	/**
	 * We need to override the default validate_settings method for cases when they are disconnecting, switching auth methods or their credentials are not valid.
	 * Be sure to call parent::validate_settings() at the end.
	 *
	 * @return bool  settings validation
	 */
	public function validate_settings( $fields, $settings ) {
		if ( $this->is_unactivated() ) {
			return false;
		}

		if ( $this->is_switch() || $this->is_disconnect() ) {
			return true;
		}

		if ( ! $this->get_connection_string() && $this->tried_to_setup() ) {
			return false;
		}

		if ( $this->get_connection_string() && ! $this->test_authentication() ) {
			return false;
		}

		return parent::validate_settings( $fields, $settings );
	}

	/**
	 * We need to override the default get_save_error_message method for cases when they are disconnecting, switching auth methods or their credentials are not valid.
	 * Be sure to call parent::get_save_error_message() at the end.
	 *
	 * @return string  Save Error Message
	 */
	public function get_save_error_message( $sections ) {
		if ( $this->is_unactivated() ) {
			global $gfeloqua_license_manager;

			$error_message = $gfeloqua_license_manager->get_error_message();

			$message = __( 'Error:', 'gfeloqua' );
			if ( $error_message ) {
				$message .= ' ' . trim( $error_message, '.' ) . '. ';
			} else {
				$message .= ' ';
			}
			$license_response = wp_json_encode( $gfeloqua_license_manager->get_last_response() );
			$this->log_debug( __METHOD__ . '() => Unactivated Error: ' . print_r( $license_response, true ) );

			return $message . __( 'If you are having trouble, please contact', 'gfeloqua' ) . ' <a href="https://briandichiara.com" target="_blank" rel="noopener">briandichiara.com</a>';
		}

		if ( $this->is_switch() ) {
			return __( 'Switched authentication method.', 'gfeloqua' );
		}

		if ( $this->is_disconnect() ) {
			return __( 'Your connection settings have been removed.', 'gfeloqua' );
		}

		if ( ! $this->get_connection_string() && $this->tried_to_setup() ) {
			return __( 'Unable to connect to Eloqua. Invalid authentication credentials. (Invalid Connection String)', 'gfeloqua' );
		}

		if ( $this->get_connection_string() && ! $this->test_authentication() ) {
			if ( $this->eloqua_timeout ) {
				return __( 'Unable to reach Eloqua due to a connection timeout. Try again shortly.', 'gfeloqua' );
			} else {
				return __( 'Unable to connect to Eloqua, possibly due to invalid authentication credentials.', 'gfeloqua' );
			}
		}

		return parent::get_save_error_message( $sections );
	}

	/**
	 * We need to override the default get_save_success_message method for cases when they are disconnecting or switching auth methods.
	 * Be sure to call parent::get_save_success_message() at the end.
	 *
	 * @return string  Save Success Message
	 */
	public function get_save_success_message( $sections ) {
		if ( $this->license_key_stored ) {
			return __( 'Your plugin license has been activated successfully!', 'gfeloqua' );
		}

		if ( $this->is_switch() ) {
			return __( 'Switched authentication method.', 'gfeloqua' );
		}

		if ( $this->is_disconnect() ) {
			return __( 'Your connection settings have been removed.', 'gfeloqua' );
		}

		return parent::get_save_success_message( $sections );
	}

	/**
	 * Display a select list of Eloqua Forms pulled from the API
	 *
	 * @param array   $field  the form field.
	 * @param boolean $echo   if the field should be echo'd.
	 *
	 * @return array  $field
	 */
	public function settings_eloqua_forms( $field, $echo = true ) {
		$forms = array(
			array(
				'label' => __( 'Select an Eloqua Form', 'gfeloqua' ),
				'value' => '',
			),
		);

		if ( ! $this->eloqua ) {
			$this->init_eloqua();
		}

		if ( $this->eloqua ) {
			$eloqua_forms = $this->eloqua->get_forms();

			if ( $this->eloqua->is_disconnected() ) {
				$this->clear_connection();
				$error = $this->eloqua->get_last_error( true );
				if ( $echo ) {
					echo $error;
					return;
				} else {
					return $error;
				}
			}

			if ( count( $eloqua_forms ) ) {
				foreach ( $eloqua_forms as $form ) {
					$this->setup_folders( $form );
				}
				foreach ( $eloqua_forms as $form ) {
					$this->add_form_to_array( $form, $forms );
				}
			} else {
				$forms[0]['label'] = __( 'No Eloqua Forms were found.', 'gfeloqua' );
				$eloqua_errors     = $this->eloqua->get_errors();
				if ( $eloqua_errors ) {
					$this->log_error( __METHOD__ . '() => ' . print_r( $eloqua_errors, true ) );
				}
			}
		}

		$field['type']    = 'select';
		$field['choices'] = $forms;

		$html = $this->settings_select( $field, false );

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

	/**
	 * Create array of folders to match Eloqua Folder Structure
	 *
	 * @param object $form  Eloqua Form Object.
	 *
	 * @return void
	 */
	public function setup_folders( $form ) {
		if ( ! isset( $form->folderId ) || ! $form->folderId ) { // @codingStandardsIgnoreLine: ok.
			return;
		}

		if ( isset( $this->folders[ $form->folderId ] ) ) { // @codingStandardsIgnoreLine: ok.
			return;
		}

		if ( ! $this->eloqua ) {
			$this->init_eloqua();
		}

		$folder = $this->eloqua->get_form_folder_name( $form->folderId ); // @codingStandardsIgnoreLine: ok.
		$this->folders[ $form->folderId ] = $folder->name; // @codingStandardsIgnoreLine: ok.
	}

	/**
	 * Add form to folder array
	 *
	 * @param object $form  Eloqua Form object.
	 * @param array &$array Folder array, passed by reference.
	 *
	 * @return void
	 */
	public function add_form_to_array( $form, &$array ) {
		if ( 'form' !== strtolower( $form->type ) ) {
			return;
		}

		if ( isset( $form->folderId ) && $form->folderId ) { // @codingStandardsIgnoreLine: ok.
			$choices = isset( $array[ '_folder' . $form->folderId ]['choices'] ) ? $array[ '_folder' . $form->folderId ]['choices'] : array(); // @codingStandardsIgnoreLine: ok.
			$choices[ '_form' . $form->id ] = array(
				'label' => $form->name . ' (' . $form->currentStatus . ')', // @codingStandardsIgnoreLine: ok.
				'value' => $form->id,
			);

			$array[ '_folder' . $form->folderId ] = array( // @codingStandardsIgnoreLine: ok.
				'label' => $this->folders[ $form->folderId ], // @codingStandardsIgnoreLine: ok.
				'choices' => $choices,
			);
		} elseif ( ! isset( $array[ '_form' . $form->id ] ) ) {
			$array[ '_form' . $form->id ] = array(
				'label' => $form->name . ' (' . $form->currentStatus . ')', // @codingStandardsIgnoreLine: ok.
				'value' => $form->id,
			);
		}
	}

	/**
	 * The Eloqua fields to be mapped to Gravity Forms fields
	 *
	 * @return array  $fields
	 */
	public function get_form_fields_map() {
		$form_id = $this->get_setting( GFELOQUA_OPT_PREFIX . 'form' );
		if ( ! $form_id ) {
			return array();
		}

		if ( ! $this->eloqua ) {
			$this->init_eloqua();
		}

		$custom_fields = $this->eloqua->get_form_fields( $form_id );

		if ( $this->eloqua->is_disconnected() ) {
			$this->clear_connection();
			$error = $this->eloqua->get_last_error( true );
			return array(
				array(
					'name'  => '',
					'label' => $error,
				),
			);
		}

		$field_map = array();

		if ( is_array( $custom_fields ) && count( $custom_fields ) ) {
			foreach ( $custom_fields as $custom_field ) {
				if ( isset( $custom_field->type ) && 'FormFieldGroup' === $custom_field->type ) {
					if ( isset( $custom_field->fields ) && is_array( $custom_field->fields ) && count( $custom_field->fields ) > 0 ) {
						foreach ( $custom_field->fields as $sub_custom_field ) {
							if ( 'submit' === $sub_custom_field->displayType ) { // @codingStandardsIgnoreLine: ok.
								continue;
							}

							$field_map[] = array(
								'name'     => $sub_custom_field->id,
								'label'    => $sub_custom_field->name,
								'required' => $this->eloqua->is_field_required( $sub_custom_field ),
								//'default_value' => $this->get_first_field_by_type( 'name', 3 ),
							);
						}
					}
				} else {
					if ( 'submit' === $custom_field->displayType ) { // @codingStandardsIgnoreLine: ok.
						continue;
					}

					$field_map[] = array(
						'name'     => $custom_field->id,
						'label'    => $custom_field->name,
						'required' => $this->eloqua->is_field_required( $custom_field ),
						//'default_value' => $this->get_first_field_by_type( 'name', 3 ),
					);
				}
			}
		} else {
			$field_map[] = array(
				'name'  => '',
				'label' => __( 'No Fields Found.', 'gfeloqua' ),
			);

			$this->log_debug( __METHOD__ . '() => ' . __( 'No form fields found.', 'gfeloqua' ) );
			$eloqua_errors = $this->eloqua->get_errors();
			if ( $eloqua_errors ) {
				$this->log_error( __METHOD__ . '() => ' . print_r( $eloqua_errors, true ) );
				return array(
					array(
						'name'  => '',
						'label' => print_r( $eloqua_errors, true ),
					),
				);
			}
		}

		return $field_map;
	}

	/**
	 * Ajax method used to clear a specified transient
	 *
	 * @return void
	 */
	public function clear_eloqua_transient() {
		$transient = isset( $_GET['transient'] ) ? sanitize_text_field( $_GET['transient'] ) : false;
		if ( $transient ) {
			if ( ! $this->eloqua ) {
				$this->init_eloqua();
			}
			$this->eloqua->clear_transient( $transient );
			$this->log_debug( __METHOD__ . '() =>  Transient Cleared: ' . print_r( $transient, true ) );
		}

		wp_send_json(
			array(
				'success' => true,
			)
		);
	}

	/**
	 * Main Settings Field for this plugin
	 *
	 * @return array  $settings
	 */
	public function plugin_settings_fields() {

		$this->_maybe_store_settings();
		$this->_maybe_clear_settings();

		global $gfeloqua_license_manager;
		$license_key = $this->get_license_key();

		if ( false === $gfeloqua_license_manager->is_active_license( $license_key ) ) {
			$this->log_debug( __METHOD__ . '() => No valid license found. ' . $gfeloqua_license_manager->get_debug_message() );
			return array(
				array(
					'title'       => __( 'Gravity Forms Eloqua License', 'gfeloqua' ),
					'description' => __( 'Enter your Gravity Forms Eloqua License to activate this plugin.', 'gfeloqua' ),
					'fields'      => array(
						array(
							'name'    => 'license_key',
							'tooltip' => __( 'Don\'t have a license? Purchase one now at ', 'gfeloqua' ) . '<a href="https://briandichiara.com/" target="_blank" rel="noopener">https://briandichiara.com/</a>',
							'label'   => __( 'License Key', 'gfeloqua' ),
							'type'    => 'text',
							'class'   => 'medium',
						),
					),
				), // end settings group.
			);
		}

		$this->log_debug( __METHOD__ . '() => Plugin is licensed. (' . trim( $license_key ) . ')' );

		$auth_fields = array();

		$authenticated = $this->get_connection_string();
		$auth_type     = $this->get_authentication_type();

		if ( 'oauth' === $auth_type && ! $authenticated ) {
			$auth_title = __( 'Login to Eloqua', 'gfeloqua' );

			$auth_fields[] = array(
				'name'    => GFELOQUA_OPT_PREFIX . 'oauth_code',
				'tooltip' => __( 'Login to Eloqua using OAuth', 'gfeloqua' ),
				'label'   => __( 'Login', 'gfeloqua' ),
				'type'    => 'oauth_link',
				'class'   => 'gfeloqua-oauth',
			);

			$auth_fields[] = array(
				'type'       => 'checkbox',
				'name'       => 'switch_to_basic',
				'label'      => __( 'Basic Authentication', 'gfeloqua' ),
				'tooltip'    => __( 'Use Basic HTTP Authentication instead of OAuth', 'gfeloqua' ),
				'horizontal' => true,
				'choices'    => array(
					array(
						'name'  => GFELOQUA_OPT_PREFIX . 'use_basic',
						'label' => __( 'Switch to Basic HTTP Authentication', 'gfeloqua' ),
					),
				),
			);

		} elseif ( 'basic' === $auth_type && ! $authenticated ) {
			$auth_title = __( 'Login to Eloqua', 'gfeloqua' );

			$auth_fields[] = array(
				'name'     => 'sitename',
				'tooltip'  => __( 'Your Site Name is usually your company name without any spaces.', 'gfeloqua' ),
				'label'    => __( 'Site Name', 'gfeloqua' ),
				'type'     => 'text',
				'class'    => 'medium',
				'required' => true,
			);
			$auth_fields[] = array(
				'name'     => 'username',
				'tooltip'  => __( 'Your login user name', 'gfeloqua' ),
				'label'    => __( 'Username', 'gfeloqua' ),
				'type'     => 'text',
				'class'    => 'medium',
				'required' => true,
			);
			$auth_fields[] = array(
				'name'     => 'password',
				'tooltip'  => __( 'Your login password', 'gfeloqua' ),
				'label'    => __( 'Password', 'gfeloqua' ),
				'type'     => 'text',
				'class'    => 'medium',
				'required' => true,
			);

			$auth_fields[] = array(
				'type'       => 'checkbox',
				'name'       => 'switch_to_oauth',
				'label'      => __( 'OAuth', 'gfeloqua' ),
				'tooltip'    => __( 'Use OAuth instead of Basic HTTP Authentication', 'gfeloqua' ),
				'horizontal' => true,
				'choices'    => array(
					array(
						'name'  => GFELOQUA_OPT_PREFIX . 'use_oauth',
						'label' => __( 'Switch to OAuth', 'gfeloqua' ),
					),
				),
			);
		} elseif ( $authenticated ) {
			$auth_title = __( 'Clear Authentication Credentials', 'gfeloqua' );

			$last_auth_text = '';
			$last_auth_date = $this->get_authentication_timestamp();
			if ( $last_auth_date ) {
				$last_auth_text = '<div class="gfeloqua-last-auth-date">' . __( 'Last authenticated:', 'gfeloqua' ) . ' ' . esc_html( $last_auth_date ) . '</div>';
			}

			$auth_fields[] = array(
				'type'       => 'checkbox',
				'name'       => 'eloqua_disconnect',
				'label'      => __( 'Disconnect', 'gfeloqua' ),
				'tooltip'    => __( 'Disconnect your Eloqua account from Gravity Forms', 'gfeloqua' ) . $last_auth_text,
				'horizontal' => true,
				'choices'    => array(
					array(
						'name'  => 'eloqua_disconnect',
						'label' => __( 'Your Eloqua settings are securely stored. To clear these settings, check this box and click "Update Settings".', 'gfeloqua' ),
					),
				),
			);

			$auth_fields[] = array(
				'type'    => 'eloqua_test',
				'name'    => 'eloqua_test',
				'label'   => __( 'Test Connection', 'gfeloqua' ),
				'tooltip' => __( 'Test your Eloqua connection to see the status.', 'gfeloqua' ),
			);
		} else {
			$auth_title = __( 'There was a problem. Please contact plugin author.', 'gfeloqua' );
		}

		$remote_api_fields = array(
			array(
				'type'       => 'text',
				'input_type' => 'number',
				'name'       => 'timeout',
				'label'      => __( 'Request Timeout', 'gfeloqua' ),
				'value'      => '5',
				'tooltip'    => __( 'When calling the Eloqua API, this is the time (in seconds) the request will wait before giving up due to a failed connection.', 'gfeloqua' ),
			),
		); // end remote api fields array.

		$disconnect_fields = array(
			array(
				'type'       => 'checkbox',
				'name'       => GFELOQUA_OPT_PREFIX . 'enable_disconnect_notice',
				'label'      => __( 'Admin Notice', 'gfeloqua' ),
				'tooltip'    => __( 'When enabled, you will see an admin notice in the WordPress Dashboard when your connection to Eloqua is lost', 'gfeloqua' ),
				'horizontal' => true,
				'choices'    => array(
					array(
						'name'  => 'enable_disconnect_notice',
						'label' => __( 'Enable Disconnect Admin Notice', 'gfeloqua' ),
					),
				),
			),
			array(
				'type'       => 'checkbox',
				'name'       => GFELOQUA_OPT_PREFIX . 'enable_disconnect_alert',
				'label'      => __( 'Email Alert', 'gfeloqua' ),
				'tooltip'    => __( 'When enabled, you will be notified by email when your connection to Eloqua is lost', 'gfeloqua' ),
				'horizontal' => true,
				'choices'    => array(
					array(
						'name'  => 'enable_disconnect_alert',
						'label' => __( 'Enable Disconnect Notification Email', 'gfeloqua' ),
					),
				),
			),
			array(
				'name'          => 'disconnect_alert_email',
				'tooltip'       => __( 'Email address to send disconnect alerts', 'gfeloqua' ),
				'label'         => __( 'Email Address', 'gfeloqua' ),
				'type'          => 'text',
				'class'         => 'medium',
				'default_value' => get_bloginfo( 'admin_email' ),
			),
		); // end disconnect fields array.

		$retry_fields = array(
			array(
				'type'       => 'text',
				'input_type' => 'number',
				'name'       => 'retry_limit',
				'label'      => __( 'Retry Attempt Limit', 'gfeloqua' ),
				'value'      => '5',
				'tooltip'    => __( 'This will determine how many retries a submission will receive before failing.', 'gfeloqua' ),
			),
			array(
				'type'    => 'select',
				'name'    => 'retry_interval',
				'label'   => __( 'Retry Interval', 'gfeloqua' ),
				'value'   => '5min',
				'tooltip' => __( 'This determines how frequently failed submissions are retried.', 'gfeloqua' ),
				'choices' => array(
					array(
						'value' => '5min',
						'label' => __( 'Every 5 Minutes', 'gfeloqua' ),
					),
					array(
						'value' => '30min',
						'label' => __( 'Every 30 Minutes', 'gfeloqua' ),
					),
					array(
						'value' => 'hourly',
						'label' => __( 'Every Hour', 'gfeloqua' ),
					),
					array(
						'value' => 'daily',
						'label' => __( 'Every 24 Hours', 'gfeloqua' ),
					),
				),
			),
			array(
				'type'       => 'checkbox',
				'name'       => GFELOQUA_OPT_PREFIX . 'enable_retry_limit_alert',
				'label'      => __( 'Email Alert When Limit Reached', 'gfeloqua' ),
				'tooltip'    => __( 'When enabled, you will be notified by email when an entry has continued to fail and reached its retry limit.', 'gfeloqua' ),
				'horizontal' => true,
				'choices'    => array(
					array(
						'name'  => 'enable_retry_limit_alert',
						'label' => __( 'Enable Retry Limit Notification Email', 'gfeloqua' ),
					),
				),
			),
			array(
				'name'          => 'retry_limit_alert_email',
				'tooltip'       => __( 'Email address to send retry limit alerts', 'gfeloqua' ),
				'label'         => __( 'Email Address', 'gfeloqua' ),
				'type'          => 'text',
				'class'         => 'medium',
				'default_value' => get_bloginfo( 'admin_email' ),
			),
		); // end retry fields array.

		$extension_fields = array(
			array(
				'name' => GFELOQUA_OPT_PREFIX . 'extensions',
				'type' => 'gfeloqua_extensions',
			),
		);

		// field groups.
		$field_groups = array(
			array(
				'title'  => $auth_title,
				'fields' => $auth_fields,
			), // end settings group.

			array(
				'title'       => __( 'Remote API Call Settings', 'gfeloqua' ),
				'description' => __( 'Adjust these to work best with your server/connection.', 'gfeloqua' ),
				'fields'      => $remote_api_fields,
			), // end settings group.

			array(
				'title'       => __( 'Disconnect Notification', 'gfeloqua' ),
				'description' => __( 'If your site ever loses connection with Eloqua for any reason, this alert will notify you when Eloqua cannot be reached, allowing you to correct the issue as quickly as possible.', 'gfeloqua' ),
				'fields'      => $disconnect_fields,
			), // end settings group.

			array(
				'title'       => __( 'Retry Attempts', 'gfeloqua' ),
				'description' => __( 'If an entry fails to send over to Eloqua, this plugin will automatically retry the submission a number of times before failing completely.', 'gfeloqua' ),
				'fields'      => $retry_fields,
			), // end settings group.

			array(
				'title'       => __( 'Extensions', 'gfeloqua' ),
				'description' => __( 'Gravity Forms Eloqua now supports Extensions! See more on', 'gfeloqua' ) . ' <a href="https://briandichiara.com" target="_blank" rel="noopener">briandichiara.com</a>',
				'fields'      => $extension_fields,
			),
		);

		return $field_groups;
	}

	/**
	 * Retrieve the oauth token from options
	 *
	 * @param bool $object  Return entire object.
	 *
	 * @return string  oauth_token
	 */
	public function get_oauth_token( $object = true ) {
		$access = get_option( GFELOQUA_OPT_PREFIX . 'access_token' );

		if ( ! is_object( $access ) ) {
			return false;
		}

		if ( $object ) {
			return $access;
		}

		return $access->getToken();
	}

	/**
	 * Retrieve the oauth refresh token from options
	 *
	 * @return string  refresh_token
	 */
	function get_oauth_refresh_token() {
		$refresh_token = get_option( GFELOQUA_OPT_PREFIX . 'oauth_refresh_token' );
		return $refresh_token;
	}

	/**
	 * Retrieve the authstring from settings or try to generate one
	 *
	 * @return string  oauth_token
	 */
	public function get_authstring() {
		$authstring = get_option( GFELOQUA_OPT_PREFIX . 'authstring' );

		if ( ! $authstring ) {
			$authstring = $this->generate_authstring();
		}

		return $authstring;
	}

	/**
	 * Generate and store an auth string either from provided $source_array or from posted values
	 *
	 * @param array $source_array  Array containing fields to generate authstring.
	 *
	 * @return string  authstring
	 */
	public function generate_authstring( $source_array = array() ) {
		$authstring = '';
		$posted     = array();

		if ( $this->is_save_postback() ) {
			$posted = $this->get_posted_settings();
		}

		if ( $source_array ) {
			$posted = $source_array;
		}

		if ( ! $posted ) {
			return $authstring;
		}

		$sitename = isset( $posted['sitename'] ) && $posted['sitename'] ? trim( $posted['sitename'] ) : '';
		$username = isset( $posted['username'] ) && $posted['username'] ? trim( $posted['username'] ) : '';
		$password = isset( $posted['password'] ) && $posted['password'] ? trim( $posted['password'] ) : '';

		if ( $sitename && $username && $password ) {
			$authstring = base64_encode( "{$sitename}\\{$username}:{$password}" );
		}

		if ( $authstring ) {
			if ( $this->test_authentication( $authstring ) ) {
				update_option( GFELOQUA_OPT_PREFIX . 'authstring', $authstring );
			} elseif ( ! $this->eloqua_timeout ) {
				$authstring = '';
			} else {
				$this->log_error( __METHOD__ . '() => Authentication test failed due to time out.' );
			}
		}

		return $authstring;
	}

	/**
	 * Special Gravity Forms Settings Field that allows a user to connect to Eloqua using OAuth
	 *
	 * @param array   $field  form field.
	 * @param boolean $echo   if the field shoul be echo'd.
	 *
	 * @return string  the field html
	 */
	public function settings_oauth_link( $field, $echo = true ) {
		if ( ! $this->eloqua ) {
			$this->init_eloqua();
		}

		$field['type'] = 'oauth_link'; //making sure type is set to oauth_link.
		$attributes    = $this->get_field_attributes( $field );
		$default_value = rgar( $field, 'value' ) ? rgar( $field, 'value' ) : rgar( $field, 'default_value' );
		$value         = $this->get_setting( $field['name'], $default_value );

		$name      = esc_attr( $field['name'] );
		$tooltip   = isset( $choice['tooltip'] ) ? gform_tooltip( $choice['tooltip'], rgar( $choice, 'tooltip_class' ), true ) : '';
		$oauth_url = $this->eloqua->get_oauth_url( $this->get_plugin_settings_url() );

		$html = '<a id="' . esc_attr( GFELOQUA_OPT_PREFIX ) . 'oauth" data-width="750" data-height="750" class="button" href="' . $oauth_url . '">' . __( 'Authenticate with your Eloqua account', 'gfeloqua' ) . '</a>
			<span id="' . GFELOQUA_OPT_PREFIX . 'oauth_code" style="display:none;">' . __( 'If you have a code, paste it here:', 'gfeloqua' ) . ' <input type="text" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" style="width:375px;" /></span>';

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

	/**
	 * Special Gravity Forms Settings Field that gives users a button to test connection.
	 *
	 * @param array   $field  form field.
	 * @param boolean $echo   if the field shoul be echo'd.
	 *
	 * @return string  the field html
	 */
	public function settings_eloqua_test( $field, $echo = true ) {
		$field['type'] = 'eloqua_test'; //making sure type is set to eloqua_test.
		$attributes    = $this->get_field_attributes( $field );
		$default_value = rgar( $field, 'value' ) ? rgar( $field, 'value' ) : rgar( $field, 'default_value' );
		$value         = $this->get_setting( $field['name'], $default_value );

		$name    = esc_attr( $field['name'] );
		$tooltip = isset( $choice['tooltip'] ) ? gform_tooltip( $choice['tooltip'], rgar( $choice, 'tooltip_class' ), true ) : '';

		$html = '<a id="' . esc_attr( GFELOQUA_OPT_PREFIX ) . 'test" class="button" href="#">' . __( 'Test Connection to Eloqua', 'gfeloqua' ) . '</a>';

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

	/**
	 * Test Connection to Eloqua.
	 */
	public function ajax_connection_test() {
		$test = $this->test_authentication();

		$result = $test ? esc_html__( 'Success!', 'gfeqloqua' ) : esc_html__( 'Failed.', 'gfeloqua' );
		$class  = $test ? ' success' : ' failed';

		$response = array(
			'html' => '<span class="gfeloqua-connection-test-result' . esc_attr( $class ) . '">' . $result . '</span>',
		);

		wp_send_json( $response );
		exit();
	}

	/**
	 * Special Gravity Forms Settings Field to display Gravity Forms Eloqua Addons
	 *
	 * @param array   $field  form field.
	 * @param boolean $echo   if the field shoul be echo'd.
	 *
	 * @return string  the field html
	 */
	public function settings_gfeloqua_extensions( $field, $echo = true ) {
		$field['type'] = 'gfeloqua_extensions'; //making sure type is set to gfeloqua_extensions
		$attributes    = $this->get_field_attributes( $field );

		global $gfeloqua_extensions;
		$extensions = $gfeloqua_extensions->get_extensions();

		$view = GFELOQUA_PATH . 'views/extensions.php';
		ob_start();
		include( $view );
		$extensions_view = ob_get_clean();

		if ( $echo ) {
			echo $extensions_view;
		}

		return $extensions_view;
	}

	/**
	 * Clears stored authentication values
	 *
	 * @return [type] [description]
	 */
	public function clear_connection() {
		delete_option( GFELOQUA_OPT_PREFIX . 'access_token' );
		delete_option( GFELOQUA_OPT_PREFIX . 'oauth_token' );
		delete_option( GFELOQUA_OPT_PREFIX . 'oauth_token_type' );
		delete_option( GFELOQUA_OPT_PREFIX . 'oauth_refresh_token' );
		delete_option( GFELOQUA_OPT_PREFIX . 'authentication_timestamp' );
		delete_option( GFELOQUA_OPT_PREFIX . 'authstring' );
		$this->log_debug( __METHOD__ . '() => ' . __( 'The Eloqua connection credentials have been cleared.', 'gfeloqua' ) );
	}

	/**
	 * Used whenever disconnect checkbox value is present
	 *
	 * @return bool  if settings where cleared
	 */
	public function _maybe_clear_settings() {
		if ( $this->is_save_postback() ) {

			$posted = $this->get_posted_settings();

			if ( isset( $posted['eloqua_disconnect'] ) && '1' === $posted['eloqua_disconnect'] ) {

				$this->clear_connection();

				$this->log_debug( __METHOD__ . '() => Eloqua Disconnected.' );

				return true;
			}
		}

		return false;
	}

	/**
	 * We don't want to store the authentication credentials in the database,
	 * so hijack the values and overwrite the save values without any extra values
	 *
	 * @return void
	 */
	public function _maybe_store_settings() {
		$settings = $this->get_plugin_settings();

		// backwards compatibility in case credentials are stored.
		$authstring = $this->generate_authstring( $settings );
		if ( $authstring ) {
			unset( $settings['sitename'] );
			unset( $settings['username'] );
			unset( $settings['password'] );
			$this->update_plugin_settings( $settings );
		}

		// backwards compatibility in case authstring is stored.
		if ( isset( $settings['authstring'] ) ) {
			update_option( GFELOQUA_OPT_PREFIX . 'authstring', $settings['authstring'] );
			unset( $settings['authstring'] );
			$this->update_plugin_settings( $settings );
		}

		// make sure this doesn't get stored.
		unset( $settings['eloqua_disconnect'] );

		// unset checkboxes.
		if ( ! isset( $settings['enable_disconnect_alert'] ) ) {
			$settings['enable_disconnect_alert'] = '';
		}
		if ( ! isset( $settings['enable_disconnect_notice'] ) ) {
			$settings['enable_disconnect_notice'] = '';
		}

		$this->update_plugin_settings( $settings );

		if ( $this->is_save_postback() ) {

			$posted = $this->get_posted_settings();

			if ( isset( $posted[ GFELOQUA_OPT_PREFIX . 'use_basic' ] ) && '1' === $posted[ GFELOQUA_OPT_PREFIX . 'use_basic' ] ) {
				update_option( GFELOQUA_OPT_PREFIX . 'auth_basic', '1' );
				delete_option( GFELOQUA_OPT_PREFIX . 'use_oauth' );
				delete_option( GFELOQUA_OPT_PREFIX . 'oauth_token' );
				delete_option( GFELOQUA_OPT_PREFIX . 'access_token' );
			} elseif ( isset( $posted[ GFELOQUA_OPT_PREFIX . 'use_oauth' ] ) && '1' === $posted[ GFELOQUA_OPT_PREFIX . 'use_oauth' ] ) {
				update_option( GFELOQUA_OPT_PREFIX . 'use_oauth', '1' );
				delete_option( GFELOQUA_OPT_PREFIX . 'auth_basic' );
				delete_option( GFELOQUA_OPT_PREFIX . 'authstring' );
			}

			// This should only be necessary if Basic Authentication is used.
			$this->generate_authstring();

			// This should only be necessary if OAuth is used. TODO: Is this supposed to be happening here?
			$this->generate_oauth_token();
		}
	}

	/**
	 * When we have an OAuth code, we need to generate the token immediately
	 *
	 * @param string $code  OAuth code.
	 *
	 * @return mixed  $token or false
	 */
	public function generate_oauth_token( $code = '' ) {
		if ( $this->is_save_postback() ) {
			$param = GFELOQUA_OPT_PREFIX . 'oauth_code';
			$code  = isset( $_POST[ $param ] ) ? sanitize_text_field( $_POST[ $param ] ) : '';
		}

		if ( ! $code ) {
			if ( ! $this->get_oauth_token() ) {
				$this->log_debug( __METHOD__ . '() => OAUTH Code not present.' );
			}
			return false;
		}

		// Fix spaces in Auth Code.
		$code = str_replace( ' ', '+', $code );

		if ( ! $this->eloqua ) {
			$this->init_eloqua();
		}

		$access_token = $this->eloqua->get_access_token( $code, $this->timeout );

		if ( ! $access_token ) {
			$error = $this->eloqua->get_last_error( true );
			$this->log_debug( __METHOD__ . '() => ' . $error );
			return false;
		}

		$this->log_debug( __METHOD__ . '() => Storing OAuth Token.' );
		update_option( GFELOQUA_OPT_PREFIX . 'access_token', $access_token );
		update_option( GFELOQUA_OPT_PREFIX . 'authentication_timestamp', date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) );

		// Refresh the eloqua object.
		$this->eloqua = $this->init_eloqua( $access_token, true );

		return true;
	}

	/**
	 * Check to make sure current blog domain matches the network domain
	 *
	 * @return bool  If domains match.
	 */
	public function current_site_matches_network() {
		global $gfeloqua_license_manager;
		return $gfeloqua_license_manager->get_current_blog_domain() === untrailingslashit( network_site_url() );
	}

	/**
	 * Retrieves the stored license key
	 *
	 * @return string  Licence key (if any).
	 */
	public function get_license_key() {
		$param = 'license_key';
		if ( is_multisite() ) {
			if ( $this->current_site_matches_network() ) {
				$license_key = get_site_option( GFELOQUA_OPT_PREFIX . $param );
				if ( ! $license_key ) {
					// Check current site for key and move to network.
					$license_key = get_blog_option( get_current_blog_id(), GFELOQUA_OPT_PREFIX . $param );
					if ( $license_key ) {
						$this->store_license_key( $license_key );
					}
				}
			} else {
				// return site specific option
				$license_key = get_blog_option( get_current_blog_id(), GFELOQUA_OPT_PREFIX . $param );
			}
		} else {
			$license_key = get_option( GFELOQUA_OPT_PREFIX . $param );

			if ( ! $license_key ) {
				// Legacy fallback.
				$license_key = $this->get_plugin_setting( $param );
			}
		}
		return $license_key;
	}

	/**
	 * When we have an OAuth code, we need to generate the token immediately
	 *
	 * @param string $code  OAuth code.
	 *
	 * @return mixed  $token or false
	 */
	public function store_license_key( $license_key = '' ) {
		if ( $this->is_save_postback() || $license_key ) {

			$param = 'license_key';

			if ( ! $license_key ) {
				$this->log_debug( __METHOD__ . '() => Reading License Key from posted values.' );
				$posted      = $this->get_posted_settings();
				$license_key = isset( $posted[ $param ] ) ? sanitize_text_field( $posted[ $param ] ) : '';
			}

			if ( ! $license_key ) {
				if ( ! $this->get_plugin_setting( $param ) ) {
					$this->log_debug( __METHOD__ . '() => License key missing.' );
				}
				return false;
			}

			if ( $this->is_activated ) {
				if ( is_multisite() ) {
					if ( $this->current_site_matches_network() ) {
						update_site_option( GFELOQUA_OPT_PREFIX . $param, $license_key );
					} else {
						update_blog_option( get_current_blog_id(), GFELOQUA_OPT_PREFIX . $param, $license_key );
					}
				} else {
					update_option( GFELOQUA_OPT_PREFIX . $param, $license_key );
				}
				$this->license_key_stored = true;
				$this->log_debug( __METHOD__ . '() => License key stored.' );
			} else {
				$this->log_debug( __METHOD__ . '() => Attempting to activate License Key.' );
				global $gfeloqua_license_manager;
				if ( $gfeloqua_license_manager->activate( $license_key ) ) {
					$this->log_debug( __METHOD__ . '() => License Activated!' );
					$this->is_activated = true;
					$this->store_license_key( $license_key );
				} else {
					$this->log_debug( __METHOD__ . '() =>  License Activation failed: ' . print_r( $gfeloqua_license_manager->get_last_response(), true ) );
				}
			}
		}

		return false;
	}

	/**
	 * Send the form data over to Eloqua with the matched field data
	 *
	 * @param array $feed   GF Feed Array.
	 * @param array $entry  Posted Entry Data.
	 * @param array $form   GF Form Array.
	 *
	 * @return void
	 */
	public function process_feed( $feed, $entry, $form, $manual = false ) {
		$this->entry_notes->set_entry_id( $entry['id'] )
			->set_form_id( $form['id'] );

		if ( ! $this->test_authentication() ) {
			$note = __( 'Authentication Failure', 'gfeloqua' ) . ' => ';
			$this->entry_notes->add( $note, $feed );
			$this->entry_notes->done();

			$this->log_error( __METHOD__ . '() => ' . $note . ': ' . print_r( $feed, true ) );

			return false;
		}

		$form_id = $feed['meta'][ GFELOQUA_OPT_PREFIX . 'form' ];

		$form_submission = new stdClass();

		$form_submission->id                   = (int) $form_id;
		$form_submission->type                 = 'FormData';
		$form_submission->submittedAt = (int) current_time( 'timestamp' ); // @codingStandardsIgnoreLine: ok.
		$form_submission->submittedByContactId = null; // @codingStandardsIgnoreLine: ok.
		$form_submission->fieldValues = array(); // @codingStandardsIgnoreLine: ok.

		foreach ( $feed['meta'] as $key => $gf_field_id ) {

			if ( false !== strpos( $key, 'mapped_fields_' ) ) {
				if ( ! isset( $entry[ $gf_field_id ] ) && ! isset( $entry[ $gf_field_id . '.1' ] ) ) {
					continue;
				}

				$key = str_replace( 'mapped_fields_', '', $key );

				$field_value       = new stdClass();
				$field_value->id   = (int) $key;
				$field_value->type = 'FieldValue';
				if ( isset( $entry[ $gf_field_id . '.1' ] ) ) {
					$field_value->value = '';
					foreach ( $entry as $entry_key => $value ) {
						if ( false !== strpos( $entry_key, $gf_field_id . '.' ) && $value ) {
							$field_value->value .= $field_value->value ? ',' . $value : $value;
						}
					}
				} else {
					$field_value->value = $entry[ $gf_field_id ];
				}

				$form_submission->fieldValues[] = $field_value; // @codingStandardsIgnoreLine: ok.
			}
		}

		if ( isset( $feed['meta'][ GFELOQUA_OPT_PREFIX . 'use_cookie_data' ] ) ) {
			if ( $feed['meta'][ GFELOQUA_OPT_PREFIX . 'use_cookie_data' ] ) {
				$note = __( 'Attaching cookie data to submission.', 'gfeloqua' );
				$this->log_debug( __METHOD__ . '() => ' . $note );
				$this->entry_notes->add_error( $note );

				$eloqua_cookies = apply_filters( 'gfeloqua_eloqua_cookies', array(
					'elqCustomerGUID' => 'ELOQUA/GUID', // Example: F3D6C58C478940B8A8F5C585D756162E
					//'elqCountry'      => 'ELQCOUNTRY', // Example: US
					//'elqStatus'       => 'ELQSTATUS', // Example: OK
					//'BKUT'            => 'BKUT', // Example: 1529582256
					//'_CG_u'           => '__CG/u', // Example: 4469636128322787300
					//'_CG_s'           => '__CG/s', // Example: 257056290
					//'_CG_t'           => '__CG/t', // Example: 1541881478145
					//'_CG_c'           => '__CG/c', // Example: 8
					//'_CG_k'           => '__CG/k', // Example: login.eloqua.com/46/818/51945
					//'_CG_f'           => '__CG/f', // Example: 1
					//'_CG_i'           => '__CG/i', // Example: 1
				) );

				if ( is_array( $eloqua_cookies ) && count( $eloqua_cookies ) ) {
					foreach ( $eloqua_cookies as $field => $cookie ) {
						$key = false;
						if ( strpos( $cookie, '/' ) !== false ) {
							list( $cookie, $key ) = explode( '/', $cookie );
						}

						if ( ! isset( $_COOKIE[ $cookie ] ) ) {
							continue;
						}

						$source = $_COOKIE[ $cookie ];

						if ( $key ) {
							$data = array();

							if ( strpos( $source, '%3A' ) !== false ) {
								$source = urldecode( $source );
								if ( strpos( $source, ',' ) === false ) {
									continue;
								}

								$pairs = explode( ',', $source );

								foreach ( $pairs as $pair ) {
									list( $k, $v ) = explode( ':', $pair );
									$data[ $k ]    = $v;
								}
							} elseif ( strpos( $source, '=' ) !== false ) {
								parse_str( $source, $data );
							}

							if ( ! isset( $data[ $key ] ) ) {
								continue;
							}

							$value = $data[ $key ];

						} else {
							$value = $source;
						}

						if ( ! $value ) {
							continue;
						}

						$cookie_value        = new stdClass();
						$cookie_value->id    = $field;
						$cookie_value->type  = 'FieldValue';
						$cookie_value->value = $value;

						$form_submission->fieldValues[] = $cookie_value; // @codingStandardsIgnoreLine: ok.
					}
				}
			}
		}

		$custom_data = apply_filters( 'gfeloqua_custom_data', array(
			// 'elqCookieWrite' => 0, // (bool) 0 / 1
		) );

		if ( is_array( $custom_data ) && count( $custom_data ) ) {
			$note = __( 'Attaching custom data to submission.', 'gfeloqua' );
			$this->log_debug( __METHOD__ . '() => ' . $note );
			$this->entry_notes->add_error( $note );
			foreach ( $custom_data as $key => $value ) {
				$custom_value        = new stdClass();
				$custom_value->id    = $key;
				$custom_value->type  = 'FieldValue';
				$custom_value->value = $value;

				$form_submission->fieldValues[] = $custom_value; // @codingStandardsIgnoreLine: ok.
			}
		}

		$note = __( 'Submitting Object to Eloqua', 'gfeloqua' );
		$this->log_debug( __METHOD__ . '() => ' . $note );

		$response = $this->eloqua->submit_form( $form_id, $form_submission );
		$errors   = $this->eloqua->get_errors();
		$this->prepare_errors( $errors, $entry['id'], $manual );

		if ( ! $response || $errors ) {

			if ( $response ) {
				$this->mark_as_sent( $entry['id'], $form['id'], $errors );
			} else {
				gform_update_meta( $entry['id'], GFELOQUA_OPT_PREFIX . 'success', $this->text_for_failed, $form['id'] );

				$note = __( 'Submission failed, empty response from Eloqua.', 'gfeloqua' );
				$this->entry_notes->add_error( $note );
			}

			$status = false;
		} else {
			$this->entry_notes->add_debug( $note, $form_submission );
			$this->mark_as_sent( $entry['id'], $form['id'] );
			$status = true;
		}

		$this->entry_notes->done();

		return $status;

	}

	/**
	 * Prepare errors to be displayed or stored.
	 *
	 * @param array $errors    Array of Errors.
	 * @param int   $entry_id  GF Entry ID.
	 * @param bool  $manual    Was this manually logged?
	 */
	public function prepare_errors( $errors = false, $entry_id = null, $manual = false ) {
		$this->entry_notes->set_entry_id( $entry_id );

		$last_response = false;
		if ( $this->eloqua->last_response ) {
			$last_response = $this->eloqua->last_response;
		} elseif ( $this->last_response ) {
			$last_response = $this->last_response;
		}

		if ( $last_response ) {
			$note = __( 'Logging Last Response', 'gfeloqua' );
			if ( is_object( $last_response ) ) {
				$last_response = wp_json_encode( $last_response );
			}
			$this->entry_notes->add_debug( $note, $last_response );
			$this->log_debug( __METHOD__ . '() => ' . $note . ': ' . print_r( $last_response, true ) );
		}

		if ( $errors ) {
			$attempts = $this->get_retry_attempts( $entry_id );
			$manual   = is_string( $manual ) && 'manual' === $manual ? ' - MANUAL' : '';
			$note     = sprintf( __( 'Attempt', 'gfeloqua' ) . ' %s%s', $attempts, $manual );

			foreach ( $errors as $error ) {
				$note = __( 'Submission Error', 'gfeloqua' );
				$this->entry_notes->add_error( $note, $error );
				$this->log_error( __METHOD__ . '() => ' . $note . ': ' . print_r( $error, true ) );
			}
		}

		$this->entry_notes->done();
	}

	/**
	 * Mark entry as sent
	 *
	 * @param int   $entry_id  GF Entry ID.
	 * @param int   $form_id   GF Form ID.
	 * @param array $errors    Errors during submission.
	 */
	public function mark_as_sent( $entry_id, $form_id = null, $errors = false ) {
		$success_text = $this->text_for_success;
		if ( $errors ) {
			$success_text = 'Error';
		}

		gform_update_meta( $entry_id, GFELOQUA_OPT_PREFIX . 'success', $success_text, $form_id );

		if ( $errors ) {
			$note = __( 'Sent with errors.', 'gfeloqua' );
			$this->entry_notes->add_error( $note, $errors );
			$this->log_error( __METHOD__ . '() => ' . $note . ': ' . print_r( $errors, true ) );
		} else {
			$note = __( 'Submission sent successfully.', 'gfeloqua' );
			$this->entry_notes->add( $note, $success_text );
			$this->log_debug( __METHOD__ . '() => ' . $note );
		}

		$this->entry_notes->done();
	}

	/**
	 * Check if entry limit has been reached.
	 *
	 * @param int $entry_id  GF Entry ID.
	 *
	 * @return bool  Limit reached boolean.
	 */
	public function limit_reached( $entry_id ) {
		return gform_get_meta( $entry_id, GFELOQUA_OPT_PREFIX . 'limit_reached' );
	}

	/**
	 * Stop retry if limit reached
	 *
	 * @param int $entry_id  GF Entry ID.
	 * @param int $form_id   GF Form ID.
	 */
	public function cease_retries( $entry_id, $form_id = null ) {
		if ( $this->limit_reached( $entry_id ) ) {
			return;
		}

		gform_update_meta( $entry_id, GFELOQUA_OPT_PREFIX . 'limit_reached', '1', $form_id );

		$this->log_debug(
			__METHOD__ . '() => Automatic retry limit reached: ' . print_r(
				array(
					'Entry ID:' => $entry_id,
					'Form ID:'  => $form_id,
				), true
			)
		);

		$this->retry_limit_notification( $entry_id, $form_id );
	}

	/**
	 * Send an email when Eloqua is disconnected
	 *
	 * @return void
	 */
	public function disconnect_notification() {
		$enabled = $this->get_plugin_setting( 'enable_disconnect_alert' );

		if ( ! $enabled ) {
			return;
		}

		$recipient = $this->get_plugin_setting( 'disconnect_alert_email' );
		if ( ! $recipient ) {
			return;
		}

		if ( ! $this->test_authentication() ) {
			if ( $this->eloqua_timeout ) {
				$this->log_error( __METHOD__ . '() => Connection to Eloqua timed out.' );
				return;
			}

			$this->log_error( __METHOD__ . '() => Eloqua Disconnected' );

			// send email.
			$subject            = __( 'Eloqua Disconnected from Gravity Forms', 'gfeloqua' );
			$headers            = array(
				'From: GFEloqua <' . get_bloginfo( 'admin_email' ) . '>',
				'Content-Type: text/html; charset=UTF-8',
			);
			$settings_page_url  = $this->get_plugin_settings_url();
			$settings_page_link = '<a href="' . $settings_page_url . '">' . $settings_page_url . '</a>';
			$template           = locate_template( array( 'gfeloqua/disconnected-email.php', 'gfeloqua-disconnected-email.php' ) );
			if ( ! $template || ! file_exists( $template ) ) {
				$template = GFELOQUA_PATH . 'views/disconnected-email.php';
			}

			ob_start();
			include( $template );
			$message = ob_get_clean();

			$this->log_debug( __METHOD__ . '() => Sending disconnect notice email.' );

			// convert multiple recipients to array.
			if ( false !== strpos( $recipient, ',' ) ) {
				$recipients = explode( ',', $recipient );
				$recipients = array_map( 'trim', $recipients );
				wp_mail( $recipients, $subject, $message, $headers );
			} else {
				wp_mail( $recipient, $subject, $message, $headers );
			}
		}
	}
	/**
	 * Send an email when retry limit is reached for an entry
	 *
	 * @return void
	 */
	public function retry_limit_notification( $entry_id, $form_id = null ) {
		$enabled = $this->get_plugin_setting( 'enable_retry_limit_alert' );
		if ( ! $enabled ) {
			return;
		}

		$recipient = $this->get_plugin_setting( 'retry_limit_alert_email' );
		if ( ! $recipient ) {
			return;
		}

		$entry = GFAPI::get_entry( $entry_id );
		if ( ! $entry ) {
			return;
		}

		// send email.
		$subject = __( 'Eloqua Submission Limit Reached', 'gfeloqua' );
		$headers = array(
			'From: GFEloqua <' . get_bloginfo( 'admin_email' ) . '>',
			'Content-Type: text/html; charset=UTF-8',
		);

		$query_string  = 'page=gf_entries&view=entry&id=' . absint( $form_id ) . '&lid=' . esc_attr( $entry_id );
		$entry_url     = $this->get_admin_link( 'admin.php?' . $query_string );
		$link_to_entry = '<a href="' . esc_url( $entry_url ) . '">' . esc_html( $entry_url ) . '</a>';

		$template = locate_template( array( 'gfeloqua/retry-limit.php', 'gfeloqua-retry-limit.php' ) );
		if ( ! $template || ! file_exists( $template ) ) {
			$template = GFELOQUA_PATH . 'views/retry-limit.php';
		}

		ob_start();
		include( $template );
		$message = ob_get_clean();

		$this->log_debug( __METHOD__ . '() => Sending retry limit email' );

		// convert multiple recipients to array.
		if ( false !== strpos( $recipient, ',' ) ) {
			$recipients = explode( ',', $recipient );
			$recipients = array_map( 'trim', $recipients );
			wp_mail( $recipients, $subject, $message, $headers );
		} else {
			wp_mail( $recipient, $subject, $message, $headers );
		}
	}

	/**
	 * Display an Admin Alert when Eloqua is Disconnected
	 *
	 * @return void
	 */
	public function disconnect_notice() {
		$enabled = $this->get_plugin_setting( 'enable_disconnect_notice' );
		if ( ! $enabled ) {
			return;
		}

		if ( ! $this->test_authentication() ) {
			$this->log_debug( __METHOD__ . '() => Displaying Admin Disconnect Notice.' );
			$settings_page_url = $this->get_plugin_settings_url();

			if ( $this->eloqua_timeout ) {
				echo '<div class="notice notice-error is-dismissible">
			        <p>' . __( 'It seems as though Gravity Forms had trouble reaching Eloqua due to a timeout. We will try again momentarily', 'gfeloqua' ) . '</p>
			    </div>';
			} else {
				echo '<div class="notice notice-error is-dismissible">
			        <p>' . sprintf( __( 'It seems as though Gravity Forms has lost connection to your Eloqua account. <a href="%s">Click here to re-connect.</a>', 'gfeloqua' ), $settings_page_url ) . '</p>
			    </div>';
			}
		}
	}

	/**
	 * Returns a formatted URL including any changes to Admin URL by ITSEC.
	 *
	 * @param string $path  Path to include with URL.
	 *
	 * @return string  Full Path to admin.
	 */
	public function get_admin_link( $path = '' ) {
		$itsec_settings = false;

		if ( is_multisite() ) {
			$itsec_settings = get_site_option( 'itsec-storage' );
			if ( ! $itsec_settings ) {
				$itsec_settings = get_blog_option( get_current_blog_id(), 'itsec-storage' );
			}
		} else {
			$itsec_settings = get_option( 'itsec-storage' );
		}

		$admin_link = admin_url( $path );

		if ( ! $itsec_settings ) {
			return $admin_link;
		}

		if ( ! isset( $itsec_settings['hide-backend'] ) || true !== $itsec_settings['hide-backend']['enabled'] ) {
			return $admin_link;
		}

		if ( false !== strpos( $admin_link, '?' ) ) {
			$admin_link .= '&itsec-hb=' . $itsec_settings['hide-backend']['slug'];
		} else {
			$admin_link .= '?itsec-hb=' . $itsec_settings['hide-backend']['slug'];
		}

		return $admin_link;
	}

	/**
	 * Display Notes from Submission to Eloqua
	 *
	 * @param object $form   Gravity Object.
	 * @param object $entry  Entry Object.
	 */
	public function display_entry_notes( $form, $entry ) {
		if ( is_admin() ) {
			$this->entry_notes->set_entry_id( $entry['id'] )
				->set_form_id( $form['id'] );

			$notes = $this->entry_notes->get();

			if ( ! $notes ) {
				return;
			}

			$notes          = array_reverse( $notes );
			$success        = $this->is_successful_submission( $entry['id'], $notes );
			$retry_attempts = $this->get_retry_attempts( $entry['id'] );
			if ( ! $retry_attempts ) {
				$retry_attempts = 0;
			}
			$retry_limit = $this->get_plugin_setting( 'retry_limit' );
			if ( ! $retry_limit ) {
				$retry_limit = '&#x221e;'; // infinite symbol
			}
			$limit_reached = $retry_attempts >= $retry_limit ? ' gfeloqua-limit-reached' : '';

			// override the notes with the success message.
			if ( $success && ! isset( $_GET['gfeloqua-entry-debug'] ) ) {
				$notes = array( __( 'Data successfully sent to Eloqua!', 'gfeloqua' ) );
			}

			$view          = GFELOQUA_PATH . 'views/entry-notes.php';
			$notes_display = GFELOQUA_PATH . 'views/notes-display.php';

			include( $view );
		}
	}

	/**
	 * Determine if the submission is successful
	 *
	 * @param int   $entry_id  GF Entry ID.
	 * @param array $notes     Notes to check for successful.
	 *
	 * @return bool  If was successful
	 */
	public function is_successful_submission( $entry_id, $notes = array(), $form_id = null ) {
		$this->entry_notes->set_entry_id( $entry_id )
			->set_form_id( $form_id );

		$success      = gform_get_meta( $entry_id, GFELOQUA_OPT_PREFIX . 'success' );
		$datestamp    = current_time( 'mysql' );
		$success_note = __( 'Determined to be successful based on:', 'gfeloqua' ) . ' %s.';

		if ( 1 === $success ) {
			$debug_note = sprintf( $success_note, 'marked as success' );
			$this->entry_notes->add_debug( $debug_note );
			$this->log_debug( __METHOD__ . '() => Is Success (' . $debug_note . '): ' . $entry_id );
			$this->entry_notes->done();
			return true;
		}

		if ( false !== strpos( strtolower( $success ), 'success' ) ) {
			$debug_note = sprintf( $success_note, 'string "success" found in success meta' );
			$this->entry_notes->add_debug( $debug_note );
			$this->log_debug( __METHOD__ . '() => Is Success (' . $debug_note . '): ' . $entry_id );
			$this->entry_notes->done();
			return true;
		}

		$notes = $this->entry_notes->get();

		if ( 0 === $success || false !== strpos( strtolower( $success ), 'failed' ) ) {
			$success = false;
		} elseif ( count( $notes ) ) { // legacy support.
			foreach ( $notes as $note ) {
				if ( is_string( $note ) ) {
					if ( strpos( $note, 'Data successfully sent' ) !== false ) {
						$success = true;

						$debug_note = sprintf( $success_note, 'string "Data successfully sent" found in notes' );
						$this->entry_notes->add( $debug_note );
						$this->log_debug( __METHOD__ . '() => Is Success (' . $debug_note . '): ' . print_r( $note, true ) );

						// mark it as successful to future-proof.
						$this->mark_as_sent( $entry_id );
						break;
					} elseif ( false !== strpos( $note, 'Unknown error' ) ) {
						$success = false;
					}
				}
			}
		}

		$this->entry_notes->done();

		return $success;
	}

	/**
	 * Method to retry entry submission (used for AJAX and Cron)
	 *
	 * @return json
	 */
	public function resubmit_entry( $entry_id = null, $form_id = null ) {
		if ( ! $entry_id ) {
			$entry_id = isset( $_GET['entry_id'] ) && $_GET['entry_id'] ? (int) sanitize_text_field( $_GET['entry_id'] ) : false;
		}

		if ( ! $form_id ) {
			$form_id = isset( $_GET['form_id'] ) && $_GET['form_id'] ? (int) sanitize_text_field( $_GET['form_id'] ) : false;
		}

		if ( ! $entry_id || ! $form_id ) {
			return false;
		}

		$this->entry_notes->set_entry_id( $entry_id, $form_id );

		// prevent duplicate successful resubmissions.
		$success = $this->is_successful_submission( $entry_id );
		if ( $success ) {
			$this->entry_notes->done();
			if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
				return true;
			}

			wp_send_json(
				array(
					'success' => $success,
				)
			);
			exit();
		}

		// gather the vars we need for resubmission.
		$feeds = GFAPI::get_feeds( null, $form_id, $this->_slug );

		if ( ! $feeds ) {
			return false;
		}

		$manual = isset( $_GET['entry_id'] ) ? 'manual' : false;

		if ( ! $manual ) {

			// make sure we haven't reached the retry limit.
			$retry_limit    = $this->get_plugin_setting( 'retry_limit' );
			$retry_attempts = $this->get_retry_attempts( $entry_id );

			if ( $retry_limit && $retry_attempts >= $retry_limit ) {
				$this->cease_retries( $entry_id, $form_id );
				return false;
			}
		}

		$this->update_retry_attempts( $entry_id, $form_id );

		$feed  = $feeds[0];
		$entry = GFAPI::get_entry( $entry_id );
		$form  = GFAPI::get_form( $form_id );

		$this->entry_notes->add_debug( __( 'Resubmitting Entry', 'gfeloqua' ) );
		// re-attempt to submit the data.
		$res = $this->process_feed( $feed, $entry, $form, $manual );

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			$this->entry_notes->done();
			return $res;
		}

		// grab a new copy of the notes.
		$notes         = $this->entry_notes->get( $entry_id );
		$notes         = array_reverse( $notes );
		$retries       = $this->get_retry_attempts( $entry_id );
		$retry_limit   = $this->get_plugin_setting( 'retry_limit' );
		$limit_reached = $retries >= $retry_limit;

		ob_start();
		$notes_display = GFELOQUA_PATH . 'views/notes-display.php';
		include( $notes_display );

		$response = array(
			'notes'         => ob_get_clean(),
			'retries'       => $retries,
			'success'       => $this->is_successful_submission( $entry_id ),
			'limit_reached' => $limit_reached,
		);

		$this->entry_notes->done();

		wp_send_json( $response );
		exit();
	}

	/**
	 * Get number of retries for entry
	 *
	 * @param int $entry_id  GF Entry ID.
	 *
	 * @return int  Number of retries.
	 */
	public function get_retry_attempts( $entry_id ) {
		return gform_get_meta( $entry_id, GFELOQUA_OPT_PREFIX . 'retries' );
	}

	/**
	 * Update entry retry attempts
	 *
	 * @param int $entry_id  GF Entry ID.
	 * @param int $form_id   GF Form ID.
	 */
	public function update_retry_attempts( $entry_id, $form_id = null ) {
		$attempts = $this->get_retry_attempts( $entry_id );
		if ( ! $attempts ) {
			$attempts = 0;
		}
		$attempts++;
		gform_update_meta( $entry_id, GFELOQUA_OPT_PREFIX . 'retries', $attempts, $form_id );
	}

	/**
	 * Add the "Sent to Eloqua" entry meta property.
	 *
	 * @param array $entry_meta  An array of entry meta already registered with the gform_entry_meta filter.
	 * @param int   $form_id     The id of the current form.
	 *
	 * @return array  Meta content in array
	 */
	public function get_entry_meta( $entry_meta, $form_id ) {
		$feeds = GFAPI::get_feeds( null, $form_id, $this->_slug );

		if ( ! $feeds ) {
			return $entry_meta;
		}

		$entry_meta[ GFELOQUA_OPT_PREFIX . 'success' ] = array(
			'label'             => __( 'Sent to Eloqua?', 'gfeloqua' ),
			'is_numeric'        => false,
			'is_default_column' => false,
		);

		return $entry_meta;
	}

	/**
	 * Setup some new cron schedules
	 *
	 * @param array $schedules  Existing cron schedules.
	 */
	public function add_extra_cron_schedule( $schedules ) {
		if ( ! isset( $schedules['5min'] ) ) {
			$schedules['5min'] = array(
				'interval' => 5 * 60,
				'display'  => __( 'Once every 5 minutes', 'gfeloqua' ),
			);
		}
		if ( ! isset( $schedules['30min'] ) ) {
			$schedules['30min'] = array(
				'interval' => 30 * 60,
				'display'  => __( 'Once every 30 minutes', 'gfeloqua' ),
			);
		}
		if ( ! isset( $schedules['hourly'] ) ) {
			$schedules['hourly'] = array(
				'interval' => HOUR_IN_SECONDS,
				'display'  => __( 'Once every hour', 'gfeloqua' ),
			);
		}
		if ( ! isset( $schedules['daily'] ) ) {
			$schedules['daily'] = array(
				'interval' => DAY_IN_SECONDS,
				'display'  => __( 'Once daily', 'gfeloqua' ),
			);
		}

		return $schedules;
	}

	/**
	 * Retry failed entry submissions to Eloqua
	 */
	public function retry_failed_submissions() {
		$feeds = GFAPI::get_feeds( null, null, $this->_slug );

		$forms = array();
		foreach ( $feeds as $feed ) {
			if ( ! isset( $feed['form_id'] ) ) {
				continue;
			}
			if ( ! in_array( $feed['form_id'], $forms ) ) {
				$forms[] = $feed['form_id'];
			}
		}

		$entries = $this->get_failed_entries( $forms );

		foreach ( $entries as $entry ) {
			$this->resubmit_entry( $entry['id'], $entry['form_id'] );
		}

	}

	/**
	 * Grab failed GF Entries
	 *
	 * @param array $forms  Collected forms.
	 *
	 * @return array  Entries
	 */
	public function get_failed_entries( $forms = array() ) {
		if ( ! $forms ) {
			return array();
		}

		$filters = array(
			'field_filters' => array(
				'mode' => 'any',
				array(
					'key'   => GFELOQUA_OPT_PREFIX . 'success',
					'value' => 0,
				),
				array(
					'key'   => GFELOQUA_OPT_PREFIX . 'success',
					'value' => $this->text_for_failed,
				),
			),
		);

		$params = array(
			'page_size' => 50,
		);

		return GFAPI::get_entries( $forms, $filters, array(), $params );
	}

	/**
	 * Reset entry for resubmission.
	 *
	 * @param int $entry_id  GF Entry ID.
	 * @param int $form_id   GF Form ID.
	 */
	public function reset_entry( $entry_id = null, $form_id = null ) {
		if ( ! $entry_id ) {
			$entry_id = isset( $_GET['entry_id'] ) && $_GET['entry_id'] ? absint( $_GET['entry_id'] ) : false;
		}

		if ( ! $form_id ) {
			$form_id = isset( $_GET['form_id'] ) && $_GET['form_id'] ? absint( $_GET['form_id'] ) : false;
		}

		if ( ! $entry_id || ! $form_id ) {
			return false;
		}

		gform_update_meta( $entry_id, GFELOQUA_OPT_PREFIX . 'success', $this->text_for_failed, $form_id );

		$response = array(
			'success' => true,
		);

		wp_send_json( $response );
		exit();
	}

	/**
	 * Override this method from GFFeedAddOn for legacy Gravity Forms support.
	 *
	 * @param array $form  GF Form Array.
	 *
	 * @return object  GFAddOnFeedsTable
	 */
	public function get_feed_table( $form ) {
		$columns               = $this->feed_list_columns();
		$column_value_callback = array( $this, 'get_column_value' );
		$feeds                 = $this->get_feeds( rgar( $form, 'id' ) );
		$bulk_actions          = $this->get_bulk_actions();
		$action_links          = $this->get_action_links();
		$no_item_callback      = array( $this, 'feed_list_no_item_message' );
		$message_callback      = array( $this, 'feed_list_message' );

		return new GFAddOnFeedsTableLegacy( $feeds, $this->_slug, $columns, $bulk_actions, $action_links, $column_value_callback, $no_item_callback, $message_callback, $this );
	}

} // end GFEloqua.
