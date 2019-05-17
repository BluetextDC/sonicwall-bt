<?php

/**
 * Prevent Direct Access
 */
defined( 'ABSPATH' ) or die( "Restricted access!" );

?>


jQuery(document).ready(function($) {

    // Count
    var countadd = <?php $options = get_option( 'spacexchimp_p011_settings' ); $countadd = count($options['snippets']); echo $countadd; ?>;

    // Add a new repeating section
    $('.addSection').click( function(e) {
        e.preventDefault();

        countadd = countadd + 1;

        $(this).before('<div class="postbox" id="repeatingSection"><h3 class="title"><input type="text" name="spacexchimp_p011_settings[snippets][function-' + countadd + '][label]" id="spacexchimp_p011_settings[snippets][function-' + countadd + '][label]" size="50%" value="" placeholder="Title or small description" /><span class="new-not-saved"><?php _e( "NOT SAVED!", "my-custom-functions-pro" ); ?></span></h3><div class="inside hide show newSection"><textarea name="spacexchimp_p011_settings[snippets][function-' + countadd + '][content]" id="spacexchimp_p011_settings[snippets][function-' + countadd + '][content]" placeholder="Enter your PHP function here"></textarea></div></div>');
    });

});
