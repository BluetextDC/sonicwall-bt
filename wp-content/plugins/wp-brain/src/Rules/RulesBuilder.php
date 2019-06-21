<?php

namespace WPBrain\Rules;

use WPBrain\Utils;

/**
 * Rules Builder
 */
class RulesBuilder extends Utils
{
    public function __construct()
    {
        $this->add_action('vc_before_init');
    }

    public function vc_before_init()
    {
        vc_add_shortcode_param("rules_builder", $this->cb('vc_rules_builder'));
    }

    public function vc_rules_builder($settings, $value)
    {
        $name = $settings["param_name"];

        ob_start();
        ?>
        <input type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>" id="wpbrain_vc_rulesuilder_value" class="wpb_vc_param_value <?php echo $name ?>">
        <script type="text/javascript">
            function wpbrain_vc_rulesuilder_callback() {
                var $this = this;

                if (vc && vc.edit_element_block_view) {
                    var $popup = vc.edit_element_block_view.$el;
                    $popup.css("min-width", 700);

                    if ($popup.hasClass("vc_ui-panel-window")) {
                        selector = ".vc_ui-button[data-vc-ui-element=button-save]";
                    } else {
                        selector = ".vc_btn.vc_panel-btn-save";
                    }
                    $btn_save = $popup.find(selector);

                    $btn_save.off("click.epxvcl").on("click.epxvcl", function () {
                        var value = $this.getB64Rules();
                        if (value) {
                            jQuery("#wpbrain_vc_rulesuilder_value").val(value);
                            return;
                        }
                        return false;
                    });

                    vc.edit_element_block_view.once("hide", function () {
                        $popup.css("min-width", "");
                    });
                }
            }
        </script>
        <?php
        $this->render('wpbrain_vc_rulesuilder', $value, 'wpbrain_vc_rulesuilder_callback');

        $html = ob_get_clean();

        return $html;
    }

    public function render($container_id, $rules, $js_callback)
    {
        ?>
        <div id="<?php echo $container_id ?>" class="erropix-ui"></div>
        <script type="text/javascript">
            !function ($, callback) {
                var $this = $("#<?= $container_id ?>");
                var filters = wpbrain.filters_js;

                $this.rulesBuilder({
                    allow_groups: 2,
                    allow_empty: true,
                    inputs_separator: '',
                    filters: filters
                });

                var rules = '<?= base64_decode($rules) ?>';
                if (rules) {
                    try {
                        rules = JSON.parse(rules);
                        if (rules && rules.condition && rules.rules[0]) {
                            $this.rulesBuilder("setRules", rules);
                        }
                    } catch (e) {
                    }
                }

                $this.getB64Rules = function () {
                    var rules = $this.rulesBuilder("getRules");
                    if (rules.condition) {
                        rules = JSON.stringify(rules);
                        rules = wpbrain.base64_encode(rules);
                        return rules;
                    } else {
                        return '';
                    }
                };

                if (typeof callback === 'function') {
                    callback.call($this, filters);
                }
            }(jQuery, <?php echo $js_callback ?>);
        </script>
        <?php
    }
}
