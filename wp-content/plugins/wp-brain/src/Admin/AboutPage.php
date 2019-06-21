<?php

namespace WPBrain\Admin;

use WPBrain\Utils;

/**
 * About page rendering
 */
class AboutPage extends Utils
{
    private $page_url;
    private $tabs;
    private $tab_slug;
    private $tab;

    public function __construct($page_url)
    {
        $tabs = [
            'overview' => [
                'title' => "Overview",
                'callback' => $this->cb('render_tab_overview'),
            ],
            'help' => [
                'title' => "Help",
                'callback' => $this->cb('render_tab_help'),
            ],
        ];

        $tab_slug = $this->GET('tab', 0);
        if (!array_key_exists($tab_slug, $tabs)) {
            $tab_slug = key($tabs);
        }

        $this->page_url = $page_url;
        $this->tabs = $tabs;
        $this->tab_slug = $tab_slug;
        $this->tab = $tabs[$tab_slug];
    }

    public function render()
    {
        require $this->path('admin/pages/about.php');
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

    public function render_tab_overview()
    {
        require $this->path('admin/pages/about/tab-overview.php');
    }

    public function render_tab_help()
    {
        require $this->path('admin/pages/about/tab-help.php');
    }
}
