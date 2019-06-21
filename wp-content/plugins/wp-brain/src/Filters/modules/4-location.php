<?php

namespace WPBrain\Location;

/**
 * @return object
 */
function Countries()
{
    $file = wpbrain()->path('data/countries.json');
    $json = file_get_contents($file);
    $data = json_decode($json, true);

    return $data;
}

/**
 * @return \WPBrain\Location\LocationProvider|null
 */
function Location()
{
    static $location = NULL;

    if (is_null($location)) {
        $location = wpbrain('location')->provider();
        $location->locate();
    }

    return $location;
}

$optgroup = __("Location", 'wpbrain');

/**
 * Filter: Country
 */
$values = Countries();
$this->register_filter([
    'id' => "country",
    'label' => __("Country", 'wpbrain'),
    'type' => "string",
    'input' => "select",
    'operators' => 'select',
    'multiple' => true,
    'optgroup' => $optgroup,
    'values' => $values,
    'get_value' => function () {
        return Location()->getCountry();
    },
]);

/**
 * Filter: Region
 */
$this->register_filter([
    'id' => "region",
    'label' => __("Region", 'wpbrain'),
    'type' => "string",
    'operators' => 'string',
    'optgroup' => $optgroup,
    'get_value' => function () {
        return Location()->getRegion();
    },
]);

/**
 * Filter: City
 */
$this->register_filter([
    'id' => "city",
    'label' => __("City", 'wpbrain'),
    'type' => "string",
    'operators' => 'string',
    'optgroup' => $optgroup,
    'get_value' => function () {
        return Location()->getCity();
    },
]);

/**
 * Filter: Region
 */
$this->register_filter([
    'id' => "eu_member",
    'label' => __("EU Member", 'wpbrain'),
    'type' => "boolean",
    'operators' => 'boolean',
    'optgroup' => $optgroup,
    'get_value' => function () {
        return Location()->isEuMember();
    },
]);

/**
 * Filter: Continent
 */
$this->register_filter([
    'id' => "continent",
    'label' => __("Continent", 'wpbrain'),
    'type' => "string",
    'input' => "select",
    'operators' => 'select',
    'multiple' => true,
    'optgroup' => $optgroup,
    'values' => [
        'AF' => __("Africa", 'wpbrain'),
        'AN' => __("Antarctica", 'wpbrain'),
        'AS' => __("Asia", 'wpbrain'),
        'EU' => __("Europe", 'wpbrain'),
        'NA' => __("North America", 'wpbrain'),
        'OC' => __("Oceania", 'wpbrain'),
        'SA' => __("South America", 'wpbrain'),
    ],
    'get_value' => function () {
        return Location()->getContinent();
    },
]);

/**
 * Filter: Latitude
 */
$this->register_filter([
    'id' => "latitude",
    'label' => __("Latitude", 'wpbrain'),
    'type' => "double",
    'operators' => 'number',
    'optgroup' => $optgroup,
    'get_value' => function () {
        return Location()->getLatitude();
    },
]);

/**
 * Filter: Longitude
 */
$this->register_filter([
    'id' => "longitude",
    'label' => __("Longitude", 'wpbrain'),
    'type' => "double",
    'operators' => 'number',
    'optgroup' => $optgroup,
    'get_value' => function () {
        return Location()->getLongitude();
    },
]);

/**
 * Filter: IP Address
 */
$this->register_filter([
    'id' => "ip",
    'label' => __("IP Address", 'wpbrain'),
    'type' => "string",
    'operators' => 'string',
    'optgroup' => $optgroup,
    'get_value' => function () {
        return $_SERVER['REMOTE_ADDR'];
    },
]);
