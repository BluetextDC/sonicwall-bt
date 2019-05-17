<h2><?php _e( 'General', 'wicked-folders' ); ?></h2>
<table class="form-table">
    <tr>
        <th scope="row">
            <?php _e( 'Enable folders for:', 'wicked-folders' ); ?>
        </th>
        <td>
            <?php if ( $is_pro_active && $attachment_post_type ) : ?>
                <label>
                    <input type="checkbox" name="post_type[]" value="<?php echo $attachment_post_type->name; ?>"<?php if ( in_array( $attachment_post_type->name, $enabled_posts_types ) ) echo ' checked="checked"'; ?>/>
                    <?php echo $attachment_post_type->label; ?>
                </label>
                <br />
            <?php endif; ?>
            <?php foreach ( $post_types as $post_type ) : ?>
                <?php
                    if ( 'attachment' == $post_type->name ) continue;
                    if ( 'acf' == $post_type->name && ! $is_pro_active ) continue;
                    if ( 'product' == $post_type->name && $is_woocommerce_active && ! $is_pro_active ) continue;
                    if ( 'shop_order' == $post_type->name && $is_woocommerce_active && ! $is_pro_active ) continue;
                    if ( 'shop_coupon' == $post_type->name && $is_woocommerce_active && ! $is_pro_active ) continue;
                    if ( ! $post_type->show_ui ) continue;
                ?>
                <label>
                    <input type="checkbox" name="post_type[]" value="<?php echo $post_type->name; ?>"<?php if ( in_array( $post_type->name, $enabled_posts_types ) ) echo ' checked="checked"'; ?>/>
                    <?php echo $post_type->label; ?>
                </label>
                <br />
            <?php endforeach; ?>
            <!--<p class="description"><?php _e( 'Control which post types folders are enabled for.', 'wicked-folders' ); ?></p>-->
            <?php if ( ! $is_pro_active && Wicked_Folders::is_upsell_enabled() ) : ?>
                <?php if ( $is_woocommerce_active ) : ?>
                    <br />
                    <p><?php _e( '<a href="https://wickedplugins.com/plugins/wicked-folders/?utm_source=core_settings&utm_campaign=wicked_folders&utm_content=post_types" target="_blank">Upgrade to Wicked Folders Pro</a> to manage media, WooCommerce products, orders and coupons using folders!' ); ?></p>
                    <?php if ( $attachment_post_type ) : ?>
                        <label>
                            <input type="checkbox" name="post_type[]" value="<?php echo $attachment_post_type->name; ?>" disabled="disabled" />
                            <?php echo $attachment_post_type->label; ?>
                        </label>
                        <br />
                    <?php endif; ?>
                    <?php foreach ( $post_types as $post_type ) : ?>
                        <?php
                            if ( 'attachment' == $post_type->name ) continue;
                            if ( ! ( 'product' == $post_type->name || 'shop_order' == $post_type->name || 'shop_coupon' == $post_type->name ) ) continue;
                            if ( ! $post_type->show_ui ) continue;
                        ?>
                        <label>
                            <input type="checkbox" name="post_type[]" value="<?php echo $post_type->name; ?>" disabled="disabled" />
                            <?php echo $post_type->label; ?>
                        </label>
                        <br />
                    <?php endforeach; ?>
                <?php elseif ( $attachment_post_type ) : ?>
                    <label>
                        <input type="checkbox" name="post_type[]" value="<?php echo $attachment_post_type->name; ?>" disabled="disabled" />
                        <?php echo $attachment_post_type->label; ?>
                        <em>(<?php _e( '<a href="https://wickedplugins.com/plugins/wicked-folders/?utm_source=core_settings&utm_campaign=wicked_folders&utm_content=media_post_type" target="_blank">Upgrade to Wicked Folders Pro</a> to manage media using folders' ); ?>)</em>
                    </label>
                    <br />
                <?php endif; ?>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <th scope="row">
            &nbsp;
        </th>
        <td>
            <label>
                <input type="checkbox" name="include_children" value="1"<?php if ( $include_children ) echo ' checked="checked"'; ?>/>
                <?php _e( 'Include items from child folders', 'wicked-folders' ); ?>
                <span class="dashicons dashicons-editor-help" title="<?php _e( "When unchecked (default) and a folder is selected, only items assigned to that folder will be displayed.  When checked, items in the selected folder *and* items in any of the folder's child folders will be displayed.  Please note: this setting does not apply to media.", 'wicked-folders' ); ?>"></span>
            </label>
        </td>
    </tr>
    <tr>
        <th scope="row">
            &nbsp;
        </th>
        <td>
            <label>
                <input type="checkbox" name="enable_folder_pages" value="1"<?php if ( $enable_folder_pages ) echo ' checked="checked"'; ?>/>
                <?php _e( 'Enable legacy folder pages', 'wicked-folders' ); ?>
                <span class="dashicons dashicons-editor-help" title="<?php _e( "Folders are now displayed directly on post list pages in the admin.  Enable this option if you would still like to use the original folder browser pages.  Enabling this option will add a 'Folders' menu item to the admin menu for each post type that folders are enabled for.", 'wicked-folders' ); ?>"></span>
            </label>
        </td>
    </tr>
