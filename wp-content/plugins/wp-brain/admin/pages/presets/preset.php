<div class="repeater-item clearfix" data-repeater-item>
    <input type="hidden" name="id" value="<?php echo $preset['id'] ?>">

    <div class="repeater-item-header clearfix">
        <div class="column column-order">
            <span class="repeater-item-order"><?php echo $index ?></span>
        </div>
        <div class="column column-name">
            <strong>
                <a class="repeater-item-toggle item-name"><?php echo $preset['name'] ? $preset['name'] : '[Untitled]' ?></a>
            </strong>
            <div class="repeater-item-actions">
                <a class="repeater-item-toggle"><?php _e("Edit", 'wpbrain') ?></a> &dash;
                <?php if ($preset['id']): ?>
                    <a class="repeater-item-shortcode" data-shortcode="<?php echo $preset['id'] ?>"><?php _e("Shortcode", 'wpbrain') ?></a> &dash;
                <?php endif ?>
                <?php if ($preset['export']): ?>
                    <a class="repeater-item-export" data-export="<?php echo $preset['export'] ?>"><?php _e("Export", 'wpbrain') ?></a> &dash;
                <?php endif ?>
                <a class="repeater-item-delete" data-repeater-delete><?php _e("Delete", 'wpbrain') ?></a>
            </div>
        </div>
        <div class="column column-condition">
            <div class="condition"></div>
        </div>
    </div>
    <div class="repeater-item-body">
        <table class="settings-table">
            <tbody>
            <tr>
                <th><label for=""><?php _e("Preset Name", 'wpbrain') ?></label></th>
                <td>
                    <input type="text" name="name" class="data-field name-value" value="<?php echo $preset['name'] ?>"
                           placeholder="Preset Name" required>
                </td>
            </tr>
            <tr>
                <th><label><?php _e("Preset Condition", 'wpbrain') ?></label></th>
                <td>
                    <input type="hidden" name="rules" class="data-field rules-value"
                           value="<?php echo $preset['rules'] ?>">
                    <div class="rules-builder clearfix" data-repeater-noindex></div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
