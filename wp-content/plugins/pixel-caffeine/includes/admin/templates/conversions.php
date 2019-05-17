<?php
/**
 * General admin settings page
 *
 * This is the template with the HTML code for the General Settings admin page
 *
 * @var AEPC_Admin_View $page
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="pixel-caffeine-wrapper">
<div class="wrap wrap-conversions">

	<h1 class="page-title"><?php $page->the_title() ?></h1>

	<?php $page->get_template_part( 'nav-tabs' ) ?>

	<section class="plugin-sec">
		<div class="plugin-content">

			<div class="alert-wrap">
				<?php $page->print_notices() ?>
			</div>

			<form method="post" id="mainform" data-toggle="ajax" action="<?php echo remove_query_arg( 'paged' ) ?>">

				<?php $page->get_template_part( 'tables/ce-tracking' ) ?>

				<div class="panel panel-ce-new form-horizontal">
					<div class="panel-heading"><h2 class="tit"><?php _e( 'Add new Tracking', 'pixel-caffeine' ) ?></h2></div>
					<div class="panel-body">
						<p><?php printf( __( 'We suggest to follow the instructions on %sthis link%s to verify if the pixel tracking event you will add through this form will work properly.', 'pixel-caffeine' ), '<a href="https://developers.facebook.com/docs/facebook-pixel/using-the-pixel#verify">', '</a>' ) ?></p>
						<?php $page->get_form_fields( 'conversion', 'action=add' ) ?>
					</div>
					<div class="panel-footer">
						<button type="submit" href="#_" class="btn btn-raised btn-success btn-save btn-plugin"><?php _e( 'Create Tracking', 'pixel-caffeine' ) ?></button>
						<input type="hidden" name="tab" value="<?php echo $_GET['tab'] ?>" />
						<input type="hidden" name="action" value="aepc_save_tracking_conversion" />
						<?php wp_nonce_field( 'save_tracking_conversion' ) ?>
					</div>
				</div>
			</form>
		</div><!-- ./plugin-content -->

		<?php $page->get_template_part( 'sidebar' ) ?>
	</section>

	<?php $page->get_template_part( 'modals/conversion-edit', array( 'title' => __( 'Edit conversion', 'pixel-caffeine' ) ) ) ?>

	<?php $page->get_template_part( 'modals/confirm-delete', array( 'title' => __( 'Delete', 'pixel-caffeine' ) ) ) ?>

</div>
</div><!--/.pixel-caffeine-wrapper -->
