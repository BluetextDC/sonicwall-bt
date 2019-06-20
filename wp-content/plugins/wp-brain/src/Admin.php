<?php

namespace WPBrain;

use WPBrain\Blocks\BlocksVisibility;
use WPBrain\Elementor\ElementorVisibility;
use WPBrain\Filters\FiltersManager;
use WPBrain\Location\LocationManager;
use WPBrain\Menus\MenusVisibility;
use WPBrain\Presets\PresetsManager;
use WPBrain\Rules\RulesBuilder;
use WPBrain\Rules\RulesValidator;
use WPBrain\Shortcodes\ShortcodesManager;
use WPBrain\Widgets\WidgetsVisibility;

/**
 * Backend Controller
 */
class Admin extends Utils
{
    use Base;

    /**
     * @var \WPBrain\Filters\FiltersManager
     */
    private $FiltersManager;

    /**
     * @var \WPBrain\Presets\PresetsManager
     */
    private $PresetsManager;

    /**
     * @var \WPBrain\Widgets\WidgetsVisibility
     */
    private $LocationManager;

    /**
     * @var \WPBrain\Shortcodes\ShortcodesManager
     */
    private $ShortcodesManager;

    /**
     * @var \WPBrain\Rules\RulesBuilder
     */
    private $RulesBuilder;

    /**
     * @var \WPBrain\Rules\RulesValidator
     */
    private $RulesValidator;

    /**
     * @var \WPBrain\Blocks\BlocksVisibility
     */
    private $BlocksVisibility;

    /**
     * @var \WPBrain\Elementor\ElementorVisibility
     */
    private $ElementorVisibility;

    /**
     * @var \WPBrain\Widgets\WidgetsVisibility
     */
    private $WidgetsVisibility;

    /**
     * @var \WPBrain\Menus\MenusVisibility
     */
    private $MenusVisibility;

    /**
     * @var \WPBrain\SmartObject|null
     */
    private $options = NULL;

    /**
     * @var string
     */
    private $main_page_slug = 'wp-brain';

    /**
     * @var string
     */
    private $presets_page_slug = 'wp-brain';

    /**
     * @var string
     */
    private $filters_page_slug = 'wp-brain-filters';

    /**
     * @var string
     */
    private $settings_page_slug = 'wp-brain-settings';

    /**
     * @var string
     */
    private $about_page_slug = 'wp-brain-about';

    /**
     * @var string
     */
    private $presets_hookname = '';

    /**
     * @var string
     */
    private $filters_hookname = '';

    /**
     * @var string
     */
    private $settings_hookname = '';

    /**
     * @var string
     */
    private $about_hookname = '';

    private function __construct()
    {
        register_activation_hook(WPBRAIN_BASE, $this->cb('plugin_activated'));

        $this->options = self::get_options();

        $this->add_action('plugins_loaded');
        $this->add_action('admin_init');
        $this->add_action('admin_menu');

        $this->add_action('admin_notices');
        $this->add_action('admin_enqueue_scripts');
        $this->add_action('admin_body_class');
        $this->add_filter('plugin_action_links', 10, 2);

        $this->add_action('admin_post_wpbrain_settings_save_presets');
        $this->add_action('admin_post_wpbrain_settings_save_filters');
        $this->add_action('admin_post_wpbrain_settings_save_options');
    }

    public function plugin_activated()
    {
        // Store the new version
        $db_version = get_option('wp_brain_version', '1.0.0');

        if ($db_version != WPBRAIN_VERSION) {
            update_option('wp_brain_version', WPBRAIN_VERSION);
        }

        // Download GeoLite2 database is file doesn't exists
        $mmdb_file = WPBRAIN_DIR . 'data/GeoLite2-City.mmdb';

        if (!file_exists($mmdb_file)) {
            set_time_limit(0);
            wp_remote_get('https://www.wpbrain.com/GeoLite2/GeoLite2-City.mmdb', [
                'sslverify' => false,
                'stream' => true,
                'filename' => $mmdb_file,
                'timeout' => 300,
            ]);
        }
    }

    public function plugins_loaded()
    {
        $this->setModule('options', $this->options);
        $this->setModule('filters', new FiltersManager());
        $this->setModule('presets', new PresetsManager());
        $this->setModule('location', new LocationManager());
        $this->setModule('shortcodes', new ShortcodesManager());
        $this->setModule('builder', new RulesBuilder());
        $this->setModule('validator', new RulesValidator());
        $this->setModule('blocks', new BlocksVisibility());

        if ($this->options->elementor_addon_enabled && $this->is_elementor_active()) {
            $this->setModule('elementor', new ElementorVisibility());
        }
        if ($this->options->widgets_visibility_enabled) {
            $this->setModule('widgets', new WidgetsVisibility());
        }
        if ($this->options->menus_visibility_enabled) {
            $this->setModule('menus', new MenusVisibility());
        }
    }

