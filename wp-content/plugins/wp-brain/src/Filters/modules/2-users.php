<?php
$optgroup = __("Users", 'wpbrain');

/**
 * Filter: Loggedin
 */
$this->register_filter([
    'id' => "loggedin",
    'label' => __("Logged in", 'wpbrain'),
    'type' => "boolean",
    'operators' => 'boolean',
    'multiple' => true,
    'optgroup' => $optgroup,
    'get_value' => 'is_user_logged_in',
]);

/**
 * Filter: User
 */
$users = get_users(['fields' => ['ID', 'display_name']]);
$values = [];
foreach ($users as $user) {
    $values["$user->ID"] = $user->display_name;
}
$this->register_filter([
    'id' => "user",
    'label' => __("User", 'wpbrain'),
    'type' => "integer",
    'input' => "select",
    'operators' => "select",
    'multiple' => true,
    'optgroup' => $optgroup,
    'values' => $values,
    'midleware' => 'is_user_logged_in',
    'get_value' => function () {
        global $current_user;
        return $current_user->ID ? $current_user->ID : '';
    },
]);

/**
 * Filter: Role
 */
global $wp_roles;

$values = $wp_roles->role_names;
$this->register_filter([
    'id' => "role",
    'label' => __("Role", 'wpbrain'),
    'type' => "string",
    'input' => "select",
    'operators' => "select",
    'multiple' => true,
    'optgroup' => $optgroup,
    'values' => $values,
    'midleware' => 'is_user_logged_in',
    'get_value' => function () {
        global $current_user;
        return is_array($current_user->roles) ? $current_user->roles[0] : '';
    },
]);
