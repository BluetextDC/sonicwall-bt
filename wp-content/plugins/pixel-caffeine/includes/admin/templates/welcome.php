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

$back_to = isset( $_GET['back_to'] ) ? esc_url( $_GET['back_to'] ) : false;
$updated_version = isset( $_GET['version'] ) ? $_GET['version'] : false;

?>

<div class="pixel-caffeine-wrapper">
	<div class="wrap wrap-dashboard">

	<h1 class="page-title"><?php $page->the_title() ?></h1>

	<?php $page->get_template_part( 'nav-tabs' ) ?>

	<section class="plugin-sec">
		<div class="plugin-content">

			<?php $page->get_template_part( 'welcomes/' . $updated_version, array( 'back_to' => $back_to ) ) ?>

		</div><!-- ./plugin-content -->

		<?php $page->get_template_part( 'sidebar' ) ?>
	</section>

	</div><!--/.wrap -->
</div><!--/.pixel-caffeine-wrapper -->
