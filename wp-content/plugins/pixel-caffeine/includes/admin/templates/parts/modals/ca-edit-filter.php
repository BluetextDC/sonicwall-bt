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

$title = isset( $title ) ? $title : __( 'Edit filter', 'pixel-caffeine' );
$message = isset( $message ) ? $message : '';

?>

<!-- Modal Edit Special Filter -->
<div id="modal-ca-edit-filter" class="modal modal-ca-filter fade js-form-modal">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Filled by script template below -->

		</div>
	</div>
</div>

<?php
$page->register_script_template( 'modal-ca-edit-filter', '
	<form method="post" id="ca-filter-form" data-scope="edit"> 
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h4 class="modal-title">'. esc_html( $title ) . '</h4>
		</div>
		<div class="modal-body">
			' . ( ! empty( $message ) ? "<p>{$message}</p>" : '' ) . '

			<div class="form-horizontal">
				' . $page->get_form_fields( 'ca-filter', 'action=new', false ) . '
			</div><!-- ./form-horizontal -->
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">' . __( 'Cancel', 'pixel-caffeine' ) . '</button>
			<button type="submit" class="btn btn-raised btn-success">' . __( 'Edit filter', 'pixel-caffeine' ) . '</button>
		</div>
	</form>
');
?>
