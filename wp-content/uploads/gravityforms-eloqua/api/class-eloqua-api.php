<?php
/**
 * Eloqua API Interface
 *
 * @package gfeloqua
 */

if ( class_exists( 'Eloqua_API' ) ) {
	return;
}

if ( ! class_exists( 'WP_Http' ) ) {
	include_once( ABSPATH . WPINC . '/class-http.php' );
}

/**
 * Main Eloqua API class
 */
class Eloqua_API {

	/**
	 * Is_required constant used to validate form fields
	 */
	const ELOQUA_IS_REQUIRED = 'IsRequiredCondition';

	/**
	 * URL storage for calling Eloqua API
	 *
	 * @var array
	 */
	public $urls;

	/**
	 * Connection Storage
	 *
	 * @var string
	 */
	public $connection;

	/**
	 * Timeout (in seconds) for requests.
	 *
	 * @var int
	 */
	public $timeout;

	/**
	 * Storage for if request timed out.
	 *
	 * @var bool
	 */
	public $is_timeout = false;

	/**
	 * Connection Arguments storage
	 *
	 * @var array
	 */
	public $connection_args = array();

	/**
	 * Authstring storage
	 *
	 * @var string
	 */
	public $authstring;

	/**
	 * Whether to use OAuth or Basic Auth
	 *
	 * @var bool
	 */
	public $use_oauth = false;

	/**
	 * URL for Basic Authentication
	 *
	 * @var string
	 */
	public $basic_auth_url = 'https://login.eloqua.com/id';

	/**
	 * Rest API Version
	 *
	 * @var string
	 */
	public $rest_api_version = '2.0';

	/**
	 * OAuth Authorize URL
	 *
	 * @var string
	 */
	public $_oauth_authorize_url = 'https://login.eloqua.com/auth/oauth2/authorize';

	/**
	 * OAuth Token URL
	 *
	 * @var string
	 */
	public $_oauth_token_url = 'https://login.eloqua.com/auth/oauth2/token';

	/**
	 * Client ID for Gravity Forms Eloqua Application
	 *
	 * @var string
	 */
	public $_oauth_client_id = '11c8590a-f513-496a-aa9c-4a224dd92861';

	/**
	 * Client secret for Gravity Forms Eloqua Application
	 *
	 * @var string
	 */
	public $_oauth_client_secret = '15325mypy7U2JFaTg35mF8ekItAyOdiOwfsZBx2dbHEECNecqSy9KK5ammgNlEhMwhhEav1te0hP8hdmQ1KaZjY1z9yQLlaGkQgP';

	/**
	 * OAuth Redirect URL
	 *
	 * @var string
	 */
	public $_oauth_redirect_uri = 'https://api.briandichiara.com/gravityformseloqua/';

	/**
	 * OAuth scope
	 *
	 * @var string
	 */
	public $_oauth_scope = 'full';

	/**
	 * Array for error storage
	 *
	 * @var array
	 */
	public $errors = array();

	/**
	 * Array for debug storage
	 *
	 * @var array
	 */
	public $debug = array();

	/**
	 * Last Response from API
	 *
	 * @var object
	 */
	public $last_response = false;

	/**
	 * Stores if connection invalid.
	 *
	 * @var bool
	 */
	public $disconnected = false;

	/**
	 * Constructor
	 *
	 * @param string $authstring  Authstring from OAuth.
	 * @param bool   $use_oauth   Use OAuth (or basic).
	 */
	function __construct( $authstring = '', $use_oauth = false, $timeout = 5 ) {
		if ( $authstring ) {
			$this->authstring = $authstring;
		}

		$this->use_oauth = $use_oauth;
		$this->set_timeout( $timeout );
	}

	/**
	 * Returns array of errors
	 *
	 * @return array  Errors during API communication
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * Returns the last error
	 *
	 * @param bool $for_display  If error will be displayed to user.
	 *
	 * @return array or string  Error(s) during API communication.
	 */
	public function get_last_error( $for_display = false ) {
		if ( count( $this->errors ) ) {
			$last_error = $this->errors[0];
			if ( $for_display && false !== strpos( '=>', $last_error ) ) {
				list( $debug_msg, $return_msg ) = explode( '=>', $last_error );
				return trim( $debug_msg );
			} else {
				return $last_error;
			}
		}
		return false;
	}

	/**
	 * Set the Connection Timeout
	 *
	 * @param int $timeout  Timeout in seconds.
	 */
	public function set_timeout( $timeout ) {
		$this->timeout = $timeout;
	}

	/**
	 * Check if request timed out.
	 *
	 * @return bool  If is timeout.
	 */
	public function is_timeout() {
		return $this->is_timeout;
	}

