<form id="wpbrain_settings_options" method="POST" action="<?php echo admin_url('admin-post.php') ?>" data-ays>
    <?php wp_nonce_field('wpbrain_settings_save_options'); ?>
    <input type="hidden" name="action" value="wpbrain_settings_save_options">

    <table class="settings-table">
        <tbody>
        <?php
        $label = __("WPBakery Page Builder Addon", 'wpbrain');
        $description = __("Enable shortcodes integration with WPBakery Page Builder", 'wpbrain');
        $name = 'options[vc_addon_enabled]';
        $id = 'options_vc_addon_enabled';
        $value = $options['vc_addon_enabled'];
        ?>
        <tr>
            <th>
                <label for="<?php echo $id ?>"><?php echo $label ?></label>
                <p class="setting-description"><?php echo $description ?></p>
            </th>
            <td><?php self::checkbox($name, $value) ?></td>
        </tr>

        <?php
        $label = __("Elementor Page Builder Addon", 'wpbrain');
        $description = __("Enable visibility control integration with Elementor Page Builder", 'wpbrain');
        $name = 'options[elementor_addon_enabled]';
        $id = 'options_elementor_addon_enabled';
        $value = $options['elementor_addon_enabled'];
        ?>
        <tr>
            <th>
                <label for="<?php echo $id ?>"><?php echo $label ?></label>
                <p class="setting-description"><?php echo $description ?></p>
            </th>
            <td><?php self::checkbox($name, $value) ?></td>
        </tr>

        <?php
        $label = __("TinyMCE Shortcode Generator", 'wpbrain');
        $description = __("Add shortcode manager to the default WordPress text editor", 'wpbrain');
        $name = 'options[tinymce_plugin_enabled]';
        $id = 'options_tinymce_plugin_enabled';
        $value = $options['tinymce_plugin_enabled'];
        ?>
        <tr>
            <th>
                <label for="<?php echo $id ?>"><?php echo $label ?></label>
                <p><?php echo $description ?></p>
            </th>
            <td><?php self::checkbox($name, $value) ?></td>
        </tr>

        <?php
        $label = __("Widgets Visibility Control", 'wpbrain');
        $description = __("Display presets dropdown on widgets to control their visibility", 'wpbrain');
        $name = 'options[widgets_visibility_enabled]';
        $id = 'options_widgets_visibility_enabled';
        $value = $options['widgets_visibility_enabled'];
        ?>
        <tr>
            <th>
                <label for="<?php echo $id ?>"><?php echo $label ?></label>
                <p><?php echo $description ?></p>
            </th>
            <td><?php self::checkbox($name, $value) ?></td>
        </tr>

        <?php
        $label = __("Menus Visibility Conrtol", 'wpbrain');
        $description = __("Display presets dropdown on menu items to control their visibility", 'wpbrain');
        $name = 'options[menus_visibility_enabled]';
        $id = 'options_menus_visibility_enabled';
        $value = $options['menus_visibility_enabled'];
        ?>
        <tr>
            <th>
                <label for="<?php echo $id ?>"><?php echo $label ?></label>
                <p><?php echo $description ?></p>
            </th>
            <td><?php self::checkbox($name, $value) ?></td>
        </tr>

        <?php
        $label = __("Menus noConflict mode", 'wpbrain');
        $description = __("Use JavaScript to prevent conflict with other plugins that add their fields to menu items", 'wpbrain');
        $name = 'options[menus_visibility_noconflict]';
        $id = 'options_menus_visibility_noconflict';
        $value = $options['menus_visibility_noconflict'];
        ?>
        <tr style="display:none" data-dependon="#options_menus_visibility_enabled" data-dependon-value="true">
            <th>
                <label for="<?php echo $id ?>"><?php echo $label ?></label>
                <p><?php echo $description ?></p>
            </th>
            <td><?php self::checkbox($name, $value) ?></td>
        </tr>

        <?php
        $label = __("GeoLocation Provider", 'wpbrain');
        $description = __("Select one of GeoLocation provider integrated services", 'wpbrain');
        $name = 'options[geolocation_provider]';
        $id = 'options_geolocation_provider';
        $value = $options['geolocation_provider'];
        ?>
        <tr>
            <th>
                <label for="<?php echo $id ?>"><?php echo $label ?></label>
                <p><?php echo $description ?></p>
            </th>
            <td>
                <div class="chosen-select">
                    <select name="<?php echo $name ?>" id="<?php echo $id ?>">
                        <?php
                        $providers = wpbrain('location')->getProviders();
                        self::html_options($providers, $value);
                        ?>
                    </select>
                </div>
            </td>
        </tr>

        <?php
        $label = __("GeoIP2 City database (optional)", 'wpbrain');
        $description = __("Provide a database file path to use instead of the Free GeoLite2 database included", 'wpbrain');
        $name = 'options[geolocation_geoip2_mmdb]';
        $id = 'options_geolocation_geoip2_mmdb';
        $value = $options['geolocation_geoip2_mmdb'];
        ?>
        <tr style="display:none" data-dependon="#options_geolocation_provider" data-dependon-value="GeoIp2">
            <th>
                <label for="<?php echo $id ?>"><?php echo $label ?></label>
                <p><?php echo $description ?></p>
            </th>
            <td>
                <input type="text" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>">
            </td>
        </tr>

        <?php
        $label = __("API Key", 'wpbrain');
        $description = __("An API Key is needed to use this location service", 'wpbrain');
        $name = 'options[geolocation_dbip_key]';
        $id = 'options_geolocation_dbip_key';
        $value = $options['geolocation_dbip_key'];
        ?>
        <tr style="display:none" data-dependon="#options_geolocation_provider" data-dependon-value="DbIP">
            <th>
                <label for="<?php echo $id ?>"><?php echo $label ?></label>
                <p><?php echo $description ?></p>
            </th>
            <td>
                <input type="text" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>">
            </td>
        </tr>

        <?php
        $label = __("API Key", 'wpbrain');
        $description = __("An API Key is needed to use this location service", 'wpbrain');
        $name = 'options[geolocation_ipstack_key]';
        $id = 'options_geolocation_ipstack_key';
        $value = $options['geolocation_ipstack_key'];
        ?>
        <tr style="display:none" data-dependon="#options_geolocation_provider" data-dependon-value="IpStack">
            <th>
                <label for="<?php echo $id ?>"><?php echo $label ?></label>
                <p><?php echo $description ?></p>
            </th>
            <td>
                <input type="text" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>">
            </td>
        </tr>

        <?php
        $label = __("Access Token", 'wpbrain');
        $description = __("An access token is needed to use this location service", 'wpbrain');
        $name = 'options[geolocation_ipinfo_token]';
        $id = 'options_geolocation_ipinfo_token';
        $value = $options['geolocation_ipinfo_token'];
        ?>
        <tr style="display:none" data-dependon="#options_geolocation_provider" data-dependon-value="IpInfo">
            <th>
                <label for="<?php echo $id ?>"><?php echo $label ?></label>
                <p><?php echo $description ?></p>
            </th>
            <td>
                <input type="text" name="<?php echo $name ?>" id="<?php echo $id ?>" value="<?php echo $value ?>">
            </td>
        </tr>

        <?php do_action('wp_brain_settings_custom_options'); ?>
        </tbody>
    </table>

    <div class="settings-buttons clearfix">
        <div class="float-left"></div>
        <div class="float-right">
            <button type="submit" class="btn btn-green"><?php _e("Save Options", 'wpbrain') ?></button>
        </div>
    </div>
</form>

<script type="text/javascript">
    !function ($) {
        $('.chosen-select select').chosen({
            disable_search: true
        });

        $('[data-dependon]').each(function () {
            var $this = $(this);
            var dependon_input = $this.data('dependon');
            var dependon_value = $this.data('dependon-value');
            var $dependon = $(dependon_input).eq(0);

            if ($dependon.length) {
                $dependon.on('change', function () {
                    var show = false;
                    if (this.type == 'checkbox' && this.checked == dependon_value) {
                        show = true
                    }
                    if (this.value == dependon_value) {
                        show = true
                    }
                    if (show) {
                        $this.show();
                    } else {
                        $this.hide();
                    }
                }).trigger('change');
            }
        });
    }(jQuery);
</script>
