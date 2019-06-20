<?php
global $wpbrain;

// Extract Rules Object
extract(array_merge([
    'rules' => '',
    'preset' => '',
], $atts));

// Get the preset rules
if (!empty($preset)) {
    $preset = wpbrain('presets')->get_preset($preset);
    if (isset($preset['rules'])) {
        $rules = $preset['rules'];
    }
}

// Output shortcodes if the rules are valid
if (wpbrain('validator')->validate($rules)) {
    echo do_shortcode($content);
}
