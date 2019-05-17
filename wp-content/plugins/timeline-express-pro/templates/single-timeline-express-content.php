<?php
/**
 * Content markup for single announcement templates
 *
 * @package Timeline Express
 * @by CodeParrots
 * @link http://www.codeparrots.com
 */

/**
 * Action hook to display content before the single announcement image
 *
 * @hooked timeline_express_categories_tax_markup - 5 @since 2.0.0
 * @hooked timeline_express_timelines_tax_markup - 10 @since 2.0.0
 */
do_action( 'timeline-express-single-before-image' );

/**
 * Render the announcement image.
 *
 * @param int    $post_id    The announcement (post) ID whos image you want to retreive.
 * @param string $image_size Name of the image size you want to retreive. Possible: timeline-express, full, large, medium, thumbnail.
 */
echo wp_kses_post( timeline_express_get_announcement_image( get_the_ID(), 'full' ) );

/* Action hook to display content after the single announcement image */
do_action( 'timeline-express-single-after-image' );

/**
 * Action hook to display content before the single announcement content
 *
 * @hooked timeline_express_date_markup 5
 * @hooked timeline_express_categories_tax_markup 10
 * @hooked timeline_express_timelines_tax_markup 15
 *
 * @since 2.0.0
 */
do_action( 'timeline-express-single-before-content' );

the_content();

/**
 * Action hook to display content before the single announcement content
 *
 * @hooked timeline_express_output_content_wrapper_end - 5 (outputs closing divs for the content) (@since 1.2.6.4)
*/
do_action( 'timeline-express-single-after-content' );
