<?php

/**
 * Prevent Direct Access
 */
defined( 'ABSPATH' ) or die( "Restricted access!" );

/**
 * Render Sidebar
 */
?>
    <div class="inner-sidebar">
        <div id="side-sortables" class="meta-box-sortabless ui-sortable">

            <div class="postbox about">
                <h3 class="title"><?php _e( 'About', $text ); ?></h3>
                <div class="inside">
                    <p><?php _e( 'This plugin allows you to easily and safely add your custom functions (PHP code) directly out of your WordPress Admin Area, without the need to have an external editor.', $text ); ?></p>
                    <a href="https://docs.spacexchimp.com/product/plugin/my-custom-functions-pro/instructions.html" target="_blank" class="btn btn-primary"><?php _e( 'Read documentation', $text ); ?></a>
                </div>
            </div>

            <div id="backup" class="postbox">
                <h3 class="title"><?php _e( 'Backup', $text ); ?></h3>
                <div class="inside">
                    <p><?php _e( 'This plugin automatically creates backups of your PHP functions and places them in the catalog <code>backups</code>. Backups are created each time you press the "Save changes" button.', $text ); ?></p>
                    <a href="<?php echo SPACEXCHIMP_P011_URL . 'backups/backup-last.txt'; ?>" class="btn btn-primary" download="Snippets-Backup.txt"><?php _e( 'Download last backup file', $text ); ?></a>
                </div>
            </div>

            <div class="postbox help">
                <h3 class="title"><?php _e( 'Help', $text ); ?></h3>
                <div class="inside">
                    <p><?php _e( 'If you have a question, please read the information in the FAQ section.', $text ); ?></p>
                </div>
            </div>

        </div>
    </div>
<?php
