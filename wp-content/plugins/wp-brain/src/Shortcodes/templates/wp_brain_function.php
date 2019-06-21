<?php

extract(array_merge([
    'name' => '',
    'type' => 0,
], $atts));

if (function_exists($name)) {
    $results = call_user_func($name);
    if ($type && (is_string($results) || is_numeric($results))) {
        echo $results;
    }
} else {
    trigger_error(sprintf(__("The function %s do not exists", 'wpbrain'), "<b>$name</b>"));
}
