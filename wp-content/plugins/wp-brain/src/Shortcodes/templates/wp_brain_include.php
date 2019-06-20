<?php

extract(array_merge([
    'file' => '',
], $atts));

$file = trim($file, '/');

if ($file) {
    $file = ABSPATH . $file;
    if (is_file($file)) {
        include $file;
    } else {
        trigger_error(sprintf(__("The file %s do not exists", 'wpbrain'), "<b>$file</b>"));
    }
}
