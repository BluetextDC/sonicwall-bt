<?php
/* Prevent direct access */
defined('ABSPATH') or die("You can't access this file directly.");

if (isset($_GET) && isset($_GET['asp_sid'])) {
    include('search.php');
    return;
}
$_comp = wpdreamsCompatibility::Instance();
?>
<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) . 'settings/assets/options_search.css?v='.ASP_CURR_VER; ?>" />
<div id="wpdreams" class='wpdreams wrap<?php echo isset($_COOKIE['asp-accessibility']) ? ' wd-accessible' : ''; ?>'>

    <?php if (defined('ASL_PATH')): ?>
        <p class="errorMsg">
            <?php echo __('Warning: Please deactivate the Ajax Search Lite to assure every PRO feature works properly.', 'ajax-search-pro'); ?>
        </p>
    <?php endif; ?>

    <?php if ( $_comp->has_errors() ): ?>
        <div class="wpdreams-box errorbox">
            <p class='errors'>
            <?php echo sprintf( __('Possible incompatibility! Please go to the
                 <a href="%s">error check</a> page to see the details and solutions!', 'ajax-search-pro'),
                get_admin_url() . 'admin.php?page=asp_compatibility_settings'
            ); ?>
            </p>
        </div>
    <?php endif; ?>

	<?php if ( wd_asp()->updates->needsUpdate() ): ?>
        <p class='infoMsgBox'>
            <?php echo sprintf( __('Version <strong>%s</strong> is available.', 'ajax-search-pro'),
                wd_asp()->updates->getVersionString() ); ?>
            <?php echo __('Download the new version from Codecanyon.', 'ajax-search-pro'); ?>
            <a target="_blank" href="https://documentation.ajaxsearchpro.com/update_notes.html">
                <?php echo __('How to update?', 'ajax-search-pro'); ?>
            </a>
        </p>
	<?php endif; ?>

    <?php if ( !wd_asp()->db->exists('main', true) ): ?>
        <div id="wpdreams" class='wpdreams wrap'>
            <div class="wpdreams-box">
                <p class="errorMsg">
                    <?php echo __('One or more plugin tables are appear to be missing from the database.', 'ajax-search-pro'); ?>
                    <?php echo sprintf( __('Please check <a href="%s" target="_blank">this article</a> to resolve the issue.', 'ajax-search-pro'),
                        'https://wp-dreams.com/go/?to=kb-asp-missing-tables' ); ?>
                </p>
                <p class="errorMsg">
                    <?php echo __('Please be <strong>very careful</strong>, before making any changes to your database. Make sure to have a full database back-up, just in case!', 'ajax-search-pro'); ?>
                </p>
                <p>
                    <fieldset>
                        <legend><?php echo __('Copy this SQL code in your database editor tool to create them manually', 'ajax-search-pro'); ?></legend>
                        <textarea style="width:100%;height:480px;"><?php echo wd_asp()->db->create(); ?></textarea>
                    </fieldset>
                </p>
            </div>
        </div>
    <?php else: ?>

    <div class="wpdreams-box" style="overflow: visible; float: left;">
        <form name="add-slider" action="" method="POST">
            <fieldset>
                <legend><?php echo __('Create a new search instance', 'ajax-search-pro'); ?></legend>
                <?php
                $new_slider = new wpdreamsText("addsearch", __('Search form name:', 'ajax-search-pro'), "", array(array("func" => "wd_isEmpty", "op" => "eq", "val" => false)), "Please enter a valid form name!");
                ?>
                <input name="submit" type="submit" value="Add"/>
                <?php
                if (isset($_POST['addsearch']) && !$new_slider->getError()) {

                    $id = wd_asp()->instances->add( $_POST['addsearch'] );

                    if ( $id !== false ) {
                        asp_generate_the_css();
                        echo "<div class='successMsg'>" . __('Search Form Successfuly added!', 'ajax-search-pro') . "</div>";
                    } else {
                        echo "<div class='errorMsg'>" . __('The search form was not created. Please contact support.', 'ajax-search-pro') . "</div>";
                    }
                }
                if (isset($_POST['instance_new_name'])
                    && isset($_POST['instance_id'])
                ) {
                    if ($_POST['instance_new_name'] != ''
                        && strlen($_POST['instance_new_name']) > 0
                    ) {
                        if ( wd_asp()->instances->rename($_POST['instance_new_name'], $_POST['instance_id']) !== false )
                            echo "<div class='infoMsg'>" . __('Form name changed!', 'ajax-search-pro') . "</div>";
                        else
                            echo "<div class='errorMsg'>" . __('Failure. Search could not be renamed.', 'ajax-search-pro') . "</div>";
                    } else {
                        echo "<div class='errorMsg'>" . __('Failure. Form name must be at least 1 character long', 'ajax-search-pro') . "</div>";
                    }
                }
                if (isset($_POST['instance_copy_id'])) {
                    if ($_POST['instance_copy_id'] != '') {
                        if ( wd_asp()->instances->duplicate($_POST['instance_copy_id']) !== false ) {
                            asp_generate_the_css();
                            echo "<div class='infoMsg'>" . __('Form duplicated!', 'ajax-search-pro') . "</div>";
                        } else {
                            echo "<div class='errorMsg'>" . __('Failure. Search form could not be duplicated.', 'ajax-search-pro') . "</div>";
                        }
                    } else {
                        echo "<div class='errorMsg'>" . __('Failure :(', 'ajax-search-pro') . "</div>";
                    }
                }
                ?>
            </fieldset>
        </form>
        <?php
        if (isset($_POST['delete'])) {
            $_POST['delete'] = $_POST['delete'] + 0;
            wd_asp()->instances->delete( $_POST['delete'] );
            asp_del_file("search" . $_POST['delete'] . ".css");
            asp_generate_the_css();
        }
        if ( isset($_POST['asp_st_override']) ) {
            update_option("asp_st_override", $_POST['asp_st_override']);
        }
        if ( isset($_POST['asp_woo_override']) ) {
            update_option("asp_woo_override", $_POST['asp_woo_override']);
        }

        $searchforms = wd_asp()->instances->getWithoutData();
        ?>
        <?php if ( !empty($searchforms) ): ?>
        <?php
        $asp_st_override = get_option("asp_st_override", -1);
        $asp_woo_override = get_option("asp_woo_override", -1);
        ?>
        <br>
        <form name="sel-asp_st_override" action="" method="POST">
        <fieldset>
            <legend><?php echo __('Theme search bar replace', 'ajax-search-pro'); ?></legend>
            <label><?php echo __('Replace the default theme search with:', 'ajax-search-pro'); ?> </label>
            <select name="asp_st_override" style="max-width:90px;">
                    <option value="-1"><?php echo __('None', 'ajax-search-pro'); ?></option>
                <?php foreach ($searchforms as $_searchform): ?>
                    <option value="<?php echo $_searchform["id"]; ?>"
                        <?php echo $asp_st_override == $_searchform["id"] ? " selected='selected'" : ""; ?>>
                        <?php echo $_searchform["name"]; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (class_exists("WooCommerce")): ?>
            <?php echo __('and the <strong>WooCommerce</strong> search with:', 'ajax-search-pro'); ?>
            <select name="asp_woo_override" style="max-width:90px;">
                <option value="-1"><?php echo __('None', 'ajax-search-pro'); ?></option>
                <?php foreach ($searchforms as $_searchform): ?>
                    <option value="<?php echo $_searchform["id"]; ?>"
                        <?php echo $asp_woo_override == $_searchform["id"] ? " selected='selected'" : ""; ?>>
                        <?php echo $_searchform["name"]; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
            <span style='
                            font-family: dashicons;
                            content: "\f348";
                            width: 24px;
                            height: 24px;
                            line-height: 24px;
                            font-size: 24px;
                            display: inline-block;
                            /* position: static; */
                            vertical-align: middle;
                            color: #167DB9;' class="dashicons dashicons-info">
            <a href="#" style="display:block; width:24px; height: 24px; margin-top: -24px;"
                class="tooltip-bottom" data-tooltip="<?php echo esc_attr__('This might not work with all themes. If the default theme search bar is still visible after selection, then the only way is to replace the search within the theme code.', 'ajax-search-pro'); ?>"></a>
            </span>
            <input name="submit" type="submit" value="Save"/>
        </fieldset>
        </form>
        <?php endif; ?>
    </div>
    <div id="asp-options-search">
        <a class="wd-accessible-switch" href="#"><?php echo isset($_COOKIE['asp-accessibility']) ? 'DISABLE ACCESSIBILITY' : 'ENABLE ACCESSIBILITY'; ?></a>
    </div>
    <div class="clear"></div>

    <?php

    $i = 0;
    if (is_array($searchforms))
        foreach ($searchforms as $search) {
            $i++;
            // Needed for the tabindex for the CSS :focus to work with div
            ?>
            <div class="wpdreams-box" tabindex="<?php echo $i; ?>">
                <div class="slider-info">
                    <a href='<?php echo get_admin_url() . "admin.php?page=asp_settings"; ?>&asp_sid=<?php echo $search['id']; ?>'><img
                            title="<?php esc_attr__('Click on this icon for search settings!', 'ajax-search-pro'); ?>"
                            src="<?php echo plugins_url('/settings/assets/icons/settings.png', __FILE__) ?>"
                            class="settings" searchid="<?php echo $search['id']; ?>"/></a>
                    <img title="Click here if you want to delete this search!"
                         src="<?php echo plugins_url('/settings/assets/icons/delete.png', __FILE__) ?>" class="delete"/>

                    <form name="polaroid_slider_del_<?php echo $search['id']; ?>" action="" style="display:none;"
                          method="POST">
                        <input type="hidden" name="delete" value=<?php echo $search['id']; ?>>
                    </form>
                <span class="wpd_instance_name"><?php
                  echo $search['name'];
                  ?>
                </span>

                <form style="display: inline" name="instance_new_name_form" class="instance_new_name_form"
                      method="post">
                    <input type="text" class="instance_new_name" name="instance_new_name"
                           value="<?php echo $search['name']; ?>">
                    <input type="hidden" name="instance_id" value="<?php echo $search['id']; ?>"/>
                    <img title="<?php esc_attr__('Click here to rename this form!', 'ajax-search-pro'); ?>"
                         src="<?php echo plugins_url('/settings/assets/icons/edit24x24.png', __FILE__) ?>"
                         class="wpd_instance_edit_icon"/>
                </form>
                <form style="display: inline" name="instance_copy_form" class="instance_copy_form"
                      method="post">
                    <input type="hidden" name="instance_copy_id" value="<?php echo $search['id']; ?>"/>
                    <img title="<?php esc_attr__('Click here to duplicate this form!', 'ajax-search-pro'); ?>"
                         src="<?php echo plugins_url('/settings/assets/icons/duplicate18x18.png', __FILE__) ?>"
                         class="wpd_instance_edit_icon"/>
                </form>
                <span style='float:right;'>
                 <label class="shortcode"><?php __('Quick shortcode:', 'ajax-search-pro'); ?></label>
                 <input type="text" class="quick_shortcode" value="[wd_asp id=<?php echo $search['id']; ?>]"
                        readonly="readonly"/>
                </span>
                </div>
                <div class="clear"></div>
            </div>
        <?php


        }
    ?>

    <?php endif; ?>
    <script>
    jQuery(function ($) {
        $('input.instance_new_name').on('focus', function () {
            $(this).parent().prev().css('display', 'none');
        }).blur(function () {
                $(this).parent().prev().css('display', '');
            });
        $('.instance_new_name_form').on('submit', function () {
            if (!confirm('<?php echo __('Do you want to change the name of this form?', 'ajax-search-pro'); ?>'))
                return false;
        });
        $('.instance_copy_form').on('submit', function () {
            if (!confirm('<?php echo __('Do you want to duplicate this form?', 'ajax-search-pro'); ?>'))
                return false;
        });
        $('.wpd_instance_edit_icon').on('click', function () {
            $(this).parent().submit();
        });
    });
    </script>
</div>

