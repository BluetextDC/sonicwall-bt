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

if ( ! $fb->is_logged_in() ) : ?>

	<div class="alert alert-warning alert-fancy alert-connect" role="alert">
		<div class="alert-inner">
			<h4 class="title"><?php _e( 'Facebook Not Connected!', 'pixel-caffeine' ) ?></h4>
			<p class="text">
				<?php _e( 'In order to enable Product Catalog creation you need to connect Pixel Caffeine with Facebook. Click below to connect now!', 'pixel-caffeine' ) ?>
			</p>
			<a href="<?php echo esc_url( $fb->get_login_url() ) ?>" class="btn btn-success btn-raised"><?php _e( 'Connect', 'pixel-caffeine' ) ?></a>
		</div>
	</div>

<?php elseif ( $fb->is_expired() ) : ?>

	<div class="alert alert-warning alert-fancy alert-token" role="alert">
		<div class="alert-inner">
			<h4 class="title"><?php _e( 'Facebook connection timed out or you need to authorize again.', 'pixel-caffeine' ) ?></h4>
			<p class="text">
				<?php _e( 'An error has occurred. Your authorization token may have expired or you\'ve revoked the permissions to Pixel Caffeine or Facebook could be experiencing a temporary problems. To reauthorize Pixel Caffeine click the button below!', 'pixel-caffeine' ) ?>
			</p>
			<a href="<?php echo esc_url( $fb->get_login_url() ) ?>" class="btn btn-success btn-raised"><?php _e( 'Renew Token', 'pixel-caffeine' ) ?></a>
		</div>
	</div>

<?php elseif ( ! $fb->is_permission_granted( 'business_management' ) ) : ?>

	<div class="alert alert-warning alert-fancy alert-connect" role="alert">
		<div class="alert-inner">
			<h4 class="title"><?php _e( 'A new permission needs to be granted!', 'pixel-caffeine' ) ?></h4>
			<p class="text">
				<?php _e( 'Please click the login button below to refresh your connection and access the Product Catalog.', 'pixel-caffeine' ) ?>
			</p>
			<a href="<?php echo esc_url( $fb->get_login_url() ) ?>" class="btn btn-success btn-raised"><?php _e( 'Connect', 'pixel-caffeine' ) ?></a>
		</div>
	</div>

<?php elseif ( ! $fb->get_business_id() ) : ?>

	<div class="alert alert-warning alert-fancy alert-connect" role="alert">
		<div class="alert-inner">
			<h4 class="title"><?php _e( 'Please add a Business Manager account.', 'pixel-caffeine' ) ?></h4>
			<p class="text">
				<?php printf( __( 'A Business Manager account is required to access the Product Catalog. Please visit the %sGeneral Settings page%s and select an Ad Account that\'s associated with a Business Manager.', 'pixel-caffeine' ), '<a href="' . AEPC_Admin::get_page('general-settings')->get_view_url() . '">', '</a>' ) ?>
			</p>
		</div>
	</div>

<?php elseif ( ! AEPC_Addons_Support::are_detected_addons() ) : $supported = AEPC_Addons_Support::get_supported_addons() ?>

	<div class="alert alert-warning alert-fancy alert-connect" role="alert">
		<div class="alert-inner">
			<h4 class="title"><?php _e( 'No supported e-commerce plugin detected.', 'pixel-caffeine' ) ?></h4>
			<p class="text">
				<?php printf( __( 'This product catalog product works with the e-commerce side of your website. The plugins supported are: %s', 'pixel-caffeine' ), implode( ', ', AEPC_Addons_Support::get_supported_addon_names() ) ) ?>
			</p>
		</div>
	</div>

<?php elseif ( function_exists('WC') && version_compare( WC()->version, AEPC_WOO_VERSION_REQUIREMENT, '<' ) ) : ?>

	<div class="alert alert-warning alert-fancy alert-connect" role="alert">
		<div class="alert-inner">
			<h4 class="title"><?php _e( 'WooCommerce version compatibility.', 'pixel-caffeine' ) ?></h4>
			<p class="text">
				<?php printf( __( 'The WooCommerce %s version actually activated could be generate some problems during the feed generation. Please, upgrade WooCommerce to latest version available in order to make Pixel Caffeine works great!', 'pixel-caffeine' ), WC()->version ) ?>
			</p>
		</div>
	</div>

<?php endif; ?>
