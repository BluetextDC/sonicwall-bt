<?php

/**
 * Prevent Direct Access
 */
defined( 'ABSPATH' ) or die( "Restricted access!" );

/**
 * Render License Tab Content
 */
?>
    <form action="options.php" method="post" enctype="multipart/form-data">
        <?php settings_fields( SPACEXCHIMP_P011_SETTINGS . '_settings_group_info' ); ?>

        <div class="postbox" id="license">
            <h3 class="title"><?php _e( 'License Info', $text ); ?></h3>
            <div class="inside">
                <p class="note"><?php
                                    printf(
                                            __( 'Here you can manage the license for the plugin %s .', $text ),
                                            $name
                                          );
                                ?></p>
                <table class="form-table">
                    <?php
                        spacexchimp_p011_control_license( 'license_key',
                                                          __( 'License Key', $text ),
                                                          __( 'Please enter the purchase code / license key you received when purchasing the plugin.', $text ),
                                                          '',
                                                          'Submit License Key'
                                                        );
                    ?>
                </table>
            </div>
        </div>

    </form>
<?php
