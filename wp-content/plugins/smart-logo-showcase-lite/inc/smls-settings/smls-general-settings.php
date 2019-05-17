<?php
defined('ABSPATH') or die("No script kiddies please!");
global $post;
$post_id = $post->ID;
$smls_settings = get_post_meta($post_id, 'smls_settings', true);
//$this->print_array($smls_settings);
?>
<div class="smls-general-setting-wrapper">
    <div class="smls-setting-wrapper">
        <label><?php _e('Wrapper Width', SMLS_TD); ?></label>
        <div class="smls-setting-field">
            <input type="number" class="smls-wrapper-width" name="smls_settings[main_wrapper_width]" max="100" value="<?php
            if (!empty($smls_settings['main_wrapper_width'])) {
                echo esc_attr($smls_settings['main_wrapper_width']);
            } else {
                echo "100";
            }
            ?>"/>
            <p class="description">
                <?php _e('You can manage the main container or wrapper width of logo.', SMLS_TD); ?>
            </p>
            <p class="description">
                <?php _e('Note: If the logo images get cropped then decreased the wrapper width.', SMLS_TD); ?>
            </p>
        </div>
    </div>
    <div class="smls-extra-effects-wrap">
        <div class="smls-setting-wrapper">
            <label><?php _e('Title Settings', SMLS_TD); ?></label>
            <div class="smls-setting-field">
                <select name="smls_settings[logo_title_view]" class="smls-view-title-type">
                    <option value="title_overlay" <?php if (!empty($smls_settings['logo_title_view'])) selected($smls_settings['logo_title_view'], 'title_overlay'); ?>><?php _e('Show title in overlay', SMLS_TD) ?></option>
                    <option value="title_tooltip" <?php if (!empty($smls_settings['logo_title_view'])) selected($smls_settings['logo_title_view'], 'title_tooltip'); ?>><?php _e('Show title in tooltip', SMLS_TD) ?></option>
                    <option value="title_none" <?php if (!empty($smls_settings['logo_title_view'])) selected($smls_settings['logo_title_view'], 'title_none'); ?>><?php _e('Does not show title', SMLS_TD) ?></option>
                </select>
                <div class="smls-overlay-note"  <?php if (isset($smls_settings['grid_layout']) && $smls_settings['grid_layout'] == 'template-9') { ?> style="display:none;" <?php } else { ?> style="display:block;" <?php } ?>>
                    <p class="description">
                        <?php _e("If you have selected 'show title in overlay' , please select 'overlay effect' in below image effects options", SMLS_TD); ?>
                    </p>
                    <p class="description">
                        <?php _e("Please note 'Show title in tooltip' is not applicable for flipster layout.", SMLS_TD); ?>
                    </p>
                </div>
                <div class="smls-grid-9-note"  <?php if (isset($smls_settings['grid_layout']) && $smls_settings['grid_layout'] == 'template-9') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
                    <p class="description">
                        <?php _e("Please note show title in overlay is not applicable for grid template 9", SMLS_TD); ?>
                    </p>
                </div>
            </div>
        </div>
        <!-- hover effect
        -->
        <div class="smls-hover-outer-wrap">
            <div class="smls-setting-wrapper">
                <label><?php _e('Image Effects', SMLS_TD); ?></label>
                <div class="smls-setting-field">
                    <select name="smls_settings[logo_image_effects]" class="smls-image-effect-type">
                        <option value="overlay" <?php if (!empty($smls_settings['logo_image_effects'])) selected($smls_settings['logo_image_effects'], 'overlay'); ?>><?php _e('Overlay effect', SMLS_TD) ?></option>
                        <option value="hover" <?php if (!empty($smls_settings['logo_image_effects'])) selected($smls_settings['logo_image_effects'], 'hover'); ?>><?php _e('Hover effect', SMLS_TD) ?></option>
                    </select>
                    <p class="description">
                        <?php _e("To show full view and external link together please select overlay effect", SMLS_TD); ?>
                    </p>
                    <div class="smls-grid-9-note"  <?php if (isset($smls_settings['grid_layout']) && $smls_settings['grid_layout'] == 'template-9') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?>>
                        <p class="description">
                            <?php _e("Please note overlay effect is not applicable for grid template 9", SMLS_TD); ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="smls-hover-setting-wrap" <?php if (!isset($smls_settings['smls_show_hover']) || $smls_settings['smls_show_hover'] == 0) { ?>style="display: none;" <?php } else { ?> style="display: block;" <?php } ?>>
                <div class="smls-setting-wrapper">
                    <label><?php _e('Hover Type', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <select name="smls_settings[hover_type]" class="smls-hover-type">
                            <option value="type-1" <?php if (!empty($smls_settings['hover_type'])) selected($smls_settings['hover_type'], 'type-1'); ?>><?php _e('Grey Scale Effect 1', SMLS_TD) ?></option>
                            <option value="type-2" <?php if (!empty($smls_settings['hover_type'])) selected($smls_settings['hover_type'], 'type-2'); ?>><?php _e('Color Effect', SMLS_TD) ?></option>
                            <option value="type-3" <?php if (!empty($smls_settings['hover_type'])) selected($smls_settings['hover_type'], 'type-3'); ?>><?php _e('Zoom In Effect', SMLS_TD) ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="smls-without-filter-setting-wrap">
        <div class="smls-setting-wrapper">
            <label><?php _e('Layout', SMLS_TD); ?></label>
            <div class="smls-setting-field">
                <select name="smls_settings[logo_layout]" class="smls-layout-type">
                    <option value="select" <?php if (!empty($smls_settings['logo_layout'])) selected($smls_settings['logo_layout'], 'select'); ?>><?php _e('Select', SMLS_TD) ?></option>
                    <option value="grid" <?php if (!empty($smls_settings['logo_layout'])) selected($smls_settings['logo_layout'], 'grid'); ?>><?php _e('Grid', SMLS_TD) ?></option>
                    <option value="carousel" <?php if (!empty($smls_settings['logo_layout'])) selected($smls_settings['logo_layout'], 'carousel'); ?>><?php _e('Carousel', SMLS_TD) ?></option>
                </select>
            </div>
        </div>

        <!--Grid setting section-->
        <div class="smls-grid-setting-wrap">
            <div class="smls-grid-toogle-outer-wrap">
                <h3>
                    <?php _e('Grid Settings', SMLS_TD); ?>
                </h3>
                <span class="dashicons dashicons-arrow-down"></span>
            </div>
            <div class="smls-inner-toogle-grid" style="display: none;">
                <div class="smls-setting-wrapper">
                    <label><?php _e('Grid templates', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <select name="smls_settings[grid_layout]" class="smls-grid-template">
                            <?php for ($k = 1; $k <= 3; $k++) { ?>
                                <option value="template-<?php echo $k; ?>" <?php if (!empty($smls_settings['grid_layout'])) selected($smls_settings['grid_layout'], 'template-' . $k); ?>><?php _e('Template ', SMLS_TD) ?><?php echo $k; ?></option>

                            <?php } ?>
                        </select>
                        <div class="smls-grid-demo smls-preview-image">
                            <?php
                            for ($cnt = 1; $cnt <= 3; $cnt++) {
                                if (isset($smls_settings['grid_layout'])) {
                                    $option_value = $smls_settings['grid_layout'];
                                    $exploed_array = explode('-', $option_value);
                                    $cnt_num = $exploed_array[1];
                                    if ($cnt != $cnt_num) {
                                        $style = "style='display:none;'";
                                    } else {
                                        $style = '';
                                    }
                                }
                                ?>
                                <div class="smls-grid-common" id="smls-grid-demo-<?php echo $cnt; ?>" <?php if (isset($style)) echo esc_attr($style); ?>>
                                    <h4><?php _e('Template', SMLS_TD); ?> <?php echo $cnt; ?> <?php _e('Preview', SMLS_TD); ?></h4>
                                    <img src="<?php echo SMLS_IMG_DIR . '/demo/grid-preview/grid-' . $cnt . '.jpg' ?>"/>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="smls-grid-border-wrap">
                    <div class="smls-setting-wrapper">
                        <label><?php _e('Border Color', SMLS_TD); ?></label>
                        <div class="smls-setting-field">
                            <input type="text" class="smls-color-picker" name="smls_settings[grid_border_color]"  value="<?php
                            if (!empty($smls_settings['grid_border_color'])) {
                                echo esc_attr($smls_settings['grid_border_color']);
                            } else {
                                echo "#e9e9e9";
                            }
                            ?>"/>
                        </div>
                    </div>
                </div>
                <div class="smls-setting-wrapper">
                    <label><?php _e('Column In Desktop', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <select name="smls_settings[desktop_column]" class="smls-column-desktop">
                            <?php for ($k = 6; $k >= 2; $k--) { ?>
                                <option value="<?php echo $k; ?>" <?php if (!empty($smls_settings['desktop_column'])) selected($smls_settings['desktop_column'], $k); ?>><?php echo $k; ?></option>

                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="smls-setting-wrapper">
                    <label><?php _e('Column In Tablet', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <select name="smls_settings[tablet_column]" class="smls-column-tablet">
                            <?php for ($k = 2; $k <= 4; $k++) { ?>
                                <option value="<?php echo $k; ?>" <?php if (!empty($smls_settings['tablet_column'])) selected($smls_settings['tablet_column'], $k); ?>><?php echo $k; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="smls-setting-wrapper">
                    <label><?php _e('Column In Mobile', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <select name="smls_settings[mobile_column]" class="smls-column-mobile">
                            <?php for ($k = 1; $k <= 2; $k++) { ?>
                                <option value="<?php echo $k; ?>" <?php if (!empty($smls_settings['mobile_column'])) selected($smls_settings['mobile_column'], $k); ?>><?php echo $k; ?></option>

                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!---End of grid  setting wrap-->
        <div class="smls-carousel-setting-section">
            <div class="smls-carousel-outer-wrap">
                <h3><?php _e('Carousel Settings', SMLS_TD); ?></h3>
                <span class="dashicons dashicons-arrow-down"></span>
            </div>
            <div class="smls-carousel-inner-wrap" style="display:none;">
                <div class="smls-setting-wrapper">
                    <label><?php _e('Carousel templates', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <select name="smls_settings[carousel_layout]" class="smls-carousel-template">
                            <option value="template-1" <?php if (!empty($smls_settings['carousel_layout'])) selected($smls_settings['carousel_layout'], 'template-1'); ?>><?php _e('Template 1', SMLS_TD) ?></option>
                            <option value="template-2" <?php if (!empty($smls_settings['carousel_layout'])) selected($smls_settings['carousel_layout'], 'template-2'); ?>><?php _e('Template 2', SMLS_TD) ?></option>
                        </select>
                        <div class="smls-carousel-demo smls-preview-image">
                            <?php
                            for ($cnt = 1; $cnt <= 2; $cnt++) {
                                if (isset($smls_settings['carousel_layout'])) {
                                    $option_value = $smls_settings['carousel_layout'];
                                    $exploed_array = explode('-', $option_value);
                                    $cnt_num = $exploed_array[1];
                                    if ($cnt != $cnt_num) {
                                        $style = "style='display:none;'";
                                    } else {
                                        $style = '';
                                    }
                                }
                                ?>
                                <div class="smls-carousel-common" id="smls-carousel-demo-<?php echo $cnt; ?>" <?php if (isset($style)) echo $style; ?>>
                                    <h4><?php _e('Template', SMLS_TD); ?> <?php echo $cnt; ?> <?php _e('Preview', SMLS_TD); ?></h4>
                                    <img src="<?php echo SMLS_IMG_DIR . '/demo/carousal-preview/carousel-' . $cnt . '.jpg' ?>"/>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="smls-car-border-color" style="display: none;">
                    <div class="smls-setting-wrapper">
                        <label><?php _e('Border Color', SMLS_TD); ?></label>
                        <div class="smls-setting-field">
                            <input type="text" class="smls-car-bor-color smls-color-picker" name="smls_settings[car_border_color]"  value="<?php
                            if (!empty($smls_settings['car_border_color'])) {
                                echo esc_attr($smls_settings['car_border_color']);
                            } else {
                                echo "#eee";
                            }
                            ?>"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="smls-pager-setting-wrapper">
            <div class="smls-pager-outer-wrap">
                <h3><?php _e('Pager Settings', SMLS_TD); ?></h3>
                <span class="dashicons dashicons-arrow-down"></span>
            </div>
            <div class="smls-pager-inner-wrap" style="display:none;">
                <div class="smls-setting-wrapper">
                    <label><?php _e('Pager', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <select name="smls_settings[carousel_pager]" class="smls-carousel-pager">

                            <option value="true" <?php if (!empty($smls_settings['carousel_pager'])) selected($smls_settings['carousel_pager'], 'true'); ?>><?php _e('True', SMLS_TD) ?>
                            </option>
                            <option value="false" <?php if (!empty($smls_settings['carousel_pager'])) selected($smls_settings['carousel_pager'], 'false'); ?>><?php _e('False', SMLS_TD) ?>
                            </option>
                        </select>
                    </div>

                </div>
                <div class="smls-pager-hide-wrap">
                    <div class="smls-setting-wrapper">
                        <label><?php _e('Active Color', SMLS_TD); ?></label>
                        <div class="smls-setting-field">
                            <input type="text" class="smls-pager-active-color smls-color-picker" name="smls_settings[pager_active_color]"  value="<?php
                            if (!empty($smls_settings['pager_active_color'])) {
                                echo esc_attr($smls_settings['pager_active_color']);
                            } else {
                                echo "#0d98dc";
                            }
                            ?>"/>
                        </div>
                    </div>
                    <div class="smls-pager-color-wrap">
                        <div class="smls-setting-wrapper">
                            <label><?php _e('Pager Color', SMLS_TD); ?></label>
                            <div class="smls-setting-field">
                                <input type="text" class="smls-pager-color smls-color-picker" name="smls_settings[pager_color]"  value="<?php
                                if (!empty($smls_settings['pager_color'])) {
                                    echo esc_attr($smls_settings['pager_color']);
                                } else {
                                    echo "#7c7c7c";
                                }
                                ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="smls-slider-setting-wrap">
            <div class="smls-slider-outer-wrap">
                <h3><?php _e('Slider Options Settings', SMLS_TD); ?></h3>
                <span class="dashicons dashicons-arrow-down"></span>
            </div>
            <div class="smls-slider-inner-wrap" style="display:none;">
                <div class="smls-setting-wrapper">
                    <label><?php _e('AutoPlay', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <select name="smls_settings[carousel_auto]" class="smls-carousel-auto">

                            <option value="true" <?php if (!empty($smls_settings['carousel_auto'])) selected($smls_settings['carousel_auto'], 'true'); ?>><?php _e('True', SMLS_TD) ?>
                            </option>
                            <option value="false" <?php if (!empty($smls_settings['carousel_auto'])) selected($smls_settings['carousel_auto'], 'false'); ?>><?php _e('False', SMLS_TD) ?>
                            </option>
                        </select>
                    </div>
                </div>
                <div class="smls-count-slide-wrap">
                    <div class="smls-setting-wrapper">
                        <label><?php _e('Count Of Slide', SMLS_TD); ?></label>
                        <div class="smls-setting-field">
                            <input type="number" class="smls-carousel-max-slide" name="smls_settings[smls_slide_count]" min="2" value="<?php
                            if (!empty($smls_settings['smls_slide_count'])) {
                                echo esc_attr($smls_settings['smls_slide_count']);
                            } else {
                                echo 3;
                            }
                            ?>"/>
                        </div>
                    </div>
                </div>
                <div class="smls-setting-wrapper">
                    <label><?php _e('Auto Play Timeout', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <input type="number" class="smls-auto-speed" name="smls_settings[smls_auto_speed]" min="0" value="<?php
                        if (!empty($smls_settings['smls_auto_speed'])) {
                            echo esc_attr($smls_settings['smls_auto_speed']);
                        } else {
                            echo 2000;
                        }
                        ?>"/>
                    </div>
                </div>
                <div class="smls-setting-wrapper">
                    <label><?php _e('Controls', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <select name="smls_settings[carousel_controls]" class="smls-carousel-controls">

                            <option value="true" <?php if (!empty($smls_settings['carousel_controls'])) selected($smls_settings['carousel_controls'], 'true'); ?>><?php _e('True', SMLS_TD) ?>
                            </option>
                            <option value="false" <?php if (!empty($smls_settings['carousel_controls'])) selected($smls_settings['carousel_controls'], 'false'); ?>><?php _e('False', SMLS_TD) ?>
                            </option>
                        </select>
                    </div>
                </div>
                <div class="smls-car-control-type-wrap">
                    <div class="smls-setting-wrapper">
                        <label><?php _e('Controls Type', SMLS_TD); ?></label>
                        <div class="smls-setting-field">
                            <select name="smls_settings[controls_type]" class="smls-controls-type">

                                <option value="arrow" <?php if (!empty($smls_settings['controls_type'])) selected($smls_settings['controls_type'], 'arrow'); ?>><?php _e('Arrow', SMLS_TD) ?>
                                </option>
                                <option value="text" <?php if (!empty($smls_settings['controls_type'])) selected($smls_settings['controls_type'], 'text'); ?>><?php _e('Text', SMLS_TD) ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="smls-controls-true-wrap">
                        <div class="smls-setting-wrapper">
                            <label><?php _e('Arrow Types', SMLS_TD); ?></label>
                            <div class="smls-setting-field">
                                <select name="smls_settings[arrow_type]" class="smls-arrow-type">
                                    <?php for ($k = 1; $k <= 5; $k++) { ?>
                                        <option value="type-<?php echo $k; ?>" <?php if (!empty($smls_settings['arrow_type'])) selected($smls_settings['arrow_type'], 'type-' . $k); ?>><?php _e('Type ', SMLS_TD) ?><?php echo $k; ?></option>
                                    <?php } ?>
                                </select>
                                <div class="smls-arrow-demo smls-preview-image">
                                    <?php
                                    for ($cnt = 1; $cnt <= 5; $cnt++) {
                                        if (isset($smls_settings['arrow_type'])) {
                                            $option_value = $smls_settings['arrow_type'];
                                            $exploed_array = explode('-', $option_value);
                                            $cnt_num = $exploed_array[1];
                                            if ($cnt != $cnt_num) {
                                                $style = "style='display:none;'";
                                            } else {
                                                $style = '';
                                            }
                                        }
                                        ?>
                                        <div class="smls-arrow-common" id="smls-arrow-demo-<?php echo $cnt; ?>" <?php if (isset($style)) echo $style; ?>>
                                            <h4><?php _e('Type', SMLS_TD); ?> <?php echo $cnt; ?> <?php _e('Preview', SMLS_TD); ?></h4>
                                            <img src="<?php echo SMLS_IMG_DIR . '/demo/arrow-pager-preview/arrow-' . $cnt . '.jpg' ?>"/>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="smls-setting-wrapper">
                            <label><?php _e('Arrow Color', SMLS_TD); ?></label>
                            <div class="smls-setting-field">
                                <input type="text" class="smls-arrow-color smls-color-picker" name="smls_settings[arrow_color]"  value="<?php
                                if (!empty($smls_settings['arrow_color'])) {
                                    echo esc_attr($smls_settings['arrow_color']);
                                } else {
                                    echo "#474747";
                                }
                                ?>"/>
                            </div>
                        </div>
                        <div class="smls-arrow-hover-wrap">
                            <div class="smls-setting-wrapper">
                                <label><?php _e('Arrow Hover Color', SMLS_TD); ?></label>
                                <div class="smls-setting-field">
                                    <input type="text" class="smls-arrow-hover-color smls-color-picker" name="smls_settings[arrow_hover_color]"  value="<?php
                                    if (!empty($smls_settings['arrow_hover_color'])) {
                                        echo esc_attr($smls_settings['arrow_hover_color']);
                                    } else {
                                        echo "rgba(71, 71, 71, 0.7)";
                                    }
                                    ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="smls-tooltip-main-wrapper">
        <div class="smls-tooltip-outer-wrap">
            <h3><?php _e('Tooltip Settings', SMLS_TD) ?></h3>
            <span class="dashicons dashicons-arrow-down"></span>
        </div>
        <div class="smls-tooltip-inner-wrap" style="display: none;">
            <div class="smls-tooltip-collection-warp" <?php if (isset($smls_settings['smls_show_tooltip']) && $smls_settings['smls_show_tooltip'] == 0) { ?>style="display: none;" <?php } else { ?> style="display: block;" <?php } ?>>
                <div class="smls-setting-wrapper">
                    <label><?php _e('Tooltip Templates', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <select name="smls_settings[tooltip_template]" class="smls-tooltip-template">
                            <?php for ($k = 1; $k <= 2; $k++) { ?>
                                <option value="template-<?php echo $k; ?>" <?php if (!empty($smls_settings['tooltip_template'])) selected($smls_settings['tooltip_template'], 'template-' . $k); ?>><?php _e('Template ', SMLS_TD) ?><?php echo $k; ?></option>
                            <?php } ?>
                        </select>
                        <div class="smls-tooltip-demo smls-preview-image">
                            <?php
                            for ($cnt = 1; $cnt <= 2; $cnt++) {
                                if (isset($smls_settings['tooltip_template'])) {
                                    $option_value = $smls_settings['tooltip_template'];
                                    $exploed_array = explode('-', $option_value);
                                    $cnt_num = $exploed_array[1];
                                    if ($cnt != $cnt_num) {
                                        $style = "style='display:none;'";
                                    } else {
                                        $style = '';
                                    }
                                }
                                ?>
                                <div class="smls-tooltip-common" id="smls-tooltip-demo-<?php echo $cnt; ?>" <?php if (isset($style)) echo $style; ?>>
                                    <h4><?php _e('Template', SMLS_TD); ?> <?php echo $cnt; ?> <?php _e('Preview', SMLS_TD); ?></h4>
                                    <img src="<?php echo SMLS_IMG_DIR . '/demo/tooltip/tooltip-' . $cnt . '.jpg' ?>"/>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="smls-setting-wrapper">
                    <label><?php _e('Tooltip Position', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <select name="smls_settings[tooltip_position]" class="smls-tooltip-position">
                            <option value="top" <?php if (!empty($smls_settings['tooltip_position'])) selected($smls_settings['tooltip_position'], 'top'); ?>><?php _e('Top', SMLS_TD) ?></option>
                            <option value="bottom" <?php if (!empty($smls_settings['tooltip_position'])) selected($smls_settings['tooltip_position'], 'bottom'); ?>><?php _e('Bottom', SMLS_TD) ?></option>
                            <option value="right" <?php if (!empty($smls_settings['tooltip_position'])) selected($smls_settings['tooltip_position'], 'right'); ?>><?php _e('Right', SMLS_TD) ?></option>
                            <option value="left" <?php if (!empty($smls_settings['tooltip_position'])) selected($smls_settings['tooltip_position'], 'left'); ?>><?php _e('Left', SMLS_TD) ?></option>
                        </select>
                        <p class="description">
                            <?php _e('Determines tooltip position.', SMLS_TD) ?>
                        </p>
                    </div>
                </div>
                <div class="smls-setting-wrapper">
                    <label><?php _e('Animation', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <select name="smls_settings[tooltip_animation]" class="smls-tooltip-animation">
                            <option value="fade" <?php if (!empty($smls_settings['tooltip_animation'])) selected($smls_settings['tooltip_animation'], 'fade'); ?>><?php _e('Fade', SMLS_TD) ?></option>
                            <option value="grow" <?php if (!empty($smls_settings['tooltip_animation'])) selected($smls_settings['tooltip_animation'], 'grow'); ?>><?php _e('Grow', SMLS_TD) ?></option>
                            <option value="swing" <?php if (!empty($smls_settings['tooltip_animation'])) selected($smls_settings['tooltip_animation'], 'swing'); ?>><?php _e('Swing', SMLS_TD) ?></option>
                            <option value="slide" <?php if (!empty($smls_settings['tooltip_animation'])) selected($smls_settings['tooltip_animation'], 'slide'); ?>><?php _e('Slide', SMLS_TD) ?></option>
                            <option value="fall" <?php if (!empty($smls_settings['tooltip_animation'])) selected($smls_settings['tooltip_animation'], 'fall'); ?>><?php _e('fall', SMLS_TD) ?></option>
                        </select>
                        <p class="description">
                            <?php _e('Determines how the tooltip will animate in and out.', SMLS_TD) ?>
                        </p>
                    </div>
                </div>
                <div class="smls-setting-wrapper">
                    <label><?php _e('Animation Duration', SMLS_TD); ?></label>
                    <div class="smls-setting-field">
                        <input type="number" class="smls-tooltip-duration" name="smls_settings[tooltip_duration]" value="<?php
                               if (!empty($smls_settings['tooltip_duration'])) {
                                   echo esc_attr($smls_settings['tooltip_duration']);
                               } else {
                                   echo 350;
                               }
                               ?>"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

