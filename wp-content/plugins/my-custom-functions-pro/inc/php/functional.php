<?php

/**
 * Prevent Direct Access
 */
defined( 'ABSPATH' ) or die( "Restricted access!" );

/**
 * Prepare the user entered code for execution
 */
function spacexchimp_p011_prepare() {

    // Read options from database and declare variables
    $options = get_option( SPACEXCHIMP_P011_SETTINGS . '_settings' );
    $snippets = !empty( $options['snippets'] ) ? $options['snippets'] : array();

    // Create a variable for storing the user entered code
    $content_out = "";

    if ( !empty($snippets) ) {
        foreach ($snippets as $snippet) {

            // If the ON/OFF trigger is enabled...
            if ( isset($snippet['enable']) AND !empty($snippet['enable']) AND $snippet['enable'] == "on") {

                $content = $snippet['content'];

                // Cleaning
                $content = trim( $content );
                $content = ltrim( $content, '<?php' );
                $content = rtrim( $content, '?>' );

                $content_out .= $content;
            }
        }
    }

    // Return prepared code
    return $content_out;
}

/**
 * Check the user entered code for duplicate names of snippets
 */
function spacexchimp_p011_duplicates( $content ) {

    // Find names of user entered snippets and check for duplicates
    preg_match_all('/function[\s\n]+(\S+)[\s\n]*\(/i', $content, $user_func_names);
    $user_func_a = count( $user_func_names[1] );
    $user_func_b = count( array_unique( $user_func_names[1] ) );

    // Find all names of declared user snippets and mutch with names of user entered snippets
    $declared_func = get_defined_functions();
    $declared_func_user = array_intersect( $user_func_names[1], $declared_func['user'] );
    $declared_func_internal = array_intersect( $user_func_names[1], $declared_func['internal'] );

    // Update error status
    if ( $user_func_a != $user_func_b OR count( $declared_func_user ) != 0 OR count( $declared_func_internal ) != 0 ) {
        update_option( SPACEXCHIMP_P011_SETTINGS . '_error', '1' );   // ERROR
        $error_status = '1';
    } else {
        update_option( SPACEXCHIMP_P011_SETTINGS . '_error', '0' );   // RESET ERROR VALUE
        $error_status = '0';
    }

    // Return error status
    return $error_status;
}

/**
 * Execute the user entered code
 */
function spacexchimp_p011_exec() {

    // If STOP file exist...
    if ( file_exists( SPACEXCHIMP_P011_PATH . 'STOP' ) ) {
        return;   // EXIT
    }

    // Get the user entered and enabled snippets by calling the "prepare" function
    $content = spacexchimp_p011_prepare();

    // If content is empty...
    if ( empty( $content ) OR $content == ' ' ) {
        return;   // EXIT
    }

    // If the duplicates snippets finded...
    $duplicates = spacexchimp_p011_duplicates( $content );
    if ( $duplicates != 0 ) {
        return;   // EXIT
    }

    // Parsing and execute by Eval
    if ( false === @eval( $content ) ) {
        update_option( SPACEXCHIMP_P011_SETTINGS . '_error', '1' );   // ERROR
        return;   // EXIT
    } else {
        update_option( SPACEXCHIMP_P011_SETTINGS . '_error', '0' );   // RESET ERROR VALUE
    }
}
spacexchimp_p011_exec();

/**
 * Backup all user entered snippets to text file
 */
function spacexchimp_p011_backup() {

    // Read options from database and declare variables
    $options = get_option( SPACEXCHIMP_P011_SETTINGS . '_settings' );
    $snippets = !empty( $options['snippets'] ) ? $options['snippets'] : array();

    // Get all user entered snippets
    $contents = array();
    if ( !empty($snippets) ) {
        foreach ($snippets as $snippet) {
            $contents[] = "\n" . "/* " . $snippet['label'] . "\n" . " ------------------------- */" . "\n";;
            $contents[] = $snippet['content'] . "\n\n";
        }
    }

    // Name and destination of backup files
    $date = date( 'm-d-Y_hia' );
    $file_location_date = SPACEXCHIMP_P011_PATH . '/backups/backup-' . $date . '.txt';
    $file_location_last = SPACEXCHIMP_P011_PATH . '/backups/backup-last.txt';

    // Make two backup files
    file_put_contents ($file_location_date, $contents);
    file_put_contents ($file_location_last, $contents);
}
