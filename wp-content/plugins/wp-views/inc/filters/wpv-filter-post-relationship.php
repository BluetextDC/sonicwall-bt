<?php

/**
* Post relationship filter
*
* @package Views
*
* @since unknown
* @since 1.12.1	Changes in the filter modes
* 		current_page becomes top_current_post and tracks $WP_Views->get_top_current_page()
* 		parent_view becomes current_post_or_parent_post_view and tracks $WP_Views->get_current_page()
* @since 2.1	Added to WordPress Archives
* @since 2.1	Include this file only when editing a View or WordPress Archive, or when doing AJAX
*/

WPV_Post_Relationship_Filter::on_load();

/**
* WPV_Post_Relationship_Filter
*
* Views Post Relationship Filter Class
*
* @since 1.7.0
*/

class WPV_Post_Relationship_Filter {

    static function on_load() {
        add_action( 'init',			array( 'WPV_Post_Relationship_Filter', 'init' ) );
		add_action( 'admin_init',	array( 'WPV_Post_Relationship_Filter', 'admin_init' ) );
		// Scripts
		add_action( 'admin_enqueue_scripts', array( 'WPV_Post_Relationship_Filter','admin_enqueue_scripts' ), 20 );
		// Custom search shortcode GUI
		add_filter( 'wpv_filter_wpv_register_form_filters_shortcodes', array( 'WPV_Post_Relationship_Filter', 'wpv_custom_search_filter_shortcodes_post_relationship' ), 0 );
    }

    static function init() {
		wp_register_script( 
			'views-filter-post-relationship-js', 
			WPV_URL . "/res/js/filters/views_filter_post_relationship.js", 
			array( 'suggest', 'views-filters-js'), 
			WPV_VERSION, 
			false
		);
		$pr_strings = array(
			'post'		=> array(
								'post_type_missing'	=> __( 'There is no post type selected in the Content Selection section', 'wpv-views' ),
								'post_type_orphan'	=> __( 'This will filter out posts of the following types, because they are not children of any other post type: %s', 'wpv-views' ),
							),
			'archive'	=> array(
								'disable_post_relationship_filter'	=> __( 'This filter will only return posts that belong to a Types child post type', 'wpv-views' ),
							),
		);
		wp_localize_script( 'views-filter-post-relationship-js', 'wpv_pr_strings', $pr_strings );
    }
	
	static function admin_init() {
		// Register filters in dialogs
		add_filter( 'wpv_filters_add_filter',							array( 'WPV_Post_Relationship_Filter', 'wpv_filters_add_filter_relationship' ), 1, 2 );
		add_filter( 'wpv_filters_add_archive_filter',					array( 'WPV_Post_Relationship_Filter', 'wpv_filters_add_archive_filter_post_relationship' ), 1, 1 );
		// Register filters in lists
		add_action( 'wpv_add_filter_list_item',							array( 'WPV_Post_Relationship_Filter', 'wpv_add_filter_post_relationship_list_item' ), 1, 1 );
		// Update and delete
		add_action( 'wp_ajax_wpv_filter_post_relationship_update',		array( 'WPV_Post_Relationship_Filter', 'wpv_filter_post_relationship_update_callback' ) );
		add_action( 'wp_ajax_wpv_filter_post_relationship_delete',		array( 'WPV_Post_Relationship_Filter', 'wpv_filter_post_relationship_delete_callback' ) );
		// Helper callbacks
		add_action( 'wp_ajax_wpv_get_post_relationship_post_select',	array( 'WPV_Post_Relationship_Filter', 'wpv_get_post_relationship_post_select_callback' ) );
		// TODO This might not be needed here, maybe for summary filter
		//add_action( 'wp_ajax_wpv_filter_post_relationship_sumary_update', array( 'WPV_Post_Relationship_Filter', 'wpv_filter_post_relationship_sumary_update_callback' ) );
	}
	
	/**
	* admin_enqueue_scripts
	*
	* Register the needed script for this filter
	*
	* @since 1.7
	*/
	
