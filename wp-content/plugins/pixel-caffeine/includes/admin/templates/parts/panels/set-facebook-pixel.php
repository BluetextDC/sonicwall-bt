<?php
/**
 * General admin settings page
 *
 * This is the HTML of the panel for facebook pixel options of general settings
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

<div class="panel panel-settings-set-fb-px">
	<div class="panel-heading">
		<h2 class="tit"><?php _e( 'Facebook Pixel Setup', 'pixel-caffeine' ) ?></h2>
		<div class="form-group form-toggle">
			<label for="<?php $page->field_id( 'aepc_enable_pixel' ) ?>" class="control-label"><?php _e( 'Enable', 'pixel-caffeine' ) ?></label>
			<div class="togglebutton<?php if ( ! PixelCaffeine()->is_pixel_enabled() && 'yes' === $page->get_value( 'aepc_enable_pixel' ) ) echo ' pending' ?>">
				<label>
					<input
						type="checkbox"
						name="<?php $page->field_name( 'aepc_enable_pixel' ) ?>"
						id="<?php $page->field_id( 'aepc_enable_pixel' ) ?>"
						class="js-switch-labeled-tosave"
						data-original-value="<?php echo $page->get_value( 'aepc_enable_pixel' ) ?>"
						<?php checked( $page->get_value( 'aepc_enable_pixel' ), 'yes' ) ?>>
				</label>
			</div>
			<?php if ( PixelCaffeine()->is_pixel_enabled() ) : ?>
				<span class="text-status text-status-on text-success"><?php _e( 'Tracking is ON!', 'pixel-caffeine' ) ?></span>
			<?php elseif ( 'yes' === $page->get_value( 'aepc_enable_pixel' ) ) : ?>
				<span class="text-status text-status-pending"><?php _e( 'Tracking is OFF, you have to set a pixel ID!', 'pixel-caffeine' ) ?></span>
			<?php else : ?>
				<span class="text-status text-status-on text-danger"><?php _e( 'Tracking is OFF!', 'pixel-caffeine' ) ?></span>
			<?php endif; ?>
		</div>
	</div><!-- ./panel-heading -->

	<div class="panel-body">
		<article class="sub-panel sub-panel-px-id form-group<?php $page->field_class( 'aepc_pixel_id', array( ! empty( $fb ) && $fb->is_logged_in() ? 'disabled' : '' ) ) ?>">
			<div class="control-label">
				<h3 class="tit">
					<?php _e( 'Manual Setup', 'pixel-caffeine' ) ?>
					<a href="#_" class="btn btn-fab btn-help btn-fab-mini"data-toggle="tooltip" data-placement="top" title="<?php _e( 'Set the Pixel ID to use in the pages manually.', 'pixel-caffeine' ) ?>"></a>
				</h3>
			</div>

			<?php if ( empty( $fb ) || ! $fb->is_logged_in() ) : ?>
				<div id="fb-connect-alert" class="alert alert-lite alert-warning">
					<strong><?php _e( 'Warning', 'pixel-caffeine' ) ?>: </strong>
					<?php _e( 'Only with Facebook Connect you\'ll have access to all the features', 'pixel-caffeine' ) ?>
				</div>

				<div class="control-wrap">
					<input
						type="text"
						class="form-control"
						name="<?php $page->field_name( 'aepc_pixel_id' ) ?>"
						id="<?php $page->field_id( 'aepc_pixel_id' ) ?>"
						value="<?php echo esc_attr( $page->get_value( 'aepc_pixel_id' ) ) ?>"
						placeholder="<?php esc_attr_e( 'Insert your Pixel ID', 'pixel-caffeine' ) ?>">
					<div class="field-helper">
						<?php $page->print_field_error( 'aepc_pixel_id', '<span class="help-block help-block-error">', '</span>' ) ?>
					</div>
				</div>

				<?php if ( '' != $page->get_value( 'aepc_pixel_id' ) ) : ?>
					<div class="actions">
						<span class="pixel-id"><?php _e( 'Pixel ID', 'pixel-caffeine' ) ?>: <strong></strong></span>
						<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'pixel-disconnect', $page->get_view_url() ), 'pixel_disconnect' ) ) ?>" class="disconnect" data-toggle="modal" data-target="#modal-confirm-disconnect-pixel" data-remote="false"><?php _e( 'Disconnect', 'pixel-caffeine' ) ?></a>
					</div>
				<?php endif; ?>

			<?php else : ?>
				<p><?php _e( 'Great, You\'re using Facebook Connect to manage your Pixel. To change the pixel being used click "Edit" on the bottom right of the next box!', 'pixel-caffeine' ) ?></p>
			<?php endif; ?>

			<em class="divider">Or</em>
		</article><!-- ./sub-panel -->

		<?php

		if ( empty( $fb ) || ! $fb->is_logged_in() ) {
			$page->get_template_part( 'fb-connect-logged-out', array( 'fb' => $fb, 'page' => $page ) );
		}

		elseif ( $fb->get_account_id() != '' && $fb->get_pixel_id() != '' ) {
			$page->get_template_part( 'fb-connect-logged-in', array( 'fb' => $fb, 'page' => $page ) );
		}

		else {
			$page->get_template_part( 'fb-connect-to-setup', array( 'fb' => $fb, 'page' => $page ) );
		}

		?>

	</div><!-- ./panel-body -->
	<div class="panel-footer form-inline">
		<div class="form-group">
			<label for="" class="control-label"><?php _e( 'Pixel position', 'pixel-caffeine' ) ?></label>
			<div class="control-wrap">
				<select class="form-control" name="<?php $page->field_name( 'aepc_pixel_position' ) ?>" id="<?php $page->field_id( 'aepc_pixel_position' ) ?>">
					<?php $page->select_options_of( 'aepc_pixel_position', $page->get_value( 'aepc_pixel_position' ) ) ?>
				</select>
			</div><!-- ./control-wrap -->
		</div><!-- ./form-group -->
	</div><!-- ./panel-footer -->
</div><!-- ./panel-settings-set-fb-px -->
