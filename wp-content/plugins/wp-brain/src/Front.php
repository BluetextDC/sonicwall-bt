<?php

namespace WPBrain;

use WPBrain\Blocks\BlocksVisibility;
use WPBrain\Elementor\ElementorVisibility;
use WPBrain\Filters\FiltersManager;
use WPBrain\Location\LocationManager;
use WPBrain\Menus\MenusVisibility;
use WPBrain\Presets\PresetsManager;
use WPBrain\Rules\RulesValidator;
use WPBrain\Shortcodes\ShortcodesManager;
use WPBrain\Widgets\WidgetsVisibility;

/**
 * Frontend Controller
 */
class Front extends Utils
{
    use Base;

    /**
     * @var \WPBrain\Filters\FiltersManager
     */
    private $FiltersManager;

    /**
     * @var \WPBrain\Presets\PresetsManager
     */
    private $Presetsmanager;

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
     * Front constructor
     */
    private function __construct()
    {
        $this->add_action('plugins_loaded');
        $this->add_action('wp_footer');
    }

    /**
     * Setup modules when all plugins are loaded
     */
    public function plugins_loaded()
    {
        $this->options = self::get_options();

        $this->setModule('options', $this->options);
        $this->setModule('filters', new FiltersManager());
        $this->setModule('presets', new PresetsManager());
        $this->setModule('location', new LocationManager());
        $this->setModule('shortcodes', new ShortcodesManager());
        $this->setModule('validator', new RulesValidator());
        $this->setModule('blocks', new BlocksVisibility());

        if ($this->options->elementor_addon_enabled) {
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
     * Add static templates to the footer
     */
    public function wp_footer()
    {
        if (function_exists('vc_is_page_editable') && vc_is_page_editable()) {
            require $this->path('front/vc_front_editor.php');
        }
    }

    /**
     * @return bool
     */
    public function isFront()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return false;
    }
}