	static function admin_enqueue_scripts( $hook ) {
		wp_enqueue_script( 'views-filter-post-relationship-js' );
	}
	
	/**
	* wpv_filters_add_filter_relationship
	*
	* Register the post relationship filter in the popup dialog
	*
	* @param $filters
	*
	* @since unknown
	*/
	
	static function wpv_filters_add_filter_relationship( $filters, $post_type ) {
		if ( function_exists( 'wpcf_pr_get_belongs' ) ) {
			$filters['post_relationship'] = array(
				'name'		=> __( 'Post relationship - Post is a child of', 'wpv-views' ),
				'present'	=> 'post_relationship_mode',
				'callback'	=> array( 'WPV_Post_Relationship_Filter', 'wpv_add_new_filter_post_relationship_list_item' ),
				'args'		=> $post_type,
				'group'		=> __( 'Post filters', 'wpv-views' )
			);
		}
		return $filters;
	}
	
	/**
	* wpv_filters_add_archive_filter_post_relationship
	*
	* Register the post relationship filter in the popup dialog for WPAs.
	*
	* @param $filters
	*
	* @since 2.1
	*/
	static function wpv_filters_add_archive_filter_post_relationship( $filters ) {
		if ( function_exists( 'wpcf_pr_get_belongs' ) ) {
			$filters['post_relationship'] = array(
				'name'		=> __( 'Post relationship - Post is a child of', 'wpv-views' ),
				'present'	=> 'post_relationship_mode',
				'callback'	=> array( 'WPV_Post_Relationship_Filter', 'wpv_add_new_archive_filter_post_relationship_list_item' ),
				'group'		=> __( 'Post filters', 'wpv-views' )
			);
		}
		return $filters;
	}
	
	/**
	* wpv_add_new_filter_post_relationship_list_item
	*
	* Register the post relationship filter in the filters list
	*
	* @param $post_type array
	*
	* @since unknown
	*/
	
	static function wpv_add_new_filter_post_relationship_list_item( $post_type ) {
		if ( function_exists( 'wpcf_pr_get_belongs' ) ) {
			$args = array(
				'view-query-mode'			=> 'normal',
				'post_relationship_mode'	=> array( 'top_current_post' ),
				'post_type'					=> $post_type
			);
			WPV_Post_Relationship_Filter::wpv_add_filter_post_relationship_list_item( $args );
		}
	}
	
	/**
	* wpv_add_new_filter_post_relationship_list_item
	*
	* Register the post relationship filter in the filters list
	*
	* @param $post_type array
	*
	* @since 2.1
	*/
	static function wpv_add_new_archive_filter_post_relationship_list_item() {
		if ( function_exists( 'wpcf_pr_get_belongs' ) ) {
			$args = array(
				'view-query-mode'			=> 'archive',
				'post_relationship_mode'	=> array( 'this_page' ),
			);
			WPV_Post_Relationship_Filter::wpv_add_filter_post_relationship_list_item( $args );
		}
	}
	
	/**
	 * wpv_add_filter_post_relationship_list_item
	 *
	 * Render post relationship filter item in the filters list
	 *
	 * @param $view_settings
	 *
	 * @since unknown
	 */
	static function wpv_add_filter_post_relationship_list_item( $view_settings ) {
		if ( function_exists( 'wpcf_pr_get_belongs' ) ) {
			if ( isset( $view_settings['post_relationship_mode'][0] ) ) {
				$li = WPV_Post_Relationship_Filter::wpv_get_list_item_ui_post_post_relationship( $view_settings );
				WPV_Filter_Item::simple_filter_list_item( 'post_relationship', 'posts', 'post-relationship', __( 'Post relationship filter', 'wpv-views' ), $li );
			}
		}
	}
	
