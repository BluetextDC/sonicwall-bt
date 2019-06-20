<?php

/**
 * Create needed classes for VC Container Elements
 */
class WPBakeryShortCode_WP_Brain extends WPBakeryShortCodesContainer
{
    public function mainHtmlBlockParams($width, $i)
    {
        $s = $this->settings;

        return 'data-element_type="' . $s["base"] . '" class="wpb_' . $s['base'] . ' wpb_sortable wpb_content_holder vc_shortcodes_container ' . $s["class"] . '"' . $this->customAdminBlockParams();
    }
}

class WPBakeryShortCode_WP_Brain_If extends WPBakeryShortCode_WP_Brain
{
}

class WPBakeryShortCode_WP_Brain_Elseif extends WPBakeryShortCode_WP_Brain
{
}

class WPBakeryShortCode_WP_Brain_Else extends WPBakeryShortCode_WP_Brain
{
}

class WPBakeryShortCode_WP_Brain_Loop extends WPBakeryShortCode_WP_Brain
{
}

$category = "WP Brain";

/**
 * Map the IF Element
 */
vc_map([
    'name' => __("WP Brain IF", 'wpbrain'),
    'category' => $category,
    'description' => __("Control elements visiblity", 'wpbrain'),
    'class' => "wp_brain_container wp_brain_row",
    'icon' => "wp_brain_icon",
    'base' => "wp_brain_if",
    'as_parent' => ['except' => 'wp_brain_if,wp_brain_elseif,wp_brain_else,wp_brain_loop'],
    'content_element' => true,
    'is_container' => true,
    'params' => [
        [
            'type' => "rules_builder",
            'heading' => __("Display elements if the following conditions are true", 'wpbrain'),
            'param_name' => "rules",
        ],
        [
            'type' => "textfield",
            'heading' => __("Notes", 'wpbrain'),
            'description' => __("This will be shown in admin editor to help you know what this is for", 'wpbrain'),
            'param_name' => "notes",
            'value' => "",
            'admin_label' => true,
        ],
    ],
    'js_view' => 'VcColumnView',
]);

/**
 * Map the ELSE IF Element
 */
vc_map([
    'name' => __("WP Brain ELSEIF", 'wpbrain'),
    'category' => $category,
    'description' => __("Fallback visiblity control", 'wpbrain'),
    'class' => "wp_brain_container wp_brain_row",
    'icon' => "wp_brain_icon",
    'base' => "wp_brain_elseif",
    'as_parent' => ['except' => 'wp_brain_if,wp_brain_elseif,wp_brain_else,wp_brain_loop'],
    'content_element' => true,
    'is_container' => true,
    'params' => [
        [
            'type' => "rules_builder",
            'heading' => __("Display elements if the following conditions are true", 'wpbrain'),
            'param_name' => "rules",
        ],
        [
            'type' => "textfield",
            'heading' => __("Notes", 'wpbrain'),
            'description' => __("This will be shown in admin editor to help you know what this is for", 'wpbrain'),
            'param_name' => "notes",
            'value' => "",
            'admin_label' => true,
        ],
    ],
    'js_view' => 'VcColumnView',
]);

/**
 * Map the ELSE Element
 */
vc_map([
    'name' => __("WP Brain Else", 'wpbrain'),
    'category' => $category,
    'description' => "Fallback visibility control",
    'class' => "wp_brain_container wp_brain_row",
    'icon' => "wp_brain_icon",
    'base' => "wp_brain_else",
    'as_parent' => ['except' => 'wp_brain_if,wp_brain_elseif,wp_brain_else,wp_brain_loop'],
    'content_element' => true,
    'is_container' => true,
    'show_settings_on_create' => false,
    'params' => [],
    'js_view' => 'VcColumnView',
]);

/**
 * Map the LOOP Element
 */
vc_map([
    'name' => __("WP Brain Loop", 'wpbrain'),
    'category' => $category,
    'description' => __("Repeat child elements", 'wpbrain'),
    'class' => "wp_brain_container wp_brain_row",
    'icon' => "wp_brain_icon",
    'base' => "wp_brain_loop",
    'as_parent' => ['except' => 'wp_brain_loop'],
    'content_element' => true,
    'is_container' => true,
    'params' => [
        [
            'type' => "textfield",
            'heading' => __("Start from", 'wpbrain'),
            'param_name' => "from",
            'edit_field_class' => "vc_col-sm-4 wp_brain_loop-column",
            'value' => "1",
        ],
        [
            'type' => "textfield",
            'heading' => __("To", 'wpbrain'),
            'param_name' => "to",
            'edit_field_class' => "vc_col-sm-4 wp_brain_loop-column",
            'value' => "5",
        ],
        [
            'type' => "textfield",
            'heading' => __("Step", 'wpbrain'),
            'param_name' => "step",
            'edit_field_class' => "vc_col-sm-4 wp_brain_loop-column",
            'value' => "1",
        ],
    ],
    'js_view' => 'VcColumnView',
]);

/**
 * Map the INCLUDE Element
 */
vc_map([
    'name' => __("WP Brain Include", 'wpbrain'),
    'category' => $category,
    'description' => __("Include PHP or HTML file", 'wpbrain'),
    'icon' => "wp_brain_icon",
    'base' => "wp_brain_include",
    'params' => [
        [
            'type' => "textfield",
            'heading' => __("HTML or PHP file path", 'wpbrain'),
            'description' => __("The file path must be relative to the WordPress directory", 'wpbrain'),
            'param_name' => "file",
            'value' => "",
            'admin_label' => true,
        ],
    ],
]);

/**
 * Map the REDIRECT Element
 */
vc_map([
    'name' => __("WP Brain Redirect", 'wpbrain'),
    'category' => $category,
    'description' => __("Redirect to another page", 'wpbrain'),
    'icon' => "wp_brain_icon",
    'base' => "wp_brain_redirect",
    'params' => [
        [
            'type' => "textfield",
            'heading' => __("Redirect to", 'wpbrain'),
            'param_name' => "url",
            'value' => "",
            'admin_label' => true,
        ],
        [
            'type' => "textfield",
            'heading' => __("Status code", 'wpbrain'),
            'param_name' => "status",
            'value' => 302,
        ],
    ],
]);

/**
 * Map the ACTION Element
 */
vc_map([
    'name' => __("WP Brain Action", 'wpbrain'),
    'category' => $category,
    'description' => __("WordPress do_action", 'wpbrain'),
    'icon' => "wp_brain_icon",
    'base' => "wp_brain_action",
    'params' => [
        [
            'type' => "textfield",
            'heading' => __("Action name", 'wpbrain'),
            'description' => __("This will run any function hooked to the specified hook name", 'wpbrain'),
            'param_name' => "name",
            'value' => "",
            'admin_label' => true,
        ],
    ],
]);

/**
 * Map the FUNCTION Element
 */
vc_map([
    'name' => __("WP Brain Function", 'wpbrain'),
    'category' => $category,
    'description' => __("Call PHP Function", 'wpbrain'),
    'icon' => "wp_brain_icon",
    'base' => "wp_brain_function",
    'params' => [
        [
            'type' => "textfield",
            'heading' => __("Function name", 'wpbrain'),
            'description' => "",
            'param_name' => "name",
            'value' => "",
            'admin_label' => true,
        ],
        [
            'type' => "dropdown",
            'heading' => __("Function Type", 'wpbrain'),
            'description' => "",
            'param_name' => "type",
            'value' => [
                'Output' => '0',
                'Return' => '1',
            ],
            'admin_label' => true,
        ],
    ],
]);
