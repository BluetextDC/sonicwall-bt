<?php

namespace WPBrain\Elementor;

use Elementor\Plugin;
use Elementor\Controls_Manager;
use WPBrain\Utils;

class ElementorVisibility extends Utils
{
    private $presets = [];

    public function __construct()
    {
        $presets = wpbrain('presets')->get_presets();

        $this->presets[''] = __("Always Visible", 'wpbrain');
        if (is_array($presets)) {
            foreach ($presets as $preset) {
                $this->presets[$preset['id']] = $preset['name'];
            }
        }

        // Add visibility condition fields
        $this->add_action('elementor/element/section/section_advanced/after_section_end', 'add_visibility_fields');
        $this->add_action('elementor/element/column/section_advanced/after_section_end', 'add_visibility_fields');
        $this->add_action('elementor/element/common/_section_style/after_section_end', 'add_visibility_fields');

        // Handle elements visibility
        $this->add_filter('elementor/frontend/section/should_render', 'is_element_visible');
        $this->add_filter('elementor/frontend/column/should_render', 'is_element_visible');
        $this->add_filter('elementor/frontend/widget/should_render', 'is_element_visible');
    }

    /**
     * @param \Elementor\Element_Base $element
     * @param null|array              $args
     */
    public function add_visibility_fields($element, $args = NULL)
    {
        $element->start_controls_section(
            'wp_brain_visibility',
            [
                'tab' => Controls_Manager::TAB_ADVANCED,
                'label' => __("Visibility Condition", 'wpbrain'),
            ]
        );

        $element->add_control(
            'wp_brain_visibility_preset',
            [
                'label' => __('Visibility Condition', 'wpbrain'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => false,
                'label_block' => true,
                'options' => $this->presets,
                'render_type' => 'none',
                'description' => __('The element will be visible when the condition is TRUE.', 'wpbrain'),
            ]
        );

        $element->end_controls_section();
    }

    /**
     * @param boolean                 $visible
     * @param \Elementor\Element_Base $element
     *
     * @return boolean
     */
    public function is_element_visible($visible, $element = NULL)
    {
        $settings = $element->get_settings_for_display();

        if (isset($settings['wp_brain_visibility_preset'])) {
            $preset_id = $settings['wp_brain_visibility_preset'];
            if ($preset_id) {
                $preset = wpbrain('presets')->get_preset($preset_id);
                $visible = wpbrain('validator')->validatePreset($preset, $visible);
            }
        }

        return $visible;
    }
}
