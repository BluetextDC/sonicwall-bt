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

$fb = AEPC_Admin::$api;
$fb_disabled = ! $fb->is_debug() && ( ! $fb->is_logged_in() || $fb->is_expired() );

?>

<div class="pixel-caffeine-wrapper">
	<div class="wrap wrap-custom-audiences">

		<h1 class="page-title"><?php $page->the_title() ?></h1>

		<?php $page->get_template_part( 'nav-tabs' ) ?>

		<section class="plugin-sec">
			<div class="plugin-content">

				<div class="alert-wrap">
					<?php $page->get_template_part( 'notices/fancy/facebook-connect' ) ?>

					<?php $page->print_notices() ?>
				</div>

				<?php $page->get_template_part( 'tables/ca-list', array( 'disabled' => $fb_disabled ) ) ?>

				<form method="post" id="mainform" data-toggle="ajax" action="<?php echo remove_query_arg( 'paged' ) ?>">
					<div class="panel panel-ca-new form-horizontal<?php echo $fb_disabled ? ' disabled-box' : '' ?>">

						<div class="panel-heading">
							<h2 class="tit"><?php _e( 'New Custom Audience', 'pixel-caffeine' ) ?></h2>
						</div>
						<div class="panel-body">
							<?php $page->get_form_fields( 'custom-audience', 'action=add' ) ?>
						</div>
						<!-- ./panel-body -->
						<div class="panel-footer">
							<?php wp_nonce_field( 'add_custom_audience' ) ?>
							<input type="hidden" name="action" value="aepc_add_custom_audience"/>
							<button class="btn btn-raised btn-success btn-save btn-plugin"><?php _e( 'Create Custom Audience', 'pixel-caffeine' ) ?></button>
						</div>
					</div>
				</form>
				<!-- ./panel-ca-new -->
			</div><!-- ./plugin-content -->

			<?php $page->get_template_part( 'sidebar' ) ?>
		</section><!--/.plugin-sec-->

		<?php $page->get_template_part( 'modals/ca-clone', array( 'title' => __( 'Clone custom audience', 'pixel-caffeine' ) ) ) ?>

		<?php $page->get_template_part( 'modals/ca-edit', array( 'title' => __( 'Edit custom audience', 'pixel-caffeine' ) ) ) ?>

		<?php $page->get_template_part( 'modals/ca-new-filter', array( 'title' => __( 'Add new filter', 'pixel-caffeine' ) ) ) ?>

		<?php $page->get_template_part( 'modals/ca-edit-filter', array( 'title' => __( 'Edit filter', 'pixel-caffeine' ) ) ) ?>

		<?php $page->get_template_part( 'modals/confirm-delete') ?>

	</div><!--/.wrap -->
</div><!--/.pixel-caffeine-wrapper -->
