<?php
/**
 * LightPost Wordpress plugin Administration Interface
 */
if (!class_exists('LightPost_front')) {

    class LightPost_front
    {


        public $prefix = 'lpw';
        public $hook = 'lightPost-wordpress';
        public $filename = 'lightPost-wordpress/lightPost-wordpress.php';
        public $longname = 'LightPost Wordpress';
        public $shortname = 'LightPost';
        public $options;
        public $version;

        /**
         * Constructor
         *
         * since 1.0
         */
        public function __construct($args = array())
        {

            // load LightPost options
            $this->options = get_option($this->prefix . '_options');

            // if LightPost is not enabled in any page we exit
            if (!isset($this->options['enable_page']) || empty($this->options['enable_page'])) return;

            $this->version = $args['version'];
            $this->prefix = $args['prefix'];

            // Add CSS & Javascript need for LightPost
            add_action('wp_enqueue_scripts', array($this, 'lpw_front_scripts'));

            // initialise LightPost
            add_action('wp', array($this, 'initLightPoxt'));

            // filter link post/page to get link LightPost
            add_action('wp', array($this, 'lpw_addClickEvent'));

        }

        /*
        * Enqueue Global scripts and styles for admin panels
        *
        * @since 1.0.0
        */
        function lpw_front_scripts()
        {
            if ($this->isEnabledOnPage()) {
                wp_enqueue_style($this->hook . '-lightpost-css', plugins_url('assets/css/front_main.css', dirname(__FILE__)), array(), $this->version, 'screen');
                wp_enqueue_script($this->hook . '-translate-js', plugins_url('assets/js/jquery.translate.js', dirname(__FILE__)), array('jquery'), '', false);
                wp_enqueue_script($this->hook . '-i18n-js', plugins_url('assets/js/i18n.js', dirname(__FILE__)), array('jquery'), '', false);
                wp_enqueue_script('ajax-script', plugins_url('assets/js/lightpost.js', dirname(__FILE__)), array('jquery'), '', false);
            }
        }


        /*
        * Add filter Change post permalink to add params LightPost
        *
        * @Since 1.0
        */
        public function lpw_addClickEvent()
        {

            if ($this->isEnabledOnPage()) {
                add_filter('post_type_link', array($this, 'addlinkLightPost'), 10, 2);
                add_filter('post_link', array($this, 'addlinkLightPost'), 10, 2);
                add_filter('page_link', array($this, 'addlinkLightPost'), 10, 2);
            }


        }

        /*
        * Change Permalink to add params LightPost and post id
        *
        * @Since 1.1
        */
        function addlinkLightPost($permalink, $post)
        {
            if (!is_object($post)) return $permalink;

            if ($this->isEnabledForPost($post->post_type) && $this->isClickLinkEnabled()) {

                if (substr_count($permalink, '?') > 0) {
                    return $permalink . '&lpw=' . $post->ID;
                } else  return $permalink . '?lpw=' . $post->ID;

            } else return $permalink;

        }

        /*
        * Check if user are enabled Click Event on permalink
        *
        * @Since 1.0
        */
        function isClickLinkEnabled()
        {

            $options = $this->options;
            if (!isset($options['use_link']) || empty($options['use_link']) || $options['use_link'] != 'on') return false;
            return true;

        }

        /*
        * Check if LightPost are enabled on current page
        *
        * @Since 1.0
        */
        function isEnabledOnPage()
        {
            $options = $this->options;
            $category = get_category(get_query_var('cat'));
            if (!empty($category) && isset($category->cat_ID)) $cat_id = $category->cat_ID;
            else $cat_id = null;

            if (!isset($options['enable_page']) || empty($options['enable_page'])) return false;

            $enable_page = $options['enable_page'];


            if (is_array($enable_page) && ((is_front_page() && array_key_exists('home', $enable_page)) ||
                    (is_search() && array_key_exists('search', $enable_page)) ||
                    (is_archive() && !is_category() && array_key_exists('archive', $enable_page)) ||
                    (is_category($cat_id) && array_key_exists('category', $enable_page)) ||
                    (is_single() && array_key_exists('single', $enable_page)) ||
                    (is_tag() && array_key_exists('tag', $enable_page)) ||
                    (is_page() && array_key_exists('page', $enable_page) && !is_front_page()))
            )
                return true;
            else return false;
        }

        /*
        * Check if LightPost are enabled for current type post
        *
        * @Since 1.0
        */
        function isEnabledForPost($typePost)
        {
            $options = $this->options;

            if (!isset($options['enable_type_post']) || empty($options['enable_type_post'])) return false;


            $enable_type_post = $options['enable_type_post'];

            if (array_key_exists($typePost, $enable_type_post)) return true;
            else return false;
        }

        /*
        * Add inline Javascript and initialise LightPost 
        *
        * @Since 1.0
        */
        public function initLightPoxt()
        {
            if ($this->isEnabledOnPage()) {
                $options = $this->options;
                $options = $this->options;
                $imgid = (isset($options['overlayImage'])) ? $options['overlayImage'] : "";
                $img = wp_get_attachment_image_src($imgid, 'full');
                $overlayImage = '';
                if ($img != "") $overlayImage = $img[0];

                $this->front_footer_output = '
                <script type="text/javascript">
                (function ($) {
                    var lpp = $.lightPostPopup({
                    ';

                $this->front_footer_output .= (isset($options["language"])) ? "language: '" . $options["language"] . "'," : "";
                $this->front_footer_output .= (isset($options["theme"])) ? "theme: '" . $options["theme"] . "'," : "";
                $this->front_footer_output .= (isset($options["width"])) ? "width: " . $options["width"] . "," : "";
                $this->front_footer_output .= (isset($options["borderRadius"])) ? "borderRadius: " . $options["borderRadius"] . "," : "";
                $this->front_footer_output .= (isset($options["fontSize"])) ? "fontSize: " . $options["fontSize"] . "," : "";
                $this->front_footer_output .= (isset($options["horizentalPadding"])) ? "horizentalPadding: " . $options["horizentalPadding"] . "," : "";
                $this->front_footer_output .= (isset($options["colorTitle"])) ? "colorTitle: '" . $options["colorTitle"] . "'," : "";
                $this->front_footer_output .= (isset($options["colorSubTitle"])) ? "colorSubTitle: '" . $options["colorSubTitle"] . "'," : "";
                $this->front_footer_output .= (isset($options["colorLink"])) ? "colorLink: '" . $options["colorLink"] . "'," : "";
                $this->front_footer_output .= (isset($options["colorText"])) ? "colorText: '" . $options["colorText"] . "'," : "";
                $this->front_footer_output .= (isset($options["colorBackground"])) ? "colorBackground: '" . $options["colorBackground"] . "'," : "";
                $this->front_footer_output .= (isset($options["layerBackground"])) ? "layerBackground: '" . $options["layerBackground"] . "'," : "";
                $this->front_footer_output .= (!empty($overlayImage)) ? "overlayImage: '" . $overlayImage . "'," : "";
                $this->front_footer_output .= (isset($options["blurValue"])) ? "blurValue: " . $options["blurValue"] . "," : "";
                $this->front_footer_output .= (isset($options["grayscaleValue"])) ? "grayscaleValue: " . $options["grayscaleValue"] . "," : "";
                $this->front_footer_output .= (isset($options["enableNavigation"])) ? "enableNavigation: '" . $options["enableNavigation"] . "'," : "";
                $this->front_footer_output .= (isset($options["showNavigationTitle"]) && count($options["showNavigationTitle"]) > 0) ? "showNavigationTitle: true," : "showNavigationTitle: false,";
                $this->front_footer_output .= (isset($options["zIndex"])) ? "zIndex: '" . $options["zIndex"] . "'," : "";
                $this->front_footer_output .= (isset($options["overlayClose"]) && count($options["overlayClose"]) > 0) ? "overlayClose: true," : "overlayClose: false,";
                $this->front_footer_output .= (isset($options["showComment"])) ? "showComment: " . $options["showComment"] . "," : "";
                $this->front_footer_output .= (isset($options["animated"]) && count($options["animated"]) > 0) ? "animated: true," : "animated: false,";
                $this->front_footer_output .= (isset($options["animation"])) ? "animation: '" . $options["animation"] . "'," : "";
                $this->front_footer_output .= (isset($options["openSpeed"])) ? "speed: '" . $options["openSpeed"] . "'," : "";
                $this->front_footer_output .= (isset($options["boxShadow"])) ? "boxShadow: " . $options["boxShadow"] . "," : "";
                $this->front_footer_output .= (isset($options["boxShadowColor"])) ? "boxShadowColor: '" . $options["boxShadowColor"] . "'," : "";
                $this->front_footer_output .= (isset($options["hide_on"]) && count($options["hide_on"]) > 0 && isset($options["hide_on"]["desktop"])) ? "hide_desktop: true," : "";
                $this->front_footer_output .= (isset($options["hide_on"]) && count($options["hide_on"]) > 0 && isset($options["hide_on"]["tablette"])) ? "hide_tablette: true," : "";
                $this->front_footer_output .= (isset($options["hide_on"]) && count($options["hide_on"]) > 0 && isset($options["hide_on"]["mobile"])) ? "hide_mobile: true," : "";
                $this->front_footer_output .= (isset($options["showTitle"]) && count($options["showTitle"]) > 0) ? "showTitle: true," : "showTitle: false,";
                $this->front_footer_output .= (isset($options["showThumbAuthor"]) && count($options["showThumbAuthor"]) > 0) ? "showThumbAuthor: true," : "showThumbAuthor: false,";
                $this->front_footer_output .= (isset($options["showAuthor"]) && count($options["showAuthor"]) > 0) ? "showAuthor: true," : "showAuthor: false,";
                $this->front_footer_output .= (isset($options["showCategories"]) && count($options["showCategories"]) > 0) ? "showCategories: true," : "showCategories: false,";
                $this->front_footer_output .= (isset($options["showDate"]) && count($options["showDate"]) > 0) ? "showDate: true," : "showDate: false,";
                $this->front_footer_output .= (isset($options["showMedia"]) && count($options["showMedia"]) > 0) ? "showMedia: true," : "showMedia: false,";
                $this->front_footer_output .= (isset($options["showContent"]) && count($options["showContent"]) > 0) ? "showContent: true," : "showContent: false,";
                $this->front_footer_output .= (isset($options["showSocialShare"]) && count($options["showSocialShare"]) > 0) ? "showSocialShare: true," : "showSocialShare: false,";
                $this->front_footer_output .= ' });

                })(jQuery)
                </script>
            ';

                add_action('wp_footer', array(&$this, 'front_header'), 15);
            }

        }

        /*
        * Get inline Javascript to add
        *
        * @Since 1.0
        */
        public function front_header()
        {
            echo $this->front_footer_output;
        }

    }

}

?>