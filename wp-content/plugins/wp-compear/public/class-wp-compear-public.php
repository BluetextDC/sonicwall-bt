<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://davenicosia.com
 * @since      1.0.0
 *
 * @package    WP_Compear
 * @subpackage WP_Compear/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Compear
 * @subpackage WP_Compear/public
 * @author     dave Nicosia <dave@davenicosia.com>
 */
class WP_Compear_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $WP_Compear    The ID of this plugin.
	 */
	private $WP_Compear;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $WP_Compear       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $WP_Compear, $version ) {

		$this->WP_Compear = $WP_Compear;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Compear_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Compear_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->WP_Compear, plugin_dir_url( __FILE__ ) . 'css/wp-compear-public.css', array(), $this->version, 'all' );

		$handle = 'slick.css';
   		$list = 'enqueued';

   		if (wp_style_is( $handle, $list )) {
			return;
		} else {
			wp_enqueue_style( 'slick.css', plugins_url() . '/wp-compear/includes/slick-1.5.7/slick/slick.css', array(), $this->version, 'all' );
		}


		$handle = 'wpcompear-fontello.css';
   		$list = 'enqueued';

   		if (wp_style_is( $handle, $list )) {
			return;
		} else {
			wp_enqueue_style( 'wpcompear-fontello.css', plugins_url() . '/wp-compear/includes/fontello-bf24cd3f/css/wpcompear-fontello.css', array(), $this->version, 'all' );
		}



		// $handle = 'slick-theme.css';
  //  		$list = 'enqueued';

  //  		if (wp_style_is( $handle, $list )) {
		// 	return;
		// } else {
		// 	//wp_enqueue_style( 'slick-theme.css', plugins_url() . '/wp-compear/includes/slick-1.5.7/slick/slick-theme.css', array(), $this->version, 'all' );
		// }


	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Compear_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Compear_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		


		// had to make this slick bug fix for slider jumping when arrows clicked, not using .min because of this
		// https://github.com/kenwheeler/slick/pull/1637/files

		$handle = 'slick.js';
   		$list = 'enqueued';

   		if (wp_script_is( $handle, $list )) {
			return;
		} else {
			wp_enqueue_script( 'slick.js', plugins_url() . '/wp-compear/includes/slick-1.5.7/slick/slick.js', array( 'jquery' ), $this->version, false );
		}


		$handle = 'sorttable.js';
   		$list = 'enqueued';

   		if (wp_script_is( $handle, $list )) {
			return;
		} else {
			wp_enqueue_script( 'sorttable.js', plugins_url() . '/wp-compear/includes/sorttable.js', array( 'jquery' ), $this->version, false );
		}


		$handle = 'jquery-ui-core';
   		$list = 'enqueued';

   		if (wp_script_is( $handle, $list )) {
			return;
		} else {
			wp_enqueue_script( 'jquery-ui-core' );
		}


		$handle = 'jquery-ui-draggable';
   		$list = 'enqueued';

   		if (wp_script_is( $handle, $list )) {
			return;
		} else {
			wp_enqueue_script( 'jquery-ui-draggable' );
		}



		$handle = 'jquery-ui-droppable';
   		$list = 'enqueued';

   		if (wp_script_is( $handle, $list )) {
			return;
		} else {
			wp_enqueue_script( 'jquery-ui-droppable' );
		}


		$handle = 'TweenMax.min.js';
   		$list = 'enqueued';

   		if (wp_script_is( $handle, $list )) {
			return;
		} else {
			wp_enqueue_script( 'TweenMax.min.js', plugins_url() . '/wp-compear/includes/greensock-js/src/minified/TweenMax.min.js', array( 'jquery' ), $this->version, false );
		}

		$handle = 'jquery.gsap.min.js';
   		$list = 'enqueued';

   		if (wp_script_is( $handle, $list )) {
			return;
		} else {
			wp_enqueue_script( 'jquery.gsap.min.js', plugins_url() . '/wp-compear/includes/greensock-js/src/minified/jquery.gsap.min.js', array( 'jquery' ), $this->version, false );
		}

		wp_enqueue_script( $this->WP_Compear, plugin_dir_url( __FILE__ ) . 'js/wp-compear-public.js', array( 'jquery', 'jquery-ui-core' ), $this->version, true );


		

	}


	/**
	 * Create the shortcode for the WP Compear Tool
	 *
	 * @since    1.0.0
	 */
	public function WP_Compear_create_shortcode() {


		function WP_Compear_shortcode($atts) {

			$atts = shortcode_atts( array(
				'id' => ''
			), $atts );

			$WP_Compear_tool = '';

			$list_type = get_post_meta( $atts['id'], '_wpcompear_list_type', true );


			$wpcompear_specs_array = get_post_meta( $atts['id'], '_wpcompear_custom_specs');
			$wpcompear_specs_serialized = $wpcompear_specs_array[0];
			$wpcompear_specs = unserialize( $wpcompear_specs_serialized );

			//$WP_Compear_tool = '<pre>'.print_r($wpcompear_specs, true).'</pre><hr />';



			$wpcompear_prods_array = get_post_meta( $atts['id'], '_wpcompear_list_products');
			$wpcompear_prods_serialized = $wpcompear_prods_array[0];
			$wpcompear_prods = unserialize( base64_decode( $wpcompear_prods_serialized ) );

			//$WP_Compear_tool .= '<pre>'.print_r($wpcompear_prods, true).'</pre><hr />';


			$wpcompear_list_theme = get_post_meta( $atts['id'], '_wpcompear_list_theme', true);


		


			


				if($list_type=='slider') :


					$wpcompear_list_slider_show_lg = get_post_meta( $atts['id'], '_wpcompear_list_slider_show_lg', true );
			    	$wpcompear_list_slider_scroll_lg = get_post_meta( $atts['id'], '_wpcompear_list_slider_scroll_lg', true );
					$wpcompear_list_slider_prevnext_lg = get_post_meta( $atts['id'], '_wpcompear_list_slider_prevnext_lg', true );
					$wpcompear_list_slider_counter_lg = get_post_meta( $atts['id'], '_wpcompear_list_slider_counter_lg', true );

			    	$wpcompear_list_slider_show_md = get_post_meta( $atts['id'], '_wpcompear_list_slider_show_md', true );
			    	$wpcompear_list_slider_scroll_md = get_post_meta( $atts['id'], '_wpcompear_list_slider_scroll_md', true );

			    	$wpcompear_list_slider_specname_show = get_post_meta( $atts['id'], '_wpcompear_list_slider_specname_show', true );

			    	$wpcompear_list_col_alignment_slider = get_post_meta( $atts['id'], '_wpcompear_list_col_alignment_slider', true );

			    	// $wpcompear_list_slider_prevnext_md = get_post_meta( $atts['id'], '_wpcompear_list_slider_prevnext_md', true );
			    	// $wpcompear_list_slider_counter_md = get_post_meta( $atts['id'], '_wpcompear_list_slider_counter_md', true );

			    	// $wpcompear_list_slider_mobile = get_post_meta( $atts['id'], '_wpcompear_list_slider_mobile', true );


		    		if(is_array($wpcompear_prods)) :

		    			$m=0;
		    			$isFirst = true;

		    			$WP_Compear_tool .= '<div class="wp-compear-tool-wrapper '.$wpcompear_list_theme.' wp-compear-tool-'.$atts['id'].'">';

		    			$WP_Compear_tool .= '<div class="wp-compear-tool-slider" data-lg-show="'.$wpcompear_list_slider_show_lg.'" data-lg-scroll="'.$wpcompear_list_slider_scroll_lg.'" data-lg-prevnext="'.$wpcompear_list_slider_prevnext_lg.'" data-lg-counter="'.$wpcompear_list_slider_counter_lg.'" data-md-show="'.$wpcompear_list_slider_show_md.'" data-md-scroll="'.$wpcompear_list_slider_scroll_md.'">';

		    			// loop through all products
		    			foreach($wpcompear_prods as $wpcompear_single_prod) :
		    			
		    				// skips first element in products array (cloning row) 
		    				if ($isFirst) {
						        $isFirst = false;
						        $m++;
						        continue;
						    }

		    				$WP_Compear_tool .= '<div class="wp-compear-slider-slide '.$wpcompear_list_col_alignment_slider.'">';

			    				foreach($wpcompear_specs as $wpcompear_single_spec) :
			    				
			    						$spec_type = $wpcompear_single_spec['spec_type'];
										$spec_name = $wpcompear_single_spec['spec_name'];
										$spec_id = $wpcompear_single_spec['spec_id'];



										if($wpcompear_list_slider_specname_show=='yes' && $spec_name !=''){
											$spec_name_html = '<span class="wpcompear-slider-spec-name">'.$spec_name.':</span><br />';
										}
										else{
											$spec_name_html = '';
										}

										if($spec_type=='text-field') :



											if($wpcompear_prods[$m][$spec_id] != ''):

												$spec_value = stripslashes($wpcompear_prods[$m][$spec_id]);

												$WP_Compear_tool .= '<div class="wpcompear-slider-spec text-field">'.$spec_name_html.$spec_value.'</div>';

											endif;


										elseif($spec_type=='text-paragraph') :

											if($wpcompear_prods[$m][$spec_id] != ''):

												$spec_value = stripslashes($wpcompear_prods[$m][$spec_id]);

												$WP_Compear_tool .= '<div class="wpcompear-slider-spec paragraph-field">'.$spec_name_html.$spec_value.'</div>';

											endif;


										elseif($spec_type=='wysiwyg') :

											if($wpcompear_prods[$m][$spec_id] != ''):

												$content_stripped = stripslashes($wpcompear_prods[$m][$spec_id]);
												//old way of parsing shortcodes
												$content = apply_filters('the_content', $content_stripped);
												// new way of parsing shortcodes
												$content = do_shortcode($content_stripped);

												$WP_Compear_tool .= '<div class="wpcompear-slider-spec wysiwyg-field">'.$spec_name_html.$content.'</div>';

											endif;


										elseif($spec_type=='image') :

											if($wpcompear_prods[$m][$spec_id] != ''):

												$WP_Compear_tool .= '<div class="wpcompear-slider-spec image image-field"><img src="'.$wpcompear_prods[$m][$spec_id].'" alt="" /></div>';

											endif;


										elseif($spec_type=='star-rating') :

											if($wpcompear_prods[$m][$spec_id] != ''):

												$star_rating = $wpcompear_prods[$m][$spec_id];

												if($star_rating=='no-rating') {
													$star_rating_html = '<span class="star-rating-txt">'.__('No Ratings Yet','wp-compear').'</span><span class="star-rating" title="No Ratings Yet" >No Ratings Yet</span>';
												}
												if($star_rating=='0') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='0.1' || $star_rating=='0.2' || $star_rating=='0.3' || $star_rating=='0.4' || $star_rating=='0.5' || $star_rating=='0.6' || $star_rating=='0.7' || $star_rating=='0.8' || $star_rating=='0.9') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star-half-alt"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='1' || $star_rating=='1.1' || $star_rating=='1.2' || $star_rating=='1.3' || $star_rating=='1.4') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='1.5' || $star_rating=='1.6' || $star_rating=='1.7' || $star_rating=='1.8' || $star_rating=='1.9') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star-half-alt"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='2' || $star_rating=='2.1' || $star_rating=='2.2' || $star_rating=='2.3' || $star_rating=='2.4') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='2.5' || $star_rating=='2.6' || $star_rating=='2.7' || $star_rating=='2.8' || $star_rating=='2.9') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-half-alt"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='3' || $star_rating=='3.1' || $star_rating=='3.2' || $star_rating=='3.3' || $star_rating=='3.4') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='3.5' || $star_rating=='3.6' || $star_rating=='3.7' || $star_rating=='3.8' || $star_rating=='3.9') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-half-alt"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='4' || $star_rating=='4.1' || $star_rating=='4.2' || $star_rating=='4.3' || $star_rating=='4.4') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='4.5' || $star_rating=='4.6' || $star_rating=='4.7' || $star_rating=='4.8' || $star_rating=='4.9') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-half-alt"></i></span>';
												}
												if($star_rating=='5') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i></span>';
												}

												$WP_Compear_tool .= '<div class="wpcompear-slider-spec star-rating-field">'.$spec_name_html.$star_rating_html.'</div>';
											endif;

										elseif($spec_type=='yes-no') :

											if($wpcompear_prods[$m][$spec_id] != ''):

												$spec_value = stripslashes($wpcompear_prods[$m][$spec_id]);

												if($spec_value=='yes') {
													$icon = '<i class="tool-yesno icon-ok"></i>';
												}
												elseif($spec_value=='no') {
													$icon = '<i class="tool-yesno icon-cancel"></i>';
												}

												$WP_Compear_tool .= '<div class="wpcompear-slider-spec yesno-field">'.$spec_name_html.$icon.'</div>';

											endif;

										endif;

			    				endforeach;

		    				$WP_Compear_tool .= '</div>'; // end 

		    				$m++;

		    			endforeach;

		    			$WP_Compear_tool .= '</div>'; // end .wp-compear-tool-slider

		    			$WP_Compear_tool .= '<div class="slider-side-shadow"></div>';

		    			$WP_Compear_tool .= '</div>';  // end .wp-compear-tool-wrapper

		    		endif;










		    	elseif($list_type=='table') :

		    		$wpcompear_list_sortable_check = get_post_meta( $atts['id'], '_wpcompear_list_sortable_check', true );
		    		$wpcompear_list_hover_check = get_post_meta( $atts['id'], '_wpcompear_list_hover_check', true );

		    		$current_wpcompear_list_col_width = get_post_meta( $atts['id'], '_wpcompear_list_col_width', true );
	    			$col_width = unserialize( $current_wpcompear_list_col_width );

	    			$current_wpcompear_list_col_alignment = get_post_meta( $atts['id'], '_wpcompear_list_col_alignment', true );
	    			$col_alignment = unserialize( $current_wpcompear_list_col_alignment );

	    			$current_wpcompear_list_col_alignment_vert = get_post_meta( $atts['id'], '_wpcompear_list_col_alignment_vert', true );
	    			$col_alignment_vert = unserialize( $current_wpcompear_list_col_alignment_vert );


			    	if($wpcompear_list_sortable_check=='yes') {$sortable_check=' sortable';}
			    	else {$sortable_check='';}

			    	if($wpcompear_list_hover_check=='yes') {$hover_check=' row-hover';}
			    	else {$hover_check='';}

		    		if(is_array($wpcompear_prods)) :

		    			$m=0;
		    			$isFirst = true;

		    			$WP_Compear_tool .= '<div class="wp-compear-tool-wrapper '.$wpcompear_list_theme.' wp-compear-tool-'.$atts['id'].'">';
		    			$WP_Compear_tool .= '<div class="wp-compear-tool-table-outer">';

                    		$WP_Compear_tool .= '<table class="wp-compear-table'.$sortable_check.$hover_check.'">';

                    		$WP_Compear_tool .= '<thead>';

	                    		$WP_Compear_tool .= '<tr>';


	                    		if(is_array($wpcompear_specs)) : // loop through all custom specs if they exist

									foreach($wpcompear_specs as $current_custom_spec) :

										$spec_id = $current_custom_spec['spec_id'];

										$WP_Compear_tool .= '<th class="product-spec-name '. $col_width[$spec_id].'" data-spec-id="'.$spec_id.'"><span>'.$current_custom_spec['spec_name'].'</span></th>';
									endforeach;

								endif;

	                    		$WP_Compear_tool .= '</tr>';

                    		$WP_Compear_tool .= '</thead>';


                    		$WP_Compear_tool .= '<tbody>';



                    			if(is_array($wpcompear_prods)) :


                    				$m=0 ;
                    				foreach($wpcompear_prods as $current_custom_product) :

                    					// skips first element in products array (cloning row) 
					    				if ($isFirst) {
									        $isFirst = false;
									        $m++;
									        continue;
									    }

                    					$WP_Compear_tool .= '<tr>';


                    					foreach($wpcompear_specs as $current_custom_spec) :

                    						$spec_type = $current_custom_spec['spec_type'];
											$spec_name = $current_custom_spec['spec_name'];
											$spec_id = $current_custom_spec['spec_id'];


											if($spec_type=='text-field') :

												$spec_value = stripslashes($wpcompear_prods[$m][$spec_id]);

												$WP_Compear_tool .= '<td class="'. $col_alignment[$spec_id].' '.$col_alignment_vert[$spec_id].' text-field">'.$spec_value.'</td>';

											elseif($spec_type=='text-paragraph') :

												$spec_value = stripslashes($wpcompear_prods[$m][$spec_id]);

												$WP_Compear_tool .= '<td class="'. $col_alignment[$spec_id].' '.$col_alignment_vert[$spec_id].' paragraph-field">'.$spec_value.'</td>';

											elseif($spec_type=='wysiwyg') :

												$content_stripped = stripslashes($wpcompear_prods[$m][$spec_id]);
												//old way of parsing shortcodes
												$content = apply_filters('the_content', $content_stripped);
												// new way of parsing shortcodes
												$content = do_shortcode($content_stripped);

												$WP_Compear_tool .= '<td class="'. $col_alignment[$spec_id].' '.$col_alignment_vert[$spec_id].' wysiwyg-field">'.$content.'</td>';

											elseif($spec_type=='image') :

												$WP_Compear_tool .= '<td class="'. $col_alignment[$spec_id].' '.$col_alignment_vert[$spec_id].' image-field"><img src="'.$wpcompear_prods[$m][$spec_id].'" alt="" /></td>';

											elseif($spec_type=='star-rating') :

												$star_rating = $wpcompear_prods[$m][$spec_id];

												if($star_rating=='no-rating') {
													$star_rating_html = '<span class="star-rating-txt">'.__('No Ratings Yet','wp-compear').'</span><span class="star-rating" title="No Ratings Yet" >No Ratings Yet</span>';
												}
												if($star_rating=='0') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='0.1' || $star_rating=='0.2' || $star_rating=='0.3' || $star_rating=='0.4' || $star_rating=='0.5' || $star_rating=='0.6' || $star_rating=='0.7' || $star_rating=='0.8' || $star_rating=='0.9') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star-half-alt"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='1' || $star_rating=='1.1' || $star_rating=='1.2' || $star_rating=='1.3' || $star_rating=='1.4') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='1.5' || $star_rating=='1.6' || $star_rating=='1.7' || $star_rating=='1.8' || $star_rating=='1.9') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star-half-alt"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='2' || $star_rating=='2.1' || $star_rating=='2.2' || $star_rating=='2.3' || $star_rating=='2.4') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='2.5' || $star_rating=='2.6' || $star_rating=='2.7' || $star_rating=='2.8' || $star_rating=='2.9') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-half-alt"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='3' || $star_rating=='3.1' || $star_rating=='3.2' || $star_rating=='3.3' || $star_rating=='3.4') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='3.5' || $star_rating=='3.6' || $star_rating=='3.7' || $star_rating=='3.8' || $star_rating=='3.9') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-half-alt"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='4' || $star_rating=='4.1' || $star_rating=='4.2' || $star_rating=='4.3' || $star_rating=='4.4') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-empty"></i></span>';
												}
												if($star_rating=='4.5' || $star_rating=='4.6' || $star_rating=='4.7' || $star_rating=='4.8' || $star_rating=='4.9') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-half-alt"></i></span>';
												}
												if($star_rating=='5') {
													$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i></span>';
												}

												$WP_Compear_tool .= '<td class="'. $col_alignment[$spec_id].' '.$col_alignment_vert[$spec_id].' star-rating-field">'.$star_rating_html.'</td>';



											elseif($spec_type=='yes-no') :

												if($wpcompear_prods[$m][$spec_id] != ''):

													$spec_value = stripslashes($wpcompear_prods[$m][$spec_id]);

													if($spec_value=='yes') {
														$icon = '<i class="tool-yesno icon-ok"></i>';
													}
													elseif($spec_value=='no') {
														$icon = '<i class="tool-yesno icon-cancel"></i>';
													}

													$WP_Compear_tool .= '<td class="'. $col_alignment[$spec_id].' '.$col_alignment_vert[$spec_id].' yesno-field">'.$icon.'</div>';

												endif;

											endif;



                    					endforeach;

                    					$WP_Compear_tool .= '</tr>';

                    					$m++;

                    				endforeach;


                    			endif;



                    		$WP_Compear_tool .= '</tbody>';






							$WP_Compear_tool .= '</table>'; // end table.wpcompear-table-sortable
                    	
						$WP_Compear_tool .= '</div>'; // end .wp-compear-tool-table-outer
		    			$WP_Compear_tool .= '</div>';  // end .wp-compear-tool-wrapper


		    		endif; 










		    	elseif($list_type=='draganddrop') :


		    		$wpcompear_list_dragable_spec = get_post_meta( $atts['id'], '_wpcompear_list_dragable_spec', true );

		    		$wpcompear_list_col_alignment_dragndrop = get_post_meta( $atts['id'], '_wpcompear_list_col_alignment_dragndrop', true );


		    		if(is_array($wpcompear_prods)) :

		    			$m=0;
		    			$isFirst = true;

		    			$WP_Compear_tool .= '<div class="wp-compear-tool-wrapper draganddrop-outer '.$wpcompear_list_theme.' wp-compear-tool-'.$atts['id'].'">';
		    			$WP_Compear_tool .= '<div id="wp-compear-tool-draganddrop" class="wp-compear-tool-draganddrop">';


		    			$WP_Compear_tool .= '<div class="comparison">';

							$WP_Compear_tool .= '<div class="comparison-inner">';
						                    
								$WP_Compear_tool .= '<div class="box key">';

								    $WP_Compear_tool .= '<div class="specs-table">';

								    $j=1;

			                        foreach($wpcompear_specs as $wpcompear_single_spec) :
		    				
			    						$spec_type = $wpcompear_single_spec['spec_type'];
										$spec_name = $wpcompear_single_spec['spec_name'];
										$spec_id = $wpcompear_single_spec['spec_id'];

										$WP_Compear_tool .= '<span class="table-row">';

				                        	$WP_Compear_tool .= '<span class="data-spec-order-'.$j.'" data-spec-order="'.$j.'">'.$spec_name.'</span>';

				                        $WP_Compear_tool .= '</span>';

				                        $j++;

									endforeach;

									$WP_Compear_tool .= '</div>';

								$WP_Compear_tool .= '</div>';




			                    $WP_Compear_tool .= '<div class="box product product-1 empty '.$wpcompear_list_col_alignment_dragndrop.'">
			                        <p class="placeholder">Drag<br />Here</p>
			                    </div>

			                    <div class="box product product-2 empty '.$wpcompear_list_col_alignment_dragndrop.'">
			                        <p class="placeholder">Drag<br />Here</p>
			                    </div>

			                    <div class="box product product-3 empty '.$wpcompear_list_col_alignment_dragndrop.'">
			                        <p class="placeholder">Drag<br />Here</p>
			                    </div>

			                    <div class="clear"></div>

			                </div>

			            </div>';


			            $WP_Compear_tool .= '<div class="all-options">';

                    		$WP_Compear_tool .= '<div class="inner">';

                        		$WP_Compear_tool .= '<ul>';

		                            $n=0;
		                            $isFirst = true;


		                            foreach($wpcompear_prods as $wpcompear_single_prod) :

		                            	// skips first element in products array (cloning row) 
					    				if ($isFirst) {
									        $isFirst = false;
									        $n++;
									        continue;
									    }

		                            	foreach($wpcompear_specs as $wpcompear_single_spec) :
					    				
				    						$spec_type = $wpcompear_single_spec['spec_type'];
											$spec_name = $wpcompear_single_spec['spec_name'];
											$spec_id = $wpcompear_single_spec['spec_id'];

											if($wpcompear_list_dragable_spec==$spec_name):

			                            		$WP_Compear_tool .= '<li class="drag-product '.$spec_type.'" data-id="'.$n.'" data-topprod-number="top_product_'.$n.'">';
				                            		
				                            		if($spec_type=='image'):
				                            			$WP_Compear_tool .= '<img src="'.stripslashes($wpcompear_prods[$n][$spec_id]).'" alt="" />';
				                            		else:
				                            			$WP_Compear_tool .= stripslashes($wpcompear_prods[$n][$spec_id]);
				                            		endif;

				                            		$WP_Compear_tool .= '<span class="ghost" data-id="'.$n.'" data-topprod-number="top_product_'.$n.'">';

				                            		if($spec_type=='image'):
				                            			$WP_Compear_tool .= '<img src="'.stripslashes($wpcompear_prods[$n][$spec_id]).'" alt="" />';
				                            		else:
				                            			$WP_Compear_tool .= stripslashes($wpcompear_prods[$n][$spec_id]);
				                            		endif;

				                            		$WP_Compear_tool .= '</span>';
			                            		$WP_Compear_tool .= '</li>';

			                            	endif;

		                                	

		                           		endforeach;

		                           		$n++;

		                           	endforeach;


            					$WP_Compear_tool .= '   </ul>

			                        <div class="clear"></div>

			                    </div>

			                </div>';




			                $WP_Compear_tool .= '<div class="dragondrop-hidden-product-info" style="display:none!important;">';



			                	$j=0;
				    			$isFirst = true;


				    			// loop through all products
				    			foreach($wpcompear_prods as $wpcompear_single_prod) :

				    			
				    				// skips first element in products array (cloning row) 
				    				if ($isFirst) {
								        $isFirst = false;
								        $j++;
								        continue;
								    }

				    				$WP_Compear_tool .= '<span class="top_product_'.$j.'">';

				    				$count_specs = 1;

					    				foreach($wpcompear_specs as $wpcompear_single_spec) :

				    						//$pos_num = $n+1;
                                			
				    				
				    						$spec_type = $wpcompear_single_spec['spec_type'];
											$spec_name = $wpcompear_single_spec['spec_name'];
											$spec_id = $wpcompear_single_spec['spec_id'];

											//$wpcompear_prods[$m][$spec_id]


											

                                            	$WP_Compear_tool .= '<span class="table-row">';


                                            		

                                            			if($spec_type=='text-field') :

                                            				$WP_Compear_tool .= '<span class="data-spec-order-'.$count_specs.' text-field" data-spec-order="'.$count_specs.'">';

															if($wpcompear_prods[$j][$spec_id] != ''):

																$spec_value = stripslashes($wpcompear_prods[$j][$spec_id]);
																$WP_Compear_tool .= $spec_value;

															endif;


														elseif($spec_type=='text-paragraph') :

															$WP_Compear_tool .= '<span class="data-spec-order-'.$count_specs.' paragraph-field" data-spec-order="'.$count_specs.'">';

															if($wpcompear_prods[$j][$spec_id] != ''):

																$spec_value = stripslashes($wpcompear_prods[$j][$spec_id]);
																$WP_Compear_tool .= $spec_value;

															endif;

														elseif($spec_type=='wysiwyg') :

															$WP_Compear_tool .= '<span class="data-spec-order-'.$count_specs.' wysiwyg-field" data-spec-order="'.$count_specs.'">';

															if($wpcompear_prods[$j][$spec_id] != ''):

																$content_stripped = stripslashes($wpcompear_prods[$j][$spec_id]);
																//old way of parsing shortcodes
																$content = apply_filters('the_content', $content_stripped);
																// new way of parsing shortcodes
																$content = do_shortcode($content_stripped);

																$WP_Compear_tool .= $content;

															endif;


														elseif($spec_type=='image') :

															$WP_Compear_tool .= '<span class="data-spec-order-'.$count_specs.' image-field" data-spec-order="'.$count_specs.'">';

															if($wpcompear_prods[$j][$spec_id] != ''):

																$WP_Compear_tool .= '<img src="'.$wpcompear_prods[$j][$spec_id].'" alt="" />';

															endif;


														elseif($spec_type=='star-rating') :

															$WP_Compear_tool .= '<span class="data-spec-order-'.$count_specs.' star-rating-field" data-spec-order="'.$count_specs.'">';

															if($wpcompear_prods[$j][$spec_id] != ''):

																$star_rating = $wpcompear_prods[$j][$spec_id];

																if($star_rating=='no-rating') {
																	$star_rating_html = '<span class="star-rating-txt">'.__('No Ratings Yet','wp-compear').'</span><span class="star-rating" title="No Ratings Yet" >No Ratings Yet</span>';
																}
																if($star_rating=='0') {
																	$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
																}
																if($star_rating=='0.1' || $star_rating=='0.2' || $star_rating=='0.3' || $star_rating=='0.4' || $star_rating=='0.5' || $star_rating=='0.6' || $star_rating=='0.7' || $star_rating=='0.8' || $star_rating=='0.9') {
																	$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star-half-alt"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
																}
																if($star_rating=='1' || $star_rating=='1.1' || $star_rating=='1.2' || $star_rating=='1.3' || $star_rating=='1.4') {
																	$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
																}
																if($star_rating=='1.5' || $star_rating=='1.6' || $star_rating=='1.7' || $star_rating=='1.8' || $star_rating=='1.9') {
																	$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star-half-alt"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
																}
																if($star_rating=='2' || $star_rating=='2.1' || $star_rating=='2.2' || $star_rating=='2.3' || $star_rating=='2.4') {
																	$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
																}
																if($star_rating=='2.5' || $star_rating=='2.6' || $star_rating=='2.7' || $star_rating=='2.8' || $star_rating=='2.9') {
																	$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-half-alt"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
																}
																if($star_rating=='3' || $star_rating=='3.1' || $star_rating=='3.2' || $star_rating=='3.3' || $star_rating=='3.4') {
																	$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-empty"></i><i class="icon-star-empty"></i></span>';
																}
																if($star_rating=='3.5' || $star_rating=='3.6' || $star_rating=='3.7' || $star_rating=='3.8' || $star_rating=='3.9') {
																	$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-half-alt"></i><i class="icon-star-empty"></i></span>';
																}
																if($star_rating=='4' || $star_rating=='4.1' || $star_rating=='4.2' || $star_rating=='4.3' || $star_rating=='4.4') {
																	$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-empty"></i></span>';
																}
																if($star_rating=='4.5' || $star_rating=='4.6' || $star_rating=='4.7' || $star_rating=='4.8' || $star_rating=='4.9') {
																	$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star-half-alt"></i></span>';
																}
																if($star_rating=='5') {
																	$star_rating_html = '<span class="star-rating-txt">'.$star_rating.' '.__('Star Rating','wp-compear').'</span><span class="star-rating" title="'.$star_rating.' '.__('Star Rating','wp-compear').'"><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i></span>';
																}

																$WP_Compear_tool .= $star_rating_html;

															endif;
															

														elseif($spec_type=='yes-no') :

															if($wpcompear_prods[$m][$spec_id] != ''):

																$spec_value = stripslashes($wpcompear_prods[$j][$spec_id]);

																if($spec_value=='yes') {
																	$icon = '<i class="tool-yesno icon-ok"></i>';
																}
																else {
																	$icon = '<i class="tool-yesno icon-cancel"></i>';
																}

																$WP_Compear_tool .= '<span class="data-spec-order-'.$count_specs.' yesno-field" data-spec-order="'.$count_specs.'">';

																if($wpcompear_prods[$j][$spec_id] != ''):

																	
																	$WP_Compear_tool .= $icon;

																endif;

															endif;

														endif;


                                            		$WP_Compear_tool .= '</span>'; // end .data-spec-order-NUM


                                            	$WP_Compear_tool .= '</span>'; // end .table-row




											$count_specs++;

					    				endforeach;

				    				$WP_Compear_tool .= '</span>'; // end .top_product_NUM

				    				$j++;

				    			endforeach;






			                $WP_Compear_tool .= '</div>'; // end .dragondrop-hidden-product-info





			                $WP_Compear_tool .= '</div>'; // end .wp-compear-tool-draganddrop




		    			$WP_Compear_tool .= '</div>';
		    			


		    		endif;




		    	else :

		    		$WP_Compear_tool .= '<div class="wp-compear-tool-wrapper"></div>';


		    	endif; 




			



			return $WP_Compear_tool;

		}

		add_shortcode( 'wp-compear', 'WP_Compear_shortcode', true );

		
	}

	

}
