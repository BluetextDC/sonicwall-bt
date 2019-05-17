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

$title = isset( $title ) ? $title : __( 'Confirm delete action', 'pixel-caffeine' );
$message = isset( $message ) ? $message : __( 'Are you sure you really want delete this item?', 'pixel-caffeine' );

?>

<!-- Delete modal -->
<div id="modal-confirm-delete" class="modal fade modal-centered modal-confirm modal-delete" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title"><?php echo esc_html( $title ) ?></h4>
			</div>
			<div class="modal-body">
				<?php if ( ! empty( $message ) ) : ?><p><?php echo $message ?></p><?php endif; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e( 'No', 'pixel-caffeine' ) ?></button>
				<button type="button" class="btn btn-raised btn-danger btn-delete btn-ok"><?php _e( 'Yes', 'pixel-caffeine' ) ?></button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
