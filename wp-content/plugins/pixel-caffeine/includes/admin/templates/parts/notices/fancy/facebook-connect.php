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

$fb = AEPC_Admin::$api;

if ( $fb->is_debug() || $fb->is_logged_in() ) {
	return;
}

if ( $fb->is_expired() ) : ?>

	<div class="alert alert-warning alert-fancy alert-token" role="alert">
		<div class="alert-inner">
			<h4 class="title"><?php _e( 'Facebook connection timed out or you need to authorize again.', 'pixel-caffeine' ) ?></h4>
			<p class="text">
				<?php _e( 'An error has occurred. Your authorization token may have expired or you\'ve revoked the permissions to Pixel Caffeine or Facebook could be experiencing a temporary problems. To reauthorize Pixel Caffeine click the button below!', 'pixel-caffeine' ) ?>
			</p>
			<a href="<?php echo esc_url( $fb->get_login_url() ) ?>" class="btn btn-success btn-raised"><?php _e( 'Renew Token', 'pixel-caffeine' ) ?></a>
		</div>
	</div>

<?php else : ?>

	<div class="alert alert-warning alert-fancy alert-connect" role="alert">
		<div class="alert-inner">
			<h4 class="title"><?php _e( 'Facebook Not Connected!', 'pixel-caffeine' ) ?></h4>
			<p class="text">
				<?php _e( 'In order to enable Advanced Custom Audience creation you need to connect Pixel Caffeine with Facebook. Click below to connect now!', 'pixel-caffeine' ) ?>
			</p>
			<a href="<?php echo esc_url( $fb->get_login_url() ) ?>" class="btn btn-success btn-raised"><?php _e( 'Connect', 'pixel-caffeine' ) ?></a>
		</div>
	</div>

<?php endif ?>