    /**
     * @param string $message
     * @param string $type
     */
    public function set_settings_message($message, $type = "updated")
    {
        $settings_errors = [
            [
                'setting' => 'wp-brain',
                'code' => 'wp-brain',
                'message' => $message,
                'type' => $type,
            ],
        ];
        set_transient('settings_errors', $settings_errors, 300);
    }

    public function admin_notices()
    {
        global $current_screen;

        $valid_screens = [
            $this->presets_hookname,
            $this->filters_hookname,
            $this->settings_hookname,
        ];

        if (in_array($current_screen->id, $valid_screens)) {
            settings_errors('wp-brain');
        }
    }

    public function admin_post_wpbrain_settings_save_presets()
    {
        // User permission check
        if (!current_user_can($this->get_capability())) {
            wp_die(__('Cheatin&#8217; uh?'));
        }

        // Check referer
        check_admin_referer('wpbrain_settings_save_presets');

        // Process presets
        $presets = self::POST('presets', []);
        if (is_array($presets)) {
            $this->PresetsManager->save_presets($presets);
            $this->set_settings_message(__("Preset conditions saved successfully!", 'wpbrain'), 'updated');
        }

        // Redirect
        $redirect_to = add_query_arg('settings-updated', 'true', wp_get_referer());
        wp_redirect($redirect_to);
    }

    public function admin_post_wpbrain_settings_save_filters()
    {
        // User permission check
        if (!current_user_can($this->get_capability())) {
            wp_die(__('Cheatin&#8217; uh?'));
        }

        // Check referer
        check_admin_referer('wpbrain_settings_save_filters');

        // Process filters
        $filters = self::POST('filters', []);
        if (is_array($filters)) {
            $this->FiltersManager->save_custom_filters($filters);
            $this->set_settings_message(__("Custom filters saved successfully!", 'wpbrain'), 'updated');
        }

        // Redirect
        $redirect_to = add_query_arg('settings-updated', 'true', wp_get_referer());
        wp_redirect($redirect_to);
    }

    public function admin_post_wpbrain_settings_save_options()
    {
        // User permission check
        if (!current_user_can($this->get_capability())) {
            wp_die(__('Cheatin&#8217; uh?'));
        }

        // Check referer
        check_admin_referer('wpbrain_settings_save_options');

        // Process options
        $options = self::POST('options', []);
        if (is_array($options)) {
            foreach ($options as &$value) {
                if ($value === 'true') {
                    $value = true;
                }
                if ($value === 'false') {
                    $value = false;
                }
            }
            update_option('wp_brain_options', $options);
            $this->set_settings_message(__("WP Brain options saved successfully!", 'wpbrain'), 'updated');
        }

        // Redirect
        $redirect_to = add_query_arg('settings-updated', 'true', wp_get_referer());
        wp_redirect($redirect_to);
    }

    public function admin_init()
    {
        if ($this->options->tinymce_plugin_enabled) {
            $this->add_filter('mce_external_plugins');
            $this->add_filter('mce_buttons');
        }
    }

    public function admin_menu()
    {
        add_menu_page(
            'WP Brain',
            'WP Brain',
            $this->get_capability(),
            $this->main_page_slug,
            $this->cb('render_presets_page'),
            'dashicons-cloud',
            '77.77'
        );

        $this->presets_hookname = add_submenu_page(
            $this->main_page_slug,
            __("Presets Conditions", 'wpbrain'),
            __("Preset Conditions", 'wpbrain'),
            $this->get_capability(),
            $this->presets_page_slug,
            $this->cb('render_presets_page')
        );

        $this->filters_hookname = add_submenu_page(
            $this->main_page_slug,
            __("Custom Filters", 'wpbrain'),
            __("Custom Filters", 'wpbrain'),
            $this->get_capability(),
            $this->filters_page_slug,
            $this->cb('render_filters_page')
        );

        $this->settings_hookname = add_submenu_page(
            $this->main_page_slug,
            __("Settings", 'wpbrain'),
            __("Settings", 'wpbrain'),
            $this->get_capability(),
            $this->settings_page_slug,
            $this->cb('render_settings_page')
        );

        $this->about_hookname = add_submenu_page(
            $this->main_page_slug,
            __("About", 'wpbrain'),
            __("About", 'wpbrain'),
            $this->get_capability(),
            $this->about_page_slug,
            $this->cb('render_about_page')
        );
    }

    public function render_presets_page()
    {
        $presets = $this->PresetsManager->get_presets([]);
        foreach ($presets as $i => $preset) {
            $data = [
                [$preset['name'], $preset['rules']],
            ];
            $preset['export'] = self::export($data);
            $presets[$i] = $preset;
        }
        require $this->path('admin/pages/presets.php');
    }

