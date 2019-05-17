<?php
/* Security-Check */
if ( !class_exists('WP') ) {
    die();
}

if( ! class_exists('pbSEOFriendlyImagesPro') ):

    class pbSEOFriendlyImagesPro extends pbSEOFriendlyImages
    {
        public static $updateURL = 'https://pb-seo-friendly-images.s3-eu-west-1.amazonaws.com/';

        public static function init()
        {
            pbSEOFriendlyImages::$proVersion = true;

            if(
                self::$proVersion &&
                file_exists(pbsfi_plugin_path.'plugin-update-checker'.DIRECTORY_SEPARATOR.'plugin-update-checker.php') &&
                defined('pbsfi_file')
            ) {
            	try {
	                require pbsfi_plugin_path.'plugin-update-checker'.DIRECTORY_SEPARATOR.'plugin-update-checker.php';

	                $UpdateChecker = new PluginUpdateChecker_3_2 (
	                    pbSEOFriendlyImagesPro::$updateURL.'pb-seo-friendly-images-pro.json',
	                    constant('pbsfi_file'),
	                    'pb-seo-friendly-images-pro'
	                );
	            } catch (Exception $e) {
		            new WP_Error( 'broke', 'PluginUpdateChecker_3_2 failed!' );
	            }
            }

            if( ! is_admin() && ! is_feed() ) {

                if( pbSEOFriendlyImages::$userSettings['enable_lazyload'] || pbSEOFriendlyImages::$userSettings['enable_lazyload_acf'] ) {
                    add_action( 'wp_head', array(__CLASS__, 'thresholdVariable') );
                    add_action( 'wp_enqueue_scripts', array(__CLASS__, 'unveilScript') );
                }

                if( pbSEOFriendlyImages::$userSettings['enable_lazyload'] ) {
                    add_filter( 'post_thumbnail_html', array(__CLASS__, 'lazyLoadImages') );
                    add_filter( 'the_content', array(__CLASS__, 'lazyLoadImages'), 12 );
                    add_filter( 'get_avatar', array(__CLASS__, 'lazyLoadImages') );
                }

                if( pbSEOFriendlyImages::$userSettings['enable_lazyload_acf'] ) {
                    add_filter( 'acf/load_value/type=textarea', array(__CLASS__, 'lazyLoadImages'), 20 );
                    add_filter( 'acf/load_value/type=wysiwyg', array(__CLASS__, 'lazyLoadImages'), 20 );

                    add_filter( 'acf_load_value-textarea', array(__CLASS__, 'lazyLoadImages'), 20 );
                    add_filter( 'acf_load_value-wysiwyg', array(__CLASS__, 'lazyLoadImages'), 20 );
                }

                if( pbSEOFriendlyImages::$userSettings['link_title'] ) {
                    add_filter( 'the_content', array(__CLASS__, 'optimizeLinkTitle'), 999 );

                    add_filter( 'acf/load_value/type=textarea', array(__CLASS__, 'optimizeLinkTitle'), 20 );
                    add_filter( 'acf/load_value/type=wysiwyg', array(__CLASS__, 'optimizeLinkTitle'), 20 );

                    add_filter( 'acf_load_value-textarea', array(__CLASS__, 'optimizeLinkTitle'), 20 );
                    add_filter( 'acf_load_value-wysiwyg', array(__CLASS__, 'optimizeLinkTitle'), 20 );
                }

                if( pbSEOFriendlyImages::$userSettings['disable_srcset'] ) {
                    self::disableResponsiveImages();
                }

	            // Woocommerce
	            if( pbSEOFriendlyImages::$userSettings['wc_title'] && pbSEOFriendlyImages::$proVersion ) {
		            add_filter('wp_get_attachment_image_attributes', array(__CLASS__, 'prepareContentImagesAttributes'), 20, 2);
	            }

	            if( ! pbSEOFriendlyImages::$userSettings['wc_title'] && pbSEOFriendlyImages::$proVersion ) {
		            add_filter('wp_get_attachment_image_attributes', array(__CLASS__, 'prepareDynamicContentImageAttributes'), 20, 2);
	            }
            }
        }

        /**
         * Threshold variable for wp_head
         */
        public static function thresholdVariable()
        {
            if( is_numeric(pbSEOFriendlyImages::$userSettings['lazyload_threshold']) && pbSEOFriendlyImages::$userSettings['lazyload_threshold'] > 0 ) {
                echo '<script>var pbUnveilThreshold = '.trim(pbSEOFriendlyImages::$userSettings['lazyload_threshold']).';</script>';
            }
        }

        /**
         * Unveil Script
         */
        public static function unveilScript()
        {
            if( pbSEOFriendlyImages::$userSettings['enable_lazyload_styles'] ) {
                wp_register_style('unveil-css', plugins_url(dirname(self::$basename)).'/css/lazy.css', false, '1.0.0');
                wp_enqueue_style('unveil-css');
            }

            wp_register_script('unveil', plugins_url(dirname(self::$basename)).'/js/unveil.js', array('jquery'), '1.0.0', true);
            wp_enqueue_script('unveil');
        }

        /**
         * Function for Lazy Load Images
         *
         * @param $content
         * @return mixed
         */
        public static function lazyLoadImages($content)
        {
            /* No lazy images? */
            if ( strpos($content, '<img') === false ) {
                return $content;
            }

            if( get_post_type() == 'tribe_events' || is_feed() ) {
                return $content;
            }

            /* Empty gif */
            $null = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

            preg_match_all('#(<img(.*?)src=["\'](.+?)["\'](.*?)(/?)>)#', $content, $matches, PREG_PATTERN_ORDER);

            if( $matches ) {
                foreach( $matches[0] as $img ) {
                    $new_img = $img;

                    if(
                    	strstr($img, 'lazy') ||
	                    strstr($img, 'no-lazy') ||
	                    strstr($img, 'image/gif;base64') ||
	                    strstr($img, 'blank.gif') ||
	                    strstr($img, 'data-src=') ||

	                    // MasterSlider
	                    strstr($img, 'ms-slide') ||
	                    strstr($img, 'ms-thumb')
                    ) {
                        continue;
                    }

                    $new_img = str_replace('src="', 'src="'.$null.'" data-src="', $new_img);

                    if( strpos($new_img, 'class=') === false ) {
                        $new_img = str_replace('src="', 'class="pb-seo-lazy" src="', $new_img);
                    } else {
                        $new_img = str_replace('class="', 'class="pb-seo-lazy ', $new_img);
                    }

                    $content = str_replace($img, $new_img.'<noscript>'.$img.'</noscript>', $content);
                }
            }

            return $content;
        }

	    /**
	     * Prepare WooCommerce Products
	     *
	     * @param $attr
	     * @param $attachment
	     * @return mixed
	     */
	    public static function prepareContentImagesAttributes( $attr, $attachment )
	    {
		    // Get post parent
		    $parent = get_post_field( 'post_parent', $attachment);

		    // Get post type to check if it's product
		    $type = get_post_field( 'post_type', $parent);
		    if( $type != 'product' ){
			    return $attr;
		    }

		    /// Get title
		    $title = get_post_field( 'post_title', $parent);

		    $attr['alt'] = apply_filters('pbsfi-wc-alt', $title);
		    $attr['title'] = apply_filters('pbsfi-wc-title', $title);

		    return $attr;
	    }

	    /**
	     * Prepare WooCommerce Products
	     *
	     * @param $attr
	     * @param $attachment
	     *
	     * @return array
	     */
	    public static function prepareDynamicContentImageAttributes( $attr, $attachment )
	    {
		    $post = get_post();

		    if( get_post_type($post) !== 'product' ) {
			    return $attr;
		    }

		    $alt = $attr['alt'];
		    $title = $attr['title'];
		    $src = $attr['src'];
		    $imageID = $attachment->ID;

		    /**
		     * Override Area
		     */
		    if( pbSEOFriendlyImages::$userSettings['wc_override_alt'] ) {
			    $alt = trim(pbSEOFriendlyImages::convertReplacements(
				    pbSEOFriendlyImages::$userSettings['wc_alt_scheme'],
				    $src,
				    $imageID
			    ));

			    $alt = apply_filters('wc-pbsfi-alt', $alt);


		    } else {
			    $alt = apply_filters('wc-pbsfi-alt', $alt);
		    }

		    if( pbSEOFriendlyImages::$userSettings['wc_override_title'] ) {

			    $title = trim(pbSEOFriendlyImages::convertReplacements(
				    pbSEOFriendlyImages::$userSettings['wc_title_scheme'],
				    $src,
				    $imageID
			    ));

			    $title = apply_filters('wc-pbsfi-title', $title);
		    } else {
			    $title = apply_filters('wc-pbsfi-title', $title);
		    }

		    /**
		     * Check attributes
		     */
		    if( !empty($alt) && empty($title) && (pbSEOFriendlyImages::$userSettings['wc_sync_method'] == 'both' || pbSEOFriendlyImages::$userSettings['wc_sync_method'] == 'alt' ) ) {

			    $alt = apply_filters('wc-pbsfi-title', $alt);

			    $title = $alt;

		    } else if( empty($alt) && !empty($title)  && (pbSEOFriendlyImages::$userSettings['wc_sync_method'] == 'both' || pbSEOFriendlyImages::$userSettings['wc_sync_method'] == 'title' ) ) {

			    $title = apply_filters('wc-pbsfi-alt', $title);
			    $alt = $title;

		    }

		    /**
		     * set if empty after sync
		     */
		    if( empty($alt) ) {
			    $alt = trim(pbSEOFriendlyImages::convertReplacements(
				    pbSEOFriendlyImages::$userSettings['wc_alt_scheme'],
				    $src,
				    $imageID
			    ));

			    $alt = apply_filters('wc-pbsfi-alt', $alt);
		    }

		    if( empty($title) ) {
			    $title = trim(pbSEOFriendlyImages::convertReplacements(
				    pbSEOFriendlyImages::$userSettings['wc_title_scheme'],
				    $src,
				    $imageID
			    ));

			    $title = apply_filters('wc-pbsfi-title', $title);
		    }

		    $new_attr = array(
			    'alt' => $alt,
			    'title' => $title,
		    );

		    return array_merge($attr, $new_attr);
	    }

        public static function optimizeLinkTitle($content)
        {
            if( empty($content) || !class_exists('DOMDocument') )
                return $content;

	        if( !empty(pbSEOFriendlyImages::$userSettings['encoding']) ) {
		        $charset = pbSEOFriendlyImages::$userSettings['encoding'];
	        } else {
		        $charset = ( (defined('DB_CHARSET') ) ? DB_CHARSET : 'utf-8' );
	        }

	        $charset = apply_filters('pbsfi-charset', $charset);
	        $encoding_declaration = sprintf('<?xml encoding="%s" ?>', $charset);

	        $document = new DOMDocument();
	        if( function_exists('mb_convert_encoding') && pbSEOFriendlyImages::$userSettings['encoding_mode'] != 'off' ) {
		        $content = @mb_convert_encoding($content, 'HTML-ENTITIES', $charset);
	        } else {
		        $content = $encoding_declaration.$content;
	        }
	        @$document->loadHTML($content);

            if( !$document )
                return $content;

            $aTags = $document->getElementsByTagName('a');

            if( ! $aTags->length || $aTags->length == 0 )
                return $content;

            foreach ($aTags as $tag) {
                $title = trim( $tag->getAttribute('title') );

                if( empty($title) ) {

                    $newTitle = '';

                    if( ! empty( $tag->textContent ) ) {

                        $newTitle = $tag->textContent;

                    } elseif( $tag->hasChildNodes() ) {
                        $childNodes = $tag->childNodes;

                        if( ! $childNodes->length || $childNodes->length == 0 )
                            continue;

                        foreach( $childNodes as $subChildNodes ) {
                            if( ! empty( $subChildNodes->textContent ) ) {

                                $newTitle = $subChildNodes->textContent;
                                break;

                            } elseif( $subChildNodes->tagName == 'img' ) {
                                $title = trim( $subChildNodes->getAttribute('title') );

                                if( !empty($title) ) {
                                    $newTitle = $title;
                                    break;
                                }
                            }
                        }
                    }

                    $tag->setAttribute('title', $newTitle);
                }
            }

	        $return = $document->saveHTML();
	        $return = str_replace($encoding_declaration, '', $return);

	        return preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $return));
        }

        /**
         * Disable Responsive Images
         */
        public static function disableResponsiveImages()
        {
            add_filter( 'wp_calculate_image_sizes', '__return_null' );
            add_filter( 'wp_calculate_image_srcset', '__return_null' );
            add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );

            remove_filter( 'the_content','wp_make_content_images_responsive' );
        }
    }

endif;