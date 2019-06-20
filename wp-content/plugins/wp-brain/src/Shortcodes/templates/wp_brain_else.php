<?php
global $wpbrain;

// Output shortcodes if the previous rules were invalid
if (wpbrain('validator')->getLastResult() === false) {
    echo do_shortcode($content);
}
