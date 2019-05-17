<?php

/**
 * Prevent Direct Access
 */
defined( 'ABSPATH' ) or die( "Restricted access!" );

/**
 * Hello message - Bootstrap Modal
 */
function spacexchimp_p011_hello_message() {

    $options = get_option( SPACEXCHIMP_P011_SETTINGS . '_settings' );

    if ( !empty( $options ) ) {
        return;
    }

    ?>
        <div id="hello-message" class="modal fade hello-message" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <img src="<?php echo SPACEXCHIMP_P011_URL . 'inc/img/spacexchimp-logo.png'; ?>">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <p><?php _e( 'Hello. We are the team of Space X-Chimp.', SPACEXCHIMP_P011_TEXT ); ?></p>
                        <p><?php printf(
                                        __( 'Thank you for installing our plugin! We hope you will love it! %s', SPACEXCHIMP_P011_TEXT ),
                                        '&#x1F603;'
                                        );
                            ?></p>
                    </div>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function($) {

                // Show the message
                $("#hello-message").modal();

                // Hide the message after 7 seconds
                setTimeout(function() {
                    $('#hello-message').modal('hide');
                }, 7000);
            });
        </script>
    <?php
}

/**
 * Successfull message
 */
function spacexchimp_p011_successfull_message() {

    // After settings updated
    if ( isset( $_GET['settings-updated'] ) ) {
        ?>
            <div id="message" class="updated">
                <p><?php _e( 'Custom functions updated successfully.', SPACEXCHIMP_P011_TEXT ); ?></p>
            </div>
        <?php
    }
}

/**
 * Error message
 */
function spacexchimp_p011_parsing_error_message() {

    $error = get_option( SPACEXCHIMP_P011_SETTINGS . '_error' );
    if ( $error == '1' ) {
        ?>
            <div id="message" class="error">
                <p>
                    <?php _e( 'Sorry, but your code causes a "Fatal error", so it is not applied!', SPACEXCHIMP_P011_TEXT ); ?><br/>
                    <?php _e( 'Please, check the code and try again.', SPACEXCHIMP_P011_TEXT ); ?>
                </p>
            </div>
        <?php
    }
}
