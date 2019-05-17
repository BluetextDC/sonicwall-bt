<?php defined( 'ABSPATH' ) or die( "No script kiddies please!" ); ?>
<div class="smls-about-main-wrapper">
    <div class="smls-header">
        <div>
            <div id="smls-fb-root"></div>
            <script>(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4"; fjs.parentNode.insertBefore(js, fjs); }(document, 'script', 'facebook-jssdk'));</script>
            <script>!function(d, s, id){var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location)?'http':'https'; if (!d.getElementById(id)){js = d.createElement(s); js.id = id; js.src = p + '://platform.twitter.com/widgets.js'; fjs.parentNode.insertBefore(js, fjs); }}(document, 'script', 'twitter-wjs');</script>
        </div>
        <div class="smls-header-section">
            <div class="smls-header-left">
                <div class="smls-title"><?php _e( 'Smart Logo Showcase Lite', SMLS_TD ); ?></div>
                <div class="smls-version-wrap">
                    <span>Version <?php echo SMLS_VERSION; ?></span>
                </div>
            </div>

            <div class="smls-header-social-link">
                <p class="smls-follow-us"><?php _e( 'Follow us for new updates', SMLS_TD ); ?></p>
                <div class="fb-like" data-href="https://www.facebook.com/accesspressthemes" data-layout="button" data-action="like" data-show-faces="true" data-share="false"></div>
                <a href="https://twitter.com/accesspressthemes" class="twitter-follow-button" data-show-count="false">Follow @accesspressthemes</a>
            </div>
        </div>
    </div>

    <div class="smls-how-to-use-container">
        <div class="smls-column-one-wrap">
            <div class="smls-panel-body">
                <div class="smls-row">
                    <div class="smls-col-three-third">
                        <h3><?php _e( 'About Us', SMLS_TD ); ?></h3>
                        <div class="smls-tab-wrapper">
                            <p><strong><?php _e( 'Smart Logo Showcase Lite - Responsive Clients Logo Gallery Plugin for WordPress', SMLS_TD ) ?></strong> <?php _e( '- is a Free WordPress Plugin by AccessPress Themes.', SMLS_TD ); ?> </p>

                            <p><?php _e( 'AccessPress Themes is a venture of Access Keys - who has developed hundreds of Custom WordPress themes and plugins for its clients over the years. ', SMLS_TD ); ?></p>

                            <p><strong><?php _e( 'Smart Logo Showcase Lite', SMLS_TD ) ?></strong><?php _e( ' - is a Free WordPress logo showcase plugin packaged with 5 beautiful pre-designed templates. With this fully responsive logo showcase builder, you can display logo images, however you like and configure it to the most. Use different layouts available and showcase your client/ sponsor/ partner/ brand logo.', SMLS_TD ); ?></p>
                            <div class="smls-halfseperator"></div>
                            <p><strong><?php _e( 'Please visit our product page for more details here:', SMLS_TD ); ?></strong>
                                <br />
                                <a href="https://accesspressthemes.com/wordpress-plugins/smart-logo-showcase-lite" target="_blank">https://accesspressthemes.com/wordpress-plugins/smart-logo-showcase-lite</a>
                            </p>
                            <div class="smls-halfseperator"></div>
                            <p><strong><?php _e( 'Please visit our demo page here:', SMLS_TD ); ?></strong>
                                <br />
                                <a href="http://demo.accesspressthemes.com/wordpress-plugins/smart-logo-showcase-lite/" target="_blank">http://demo.accesspressthemes.com/wordpress-plugins/smart-logo-showcase-lite/</a>
                            </p>

                            <div class="smls-halfseperator"></div>
                            <p><strong><?php _e( 'Plugin documentation can be found here:', SMLS_TD ); ?></strong>
                                <br />
                                <a href="https://accesspressthemes.com/documentation/smart-logo-showcase-lite/" target="_blank">https://accesspressthemes.com/documentation/smart-logo-showcase-lite/</a>
                            </p>
                            <p>&nbsp;</p>
                            <h3 class="smls-sub-title">More from AccessPress themes </h3>
                            <div class="smls-row">
                                <div class="smls-col-one-third">
                                    <div class="smls-product">
                                        <div class="smls-logo-product">
                                            <a href="http://accesspressthemes.com/plugins/" target="_blank">
                                                <img src="<?php echo SMLS_IMG_DIR; ?>/plugin.png" alt="<?php esc_attr_e( 'AccessPress Social Icons', SMLS_TD ); ?>" />
                                            </a>
                                        </div>
                                        <div class="smls-productext">
                                            <p><strong>WordPress Plugins</strong>
                                                <br />
                                                <a href="http://accesspressthemes.com/plugins/" target="_blank">http://accesspressthemes.com/plugins/</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="smls-col-one-third">
                                    <div class="smls-product">
                                        <div class="smls-logo-product">
                                            <a href="http://accesspressthemes.com/themes/" target="_blank"><img src="<?php echo SMLS_IMG_DIR; ?>/theme.png" /></a>
                                        </div>
                                        <div class="smls-productext">
                                            <p><strong>WordPress Themes</strong>
                                                <br />
                                                <a href="http://accesspressthemes.com/themes/" target="_blank">http://accesspressthemes.com/themes/</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>



                                <div class="smls-col-one-third">
                                    <div class="smls-product">
                                        <div class="smls-logo-product">
                                            <a href="http://accesspressthemes.com/contact/" target="_blank"><img src="<?php echo SMLS_IMG_DIR; ?>/customize.png" /></a>
                                        </div>
                                        <div class="smls-productext">
                                            <p><strong>WordPress Customization</strong>
                                                <br />
                                                <a href="http://accesspressthemes.com/contact/" target="_blank">http://accesspressthemes.com/wordpress-plugins/contact/</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <hr />
                            <h3><?php _e( 'Get in touch', SMLS_TD ); ?></h3>
                            <p><?php _e( 'If you have any question/feedback, please get in touch:', SMLS_TD ); ?></p>
                            <p>
                                <strong>General enquiries:</strong> <a href="mailto:info@accesspressthemes.com">info@accesspressthemes.com</a><br />
                                <strong>Support:</strong> <a href="mailto:support@accesspressthemes.com">support@accesspressthemes.com</a><br />
                                <strong>Sales:</strong> <a href="mailto:sales@accesspressthemes.com">sales@accesspressthemes.com</a>
                            </p>
                            <div class="smls-seperator"></div>
                            <div class="smls-dottedline"></div>
                            <div class="smls-seperator"></div>
                        </div>
                    </div>
                    <div class="smls-col-three-third">
                        <h3><?php _e( 'Get social', SMLS_TD ); ?></h3>
                        <p><?php _e( 'Get connected with us on social media. Facebook is the best place to find updates on our themes/plugins: ', SMLS_TD ); ?></p>

                        <p><strong>Like us on facebook:</strong>
                            <br />
                            <iframe style="border: none; overflow: hidden; width: 700px; height: 250px;" src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FAccessPress-Themes%2F1396595907277967&amp;width=842&amp;height=258&amp;colorscheme=light&amp;show_faces=true&amp;header=false&amp;stream=false&amp;show_border=true&amp;appId=1411139805828592" width="240" height="150" frameborder="0" scrolling="no"></iframe>
                        </p>

                        <ul class="smls-about smls-unstyled smls-inlinelist">
                            <li><a href="https://plus.google.com/u/0/+Accesspressthemesprofile/about" target="_blank"><img src="<?php echo SMLS_IMG_DIR; ?>/googleplus.png" alt="google+"></a>
                            </li>
                            <li><a href="http://www.pinterest.com/accesspresswp/" target="_blank"><img src="<?php echo SMLS_IMG_DIR; ?>/pinterest.png" alt="pinterest"></a>
                            </li>
                            <li><a href="https://www.flickr.com/photos/accesspressthemes/" target="_blank"><img src="<?php echo SMLS_IMG_DIR; ?>/flicker.png" alt="flicker"></a>
                            </li>
                            <li><a href="https://twitter.com/apthemes" target="_blank"><img src="<?php echo SMLS_IMG_DIR; ?>/twitter.png" alt="twitter"></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="smls-right-sidebar">
    <img src="<?php echo SMLS_IMG_DIR; ?>/upgrade.jpg" alt="<?php _e( 'Upgrade Smart Logo Showcase', SMLS_TD ); ?>">
    <div class="smls-button-wrap-backend">
        <a href="http://demo.accesspressthemes.com/wordpress-plugins/smart-logo-showcase/" class="smls-demo-btn" target="_blank">Demo</a>
        <a href="https://codecanyon.net/item/smart-logo-showcase-responsive-clients-logo-gallery-plugin-for-wordpress/19274075?ref=AccessKeys" target="_blank" class="smls-upgrade-btn">Upgrade</a>
        <a href="https://accesspressthemes.com/wordpress-plugins/smart-logo-showcase/" target="_blank" class="smls-upgrade-btn">Plugin Information</a>
    </div>
    <img src="<?php echo SMLS_IMG_DIR; ?>/feature.jpg" alt="<?php _e( 'Smart Logo Showcase Features', SMLS_TD ); ?>">
</div>
<div class="clearfix"></div>