<?php
global $wp_filter;

extract(array_merge([
    'name' => '',
], $atts));

if ($name && isset($wp_filter[$name])) {
    do_action($name);
} else {
    trigger_error(sprintf(__("The action hook %s is not available", 'wpbrain'), "<b>$name</b>"));
}
