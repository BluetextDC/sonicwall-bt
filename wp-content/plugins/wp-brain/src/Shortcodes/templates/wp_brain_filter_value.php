<?php

// Extract attributes
extract(array_merge([
    'filter' => '',
], $atts));

// Proccess preset
if (!empty($filter)) {
    $filter_name = $filter;
    $filter = wpbrain('filters')->get_filter($filter);

    $valid = true;
    if (isset($filter['midleware'])) {
        $valid = call_user_func($filter['midleware'], NULL, $filter);
    }

    if ($valid && is_callable($filter['get_value'])) {
        $data = [];
        if (isset($filter['data'])) {
            $data = $filter['data'];
        }

        $values = (array)call_user_func($filter['get_value'], $data, NULL);
    } else {
        $values = [''];
    }

    $strings = [];
    foreach ($values as $value) {
        // Convert boolean value to string
        if (is_bool($value)) {
            $value = $value ? 'Yes' : 'No';
        } // Convert null value to string
        elseif (is_null($value)) {
            $value = 'null';
        } // Look for the value label if available
        elseif (!empty($filter['values']) && is_array($filter['values'])) {
            $values = $filter['values'];
            if (!empty($values[$value])) {
                $value = $values[$value];
            }
        }

        // Replace empty strings by N/A
        if ($value === '') {
            $value = 'N/A';
        }

        $strings[] = $value;
    }

    echo implode(', ', $strings);
}