	/**
	* wpv_get_list_item_ui_post_post_relationship
	*
	* Render post relationship filter item content in the filters list
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_get_list_item_ui_post_post_relationship( $view_settings = array() ) {
		if ( isset( $view_settings['post_relationship_mode'] ) && is_array( $view_settings['post_relationship_mode'] ) ) {
			$view_settings['post_relationship_mode'] = $view_settings['post_relationship_mode'][0];
		}
		if (
			isset( $view_settings['post_relationship_id'] ) 
			&& ! empty( $view_settings['post_relationship_id'] )
		) {
			// Adjust for WPML support
			$view_settings['post_relationship_id'] = apply_filters( 'translate_object_id', $view_settings['post_relationship_id'], 'any', true, null );
		}
		if ( ! isset( $view_settings['post_type'] ) ) {
			$view_settings['post_type'] = array();
		}
		ob_start()
		?>
		<p class='wpv-filter-post-relationship-edit-summary js-wpv-filter-summary js-wpv-filter-post-relationship-summary'>
			<?php echo wpv_get_filter_post_relationship_summary_txt( $view_settings ); ?>
		</p>
		<?php
		WPV_Filter_Item::simple_filter_list_item_buttons( 'post-relationship', 'wpv_filter_post_relationship_update', wp_create_nonce( 'wpv_view_filter_post_relationship_nonce' ), 'wpv_filter_post_relationship_delete', wp_create_nonce( 'wpv_view_filter_post_relationship_delete_nonce' ) );
		?>
		<div id="wpv-filter-post-relationship-edit" class="wpv-filter-edit js-wpv-filter-edit" style="padding-bottom:28px;">
			<div id="wpv-filter-post-relationship" class="js-wpv-filter-options js-wpv-filter-post-relationship-options">
				<?php WPV_Post_Relationship_Filter::wpv_render_post_relationship( $view_settings ); ?>
			</div>
			<div class="js-wpv-filter-toolset-messages"></div>
			<span class="filter-doc-help">
				<a class="wpv-help-link" target="_blank" href="http://wp-types.com/documentation/user-guides/querying-and-displaying-child-posts/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-view-relationships-filter&utm_term=Querying and Displaying Child Posts">
					<?php _e('Querying and Displaying Child Posts', 'wpv-views'); ?>
				 &raquo;</a>
			</span>
		</div>
		<?php
		$res = ob_get_clean();
		return $res;
	}
	
	/**
	* wpv_filter_post_relationship_update_callback
	*
	* Update post relationship filter callback
	*
	* @since unknown
	*/

