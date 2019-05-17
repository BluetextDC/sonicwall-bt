<?php
/**
 * Timeline Express Pro Support functionality
 * @since 1.2
 */
/* Register our new license options */
register_setting(
	'timeline_express_license',
	'timeline_express_license_key',
	'timeline_express_sanitize_license'
);

/**
 * Sanitize and save our license key
 * @package  TimelineExpressBase
 * @param array $options Options array to update.
 */
function timeline_express_sanitize_license( $new_license ) {

	$old = get_option( 'timeline_express_license_key', false );

	if ( $old && $old !== $new_license ) {

		delete_option( 'timeline_express_license_status' );

	}

	return $new_license;

}

/**
* Cross check if a new version exists
*
* @since    1.0
*/
function timeline_express_addon_plugin_updater() {

	// Custom Uploader Class
	if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {

		// load our custom updater
		include( TIMELINE_EXPRESS_PATH . 'lib/support-files/EDD_SL_Plugin_Updater.php' );

	}

	// retrieve our license key from the DB
	$license_key = trim( get_option( 'timeline_express_license_key', '' ) );

	if ( empty( $license_key ) ) {

		return;

	}

	// @codingStandardsIgnoreStart
	// setup the updater
	$timeline_express_crosscheck = new EDD_SL_Plugin_Updater(
		TIMELINE_EXPRESS_SITE_URL, TIMELINE_EXPRESS_PATH . 'timeline-express-pro.php', array(
			'version'   => TIMELINE_EXPRESS_VERSION_CURRENT,
			'license'   => $license_key,
			'item_name' => urlencode( 'Timeline Express' ),
			'author'    => 'Code Parrots',
		)
	);
	// @codingStandardsIgnoreEnd

}
add_action( 'admin_init', 'timeline_express_addon_plugin_updater', 0 );

/***
* Licensing Functions
* @since 1.0
*/

/*
* timeline_express_activate_license()
* Remote Support License Activation
* since @v1.1.4
*/
function timeline_express_activate_license() {

	// listen for our activate button to be clicked
	if ( isset( $_POST['timeline_express_license_activate'] ) ) {

		// run a quick security check
		if ( ! check_admin_referer( 'timeline_express_nonce', 'timeline_express_nonce' ) ) {

			return;

		}

		// retrieve the license from the database
		$license = $_POST['timeline_express_license_key'];

		if ( empty( $license ) ) {

			return;

		}

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( 'Timeline Express' ),
			'url'        => home_url(),
		);

		$args = array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		);

		// Call the custom API.
		$response = wp_remote_post( TIMELINE_EXPRESS_SITE_URL, $args );

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license returns "valid" or "invalid"
		update_option( 'timeline_express_license_status', $license_data->license );
		update_option( 'timeline_express_license_data', $license_data );

	} // End if().

}
add_action( 'admin_init', 'timeline_express_activate_license' );

/*
* timeline_express_deactivate_license()
* Remote Support License De-activation
* since @v1.1.4
*/
function timeline_express_deactivate_license() {

	// listen for our activate button to be clicked
	if ( isset( $_POST['timeline_express_license_deactivate'] ) ) {

		// run a quick security check
		if ( ! check_admin_referer( 'timeline_express_nonce', 'timeline_express_nonce' ) ) {

			return;

		}

		// retrieve the license from the database
		$license = trim( get_option( 'timeline_express_license_key' ) );

		// data to send in our API request
		$args = array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => array(
				'edd_action' => 'deactivate_license',
				'license'    => $license,
				'item_name'  => urlencode( 'Timeline Express' ),
				'url'        => home_url(),
			),
		);

		// Call the custom API.
		$response = wp_remote_post( TIMELINE_EXPRESS_SITE_URL, $args );

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {

			return false;

		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license returns either "deactivated" or "failed"
		if ( 'deactivated' === $license_data->license ) {

			delete_option( 'timeline_express_license_status' );

		}
	} // End if().
} // end remote deactivation
add_action( 'admin_init', 'timeline_express_deactivate_license' );
