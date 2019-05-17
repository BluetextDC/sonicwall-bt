<div class="wrap">
    <h1><?php _e( 'Wicked Folders Settings', 'wicked-folders' ); ?></h1>
    <div class="wicked-settings wicked-clearfix">
        <div class="wicked-left">
            <form action="" method="post">
                <input type="hidden" name="action" value="wicked_folders_save_settings" />
                <?php wp_nonce_field( 'wicked_folders_save_settings', 'nonce' ); ?>
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
                <p class="submit">
                    <input name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes' ); ?>" type="submit" />
                </p>
            </form>
        </div>
        <div class="wicked-right">
            <div class="wicked-logo">
                <a href="https://wickedplugins.com/" target="_blank"><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>plugins/wicked-folders/images/wicked-plugins-logo.png" alt="Wicked Plugins" width="300" height="223" /></a>
            </div>
            <div class="wicked-rate">
                <h4><?php _e( 'Thanks for using Wicked Folders!', 'wicked-folders' ); ?></h4>
                <p>Please <a href="https://wordpress.org/support/plugin/wicked-folders/reviews/#new-post" target="_blank">rate Wicked Folders<br /><span class="stars">★★★★★</span><span class="screen-reader-text">five stars</span></a><br /> to help spread the word!</p>
            </div>
            <hr />
            <h4><?php _e( 'About Wicked Plugins', 'wicked-folders' ); ?></h4>
            <p><?php _e( 'Wicked Plugins specializes in crafting high-quality, reliable plugins that extend WordPress in powerful ways while being simple and intuitive to use.  We’re full-time developers who know WordPress inside and out and our customer happiness engineers offer friendly support for all our products.  <a href="https://wickedplugins.com/" target="_blank">Visit our website</a> to learn more about us.', 'wicked-folders' ); ?>
        </div>
    </div>
</div>
