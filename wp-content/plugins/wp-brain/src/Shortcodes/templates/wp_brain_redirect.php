<?php

extract(array_merge([
    'url' => '',
    'status' => 302,
], $atts));

if ($url) {
    if (headers_sent()) {
        $url = apply_filters('wp_redirect', $url, $status);
        echo '<meta http-equiv="refresh" content="0;url=' . $url . '"/>';
    } else {
        wp_redirect($url, $status);
    }
    exit;
}
