<?php defined('ABSPATH') or die("No script kiddies please!"); ?>
<style>
    .smls-main-logo-outer-<?php echo $random_num; ?>.smls-main-logo-wrapper {
        width: <?php echo (isset($smls_settings['main_wrapper_width'])) ? esc_attr($smls_settings['main_wrapper_width']) : '100' ?>%;
        margin:0 auto;
    }
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-grid-container-template-1 .smls-grid-each-item:before,
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-grid-container-template-1 .smls-grid-image-wrap:before,
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-grid-container-template-2 .smls-grid-each-item:before,
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-grid-container-template-2 .smls-grid-image-wrap:before {

        border-left: 1px solid <?php
if (isset($smls_settings['grid_border_color'])) {
    echo esc_attr($smls_settings['grid_border_color']);
} else {
    echo '#e9e9e9';
}
?>;
    }
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-grid-container-template-1 .smls-grid-each-item:after,
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-grid-container-template-1 .smls-grid-image-wrap:after,
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-grid-container-template-2 .smls-grid-each-item:after,
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-grid-container-template-2 .smls-grid-image-wrap:after {
        border-bottom: 1px solid <?php
        if (isset($smls_settings['grid_border_color'])) {
            echo esc_attr($smls_settings['grid_border_color']);
        } else {
            echo '#e9e9e9';
        }
?>;
    }
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-grid-container-template-2 {
        border: 1px solid <?php
        if (isset($smls_settings['grid_border_color'])) {
            echo esc_attr($smls_settings['grid_border_color']);
        } else {
            echo '#e9e9e9';
        }
?>;
    }

    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-carousel-pager-template-2.owl-theme .owl-dots .owl-dot span:before
    {
        background-color:  <?php
        if (isset($smls_settings['pager_active_color'])) {
            echo esc_attr($smls_settings['pager_active_color']);
        } else {
            echo '#0d98dc';
        }
?>;

    }
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-carousel-pager-template-2.owl-theme .owl-dots .owl-dot span {
        background:  <?php
        if (isset($smls_settings['pager_color'])) {
            echo esc_attr($smls_settings['pager_color']);
        } else {
            echo '#7c7c7c';
        }
?>;
    }

    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-carousel-arrow-type-1.owl-theme .owl-controls .owl-nav [class*=owl-]:hover {
        background-color: <?php
        if (isset($smls_settings['arrow_hover_color'])) {
            echo esc_attr($smls_settings['arrow_hover_color']);
        } else {
            echo 'rgba(71, 71, 71, 0.7)';
        }
?>;
    }
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-carousel-arrow-type-1.owl-theme .owl-controls .owl-nav [class*=owl-] {
        background-color: <?php
        if (isset($smls_settings['arrow_color'])) {
            echo esc_attr($smls_settings['arrow_color']);
        } else {
            echo '#474747';
        }
?>;
    }
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-carousel-arrow-type-2.owl-theme .owl-controls .owl-nav [class*=owl-] {
        background-color:  <?php
        if (isset($smls_settings['arrow_color'])) {
            echo esc_attr($smls_settings['arrow_color']);
        } else {
            echo '#bcbcbc';
        }
?>;
    }
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-carousel-arrow-type-2.owl-theme .owl-controls .owl-nav [class*=owl-]:hover {
        background-color: <?php
        if (isset($smls_settings['arrow_hover_color'])) {
            echo esc_attr($smls_settings['arrow_hover_color']);
        } else {
            echo '#f6881f';
        }
?>;
    }
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-carousel-arrow-type-3.owl-theme .owl-controls .owl-nav [class*=owl-] {
        border: 2px solid <?php
        if (isset($smls_settings['arrow_color'])) {
            echo esc_attr($smls_settings['arrow_color']);
        } else {
            echo '#e8e8e8';
        }
?>;

    }
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-carousel-arrow-type-3.owl-theme .owl-controls .owl-nav [class*=owl-]:before {
        background-color: <?php
        if (isset($smls_settings['arrow_hover_color'])) {
            echo esc_attr($smls_settings['arrow_hover_color']);
        } else {
            echo '#f24831';
        }
?>;
    }
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-carousel-arrow-type-3.owl-theme .owl-controls .owl-nav [class*=owl-]:hover {
        border-color: <?php
        if (isset($smls_settings['arrow_hover_color'])) {
            echo esc_attr($smls_settings['arrow_hover_color']);
        } else {
            echo '#f24831';
        }
?>;
    }
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-carousel-arrow-type-4.owl-theme .owl-controls .owl-nav [class*=owl-] {
        border: 2px solid <?php
        if (isset($smls_settings['arrow_color'])) {
            echo esc_attr($smls_settings['arrow_color']);
        } else {
            echo '#cccccc';
        }
?>;
        color: <?php
        if (isset($smls_settings['arrow_color'])) {
            echo esc_attr($smls_settings['arrow_color']);
        } else {
            echo '#cccccc';
        }
?>;
    }
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-carousel-arrow-type-4.owl-carousel .owl-controls .owl-nav [class*=owl-]:hover {
        background-color: <?php
        if (isset($smls_settings['arrow_hover_color'])) {
            echo esc_attr($smls_settings['arrow_hover_color']);
        } else {
            echo '#e8e8e8';
        }
?>;
        color: #333333;
    }
    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-carousel-arrow-type-5.owl-theme .owl-controls .owl-nav [class*=owl-] {
        background-color:  <?php
        if (isset($smls_settings['arrow_color'])) {
            echo esc_attr($smls_settings['arrow_color']);
        } else {
            echo '#75be08';
        }
?>;

    }

    .smls-main-logo-outer-<?php echo $random_num; ?> .smls-carousel-template-2.owl-carousel .smls-carousel-four-items {
        border: 1px solid  <?php
        if (isset($smls_settings['car_border_color'])) {
            echo esc_attr($smls_settings['car_border_color']);
        } else {
            echo '#eee';
        }
?>;
    }

</style>