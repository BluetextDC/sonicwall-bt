<?php
/*
Plugin name: LightPost Wordpress
Plugin URI:  http://webmatiq.com/lightpost-wordpress-lightbox
Description: Lightbox to show post  wordpress on lightbox
Author:      WebMatiq
Author URI:  http://www.webmatiq.com
Version:     1.0
License:     Sold exclusively on CodeCanyon
*/

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !defined( 'LPW_VERSION' ) ) {
	define( 'LPW_VERSION', '1.0.0');
}

if( !defined( 'LPW_BASE_DIR' ) ) {
	define( 'LPW_BASE_DIR', plugin_dir_path( __FILE__ ));
}

if( !defined( 'LPW_DIR_NAME' ) ){
	define( 'LPW_DIR_NAME', plugin_basename( dirname( __FILE__ ) ) );
}

register_activation_hook( __FILE__, 'on_lpw_activate' );


/*
* Function on activation hook check version PHP & wp
*
* @Since 1.0
*/
function on_lpw_activate() {

	global $wp_version;

	update_option( 'lpw_redirect', true );
	$wp = '3.5';
	$php = '5.3.2';
    if ( version_compare( PHP_VERSION, $php, '<' ) )
        $flag = 'PHP';
    elseif
        ( version_compare( $wp_version, $wp, '<' ) )
        $flag = 'WordPress';
    else
        return;
    $version = 'PHP' == $flag ? $php : $wp;
    deactivate_plugins( basename( __FILE__ ) );
    wp_die('<p><strong>LightPost Wordpress </strong> requires <strong>'.$flag.'</strong> version <strong>'.$version.'</strong> or greater. Please contact your host.</p>','Plugin Activation Error',  array( 'response'=>200, 'back_link'=> TRUE ) );
}


if(!class_exists( 'LightPost_WP' )){

        class LightPost_WP
        {

            public $options = array();
            public $version = '1.0';
            public $prefix = 'lpw';
            public $hook = 'lpw';
            /*
             * Construcor
             * */
            function __construct()
            {


                add_action('admin_init', array($this, 'lpw_redirect_on_activation'));
                add_action('plugins_loaded', array($this, 'lpw_load_textdomain'));
                add_action('wp_enqueue_scripts', array($this, 'ajax_enqueue_scripts'), 100);

                // Initialise Admin/Front Class
                add_filter('init', array(&$this, 'lpw_init'));

                //Add shortcode
                add_action( 'init', array($this,'register_shortcodes'));

                //Create ajax handler for lightpost
                add_action('wp_ajax_nopriv_lightpost_load_data', array($this, 'lightpost_load_data'));
                add_action('wp_ajax_lightpost_load_data', array($this, 'lightpost_load_data'));

            }

            /*
            * Function instance front/admin file
            *
            * @Since 1.0
            */
            function lpw_init()
            {
                if (is_admin()) {

                    require_once('includes/lpw_admin.php');

                    $LightPost_admin = new LightPost_admin(array(
                        'prefix' => $this->prefix,
                        'version' => $this->version
                    ));
                } else {
                    require_once('includes/lpw_front.php');

                    $LightPost_admin = new LightPost_front(array(
                        'prefix' => $this->prefix,
                        'version' => $this->version
                    ));

                }
            }

            /**
             * Load plugin textdomain
             *
             * @since 1.0.
             */
            public function lpw_load_textdomain()
            {

                load_plugin_textdomain($this->hook, false, LPW_DIR_NAME . '/lang');
            }


            /*
            * Redirect on activation hook
            *
            * @Since 1.0
            */
            function lpw_redirect_on_activation()
            {

                if (get_option('lpw_redirect') == true) {
                    update_option('lpw_redirect', false);
                    if (!is_multisite()) :
                        wp_redirect(admin_url('admin.php?page=' . $this->hook.'_options'));
                    endif;
                }

            }

            /*
            * Add shortcode
            *
            * @Since 1.0
            */
            function register_shortcodes(){
                add_shortcode('lightpost', array($this, 'shortcode_lightpost'));
            }

            /*
            * Function transform shortcode
            *
            * @Since 1.0
            */
            function shortcode_lightpost($attr, $content){
                global $post;
                return  '<div class="lightpost" data-id="'. $post->ID .'">'. $content .'</div>';
            }

            /*
            * Load dynamique data for LightPost
            *
            * @Since 1.0
            */
            public function lightpost_load_data()
            {
                global $post;

                $lightpostData = array();
                $post = get_post($_POST['post_id']);
                $lightpostData['title'] = $post->post_title;
                $lightpostData['link'] = get_permalink($post->ID);
                if(class_exists(WPBMap)) WPBMap::addAllMappedShortcodes();

                $lightpostData['content'] = do_shortcode($post->post_content);
                $lightpostData['publishedAt'] = strtotime($post->post_date);
                $author = array(
                    'id' => $post->post_author,
                    "fullname" => get_the_author_meta( 'user_nicename' , $post->post_author ) ,
                    "url" => get_author_posts_url( $post->post_author ),
                    "image_uri" => get_avatar_url(  $post->post_author )
                );
                $cats = get_the_category($_POST['post_id']);
                foreach($cats as $cat){
                    $categories[] = array(
                        'name' => $cat->name,
                        'url' => get_category_link($cat->term_id)
                    );
                }

                $lightpostData['author'] = $author;
                $lightpostData['categories'] = $categories;

                $next = get_next_post();
                $previous = get_previous_post();
                if($next != null && !empty($next)){
                    $lightpostData['next_post'] = array(
                        'id' => $next->ID,
                        'title' => $next->post_title
                    );;
                }
                if($previous != null && !empty($previous)){
                    $lightpostData['previous_post'] = array(
                        'id' => $previous->ID,
                        'title' => $previous->post_title
                    );
                }
                $overlayImage = get_post_meta($post->ID,'overlayImage',true);
                if(!empty($overlayImage)){
                    $overlayImage = wp_get_attachment_image_src( $overlayImage, 'full' , false );
                    if(isset($overlayImage[0]))
                        $lightpostData['overlayImage']= $overlayImage[0];
                }

                $typeMedia = get_post_meta($post->ID,'featured_media_type',true);
                if($typeMedia == 'image' || empty($typeMedia)){
                    $urlImage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' , false );
                    if(isset($urlImage[0])){
                        $lightpostData['media']['type'] =  'image';
                        $lightpostData['media']['url'] = $urlImage[0] ;

                    }

                }

                if($typeMedia == 'embed'){
                    $lightpostData['media']['type'] =  'embed';
                    $lightpostData['media']['embed'] =  htmlspecialchars_decode(get_post_meta($post->ID,'featured_media_code',true));
                }

                echo json_encode($lightpostData);
                die();
            }

            /*
            * Localise script Ajax Api WP
            *
            * @Since 1.0
            */
            public function ajax_enqueue_scripts()
            {

                wp_localize_script('ajax-script', 'lightpost', array( 'ajax_url' => admin_url('admin-ajax.php')
                ));

            }

        }

}

new LightPost_WP();

?>