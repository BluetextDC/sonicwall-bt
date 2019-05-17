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

?>

<!-- Pixel Disconnect modal -->
<div id="modal-confirm-disconnect-pixel" class="modal fade modal-centered modal-confirm modal-confirm-disconnect-pixel" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title"><?php _e( 'Disconnect your Pixel ID', 'pixel-caffeine' ) ?></h4>
			</div>
			<div class="modal-body">
				<p><?php _e( 'Are you sure you want to disconnect your Pixel ID?', 'pixel-caffeine' ) ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e( 'No', 'pixel-caffeine' ) ?></button>
				<button type="button" class="btn btn-raised btn-danger btn-ok"><?php _e( 'Yes', 'pixel-caffeine' ) ?></button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
