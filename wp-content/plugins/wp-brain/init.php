<?php

use WPBrain\Admin;
use WPBrain\Front;

// Global instance getter
function wpbrain($module = NULL)
{
    static $instance = NULL;

    if (is_null($instance)) {
        if (is_admin()) {
            $instance = Admin::getInstance();
        } else {
            $instance = Front::getInstance();
        }
    }

    if (is_null($module)) {
        return $instance;
    } else {
        return $instance->getModule($module);
    }
}

wpbrain();
