<?php
/**
 * Wider Admin Menu > Settings > Alternate method
 */
?>
<div class="wpmwam">

<p><?php printf( __( 'Find the %s stylesheet.', 'wider-admin-menu' ), '<code>/wp-content/plugins/wider-admin-menu/css/wider-admin-menu.css</code>' ); ?></p>
<p><?php _e( 'Then either copy its contents to your theme&apos;s stylesheet', 'wider-admin-menu' ); ?></p>
<p><?php _e( 'OR', 'wider-admin-menu'); ?></p>
<p><?php _e( 'copy the file to your theme folder and add this to your theme&apos;s <code>functions.php</code> to load it:', 'wider-admin-menu' ); ?></p>

<pre<?php if ( version_compare( $wp_version, '3.8', '<' ) ) echo ' class="lt38"'; ?>>
/* 
 * Wider Admin Menu stylesheet
 */
function wpmwam_style() {
  wp_enqueue_style( 'wpmwam-style', get_stylesheet_directory_uri() . '/wider-admin-menu.css' );
}
add_action( 'admin_enqueue_scripts', 'wpmwam_style' );
</pre>
	
<p><strong><?php _e( 'That stylesheet covers WordPress version 5.', 'wider-admin-menu' ); ?></strong></p>
<p><?php printf( __( 'For WordPress %s, substitute %s.', 'wider-admin-menu'), '4', '<code>wider-admin-menu-40.css</code>' ); ?></p>
<p><?php printf( __( 'For WordPress %s to %s, substitute %s.', 'wider-admin-menu'), '3.8', '3.9.2', '<code>wider-admin-menu-38.css</code>' ); ?></p>
<p><?php printf( __( 'For WordPress %s to %s, substitute %s.', 'wider-admin-menu'), '3.5', '3.7.1', '<code>wider-admin-menu-35.css</code>' ); ?></p>
<p><?php printf( __( 'For WordPress %s to %s, substitute %s.', 'wider-admin-menu'), '3.3', '3.4.2', '<code>wider-admin-menu-33.css</code>' ); ?></p>
<p><?php _e( 'Then you can deactivate this plugin.', 'wider-admin-menu' ); ?></p>

</div>