	static function wpv_filter_post_relationship_update_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_post_relationship_nonce' ) 
		) {
			$data = array(
				'type' => 'nonce',
				'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if (
			! isset( $_POST["id"] )
			|| ! is_numeric( $_POST["id"] )
			|| intval( $_POST['id'] ) < 1 
		) {
			$data = array(
				'type' => 'id',
				'message' => __( 'Wrong or missing ID.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( empty( $_POST['filter_options'] ) ) {
			$data = array(
				'type' => 'data_missing',
				'message' => __( 'Wrong or missing data.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		$change = false;
		$view_id = intval( $_POST['id'] );
		parse_str( $_POST['filter_options'], $filter_relationship );
		$view_array = get_post_meta( $view_id, '_wpv_settings', true );
		if ( ! isset( $filter_relationship['post_relationship_id'] ) ) {
			$filter_relationship['post_relationship_id'] = 0;
		}
		$settings_to_check = array(
			'post_relationship_mode',
			'post_relationship_id',
			'post_relationship_shortcode_attribute',
			'post_relationship_url_parameter',
			'post_relationship_framework'
		);
		foreach ( $settings_to_check as $set ) {
			if ( 
				isset( $filter_relationship[$set] ) 
				&& (
					! isset( $view_array[$set] ) 
					|| $filter_relationship[$set] != $view_array[$set] 
				)
			) {
				if ( is_array( $filter_relationship[$set] ) ) {
					$filter_relationship[$set] = array_map( 'sanitize_text_field', $filter_relationship[$set] );
				} else {
					$filter_relationship[$set] = sanitize_text_field( $filter_relationship[$set] );
				}
				$change = true;
				$view_array[$set] = $filter_relationship[$set];
			}
		}
		if ( $change ) {
			$result = update_post_meta( $view_id, '_wpv_settings', $view_array );
			do_action( 'wpv_action_wpv_save_item', $view_id );
		}
		
		$parametric_search_hints = wpv_get_parametric_search_hints_data( $view_id );
		
		$data = array(
			'id'			=> $view_id,
			'message'		=> __( 'Specific users filter saved', 'wpv-views' ),
			'summary'		=> wpv_get_filter_post_relationship_summary_txt( $filter_relationship ),
			'parametric'	=> $parametric_search_hints
		);
		wp_send_json_success( $data );
	}
	
	/**
	* Update post relationship filter summary callback
	*/
	/*
	static function wpv_filter_post_relationship_sumary_update_callback() {
		$nonce = $_POST["wpnonce"];
		if ( ! wp_verify_nonce( $nonce, 'wpv_view_filter_post_relationship_nonce' ) ) {
			die( "Security check" );
		}
		if ( ! isset( $_POST['post_relationship_id'] ) ) {
			$_POST['post_relationship_id'] = 0;
		}
		echo wpv_get_filter_post_relationship_summary_txt(
			array(
				'post_relationship_mode'	=> $_POST['post_relationship_mode'],
				'post_relationship_id'		=> $_POST['post_relationship_id']
			)
		);
		die();
	}
	*/
	
	/**
	* wpv_filter_post_relationship_delete_callback
	*
	* Delete post relationship filter callback
	*
	* @since unknown
	*/

	static function wpv_filter_post_relationship_delete_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST["wpnonce"] )
			|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_filter_post_relationship_delete_nonce' ) 
		) {
			$data = array(
				'type' => 'nonce',
				'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if (
			! isset( $_POST["id"] )
			|| ! is_numeric( $_POST["id"] )
			|| intval( $_POST['id'] ) < 1 
		) {
			$data = array(
				'type' => 'id',
				'message' => __( 'Wrong or missing ID.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		$view_array = get_post_meta( $_POST["id"], '_wpv_settings', true );
		$to_delete = array(
			'post_relationship_mode',
			'post_relationship_id',
			'post_relationship_shortcode_attribute',
			'post_relationship_url_parameter',
			'post_relationship_url_tree',
			'post_relationship_framework',
			// Backwards compatibility: 
			// those entries existed in the View settings up until 2.4.0
			'filter_controls_field_name',
			'filter_controls_mode',
			'filter_controls_label',
			'filter_controls_type',
			'filter_controls_values',
			'filter_controls_enable',
			'filter_controls_param'
		);
		foreach ( $to_delete as $index ) {
			if ( isset( $view_array[$index] ) ) {
				unset( $view_array[$index] );
			}
		}
		update_post_meta( $_POST["id"], '_wpv_settings', $view_array );
		do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
		
		$parametric_search_hints = wpv_get_parametric_search_hints_data( $_POST["id"] );
		
		$data = array(
			'id'			=> $_POST["id"],
			'parametric'	=> $parametric_search_hints,
			'message'		=> __( 'Post relationship filter deleted', 'wpv-views' )
		);
		wp_send_json_success( $data );
	}
	
	/**
	* get_options_by_query_mode
	*
	* Define which options will be offered depending on the query mode.
	*
	* @param $query_mode	string	'normal'|'archive'|?
	*
	* @since 2.1
	*/
	
	static function get_options_by_query_mode( $query_mode = 'normal' ) {
		$options = array();
		if ( 'normal' == $query_mode ) {
			$options = array( 'top_current_post', 'current_post_or_parent_post_view', 'this_page', 'shortcode_attribute', 'url_parameter', 'framework' );
		} else {
			$options = array( 'this_page', 'url_parameter', 'framework' );
		}
		return $options;
	}
	
	/**
	* render_options_by_post_relationship_mode
	*
	* Render each filter option.
	*
	* @param $parent_mode	string	'top_current_post'|'current_post_or_parent_post_view'|'this_page'|'shortcode_attribute'|'url_parameter'|'framework'
	* @param @view_settings	array	The View settings.
	*
	* @since 2.1
	*/
	
	static function render_options_by_post_relationship_mode( $post_relationship_mode, $view_settings ) {
		switch ( $post_relationship_mode ) {
			case 'top_current_post':
				?>
				<li>
					<input type="radio" id="post-relationship-mode-current-page" class="js-post-relationship-mode" name="post_relationship_mode[]" value="top_current_post" <?php checked( in_array( $view_settings['post_relationship_mode'], array( 'current_page', 'top_current_post' ) ) ); ?> autocomplete="off" />
					<label for="post-relationship-mode-current-page"><?php _e('Post where this View is shown', 'wpv-views'); ?></label>
				</li>
				<?php
				break;
			case 'current_post_or_parent_post_view':
				?>
				<li>
					<input type="radio" id="post-relationship-mode-parent-view" class="js-post-relationship-mode" name="post_relationship_mode[]" value="current_post_or_parent_post_view" <?php checked( in_array( $view_settings['post_relationship_mode'], array( 'parent_view', 'current_post_or_parent_post_view' ) ) ); ?> autocomplete="off" />
					<label for="post-relationship-mode-parent-view"><?php _e('The current post in the loop', 'wpv-views'); ?></label>
				</li>
				<?php
				break;
			case 'this_page':
				?>
				<li>
				<input type="radio" id="post-relationship-mode-this-page" class="js-post-relationship-mode" name="post_relationship_mode[]" value="this_page" <?php checked( $view_settings['post_relationship_mode'], 'this_page' ); ?> autocomplete="off" />
				<label for="post-relationship-mode-this-page"><?php _e('Specific:', 'wpv-views'); ?></label>
				<select id="wpv_post_relationship_post_type" name="post_relationship_type" class="js-post-relationship-post-type" data-nonce="<?php echo wp_create_nonce( 'wpv_view_filter_post_relationship_post_type_nonce' ); ?>" autocomplete="off">
				<?php
				$post_types = get_post_types( array( 'public' => true ), 'objects' );
				if ( 
					$view_settings['post_relationship_id'] == 0 
					|| $view_settings['post_relationship_id'] == '' 
				) {
					$selected_type = 'page';
				} else {
					global $wpdb;
					$selected_type = $wpdb->get_var( 
						$wpdb->prepare(
							"SELECT post_type FROM {$wpdb->posts} 
							WHERE ID = %d 
							LIMIT 1",
							$view_settings['post_relationship_id']
						)
					);
					if ( ! $selected_type ) {
						$selected_type = 'page';
					}
				}
				foreach ( $post_types as $post_type ) {
					?>
					<option value="<?php echo esc_attr( $post_type->name ); ?>" <?php selected( $selected_type, $post_type->name ); ?>><?php echo $post_type->labels->singular_name; ?></option>
					<?php 
				}
				?>
				</select>
				<?php 
				$dropdown_args = array(
					'post_type'		=> $selected_type,
					'name'			=> 'post_relationship_id',
					'selected'		=> (int) $view_settings['post_relationship_id']
				);
				wpv_render_posts_select_dropdown( $dropdown_args );
				?>
			</li>
				<?php
				break;
			case 'shortcode_attribute':
				?>
				<li>
					<input type="radio" id="post-relationship-mode-shortcode" class="js-post-relationship-mode" name="post_relationship_mode[]" value="shortcode_attribute" <?php checked( $view_settings['post_relationship_mode'], 'shortcode_attribute' ); ?> autocomplete="off" />
					<label for="post-relationship-mode-shortcode"><?php _e('Post with ID set by the shortcode attribute', 'wpv-views'); ?></label>
					<input class="js-post-relationship-shortcode-attribute js-wpv-filter-validate" name="post_relationship_shortcode_attribute" data-type="shortcode" type="text" value="<?php echo esc_attr( $view_settings['post_relationship_shortcode_attribute'] ); ?>" autocomplete="off" />
				</li>
				<?php
				break;
			case 'url_parameter':
				?>
				<li>
					<input type="radio" id="post-relationship-mode-url" class="js-post-relationship-mode" name="post_relationship_mode[]" value="url_parameter" <?php checked( $view_settings['post_relationship_mode'], 'url_parameter' ); ?> autocomplete="off" />
					<label for="post-relationship-mode-url"><?php _e('Post with ID set by the URL parameter', 'wpv-views'); ?></label>
					<input class="js-post-relationship-url-parameter js-wpv-filter-validate" name="post_relationship_url_parameter" data-type="url" type="text" value="<?php echo esc_attr( $view_settings['post_relationship_url_parameter'] ); ?>" autocomplete="off" />
				</li>
				<?php
				break;
			case 'framework':
				global $WP_Views_fapi;
				if ( $WP_Views_fapi->framework_valid ) {
					$framework_data = $WP_Views_fapi->framework_data
				?>
				<li>
					<input type="radio" id="post-relationship-mode-framework" class="js-post-relationship-mode" name="post_relationship_mode[]" value="framework" <?php checked( $view_settings['post_relationship_mode'], 'framework' ); ?> autocomplete="off" />
					<label for="post-relationship-mode-framework"><?php echo sprintf( __( 'Post with ID set by the %s key: ', 'wpv-views'), sanitize_text_field( $framework_data['name'] ) ); ?></label>
					<select name="post_relationship_framework" autocomplete="off">
						<option value=""><?php _e( 'Select a key', 'wpv-views' ); ?></option>
						<?php
						$fw_key_options = array();
						$fw_key_options = apply_filters( 'wpv_filter_extend_framework_options_for_post_relationship', $fw_key_options );
						foreach ( $fw_key_options as $index => $value ) {
							?>
							<option value="<?php echo esc_attr( $index ); ?>" <?php selected( $view_settings['post_relationship_framework'], $index ); ?>><?php echo $value; ?></option>
							<?php
						}
						?>
					</select>
				</li>
				<?php
				}
				break;
		};
	}
	
	/**
	* wpv_render_post_relationship
	*
	* Render post relationship filter options
	*
	* @param $view_settings
	*
	* @since unknown
	*/

	static function wpv_render_post_relationship( $view_settings = array() ) {
		$defaults = array(
			'view-query-mode'						=> 'normal',
			'post_relationship_mode'				=> 'top_current_post',
			'post_relationship_id'					=> 0,
			'post_relationship_shortcode_attribute'	=> 'wpvprchildof',
			'post_relationship_url_parameter'		=> 'wpv-pr-child-of',
			'post_relationship_framework'			=> ''
		);
		$view_settings = wp_parse_args( $view_settings, $defaults );
		?>
		<h4><?php _e( 'Select posts that are children of...', 'wpv-views' ); ?></h4>
		<ul class="wpv-filter-options-set">
			<?php
			$options_to_render = WPV_Post_Relationship_Filter::get_options_by_query_mode( $view_settings['view-query-mode'] );
			foreach ( $options_to_render as $renderer ) {
				WPV_Post_Relationship_Filter::render_options_by_post_relationship_mode( $renderer, $view_settings );
			}
			?>
		</ul>
		<?php
	}
	
	/**
	* wpv_get_post_relationship_post_select_callback
	*
	* Render a select dropdown given a post type
	*
	* @since unknown
	*/
	
	static function wpv_get_post_relationship_post_select_callback() {
		$nonce = $_POST["wpnonce"];
		if ( ! wp_verify_nonce( $nonce, 'wpv_view_filter_post_relationship_post_type_nonce' ) ) {
			die( "Security check" );
		}
		$dropdown_args = array(
			'post_type'		=> sanitize_text_field( $_POST['post_type'] ),
			'name'			=> 'post_relationship_id'
		);
		wpv_render_posts_select_dropdown( $dropdown_args );
		die();
	}

	//----------------------------------------------------------------

	/**
	* DEPRECATED maybe used by MM?
	*/
	/*
	static function wpv_ajax_wpv_get_post_relationship_info() { // TODO check if this is deprecated
		if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_get_posts_select_nonce')) {
			if (function_exists('wpcf_pr_get_belongs') && isset($_POST['post_types'])) {
				$post_types = get_post_types('', 'objects');

				$output_done = false;
				foreach ($_POST['post_types'] as $post_type) {

					$related = wpcf_pr_get_belongs($post_type);
					if ($related === false) {
						echo sprintf(__('Post type <strong>%s</strong> doesn\'t belong to any other post type', 'wpv-views'), $post_types[$post_type]->labels->singular_name, $related);
						echo '<br />';
						$output_done = true;
					}
					if (is_array($related) && count($related)) {
						$keys = array_keys($related);
						$related = array();

						foreach($keys as$key) {
							$related[] = $post_types[$key]->labels->singular_name;
						}

					}
					if (is_array($related) && count($related) == 1) {
						$related = implode(', ', $related);
						echo sprintf(__('Post type <strong>%s</strong> is a child of <strong>%s</strong> post type', 'wpv-views'), $post_types[$post_type]->labels->singular_name, $related);
						echo '<br />';
						$output_done = true;
					}
					if (is_array($related) && count($related) > 1) {
						$last = array_pop($related);
						$related = implode(', ', $related);
						$related .= __(' and ') . $last;
						echo sprintf(__('Post type <strong>%s</strong> is a child of <strong>%s</strong> post types', 'wpv-views'), $post_types[$post_type]->labels->singular_name, $related);
						echo '<br />';
						$output_done = true;
					}
				}
				if ($output_done) {
					echo '<br />';
				}
			}

		}
		die();
	}
	*/
	
	/**
	 * Register the wpv-control-post-relationship shortcode filter.
	 *
	 * @since 2.4.0
	 */
	
	static function wpv_custom_search_filter_shortcodes_post_relationship( $form_filters_shortcodes ) {
		$form_filters_shortcodes['wpv-control-post-relationship'] = array(
			'query_type_target'				=> 'posts',
			'query_filter_define_callback'	=> array( 'WPV_Post_Relationship_Filter', 'query_filter_define_callback' ),
			'custom_search_filter_group'	=> __( 'Post filters', 'wpv-views' ),
			'custom_search_filter_items'	=> array(
												'post_relationship'	=> array(
													'name'			=> __( 'Post relationship', 'wpv-views' ),
													'present'		=> 'post_relationship_mode',
													'params'	=> array()
												)
			)
		);
		return $form_filters_shortcodes;
	}
	
	/**
	 * Callback to create or modify the query filter after creating or editing the custom search shortcode.
	 *
	 * @param $view_id		int		The View ID
	 * @param $shortcode		string	The affected shortcode, wpv-control-post-relationship
	 * @param $attributes	array	The associative array of attributes for this shortcode
	 * @param $attributes_raw array	The associative array of attributes for this shortcode, as collected from its dialog, before being filtered
	 *
	 * @uses wpv_action_wpv_save_item
	 *
	 * @since 2.4.0
	 */
	
	static function query_filter_define_callback( $view_id, $shortcode, $attributes = array(), $attributes_raw = array() ) {
		if ( ! isset( $attributes['url_param'] ) ) {
			return;
		}
		$view_array = get_post_meta( $view_id, '_wpv_settings', true );
		$view_array['post_relationship_mode']			= array( 'url_parameter' );
		$view_array['post_relationship_url_parameter']	= $attributes['url_param'];
		$view_array['post_relationship_url_tree']		= isset( $attributes['ancestors'] ) ? $attributes['ancestors'] : '';
		$result = update_post_meta( $view_id, '_wpv_settings', $view_array );
		do_action( 'wpv_action_wpv_save_item', $view_id );
	}

}
