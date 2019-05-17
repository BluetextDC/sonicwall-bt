<?php
/**
 * General admin settings page
 *
 * This is the template with the HTML code for the General Settings admin page
 *
 * @var AEPC_Admin_View $page
 * @var array $widget
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$posts = AEPC_Admin::fetch_sidebar_posts( $widget );

if ( ! isset( $posts['error'] ) && empty( $posts ) ) {
	return;
}

?>

<div class="plugin-sidebar-item">
	<h5 class="list-group-tit"><?php _e( 'AdEspresso News', 'pixel-caffeine' ) ?></h5>

	<?php if ( ! empty( $posts['error'] ) ) : ?>
		<p><?php echo $posts['error'] ?></p>

	<?php elseif ( ! empty( $posts ) ) : ?>
	<div class="list-group no-icon">

		<?php foreach ( $posts as $post ) : ?>
		<div class="list-group-item">
			<div class="row-content">
				<a href="<?php echo $post['link'] ?>" class="list-group-item-heading" target="_blank"><?php echo $post['title'] ?></a>
				<span class="list-group-item-date"><?php printf( __( '%s ago', 'pixel-caffeine' ), human_time_diff( strtotime( $post['date'] ) ) ) ?></span>

				<p class="list-group-item-text"><?php echo wp_trim_words( $post['description'], 10 ) ?></p>
			</div>
		</div>
		<div class="list-group-separator"></div>
		<?php endforeach; ?>

	</div>
	<?php endif; ?>
</div>
