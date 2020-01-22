<?php
/**
 * LightPost Wordpress plugin Administration Interface
 */
if (!class_exists('LightPost_admin')) {

    class LightPost_admin
    {

        public $prefix = 'lpw';
        public $hook = 'lpw';
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
            $this->load_text_domain();

            // add script, style & page in admin
            add_filter("plugin_action_links_{$this->filename}", array(&$this, 'add_settings_link'));
            add_action('admin_menu', array(&$this, 'add_option_page'));
            add_action("admin_init", array(&$this, 'add_admin_styles'));
            add_action("admin_init", array(&$this, 'add_admin_scripts'));


            $this->version = $args['version'];
            $this->prefix = $args['prefix'];

            // declare section settings plugin
            $this->sections['general'] = __('General Settings', $this->hook);
            $this->sections['themes'] = __('Stylling & apperarance', $this->hook);
            $this->sections['style'] = __('Generator LightPost', $this->hook);

            register_activation_hook(__FILE__, array(&$this, 'initialize_settings'));

            // Add inline javascript
            add_action('admin_footer', array(&$this, 'inlineJs'), 10);

            $this->checkboxes = array();
            $this->settings = array();
            $this->get_settings();

            // Set up the settings.
            add_action('admin_init', array(&$this, 'register_settings'));

            // add meta box
            add_action('admin_init', array(&$this, 'add_meta_lightpost'));

            if (!get_option($this->prefix . '_options'))
                $this->initialize_settings();

        }


        /**
         * Initialize settings to their default values
         *
         * since 1.0
         */
        public function initialize_settings()
        {

            $default_settings = array();
            foreach ($this->settings as $id => $setting) {
                    $default_settings[$id] = $setting['std'];
            }
            update_option($this->prefix . '_options', $default_settings);
        }

        /**
         * Register and load textdomain
         *
         * since 1.0
         */
        public function load_text_domain()
        {

            load_plugin_textdomain($this->hook, null, plugin_basename(dirname(__FILE__)) . '/lang');
        }

        /**
         * Register settings
         *
         * since 1.0
         */
        public function register_settings()
        {

            register_setting($this->prefix . '_options', $this->prefix . '_options', array(&$this, 'validate_settings'));

            foreach ($this->sections as $slug => $title) {
                add_settings_section($slug, $title, array(&$this, 'display_section'), $this->prefix . '_options');
            }

            $this->get_settings();

            foreach ($this->settings as $id => $setting) {
                $setting['id'] = $id;
                $this->create_setting($setting);
            }


        }


        /**
         * Create settings field
         *
         * since 1.0
         */
        public function create_setting($args = array())
        {

            $defaults = array(
                'id' => 'default_field',
                'title' => __('Default Field'),
                'subtitle' => __('Default Field'),
                'desc' => '',
                'image' => __('Image URL'),
                'std' => '',
                'type' => 'text',
                'section' => 'general',
                'choices' => array(),
                'class' => '',
                'min' => '',
                'max' => '',
                'increment' => '',
                'data_demo' => '',
                'display' => ''
            );

            extract(wp_parse_args($args, $defaults));

            $field_args = array(
                'type' => $type,
                'section' => $section,
                'id' => $id,
                'title' => $title,
                'subtitle' => $subtitle,
                'desc' => $desc,
                'image' => $image,
                'std' => $std,
                'tab' => (isset($tab) ? $tab : null),
                'tablimit' => (isset($tablimit) ? $tablimit : null),
                'choices' => $choices,
                'label_for' => $id,
                'class' => $class,
                'min' => $min,
                'max' => $max,
                'increment' => $increment,
                'data_demo' => $data_demo,
                'display' => $display
            );

            if ($type == 'checkbox')
                $this->checkboxes[] = $id;

            add_settings_field($id, $title . ((isset($desc) && !empty($desc)) ? '<span class="tooltip lpw_helper" data-position="right center" data-content="' . __($desc, $this->hook) . '">?</span>' : ''), array($this, 'display_setting'), $this->prefix . '_options', $section, $field_args);
        }


        /**
         * Settings and defaults
         *
         * since 1.0
         */
        public function get_settings()
        {

            /* == General Settings == */
            /*===========================================*/

            $this->settings['use_link'] = array(
                'section' => 'general',
                'title' => __('Linking lightbox display at the click on the post link:', $this->hook),
                'desc' => __('Lightpost will be show on click in any link post, you can desable this if you want use shortcode to show your lightpost', $this->hook),
                'type' => 'slider',
                'std' => 'on',
                'class' => 'lpw_use_link',

            );


            $this->settings['hide_on'] = array(
                'section' => 'general',
                'title' => __('Hide on:', $this->hook),
                'type' => 'checkbox',
                'std' => 'true',
                'class' => 'lpw_select_language',
                'choices' => array(
                    'desktop' => 'Desktop',
                    'tablette' => 'Tablette',
                    'mobile' => 'Mobile'
                )
            );


            $postTypes = get_post_types();
            $choices = array();
            foreach ($postTypes as $key => $value) {
                if ($key != 'revision' && $key != 'nav_menu_item' && $key != 'attachment')
                    $choices[$key] = $value;
            }
            if(isset($choices['page']))  unset($choices['page']);

            $this->settings['enable_type_post'] = array(
                'section' => 'general',
                'title' => __('Enable LightPost for:', $this->hook),
                'desc' => __('Which type post you want show on Lightbox', $this->hook),
                'type' => 'checkbox',
                'std' => array('post' => 'post'),
                'choices' => $choices,
            );


            $postTypes = get_pages();
            $choices = array();
            $choices['home'] = __('Home page', $this->hook);
            $choices['search'] = __('Search page', $this->hook);
            $choices['category'] = __('Category page', $this->hook);
            $choices['archive'] = __('Archive page', $this->hook);
            $choices['single'] = __('Single page', $this->hook);
            $choices['tag'] = __('Tag page', $this->hook);
            $choices['page'] = __('Default page', $this->hook);

            $this->settings['enable_page'] = array(
                'section' => 'general',
                'title' => __('Enable LightPost on:', $this->hook),
                'desc' => __('In which page can appear', $this->hook),
                'type' => 'checkbox',
                'std' => array('home' => 'home', 'search' => 'search', 'category' => 'category', 'archive' => 'archive', 'single' => 'single', 'tag' => 'tag', 'page' => 'page'),
                'choices' => $choices,
            );


            $this->settings['language'] = array(
                'section' => 'general',
                'title' => __('Language:', $this->hook),
                'type' => 'select',
                'std' => 'en',
                'class' => 'lpw_select_language',
                'choices' => array(
                    'en' => 'Anglais',
                    'fr' => 'Français'
                )
            );

            /* == Themes Settings == */
            /*===========================================*/
            $this->settings['theme'] = array(
                'section' => 'themes',
                'title' => __('Themes:', $this->hook),
                'type' => 'theme',
                'std' => null,
                'choices' => array(
                    array(
                        'slug' => 'theme1',
                        'name' => __('Theme 1', $this->hook),
                        'img' => plugins_url('assets/img/theme1.jpg', dirname(__FILE__)),
                    ),
                    array(
                        'slug' => 'theme2',
                        'name' => __('Theme 2', $this->hook),
                        'img' => plugins_url('assets/img/theme2.jpg', dirname(__FILE__)),
                    )
                )
            );

            /* == Style Settings == */
            /*===========================================*/
            $this->settings['width'] = array(
                'section' => 'style',
                'title' => __('Width:', $this->hook),
                'type' => 'range',
                'tab' => __('Style', $this->hook),
                'min' => 0,
                'max' => 2200,
                'tablimit' => 'open',
                'std' => 600,
                'class' => 'lpw_width',
            );
            $this->settings['borderRadius'] = array(
                'section' => 'style',
                'title' => __('Border radius:', $this->hook),
                'type' => 'range',
                'tab' => __('Style', $this->hook),
                'min' => 0,
                'max' => 200,
                'std' => 4,
                'class' => 'lpw_borderRadius'
            );

            $this->settings['fontSize'] = array(
                'section' => 'style',
                'title' => __('Font size:', $this->hook),
                'type' => 'range',
                'tab' => __('Style', $this->hook),
                'min' => 0,
                'max' => 100,
                'std' => 14,
                'class' => 'lpw_fontSize'
            );


            $this->settings['horizentalPadding'] = array(
                'section' => 'style',
                'title' => __('Marge left & right:', $this->hook),
                'type' => 'range',
                'tab' => __('Style', $this->hook),
                'min' => 0,
                'max' => 100,
                'std' => 18,
                'class' => 'lpw_horizentalPadding'
            );
            $this->settings['overlayImage'] = array(
                'section' => 'style',
                'title' => __('Default overlay  image:', $this->hook),
                'type' => 'image',
                'tab' => __('Style', $this->hook),
                'std' => '',
                'class' => 'lpw_overlayImage'
            );



            $this->settings['colorTitle'] = array(
                'section' => 'style',
                'title' => __('Title color:', $this->hook),
                'type' => 'alphacolor',
                'tab' => __('Style', $this->hook),
                'std' => 'rgba(0,0,0, .9)',
                'class' => 'lpw_colorTitle'
            );

            $this->settings['colorSubTitle'] = array(
                'section' => 'style',
                'title' => __('Sub Title color:', $this->hook),
                'type' => 'alphacolor',
                'tab' => __('Style', $this->hook),
                'std' => 'rgba(0,0,0, .4)',
                'class' => 'lpw_colorSubTitle'
            );

            $this->settings['colorLink'] = array(
                'section' => 'style',
                'title' => __('Link color:', $this->hook),
                'type' => 'alphacolor',
                'tab' => __('Style', $this->hook),
                'std' => 'rgba(16,162,124,1)',
                'class' => 'lpw_colorLink'
            );

            $this->settings['colorText'] = array(
                'section' => 'style',
                'title' => __('Text color:', $this->hook),
                'type' => 'alphacolor',
                'tab' => __('Style', $this->hook),
                'std' => 'rgba(0,0,0, .9)',
                'class' => 'lpw_colorText'
            );

            $this->settings['colorBackground'] = array(
                'section' => 'style',
                'title' => __('Background Color:', $this->hook),
                'type' => 'alphacolor',
                'tab' => __('Style', $this->hook),
                'std' => 'rgba(255,255,255, 1)',
                'class' => 'lpw_colorBackground'
            );

            $this->settings['layerBackground'] = array(
                'section' => 'style',
                'title' => __('Overlay Color:', $this->hook),
                'type' => 'alphacolor',
                'tab' => __('Style', $this->hook),
                'std' => 'rgba(0,0,0, .6)',
                'class' => 'lpw_layerBackground'
            );


            $this->settings['blurValue'] = array(
                'section' => 'style',
                'title' => __('Blur:', $this->hook),
                'type' => 'range',
                'tab' => __('Style', $this->hook),
                'min' => 0,
                'max' => 50,
                'std' => 3,
                'class' => 'lpw_blurValue'
            );

            $this->settings['grayscaleValue'] = array(
                'section' => 'style',
                'title' => __('Grayscale:', $this->hook),
                'type' => 'range',
                'tab' => __('Style', $this->hook),
                'min' => 0,
                'max' => 100,
                'std' => 70,
                'class' => 'lpw_grayscaleValue'
            );

            $this->settings['boxShadow'] = array(
                'section' => 'style',
                'title' => __('Shadow width:', $this->hook),
                'type' => 'range',
                'tab' => __('Style', $this->hook),
                'min' => 0,
                'max' => 200,
                'std' => 4,
                'class' => 'lpw_boxShadow'
            );

            $this->settings['boxShadowColor'] = array(
                'section' => 'style',
                'title' => __('Shadow color:', $this->hook),
                'type' => 'alphacolor',
                'tab' => __('Style', $this->hook),
                'tablimit' => 'close',
                'std' => 'rgba(0,0,0, .4)',
                'class' => 'lpw_boxShadowColor'
            );


            $this->settings['animation'] = array(
                'section' => 'style',
                'title' => __('Animation name:', $this->hook),
                'desc' => __('Name of opening animation', $this->hook),
                'type' => 'select',
                'std' => 'fadeInDown',
                'tab' => __('Animation', $this->hook),
                'tablimit' => 'open',
                'class' => 'lpw_animationName',
                'choices' => array(
                    'bounce' => 'Bounce',
                    'swing' => 'Swing',
                    'tada' => 'Tada',
                    'jello' => 'Jello',
                    'fadeInDown' => 'FadeInDown',
                    'fadeInUp' => 'FadeInDown',
                    'pulse' => 'Pulse',
                    'flash' => 'Flash',
                    'rubberBand' => 'RubberBand',
                    'shake' => 'Shake',
                    'wobble' => 'wobble',
                    'bounceIn' => 'BounceIn',
                    'bounceInDown' => 'BounceInDown',
                    'bounceInLeft' => 'BounceInLeft',
                    'bounceInRight' => 'BounceInRight',
                    'bounceInUp' => 'BounceInUp',
                    'fadeIn' => 'fadeIn',
                    'fadeInDown' => 'FadeInDown',
                    'fadeInDownBig' => 'FadeInDownBig',
                    'fadeInUp' => 'FadeInUp',
                    'fadeInUpBig' => 'FadeInUpBig',
                    'fadeInRight' => 'FadeInRight',
                    'fadeInRightBig' => 'FadeInRightBig',
                    'fadeInLeft' => 'FadeInLeft',
                    'fadeInLeftBig' => 'FadeInLeftBig',
                    'flip' => 'Flip',
                    'flipInX' => 'FlipInX',
                    'flipInY' => 'FlipInY',
                    'lightSpeedIn' => 'lightSpeedIn',
                    'rotateIn' => 'rotateIn',
                    'rotateInDownLeft' => 'RotateInDownLeft',
                    'rotateInDownRight' => 'RotateInDownRight',
                    'rotateInUpLeft' => 'RotateInUpLeft',
                    'rotateInUpRight' => 'RotateInUpRight',
                    'slideInUp' => 'SlideInUp',
                    'slideInDown' => 'SlideInDown',
                    'slideInLeft' => 'SlideInLeft',
                    'slideInRight' => 'SlideInRight',
                    'zoomIn' => 'ZoomIn',
                    'zoomInUp' => 'ZoomInUp',
                    'zoomInDown' => 'ZoomInDown',
                    'zoomInLeft' => 'ZoomInLeft',
                    'zoomInRight' => 'ZoomInRight'
                )
            );

            $this->settings['openSpeed'] = array(
                'section' => 'style',
                'title' => __('Open speed:', $this->hook),
                'type' => 'number',
                'tab' => __('Animation', $this->hook),
                'std' => 1000,
                'class' => 'lpw_openSpeed'
            );

            $this->settings['animated'] = array(
                'section' => 'style',
                'title' => __('Animated:', $this->hook),
                'type' => 'toggle',
                'tab' => __('Animation', $this->hook),
                'tablimit' => 'close',
                'std' => 'on',
                'class' => 'lpw_animated'
            );

            $this->settings['showTitle'] = array(
                'section' => 'style',
                'title' => __('Show Title:', $this->hook),
                'type' => 'toggle',
                'std' => 'on',
                'tab' => __('Content', $this->hook),
                'tablimit' => 'open',
                'class' => 'lpw_showTitle'
            );

            $this->settings['showThumbAuthor'] = array(
                'section' => 'style',
                'title' => __('Show thumbnail author:', $this->hook),
                'type' => 'toggle',
                'std' => 'on',
                'tab' => __('Content', $this->hook),
                'class' => 'lpw_showThumbAuthor'
            );

            $this->settings['showAuthor'] = array(
                'section' => 'style',
                'title' => __('Show author name:', $this->hook),
                'type' => 'toggle',
                'tab' => __('Content', $this->hook),
                'std' => 'on',
                'class' => 'lpw_showAuthor'
            );

            $this->settings['showCategories'] = array(
                'section' => 'style',
                'title' => __('Show categories:', $this->hook),
                'type' => 'toggle',
                'std' => 'on',
                'tab' => __('Content', $this->hook),
                'class' => 'lpw_showCategories'
            );

            $this->settings['showDate'] = array(
                'section' => 'style',
                'title' => __('Show publish date:', $this->hook),
                'type' => 'toggle',
                'std' => 'on',
                'tab' => __('Content', $this->hook),
                'class' => 'lpw_showDate'
            );

            $this->settings['showMedia'] = array(
                'section' => 'style',
                'title' => __('Show media section:', $this->hook),
                'type' => 'toggle',
                'std' => 'on',
                'tab' => __('Content', $this->hook),
                'class' => 'lpw_showMedia'
            );

            $this->settings['showSocialShare'] = array(
                'section' => 'style',
                'title' => __('Show Social Share Buttons:', $this->hook),
                'type' => 'toggle',
                'std' => 'on',
                'tab' => __('Content', $this->hook),
                'class' => 'lpw_showSocialShare'
            );

            $this->settings['showContent'] = array(
                'section' => 'style',
                'title' => __('Show content section:', $this->hook),
                'type' => 'toggle',
                'std' => 'on',
                'tab' => __('Content', $this->hook),
                'tablimit' => 'close',
                'class' => 'lpw_showContent'
            );


            $this->settings['enableNavigation'] = array(
                'section' => 'style',
                'title' => __('Enable Navigation:', $this->hook),
                'desc' => __('next/ previous post', $this->hook),
                'type' => 'toggle',
                'tab' => __('Navigation', $this->hook),
                'tablimit' => 'open',
                'std' => 'on',
                'class' => 'lpw_enableNavigation'
            );


            $this->settings['showNavigationTitle'] = array(
                'section' => 'style',
                'title' => __('Title Navigation:', $this->hook),
                'desc' => __('Show Title next/previous post', $this->hook),
                'type' => 'toggle',
                'tab' => __('Navigation', $this->hook),
                'tablimit' => 'close',
                'std' => 'on',
                'class' => 'lpw_showNavigationTitle'
            );

            $this->settings['overlayClose'] = array(
                'section' => 'style',
                'title' => __('Overlay Close:', $this->hook),
                'desc' => __('Close lightPost on click overlay', $this->hook),
                'type' => 'toggle',
                'tab' => __('Advanced options', $this->hook),
                'tablimit' => 'open',
                'std' => 'on',
                'class' => 'lpw_overlayClose'
            );

            $this->settings['zIndex'] = array(
                'section' => 'style',
                'title' => __('Z-Index', $this->hook),
                'type' => 'number',
                'tab' => __('Advanced options', $this->hook),
                'tablimit' => 'close',
                'std' => 100000,
                'class' => 'lpw_zIndex'
            );

        }


        /**
         * Add a settings link to the plugin box
         *
         * since 1.0
         */
        function add_settings_link($links)
        {

            $settings_link = '<a href="options-general.php?page=' . $this->prefix . '_options' . '">Settings</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        /**
         * Add a link to the WordPress menu
         *
         * since 1.0
         */
        public function add_option_page()
        {

            add_options_page($this->longname, $this->shortname, 'manage_options', $this->prefix . '_options', array(&$this, 'display_admin_page'));
        }

        /**
         * Enqueue Dashboard CSS files
         *
         * since 1.0
         */
        public function add_admin_styles()
        {

            wp_enqueue_style($this->hook . '-admin', plugins_url('assets/css/admin_main.css', dirname(__FILE__)), array(), $this->version, 'screen');
            wp_enqueue_style($this->hook . '-front-css', plugins_url('assets/css/front_main.css', dirname(__FILE__)), array(), $this->version, 'screen');
            wp_enqueue_style($this->hook . 'alpha-color-picker', plugins_url('assets/js/alpha-color-picker/alpha-color-picker.css', dirname(__FILE__)), array('wp-color-picker') );
            wp_enqueue_style('wp-color-picker');
        }

        /**
         * Enqueue Dashboard JavaScript files
         *
         * since 1.0
         */
        public function add_admin_scripts()
        {

            wp_enqueue_script($this->hook . '-semantic-js', plugins_url('assets/js/semantic.min.js', dirname(__FILE__)), array('jquery'), '', false);
            wp_enqueue_script($this->hook . '-jquery-translate-js', plugins_url('assets/js/jquery.translate.js', dirname(__FILE__)), array('jquery'), '', false);
            wp_enqueue_script($this->hook . '-i18n', plugins_url('assets/js/i18n.js', dirname(__FILE__)), array(), '', false);
            wp_enqueue_script($this->hook . '-rangeslider', plugins_url('assets/js/rangeslider.min.js', dirname(__FILE__)), array('jquery'), '', false);
            wp_enqueue_script($this->hook . '-lightpost-js', plugins_url('assets/js/lightpost.js', dirname(__FILE__)), array('jquery'), '', false);
            wp_enqueue_script($this->hook . '-admin-js', plugins_url('assets/js/admin_main.js', dirname(__FILE__)), array('jquery', $this->hook . '-lightpost-js', 'alpha-color-picker'), '', 100);
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_script('alpha-color-picker', plugins_url('assets/js/alpha-color-picker/alpha-color-picker.js', dirname(__FILE__)), array('jquery', 'wp-color-picker'), null,true);
            if( is_admin() && isset($_GET['page']) && $_GET['page'] == $this->prefix .'_options')
                wp_enqueue_media();
        }


        /**
         * Setup & create the admin interface page
         *
         * since 1.0
         */
        public function setup_admin_page($title)
        {
            $options = get_option($this->prefix . '_options');
            ?>
            <div class="wrap">

                <h2><?php echo $title ?></h2>

                <p><?php echo sprintf(__('Use this page to setup and style the %s plugin.<br />Once you\'ve done that, you can use lightpost panel in  new post/page page\'s to choise the featured media of your post/page. <br> To show lightPost by shortcode just use this example: <strong>[lightpost] Your content [/lightpost]</strong>', $this->hook), $title); ?></p>

                <div class="postbox-container" style="width:100%; margin-right:10px;" id="lpw">
                    <div class="metabox-holder">
                        <div class="meta-box-sortables">
                            <form action="options.php" method="post" class="lpw-form ui tiny form"
                                  enctype="multipart/form-data">
                                <?php
        }

        /**
         * Close the admin page HTML
         *
         * since 1.0
         */
        public function close_admin_page() {
                                ?>
                                <p class="submit"><input name="Submit" type="submit" class="button-primary"
                                                         value="<?php echo __('Save Changes', $this->hook); ?>"/></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="postbox-container" style="width:30%;">
                <div class="metabox-holder">
                </div>
            </div>
            <?php
        }


        /**
         * Display options page
         *
         * since 1.0
         */
        public function display_admin_page()
        {
            $this->setup_admin_page('LightPost Wordpress');
            settings_fields($this->prefix . '_options'); ?>

            <div class="lwp-admin">
                <div class="lpw-tab-container">
                    <div class="ui top attached tabular menu">
                        <?php
                        foreach ($this->sections as $section_slug => $section) {
                            $class = '';
                            if ($section_slug == "general") $class = 'active';
                            if ($section_slug != "style") echo '<a class="item ' . $class . ' " data-tab="' . $section_slug . '">' . __($section, $this->hook) . '</a>';
                        }

                        ?>
                    </div>
                    <?php $this->custom_do_settings_sections($_GET['page']); ?>
                </div>
            </div>
            <?php
            $this->close_admin_page();

        }

        /**
         * HTML output for specific plugin settings
         *
         * since 1.0
         */
        public function display_setting($args = array())
        {

            extract($args);

            $defaults = array('id' => '', 'std' => '', 'class' => '', 'tab' => null, 'tablimit' => null, 'section' => '', 'title' => '', 'min' => 0, 'max' => 100);
            $args = wp_parse_args($args, $defaults);


            $id = $args['id'];
            $std = $args['std'];
            $class = $args['class'];
            $tab = $args['tab'];
            $tablimit = $args['tablimit'];
            $section = $args['section'];
            $title = $args['title'];
            $min = $args['min'];
            $max = $args['max'];

            $options = get_option($this->prefix . '_options');


            if (!isset($options[$id]) && $type != 'checkbox' && $type != 'toggle'  && $type != 'slider')
                $options[$id] = $std;
            elseif (!isset($options[$id]))
                $options[$id] = 0;

            $field_class = '';
            if ($class != '')
                $field_class = ' ' . $class;

            $output = '';


            switch ($type) {


                case 'heading':
                    $output .= '</td></tr><tr valign="top" class="' . $field_class . '"><td colspan="2"><h4>' . $desc . '</h4>';
                    break;

                case 'heading_img':
                    $output .= '</td></tr><tr valign="top"><td colspan="2"><span class="lpw_admimg">' . $image . '</span><h4>' . $desc . '</h4>';
                    break;

                case 'hr':
                    $output .= '</td></tr><tr valign="top"><td colspan="2"><hr />';
                    break;


                case 'slider':

                    if (!empty($choices)) {
                        $i = 0;
                        foreach ($choices as $value => $label) {

                            $output .= '<div class="field inline"><div class="ui slider checkbox "><input class="chekbox' . $field_class . '" type="checkbox" name="' . $this->prefix . '_options[' . $id . '][' . $value . ']" id="' . $id . $i . '" ' . ((isset($options[$id][$value]) && $options[$id][$value] == 'on') ? ' checked="checked"' : '') . '> <label for="' . $id . $i . '">' . $label . '</label></div><label></label></div>';

                            $i++;
                        }
                    } else {
                        $output .= '<div class="field inline"><div class="ui slider checkbox "><input class="chekbox' . $field_class . '" type="checkbox" name="' . $this->prefix . '_options[' . $id . ']" id="' . $id . '" ' . (($options[$id] != '0') ? 'checked="checked"' : '') . '> <label for="' . $id . '"></label></div><label></label></div>';

                    }

                    break;
                case 'toggle':
                    $output .= '<div class="field inline"><div class="ui toggle checkbox ' . (($options[$id] != '0') ? 'checked' : '') . '"><input class="chekbox' . $field_class . '" type="checkbox" name="' . $this->prefix . '_options[' . $id . ']" id="' . $id . '" ' . (($options[$id] != '0') ? 'checked="checked"' : '') . '> <label for="' . $id . '"></label></div><label></label></div>';
                    break;


                case 'checkbox':
                    if (!empty($choices)) {
                        $i = 0;
                        foreach ($choices as $value => $label) {
                            if (isset($options[$id])) {
                                if (isset($options[$id][$value]))
                                    $checked = checked($options[$id][$value], $value, false);
                                else $checked = '';
                            } elseif (is_array($std) && isset($std[$value])) $checked = checked($std[$value], $value, false);
                            else $checked = '';
                            $output .= '<div class="field inline"><div class="ui  checkbox"><input class="chekbox' . $field_class . '" type="checkbox" name="' . $this->prefix . '_options[' . $id . '][' . $value . ']" id="' . $id . $i . '" value="' . esc_attr($value) . '" ' . $checked . '> <label for="' . $id . $i . '">' . $label . '</label></div></div>';

                            $i++;
                        }
                    } else {
                        $output .= '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="' . $this->prefix . '_options[' . $id . ']" value="1" ' . checked($options[$id], 1, false) . ' /> <label for="' . $id . '">' . $desc . '</label>';
                    }

                    break;

                case 'theme':
                    $i = 0;
                    $options = get_option($this->prefix . '_options');
                    foreach ($choices as $choice) {
                        $class = '';
                        if (isset($options['theme']) && $options['theme'] == $choice['slug']) $class = ' active';
                        $output .= '<div class="box_theme ' . $class . '"> ';
                        $output .= '<img src="' . $choice['img'] . '" /> ';
                        $output .= '<div class="overlay">';
                        $output .= '<div class="title">' . $choice['name'] . '</div>';
                        $output .= '<div class="action">';
                        if ( isset($options['theme'])  && $options['theme'] == $choice['slug'] ) {
                            $output .= '<div class="ui button  show_active_lightpost" data-theme="' . $choice['slug'] . '">' . __('Show', $this->hook) . '</div>';
                            $output .= '<div class="ui button  edit_lightpost" data-theme="' . $choice['slug'] . '">' . __('Customize', $this->hook) . '</div>';
                        } else {
                            $output .= '<div class="ui button  show_theme" data-theme="' . $choice['slug'] . '">' . __('Show', $this->hook) . '</div>';
                            $output .= '<div class="ui button  open_customizer" data-theme="' . $choice['slug'] . '">' . __('Use model', $this->hook) . '</div>';
                        }
                        $i++;
                        if (isset($options[$id]))
                            $output .= '<input class="radio" type="radio" name="' . $this->prefix . '_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr($choice['slug']) . '" ' . checked($options[$id], $choice['slug'], false) . '> ';
                        else                         $output .= '<input class="radio" type="radio" name="' . $this->prefix . '_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr($choice['slug']) . '" > ';
                        $output .= '</div>';
                        $output .= '</div>';
                        $output .= '</div>';
                    }
                    break;

                case 'fancycheckbox':

                    $hiddendiv = explode("_", $id);

                    $output .= '<div class="lpw-switch-link" id="ch_' . $id . '" data-for="#' . $hiddendiv[0] . '">';
                    $output .= '<a href="#" rel="true" class="link-true ';
                    $output .= esc_attr($options[$id]) == 'true' ? 'active' : '';
                    $output .= '"></a>';
                    $output .= '<a href="#" rel="false" class="link-false ';
                    $output .= esc_attr($options[$id]) == 'false' ? 'active' : '';
                    $output .= '"></a></div>';

                    $output .= '<input id="' . $id . '" name="' . $this->prefix . '_options[' . $id . ']" class="plugin-switch-value" type="hidden" value="' . esc_attr($options[$id]) . '" />';

                    break;

                case 'select':
                    $output .= '<div class="field inline "><select class=" ui dropdown' . $field_class . '" name="' . $this->prefix . '_options[' . $id . ']" id="' . $this->prefix . '_options[' . $id . ']">';

                    foreach ($choices as $value => $label)
                        $output .= '<option id="' . esc_attr($value) . '" value="' . esc_attr($value) . '"' . selected($options[$id], $value, false) . '>' . $label . '</option>';

                    $output .= '</select></div>';

                    break;

                case 'radio':
                    $i = 0;
                    foreach ($choices as $value => $label) {
                        $output .= '<input class="radio' . $field_class . '" type="radio" name="' . $this->prefix . '_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr($value) . '" ' . checked($options[$id], $value, false) . '> <label for="' . $id . $i . '">' . $label . '</label>';
                        if ($i < count($options) - 1)
                            $output .= '<br />';
                        $i++;
                    }

                    break;

                case 'textarea':
                    $output .= '<textarea class="' . $field_class . '" id="' . $id . '" name="' . $this->prefix . '_options[' . $id . ']" placeholder="' . $std . '" rows="5" cols="55">' . format_for_editor($options[$id]) . '</textarea>';


                    break;

                case 'color':
                    $output .= '<div class="field"><label>' . $subtitle . '</label>';
                    $output .= '<input id="picker_' . $id . '" class="color-selector ' . $field_class . '" name="' . $this->prefix . '_options[' . $id . ']" type="text" value="' . esc_attr($options[$id]) . '" data-demo=' . esc_attr($data_demo) . ' />';
                    $output .= '</div>';
                    break;

                case 'alphacolor':
                    $output .= '<div class="field field_' . $id . '" >';
                    $output .= '<input class="alpha-color-picker-submit " type="button" value="' . __('Change', $this->hook) . '"  />';
                    $output .= '<input id="picker_' . $id . '" class="alpha-color-picker ' . $field_class . '" name="' . $this->prefix . '_options[' . $id . ']" type="text" value="' . esc_attr($options[$id]) . '" data-demo=' . esc_attr($data_demo) . ' />';
                    $output .= '</div>';

                    break;

                case 'range':
                    $output .= '<div id="' . $id . '" class="field ui range ' . $field_class . '" data-min="' . $min . '"  data-max="' . $max . '" data-start="' . esc_attr($options[$id]) . '"></div>';
                    $output .= '<input class="regular-text' . $field_class . '" type="hidden"  name="' . $this->prefix . '_options[' . $id . ']" value="' . esc_attr($options[$id]) . '" />';
                    break;

                case 'color2':
                    $output .= '<div class="inline-header">' . $subtitle . '</div><div class="description">' . $desc . '</div>';
                    $output .= '<input id="picker_' . $id . '" class="color-selector ' . $field_class . '" name="' . $this->prefix . '_options[' . $id . ']" type="text" value="' . esc_attr($options[$id]) . '" data-demo=' . esc_attr($data_demo) . ' />';

                    break;

                case 'number':
                    $output .= '<div class="field"><div class="ui input"><input class="regular-text' . $field_class . '" type="number" id="' . $id . '" name="' . $this->prefix . '_options[' . $id . ']" value="' . esc_attr($options[$id]) . '" /></div></div>';
                    break;

                case 'image':
                    $imgid = (isset($options[$id])) ? $options[$id] : "";
                    $img = wp_get_attachment_image_src($imgid, 'thumbnail');
                    $output .= '<div class="field field_image">';

                    $output .= '<div class="field_thumb ' . (($img != "") ? 'show' : 'hide') . '"> <img src="' . $img[0] . '" />';
                    $output .= '<div class="ui input "><input type="number" value="' . esc_attr($options[$id]) . '" class="regular-text process_custom_images' . $field_class . '" id="' . $id . '" name="' . $this->prefix . '_options[' . $id . ']" max="" min="1" step="1">';
                    $output .= ' <button class="set_custom_images button">' . __('Edit', $this->hook) . '</button>';
                    $output .= ' <button class="remove_custom_images button">' . __('Remove', $this->hook) . '</button></div> </div>';
                    $output .= '<div class="ui input add_img ' . (($img != "") ? 'hide' : 'show') . '"><input type="number" value="' . esc_attr($options[$id]) . '" class="regular-text process_custom_images' . $field_class . '" id="' . $id . '" name="' . $this->prefix . '_options[' . $id . ']" max="" min="1" step="1">            <button class="set_custom_images button">' . __('Set image', $this->hook) . '</button></div>';
                    $output .= '</div>';
                    break;

                case 'text':

                default:
                    $output .= '<div class="ui input"><input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="' . $this->prefix . '_options[' . $id . ']" value="' . esc_attr($options[$id]) . '" /></div>';


                    break;
            }


            if ($tablimit != null && $tablimit == 'open') {

                echo '<div class="title">' .
                    $tab
                    . ' <i class="dropdown icon"></i></div>
                              <div class="content"><p>';
            }
            if ($section == "style") {
                echo '<label class="field_label" for="' . $id . '">' . $title;
                echo((isset($desc) && !empty($desc)) ? '<span class="tooltip lpw_helper" data-position="right center" data-content="' . __($desc, $this->hook) . '">?</span>' : '');
                echo '</label>';
            }
            echo $output;
            if ($tablimit != null && $tablimit == 'close') echo '</p></div>';


        }

        /**
         * Description for section
         *
         * since 1.0
         */
        public function display_section()
        {
            // Section description
        }

        /**
         * Validate settings
         *
         * since 1.0
         */
        public function validate_settings($input)
        {

            $options = get_option($this->prefix . '_options');

            foreach ($this->checkboxes as $id) {
                if (isset($options[$id]) && !isset($input[$id]))
                    unset($options[$id]);
            }

            return $input;

            return false;
        }

        /**
         * Parse content sections
         *
         * since 1.0
         */
        public function custom_do_settings_sections($page)
        {
            global $wp_settings_sections, $wp_settings_fields;

            if (!isset($wp_settings_sections) || !isset($wp_settings_sections[$page]))
                return;

            foreach ((array)$wp_settings_sections[$page] as $section) {


                if ($section['id'] == 'general') $class = 'active';
                else $class = '';


                call_user_func($section['callback'], $section);
                if (!isset($wp_settings_fields) ||
                    !isset($wp_settings_fields[$page]) ||
                    !isset($wp_settings_fields[$page][$section['id']])
                )
                    continue;
                if ($section['id'] == 'style') {
                    call_user_func($section['callback'], $section);
                    $this->display_customizer($page, $section);

                } else {

                    echo "<div class='ui bottom attached tab segment " . $class . "' data-tab='" . $section['id'] . "'>";
                    do_settings_fields($page, $section['id']);
                    echo '</div>';
                }

            }
        }

        /**
         * Create HTml content for customizer
         *
         * since 1.0
         */
        function display_customizer($page, $section)
        {
            ?>
            <div class="lpw_customizer show">
                <div class="lpw_customizer_sidebar show">
                    <div class="lpw_sidebar_container">
                        <div class="ui accordion">

                            <?php

                            do_settings_fields($page, $section['id']);

                            ?>
                        </div>
                        <div class="panel_responsive">
                            <div class="viewport_bttn">
                                <div class="mobile">
                                    <i class="icon-smartphone"></i>
                                </div>
                                <div class="mobile portrait">
                                    <i class="icon-smartphone"></i>
                                </div>
                                <div class="tablette">
                                    <i class="icon-ipad"></i>
                                </div>
                                <div class="tablette portrait">
                                    <i class="icon-ipad"></i>
                                </div>
                                <div class="desktop">
                                    <i class="icon-desktop"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lpw_footer">
                        <div class="action toggle_bttn">
                            <i class="icon-left"></i>
                            <i class="icon-right"></i>
                        </div>
                        <div class=" action responsive_bttn tooltip"
                             data-content="<?php echo __('Responsive', $this->hook); ?>">
                            <i class="icon-desktop"></i>
                        </div>

                        <div class="action save_bttn tooltip" data-content="<?php echo __('Save', $this->hook); ?>">
                            <button type="submit">
                                <i class="icon-save"></i>
                            </button>
                        </div>
                        <div class="action cancel_bttn tooltip" data-content="<?php echo __('Cancel', $this->hook); ?>">
                            <i class="icon-cancel"></i>
                        </div>
                    </div>
                </div>
                <div class="lpw_customizer_container">
                    <div class="lpw_customizer_body">
                        <iframe src="" data-home="<?php echo home_url(); ?>" class="lpw_iframe_layer"></iframe>
                    </div>
                </div>
            </div>
            <?php


        }

        /**
         * Create inline Javascript for admin lightpost
         *
         * since 1.0
         */
        public function inlineJs()
        {
            $options = get_option($this->prefix . '_options');
            ?>
            <script type="text/javascript">
                <?php
                            $imgid =(isset( $options[ 'overlayImage' ] )) ? $options[ 'overlayImage' ] : "";
                            $img    = wp_get_attachment_image_src($imgid, 'full');
                            $overlayImage = '';
                            if($img != "") $overlayImage = $img[0];
                            $output = '(function ($) {
                                            $.currentLightPost = $.lightPostPopup();
                                            var options = {';
                                                $output .= "url: '".plugins_url()."/lightpost-wordpress/includes/dummy.php',";
                                                $output .= (isset($options["language"])) ? "language: '" . $options["language"] . "'," : "";
                                                $output .= (isset($options["theme"])) ? "theme: '" . $options["theme"] . "'," : "";
                                                $output .= (isset($options["width"])) ? "width: " . $options["width"] . "," : "";
                                                $output .= (isset($options["borderRadius"])) ? "borderRadius: " . $options["borderRadius"] . "," : "";
                                                $output .= (isset($options["fontSize"])) ? "fontSize: " . $options["fontSize"] . "," : "";
                                                $output .= (isset($options["horizentalPadding"])) ? "horizentalPadding: " . $options["horizentalPadding"] . "," : "";
                                                $output .= (isset($options["colorTitle"])) ? "colorTitle: '" . $options["colorTitle"] . "'," : "";
                                                $output .= (isset($options["colorSubTitle"])) ? "colorSubTitle: '" . $options["colorSubTitle"] . "'," : "";
                                                $output .= (isset($options["colorLink"])) ? "colorLink: '" . $options["colorLink"] . "'," : "";
                                                $output .= (isset($options["colorText"])) ? "colorText: '" . $options["colorText"] . "'," : "";
                                                $output .= (isset($options["colorBackground"])) ? "colorBackground: '" . $options["colorBackground"] . "'," : "";
                                                $output .= (isset($options["layerBackground"])) ? "layerBackground: '" . $options["layerBackground"] . "'," : "";
                                                $output .= (!empty($overlayImage)) ? "overlayImage: '" . $overlayImage . "'," : "";
                                                $output .= (isset($options["blurValue"])) ? "blurValue: " . $options["blurValue"] . "," : "";
                                                $output .= (isset($options["grayscaleValue"])) ? "grayscaleValue: " . $options["grayscaleValue"] . "," : "";
                                                $output .= (isset($options["enableNavigation"])) ? "enableNavigation: '" . $options["enableNavigation"] . "'," : "";
                                                $output .= (isset($options["showNavigationTitle"]) && count($options["showNavigationTitle"]) > 0 ) ? "showNavigationTitle: true," : "showNavigationTitle: false,";
                                                $output .= (isset($options["zIndex"])) ? "zIndex: '" . $options["zIndex"] . "'," : "";
                                                $output .= (isset($options["overlayClose"])  && count($options["overlayClose"]) > 0  ) ? "overlayClose: true," : "overlayClose: false,";
                                                $output .= (isset($options["showComment"])) ? "showComment: " . $options["showComment"] . "," : "";
                                                $output .= (isset($options["animated"]) && count($options["animated"]) > 0 ) ? "animated: true," : "animated: false," ;
                                                $output .= (isset($options["animation"])) ? "animation: '" . $options["animation"] . "'," : "";
                                                $output .= (isset($options["openSpeed"])) ? "speed: '" . $options["openSpeed"] . "'," : "";
                                                $output .= (isset($options["boxShadow"])) ? "boxShadow: " . $options["boxShadow"] . "," : "";
                                                $output .= (isset($options["boxShadowColor"])) ? "boxShadowColor: '" . $options["boxShadowColor"] . "'," : "";
                                                $output .= (isset($options["hide_on"]) && count($options["hide_on"]) > 0 && isset($options["hide_on"]["desktop"])) ? "hide_desktop: true," : "";
                                                $output .= (isset($options["hide_on"]) && count($options["hide_on"]) > 0 && isset($options["hide_on"]["tablette"])) ? "hide_tablette: true," : "";
                                                $output .= (isset($options["hide_on"]) && count($options["hide_on"]) > 0 && isset($options["hide_on"]["mobile"])) ? "hide_mobile: true," : "";
                                                $output .= (isset($options["showTitle"]) && $options["showTitle"] == 'on' ) ? "showTitle: true," : "showTitle: false,";
                                                $output .= (isset($options["showThumbAuthor"])  && $options["showThumbAuthor"] == 'on' ) ? "showThumbAuthor: true," : "showThumbAuthor: false,";
                                                $output .= (isset($options["showAuthor"])  && $options["showAuthor"] == 'on'   ) ? "showAuthor: true," : "showAuthor: false,";
                                                $output .= (isset($options["showCategories"])  && $options["showCategories"] == 'on' ) ? "showCategories: true," : "showCategories: false,";
                                                $output .= (isset($options["showDate"]) && $options["showDate"] == 'on'  ) ?  "showDate: true," : "showDate: false,";
                                                $output .= (isset($options["showMedia"]) && $options["showMedia"] == 'on'  ) ?  "showMedia: true," : "showMedia: false,";
                                                $output .= (isset($options["showContent"]) && $options["showContent"] == 'on'  ) ?  "showContent: true," : "showContent: false,";
                                                $output .= (isset($options["showSocialShare"]) && $options["showSocialShare"] == 'on'  ) ?  "showSocialShare: true," : "showSocialShare: false";
                                                $output .= '};';

                                                $output .= ' $.currentLightPost.update(options); ';


                            $output .= "$('body').on('click', '.edit_lightpost', function(){

                                            var theme = $(this).attr('data-theme');
                                            $('input[name=\"lpw_options[theme]\"]').attr('checked', false)
                                            $('input[value='+ theme +']').attr('checked', true);
                                            $('#wpcontent').css('zIndex', 9999);
                                            $('#wpadminbar').css('display', 'none');
                                            $('#wpfooter').css('display', 'none');
                                            $('.lpw_customizer').css('visibility', 'visible').css('opacity', 1);
                                            $('.lpw_customizer .lpw_iframe_layer').attr('src', $('.lpw_customizer .lpw_iframe_layer').attr('data-home'));
                                            $('.lpw_customizer .lpw_iframe_layer').on('load', function(){
                                                $('.lpw_customizer').addClass('show');
                                                $.currentLightPost.update({root: '.lpw_customizer_body'});
                                                $.currentLightPost.openDemo();
                                                var optionsInst =  $.currentLightPost.options;
                                                setTimeout(function(){
                                                if(!optionsInst.showTitle)  $('.npl_layer_scrolling .npl_header_title').addClass('hide');
                                                if(!optionsInst.showAuthor) $('.npl_layer_scrolling .npl_author_name').addClass('hide');
                                                if(!optionsInst.showThumbAuthor) $('.npl_layer_scrolling .npl_thumb_user.big').addClass('hide');
                                                if(!optionsInst.showCategories) $('.npl_layer_scrolling .npl_categories').addClass('hide');
                                                if(!optionsInst.showDate) $('.npl_layer_scrolling .npl_postdate').addClass('hide');
                                                if(!optionsInst.showMedia) $('.npl_layer_scrolling .npl_media_section').addClass('hide');
                                                if(!optionsInst.showContent) $('.npl_layer_scrolling .npl_body_section').addClass('hide');
                                                if(!optionsInst.showSocialShare) $('.npl_layer_scrolling .npl_body_action').addClass('hide');
                                                if(!optionsInst.enableNavigation) $(' .npl_navigation').css('display', 'none');
                                                if(!optionsInst.showNavigationTitle) $('.npl_layer .npl_previous_title, .npl_layer .npl_next_title').addClass('hide');
                                                if(!optionsInst.showAuthor && !optionsInst.showAuthorThumb && !optionsInst.showTitle && !optionsInst.showCategories && !optionsInst.showDate && !optionsInst.showContent && !optionsInst.npl_body_action) $('.npl_header_section').addClass('hide');
                                                }, 100);

                                            });



                                        });";
                                    $output .= " $('.show_active_lightpost').on('click', function(){

                                        $('#wpcontent').css('zIndex', 9999);
                                        $('#wpadminbar').css('display', 'none');
                                        $('#wpfooter').css('display', 'none');
                                        $.currentLightPost.open();
                                        function onClose(){
                                            $('#wpcontent').css('zIndex', 1);
                                            $('#wpadminbar').css('display', 'block');
                                            $('#wpfooter').css('display', 'block');
                                        }
                                        $.currentLightPost.onClose(onClose);

                                    });";

                                    $output .= "$('.open_customizer').on('click', function(){
                                                    var theme = $(this).attr('data-theme');
                                                    $('input[name=\"lpw_options[theme]\"]').attr('checked', false)
                                                    $('input[value='+ theme +']').attr('checked', true);
                                                    $('#wpcontent').css('zIndex', 9999);
                                                    $('#wpadminbar').css('display', 'none');
                                                    $('#wpfooter').css('display', 'none');
                                                    $('.lpw_customizer').css('visibility', 'visible').css('opacity', 1);
                                                    $('.lpw_customizer  .lpw_iframe_layer').attr('src', $('.lpw_customizer .lpw_iframe_layer').attr('data-home'));
                                                    $('.lpw_customizer  .lpw_iframe_layer').on('load', function(){
                                                    $('.lpw_customizer').addClass('show');
                                                        $.currentLightPost.update({root: '.lpw_customizer_body', theme: theme, url: '".plugins_url()."/lightpost-wordpress/includes/dummy.php' ". ((isset($options["width"])) ? ",width: " . $options["width"]  : "") ."});
                                                         $.currentLightPost.openDemo();
                                                         })
                                                });";

                                    $output .= "$('.show_theme').on('click', function(){
                                                    var theme = $(this).attr('data-theme');
                                                    $('#wpcontent').css('zIndex', 9999);
                                                    $('#wpadminbar').css('display', 'none');
                                                    $('#wpfooter').css('display', 'none');
                                                    $.currentLightPost.update({theme: theme, url: '".plugins_url()."/lightpost-wordpress/includes/dummy.php'});
                                                    $.currentLightPost.open();

                                                    function onClose(){
                                                        $('#wpcontent').css('zIndex', 1);
                                                        $('#wpadminbar').css('display', 'block');
                                                        $('#wpfooter').css('display', 'block');
                                                    }
                                                    $.currentLightPost.onClose(onClose);
                                                });";


                                $output .= "})(jQuery);
                                        ";

                                echo $output;
                            ?>
            </script>
            <?php
        }



        /**
         * Meta Box for New Post Page
         *
         * since 1.0
         */
        function settings_metabox()
        {
            $options = get_option($this->prefix . '_options');
            $meta_box = array(
                'id' => 'featured_media',
                'title' => __('LightPost', $this->hook),
                'pages' =>  $options['enable_type_post'], // multiple post types, accept custom post types
                'context' => 'side',
                'priority' => 'default',
            );


            foreach ($meta_box['pages'] as $page) {
                add_meta_box($meta_box['id'], $meta_box['title'], array($this, 'show_metabox'), $page, $meta_box['context'], $meta_box['priority']);
            }
        }

        /**
         * Display Meta Box for New Post Page
         *
         * since 1.0
         */
        function show_metabox($post)
        {
            $options = get_option($this->prefix . '_options');
            $type = get_post_meta($post->ID, 'featured_media_type', true);
            $code = get_post_meta($post->ID, 'featured_media_code', true);

            $output =  '<label for="type_media"> ' . __('Type Media', $this->hook) . ': </label>';
            $output .=  '<select name="type_media" id="type_media">';
            $output .=  '<option value="image" ' . (($type == "image") ? "selected" : "") . '>' . __('Featured Image', $this->hook) . '</option>';
            $output .=  '<option value="embed" ' . (($type == "embed") ? "selected" : "") . '>' . __('Embed Code', $this->hook) . '</option>';
            $output .=  '</select><br>';
            $output .=  '<div class="field_code_embed ' . (($type == "embed") ? "show" : "") . '">';
            $output .=  '<label for="code_embed"> ' . __('Code Embed', $this->hook) . ': </label><br>';
            $output .=  '<textarea id="code_embed" type="text" name="code_embed" placeholder="' . __('Add embed code here', $this->hook) . '">' . $code . '</textarea>';
            $output .=  '</div>';

            $overlayImage = get_post_meta($post->ID, 'overlayImage', true);
            $img = wp_get_attachment_image_src($overlayImage, 'thumbnail');
            $output .= '<div class="field field_image">';
            $output .= '<label>' . __('Image background', $this->hook) . '</label><br>';
            $output .= '<div class="field_thumb ' . (($img != "") ? 'show' : 'hide') . '"> <img src="' . $img[0] . '" />';
            $output .= '<div class="ui input "><input type="number" value="' .$overlayImage . '" class="regular-text process_custom_images overlayImage" id="overlayImage" name="overlayImage" max="" min="1" step="1">';
            $output .= ' <button class="set_custom_images button">' . __('Edit', $this->hook) . '</button>';
            $output .= ' <button class="remove_custom_images button">' . __('Remove', $this->hook) . '</button></div> </div>';
            $output .= '<div class="ui input add_img ' . (($img != "") ? 'hide' : 'show') . '"><input type="number" value="' . $overlayImage . '" class="regular-text process_custom_images overlayImage" id="overlayImage" name="overlayImage" max="" min="1" step="1"> <button class="set_custom_images button">' . __('Set image', $this->hook) . '</button></div>';
            $output .= '</div>';
            if(isset($post->ID))
                $output .= '<br><div>' . __('External Link: ', $this->hook) . '<strong><a href="'.  home_url(). '?llpw='.$post->ID.'">'.  home_url(). '?llpw='.$post->ID.'</a></strong></div>';
            echo $output;
            ?>
            <style>
                 .field.field_image {
                    min-height: 30px;
                     margin-top: 18px;
                }

                 .field.field_image .hide {
                     display: none !important;
                 }
                 .field.field_image .show {
                     display: block !important;
                 }

                 .field.field_image input {
                    display: none;
                }

                 .field.field_image .input.add_img {
                     position: static;
                 }

                 .field.field_image .input {
                    position: absolute;
                    top: 0;
                }

                 .field_thumb {
                    width: 100%;
                    position: relative;
                    height: 102px;
                    overflow: hidden;
                    border: 2px solid rgba(0, 0, 0, 0.15);
                    border-radius: 4px;
                }

                 .field_thumb img {
                    position: absolute;
                    top: 0;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    width: 100%;
                }
            </style>
            <script type="text/javascript">
                (function ($) {
                    $('#featured_media #type_media').on('change', function () {

                        var val = $(this).val();
                        if (val == 'embed') $('#featured_media .field_code_embed').show();
                        else $('#featured_media .field_code_embed').hide();
                    });
                })(jQuery);

            </script>
            <?php
        }

        /**
         * function to save data meta box
         *
         * since 1.0
         */
        function save_metaboxes($post_ID)
        {


            // si la metabox est définie, on sauvegarde sa valeur
            if (isset($_POST['type_media'])) {
                update_post_meta($post_ID, 'featured_media_type', esc_html($_POST['type_media']));
            }
            if (isset($_POST['code_embed'])) {
                update_post_meta($post_ID, 'featured_media_code', esc_html($_POST['code_embed']));
            }

            if (isset($_POST['overlayImage'])) {
                update_post_meta($post_ID, 'overlayImage', esc_html($_POST['overlayImage']));
            }
        }

        /**
         * Add Meta Box of LightPost
         *
         * since 1.0
         */
        function add_meta_lightpost()
        {
            $options = get_option($this->prefix . '_options');
            if(!isset($options['enable_type_post']) || empty($options['enable_type_post'])) return;
            add_action('add_meta_boxes', array($this, 'settings_metabox'));
            add_action('save_post', array($this, 'save_metaboxes'));
        }


    }
}