	/**
	 * Get URL for OAuth
	 *
	 * @param string $source  OAuth source parameter.
	 *
	 * @return string  OAuth URL.
	 */
	function get_oauth_url( $source = false ) {
		if ( is_multisite() ) {
			$return_url = get_site_url( get_current_blog_id() );
		} else {
			$return_url = site_url();
		}
		$return_url = str_replace( array( 'http://', 'https://' ), '', $return_url );

		$url = $this->_oauth_authorize_url .
			'?response_type=code&client_id=' . $this->_oauth_client_id .
			'&scope=' . urlencode( $this->_oauth_scope ) .
			'&redirect_uri=' . urlencode( $this->_oauth_redirect_uri ) .
			'&state=' . $return_url;

		if ( $source ) {
			$url .= '&source=' . urlencode( $source );
		}

		return $url;
	}

	/**
	 * Returns Basic Auth URL
	 *
	 * @return string  Basic Auth URL
	 */
	function get_auth_url() {
		return $this->basic_auth_url;
	}

	/**
	 * Init connection to Eloqua, needs ::connect() first.
	 *
	 * @param bool $connection  Connection object.
	 *
	 * @return bool  If initialized
	 */
	function init( $connection = false ) {
		if ( ! $this->connection ) {
			return false;
		}

		if ( ! $connection ) {
			$connection = get_transient( 'gfeloqua_connection' );
		}

		if ( ! $connection ) {
			$this->errors[] = __METHOD__ . '() => ' . __( 'Connection to Eloqua does not exist.', 'gfeloqua' );
			return false;
		}

		if ( ! isset( $connection->urls ) ) {
			$this->errors[] = __METHOD__ . '() => ' . __( 'Connection URLs not setup', 'gfeloqua' );
			return false;
		}

		$this->_setup_urls( $connection->urls );

		if ( ! get_transient( 'gfeloqua_connection' ) ) {
			set_transient( 'gfeloqua_connection', $connection, MINUTE_IN_SECONDS * 60 );
		}

		return true;
	}

	/**
	 * Connect to Eloqua using authstring
	 *
	 * @return bool  If Connected.
	 */
	public function connect() {
		if ( $this->init() ) {
			return true;
		}

		$type = $this->use_oauth ? 'Bearer' : 'Basic';

		$this->connection_args = array(
			'headers' => array(
				'Authorization' => $type . ' ' . $this->authstring,
			),
			'timeout' => $this->timeout,
		);

		$this->connection = new WP_Http();
		$response = $this->connection->request( $this->get_auth_url(), $this->connection_args );
		$this->last_response = $response;

		if ( is_wp_error( $response ) ) {
			if ( false !== stripos( 'curl error 28', $response->get_error_message() ) ) {
				$this->is_timeout = true;
			}
			$this->errors[] = __METHOD__ . '() => WP_Http Error: ' . $response->get_error_message() . ' (' . $response->get_error_code() . ')';
			return false;
		}

		if ( $this->is_json( $response['body'] ) ) {
			$connection = json_decode( $response['body'] );
		} else {
			$connection = $response['body'];
		}

		if ( is_object( $connection ) ) {
			return $this->init( $connection );
		}

		// Looks like the credentials could be bad.
		if ( is_string( $connection ) && strpos( strtolower( $connection ), 'not authenticated' ) !== false ) {
			$this->disconnected = true;
			$this->errors[] = __METHOD__ . '() => ' . __( 'Not Authenticated: Please check your Eloqua credentials. If you have confirmed valid Eloqua credentials, it\'s possible your OAUTH token has expired and needs to be reset.' , 'gfeloqua' );
			return false;
		}

		// Something went wrong. Probably an error.
		$this->errors[] = __METHOD__ . '() => ' . print_r( $connection, true );

		return false;

	}

	/**
	 * Tells if Eloqua got disconnected.
	 *
	 * @return bool  If disconnected.
	 */
	public function is_disconnected() {
		return $this->disconnected;
	}

	/**
	 * Setup REST API Urls
	 *
	 * @param object $urls  Response Object with URLs.
	 */
	private function _setup_urls( $urls ) {
		$rest_urls = array();
		foreach ( $urls->apis->rest as $key => $rest_url ) {
			$rest_urls[ $key ] = str_replace( '{version}', $this->rest_api_version, $rest_url );
		}

		$this->urls = $rest_urls;
	}

