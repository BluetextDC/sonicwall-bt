<?php
/**
 * Uninstall plugin options
 *
 * @package Timeline Express - Date - Time Add-On
 * @since 1.2
 */

//if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {

	exit();

}

delete_option( 'timeline_express_date_time_migration_notice' );
delete_option( 'timeline_express_date_time_migration_ids' );
