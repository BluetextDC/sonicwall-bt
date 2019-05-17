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

$sidebar = AEPC_Admin::fetch_sidebar();

if ( false === $sidebar ) {
	return;
}

$widgets = isset( $sidebar->widgets ) ? $sidebar->widgets : array();

// Sidebar loaded
if ( isset( $sidebar->widgets ) ) : ?>

	<aside class="plugin-sidebar">

		<?php foreach ( $widgets as $widget ) {
			if ( empty( $widget->type ) ) {
				continue;
			}

			$page->get_template_part( 'widgets/' . $widget->type, array( 'widget' => $widget ) );
		} ?>

	</aside>

<?php else : ?>

	<aside class="plugin-sidebar<?php echo ! isset( $sidebar->widgets ) ? ' loading-sec' : '' ?>">

		<div class="plugin-sidebar-item fake-item loading-data loading-box">
			<h5 class="list-group-tit"><?php _e( 'Adespresso News', 'pixel-caffeine' ) ?></h5>
			<span class="loading-msg"><?php _e( 'Fetching News...', 'pixel-caffeine' ) ?></span>
			<div class="list-group no-icon">
				<div class="list-group-item">
					<div class="row-content">
					</div>
				</div>
			</div>
			<div class="list-group no-icon">
				<div class="list-group-item">
					<div class="row-content">
					</div>
				</div>
			</div>
			<div class="list-group no-icon">
				<div class="list-group-item">
					<div class="row-content">
					</div>
				</div>
			</div>
		</div>

	</aside>

<?php endif ?>
