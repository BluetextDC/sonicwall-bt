<?php

namespace WPBrain\Widgets;

use WPBrain\Utils;

/**
 * Widgets Manager
 */
class WidgetsVisibility extends Utils
{
    /**
     * @const string
     */
    const ID = 'wpbrain_rules_preset';

    /**
     * WidgetsVisibility constructor.
     */
    public function __construct()
    {
        $this->add_action('in_widget_form');
        $this->add_filter('widget_update_callback');
        $this->add_filter('widget_display_callback', 0);

        if (!is_admin()) {
            $this->add_filter('siteorigin_panels_data');
        }
    }

    /**
     * @param $widget   \WP_Widget
     * @param $return   null
     * @param $instance array
     */
    public function in_widget_form($widget, $return, $instance)
    {
        $id = $widget->get_field_id(self::ID);
        $name = $widget->get_field_name(self::ID);
        $value = self::array_get($instance, self::ID);

        $options = [];
        $presets = wpbrain('presets')->get_presets();
        foreach ($presets as $preset) {
            $options[$preset['id']] = $preset['name'];
        }
        ?>
        <p class="wp_brain_visibility_preset erropix-ui">
            <label for="<?php echo $id ?>"><?php _e("Visibility Condition", 'wpbrain') ?></label>
            <select name="<?php echo $name ?>" id="<?php echo $id ?>" class="widefat">
                <option value=""><?php _e("Always Visible", 'wpbrain') ?></option>
                <?php self::html_options($options, $value) ?>
            </select>
        </p>
        <?php
    }

    /**
     * @param $instance     array
     * @param $new_instance array
     *
     * @return array
     */
    public function widget_update_callback($instance, $new_instance)
    {
        $instance[self::ID] = $new_instance[self::ID];

        return $instance;
    }

    /**
     * @param $instance array
     * @param $widget   \WP_Widget
     * @param $args     array
     *
     * @return bool|array
     */
    public function widget_display_callback($instance, $widget, $args)
    {
        $preset_id = self::array_get($instance, self::ID);
        if ($preset_id) {
            $preset = wpbrain('presets')->get_preset($preset_id);
            $visibility = wpbrain('validator')->validatePreset($preset, true);
            if ($visibility === false) {
                return false;
            }
        }

        return $instance;
    }

    /**
     * @param array   $panels_data
     * @param integer $post_id
     *
     * @return array
     */
    public function siteorigin_panels_data($panels, $post_id)
    {
        $widgets = &$panels['widgets'];

        foreach ($widgets as $i => $widget) {
            $preset_id = self::array_get($widget, self::ID);
            if ($preset_id) {
                $preset = wpbrain('presets')->get_preset($preset_id);
                $visibility = wpbrain('validator')->validatePreset($preset, true);
                if ($visibility === false) {
                    unset($widgets[$i]);
                }
            }
        }

        return $panels;
    }
}