	/**
	 * Make a call to the API
	 *
	 * @param string $endpoint  Endpoint to call.
	 * @param array  $data      Data object to send with API call.
	 * @param string $method    Get or Post.
	 *
	 * @return object  API response.
	 */
	public function _call( $endpoint, $data = null, $method = 'GET' ) {
		if ( ! $this->connect() ) {
			return false;
		}
		$this->debug[] = __METHOD__ . '() => API CALL: ' . $endpoint;

		$url = $this->urls['standard'] . trim( $endpoint, '/' );
		$args = $this->connection_args;
		$args['method'] = $method;

		if ( $data ) {
			$args['body'] = json_encode( $data );
			$args['headers']['Content-Type'] = 'application/json';
		}

		$response = $this->connection->request( $url, $args );
		$this->last_response = $response;

		if ( is_wp_error( $response ) ) {
			$this->errors[] = __METHOD__ . '() => WP_Http Error: ' . $response->get_error_message() . ' (' . $response->get_error_code() . ')';
			return false;
		}

		$data = $this->validate_response( $response );

		if ( $data ) {
			if ( ! is_object( $data ) ) {
				if ( is_string( $response ) ) {
					$this->errors[] = __METHOD__ . '() => ' . __( 'API Response Error', 'gfeloqua' ) . ': ' . print_r( $response, true );
				} else {
					$this->errors[] = __METHOD__ . '() => ' . __( 'Response Validation Failed.', 'gfeloqua' );
				}

				return false;
			}
		} else {
			$this->errors[] = __METHOD__ . '() => ' . __( 'Data returned empty.', 'gfeloqua' );
		}

		return $data;
	}

	/**
	 * Make sure the response from the API call was valid
	 *
	 * @api gfeloqua_validate_response  Hook to modify response.
	 *
	 * @param object $response  Object from _call().
	 *
	 * @return object  Validated response body
	 */
	public function validate_response( $response ) {
		$return = false;

		if ( $response && is_array( $response ) ) {
			if ( isset( $response['response'] ) && isset( $response['response']['code'] ) ) {
				// 201 = "Created"
				if ( in_array( $response['response']['code'], array( '200', '201', '202' ) ) && $response['body'] ) {
					if ( $this->is_json( $response['body'] ) ) { // valid response from Eloqua.
						$return = json_decode( $response['body'] );
					} elseif ( is_string( $response['body'] ) ) { // Error message.
						$return = trim( $response['body'], ' \t\n\r\0\x0B"' );
					} else { // something else.
						$return = $response['body'];
					}
				} elseif ( '400' == $response['response']['code'] ) {
					if ( $this->is_json( $response['body'] ) ) { // valid, albeit error, response from Eloqua.
						$error = json_decode( $response['body'] );
						$this->errors[] = __METHOD__ . '() => ' . __( 'Error 400 Bad Request', 'gfeloqua' ) . ': ' . $error->type . ' => '. print_r( $error, true );
					} else {
						$this->errors[] = __METHOD__ . '() => ' . __( 'Response Code 400 Bad Request', 'gfeloqua' ) . ': ' . print_r( $response, true );
					}
				} else {
					$this->errors[] = __METHOD__ . '() => ' . __( 'Unsupported Response Code', 'gfeloqua' ) . ': ' . print_r( $response, true );
				}
			} else {
				$this->errors[] = __METHOD__ . '() => ' . __( 'Invalid response format', 'gfeloqua' ) . ': ' . print_r( $response, true );
			}
		} else {
			$this->errors[] = __METHOD__ . '() => ' . __( 'Bad Response', 'gfeloqua' ) . ': ' . print_r( $response, true );
		}

		return apply_filters( 'gfeloqua_validate_response', $return, $response );
	}

