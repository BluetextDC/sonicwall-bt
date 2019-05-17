<?php
defined( 'ABSPATH' ) or die( "No script kiddies please!" );
/**
 * Plugin Name: Smart Logo Showcase Lite
 * Plugin URI:  http://accesspressthemes.com/wordpress-plugins/smart-logo-showcase-lite/
 * Description: An ultimate WordPress Clients Logo Gallery Plugin | Showcase your client/ sponsor/ partner/ brand logo in a visually appealing way. | Easy to use!
 * Version:     1.1.1
 * Author:      AccessPress Themes
 * Author URI:  http://accesspressthemes.com/
 * Domain Path: /languages/
 * Text Domain: smart-logo-showcase-lite
 * */
/**
 * Declartion of necessary constants for plugin
 * */
defined( 'SMLS_VERSION' ) or define( 'SMLS_VERSION', '1.1.1' ); //plugin version
defined( 'SMLS_TD' ) or define( 'SMLS_TD', 'smart-logo-showcase-lite' ); //plugin's text domain
defined( 'SMLS_IMG_DIR' ) or define( 'SMLS_IMG_DIR', plugin_dir_url( __FILE__ ) . 'images' ); //plugin image directory
defined( 'SMLS_JS_DIR' ) or define( 'SMLS_JS_DIR', plugin_dir_url( __FILE__ ) . 'js' );  //plugin js directory
defined( 'SMLS_CSS_DIR' ) or define( 'SMLS_CSS_DIR', plugin_dir_url( __FILE__ ) . 'css' ); // plugin css dir
defined( 'SMLS_PATH' ) or define( 'SMLS_PATH', plugin_dir_path( __FILE__ ) );
defined( 'SMLS_URL' ) or define( 'SMLS_URL', plugin_dir_url( __FILE__ ) );

