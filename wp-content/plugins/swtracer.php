<?php
/*
Plugin Name: SW Tracer
Plugin URI: https://www.sonicwall.com
Description: Helps trace servers behind our LB
Version: 0.1.0
Author: Brad Kendall*/

function trace_wp_footer() {

    $hostname = gethostname();
    $host = explode('-',$hostname);

    $trace = 'Trace:'.md5($hostname).'-'.$host[count($host) - 1];
    echo "<div style='display: none;'>$trace</div>".PHP_EOL;
}

add_action('wp_footer', 'trace_wp_footer', PHP_INT_MAX);