	/**
	 * Validate if is JSON
	 *
	 * @param string $string  String to be tested.
	 *
	 * @return bool  Whether it's valid JSON.
	 */
	public function is_json( $string ) {
		if ( ! is_string( $string ) ) {
			return false;
		}
		if ( '{' !== substr( $string, 0, 1 ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Validate Data object
	 *
	 * @param object $data  Object to be tested.
	 *
	 * @return bool  If data is valid.
	 */
	public function is_valid_data( $data ) {
		if ( ! is_object( $data ) || ! isset( $data->elements ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get prefixed transient data.
	 *
	 * @param string $transient  Transient key/identifier.
	 *
	 * @return mixed  Transient value.
	 */
	private function get_transient( $transient ){
		return get_transient( 'gfeloqua/' . $transient );
	}

	/**
	 * Set prefixed transient data.
	 *
	 * @param string $transient   Transient key/identifier.
	 * @param mixed  $value       Value of transient.
	 * @param int    $expiration  Time in seconds untl transient expires.
	 */
	private function set_transient( $transient, $value, $expiration = null ) {
		if ( $expiration === null ) {
			$expiration = DAY_IN_SECONDS * 15;
		}

		set_transient( 'gfeloqua/' . $transient, $value, $expiration );
	}

	/**
	 * Clear prefixed transient value
	 *
	 * @param string $transient  Transient key/identifier.
	 */
	public function clear_transient( $transient ) {
		delete_transient( 'gfeloqua/' . $transient );
	}

	/**
	 * Call API forms endpoint to get forms.
	 *
	 * @param int $page   For pagination.
	 * @param int $count  Number of results to return.
	 *
	 * @return [type] [description]
	 */
	public function get_forms( $page = null, $count = 1000 ) {
		$call = 'assets/forms';

		$transient = $this->get_transient( $call );
		if ( $transient ) {
			return $transient;
		}

		$actual_call = $call;

		if ( $count || $page ) {
			$qs = '';
			if ( $count ) {
				$qs .= 'count=' . $count;
			}
			if ( $page ) {
				$qs .= $qs ? '&page=' . $page : 'page=' . $page;
			}
			$actual_call .= '?' . $qs;
		}

		$forms = $this->_call( $actual_call );

		if ( $this->is_valid_data( $forms ) ) {
			$all_forms = $forms->elements;

			if ( count( $all_forms ) >= $count ) {
				$page = $page ? $page + 1 : 2;
				$the_rest = $this->get_forms( $page, $count );
				if ( is_array( $the_rest ) ) {
					$all_forms = array_merge( $all_forms, $the_rest );
				}
			}

			usort( $all_forms, array( $this, 'compare_by_folder' ) );

			$this->set_transient( $call, $all_forms );

			return $all_forms;
		}

		return array();
	}

	/**
	 * Retrieve single form
	 *
	 * @param int $form_id  Eloqua Form ID.
	 *
	 * @return object  Eloqua Form object.
	 */
	public function get_form( $form_id ) {
		$call = 'assets/form/' . $form_id;

		$transient = $this->get_transient( $call );
		if ( $transient ) {
			return $transient;
		}

		$form = $this->_call( $call );

		if ( $form ) {
			$this->set_transient( $call, $form );
			return $form;
		}
	}

	/**
	 * Retrieve form fields for specific form
	 *
	 * @param int $form_id  Eloqua Form ID.
	 *
	 * @return object  Eloqua Form Elements.
	 */
	public function get_form_fields( $form_id ) {
		$form = $this->get_form( $form_id );

		if ( $this->is_valid_data( $form ) ) {
			return $form->elements;
		}

		return array();
	}

	/**
	 * Get form folder by Folder ID.
	 *
	 * @param int $folder_id  Eloqua Folder ID.
	 *
	 * @return string  Folder Name
	 */
	public function get_form_folder_name( $folder_id ) {
		$call = 'assets/folder/' . $folder_id;

		$transient = $this->get_transient( $call );
		if ( $transient ) {
			return $transient;
		}

		$folder = $this->_call( $call );

		if ( $folder ) {
			$this->set_transient( $call, $folder );
			return $folder;
		}
	}

	/**
	 * Submit Eloqua Form Data
	 *
	 * @param int $form_id  Eloqua Form ID.
	 * @param object $submission  Eloqua Submission object.
	 *
	 * @return bool  If submission was successful.
	 */
	public function submit_form( $form_id, $submission ) {
		$response = $this->_call( 'data/form/' . $form_id, $submission, 'POST' );

		// on Success, Eloqua returns the submission data.
		if ( $response ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Create an Eloqua Contact
	 *
	 * @param object $contact  Contact Object.
	 *
	 * @return bool  If contact was created successfully.
	 */
	public function create_contact( $contact ) {
		$response = $this->_call( 'data/contact', $contact, 'POST' );

		if ( $response ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if field is required.
	 *
	 * @param object $field  Eloqua Field Object.
	 *
	 * @return bool  If field is required.
	 */
	public function is_field_required( $field ) {
		$validations = $field->validations;

		if ( is_array( $validations ) && count( $validations ) ) {
			foreach ( $validations as $validation ) {
				if ( self::ELOQUA_IS_REQUIRED === $validation->condition->type ) {
					if ( 'true' === $validation->isEnabled ) { // @codingStandardsIgnoreLine: ok.
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Sort Eloqua folders.
	 *
	 * @param object $a  Compare object A.
	 * @param object $b  Compare object B.
	 *
	 * @return bool  Which object goes first.
	 */
	public function compare_by_folder( $a, $b ) {
		$folderA = isset( $a->folderId ) && $a->folderId ? $a->folderId : ''; // @codingStandardsIgnoreLine: ok.
		$folderB = isset( $b->folderId ) && $b->folderId ? $b->folderId : ''; // @codingStandardsIgnoreLine: ok.
		return strcmp( $folderA, $folderB ); // @codingStandardsIgnoreLine: ok.
	}
}
