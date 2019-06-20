<?php

namespace WPBrain\Admin;

use WPBrain\Utils;

/**
 * Settings page rendering
 */
class SettingsPage extends Utils
{
    private $page_url;
    private $tabs;
    private $tab;
    private $tab_slug;

    public function __construct($page_url)
    {
        $this->page_url = $page_url;

        $tabs = [
            'options' => [
                'title' => __("Options", 'wpbrain'),
                'callback' => $this->cb('render_tab_options'),
            ],
        ];

        $tabs = apply_filters('wp_brain_settings_tabs', $tabs);

        $tab_slug = $this->GET('tab', 0);
        if (!array_key_exists($tab_slug, $tabs)) {
            $tab_slug = key($tabs);
        }

        $this->tabs = $tabs;
        $this->tab_slug = $tab_slug;
        $this->tab = $tabs[$tab_slug];
    }

    public function render()
    {
        require $this->path('admin/pages/settings.php');
    }

    public function get_tab_url($tab_slug)
    {
        return add_query_arg('tab', $tab_slug, $this->page_url);
    }

    public function render_current_tab()
    {
        if (isset($this->tab['callback']) && is_callable($this->tab['callback'])) {
            call_user_func($this->tab['callback']);
        }
    }

    public function render_tab_options()
    {
        $options = self::get_options();
        require $this->path('admin/pages/settings/tab-options.php');
    }
}
