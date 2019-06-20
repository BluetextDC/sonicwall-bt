<?php
$optgroup = __("Date & Time", 'wpbrain');

/**
 * Filter: DateTime
 */
$this->register_filter([
    'id' => "datetime",
    'label' => __("Date time", 'wpbrain'),
    'type' => "date",
    'operators' => 'number',
    'placeholder' => 'DD-MM-YYYY HH:mm',
    'optgroup' => $optgroup,
    'validation' => [
        'format' => 'DD-MM-YYYY HH:mm',
    ],
    'get_value' => function () {
        return date("Y-m-d H:i");
    },
    'format_value' => function ($date) {
        if (is_array($date)) {
            foreach ($date as $i => $date_i) {
                $date[$i] = date("Y-m-d H:i", strtotime($date_i));
            }
        } else {
            $date = date("Y-m-d H:i", strtotime($date));
        }

        return $date;
    },
]);

/**
 * Filter: Date
 */
$this->register_filter([
    'id' => "date",
    'label' => __("Date", 'wpbrain'),
    'type' => "date",
    'operators' => 'number',
    'placeholder' => 'DD-MM-YYYY',
    'optgroup' => $optgroup,
    'validation' => [
        'format' => 'DD-MM-YYYY',
    ],
    'get_value' => function () {
        return date("Y-m-d");
    },
    'format_value' => function ($date) {
        if (is_array($date)) {
            foreach ($date as $i => $date_i) {
                $date[$i] = date("Y-m-d", strtotime($date_i));
            }
        } else {
            $date = date("Y-m-d", strtotime($date));
        }

        return $date;
    },
]);

/**
 * Filter: Time
 */
$this->register_filter([
    'id' => "time",
    'label' => __("Time", 'wpbrain'),
    'type' => "time",
    'operators' => 'number',
    'placeholder' => 'HH:mm',
    'optgroup' => $optgroup,
    'validation' => [
        'format' => 'HH:mm',
    ],
    'get_value' => function () {
        return date("H:i");
    },
]);

/**
 * Filter: Minute
 */
$values = [];
for ($i = 0; $i < 60; $i++) {
    $values[$i] = sprintf('%02d', $i);
}
$this->register_filter([
    'id' => "minute",
    'label' => __("Minute", 'wpbrain'),
    'type' => "integer",
    'input' => "select",
    'operators' => 'number',
    'optgroup' => $optgroup,
    'values' => $values,
    'validation' => [
        'min' => 0,
        'max' => 59,
    ],
    'get_value' => function () {
        return intval(date("i"));
    },
]);

/**
 * Filter: Hour
 */
$values = [];
for ($i = 0; $i < 24; $i++) {
    $values[$i] = sprintf('%02d', $i);
}
$this->register_filter([
    'id' => "hour",
    'label' => __("Hour", 'wpbrain'),
    'type' => "integer",
    'input' => "select",
    'operators' => 'number',
    'optgroup' => $optgroup,
    'values' => $values,
    'validation' => [
        'min' => 0,
        'max' => 23,
    ],
    'get_value' => function () {
        return intval(date("H"));
    },
]);

/**
 * Filter: Day
 */
$values = [];
for ($i = 1; $i <= 31; $i++) {
    $values[$i] = sprintf('%02d', $i);
}
$this->register_filter([
    'id' => "day",
    'label' => __("Day", 'wpbrain'),
    'type' => "integer",
    'input' => "select",
    'operators' => 'number',
    'optgroup' => $optgroup,
    'values' => $values,
    'validation' => [
        'min' => 1,
        'max' => 31,
    ],
    'get_value' => function () {
        return intval(date("d"));
    },
]);

/**
 * Filter: Day of week
 */
$this->register_filter([
    'id' => "dayofweek",
    'label' => __("Day of week", 'wpbrain'),
    'type' => "integer",
    'input' => "select",
    'operators' => 'number',
    'optgroup' => $optgroup,
    'values' => [
        '1' => __("Monday", 'wpbrain'),
        '2' => __("Tuesday", 'wpbrain'),
        '3' => __("Wednesday", 'wpbrain'),
        '4' => __("Thursday", 'wpbrain'),
        '5' => __("Friday", 'wpbrain'),
        '6' => __("Saturday", 'wpbrain'),
        '7' => __("Sunday", 'wpbrain'),
    ],
    'validation' => [
        'min' => 1,
        'max' => 7,
    ],
    'get_value' => function () {
        return intval(date("N"));
    },
]);

/**
 * Filter: Month
 */
$this->register_filter([
    'id' => "month",
    'label' => __("Month", 'wpbrain'),
    'type' => "integer",
    'input' => "select",
    'operators' => 'number',
    'optgroup' => $optgroup,
    'values' => [
        '1' => __("January", 'wpbrain'),
        '2' => __("February", 'wpbrain'),
        '3' => __("March", 'wpbrain'),
        '4' => __("April", 'wpbrain'),
        '5' => __("May", 'wpbrain'),
        '6' => __("June", 'wpbrain'),
        '7' => __("July", 'wpbrain'),
        '8' => __("August", 'wpbrain'),
        '9' => __("September", 'wpbrain'),
        '10' => __("October", 'wpbrain'),
        '11' => __("November", 'wpbrain'),
        '12' => __("December", 'wpbrain'),
    ],
    'validation' => [
        'min' => 1,
        'max' => 12,
    ],
    'get_value' => function () {
        return intval(date("n"));
    },
]);

/**
 * Filter: Year
 */
$this->register_filter([
    'id' => "year",
    'label' => __("Year", 'wpbrain'),
    'type' => "integer",
    'operators' => 'number',
    'placeholder' => 'YYYY',
    'optgroup' => $optgroup,
    'get_value' => function () {
        return intval(date("Y"));
    },
]);
