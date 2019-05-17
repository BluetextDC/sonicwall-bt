<?php
/**
 * General admin settings page
 *
 * This is the template with the HTML code for the General Settings admin page
 *
 * @var AEPC_Admin_View $page
 * @var object $widget
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="plugin-sidebar-item banner-wrap">
	<a href="<?php echo $widget->link ?>" target="_blank">
		<img src="<?php echo $widget->img ?>">
	</a>
</div>
