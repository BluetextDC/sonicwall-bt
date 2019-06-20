<?php

namespace WPBrain;

$optgroup = "WordPress";

/**
 * Filter: Post Type
 */
$values = [];
$post_types = get_post_types(['public' => true], 'objects');
foreach ($post_types as $post_type => $object) {
    $values[$post_type] = $object->label;
}

$this->register_filter([
    'id' => "post_type",
    'label' => __("Post Type", 'wpbrain'),
    'type' => "string",
    'input' => "select",
    'operators' => "select",
    'multiple' => true,
    'optgroup' => $optgroup,
    'values' => $values,
    'get_value' => function () {
        return get_post_type();
    },
]);

/**
 * Filter: Page
 */
$values = [];
$pages = get_pages(['sort_column' => 'post_title']);

function build_pages_tree($pages, $values, $parent = 0, $prefix = '')
{
    foreach ($pages as $key => $page) {
        if ($page->post_parent == $parent) {
            $id = $page->ID;
            $key = ' ' . $id . ' ';
            $values[" $id "] = $prefix . $page->post_title;
            $_prefix = $prefix . $page->post_title . ' > ';
            $values = build_pages_tree($pages, $values, $id, $_prefix);
        }
    }

    return $values;
}

$values = build_pages_tree($pages, $values);

$this->register_filter([
    'id' => "page",
    'label' => __("Page", 'wpbrain'),
    'type' => "integer",
    'input' => "select",
    'operators' => "select",
    'multiple' => true,
    'optgroup' => $optgroup,
    'values' => $values,
    'midleware' => function () {
        return is_page();
    },
    'get_value' => function () {
        return ' ' . get_the_ID() . ' ';
    },
]);

/**
 * Filter: Page Template
 */
$default = [
    'default' => __("Default Template"),
];
$templates = wp_get_theme()->get_page_templates();
$values = array_merge($default, $templates);

$this->register_filter([
    'id' => "page_template",
    'label' => __("Page Template", 'wpbrain'),
    'type' => "string",
    'input' => "select",
    'operators' => "select",
    'multiple' => true,
    'optgroup' => $optgroup,
    'values' => $values,
    'get_value' => function () {
        $template = get_page_template_slug();
        if ($template === false) {
            return 'is_not_page';
        }
        if ($template === '') {
            $template = 'default';
        }

        return $template;
    },
]);

/**
 * Filter: Page Type
 */
$values = [
    'archive' => __("Archive", 'wpbrain'),
    'author' => __("Author archive", 'wpbrain'),
    'date' => __("Date archive", 'wpbrain'),
    'category' => __("Category archive", 'wpbrain'),
    'tag' => __("Tag archive", 'wpbrain'),
    'tax' => __("Taxonomy archive", 'wpbrain'),
    'search' => __("Search results", 'wpbrain'),
    'front_page' => __("Front page", 'wpbrain'),
    'home' => __("Posts page", 'wpbrain'),
    'attachment' => __("Single attachment", 'wpbrain'),
    'page' => __("Single page", 'wpbrain'),
    'single' => __("Single post", 'wpbrain'),
];

$this->register_filter([
    'id' => "page_type",
    'label' => __("Page Type", 'wpbrain'),
    'type' => "string",
    'input' => "select",
    'operators' => "select",
    'multiple' => true,
    'optgroup' => $optgroup,
    'values' => $values,
    'get_value' => function () use ($values) {
        $types = [];

        if (is_archive()) $types[] = 'archive';
        if (is_author()) $types[] = 'author';
        if (is_date()) $types[] = 'date';
        if (is_category()) $types[] = 'category';
        if (is_tag()) $types[] = 'tag';
        if (is_tax()) $types[] = 'tax';
        if (is_search()) $types[] = 'search';
        if (is_front_page()) $types[] = 'front_page';
        if (is_home()) $types[] = 'home';
        if (is_attachment()) $types[] = 'attachment';
        if (is_page()) $types[] = 'page';
        if (is_single()) $types[] = 'single';

        return $types;
    },
]);

add_filter('wp_brain_before_validate_rule', function ($result, $id, $value, $operator, $correct_value) {
    if ($id == 'page_type') {
        switch ($operator) {
            case 'equal':
                $result = in_array($value, $correct_value);
                break;

            case 'in':
                $result = count(array_intersect($correct_value, $value)) > 0;
                break;
        }
    }

    return $result;
}, 10, 5);

/**
 * Filter: Taxonomy
 */
$values = [];
$taxonomies = get_taxonomies(['public' => true], 'objects');
foreach ($taxonomies as $taxonomy => $object) {
    $values[$taxonomy] = $object->label;
}

$this->register_filter([
    'id' => "taxonomy",
    'label' => __("Taxonomy Archive", 'wpbrain'),
    'type' => "string",
    'input' => "select",
    'operators' => "select",
    'multiple' => true,
    'optgroup' => $optgroup,
    'values' => $values,
    'get_value' => function () {
        $taxonomy = '';
        $query = get_queried_object();
        if (isset($query->taxonomy)) {
            $taxonomy = $query->taxonomy;
        }

        return $taxonomy;
    },
]);

/**
 * Filter: List of all terms and their values
 */
// foreach ($taxonomies as $taxonomy => $object) {
//     $values = get_terms([
//         'taxonomy' => $taxonomy,
//         'hide_empty' => false,
//         'fields' => 'id=>name'
//     ]);
//
//     $this->register_filter([
//         'id' => "taxonomy_{$taxonomy}_terms",
//         'label' => "Post {$object->label}",
//         'type' => "string",
//         'input' => "select",
//         'operators' => "array",
//         'multiple' => true,
//         'data' => $taxonomy,
//         'optgroup' => $optgroup,
//         'values' => $values,
//         'get_value' => function ($taxonomy, $value) {
//             $terms = [];
//             if( is_single() ) {
//                 $terms = wp_get_post_terms(get_the_ID(), $taxonomy, ['fields' => 'ids']);
//             }
//             return $terms;
//         },
//     ]);
// }

/**
 * Filter: Pagination number
 */
$this->register_filter([
    'id' => "pagination_number",
    'label' => __("Pagination Number", 'wpbrain'),
    'type' => "integer",
    'operators' => "number",
    'optgroup' => $optgroup,
    'get_value' => function () {
        $paged = get_query_var('paged') ?: 1;
        return $paged;
    },
]);

/**
 * Filter: HTTP Referer
 */
$this->register_filter([
    'id' => "http_referer",
    'label' => __("HTTP Referer", 'wpbrain'),
    'type' => "string",
    'operators' => "string",
    'optgroup' => $optgroup,
    'get_value' => function () {
        return self::SERVER('HTTP_REFERER', '');
    },
]);
