<div class="wrap erropix-ui">
    <h2><?php _e("Presets Conditions", 'wpbrain') ?></h2>

    <div id="wpbrain_settings">
        <form id="wpbrain_settings_presets" method="POST" action="<?php echo admin_url('admin-post.php') ?>" data-ays>
            <?php wp_nonce_field('wpbrain_settings_save_presets'); ?>
            <input type="hidden" name="action" value="wpbrain_settings_save_presets">

            <div class="repeater clearfix">
                <script type="text/htel-template" data-repeater-template>
                    <?php
                    $index = 0;
                    $preset = array(
                        'id' => '',
                        'name' => '',
                        'rules' => '',
                        'export' => ''
                    );
                    include('presets/preset.php');
                    ?>
                </script>
                <div class="repeater-header clearfix">
                    <div class="column column-order"><?php _e("Order", 'wpbrain') ?></div>
                    <div class="column column-name"><?php _e("Name", 'wpbrain') ?></div>
                    <div class="column column-condition"><?php _e("Conditions", 'wpbrain') ?></div>
                </div>
                <div class="repeater-list" data-repeater-list="presets">
                    <?php
                    $export = '';
                    if ($presets) {
                        $index = 1;
                        $export = array();
                        foreach ($presets as $preset) {
                            include('presets/preset.php');
                            $export[] = array($preset['name'], $preset['rules']);
                            $index++;
                        }
                        $export = self::export($export);
                    }
                    ?>
                </div>

                <div class="settings-buttons clearfix">
                    <div class="float-left">
                        <a type="button" class="btn btn-blue" data-repeater-create><?php _e("Add New", 'wpbrain') ?></a>
                    </div>
                    <div class="float-right">
                        <button type="button" class="btn btn-gray" data-import><?php _e("Import", 'wpbrain') ?></button>
                        <?php if ($export): ?>
                            <button type="button" class="btn btn-gray" data-export="<?php echo $export ?>"><?php _e("Export", 'wpbrain') ?></button>
                        <?php endif ?>
                        <button type="submit" class="btn btn-green"><?php _e("Save Presets", 'wpbrain') ?></button>
                    </div>
                </div>
            </div>
            <div id="wpbrain_presets_imexporter" class="hidden">
                <div style="height:100%">
                    <textarea class="wpbrain-imexport"></textarea>
                </div>
            </div>
        </form>

        <script type="text/javascript">
            !function ($) {
                var doing_import = false;

                // Repeater
                var $repeater = $('.repeater');
                var $sortable = $repeater.children('.repeater-list');
                $repeater.repeater({
                    ready: function (setIndexes) {
                        $repeater.fixRepeaterLabels();

                        $(document).ready(function () {
                            $sortable.sortable({
                                tolerance: 'pointer',
                                handle: '.repeater-item-order',
                                placeholder: 'repeater-item-placeholder',
                                forcePlaceholderSize: true,
                                update: function (event, ui) {
                                    setIndexes.call();
                                }
                            });
                            $sortable.on("sortupdate", function (event, ui) {
                                var $items = $(this).children();
                                $items.each(function () {
                                    var order = $items.index(this) + 1;
                                    $(this).find('.repeater-item-order').text(order);
                                });
                            });
                        });
                    },
                    show: function () {
                        var $item = $(this);
                        $item.fadeIn(200);
                        $item.find('input.name-value').trigger('change');
                        $item.find('.rules-builder').each(setupRulesBuilder);
                        $sortable.trigger('sortupdate');
                        if (!doing_import) {
                            $item.find('.repeater-item-toggle').eq(0).trigger('click');
                        }
                    },
                    hide: function (deleteElement) {
                        var $item = $(this);
                        var windowManager = tinymce.activeEditor.windowManager;
                        windowManager.confirm('<?php _e("Are you sure you want to delete this preset?", 'wpbrain') ?>', function (accepted) {
                            if (accepted) {
                                $item.addClass('has-errors').finish().fadeOut(400, function () {
                                    deleteElement.call();
                                    $sortable.trigger('sortupdate');
                                });
                            }
                        });
                    },
                });

                // Repeater Accordion
                $repeater.on('click', '.repeater-item-toggle', function (e) {
                    e.stopPropagation();

                    var $item = $(this).closest('.repeater-item');
                    var $open = $repeater.find('.repeater-item.open');

                    if ($open.length) {
                        $open.removeClass('open').children('.repeater-item-body').finish().slideUp(200);
                        if ($item.is($open)) {
                            return;
                        }
                    }
                    $item.addClass('open').children('.repeater-item-body').finish().slideDown(200);
                });

                // Accordion Title
                $repeater.on('keyup change', '.name-value', function () {
                    var $input = $(this);
                    var value = this.value.trim();
                    var title = value ? value : '[<?php _e("Untitled", 'wpbrain') ?>]';
                    $input.closest('.repeater-item').find('.item-name').text(title);
                });

                // RulesBuilder Setup, filters without presets
                var filters = wpbrain.filters_js.filter(function (filter) {
                    return /preset_[0-9a-f]{13}/.test(filter.id) === false;
                });

                function setupRulesBuilder() {
                    var $this = $(this);
                    $this.rulesBuilder({
                        allow_groups: 2,
                        allow_empty: false,
                        inputs_separator: '',
                        filters: filters
                    });

                    var rules = $this.siblings('.rules-value').val();
                    try {
                        rules = wpbrain.import(rules);
                        if (rules && rules.condition && rules.rules[0]) {
                            $this.rulesBuilder("setRules", rules);
                            var rules_text = $this.rulesBuilder('getText');
                            $this.closest('.repeater-item').find('.condition').html(rules_text);
                        }
                    } catch (e) {
                        // $this.rulesBuilder("getRules");
                        $this.closest('.repeater-item').addClass('has-errors');
                    }
                };
                $repeater.find('.rules-builder').each(setupRulesBuilder);

                // Save rules
                $('#wpbrain_settings_presets').submit(function (e) {
                    $repeater.find('.rules-builder').each(function () {
                        var $this = $(this);
                        try {
                            var rules = $this.rulesBuilder("getRules");
                        } catch (x) {
                            var rules = false;
                        }

                        var $item = $this.closest('.repeater-item');
                        if (rules && rules.condition) {
                            value = wpbrain.base64_encode(JSON.stringify(rules));
                            $item.removeClass('has-errors');
                        } else {
                            value = '';
                            $item.addClass('has-errors');
                            e.preventDefault();
                        }

                        $item.find('.rules-value').val(value);
                    });
                });

                // Export/Import
                $(window).load(function () {
                    var windowManager = tinymce.activeEditor.windowManager;
                    var popup = $('#wpbrain_presets_imexporter');

                    // Presets Shortcode
                    $repeater.find('[data-shortcode]').each(function () {
                        var $btn = $(this);
                        var id = $btn.data('shortcode');
                        if (id) {
                            var shortcode = '[wp_brain_if "' + id + '"]...[/wp_brain_if]';
                            $btn.on('click', function (e) {
                                windowManager.alert(shortcode);
                                e.preventDefault();
                            });
                        }
                    });

                    // Presets Exporter
                    $repeater.find('[data-export]').each(function () {
                        var $btn = $(this);
                        var title = $btn.is('button') ? "<?php _e("Export All Presets", 'wpbrain') ?>" : "<?php _e("Export Preset", 'wpbrain') ?>";
                        var data = $btn.data('export');

                        if (data) {
                            $btn.on('click', function (e) {
                                var win = windowManager.open({
                                    html: popup.html(),
                                    title: title,
                                    width: 800,
                                    height: 400,
                                    buttons: [
                                        {
                                            text: '<?php _e("Close", 'wpbrain') ?>',
                                            subtype: 'primary',
                                            onclick: function () {
                                                win.close();
                                            }
                                        }
                                    ]
                                });

                                var textarea = $('textarea', win.getEl());
                                textarea.val(data);
                                textarea.prop('readonly', true);
                                textarea.select();

                                e.preventDefault();
                            });
                        }
                    });

                    // Prsets Importer
                    $repeater.find('[data-import]').on('click', function (e) {
                        var win, textarea;
                        var $btn = $(this);

                        win = windowManager.open({
                            html: popup.html(),
                            title: "<?php _e("Import Presets", 'wpbrain') ?>",
                            width: 800,
                            height: 400,
                            buttons: [
                                {
                                    text: '<?php _e("Override", 'wpbrain') ?>',
                                    subtype: 'danger',
                                    onclick: function () {
                                        windowManager.confirm(
                                            '<?php _e("Are you sure you want to override existsing presets?", 'wpbrain') ?>',
                                            function (accepted) {
                                                if (accepted) {
                                                    importPressets(win, textarea, true);
                                                }
                                            },
                                            this
                                        );
                                    }
                                },
                                {
                                    text: '<?php _e("Import", 'wpbrain') ?>',
                                    subtype: 'primary',
                                    onclick: function () {
                                        importPressets(win, textarea);
                                    }
                                }
                            ]
                        });

                        textarea = $('textarea', win.getEl());
                        textarea.focus();

                        e.preventDefault();
                    });

                    function importPressets(win, textarea, override) {
                        // Validate data
                        var data = textarea.val();
                        var presets = wpbrain.import(data);
                        if (!presets || !$.isArray(presets[0]) || !presets[0].length == 2) {
                            windowManager.alert('<?php _e("Invalid data provided!", 'wpbrain') ?>');
                            return;
                        }

                        // Prepare items list
                        var list = [];
                        presets.forEach(function (preset) {
                            list.push({
                                name: preset[0],
                                rules: preset[1],
                            });
                        });

                        // Add items
                        doing_import = true;
                        if (override) {
                            $repeater.setList(list);
                        } else {
                            $repeater.addList(list);
                        }
                        doing_import = false;

                        // Close the importer dialog
                        win.close();
                    }
                });

            }(jQuery);
        </script>
    </div>

    <div style="display:none;">
        <?php wp_editor('', uniqid(), array('quicktags' => 0, 'media_buttons' => 0, 'teeny' => 1)) ?>
    </div>

    <script type="text/javascript">
        !function ($) {
            // Open chosen select on focus
            $(document).on('focus', '.chosen-select > select', function (e) {
                var $this = $(this);
                setTimeout(function () {
                    $this.trigger('chosen:open');
                }, 0);
            });

            // Dirty forms notification before window close
            $(window).on('load', function () {
                setTimeout(function () {
                    $('form[data-ays]').ays();
                }, 100);
            });
        }(jQuery);
    </script>
</div><!-- .wrap -->

