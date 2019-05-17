<?php

/**
 * Prevent Direct Access
 */
defined( 'ABSPATH' ) or die( "Restricted access!" );

/**
 * Generator of the help text under controls
 */
function spacexchimp_p011_control_help( $help=null ) {

    // Return if help text not defined
    if ( empty( $help ) ) {
        return;
    }

    // Generate a part of table
    $out = "<tr>
                <td></td>
                <td class='help-text'>
                    $help
                </td>
            </tr>";

    // Print the generated part of table
    echo $out;
}

/**
 * Generator of the field for saving license key to database
 */
function spacexchimp_p011_control_license( $name, $label, $help=null, $placeholder=null, $button_text ) {

    // Read options from database and declare variables
    $options = get_option( SPACEXCHIMP_P011_SETTINGS . '_info' );
    $value = !empty( $options[$name] ) ? esc_textarea( $options[$name] ) : '';

    // Generate a part of table
    $out = "<tr>
                <th scope='row'>
                    $label
                </th>
                <td>
                    <input
                        type='text'
                        name='" . SPACEXCHIMP_P011_SETTINGS . "_info[$name]'
                        id='" . SPACEXCHIMP_P011_SETTINGS . "_info[$name]'
                        value='$value'
                        placeholder='$placeholder'
                        class='control-field $name'
                    >
                    <input
                        type='submit'
                        name='submit'
                        id='submit'
                        class='btn btn-success'
                        value='$button_text'
                    >
                </td>
            </tr>";

    // Print the generated part of table
    echo $out;

    // Print a help text
    spacexchimp_p011_control_help( $help );
}
