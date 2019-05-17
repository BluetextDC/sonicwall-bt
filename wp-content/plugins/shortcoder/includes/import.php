<?php

class Shortcoder_Import{
    
    public static function init(){
        
        add_action( 'wp_ajax_sc_export', array( __CLASS__, 'do_export' ) );
        
    }
    
    public static function import_form(){
        
        echo '<form method="post" enctype="multipart/form-data" id="import_form">';
        echo '<p class="import_desc">' . __( 'Are you sure want to import shortcodes ?', 'shortcoder' ) . '</p>';
        echo '<p class="import_desc2">' . __( 'Do you want to delete existing shortcodes and perform a clean import ? (selecting "cancel" will overwrite existing shortcodes)', 'shortcoder' ) . '</p>';
        echo '<input type="checkbox" name="fresh_import" id="fresh_import" value="1" />';
        echo '<input type="file" name="import" id="import" accept="text/plain"/>';
        wp_nonce_field( 'sc_import_data' );
        echo '<input type="submit" />';
        echo '</form>';
        
    }
    
    public static function check_import(){
        
        if( isset( $_POST ) && !empty( $_FILES[ 'import' ][ 'tmp_name' ] ) ){
            
            check_admin_referer( 'sc_import_data' );
            
            $file = wp_import_handle_upload();

            if ( isset( $file['error'] ) ){
                self::print_notice( __( 'Failed to import file. Error: ', 'shortcoder' ) . $file['error'] );
                return false;
            }

            $file_id = absint( $file['id'] );
            $file_path = get_attached_file( $file_id );
            $fresh_import = isset( $_POST[ 'fresh_import' ] ) && $_POST[ 'fresh_import' ] == '1' ? true : false;
            
            self::do_import( $file_path, $fresh_import );
            
        }
        
    }
    
    public static function do_import( $file_path, $fresh_import = false ){
        
        if ( !is_file( $file_path ) ){
            self::print_notice( __( 'Uploaded file does not exist for import. ', 'shortcoder' ) . $file_path );
        }
        
        $imported_json = utf8_encode( file_get_contents( $file_path ) );
        $imported_json = ( substr($imported_json, -1) == '0' ) ? substr($imported_json, 0 , -1) : $imported_json;
        $imported_data = json_decode( $imported_json, true );
        
        if( $imported_data && !empty( $imported_data ) ){
            
            $shortcodes = $fresh_import ? array() : Shortcoder::list_all();
            $import_count = 0;
            
            if( isset( $imported_data[ 'shortcodes' ] ) ){
                
                foreach( $imported_data[ 'shortcodes' ] as $name => $content ){
                    $shortcodes[ $name ] = wp_parse_args( $content, Shortcoder::defaults() );
                    $import_count++;
                }
                
                if( update_option( 'shortcoder_data', $shortcodes ) ){
                    self::print_notice( $import_count . __( ' shortcodes imported successfully !', 'shortcoder' ), 'success' );
                }else{
                    self::print_notice( __( 'Shortcodes are not updated because all the shortcodes remain the same.', 'shortcoder' ) );
                    return false;
                }
                
            }
            
        }else{
            self::print_notice( __( 'Failed to decode JSON in imported data. Error code: ', 'shortcoder' ) . json_last_error() );
        }
        
    }
    
    public static function do_export(){
        
        check_admin_referer( 'sc_export_data' );
        
        $export_file_name = 'shortcoder export ' . date( 'm/d/Y' ) . '.txt';
        $shortcodes = Shortcoder::list_all();
        
        $to_export = array(
            'shortcodes' => $shortcodes
        );
        
        $export_json = json_encode( $to_export );

        header('Content-Disposition: attachment; filename="' . $export_file_name . '"');
        header('Content-Type: text/plain');
        header('Content-Length: ' . strlen( $export_json ));
        header('Connection: close');
        
        echo $export_json;
        return;
    }
    
    public static function print_notice( $msg, $type = 'error' ){
        echo '<div class="notice notice-' . $type . ' is-dismissible"><p>' . $msg . '</p></div>';
    }
    
}

Shortcoder_Import::init();

?>