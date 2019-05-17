<?php
/**
#_________________________________________________ PLUGIN
Module Name: Timeline Express - Extra Content
Module URI: https://www.wp-timelineexpress.com
Description: Add content above/before each announcement on the timeline.
Version: 1.0.0
Author: Code Parrots
Author URI: http://www.codeparrots.com
License: GPL2

#_________________________________________________ LICENSE
Copyright 2012-16 Code Parrots (email : codeparrots@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

#_________________________________________________ CONSTANTS
*/

if ( ! defined( 'WPINC' ) ) {

	die;

}

/**
 * Render the extra content metabox and fields.
 *
 * @return mixed Markup for the extra content metabox and fields.
 */
function load_timeline_express_extra_content_metaboxes() {

	require_once TIMELINE_EXPRESS_PATH . 'lib/modules/extra-content/lib/metabox.php';

}
add_action( 'cmb2_init', 'load_timeline_express_extra_content_metaboxes' );

/**
 * Render before extra content.
 *
 * @param  integer $post_id Announcement ID.
 *
 * @return Mixed            Markup for hte announcement extra before content.
 */
function timeline_express_pre_extra_content( $post_id ) {

	$before_content = get_post_meta( $post_id, 'announcement_before_content', true );

	if ( ! $before_content ) {

		return;

	}

	echo apply_filters( 'the_content', $before_content );

}
add_action( 'timeline-express-before-announcement-block', 'timeline_express_pre_extra_content' );

/**
 * Render after extra content.
 *
 * @param  integer $post_id Announcement ID.
 *
 * @return Mixed            Markup for hte announcement extra after content.
 */
function timeline_express_post_extra_content( $post_id ) {

	$after_content = get_post_meta( $post_id, 'announcement_after_content', true );

	if ( ! $after_content ) {

		return;

	}

	echo apply_filters( 'the_content', $after_content );

}
add_action( 'timeline-express-after-announcement-block', 'timeline_express_post_extra_content' );
