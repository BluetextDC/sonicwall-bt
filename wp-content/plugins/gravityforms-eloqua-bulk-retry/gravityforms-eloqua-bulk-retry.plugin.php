<?php
/**
 * Plugin Name: Gravity Forms Eloqua Bulk Retry
 * Plugin URI: https://briandichiara.com/product/gravityforms-eloqua-bulk-retry/
 * Description: Extension for Gravity Forms Eloqua - Enables bulk retry failed submissions
 * Version: 1.0.1
 * Author: Brian DiChiara
 * Author URI: http://www.briandichiara.com
 */

define( 'GFELQ_BULKRETRY_VERSION', '1.0.1' );
define( 'GFELQ_BULKRETRY_PATH', plugin_dir_path( __FILE__ ) );
define( 'GFELQ_BULKRETRY_DIR', plugin_dir_url( __FILE__ ) );
define( 'GFELQ_BULKRETRY_BULKACTION', 'gfelq-bulk-retry' );

if ( ! function_exists( 'gfeloqua' ) ) {
	// Gravity Forms Eloqua is not active. Do something.
}

/**
 * Returns the array for this extension
 *
 * @return array  GFEloqua formatted array to identify extension.
 */
function gfelq_bulk_retry_extension() {
	$bulk_slug = 'gravityforms-eloqua-bulk-retry';
	$bulk_retry = array(
		'plugin' => esc_html( $bulk_slug . '/' . basename( __FILE__ ) ),
		'slug' => esc_html( $bulk_slug ),
		'sku' => esc_html__( 'GFELQEX-BULKR' ),
	);
	return $bulk_retry;
}

/**
 * Register Extension with Gravity Forms Eloqua
 */
function gfelq_bulk_retry_init() {
	gfeloqua_extensions()->init_extension( gfelq_bulk_retry_extension() );
}

add_action( 'init', 'gfelq_bulk_retry_init' );

/**
 * Adds bulk retry option to bulk action list for gravity forms entries
 *
 * @param array $actions  Bulk actions array.
 *
 * @return array  Modified actions array.
 */
function gfelq_bulkretry_bulk_action( $actions ) {
	if ( ! function_exists( 'gfeloqua' ) ) {
		return $actions;
	}

	if ( ! gfelq_bulkretry_check_license() ) {
		return $actions;
	}

	if ( ! isset( $actions[ GFELQ_BULKRETRY_BULKACTION ] ) ) {
		// If there is no print action, just insert the action.
		if ( ! isset( $actions['print'] ) ) {
			$actions[ GFELQ_BULKRETRY_BULKACTION ] = __( 'Retry Submission to Eloqua', 'gfeloqua' );
			return $actions;
		}

		$new_actions = array();
		foreach ( $actions as $key => $action ) {
			// Insert action just before print action.
			if ( 'print' === $key ) {
				$new_actions[ GFELQ_BULKRETRY_BULKACTION ] = __( 'Retry Submission to Eloqua', 'gfeloqua' );
			}
			$new_actions[ $key ] = $action;
		}
		return $new_actions;
	}
	return $actions;
}

add_filter( 'gform_entry_list_bulk_actions', 'gfelq_bulkretry_bulk_action' );

/**
 * Process bulk retry to Eloqua
 *
 * @return void
 */
function gfelq_bulkretry_process_bulk_retry() {
	if ( ! function_exists( 'gfeloqua' ) ) {
		return;
	}

	if ( ! gfelq_bulkretry_check_license() ) {
		return;
	}

	$action = GFELQ_BULKRETRY_BULKACTION;

	if ( ! isset( $_POST['action'] ) || $action !== $_POST['action'] ) {
		return;
	}

	// TODO: verify nonce
	/*if ( ! wp_verify_nonce( 'gforms_entry_list', $action ) ) {
		return;
	}*/

	$entries = rgpost( 'entry' );

	if ( ! $entries ) {
		add_action( 'admin_notices', 'gfelq_bulkretry_admin_notice_failure_no_entries' );
		return;
	}

	if ( ! is_array( $entries ) ) {
		$entries = array( $entries );
	}

	if ( ! count( $entries ) ) {
		add_action( 'admin_notices', 'gfelq_bulkretry_admin_notice_failure_no_entries' );
		return;
	}

	$form_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : false;

	if ( ! $form_id ) {
		add_action( 'admin_notices', 'gfelq_bulkretry_admin_notice_failure_form_id' );
		return;
	}

	$entry_ids = array();
	foreach ( $entries as $entry ) {
		$entry_ids[] = absint( $entry );
	}

	// Get instance of GFEloqua.
	$gfeloqua = gfeloqua();

	$successful_retries = 0;
	$total_entries = count( $entry_ids );

	foreach ( $entry_ids as $entry_id ) {
		if ( $gfeloqua->resubmit_entry( $entry_id, $form_id ) ) {
			$successful_retries++;
		}
	}

	// check if closures are supported.
	if ( phpversion() < '5.3.0' ) {
		add_action( 'admin_notices', 'gfelq_bulkretry_generic_success_notification' );
		return;
	}

	$class = $successful_retries >= 1 ? 'notice-success' : 'notice-warning';

	// display a successful admin notice.
	add_action( 'admin_notices', function() use ( $successful_retries, $total_entries, $class ) {
		$attempts = $successful_retries . '/' . $total_entries;
		$submissions_string = _n( 'submission', 'submissions', $successful_retries, 'gfeloqua' );
		?>
			<div class="notice <?php echo esc_attr( $class ); ?> is-dismissible">
				<p><?php echo $attempts . ' ' . $submissions_string; ?>
					<?php _e( 'have been resubmit to Eloqua successfully.', 'gfeloqua' ); ?></p>
			</div>
		<?php
	});
}

add_action( 'admin_init', 'gfelq_bulkretry_process_bulk_retry' );

/**
 * Generic success message when closures are not supported.
 */
function gfelq_bulkretry_generic_success_notification() {
	if ( ! gfelq_bulkretry_check_license() ) {
		return;
	}
	?>
		<div class="notice notice-success is-dismissible">
			<p><?php _e( 'Entry resubmission to Eloqua complete.', 'gfeloqua' ); ?></p>
		</div>
	<?php
}

/**
 * Error to display when no entries found to resubmit.
 */
function gfelq_bulkretry_admin_notice_failure_no_entries() {
	if ( ! gfelq_bulkretry_check_license() ) {
		return;
	}
	?>
		<div class="notice notice-error is-dismissible">
			<p><?php _e( 'Resubmission failed. Please select one or more entries.', 'gfeloqua' ); ?></p>
		</div>
	<?php
}

/**
 * Error to display when Form ID not in URL.
 */
function gfelq_bulkretry_admin_notice_failure_form_id() {
	if ( ! gfelq_bulkretry_check_license() ) {
		return;
	}
	?>
		<div class="notice notice-error is-dismissible">
			<p><?php _e( 'Resubmission failed. Form ID missing.', 'gfeloqua' ); ?></p>
		</div>
	<?php
}

/**
 * Check to see if we are using a valid license
 *
 * @return bool  If valid license.
 */
function gfelq_bulkretry_check_license() {
	if ( ! function_exists( 'gfeloqua_extensions' ) ) {
		return false;
	}

	if ( ! gfeloqua_extensions()->has_valid_license( gfelq_bulk_retry_extension() ) ) {
		return false;
	}

	return true;
}

