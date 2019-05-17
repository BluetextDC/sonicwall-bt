<?php
/**
 * Product catalog admin page
 *
 * This is the template with the HTML code for the Product Catalog admin page
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
	<div class="wrap wrap-prd-catalog">

		<h1 class="page-title"><?php $page->the_title() ?></h1>

		<?php $page->get_template_part( 'nav-tabs' ) ?>

		<section class="plugin-sec">
			<div class="plugin-content">

				<div class="alert-wrap">
				<?php $page->get_template_part( 'notices/fancy/product-catalog' ) ?>
					<?php $page->print_notices() ?>
				</div>

				<form method="post" id="mainform" data-toggle="ajax">

					<?php
						$page->get_template_part( 'panels/product-feed-status', array(
							'product_catalog' => $page->get_product_catalog()
						) );
					?>

				</form>
			</div><!-- ./plugin-content -->

			<?php $page->get_template_part( 'sidebar' ) ?>
		</section>
	</div><!--/.wrap -->

	<?php $page->get_template_part( 'modals/confirm-delete', array( 'title' => __( 'Delete Product Catalog', 'pixel-caffeine' ) ) ) ?>

	<?php $page->get_template_part( 'modals/confirm-refresh-product-feed' ) ?>

</div><!--/.pixel-caffeine-wrapper -->
