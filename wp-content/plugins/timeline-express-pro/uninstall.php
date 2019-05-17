<?php
/**
 * Uninstall our plugin options, and all of our Announcements
 *
 * @package Timeline Express
 * @since 1.2
 */

//if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {

	exit();

}

global $wpdb;

$options = get_option( 'timeline_express_storage' );

/* If the user has set to delete the announcement on uninstall, do so */
if ( isset( $options['delete-announcement-posts-on-uninstallation'] ) && '1' === $options['delete-announcement-posts-on-uninstallation'] ) {

	/* SQL query for to delete all announcement posts */
	$sql = $wpdb->get_results( $wpdb->prepare( 'DELETE FROM %s WHERE post_type = "te_announcements"', $wpdb->posts ), ARRAY_A );

}

/* Delete Timeline Express options */
delete_option( 'timeline_express_storage' );
delete_option( 'timeline-express_nobug' );

/* Delete Timeline Express disable cache option */
delete_option( 'timeline-timeline_express_cache_enabled' );

/* For site options in multisite */
delete_site_option( 'timeline_express_storage' );

/* Delete Our Transients */
delete_transient( 'timeline_express_ad_title' );
delete_transient( 'timeline_express_ad_image' );
delete_transient( 'timeline_express_ad_content' );
delete_transient( 'timeline_express_ad_url' );
delete_transient( 'te_font_awesome_transient' );

/* Delete all timeline express query transients created on pages eg: timeline-express-query-PAGE_ID */
// Query the database for all transients with the text 'timeline-express-query'
$results = $wpdb->get_results( $wpdb->prepare( "SELECT * from `{$wpdb->prefix}options` WHERE option_name LIKE %s;", '%' . $wpdb->esc_like( 'timeline-express-query' ) . '%' ) );

if ( empty( $results ) ) {

	return;

}

// loop and delete our transient
foreach ( $results as $transient ) {

	delete_transient( str_replace( '_transient_', '', $transient->option_name ) );

}
