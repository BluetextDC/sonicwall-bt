<?php

// Extract Attributes
extract(array_merge([
    'from' => '1',
    'to' => '5',
    'step' => '1',
], $atts));

$from = intval($from);
$to = intval($to);
$step = abs(intval($step));

// Expose loop index globally
global $wp_brain_loop_index;

// Output shortcodes
for ($index = $from; ;) {
    $wp_brain_loop_index = $index;
    $increase = $from <= $to;

    if ($increase) {
        if ($index > $to) {
            break;
        }
    } else {
        if ($index < $to) {
            break;
        }
    }

    $content = str_replace('$index', $index, $content);
    echo do_shortcode($content);

    if ($increase) {
        $index += $step;
    } else {
        $index -= $step;
    }
}

unset($wp_brain_loop_index);
