<?php

namespace WPBrain\Filters;

use WPBrain\Utils;
use WPBrain\SmartObject;

/**
 * Filter Manager
 */
class FiltersManager extends Utils
{
    private $filters = [];

    private $custom_filters = [];

    private $operators = [
        'all' => [
            'equal',
            'not_equal',
            'in',
            'not_in',
            'less',
            'less_or_equal',
            'greater',
            'greater_or_equal',
            'between',
            'not_between',
            'begins_with',
            'not_begins_with',
            'contains',
            'not_contains',
            'ends_with',
            'not_ends_with',
            'match',
            'not_match',
            'empty',
            'not_empty',
            'null',
            'not_null',
            'true',
            'not_true',
        ],
        'string' => [
            'equal',
            'not_equal',
            'begins_with',
            'not_begins_with',
            'contains',
            'not_contains',
            'ends_with',
            'not_ends_with',
            'match',
            'not_match',
            'empty',
            'not_empty',
            'null',
            'not_null',
        ],
        'number' => [
            'equal',
            'not_equal',
            'less',
            'less_or_equal',
            'between',
            'not_between',
            'greater',
            'greater_or_equal',
            'empty',
            'not_empty',
            'null',
            'not_null',
        ],
        'version' => [
            'equal',
            'not_equal',
            'less',
            'less_or_equal',
            'greater',
            'greater_or_equal',
        ],
        'select_full' => [
            'equal',
            'not_equal',
            'in',
            'not_in',
            'empty',
            'not_empty',
        ],
        'select' => [
            'equal',
            'not_equal',
            'in',
            'not_in',
        ],
        'array' => [
            'in',
            'not_in',
        ],
        'choices' => [
            'equal',
            'not_equal',
        ],
        'boolean' => [
            'true',
            'not_true',
        ],
    ];

    public function __construct()
    {
        $this->add_action('init');
    }

    public function init()
    {
        // Load all built-in filters
        $modules = glob(dirname(__FILE__) . '/modules/*.php');
        $modules = array_filter($modules, 'is_readable');
        foreach ($modules as $module) {
            include $module;
        }

        // Allow plugins to register their own filters
        do_action("wp_brain_register_filters", $this);

        // Load custom filters
        $this->register_custom_filters();

        // Load presets as filters
        $this->register_presets_as_filters();
    }

    public function register_filter($filter)
    {
        $filter = apply_filters('wp_brain_before_register_filter', $filter);
        extract($filter);

        // Verify required filters details
        if (empty($id) || empty($type) || empty($operators) || empty($optgroup) || empty($get_value)) {
            trigger_error("Invalid filter: id, type, operators, optgroup and get_value are required!", E_USER_WARNING);

            return false;
        }

        // Verify callbacks
        if (!is_callable($get_value)) {
            trigger_error("Invalid filter data: 'get_value' must be a valid callback!", E_USER_WARNING);

            return false;
        }
        if (isset($midleware) && !is_callable($midleware)) {
            trigger_error("Invalid filter data: 'midleware' must be a valid callback!", E_USER_WARNING);

            return false;
        }
        if (isset($format_value) && !is_callable($format_value)) {
            trigger_error("Invalid filter data: 'format_value' must be a valid callback!", E_USER_WARNING);

            return false;
        }

        // Sanitize operators
        if (isset($operators)) {
            if (is_string($operators)) {
                if (isset($this->operators[$operators])) {
                    $filter['operators'] = $this->operators[$operators];
                } else {
                    trigger_error("Invalid filter data: operators group doesn't exist!", E_USER_WARNING);

                    return false;
                }
            } elseif (is_array($operators)) {
                $all_operators = $this->operators['all'];
                $operators = array_uintersect($operators, $all_operators);
                if (!empty($operators)) {
                    $filter['operators'] = $operators;
                } else {
                    trigger_error("Invalid filter data: all operators doesn't exist!", E_USER_WARNING);

                    return false;
                }
            }
        }

        // Filter value is stable or not
        if (!isset($filter['stable_value'])) {
            $filter['stable_value'] = true;
        }

        // Register the filter
        $filter = apply_filters("wp_brain_register_filter", $filter, $id);
        $this->filters[$id] = $filter;

        return true;
    }

