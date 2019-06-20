<?php

use WPBrain\Libs\BrowserDetection;
use WPBrain\Libs\MobileDetect;

$optgroup = __("System Detection", 'wpbrain');

/**
 * Filter: Device
 */
$this->register_filter([
    'id' => "device",
    'label' => __("Device", 'wpbrain'),
    'type' => "string",
    'input' => "select",
    'operators' => "choices",
    'optgroup' => $optgroup,
    'values' => [
        'mobile' => __("Mobile", 'wpbrain'),
        'tablet' => __("Tablet", 'wpbrain'),
        'desktop' => __("Desktop", 'wpbrain'),
    ],
    'get_value' => function () {
        $detect = new MobileDetect;

        if ($detect->isTablet()) {
            return 'tablet';
        }

        if ($detect->isMobile()) {
            return 'mobile';
        }

        return 'desktop';
    },
]);

/**
 * Filter: Operating System
 */
$values = [
    BrowserDetection::PLATFORM_ANDROID,
    BrowserDetection::PLATFORM_BLACKBERRY,
    BrowserDetection::PLATFORM_FREEBSD,
    BrowserDetection::PLATFORM_IPAD,
    BrowserDetection::PLATFORM_IPHONE,
    BrowserDetection::PLATFORM_LINUX,
    BrowserDetection::PLATFORM_MACINTOSH,
    BrowserDetection::PLATFORM_WINDOWS,
    BrowserDetection::PLATFORM_WINDOWS_PHONE,
    BrowserDetection::PLATFORM_UNKNOWN,
];
$values = array_combine($values, $values);

$this->register_filter([
    'id' => "op_system",
    'label' => __("Platform", 'wpbrain'),
    'type' => "string",
    'input' => "select",
    'operators' => "select",
    'multiple' => true,
    'optgroup' => $optgroup,
    'values' => $values,
    'get_value' => function () {
        $detect = new BrowserDetection;

        return $detect->getPlatform();
    },
]);

/**
 * Filter: Browser
 */
$values = [
    BrowserDetection::BROWSER_CHROME,
    BrowserDetection::BROWSER_EDGE,
    BrowserDetection::BROWSER_FIREFOX,
    BrowserDetection::BROWSER_IE,
    BrowserDetection::BROWSER_LYNX,
    BrowserDetection::BROWSER_OPERA,
    BrowserDetection::BROWSER_SAFARI,
    BrowserDetection::BROWSER_UNKNOWN,
];
$values = array_combine($values, $values);

$this->register_filter([
    'id' => "browser",
    'label' => __("Browser", 'wpbrain'),
    'type' => "string",
    'input' => "select",
    'multiple' => "true",
    'operators' => "select",
    'optgroup' => $optgroup,
    'values' => $values,
    'get_value' => function () {
        $detect = new BrowserDetection;

        return $detect->getName();
    },
]);

/**
 * Filter: Browser Version
 */
$this->register_filter([
    'id' => "browser_version",
    'label' => __("Browser version", 'wpbrain'),
    'type' => "version",
    'operators' => "version",
    'optgroup' => $optgroup,
    'get_value' => function () {
        $detect = new BrowserDetection;

        return $detect->getVersion();
    },
]);
