<?php
defined( 'ABSPATH' ) or die( "No script kiddies please!" );
$post_id = $atts[ 'id' ];
$smls_option = get_post_meta( $post_id, 'smls_option', true );
$smls_settings = get_post_meta( $post_id, 'smls_settings', true );
$smls_settings[ 'logo_layout' ] = ($smls_settings[ 'logo_layout' ] == 'select') ? 'grid' : $smls_settings[ 'logo_layout' ];
$random_num = rand( 111111111, 999999999 );
//$this->print_array($smls_option);
//$this->print_array($smls_settings);
?>
<div class="smls-main-logo-outer-<?php echo $random_num; ?> smls-main-logo-wrapper">
    <?php
    if ( isset( $smls_settings[ 'logo_layout' ] ) && $smls_settings[ 'logo_layout' ] == 'grid' ) {
        ?>
        <div class="smls-grid-container-<?php echo esc_attr( $smls_settings[ 'grid_layout' ] ); ?> smls-grid smls-grid-column-<?php echo esc_attr( $smls_settings[ 'desktop_column' ] ); ?> smls-tablet-column-<?php echo esc_attr( $smls_settings[ 'tablet_column' ] ); ?> smls-mobile-column-<?php echo esc_attr( $smls_settings[ 'mobile_column' ] ); ?> <?php
        if ( $smls_settings[ 'grid_layout' ] == "template-1" || $smls_settings[ 'grid_layout' ] == "template-2" || $smls_settings[ 'grid_layout' ] == "template-3" ) {

            if ( isset( $smls_settings[ 'logo_image_effects' ] ) && $smls_settings[ 'logo_image_effects' ] == 'hover' ) {
                ?> smls-hover-<?php echo esc_attr( $smls_settings[ 'hover_type' ] ); ?><?php
                 } else {
                     echo ' smls-overlay-effect';
                 }
             }
             ?> clearfix
             ">
                 <?php
                 foreach ( $smls_option[ 'logo' ] as $logo_index => $logo_serialized_detail ) {
                     if ( is_numeric( $logo_index ) ) {
                         parse_str( $logo_serialized_detail, $logo_key_detail );
                         reset( $logo_key_detail );
                         $logo_key = key( $logo_key_detail );
                         $logo_detail = $logo_key_detail[ $logo_key ];
                         $smls_option[ 'logo' ][ $logo_key ] = $logo_detail;
                     } else {
                         $logo_key = $logo_index;
                         $logo_detail = $logo_serialized_detail;
                     }
                     ?>
                <div class="smls-grid-image-wrap <?php
                if ( isset( $smls_settings[ 'logo_title_view' ] ) && $smls_settings[ 'logo_title_view' ] == 'title_tooltip' ) {
                    echo 'smls-tooltip';
                } if ( $smls_settings[ 'logo_title_view' ] == 'title_overlay' && ($smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] == 1) ) {
                    echo ' smls-overlay-title-center';
                }
                if ( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] == 1 ) {
                    echo ' smls-external-link-wrapper';
                }
                ?>" <?php if ( isset( $smls_settings[ 'logo_title_view' ] ) && $smls_settings[ 'logo_title_view' ] == 'title_tooltip' ) {
                    ?>
                         title="<?php
                         if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] ) ) {
                             echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] );
                         }
                         ?>"  data-id="smls_<?php
                         echo rand( 111111111, 999999999 );
                         ?>"
                         data-template="<?php
                         if ( isset( $smls_settings[ 'tooltip_template' ] ) ) {
                             echo esc_attr( $smls_settings[ 'tooltip_template' ] );
                         }
                         ?>"
                         data-position="<?php
                         if ( isset( $smls_settings[ 'tooltip_position' ] ) ) {
                             echo esc_attr( $smls_settings[ 'tooltip_position' ] );
                         }
                         ?>"
                         data-animation="<?php
                         if ( isset( $smls_settings[ 'tooltip_animation' ] ) ) {
                             echo esc_attr( $smls_settings[ 'tooltip_animation' ] );
                         }
                         ?>"
                         data-duration="<?php
                         if ( isset( $smls_settings[ 'tooltip_duration' ] ) ) {
                             echo esc_attr( $smls_settings[ 'tooltip_duration' ] );
                         }
                         ?>"
                         <?php
                     }
                     ?>>
                    <div class="smls-grid-pad-container">
                        <?php
                        if ( $smls_settings[ 'logo_image_effects' ] == 'hover' ) {
                            if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] ) && $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] == 1 ) {
                                ?>
                                <a class="smls-url-link-only" href="<?php
                                if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] ) ) {
                                    echo esc_url( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_url' ] );
                                }
                                ?>" target="_blank"><img src="<?php echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'logo_image_url' ] ); ?>"></a>
                                   <?php
                               } else {
                                   ?>
                                <img src="<?php echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'logo_image_url' ] ); ?>">
                                <?php
                            }
                        }
                        if ( $smls_settings[ 'logo_image_effects' ] == 'overlay' ) {
                            ?>
                            <img src="<?php echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'logo_image_url' ] ); ?>">
                            <div class="smls-overlay-all-wrap">
                                <?php
                                if ( $smls_settings[ 'logo_title_view' ] == 'title_overlay' ) {
                                    if ( ! empty( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] ) ) {
                                        ?>
                                        <div class="smls-overlay-title">
                                            <?php
                                            if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] ) ) {
                                                echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] );
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                                if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] ) && $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] == 1 ) {
                                    ?>
                                    <a class="smls-link-style" href="<?php
                                    if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] ) ) {
                                        echo esc_url( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_url' ] );
                                    }
                                    ?>" target="_blank"><span><i class="fa fa-link" aria-hidden="true"></i></span></a>
                                       <?php
                                   }
                                   ?>
                            </div>
                            <div class="smls-overlay-wrap"></div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
            }//closing for foreach
            ?>
        </div>
        <?php
    }//closing for grid
    /*
     * Carousel
     */
    if ( isset( $smls_settings[ 'logo_layout' ] ) && $smls_settings[ 'logo_layout' ] == 'carousel' ) {
        ?>
        <div class="smls-carousel-logo smls-carousel-<?php echo esc_attr( $smls_settings[ 'carousel_layout' ] ); ?> <?php
        if ( $smls_settings[ 'carousel_controls' ] == 'true' ) {
            if ( $smls_settings[ 'controls_type' ] == 'arrow' ) {
                ?> smls-carousel-arrow-<?php
                     echo esc_attr( $smls_settings[ 'arrow_type' ] );
                 } if ( $smls_settings[ 'controls_type' ] == 'text' ) {
                     echo 'smls-text-arrow';
                 }
             } if ( $smls_settings[ 'carousel_pager' ] == 'true' ) {
                 echo ' smls-carousel-pager-template-2';
             }
             if ( $smls_settings[ 'carousel_layout' ] == 'template-1' || $smls_settings[ 'carousel_layout' ] == 'template-2' ) {
                 if ( isset( $smls_settings[ 'logo_image_effects' ] ) && $smls_settings[ 'logo_image_effects' ] == 'hover' ) {
                     ?> smls-hover-<?php echo esc_attr( $smls_settings[ 'hover_type' ] ); ?><?php
                 } else {
                     echo ' smls-overlay-effect';
                 }
             }
             ?>"  data-autoplay="<?php
             if ( isset( $smls_settings[ 'carousel_auto' ] ) ) {
                 echo esc_attr( $smls_settings[ 'carousel_auto' ] );
             }
             ?>"
             data-id="smls_<?php
             echo rand( 1111111, 9999999 );
             ?>"
             data-pager="<?php
             if ( isset( $smls_settings[ 'carousel_pager' ] ) ) {
                 echo esc_attr( $smls_settings[ 'carousel_pager' ] );
             }
             ?>" data-controls="<?php
             if ( isset( $smls_settings[ 'carousel_controls' ] ) ) {
                 echo esc_attr( $smls_settings[ 'carousel_controls' ] );
             }
             ?>" data-controls-type="<?php
             if ( isset( $smls_settings[ 'controls_type' ] ) ) {
                 echo esc_attr( $smls_settings[ 'controls_type' ] );
             }
             ?>" data-slide-count="<?php
             if ( isset( $smls_settings[ 'smls_slide_count' ] ) ) {
                 echo esc_attr( $smls_settings[ 'smls_slide_count' ] );
             }
             ?>"
             data-auto-speed="<?php
             if ( isset( $smls_settings[ 'smls_auto_speed' ] ) ) {
                 echo esc_attr( $smls_settings[ 'smls_auto_speed' ] );
             }
             ?>"
             data-template="<?php
             if ( isset( $smls_settings[ 'carousel_layout' ] ) ) {
                 echo esc_attr( $smls_settings[ 'carousel_layout' ] );
             }
             ?>" data-pager-template="<?php
             if ( isset( $smls_settings[ 'pager_template' ] ) ) {
                 echo esc_attr( $smls_settings[ 'pager_template' ] );
             }
             ?>"
             data-arrow-type="<?php
             if ( isset( $smls_settings[ 'arrow_type' ] ) ) {
                 echo esc_attr( $smls_settings[ 'arrow_type' ] );
             }
             ?>">
                 <?php
                 $total_logo = count( $smls_option[ 'logo' ] );
                 $count_item = 0;
                 // foreach ( $smls_option[ 'logo' ] as $logo_key => $logo_detail ) {
                 foreach ( $smls_option[ 'logo' ] as $logo_index => $logo_serialized_detail ) {
                     if ( is_numeric( $logo_index ) ) {
                         parse_str( $logo_serialized_detail, $logo_key_detail );
                         reset( $logo_key_detail );
                         $logo_key = key( $logo_key_detail );
                         $logo_detail = $logo_key_detail[ $logo_key ];
                         $smls_option[ 'logo' ][ $logo_key ] = $logo_detail;
                     } else {
                         $logo_key = $logo_index;
                         $logo_detail = $logo_serialized_detail;
                     }
                     $count_item ++;
                     if ( $smls_settings[ 'carousel_layout' ] == 'template-1' ) {
                         ?>
                    <div class="smls-logo-carousel-1 <?php
                    if ( isset( $smls_settings[ 'logo_title_view' ] ) && $smls_settings[ 'logo_title_view' ] == 'title_tooltip' ) {
                        echo 'smls-tooltip';
                    }
                    if ( $smls_settings[ 'logo_title_view' ] == 'title_overlay' && ($smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] == 1) ) {
                        echo ' smls-overlay-title-center';
                    }
                    ?>"
                    <?php if ( isset( $smls_settings[ 'logo_title_view' ] ) && $smls_settings[ 'logo_title_view' ] == 'title_tooltip' ) {
                        ?>
                             title="<?php
                             if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] ) ) {
                                 echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] );
                             }
                             ?>"  data-id="smls_<?php
                             echo rand( 111111111, 999999999 );
                             ?>"
                             data-template="<?php
                             if ( isset( $smls_settings[ 'tooltip_template' ] ) ) {
                                 echo esc_attr( $smls_settings[ 'tooltip_template' ] );
                             }
                             ?>"
                             data-position="<?php
                             if ( isset( $smls_settings[ 'tooltip_position' ] ) ) {
                                 echo esc_attr( $smls_settings[ 'tooltip_position' ] );
                             }
                             ?>"
                             data-animation="<?php
                             if ( isset( $smls_settings[ 'tooltip_animation' ] ) ) {
                                 echo esc_attr( $smls_settings[ 'tooltip_animation' ] );
                             }
                             ?>"
                             data-duration="<?php
                             if ( isset( $smls_settings[ 'tooltip_duration' ] ) ) {
                                 echo esc_attr( $smls_settings[ 'tooltip_duration' ] );
                             }
                             ?>"
                             <?php
                         }
                         ?>>

                        <div class="smls-car-img-wrap">
                            <?php
                            if ( $smls_settings[ 'logo_image_effects' ] == 'hover' ) {
                                if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] ) && $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] == 1 ) {
                                    ?>
                                    <a class="smls-url-link-only" href="<?php
                                    if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] ) ) {
                                        echo esc_url( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_url' ] );
                                    }
                                    ?>" target="_blank"><img src="<?php echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'logo_image_url' ] ); ?>"></a>
                                       <?php
                                   } else {
                                       ?>
                                    <img src="<?php echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'logo_image_url' ] ); ?>">
                                    <?php
                                }
                            }
                            if ( $smls_settings[ 'logo_image_effects' ] == 'overlay' ) {
                                ?>
                                <img src="<?php echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'logo_image_url' ] ); ?>">
                                <div class = "smls-overlay-all-wrap">
                                    <?php
                                    if ( $smls_settings[ 'logo_title_view' ] == 'title_overlay' ) {
                                        if ( ! empty( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] ) ) {
                                            ?>
                                            <div class="smls-overlay-title">
                                                <?php
                                                if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] ) ) {
                                                    echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] );
                                                }
                                                ?>
                                            </div>

                                            <?php
                                        }
                                    }
                                    if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] ) && $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] == 1 ) {
                                        ?>
                                        <a class="smls-link-style" href="<?php
                                        if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] ) ) {
                                            echo esc_url( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_url' ] );
                                        }
                                        ?>"  target="_blank"><span><i class="fa fa-link" aria-hidden="true"></i></span></a>
                                           <?php
                                       }
                                       ?>
                                </div>
                                <div class="smls-overlay-wrap"></div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                if ( $smls_settings[ 'carousel_layout' ] == 'template-2' ) {
                    ?>
                    <div class="smls-carousel-four-items  <?php
                    if ( isset( $smls_settings[ 'logo_title_view' ] ) && $smls_settings[ 'logo_title_view' ] == 'title_tooltip' ) {
                        echo 'smls-tooltip';
                    }
                    if ( $smls_settings[ 'logo_title_view' ] == 'title_overlay' && ($smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] == 1) ) {
                        echo ' smls-overlay-title-center';
                    }
                    ?>"
                    <?php if ( isset( $smls_settings[ 'logo_title_view' ] ) && $smls_settings[ 'logo_title_view' ] == 'title_tooltip' ) {
                        ?>
                             title="<?php
                             if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] ) ) {
                                 echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] );
                             }
                             ?>"  data-id="smls_<?php
                             echo rand( 111111111, 999999999 );
                             ?>"
                             data-template="<?php
                             if ( isset( $smls_settings[ 'tooltip_template' ] ) ) {
                                 echo esc_attr( $smls_settings[ 'tooltip_template' ] );
                             }
                             ?>"
                             data-position="<?php
                             if ( isset( $smls_settings[ 'tooltip_position' ] ) ) {
                                 echo esc_attr( $smls_settings[ 'tooltip_position' ] );
                             }
                             ?>"
                             data-animation="<?php
                             if ( isset( $smls_settings[ 'tooltip_animation' ] ) ) {
                                 echo esc_attr( $smls_settings[ 'tooltip_animation' ] );
                             }
                             ?>"
                             data-duration="<?php
                             if ( isset( $smls_settings[ 'tooltip_duration' ] ) ) {
                                 echo esc_attr( $smls_settings[ 'tooltip_duration' ] );
                             }
                             ?>"
                             <?php
                         }
                         ?>>
                        <div class="smls-car-img-wrap">
                            <?php
                            if ( $smls_settings[ 'logo_image_effects' ] == 'hover' ) {
                                if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] ) && $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] == 1 ) {
                                    ?>
                                    <a class="smls-url-link-only" href="<?php
                                    if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] ) ) {
                                        echo esc_url( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_url' ] );
                                    }
                                    ?>" target="_blank"><img src="<?php echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'logo_image_url' ] ); ?>"></a>
                                       <?php
                                   } else {
                                       ?>
                                    <img src="<?php echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'logo_image_url' ] ); ?>">
                                    <?php
                                }
                            }
                            if ( $smls_settings[ 'logo_image_effects' ] == 'overlay' ) {
                                ?>
                                <img src="<?php echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'logo_image_url' ] );
                                ?>">
                                <div class="smls-overlay-all-wrap">
                                    <?php
                                    if ( $smls_settings[ 'logo_title_view' ] == 'title_overlay' ) {
                                        if ( ! empty( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] ) ) {
                                            ?>
                                            <div class="smls-overlay-title">
                                                <?php
                                                if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] ) ) {
                                                    echo esc_attr( $smls_option[ 'logo' ][ $logo_key ][ 'title' ] );
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                    if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] ) && $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] == 1 ) {
                                        ?>
                                        <a class="smls-link-style" href="<?php
                                        if ( isset( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_link' ] ) ) {
                                            echo esc_url( $smls_option[ 'logo' ][ $logo_key ][ 'logo_external_url' ] );
                                        }
                                        ?>"  target="_blank"><span><i class="fa fa-link" aria-hidden="true"></i></span></a>
                                           <?php
                                       }
                                       ?>
                                </div>
                                <div class="smls-overlay-wrap"></div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <?php
    }//closing of carousel
    ?>
</div>
<?php
include(SMLS_PATH . 'inc/frontend/smls-detail/smls-inline-style.php');