    public function render_filters_page()
    {
        $sources = $this->FiltersManager->get_custom_data_sources();
        $filters = $this->FiltersManager->get_custom_filters();
        foreach ($filters as $i => $filter) {
            $data = [
                [$filter['name'], $filter['source'], $filter['type'], $filter['key']],
            ];
            $filter['export'] = self::export($data);
            $filters[$i] = $filter;
        }
        require $this->path('admin/pages/filters.php');
    }

    public function render_settings_page()
    {
        $page_url = menu_page_url($this->settings_page_slug, false);
        $page = new Admin\SettingsPage($page_url);
        $page->render();
    }

    public function render_about_page()
    {
        $page_url = menu_page_url($this->about_page_slug, false);
        $page = new Admin\AboutPage($page_url);
        $page->render();
    }

    /**
     * @param array  $links
     * @param string $plugin_base
     *
     * @return array
     */
    public function plugin_action_links($links, $plugin_base)
    {
        if ($plugin_base == WPBRAIN_BASE) {
            $links = array_merge([
                'settings' => '<a href="' . menu_page_url($this->main_page_slug, false) . '">Settings</a>',
            ], $links);
        }

        return $links;
    }

    public function admin_enqueue_scripts()
    {
        global $current_screen;
        $base = $current_screen->base;

        $screens = [
            'posts' => [
                'post',
            ],
            'rulesbuilder' => [
                'post',
                $this->presets_hookname,
            ],
            'welcome' => [
                $this->about_hookname,
            ],
            'settings' => [
                $this->presets_hookname,
                $this->filters_hookname,
                $this->settings_hookname,
            ],
        ];

        wp_enqueue_style('erropix-ui', $this->url('admin/assets/css/erropix-ui.css'));
        wp_enqueue_style('wpbrain-admin', $this->url('admin/assets/css/admin.css'));
        wp_enqueue_script('wp-util');
        wp_enqueue_script('wpbrain', $this->url('admin/assets/js/wpbrain.min.js'));
        wp_localize_script('wpbrain', 'wpbrain', [
            'url' => WPBRAIN_URL,
            'version' => WPBRAIN_VERSION,
            'filters' => $this->FiltersManager->get_filters(),
            'filters_js' => $this->FiltersManager->get_filters_js(),
            'presets' => $this->PresetsManager->get_presets(),
            'options' => $this->options,
        ]);

        if (in_array($base, $screens['posts'])) {
            wp_enqueue_style('wpbrain-posts', $this->url('admin/assets/css/posts.css'));
        }

        if (in_array($base, $screens['rulesbuilder'])) {
            wp_enqueue_style('wpbrain-rulesbuilder', $this->url('admin/assets/css/rulesbuilder.css'));
            wp_enqueue_script('wpbrain-moment', $this->url('admin/assets/libs/moment/moment.min.js'));
            wp_enqueue_script('wpbrain-rulesbuilder', $this->url('admin/assets/js/rulesbuilder.min.js'));
        }

        if (in_array($base, $screens['welcome'])) {
            wp_enqueue_style('wpbrain-flaticon', $this->url('admin/assets/flaticon/flaticon.css'));
        }

        if (in_array($base, $screens['settings'])) {
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_style('wpbrain-chosen', $this->url('admin/assets/libs/jquery.chosen/chosen.min.css'));
            wp_enqueue_script('wpbrain-chosen', $this->url('admin/assets/libs/jquery.chosen/jquery.chosen.min.js'));
            wp_enqueue_script('wpbrain-repeater', $this->url('admin/assets/libs/jquery.repeater/jquery.repeater.js'));
            wp_enqueue_script('wpbrain-ays', $this->url('admin/assets/js/jquery.ays.min.js'));
        }

        if ($this->options->tinymce_plugin_enabled && get_user_option('rich_editing') == 'true') {
            wp_enqueue_style('wpbrain-tinymce', $this->url('admin/assets/css/tinymce.css'));
        }
    }

    /**
     * @param array $plugins
     *
     * @return array
     */
    public function mce_external_plugins($plugins)
    {
        $plugins['wpbrain'] = $this->url('admin/assets/js/tinymce-plugin.min.js');

        return $plugins;
    }

    /**
     * @param array $buttons
     *
     * @return array
     */
    public function mce_buttons($buttons)
    {
        $buttons[] = 'wpbrain';

        return $buttons;
    }

    /**
     * @param string $classes
     *
     * @return string
     */
    public function admin_body_class($classes)
    {
        if (defined('WPB_VC_VERSION') && version_compare(WPB_VC_VERSION, '5.3', '>=')) {
            $classes .= ' vc_ui_53';
        }

        return $classes;
    }

    /**
     * @return string
     */
    public function get_capability()
    {
        return 'manage_options';
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isFront()
    {
        return false;
    }
}
