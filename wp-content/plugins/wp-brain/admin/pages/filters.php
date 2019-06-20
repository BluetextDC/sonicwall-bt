<div class="wrap erropix-ui">
    <h2><?php _e("Custom Filters", 'wpbrain') ?></h2>

    <div id="wpbrain_settings">
        <form id="wpbrain_settings_filters" method="POST" action="<?php echo admin_url('admin-post.php') ?>" data-ays>
            <?php wp_nonce_field('wpbrain_settings_save_filters'); ?>
            <input type="hidden" name="action" value="wpbrain_settings_save_filters">

            <div class="repeater clearfix">
                <script type="text/html-template" data-repeater-template>
                    <?php
                    $index = 0;
                    $filter = array(
                        'name' => '',
                        'source' => '',
                        'type' => '',
                        'key' => '',
                        'export' => '',
                    );
                    include('filters/filter.php');
                    ?>
                </script>
                <div class="repeater-header clearfix">
                    <div class="column column-order"><?php _e("Order", 'wpbrain') ?></div>
                    <div class="column column-name"><?php _e("Name", 'wpbrain') ?></div>
                </div>
                <div class="repeater-list" data-repeater-list="filters">
                    <?php
                    $export = '';
                    if ($filters) {
                        $index = 1;
                        $export = array();
                        foreach ($filters as $filter) {
                            include('filters/filter.php');
                            $export[] = array_values($filter);
                            $index++;
                        }
                        $export = self::export($export);
                    }
                    ?>
                </div>

                <div class="settings-buttons clearfix">
                    <div class="float-left">
                        <button type="button" class="btn btn-blue" data-repeater-create><?php _e("Add New", 'wpbrain') ?></button>
                    </div>
                    <div class="float-right">
                        <button type="button" class="btn btn-gray" data-import><?php _e("Import", 'wpbrain') ?></button>
                        <?php if ($export): ?>
                            <button type="button" class="btn btn-gray" data-export="<?php echo $export ?>"><?php _e("Export", 'wpbrain') ?></button>
                        <?php endif ?>
                        <button type="submit" class="btn btn-green"><?php _e("Save Filters", 'wpbrain') ?></button>
                    </div>
                </div>
            </div>
            <div id="wpbrain_filters_imexporter" class="hidden">
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
                            $repeater.find('.chosen-select select').chosen({
                                disable_search: true
                            });
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
                        $item.show();
                        $item.find('input.name-value').trigger('change');
                        $sortable.trigger('sortupdate');
                        $item.find('.chosen-select select').chosen({
                            disable_search: true
                        });
                        if (!doing_import) {
                            $item.find('.repeater-item-toggle').eq(0).trigger('click');
                        }
                    },
                    hide: function (deleteElement) {
                        var $item = $(this);
                        var windowManager = tinymce.activeEditor.windowManager;
                        windowManager.confirm('<?php _e("Are you sure you want to delete this filter?", 'wpbrain') ?>', function (accepted) {
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

                // Export/Import
                $(window).load(function () {
                    var windowManager = tinymce.activeEditor.windowManager;
                    var popup = $('#wpbrain_filters_imexporter');

                    // Presets Exporter
                    $repeater.find('[data-export]').each(function () {
                        var $btn = $(this);
                        var title = $btn.is('button') ? "<?php _e("Export All Filters", 'wpbrain') ?>" : "<?php _e("Export Filter", 'wpbrain') ?>";
                        var data = $btn.data('export');

                        if (data) {
                            $btn.on('click', function () {
                                var win, textarea;

                                win = windowManager.open({
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

                                textarea = $('textarea', win.getEl());
                                textarea.val(data);
                                textarea.prop('readonly', true);
                                textarea.select();
                            });
                        }
                    });

                    // Filters Importer
                    $repeater.find('[data-import]').on('click', function () {
                        var win, textarea;

                        win = windowManager.open({
                            html: popup.html(),
                            title: "<?php _e("Import Filters", 'wpbrain') ?>",
                            width: 800,
                            height: 400,
                            buttons: [
                                {
                                    text: '<?php _e("Override", 'wpbrain') ?>',
                                    subtype: 'danger',
                                    onclick: function () {
                                        windowManager.confirm(
                                            '<?php _e("Are you sure you want to override existsing filters?", 'wpbrain') ?>',
                                            function (accepted) {
                                                if (accepted) {
                                                    importFilters(win, textarea, true);
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
                                        importFilters(win, textarea);
                                    }
                                }
                            ]
                        });

                        textarea = $('textarea', win.getEl());
                        textarea.focus();
                    });

                    function importFilters(win, textarea, override) {
                        // Validate data
                        var data = textarea.val();
                        var filters = wpbrain.import(data);
                        if (!filters || !$.isArray(filters[0]) || !filters[0].length == 4) {
                            windowManager.alert('<?php _e("Invalid data provided!", 'wpbrain') ?>');
                            return;
                        }

                        // Prepare items list
                        var list = [];
                        filters.forEach(function (filter) {
                            list.push({
                                name: filter[0],
                                source: filter[1],
                                type: filter[2],
                                key: filter[3],
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

            }(jQuery)
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
