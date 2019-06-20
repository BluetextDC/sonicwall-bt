<?php

namespace WPBrain\Rules;

use WPBrain\Utils;

/**
 * Rules Validator
 */
class RulesValidator extends Utils
{
    private $correct_values = [];
    private $last_result = true;

    /**
     * @param $rule
     */
    private function validateRule($rule)
    {
        $id = $rule->id;
        $operator = $rule->operator;
        $value = $rule->value;

        $filter = wpbrain('filters')->get_filter($id);
        if (empty($filter)) {
            return;
        }

        $data = [];
        if (isset($filter['data'])) {
            $data = $filter['data'];
        }
        $data = apply_filters('wp_brain_filter_data', $data, $id);

        if (isset($filter['midleware'])) {
            $continue = call_user_func($filter['midleware'], $rule, $id);
            if ($continue === false) {
                return false;
            }
        }

        if (isset($filter['format_value'])) {
            $value = call_user_func($filter['format_value'], $value, $data);
            $value = apply_filters('wp_brain_rule_value', $value, $data, $rule, $id);
        }

        if (!isset($this->correct_values[$id])) {
            $correct_value = call_user_func($filter['get_value'], $data, $value);
            $correct_value = apply_filters('wp_brain_filter_correct_value', $correct_value, $data, $value, $filter);
            if ($filter['stable_value'] === true) {
                $this->correct_values[$id] = $correct_value;
            }
        } else {
            $correct_value = $this->correct_values[$id];
        }

        switch ($filter['type']) {
            case 'integer':
                if (is_array($value)) {
                    $value = array_map('intval', $value);
                } else {
                    $value = intval($value);
                }
                $correct_value = intval($correct_value);
                break;

            case 'double':
                if (is_array($value)) {
                    $value = array_map('doubleval', $value);
                } else {
                    $value = doubleval($value);
                }
                $correct_value = doubleval($correct_value);
                break;

            case 'version':
                $x = version_compare($correct_value, $value);
                $value = intval($value);
                $correct_value = $value + $x;
                break;
        }

        $reverse = false;
        if (strpos($operator, 'not_') === 0) {
            $reverse = true;
            $operator = substr($operator, 4);
        }

        $result = apply_filters("wp_brain_before_validate_rule", NULL, $id, $value, $operator, $correct_value);

        if (!is_bool($result)) {
            $result = false;
            switch ($operator) {
                case 'equal':
                    $result = $value == $correct_value;
                    break;

                case 'less':
                    $result = $correct_value < $value;
                    break;

                case 'less_or_equal':
                    $result = $correct_value <= $value;
                    break;

                case 'between':
                    $result = $value[0] <= $correct_value && $correct_value <= $value[1];
                    break;

                case 'greater':
                    $result = $correct_value > $value;
                    break;

                case 'greater_or_equal':
                    $result = $correct_value >= $value;
                    break;

                case 'in':
                    $array = array_intersect((array)$correct_value, $value);
                    $result = count($array) ? true : false;
                    break;

                case 'begins_with':
                    $result = strpos($correct_value, $value) === 0;
                    break;

                case 'contains':
                    $result = strpos($correct_value, $value) !== false;
                    break;

                case 'ends_with':
                    $result = strpos($correct_value, $value) === strlen($correct_value) - strlen($value);
                    break;

                case 'empty':
                    $result = empty($correct_value);
                    break;

                case 'null':
                    $result = is_null($correct_value);
                    break;

                case 'match':
                    $result = (bool)@preg_match($value, $correct_value);
                    break;

                case 'true':
                    $result = $correct_value === true;
                    break;
            }
        }

        if ($reverse) {
            $result = !$result;
        }

        $result = apply_filters("wp_brain_after_validate_rule", $result, $id, $value, $operator, $correct_value);

        return $result;
    }

    public function validateGroup($group)
    {
        $result = true;

        if (is_object($group) and isset($group->condition, $group->rules)) {
            $condition = $group->condition;
            $rules = $group->rules;
            $last = count($rules) - 1;
            foreach ($rules as $i => $rule) {
                if (isset($rule->id)) {
                    $result = $this->validateRule($rule);
                } else {
                    $result = $this->validateGroup($rule);
                }

                if ($condition == 'AND' && $result === false) {
                    break;
                }
                if ($condition == 'OR' && $result === true) {
                    break;
                }
            }
        }

        return $result;
    }

    public function validatePreset($preset, $default = false)
    {
        if (!empty($preset['rules'])) {
            $rules = $preset['rules'];
            return $this->validate($rules, false);
        } else {
            return $default;
        }
    }

    public function validate($rules, $save = true)
    {
        $result = true;

        if ($rules) {
            $rules = @base64_decode($rules);
            $rules = @json_decode($rules);
            $result = $this->validateGroup($rules);

            if ($save) {
                $this->last_result = $result;
            }
        }

        return $result;
    }

    public function getLastResult($reset = true)
    {
        $last_result = $this->last_result;
        if ($reset) {
            $this->last_result = true;
        }

        return $last_result;
    }
}