</table>
<h2><?php _e( 'Dynamic Folders', 'wicked-folders' ); ?></h2>
<p><?php _e( 'Dynamic folders are generated on the fly based on your content.  They are useful for finding content based on things like date, author, etc.', 'wicked-folders' ); ?></p>
<table class="form-table">
    <tr>
        <th scope="row">
            <?php _e( 'Enable dynamic folders for:', 'wicked-folders' ); ?>
        </th>
        <td>
            <?php if ( $is_pro_active && $attachment_post_type ) : ?>
                <label>
                    <input type="checkbox" name="dynamic_folder_post_type[]" value="<?php echo $attachment_post_type->name; ?>"<?php if ( in_array( $attachment_post_type->name, $dynamic_folders_enabled_posts_types ) ) echo ' checked="checked"'; ?>/>
                    <?php echo $attachment_post_type->label; ?>
                </label>
                <br />
            <?php endif; ?>
            <?php foreach ( $post_types as $post_type ) : ?>
                <?php
                    if ( 'attachment' == $post_type->name ) continue;
                    if ( 'product' == $post_type->name && $is_woocommerce_active && ! $is_pro_active ) continue;
                    if ( 'shop_order' == $post_type->name && $is_woocommerce_active && ! $is_pro_active ) continue;
                    if ( 'shop_coupon' == $post_type->name && $is_woocommerce_active && ! $is_pro_active ) continue;
                    if ( ! $post_type->show_ui ) continue;
                ?>
                <label>
                    <input type="checkbox" name="dynamic_folder_post_type[]" value="<?php echo $post_type->name; ?>"<?php if ( in_array( $post_type->name, $dynamic_folders_enabled_posts_types ) ) echo ' checked="checked"'; ?><?php //if ( ! in_array( $post_type->name, $enabled_posts_types ) ) echo ' disabled="disabled"'; ?>/>
                    <?php echo $post_type->label; ?>
                </label>
                <br />
            <?php endforeach; ?>
            <p class="description"><?php _e( 'Control which post types dynamic folders are enabled for.', 'wicked-folders' ); ?></p>
        </td>
    </tr>
    <?php /* ?>
    <th scope="row">
        <?php _e( 'Tree View', 'wicked-folders' ); ?>
    </th>
    <td>
        <label>
            <input type="checkbox" name="show_folder_contents_in_tree_view" value="1"<?php if ( $show_folder_contents_in_tree_view ) echo ' checked="checked"'; ?>/>
            <?php _e( 'Show folder contents in tree view', 'wicked-folders' ); ?>
        </label>
        <p class="description"><?php _e( "When checked, the tree view will display each folder's items in addition to its sub folders.", 'wicked-folders' ); ?></p>
    </td>
    <?php */ ?>
</table>
<?php if ( $is_pro_active ) : ?>
    <h2><?php _e( 'Media', 'wicked-folders' ); ?></h2>
    <table class="form-table">
        <tr>
            <th scope="row">
                <?php //_e( 'Sync folder upload dropdown', 'wicked-folders' ); ?>
            </th>
            <td>
                <label>
                    <input type="checkbox" name="sync_upload_folder_dropdown" value="1"<?php if ( $sync_upload_folder_dropdown ) echo ' checked="checked"'; ?>/>
                    <?php _e( 'Sync folder upload dropdown', 'wicked-folders' ); ?>
                    <span class="dashicons dashicons-editor-help" title="<?php _e( 'When checked, the dropdown that lets you to choose which folder to assign new uploads to will change as you browse folders and default to the currently selected folder. If left unchecked, the dropdown will default to no folder selected.', 'wicked-folders' ); ?>"></span>
                </label>
            </td>
        </tr>
        <tr>
            <th scope="row">
                &nbsp;
            </th>
            <td>
                <label>
                    <input type="checkbox" name="include_attachment_children" value="1"<?php if ( $include_attachment_children ) echo ' checked="checked"'; ?>/>
                    <?php _e( 'Include media from child folders', 'wicked-folders' ); ?>
                    <span class="dashicons dashicons-editor-help" title="<?php _e( "When unchecked (default) and a folder is selected, only media assigned to that folder will be displayed.  When checked, media in the selected folder *and* media in any of the folder's child folders will be displayed.", 'wicked-folders' ); ?>"></span>
                </label>
            </td>
        </tr>
    </table>
<?php endif; ?>

<?php if ( $is_pro_active && ! is_multisite() ) : ?>
    <h2><?php _e( 'Wicked Folders Pro', 'wicked-folders' ); ?></h2>
    <table class="form-table">
        <tr>
            <th scope="row">
                <label for="wicked-folders-pro-license-key"><?php _e( 'License Key', 'wicked-folders' ); ?></label>
            </th>
            <td>
                <input type="text" id="wicked-folders-pro-license-key" class="regular-text" name="wicked_folders_pro_license_key" value="<?php echo $license_key; ?>" />
                <div><?php echo $license_status; ?></div>
            </td>
        </tr>
    </table>
<?php endif; ?>
<p class="submit">
    <input name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>" type="submit" />
</p>
