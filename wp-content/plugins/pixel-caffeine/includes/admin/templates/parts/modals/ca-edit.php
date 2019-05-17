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

$title = isset( $title ) ? $title : __( 'Edit action', 'pixel-caffeine' );
$message = isset( $message ) ? $message : '';

?>

<!-- Edit modal -->
<div id="modal-ca-edit" class="modal fade modal-centered modal-confirm modal-edit js-form-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<!-- Filled by script template below -->

		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
$page->register_script_template( 'modal-ca-edit', '
	<form method="post" data-toggle="ajax">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h4 class="modal-title">' . esc_html( $title ) . '</h4>
		</div>
		<div class="modal-body">
			' . ( ! empty( $message ) ? "<p>{$message}</p>" : '' ) . '

			' . $page->get_form_fields( 'custom-audience', 'action=edit', false ) . '
		</div>
		<div class="modal-footer">
			' . wp_nonce_field( 'edit_custom_audience', "_wpnonce", true, false ) . '
			<input type="hidden" name="ca_id" value="{{{ data.id }}}" />
			<input type="hidden" name="action" value="aepc_edit_custom_audience"/>
			<button type="button" class="btn btn-default" data-dismiss="modal">' . __( 'Cancel', 'pixel-caffeine' ) . '</button>
			<button type="submit" class="btn btn-raised btn-success btn-save">' . __( 'Save', 'pixel-caffeine' ) . '</button>
		</div>
	</form>
');
?>
