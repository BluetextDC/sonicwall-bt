<?php
/*
Plugin Name: Shortcoder
Plugin URI: https://www.aakashweb.com/
Description: Shortcoder is a plugin which allows to create a custom shortcode and store HTML, JavaScript and other snippets in it. So if that shortcode is used in any post or pages, then the code stored in the shortcode get executed in that place. You can create a shortcode for Youtube videos, adsense ads, buttons and more.
Author: Aakash Chakravarthy
Version: 4.3
Author URI: https://www.aakashweb.com/
*/

define( 'SC_VERSION', '4.3' );
define( 'SC_PATH', plugin_dir_path( __FILE__ ) ); // All have trailing slash
define( 'SC_URL', plugin_dir_url( __FILE__ ) );
define( 'SC_ADMIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) . 'admin' ) );
define( 'SC_BASE_NAME', plugin_basename( __FILE__ ) );

class Shortcoder{
    
    public static function init(){
        
        add_action( 'plugins_loaded', array( __class__, 'load_text_domain' ) );
        
        register_activation_hook( __FILE__, array( __class__, 'on_activate' ) );
        
        add_filter( 'the_content', array( __class__, 'wp_44_workaround' ), 5 );
        
        // Register the shortcode
        add_shortcode( 'sc', array( __class__, 'execute_shortcode' ) );
        
        // Include the required
        self::includes();
        
    }
    
    public static function list_all(){
        
        $shortcodes = get_option( 'shortcoder_data' );
        
        return empty( $shortcodes ) ? array() : $shortcodes;
        
    }
    
    public static function includes(){
        
        include_once( SC_PATH . 'includes/metadata.php' );
        include_once( SC_PATH . 'includes/import.php' );
        include_once( SC_PATH . 'admin/sc-admin.php' );
        
    }
    
    public static function execute_shortcode( $atts, $enc_content ) {
        
        $shortcodes = self::list_all();
        
        if( empty( $shortcodes ) ){
            return '';
        }
        
        // Get the Shortcode name
        if(isset($atts[0])){
            $sc_name = str_replace(array('"', "'", ":"), '', $atts[0]);
            unset($atts[0]);
        }else{
            // Old version with "name" param support
            if(array_key_exists("name", $atts)){
                $tVal = $atts['name'];
                if(array_key_exists($tVal, $shortcodes)){
                    $sc_name = $tVal;
                    unset($atts['name']);
                }
            }
        }
        
        if(!isset($sc_name)){
            return '';
        }
        
        // Check whether shortcoder can execute
        if( self::check_conditions( $sc_name ) ){
        
            $sc_content_final = '';
        
            // If SC has parameters, then replace it
            if( !empty( $atts ) ){
                
                $keys = array();
                $values = array();
                $i = 0;
        
                // Seperate key and value from atts
                foreach( $atts as $k => $v ){
                    if( $k !== 0 ){
                        $keys[$i] = "%%" . $k . "%%";
                        $values[$i] = $v;
                    }
                    $i++;
                }
                
                // Replace the params
                $sc_content = $shortcodes[ $sc_name ][ 'content' ]; 
                $sc_content_rep1 = str_ireplace( $keys, $values, $sc_content );
                $sc_content_final = preg_replace( '/%%[^%\s]+%%/', '', $sc_content_rep1 );
                
            }
            else{
            
                // If the SC has no params, then replace the %%vars%%
                $sc_content = $shortcodes[ $sc_name ][ 'content' ]; 
                $sc_content_final = preg_replace( '/%%[^%\s]+%%/', '', $sc_content );
                
            }
            
            $sc_content_final = self::replace_wp_params( $sc_content_final, $enc_content );
            
            $sc_content_final = self::replace_custom_fields( $sc_content_final );
            
            return do_shortcode( $sc_content_final );
            
        }else{
            return '<!-- Shortcoder conditions not met -->';
        }
    }
    
    public static function check_conditions( $name ){
        
        $shortcodes = self::list_all();
        
        if( array_key_exists( $name, $shortcodes ) ){
            
            $sc = wp_parse_args( $shortcodes[ $name ], self::defaults() );
            
            $devices = $sc[ 'devices' ];
            if( $devices == 'mobile_only' && !wp_is_mobile() ){
                return false;
            }
            
            if( $devices == 'desktop_only' && wp_is_mobile() ){
                return false;
            }
            
            if( $sc[ 'disabled' ] == 0 ){
                if( current_user_can( 'level_10' ) && $sc[ 'hide_admin' ] == 1 ){
                    return false;
                }else{
                    return true;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
        
    }
    
    public static function replace_wp_params( $content, $enc_content ){
        
        $params = self::wp_params_list();
        $all_params = array();
        
        foreach( $params as $group => $group_info ){
            $all_params = array_merge( $group_info[ 'params' ], $all_params );
        }
        
        $metadata = Shortcoder_Metadata::metadata();
        $to_replace = array();
        
        $metadata[ 'enclosed_content' ] = $enc_content;
        
        foreach( $all_params as $id => $name ){
            if( array_key_exists( $id, $metadata ) ){
                $placeholder = '$$' . $id . '$$';
                $to_replace[ $placeholder ] = $metadata[ $id ];
            }
        }
        
        $content = strtr( $content, $to_replace );
        
        return $content;
        
    }
    
    public static function replace_custom_fields( $content ){
        
        global $post;
        
        preg_match_all('/\$\$[^\s]+\$\$/', $content, $matches );
        
        $cf_tags = $matches[0];
        
        if( empty( $cf_tags ) ){
            return $content;
        }
        
        foreach( $cf_tags as $tag ){
            
            if( strpos( $tag, 'custom_field:' ) === false ){
                continue;
            }
            
            preg_match( '/:[^\s\$]+/', $tag, $match );
            
            if( empty( $match ) ){
                continue;
            }
            
            $match = substr( $match[0], 1 );
            $value = get_post_meta( $post->ID, $match, true );
            $content = str_replace( $tag, $value, $content );
            
        }
        
        return $content;
        
    }
    
    public static function wp_params_list(){
        return apply_filters( 'sc_mod_wp_params', array(
            'wp_info' => array(
                'name' => __( 'WordPress information', 'shortcoder' ),
                'params' => array(
                    'url' => __( 'URL of the post/location', 'shortcoder' ),
                    'title' => __( 'Title of the post/location', 'shortcoder' ),
                    'short_url' => __( 'Short URL of the post/location', 'shortcoder' ),
                    
                    'post_id' => __( 'Post ID', 'shortcoder' ),
                    'post_image' => __( 'Post featured image URL', 'shortcoder' ),
                    'post_excerpt' => __( 'Post excerpt', 'shortcoder' ),
                    'post_author' => __( 'Post author', 'shortcoder' ),
                    'post_date' => __( 'Post date', 'shortcoder' ),
                    'post_modified_date' => __( 'Post modified date', 'shortcoder' ),
                    'post_comments_count' => __( 'Post comments count', 'shortcoder' ),
                    
                    'site_name' => __( 'Site title', 'shortcoder' ),
                    'site_description' => __( 'Site description', 'shortcoder' ),
                    'site_url' => __( 'Site URL', 'shortcoder' ),
                    'site_wpurl' => __( 'WordPress URL', 'shortcoder' ),
                    'site_charset' => __( 'Site character set', 'shortcoder' ),
                    'wp_version' => __( 'WordPress version', 'shortcoder' ),
                    'stylesheet_url' => __( 'Active theme\'s stylesheet URL', 'shortcoder' ),
                    'stylesheet_directory' => __( 'Active theme\'s directory', 'shortcoder' ),
                    'atom_url' => __( 'Atom feed URL', 'shortcoder' ),
                    'rss_url' => __( 'RSS 2.0 feed URL', 'shortcoder' )
                )
            ),
            'date_info' => array(
                'name' => __( 'Date parameters', 'shortcoder' ),
                'params' => array(
                    'day' => __( 'Day', 'shortcoder' ),
                    'day_lz' => __( 'Day - leading zeros', 'shortcoder' ),
                    'day_ws' => __( 'Day - words - short form', 'shortcoder' ),
                    'day_wf' => __( 'Day - words - full form', 'shortcoder' ),
                    'month' => __( 'Month', 'shortcoder' ),
                    'month_lz' => __( 'Month - leading zeros', 'shortcoder' ),
                    'month_ws' => __( 'Month - words - short form', 'shortcoder' ),
                    'month_wf' => __( 'Month - words - full form', 'shortcoder' ),
                    'year' => __( 'Year', 'shortcoder' ),
                    'year_2d' => __( 'Year - 2 digit', 'shortcoder' ),
                )
            ),
            'sc_cnt' => array(
                'name' => __( 'Shortcode enclosed content', 'shortcoder' ),
                'params' => array(
                    'enclosed_content' => __( 'Shortcode enclosed content', 'shortcoder' )
                )
            )
        ));
    }
    
    public static function get_tags(){
        
        $shortcodes = self::list_all();
        $all_tags = array();
        
        foreach( $shortcodes as $id => $opts ){
            if( isset( $opts['tags'] ) && !empty( $opts['tags'] ) && is_array($opts['tags']) ){
                $all_tags = array_merge( $all_tags, $opts['tags'] );
            }
        }
        
        return array_unique($all_tags);
        
    }
    
    public static function on_activate(){
        
        $shortcodes = self::list_all();
        $sc_flags = get_option( 'shortcoder_flags' );
        
        // Move the flag version fix to sc_flags option
        if( isset( $shortcodes[ '_version_fix' ] ) ){
            unset( $shortcodes['_version_fix'] );
            update_option( 'shortcoder_data', $shortcodes );
        }
        
        $sc_flags[ 'version' ] = SC_VERSION;
        update_option( 'shortcoder_flags', $sc_flags );

    }
    
    public static function defaults(){
        return array(
            'content' => '',
            'disabled' => 0,
            'hide_admin' => 0,
            'devices' => 'all',
            'editor' => 'text',
            'tags' => array()
        );
    }
    
    public static function can_edit_sc(){
        return current_user_can( 'manage_options' );
    }
    
    public static function wp_44_workaround( $content ){
        return str_replace( '[sc:', '[sc name=', $content );
    }
    
    public static function load_text_domain(){
        
        load_plugin_textdomain( 'shortcoder', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
        
    }
    
}

Shortcoder::init();

?>