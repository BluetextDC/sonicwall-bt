<?php
/**
 * General admin settings page
 *
 * This is the template with the HTML code for the General Settings admin page
 *
 * @var AEPC_Admin_View $page
 * @var AEPC_Facebook_Adapter $fb
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<!-- Fb Connect Modal -->
<div id="modal-fb-connect-options" class="modal fade modal-centered modal-fb-connect-options" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<!-- Filled by script template below -->

		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
$page->register_script_template( 'modal-facebook-options', '
	<form method="post" data-toggle="ajax">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h4 class="modal-title">' . __( 'Choose your account', 'pixel-caffeine' ) . '</h4>
		</div>
		<div class="modal-body">
			<div class="form-group">
				<label for="aepc_account_id" class="control-label">' . __( 'Ad account', 'pixel-caffeine' ) . '</label>
				<div class="control-wrap">
					<select id="aepc_account_id" class="form-control" name="aepc_account_id" data-placeholder="' . __( 'Select an account', 'pixel-caffeine' ) . '">
						<option></option>
					</select>
					<div class="field-helper"></div>
				</div>
			</div>
			<div class="form-group">
				<label for="aepc_pixel_id" class="control-label">' . __( 'Pixel ID', 'pixel-caffeine' ) . '</label>
				<div class="control-wrap">
					<select id="aepc_pixel_id" class="form-control" name="aepc_pixel_id" data-placeholder="' . __( 'Select a Pixel ID', 'pixel-caffeine' ) . '" ' . disabled( empty( $pixel ), true, false ) . '>
						<option></option>
					</select>
					<div class="field-helper"></div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			' . wp_nonce_field( 'save_facebook_options', "_wpnonce", false , false ) . '
			<input type="hidden" name="action" value="aepc_save_facebook_options" />
			<button type="button" class="btn btn-default" data-dismiss="modal">' . __( 'Cancel', 'pixel-caffeine' ) . '</button>
			<button type="submit" class="btn btn-raised btn-success btn-save">' . __( 'Yes', 'pixel-caffeine' ) . '</button>
		</div>
	</form>
');
?>
