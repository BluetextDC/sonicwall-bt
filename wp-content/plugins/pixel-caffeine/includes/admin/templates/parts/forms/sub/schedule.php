<?php
/**
 * Form add/edit of conversion
 *
 * @var AEPC_Admin_View $page
 * @var ProductCatalogManager $product_catalog
 * @var string $group
 * @var string $product_feed_id This is passed via AJAX when the user is selecting an existing product catalog and feed
 *
 * @package Pixel Caffeine
 */

use PixelCaffeine\ProductCatalog\Configuration;
use PixelCaffeine\ProductCatalog\ProductCatalogManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The fields must be hidden when:
 * - the page is loaded and the user chosen to select an existing product catalog and product feed
 */
$show = Configuration::VALUE_FB_ACTION_NEW === $group || ! empty( $product_feed_id );

// Default values
$interval             = $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL );
$interval_count       = $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL_COUNT );
$interval_day_of_week = $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_DAY_OF_WEEK );
$schedule_hour        = $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_HOUR );
$schedule_minute      = $page->get_feed_field_value( $product_catalog, Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_MINUTE );

if ( ! empty( $product_feed_id ) && 'new' !== $product_feed_id && defined('DOING_AJAX') && DOING_AJAX ) {
	try {
		// Retrieve data from FB API
		$product_feed = AEPC_Admin::$api->get_product_feed( $product_feed_id );

		$interval             = $product_feed->schedule->interval;
		$interval_count       = $product_feed->schedule->interval_count;
		$interval_day_of_week = ! empty( $product_feed->schedule->day_of_week ) ? $product_feed->schedule->day_of_week : '';
		$schedule_hour        = $product_feed->schedule->hour;
		$schedule_minute      = $product_feed->schedule->minute;
	} catch ( Exception $e ) {
		$fberror = $e->getMessage();
	}
}
?>

<div class="js-schedule-options <?php echo esc_attr( $group ) ?><?php echo ! $show ? ' hide' : '' ?>">

	<?php ! empty( $fberror ) && $page->print_notice( 'warning', nl2br( sprintf( __( "We couldn't retrieve the schedule options from the selected feed: %s.\nThe default ones are loaded.", 'pixel-caffeine' ), $fberror ) ) ) ?>
	
	<h2 class="sub-tit"><?php _e( 'Schedule Your Uploads', 'pixel-caffeine' ) ?></h2>

	<div class="form-group form-radio">
		<div class="control-wrap">

			<div class="radio">
				<label>
					<input
						type="radio"
						name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL ) ?>"
						id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL ) ?>"
						value="<?php echo \AEPC_Facebook_Adapter::FEED_SCHEDULE_INTERVAL_DAILY ?>"
						data-toggle="schedule-interval"
						<?php checked( \AEPC_Facebook_Adapter::FEED_SCHEDULE_INTERVAL_DAILY, $interval ) ?>
					><?php _e( 'Daily', 'pixel-caffeine' ) ?>
				</label>
			</div>

			<div class="radio">
				<label>
					<input
						type="radio"
						name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL ) ?>"
						id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL ) ?>"
						value="<?php echo \AEPC_Facebook_Adapter::FEED_SCHEDULE_INTERVAL_HOURLY ?>"
						data-toggle="schedule-interval"
						data-dep="hourly"
						<?php checked( \AEPC_Facebook_Adapter::FEED_SCHEDULE_INTERVAL_HOURLY, $interval ) ?>
					><?php _e( 'Hourly', 'pixel-caffeine' ) ?>
				</label>
			</div>

			<div class="radio">
				<label>
					<input
						type="radio"
						name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL ) ?>"
						id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL ) ?>"
						value="<?php echo \AEPC_Facebook_Adapter::FEED_SCHEDULE_INTERVAL_WEEKLY ?>"
						data-toggle="schedule-interval"
						data-dep="weekly"
						<?php checked( \AEPC_Facebook_Adapter::FEED_SCHEDULE_INTERVAL_WEEKLY, $interval ) ?>
					><?php _e( 'Weekly', 'pixel-caffeine' ) ?>
				</label>
			</div>

		</div>
	</div>

	<div class="form-group multiple-fields-inline">

		<div class="control-wrap <?php echo \AEPC_Facebook_Adapter::FEED_SCHEDULE_INTERVAL_HOURLY === $interval ? '' : 'hide' ?>" data-schedule-option="hourly">
			<label for="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL_COUNT ) ?>" class="control-label">
				<?php _e( 'Repeat', 'pixel-caffeine' ) ?>
			</label>
			<select
				class="form-control"
				id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL_COUNT ) ?>"
				name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_INTERVAL_COUNT ) ?>"
			>
				<?php foreach ( array( 1, 2, 3, 4, 6, 8, 12 ) as $count ) : ?>
					<option value="<?php echo $count ?>"<?php selected( $count, $interval_count ) ?>>
						<?php printf( _n( 'Every %s hour', 'Every %s hours', $count, 'pixel-caffeine' ), $count > 1 ? $count : '' ) ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="control-wrap <?php echo \AEPC_Facebook_Adapter::FEED_SCHEDULE_INTERVAL_WEEKLY === $interval ? '' : 'hide' ?>" data-schedule-option="weekly">
			<label for="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_DAY_OF_WEEK ) ?>" class="control-label">
				<?php _e( 'Repeat', 'pixel-caffeine' ) ?>
			</label>
			<select
				class="form-control"
				id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_DAY_OF_WEEK ) ?>"
				name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_DAY_OF_WEEK ) ?>"
			>
				<?php foreach ( $page->get_feed_weekly_options() as $k => $display ) : ?>
					<option value="<?php echo $k ?>"<?php selected( $interval_day_of_week, $k ) ?>>
						<?php echo $display ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="control-wrap">
			<label
				for="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_HOUR ) ?>"
				class="control-label"
			><?php _e( 'Time', 'pixel-caffeine' ) ?></label>
			<select
				class="form-control"
				id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_HOUR ) ?>"
				name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_HOUR ) ?>"
			>
				<?php for ( $hh = 0; $hh < 24; $hh++ ) : ?>
					<option value="<?php echo $hh ?>"<?php selected( $hh, $schedule_hour ) ?>>
						<?php echo str_pad( $hh, 2, '0', STR_PAD_LEFT ); ?>
					</option>
				<?php endfor; ?>
			</select>
			<select
				class="form-control"
				id="<?php $page->feed_field_id( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_MINUTE ) ?>"
				name="<?php $page->feed_field_name( Configuration::OPTION_FEED_CONFIG, $group, Configuration::OPTION_FB_PRODUCT_FEED_SCHEDULE_MINUTE ) ?>"
			>
				<?php for ( $mm = 0; $mm < 60; $mm++ ) : ?>
					<option value="<?php echo $mm ?>"<?php selected( $mm, $schedule_minute ) ?>>
						<?php echo str_pad( $mm, 2, '0', STR_PAD_LEFT ); ?>
					</option>
				<?php endfor; ?>
			</select>
		</div>

	</div>

</div>
