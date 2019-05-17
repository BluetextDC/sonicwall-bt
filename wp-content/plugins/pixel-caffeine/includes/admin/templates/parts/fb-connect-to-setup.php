<?php
/**
 * HTML for the facebook connect box when user is logged in, but he must select account ID and pixel ID
 *
 * @var AEPC_Admin_View $page
 * @var AEPC_Facebook_Adapter $fb
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

$highlight_fbpixel = isset( $_GET['ref'] ) && 'fblogin' == $_GET['ref'];

?>

<article class="sub-panel sub-panel-fb-connect to-set-up<?php echo $highlight_fbpixel ? ' bumping' : '' ?>">
	<h3 class="tit">
		<?php _e( 'Facebook Connect', 'pixel-caffeine' ) ?>
		<a href="#_" class="btn btn-fab btn-help btn-fab-mini" data-toggle="tooltip" data-placement="top" title="<?php _e( 'One Click Setup Through Facebook. Recommended option!', 'pixel-caffeine' ) ?>"></a>
	</h3>

	<?php if ( ( $user_error = $fb->get_user() ) && is_wp_error( $user_error ) ) : ?>
		<?php $page->print_notice( 'error', $user_error->get_error_message() ) ?>

	<?php else : ?>
		<div class="js-options-group">
			<p><?php _e( 'Please select the Ad account and Pixel Id to use for this site!', 'pixel-caffeine' ) ?></p>

			<div class="form-group">
				<label for="aepc_account_id" class="control-label"><?php _e( 'Ad account', 'pixel-caffeine' ) ?></label>
				<div class="control-wrap">
					<select name="aepc_account_id" id="aepc_account_id" class="form-control" data-placeholder="<?php _e( 'Select an account', 'pixel-caffeine' ) ?>">
						<option></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="aepc_pixel_id" class="control-label"><?php _e( 'Pixel ID', 'pixel-caffeine' ) ?></label>
				<div class="control-wrap">
					<select name="aepc_pixel_id" id="aepc_pixel_id" class="form-control" data-placeholder="<?php _e( 'Select a Pixel ID', 'pixel-caffeine' ) ?>"<?php disabled( true ) ?>>
						<option></option>
					</select>
				</div>
			</div>

			<div class="user-info">
				<img class="user-avatar" src="<?php echo esc_url( $fb->get_user_photo_uri() ) ?>">
				<div class="user-info-account">
					<?php _e( 'You are connected to Facebook as', 'pixel-caffeine' ) ?>
					<strong class="user-name"><?php echo $fb->get_user_name() ?></strong>.
					<a href="<?php echo esc_url( $fb->get_logout_url() ) ?>" class="user-disconnect" data-toggle="modal" data-target="#modal-confirm-disconnect-fb" data-remote="false"><?php _e( 'Disconnect', 'pixel-caffeine' ) ?></a>
				</div>
			</div>

			<div class="sub-panel-actions">
				<a href="#_" class="btn btn-raised btn-success btn-apply js-save-facebook-options disabled"><?php _e( 'Apply', 'pixel-caffeine' ) ?><div class="ripple-container"></div></a>
			</div>
		</div>
	<?php endif; ?>
</article>
