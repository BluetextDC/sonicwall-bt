<?php
/**
 * Timeline Express :: Custom Admin Columns
 * This file handles all of our admin tables, including adding and rendering new columns.
 * By Code Parrots
 *
 * @link http://www.codeparrots.com
 *
 * @package WordPress
 * @subpackage Component
 * @since 1.2
 **/

/**
 * Add our custom table columns to the te_announcements table.
 *
 * @param type $timeline_express_announcement_columns Timeline Express default admin table columns.
 * @since v1.2
 * @return array New, custom admin tables.
 */
function add_new_timeline_express_columns( $timeline_express_announcement_columns ) {

	$date_column = $timeline_express_announcement_columns['date'];

	unset( $timeline_express_announcement_columns['date'] );

	foreach ( $timeline_express_announcement_columns as $key => $value ) {

		if ( 'past_announcement' === $key ) {

			$new['date'] = $date_column;

		}

		$new[ $key ] = $value;

	}

	$timeline_express_announcement_columns['cb']    = '<input type="checkbox" />';
	$timeline_express_announcement_columns['title'] = sprintf( /* translators: Singular announcement name (ie: Announcement) */ esc_html__( '%s Name', 'timeline-express-pro' ), apply_filters( 'timeline_express_singular_name', 'Announcement' ) );
	$timeline_express_announcement_columns['color'] = esc_html__( 'Color', 'timeline-express-pro' );

	// If years are being used, hide this column
	if ( ! defined( 'TIMELINE_EXPRESS_YEAR_ICONS' ) || ! TIMELINE_EXPRESS_YEAR_ICONS ) {

		$timeline_express_announcement_columns['icon'] = esc_html__( 'Icon', 'timeline-express-pro' );

	}

	$timeline_express_announcement_columns['announcement_date'] = sprintf( /* translators: Singular announcement name (ie: Announcement) */ esc_html__( '%s Date', 'timeline-express-pro' ), apply_filters( 'timeline_express_singular_name', 'Announcement' ) );
	$timeline_express_announcement_columns['image']             = esc_html__( 'Image', 'timeline-express-pro' );
	$timeline_express_announcement_columns['past_announcement'] = sprintf( /* translators: Singular announcement name (ie: Announcement) */ esc_html__( '%s Past?', 'timeline-express-pro' ), apply_filters( 'timeline_express_singular_name', 'Announcement' ) );
	$timeline_express_announcement_columns['date']              = esc_html__( 'Published Date', 'timeline-express-pro' );

	return $timeline_express_announcement_columns;

}
add_filter( 'manage_edit-te_announcements_columns', 'add_new_timeline_express_columns' );

/**
 * Render the content for our custom admin columns
 *
 * @since 1.2
 * @param string $column_name the name of the field to render.
 * @param string $id the id of the field to render.
 */
add_action( 'manage_te_announcements_posts_custom_column', 'manage_timeline_express_column_content', 10, 2 );
function manage_timeline_express_column_content( $column_name, $id ) {

	switch ( $column_name ) {

		case 'color':
			$announcement_color = get_post_meta( $id, 'announcement_color', true );

			if ( ! $announcement_color ) {

				echo '<span class="no-image-used-text"><em>' . esc_html__( 'not set', 'timeline-express-pro' ) . '</em></span>';

				return;

			}

			echo '<span class="announcement_color_box" style="background-color:' . esc_attr( $announcement_color ) . ';" title="' . esc_attr( $announcement_color ) . '"></span>';

			break;

		case 'icon':
			$announcement_icon = get_post_meta( $id, 'announcement_icon', true );

			if ( ! $announcement_icon ) {

				echo '<span class="no-image-used-text"><em>' . esc_html__( 'not set', 'timeline-express-pro' ) . '</em></span>';

				return;

			}

			echo '<span class="fa ' . esc_attr( $announcement_icon ) . ' edit-announcement-icon" title="' . esc_attr( $announcement_icon ) . '"></span>';

			break;

		case 'announcement_date':
			$announcment_date = get_post_meta( $id, 'announcement_date', true );

			if ( ! $announcment_date ) {

				echo '<span class="no-image-used-text"><em>' . esc_html__( 'not set', 'timeline-express-pro' ) . '</em></span>';

				return;

			}

			echo wp_kses_post( apply_filters( 'timeline_express_admin_column_date_format', date_i18n( apply_filters( 'timeline_express_custom_date_format', get_option( 'date_format' ) ), $announcment_date ), $announcment_date ) );

			break;

		case 'image':
			$announcement_image_id = get_post_meta( $id, 'announcement_image_id', true );

			if ( $announcement_image_id ) {

				echo wp_get_attachment_image( $announcement_image_id, 'timeline-express-thumbnail' );

			} else {

				echo '<span class="no-image-used-text"><em>' . esc_html__( 'not set', 'timeline-express-pro' ) . '</em></span>'; /* blank spot for spacing */

			}

			break;

		case 'past_announcement':
			$announcment_date = get_post_meta( $id, 'announcement_date', true );

			if ( ! $announcment_date ) {

				return;

			}

			$todays_date = strtotime( date_i18n( 'm/d/Y' ) );

			if ( $announcment_date < $todays_date ) {

				// translators: Singular announcement name (ie: Announcement)
				$title_attr = sprintf( esc_attr__( '%s has past.', 'timeline-express-pro' ), esc_attr( apply_filters( 'timeline_express_singular_name', 'Announcement' ) ) );

				echo '<div class="dashicon-past-announcement dashicons dashicons-backup" title="' . $title_attr . '" style="display:block;width:100%;"></div>';

			}

			break;

		default:
			break;

	} // End switch().

}

/**
 * Define which Timeline Express admin table columns are sortable.
 *
 * @since 1.2
 * @param array $columns Default array of sortable columns.
 * @return array $columns the array of columns that are now sortable.
 */
add_filter( 'manage_edit-te_announcements_sortable_columns', 'make_sortable_timeline_express_column' );
function make_sortable_timeline_express_column( $columns ) {

	$columns['announcement_date'] = 'announcement_date';

	return $columns;

}

/**
 * Custom Column Sorting
 *
 * @since 1.2
 * @param array $query default query array.
 * @return array $query return the altered, or default, query array.
 */
add_action( 'pre_get_posts', 'te_announcements_pre_get_posts', 1 );
function te_announcements_pre_get_posts( $query ) {

	$orderby = ( isset( $_GET['orderby'] ) ) ? sanitize_key( $_GET['orderby'] ) : false;

	if ( ! is_admin() || ! $orderby ) {

		return $query;

	}

	// We only want our code to run in the main WP query
	// AND only when an orderby query variable is designated.
	// AND when the orderby query variable is set to 'announcement_date'
	if ( ( $orderby === $query->get( 'orderby' ) ) && $query->is_main_query() ) {

		switch ( $orderby ) {

			/* If we're ordering by 'announcement_date'. */
			case 'announcement_date':
				/* set our query's meta_key, which is used for custom fields. */
				$query->set( 'meta_key', 'announcement_date' );
				$query->set( 'orderby', 'meta_value_num' );

				break;

		}
	}

	return $query;

}
