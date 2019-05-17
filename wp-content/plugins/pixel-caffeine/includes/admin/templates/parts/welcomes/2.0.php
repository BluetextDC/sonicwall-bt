<?php
/**
 * @var AEPC_Admin_View $page
 * @var string $back_to
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="jumbotron intro-dashboard intro-dude dude-prd-catalog intro-product-catalog">
	<div class="jumbotron-body">
		<h2 class="tit"><?php _e( 'Great news!', 'pixel-caffeine' ) ?></h2>

		<p class="text"><?php _e( 'Pixel Caffeine 2.0 now supports Product Catalog creation.', 'pixel-caffeine' ) ?></p>
		<p class="text"><?php _e( 'If you\'re in eCommerce, it\'s now super easy to upload your product catalog to Facebook and have it stay always up to date.', 'pixel-caffeine' ) ?></p>

		<div class="calltoact">
			<a href="<?php echo $page->get_view_url( 'tab=product-catalog' ) ?>" class="btn btn-raised btn-success btn-config"><?php _e( 'Learn more', 'pixel-caffeine' ) ?></a>
		</div>

		<?php if ( $back_to ) : ?>
		<div class="actions">
			<a href="<?php echo esc_url( $back_to ) ?>" class="btn-back"><?php _e( 'Back to previous page', 'pixel-caffeine' ) ?></a>
		</div>
		<?php endif; ?>
	</div>
</div>

<?php $page->get_template_part( 'welcomes/features' ) ?>
