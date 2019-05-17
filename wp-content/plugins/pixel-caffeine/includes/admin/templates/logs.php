<?php
/**
 * @var AEPC_Admin_View $page
 *
 * @package Pixel Caffeine
 */

use PixelCaffeine\Logs\LogRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="pixel-caffeine-wrapper">
	<div class="wrap wrap-custom-audiences">

		<h1 class="page-title"><?php $page->the_title() ?></h1>

		<?php $page->get_template_part( 'nav-tabs' ) ?>

		<section class="plugin-sec">
			<div class="plugin-content">

				<div class="alert-wrap">
					<?php $page->print_notices() ?>
				</div>

				<?php $page->get_template_part( 'tables/log-list' ) ?>

			</div><!-- ./plugin-content -->

			<?php $page->get_template_part( 'sidebar' ) ?>
		</section><!--/.plugin-sec-->

	</div><!--/.wrap -->
</div><!--/.pixel-caffeine-wrapper -->