    public function register_custom_filters()
    {
        $filters = $this->get_custom_filters();

        foreach ($filters as $filter) {
            $Filter = new SmartObject($filter);

            $source = $Filter->source;
            $key = $Filter->key;
            $name = $Filter->name;
            $id = "{$source}_{$key}";
            $args = [
                'id' => $id,
                'label' => $name,
                'optgroup' => __("Custom", 'wpbrain'),
                'data' => $filter,
            ];

            // Set type
            $type = $filter['type'];
            $args['type'] = $type;

            // Set operators
            $operators = $filter['type'];
            switch ($operators) {
                case 'datetime':
                case 'date':
                case 'time':
                case 'integer':
                case 'double':
                    $operators = 'number';
                    break;
            }
            $args['operators'] = $operators;

            // Advanced settings
            switch ($type) {
                case 'datetime':
                    $args['placeholder'] = 'DD-MM-YYYY HH:mm';
                    $args['validation'] = ['format' => 'DD-MM-YYYY HH:mm'];
                    break;

                case 'date':
                    $args['placeholder'] = 'DD-MM-YYYY';
                    $args['validation'] = ['format' => 'DD-MM-YYYY'];
                    break;

                case 'time':
                    $args['placeholder'] = 'HH:mm';
                    $args['validation'] = ['format' => 'HH:mm'];
                    break;
            }

            // Value getter callback
            $args['get_value'] = [$this, 'get_custom_filter_value'];

            // Prevent caching values for some sources
            if ($source == 'global') {
                $args['stable_value'] = false;
            }

            // Add the filter
            $this->register_filter($args);
        }
    }

    public function get_filter($id)
    {
        if (isset($this->filters[$id])) {
            $filter = $this->filters[$id];
            $filter = apply_filters("wp_brain_get_filter", $filter, $id);

            return $filter;
        }
    }

    public function get_filters()
    {
        $filters = apply_filters("wp_brain_get_filter", $this->filters);

        return $filters;
    }

    public function get_filters_js()
    {
        $filters = $this->get_filters();
        $filters = array_values($filters);

        foreach ($filters as &$filter) {
            unset($filter['midleware']);
            unset($filter['get_value']);
            unset($filter['format_value']);
            unset($filter['stable_value']);
            unset($filter['data']);
        }

        return $filters;
    }

    public function get_filters_json()
    {
        $filters = $this->get_filters_js();

        return json_encode($filters, JSON_PRETTY_PRINT);
    }

    public function save_custom_filters($filters)
    {
        $unique_filters = [];
        foreach ($filters as $filter) {
            $key = $filter['key'];
            if (empty($unique_filters[$key])) {
                $unique_filters[$key] = $filter;
            }
        }
        $filters = array_values($unique_filters);
        update_option('wp_brain_filters', $filters);
    }

    public function get_custom_data_sources()
    {
        $sources = [
            "usermeta" => __("User metadata", 'wpbrain'),
            "postmeta" => __("Post metadata", 'wpbrain'),
            "query_var" => __("Query variable", 'wpbrain'),
            "get" => __("URL variables", 'wpbrain'),
            "post" => __("Form fields", 'wpbrain'),
            "constant" => __("PHP constant", 'wpbrain'),
            "function" => __("PHP function", 'wpbrain'),
            "global" => __("PHP variable", 'wpbrain'),
            "cookies" => __("Cookies", 'wpbrain'),
            "session" => __("Session", 'wpbrain'),
            "env" => __("Environment variables", 'wpbrain'),
            "server" => __("Server variables", 'wpbrain'),
        ];

        return apply_filters('wp_brain_data_sources', $sources, $this);
    }

    public function get_custom_filters($default = [])
    {
        if (empty($this->custom_filters)) {
            $this->custom_filters = get_option('wp_brain_filters', $default);
        }

        return $this->custom_filters;
    }

    public function get_custom_filter_value($data)
    {
        global $wp_query;
        extract($data);

        $value = '';
        if (isset($key) && isset($source)) {
            switch ($source) {
                case 'usermeta':
                    $value = Utils::umeta($key);
                    break;
                case 'postmeta':
                    $value = Utils::pmeta($key);
                    break;
                case 'query_var':
                    $value = get_query_var($key);
                    break;
                case 'get':
                    $value = Utils::GET($key);
                    break;
                case 'post':
                    $value = Utils::POST($key);
                    break;
                case 'cookies':
                    $value = Utils::COOKIE($key);
                    break;
                case 'session':
                    $value = Utils::SESSION($key);
                    break;
                case 'server':
                    $value = Utils::SERVER($key);
                    break;
                case 'env':
                    $value = Utils::ENV($key);
                    break;
                case 'global':
                    $value = Utils::GLOBALS($key);
                    break;
                case 'constant':
                    $value = constant($key);
                    break;
                case 'function':
                    if (function_exists($key)) {
                        $value = call_user_func($key);
                    }
                    break;
            }

            $value = apply_filters("wp_brain_data_source_value", $value, $source, $key);
        }

        return $value;
    }

    public function register_presets_as_filters()
    {
        $presets = wpbrain('presets')->get_presets();

        foreach ($presets as $preset) {
            $Preset = new SmartObject($preset);
            $args = [
                'id' => "preset_$preset[id]",
                'label' => $preset['name'],
                'optgroup' => __("Preset Conditions", 'wpbrain'),
                'type' => 'boolean',
                'operators' => 'boolean',
                'data' => $preset,
                'get_value' => wpbrain('validator')->cb('validatePreset'),
                'stable_value' => false,
            ];

            $this->register_filter($args);
        }
    }
}
