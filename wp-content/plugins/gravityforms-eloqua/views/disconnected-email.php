<?php
/**
 * Disconnected Email Template
 *
 * @package gfeloqua
 */
?>
<h3><?php _e( 'Eloqua Disconnected', 'gfeloqua' ); ?></h3>
<p><?php _e( 'It seems as though Gravity Forms has lost connection to your Eloqua account. This may cause issues with any forms conntected with Eloqua. Please login and re-connect your Eloqua Account here:', 'gfeloqua' ); ?></p>
<p><?php echo $settings_page_link; ?></p>
<p><?php _e( 'To view debugging information, you can now see the debugging log under Forms > Settings > Logging in your WordPress admin.', 'gfeloqua' ); ?></p>
