<?php
/**
 * General admin settings page
 *
 * This is the template with the HTML code for the General Settings admin page
 *
 * @var AEPC_Admin_View $page
 * @var string $title
 * @var string $message
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$title = isset( $title ) ? $title : __( 'Clone action', 'pixel-caffeine' );
$message = isset( $message ) ? $message : '';

?>

<!-- Clone modal -->
<div id="modal-ca-clone" class="modal fade modal-centered modal-confirm modal-clone js-form-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<!-- Filled by script template below -->

		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
$page->register_script_template( 'modal-ca-clone', '
	<form method="post" data-toggle="ajax">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h4 class="modal-title">'. esc_html( $title ) . '</h4>
		</div>
		<div class="modal-body">
			' . ( ! empty( $message ) ? "<p>{$message}</p>" : '' ) . '

			<div class="form-group">
				<label for="ca_name" class="control-label">' . __( 'Name', 'pixel-caffeine' ) . '</label>
				<div class="control-wrap">
					<input type="text" class="form-control" name="ca_name" id="ca_name" placeholder="' . __( 'Name of your new cloned field', 'pixel-caffeine' ) . '">
				</div>
			</div><!-- /.form-froup -->
		</div><!-- /.modal-body -->
		<div class="modal-footer">
			' . wp_nonce_field( 'duplicate_custom_audience', "_wpnonce", true, false ) . '
			<input type="hidden" value="aepc_duplicate_custom_audience" name="action" />
			<input type="hidden" value="{{{ data.id }}}" name="ca_id" />
			<button type="button" class="btn btn-default" data-dismiss="modal">' . __( 'Cancel', 'pixel-caffeine' ) . '</button>
			<button type="submit" class="btn btn-raised btn-primary btn-clone">' . __( 'Clone', 'pixel-caffeine' ) . '</button>
		</div>
	</form>
');
?>
