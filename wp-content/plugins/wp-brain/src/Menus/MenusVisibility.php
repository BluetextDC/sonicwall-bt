<?php

namespace WPBrain\Menus;

use WPBrain\Utils;

/**
 * Menus Manager
 */
class MenusVisibility extends Utils
{
    const NAME = 'menu-item-visibility-preset';
    const META = '_menu_item_visibility_preset';

    public function __construct()
    {
        $options = self::get_options();

        if (is_admin()) {
            $this->add_action('wp_update_nav_menu_item');
            $this->add_filter('manage_nav-menus_columns');

            if ($options->menus_visibility_noconflict) {
                $this->add_filter('admin_head', 'extend_edit_nav_menu_with_js');
            } else {
                $this->add_filter('wp_edit_nav_menu_walker');
                $this->add_action('wp_nav_menu_item_custom_fields', 10, 4);
            }
        } else {
            $this->add_filter('wp_get_nav_menu_items');
        }
    }

    public function manage_nav_menus_columns($columns)
    {
        $columns['visibility-condition'] = __("Visibility Condition", 'wpbrain');

        return $columns;
    }

    public function wp_update_nav_menu_item($menu, $id)
    {
        $data = $this->POST(self::NAME);
        $preset = $this->array_get($data, $id);
        if ($preset) {
            update_post_meta($id, self::META, $preset);
        } else {
            delete_post_meta($id, self::META);
        }
    }

    public function extend_edit_nav_menu_with_js()
    {
        global $current_screen, $wpdb;
        if ($current_screen->id == 'nav-menus') {
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE meta_key=%1$s", self::META);
            $metas = $wpdb->get_results($query, OBJECT);

            $menu_presets = [];
            foreach ($metas as $meta) {
                $menu_presets[$meta->post_id] = $meta->meta_value;
            }

            $options = [];
            $presets = wpbrain('presets')->get_presets();
            foreach ($presets as $preset) {
                $options[$preset['id']] = $preset['name'];
            }
            ?>
            <script type="text/javascript">
                jQuery(function ($) {
                    $menu = $("#menu-to-edit");
                    if (!$menu.length) return;

                    var presets = <?php echo json_encode($menu_presets) ?>;

                    setInterval(function () {
                        $menu.find(".menu-item").each(function () {
                            var $item = $(this);
                            var $form = $(".menu-item-settings", this);

                            if ($form.length === 0) {
                                return;
                            }

                            if ($form.hasClass("wpbrain")) {
                                return;
                            } else {
                                $form.addClass("wpbrain");
                            }

                            var $reference = $form.children("p").last();
                            if ($reference.length === 0) {
                                return;
                            }

                            var id = $item.attr("id").replace("menu-item-", "");
                            var input_id = "edit-menu-item-visibility-condition-" + id;
                            var input_name = '<?php echo self::NAME ?>[' + id + "]";

                            var field = "" +
                                "<p class=\"field-visibility-condition erropix-ui description description-wide\">" +
                                "<label for=\"" + input_id + "\">" +
                                '<?php _e("Visibility Condition", 'wpbrain') ?>' +
                                "<select name=\"" + input_name + "\" id=\"" + input_id + "\" class=\"widefat\">" +
                                '<option value=""><?php _e("Always Visible", 'wpbrain') ?></option>' +
                                '<?php self::html_options($options, -1) ?>' +
                                "</select>" +
                                "</label>" +
                                "</p>";
                            $field = $(field);
                            $field.insertAfter($reference);

                            if (presets[id]) {
                                $field.find("select").val(presets[id]);
                            }
                        });
                    }, 500);
                });
            </script>
            <?php
        }
    }

    public function wp_edit_nav_menu_walker($walker)
    {
        // Prevent false warnings from plugins that notify you of nav menu walker replacement.
        if (doing_filter('plugins_loaded')) {
            return $walker;
        }

        // Return early if another plugin/theme is using the custom fields walker.
        if ($walker == 'Walker_Nav_Menu_Edit_Custom_Fields') {
            return $walker;
        }

        // Load the proper walker class based on current WP version.
        if (!class_exists('Walker_Nav_Menu_Edit_Custom_Fields')) {
            require_once $this->path('admin/walkers/class-nav-menu-edit-custom-fields.php');
        }

        return 'Walker_Nav_Menu_Edit_Custom_Fields';
    }

    public function wp_nav_menu_item_custom_fields($item_id, $item, $depth, $args)
    {
        $options = [];
        $presets = wpbrain('presets')->get_presets();
        foreach ($presets as $preset) {
            $options[$preset['id']] = $preset['name'];
        }
        ?>
        <!-- Visibility Conditions -->
        <p class="field-visibility-condition erropix-ui description description-wide">
            <label for="edit-menu-item-visibility-condition-<?php echo $item_id; ?>">
                <?php _e("Visibility Condition", 'wpbrain') ?><br/>
                <select id="edit-menu-item-visibility-condition-<?php echo $item_id; ?>" class="widefat edit-menu-item-visibility-condition" name="<?php echo self::NAME ?>[<?php echo $item_id; ?>]">
                    <option value=""><?php _e("Always Visible", 'wpbrain') ?></option>
                    <?php self::html_options($options, $this->pmeta(self::META, $item_id)) ?>
                </select> </label>
        </p><!--/ End Visibility Condition -->
        <?php
    }

    public function wp_get_nav_menu_items($items, $menu, $args)
    {
        $hidden = [];

        foreach ($items as $key => $item) {
            $visibile = true;
            if (in_array($item->menu_item_parent, $hidden)) {
                $visibile = false;
            } else {
                $preset_id = $this->pmeta(self::META, $item->ID);
                if ($preset_id) {
                    $preset = wpbrain('presets')->get_preset($preset_id);
                    $visibile = wpbrain('validator')->validatePreset($preset, $visibile);
                }
            }
            if ($visibile === false) {
                $hidden[] = $item->ID;
                unset($items[$key]);
            }
        }

        return $items;
    }
}
