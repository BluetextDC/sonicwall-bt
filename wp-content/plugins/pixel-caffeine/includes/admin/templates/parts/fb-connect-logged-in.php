<?php
/**
 * HTML for the facebook connect box when user is logged in
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

<article class="sub-panel sub-panel-fb-connect active">
	<h3 class="tit">
		<?php _e( 'Facebook Connect', 'pixel-caffeine' ) ?>
		<a href="#_" class="btn btn-fab btn-help btn-fab-mini" data-toggle="tooltip" data-placement="top" title="<?php _e( 'One click setup! Recommended option', 'pixel-caffeine' ) ?>"></a>
	</h3>

	<?php if ( ( $user_error = $fb->get_user() ) && is_wp_error( $user_error ) ) : ?>
		<?php $page->print_notice( 'error', $user_error->get_error_message() ) ?>

	<?php else :

		$account = $pixel = '';

		if ( $account_id = $fb->get_account_id() ) {
			$account = array(
				'id' => $account_id,
				'name' => $fb->get_account_name()
			);
		}

		if ( $pixel_id = $fb->get_pixel_id() ) {
			$pixel = array(
				'id' => $pixel_id,
				'name' => $fb->get_pixel_name()
			);
		}

		?>
		<div class="fb-connect-info">
			<span class="pixel-id"><?php _e( 'Pixel ID', 'pixel-caffeine' ) ?>: <strong class="pixel-id-value">#<?php echo $fb->get_pixel_id() ?></strong></span>

			<div class="user-info">

				<img class="user-avatar" src="<?php echo esc_url( $fb->get_user_photo_uri() ) ?>">

				<div class="user-info-account">
					<span class="user-ad-account"><?php _e( 'Ad Account', 'pixel-caffeine' ) ?>: <strong class="user-ad-account-value"><?php echo $fb->get_account_name() ?></strong></span>
					<span class="user-name"><?php echo $fb->get_user_name() ?></span>
				</div>

			</div>
		</div>
		<div class="user-actions">
			<a href="<?php echo esc_url( $fb->get_logout_url() ) ?>" class="user-disconnect" data-toggle="modal" data-target="#modal-confirm-disconnect-fb" data-remote="false"><?php _e( 'Disconnect', 'pixel-caffeine' ) ?></a>
			<a href="#_" class="user-edit" data-toggle="modal" data-target="#modal-fb-connect-options"><?php _e( 'Edit', 'pixel-caffeine' ) ?></a>

			<input type="hidden" name="aepc_account_id" id="aepc_account_id" value="<?php echo esc_attr( wp_json_encode( $account ) ) ?>" />
			<input type="hidden" name="aepc_pixel_id" id="aepc_pixel_id" value="<?php echo esc_attr( wp_json_encode( $pixel ) ) ?>" />
		</div>
	<?php endif; ?>
</article>
