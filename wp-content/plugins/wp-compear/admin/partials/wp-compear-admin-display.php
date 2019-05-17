<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://davenicosia.com
 * @since      1.0.0
 *
 * @package    WP_Compear
 * @subpackage WP_Compear/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->


<div class="wrap">
     
    <div id="icon-themes" class="icon32"></div>
    <h2><?php _e('WP ComPEAR Settings', 'wp-compear'); ?></h2>
    <?php //settings_errors(); ?>

    <?php
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'compear_options';
	?>
     
    <h2 class="nav-tab-wrapper">
        <a href="?page=wp-compear-options&tab=compear_options" class="nav-tab <?php echo $active_tab == 'compear_options' ? 'nav-tab-active' : ''; ?>"><?php _e('WP ComPEAR License Key', 'wp-compear'); ?></a>
    </h2>
     
    <form method="post" action="options.php">

        <?php
         
        
        if ( $active_tab == 'compear_options') {
            settings_fields( 'WP_Compear_options' );
            do_settings_sections( 'wp-compear-options' );
        } // end if/else

        // else if( $active_tab == 'compear_lists' ) {
        //  settings_fields( 'WP_Compear_lists' );
        //     do_settings_sections( 'wp-compear-lists' );
        // } 
         
        //submit_button();
         
    	?>
         
    </form>
     
</div><!-- /.wrap -->