if ( ! class_exists( 'SMLS_Class' ) ) {

    class SMLS_Class{

        var $SMLS_settings;

        /**
         * Initializes the plugin functions
         */
        function __construct(){

            add_action( 'init', array( $this, 'plugin_text_domain' ) ); //loads text domain for translation ready
            add_action( 'wp_enqueue_scripts', array( $this, 'smls_register_assets' ) ); //registers scripts and styles for front end
            add_action( 'init', array( $this, 'smls_register_post_type' ) ); //register custom post type
            add_action( 'admin_enqueue_scripts', array( $this, 'smls_register_admin_assets' ) ); //register plugin scripts and css in wp-admin
            add_action( 'add_meta_boxes', array( $this, 'smls_add_slides_metabox' ) ); //added logo showcase metabox
            add_action( 'save_post', array( $this, 'smls_meta_save' ) );
            add_shortcode( 'smls', array( $this, 'smls_generate_shortcode' ) ); // generating shortcode
            add_action( 'add_meta_boxes', array( $this, 'smls_shortcode_usage_metabox' ) ); //added shortcode usages metabox
            add_action( 'add_meta_boxes', array( $this, 'smls_general_settings_metabox' ) ); //added general settings metabox
            add_action( 'save_post', array( $this, 'smls_general_setting_save' ) );
            add_action( 'wp_ajax_smls_logo_detail', array( $this, 'smls_logo_detail' ) );
            add_filter( 'manage_smartlogo_posts_columns', array( $this, 'smls_columns_head' ) ); //adding custom row
            add_action( 'manage_smartlogo_posts_custom_column', array( $this, 'smls_columns_content' ), 10, 2 ); //adding custom row content
            add_action( 'admin_head-post-new.php', array( $this, 'smls_posttype_admin_css' ) );
            add_action( 'admin_head-post.php', array( $this, 'smls_posttype_admin_css' ) );
            add_filter( 'post_row_actions', array( $this, 'smls_remove_row_actions' ), 10, 1 );
            add_action( 'admin_menu', array( $this, 'smls_register_about_us_page' ) ); //add submenu page
            add_action( 'admin_menu', array( $this, 'smls_register_stuff_page' ) ); //add submenu page

            add_action( 'add_meta_boxes', array( $this, 'smls_upgrade_metabox' ) ); //added upgrade metabox
        }

        //load the text domain for language translation
        function plugin_text_domain(){
            load_plugin_textdomain( 'smart-logo-showcase-lite', false, basename( dirname( __FILE__ ) ) . '/languages/' );
        }

        //register admin assets
        function smls_register_admin_assets( $hook ){
            global $post;
            wp_enqueue_media();
            wp_enqueue_style( 'thickbox' );
            wp_enqueue_script( 'thickbox' );
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
            if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
                if ( 'smartlogo' == $post -> post_type ) {
                    wp_enqueue_script( 'smls-admin-script', SMLS_JS_DIR . '/smls-admin-script.js', array( 'jquery', 'wp-color-picker', 'jquery-ui-sortable' ), SMLS_VERSION );
                    $admin_ajax_nonce = wp_create_nonce( 'smls-admin-ajax-nonce' );
                    $admin_ajax_object = array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'ajax_nonce' => $admin_ajax_nonce );
                    wp_localize_script( 'smls-admin-script', 'smls_backend_js_params', $admin_ajax_object );
                    wp_enqueue_style( 'smls-google-fonts-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800', false );
                    wp_enqueue_style( 'smls-google-fonts-roboto', 'https://fonts.googleapis.com/css?family=Roboto:400,700,700italic,900italic,900', false );
                    wp_enqueue_style( 'smls-google-fonts-lato', 'https://fonts.googleapis.com/css?family=Lato:400,300italic,400italic,700,700italic,900italic,900', false );
                }
            }
            wp_enqueue_style( 'smls-backend-style', SMLS_CSS_DIR . '/smls-backend-style.css', false, SMLS_VERSION );
        }

        //register frontend assests
        function smls_register_assets(){
            wp_enqueue_style( 'smls-fontawesome-style', SMLS_CSS_DIR . '/font-awesome.min.css', false, SMLS_VERSION );
            wp_enqueue_style( 'smls-google-fonts-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800', false );
            wp_enqueue_style( 'smls-google-fonts-roboto', 'https://fonts.googleapis.com/css?family=Roboto:400,300italic,400italic,500,500italic,700,700italic,900italic,900', false );
            wp_enqueue_style( 'smls-google-fonts-lato', 'https://fonts.googleapis.com/css?family=Lato:400,300italic,400italic,700,700italic,900italic,900', false );
            wp_enqueue_style( 'smls-google-fonts-montserrat', 'https://fonts.googleapis.com/css?family=Montserrat:400,700', false );
            wp_enqueue_style( 'smls-google-fonts-merriweather', 'https://fonts.googleapis.com/css?family=Merriweather+Sans:300,400,700,800+Sans:300,400,700', false );
            wp_enqueue_style( 'smls-google-fonts-droid', 'https://fonts.googleapis.com/css?family=Droid+Sans:400,700', false );
            wp_enqueue_style( 'smls-google-fonts-droid', 'https://fonts.googleapis.com/css?family=Droid+Serif:400,700', false );
            wp_enqueue_style( 'smls-google-fonts-oxygen', 'https://fonts.googleapis.com/css?family=Oxygen:300,400,700', false );
            wp_enqueue_style( 'smls-google-fonts-raleway', 'https://fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900', false );
            //end
            wp_enqueue_style( 'smls-owl-style', SMLS_CSS_DIR . '/owl.carousel.css', false, SMLS_VERSION );
            wp_enqueue_style( 'smls-tooltip-style', SMLS_CSS_DIR . '/tooltipster.bundle.css', false, SMLS_VERSION );
            wp_enqueue_style( 'smls-frontend-style', SMLS_CSS_DIR . '/smls-frontend-style.css', false, SMLS_VERSION );
            wp_enqueue_style( 'smls-responsive-style', SMLS_CSS_DIR . '/smls-responsive.css', false, SMLS_VERSION );
            wp_enqueue_script( 'smls-owl-script', SMLS_JS_DIR . '/owl.carousel.js', array( 'jquery' ), SMLS_VERSION );
            wp_enqueue_script( 'smls-tooltip-script', SMLS_JS_DIR . '/tooltipster.bundle.js', array( 'jquery' ), SMLS_VERSION );
            wp_enqueue_script( 'smls-frontend-script', SMLS_JS_DIR . '/smls-frontend-script.js', array( 'jquery', 'smls-owl-script', 'smls-tooltip-script' ), SMLS_VERSION );
        }

        //register smartlogo custom post type
        function smls_register_post_type(){
            include('inc/admin/smls-logo-register-post.php');
            register_post_type( 'smart logo', $args );
        }

        //adding Logo Showcase metabox
        function smls_add_slides_metabox(){
            add_meta_box( 'smls_add_showcase', __( 'Logo Showcase', SMLS_TD ), array( $this, 'smls_add_showcase_callback' ), 'smartlogo', 'normal', 'high' );
        }

        //callback function for Logo Showcase metabox
        function smls_add_showcase_callback( $post ){
            wp_nonce_field( basename( __FILE__ ), 'smls_showcase_nonce' );
            $smls_option = get_post_meta( $post -> ID );
            include('inc/admin/smls-logo-meta.php');
        }

        //save the metabox
        function smls_meta_save( $post_id ){

            // Checks save status
            $is_autosave = wp_is_post_autosave( $post_id );
            $is_revision = wp_is_post_revision( $post_id );
            $is_valid_nonce = ( isset( $_POST[ 'smls_showcase_nonce' ] ) && wp_verify_nonce( $_POST[ 'smls_showcase_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
            // Exits script depending on save status
            if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
                return;
            }
            if ( isset( $_POST[ 'smls_option' ] ) ) {
                /*
                  $smls_array = (array) $_POST['smls_option'];
                  $logo_array = array();
                  foreach ($smls_array['logo'] as $logo_key => $logo_value) {
                  // sanitize array
                  $smls_val = array_map('sanitize_text_field', $logo_value);
                  $logo_array[$logo_key] = $smls_val;
                  }
                  $smls_array['logo'] = $logo_array;
                 *
                 */
                // save data
                update_post_meta( $post_id, 'smls_option', $_POST[ 'smls_option' ] );
            }

            return;
        }

        function print_array( $array ){
            echo "<pre>";
            print_r( $array );
            echo "</pre>";
        }

        //generating shortcode with post id
        function smls_generate_shortcode( $atts, $content = null ){
            $args = array(
                'post_type' => 'smartlogo',
                'post_status' => 'publish',
                'posts_per_page' => 1,
                'p' => $atts[ 'id' ]
            );
            foreach ( $atts as $key => $val ) {
                $$key = $val;
            }
            $smls_logo = new WP_Query( $args );
            if ( $smls_logo -> have_posts() ) :
                ob_start();
                include('inc/frontend/smls-shortcode.php');
                $smls_showcase = ob_get_contents();
            endif;
            wp_reset_query();
            ob_end_clean();
            return $smls_showcase;
        }

        function smls_shortcode_usage_metabox(){
            add_meta_box( 'smls_shortcode_usage_option', __( 'Smart Logo Showcase Usage', SMLS_TD ), array( $this, 'smls_shortcode_usage_option_callback' ), 'smartlogo', 'side', 'default' );
        }

        function smls_shortcode_usage_option_callback( $post ){

            wp_nonce_field( basename( __FILE__ ), 'smls_shortcode_usage_option_nonce' );
            $smls_stored_meta_usage = get_post_meta( $post -> ID );
            include('inc/smls-settings/smls-usages-option.php');
        }

        function smls_general_settings_metabox(){
            add_meta_box( 'smls_settings', __( 'General Settings', SMLS_TD ), array( $this, 'smls_general_settings' ), 'smartlogo', 'normal', 'low' );
        }

        //callback function for general settings metabox
        function smls_general_settings( $post ){

            wp_nonce_field( basename( __FILE__ ), 'smls_general_settings_nonce' );
            $smls_settings = get_post_meta( $post -> ID );
            include('inc/smls-settings/smls-general-settings.php');
        }

        //save function for general setting metabox
        function smls_general_setting_save( $post_id ){
            if ( isset( $_POST[ 'smls_settings' ] ) ) {
                $smls = ( array ) $_POST[ 'smls_settings' ];
                // sanitize array
                $smls_option = array_map( 'sanitize_text_field', $smls );
                // save data
                update_post_meta( $post_id, 'smls_settings', $smls_option );
            }
            return;
            // Checks save status
            $is_autosave = wp_is_post_autosave( $post_id );
            $is_revision = wp_is_post_revision( $post_id );
            $is_valid_nonce = ( isset( $_POST[ 'smls_general_settings_nonce' ] ) && wp_verify_nonce( $_POST[ 'smls_general_settings_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
            // Exits script depending on save status
            if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
                return;
            }
        }

        function smls_logo_detail(){
            global $wpdb;
            include( 'inc/admin/smls-forms/smls-logo-data.php' );
            die();
        }

        function smls_generate_random_string( $length ){
            $string = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $random_string = '';
            for ( $i = 1; $i <= $length; $i ++ ) {
                $random_string .= $string[ rand( 0, 61 ) ];
            }
            return $random_string;
        }

        /*
         * Add custom column to smartlogo post
         */

        function smls_columns_head( $columns ){
            $columns[ 'shortcodes' ] = __( 'Shortcodes', SMLS_TD );
            $columns[ 'template' ] = __( 'Template Include', SMLS_TD );
            return $columns;
        }

        /*
         * Added content to custom column
         */

        function smls_columns_content( $column, $post_id ){
            if ( $column == 'shortcodes' ) {
                $id = $post_id;
                ?>
                <textarea style="resize: none;" rows="2" cols="15" readonly="readonly">[smls id="<?php echo $post_id; ?>"]</textarea><?php
            }
            if ( $column == 'template' ) {
                $id = $post_id;
                ?>
                <textarea style="resize: none;" rows="2" cols="32" readonly="readonly">&lt;?php echo do_shortcode("[smls id='<?php echo $post_id; ?>']"); ?&gt;</textarea><?php
            }
        }

        /*
         * Remove view and preview from smart logo post
         */

        function smls_posttype_admin_css(){
            global $post_type;
            $post_types = array(
                /* set post types */
                'smartlogo'
            );
            if ( in_array( $post_type, $post_types ) )
                echo '<style type="text/css">#post-preview, #view-post-btn, .updated a,#screen-meta-links .screen-meta-toggle
                {display: none;}</style>';
        }

        function smls_remove_row_actions( $actions ){
            if ( get_post_type() == 'smartlogo' ) { // choose the post type where you want to hide the button
                unset( $actions[ 'view' ] ); // this hides the VIEW button on your edit post screen
                unset( $actions[ 'inline hide-if-no-js' ] );
            }
            return $actions;
        }

        /*
         * Adding Submenu page
         */

        function smls_register_about_us_page(){
            add_submenu_page(
                    'edit.php?post_type=smartlogo', __( 'About Us', SMLS_TD ), __( 'About Us', SMLS_TD ), 'manage_options', 'smls-about-us', array( $this, 'smls_about_us_submenu_page_callback' ) );
        }

        function smls_about_us_submenu_page_callback(){

            include('inc/admin/smls-about-page.php');
        }

        function smls_register_stuff_page(){
            add_submenu_page(
                    'edit.php?post_type=smartlogo', __( 'More WordPress Stuff', SMLS_TD ), __( 'More WordPress Stuff', SMLS_TD ), 'manage_options', 'smls-stuff-page', array( $this, 'smls_stuff_page_callback' ) );
        }

        function smls_stuff_page_callback(){

            include('inc/admin/smls-stuff-page.php');
        }

        /*
         * Upgrade Metabox
         */

        function smls_upgrade_metabox(){
            add_meta_box( 'smls_upgrade_option', __( 'Upgrade to Pro Version', SMLS_TD ), array( $this, 'smls_upgrade_callback' ), 'smartlogo', 'side', 'default' );
        }

        function smls_upgrade_callback( $post ){

            wp_nonce_field( basename( __FILE__ ), 'smls_upgrade_nonce' );
            //$smls_stored_meta_usage = get_post_meta($post->ID);
            include('inc/smls-settings/upgrade.php');
        }

    }

    //class terminations

    $smls_obj = new SMLS_Class();
}//class exist check close
