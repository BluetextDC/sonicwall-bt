<div class="repeater-item clearfix" data-repeater-item>
    <div class="repeater-item-header clearfix">
        <div class="column column-order">
            <span class="repeater-item-order"><?php echo $index ?></span>
        </div>
        <div class="column column-name">
            <strong>
                <a class="repeater-item-toggle item-name"><?php echo $filter['name'] ? $filter['name'] : '[Untitled]' ?></a>
            </strong>
            <div class="repeater-item-actions">
                <a class="repeater-item-toggle"><?php _e("Edit", 'wpbrain') ?></a> &dash;
                <?php if ($filter['export']): ?>
                    <a class="repeater-item-export" data-export="<?php echo $filter['export'] ?>"><?php _e("Export", 'wpbrain') ?></a> &dash;
                <?php endif ?>
                <a class="repeater-item-delete" data-repeater-delete><?php _e("Delete", 'wpbrain') ?></a>
            </div>
        </div>
    </div>
    <div class="repeater-item-body">
        <table class="settings-table">
            <tbody>
            <tr>
                <th>
                    <label for=""><?php _e("Filter Name", 'wpbrain') ?></label>
                    <p><?php _e("This name is used as label in the filters dropdown list", 'wpbrain') ?></p>
                </th>
                <td>
                    <input type="text" class="data-field name-value" name="name" value="<?php echo $filter['name'] ?>"
                           required>
                </td>
            </tr>
            <tr>
                <th>
                    <label for=""><?php _e("Filter Value Source", 'wpbrain') ?></label>
                    <p><?php _e("Source from where the validator will fetch the correct value", 'wpbrain') ?></p>
                </th>
                <td>
                    <div class="chosen-select">
                        <select class="data-field source-value" name="source" required>
                            <?php
                            self::html_options(
                                $sources,
                                $filter['source']
                            )
                            ?>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <label for=""><?php _e("Filter Value Type", 'wpbrain') ?></label>
                    <p><?php _e("The type of value (i.e. string, integer, date, etc)", 'wpbrain') ?></p>
                </th>
                <td>
                    <div class="chosen-select">
                        <select class="data-field type-value" name="type" required>
                            <?php
                            self::html_options(
                                array(
                                    "string" => __("String", 'wpbrain'),
                                    "integer" => __("Integer", 'wpbrain'),
                                    "double" => __("Double", 'wpbrain'),
                                    "datetime" => __("Date & Time", 'wpbrain'),
                                    "date" => __("Date", 'wpbrain'),
                                    "time" => __("Time", 'wpbrain'),
                                    "boolean" => __("Boolean", 'wpbrain'),
                                    "version" => __("Version", 'wpbrain'),
                                ),
                                $filter['type']
                            )
                            ?>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <label for=""><?php _e("Filter Value Key", 'wpbrain') ?></label>
                    <p><?php _e("The identifier of the value within the chosen data source", 'wpbrain') ?></p>
                </th>
                <td>
                    <input type="text" class="data-field key-value" name="key" value="<?php echo $filter['key'] ?>"
                           required>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
