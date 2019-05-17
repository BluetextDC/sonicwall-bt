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

if ( ! AEPC_Admin::$api->is_logged_in() ) {
	return;
}

?>

<div class="panel panel-dashboard-activity">
	<div class="panel-heading">
		<h2 class="tit"><?php _e( 'Activity', 'pixel-caffeine' ) ?></h2>

		<div class="form-group is-empty pull-right">
			<div class="control-wrap">
				<select class="form-control" id="date-range" style="text-transform: none;">
					<option value="today"><?php _e( 'Today', 'pixel-caffeine' ) ?></option>
					<option value="yesterday"><?php _e( 'Yesterday', 'pixel-caffeine' ) ?></option>
					<option value="last-7-days"<?php selected( true ) ?>><?php _e( 'Last 7 days', 'pixel-caffeine' ) ?>: <?php echo date( _x( 'm/d', 'Short format of date without year', 'pixel-caffeine' ), time() - 7 * DAY_IN_SECONDS ) ?> - <?php echo date( _x( 'm/d', 'Short format of date without year', 'pixel-caffeine' ) ) ?></option>
					<option value="last-14-days"><?php _e( 'Last 14 days', 'pixel-caffeine' ) ?>: <?php echo date( _x( 'm/d', 'Short format of date without year', 'pixel-caffeine' ), time() - 14 * DAY_IN_SECONDS ) ?> - <?php echo date( _x( 'm/d', 'Short format of date without year', 'pixel-caffeine' ) ) ?></option>
				</select>
			</div><!-- ./control-wrap -->
		</div>
	</div>
	<div class="panel-body">
		<div id="activity-chart" style="width:100%; height:400px;"></div>
	</div>
</div><!-- ./panel-dashboard-activity -->
