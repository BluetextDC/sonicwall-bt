<?php

/**
* Post Relationship frontend filter
*
* @package Views
*
* @since 2.1
*/

WPV_Post_Relationship_Frontend_Filter::on_load();

/**
* WPV_Parent_Frontend_Filter
*
* Views Post Relationship Filter Frontend Class
*
* @since 2.1
*/

class WPV_Post_Relationship_Frontend_Filter {
	
	static function on_load() {
		// Apply frontend filter by post relationship
        add_filter( 'wpv_filter_query',										array( 'WPV_Post_Relationship_Frontend_Filter', 'filter_post_relationship' ), 11, 3 );
		add_action( 'wpv_action_apply_archive_query_settings',				array( 'WPV_Post_Relationship_Frontend_Filter', 'archive_filter_post_relationship' ), 40, 3 );
		// Auxiliar methods for requirements
		add_filter( 'wpv_filter_requires_current_page',						array( 'WPV_Post_Relationship_Frontend_Filter', 'requires_current_page' ), 10, 2 );
		add_filter( 'wpv_filter_requires_parent_post',						array( 'WPV_Post_Relationship_Frontend_Filter', 'requires_parent_post' ), 20, 2 );
		add_filter( 'wpv_filter_requires_framework_values',					array( 'WPV_Post_Relationship_Frontend_Filter', 'requires_framework_values' ), 20, 2 );
		// Auxiliar methods for gathering data
		add_filter( 'wpv_filter_register_shortcode_attributes_for_posts',	array( 'WPV_Post_Relationship_Frontend_Filter', 'shortcode_attributes' ), 10, 2 );
		add_filter( 'wpv_filter_register_url_parameters_for_posts',			array( 'WPV_Post_Relationship_Frontend_Filter', 'url_parameters' ), 10, 2 );
		// Extra methods
		add_action( 'wpv-before-display-post',								array( 'WPV_Post_Relationship_Frontend_Filter', 'wpv_before_display_post_post_relationship' ), 10, 2 );
		
		add_shortcode( 'wpv-control-post-relationship',						array( 'WPV_Post_Relationship_Frontend_Filter', 'wpv_shortcode_wpv_control_post_relationship' ) );
		add_shortcode( 'wpv-control-post-ancestor',							array( 'WPV_Post_Relationship_Frontend_Filter', 'wpv_shortcode_wpv_control_post_ancestor' ) );
		add_filter( 'wpv_filter_wpv_shortcodes_gui_data',					array( 'WPV_Post_Relationship_Frontend_Filter', 'wpv_shortcodes_register_wpv_control_post_relationship_data' ) );
    }
	
	/**
	* filter_post_relationship
	*
	* Add the filter by post relationship to the $query
	*
	* This function adds the filter by post relationship to the $query.
	* It uses an additional auxiliary query, because post relationships are stored in custom fields, and we need to intersect those two filters.
	* It usually takes a parent ID to execute the filter, but when filtering by URL parameter we must accept multiple parent IDs.
	*
	* @param $query
	* @param $view_settings
	*
	* @return $query
	*
	* @since unknown
	* @since 2.1		Renamed from wpv_filter_post_relationship and moved to a static method
	*/
	
	static function filter_post_relationship( $query, $view_settings, $view_id ) {
		if ( isset( $view_settings['post_relationship_mode'][0] ) ) {
			$post_relationship_query = WPV_Post_Relationship_Frontend_Filter::get_settings( $query, $view_settings, $view_id );
			if ( count( $post_relationship_query['post__in'] ) > 0 ) {
				if ( isset( $query['post__in'] ) ) {
					$query['post__in'] = array_intersect( (array) $query['post__in'], $post_relationship_query['post__in'] );
					$query['post__in'] = array_values( $query['post__in'] );
					if ( empty( $query['post__in'] ) ) {
						$query['post__in'] = array( '0' );
					}
				} else {
					$query['post__in'] = $post_relationship_query['post__in'];
				}
			}
			if ( count( $post_relationship_query['pr_filter_post__in'] ) > 0 ) {
				$query['pr_filter_post__in'] = $post_relationship_query['pr_filter_post__in'];
			}
		}
		return $query;
	}
	
	/**
	* archive_filter_post_relationship
	*
	* Apply the post relationship filter to WPAs.
	*
	* @since 2.1
	*/
	
	static function archive_filter_post_relationship( $query, $archive_settings, $archive_id ) {
		if ( isset( $archive_settings['post_relationship_mode'][0] ) ) {
			$post_relationship_query = WPV_Post_Relationship_Frontend_Filter::get_settings( $query, $archive_settings, $archive_id );
			if ( count( $post_relationship_query['post__in'] ) > 0 ) {
				$post__in = $query->get( 'post__in' );
				$post__in = isset( $post__in ) ? $post__in : array();
				if ( count( $post__in ) > 0 ) {
					$post__in = array_intersect( (array) $post__in, $post_relationship_query['post__in'] );
					$post__in = array_values( $post__in );
					if ( empty( $post__in ) ) {
						$post__in = array( '0' );
					}
					$query->set( 'post__in', $post__in );
				} else {
					$query->set( 'post__in', $post_relationship_query['post__in'] );
				}
			}
			if ( count( $post_relationship_query['pr_filter_post__in'] ) > 0 ) {
				$query->set( 'pr_filter_post__in', $post_relationship_query['pr_filter_post__in'] );
			}
		}
	}
	
	/**
	* get_settings
	*
	* Get settings for the query filter by post relationship.
	*
	* @since 2.1
	*/
	
	static function get_settings( $query, $view_settings, $view_id ) {
		global $wpdb;
		$post_relationship_query = array(
			'post__in'				=> array(),
			'pr_filter_post__in'	=> array()
			
		);
		$post_owner_id = 0; // the parent ID when it is just one
		$post_owner_data = array(); // we will store the data (parent ID and post_type) here to perform the auxiliar wp_query
		
		$returned_post_types = WPV_Post_Relationship_Frontend_Filter::get_returned_post_types( $view_settings );
		
		switch ( $view_settings['post_relationship_mode'][0] ) {
			case 'current_page': // @deprecated in 1.12.1
			case 'top_current_post':
				$current_page = apply_filters( 'wpv_filter_wpv_get_top_current_post', null );
				if ( is_archive() ) {
					// For archive pages, the "current page" as "post where this View is inserted" is this
					// @todo check if this is also needed for flters by post author, post parent or post taxonomy
					$current_page = apply_filters( 'wpv_filter_wpv_get_current_post', null );
				}
				if ( $current_page ) {
					$post_owner_id = $current_page->ID;
				}
				if ( $post_owner_id > 0 ) {
					$post_type = $wpdb->get_var( 
						$wpdb->prepare( 
							"SELECT post_type FROM {$wpdb->posts} 
							WHERE ID = %d 
							LIMIT 1", 
							$post_owner_id 
						) 
					);
					$post_owner_data[$post_type][] = $post_owner_id;
				}
				break;
			case 'parent_view': // @deprecated in 1.12.1
			case 'current_post_or_parent_post_view':
				$current_page = apply_filters( 'wpv_filter_wpv_get_current_post', null );
				if ( $current_page ) {
					$post_owner_id = $current_page->ID;
				}
				if ( $post_owner_id > 0 ) {
					$post_type = $wpdb->get_var( 
						$wpdb->prepare( 
							"SELECT post_type FROM {$wpdb->posts} 
							WHERE ID = %d 
							LIMIT 1", 
							$post_owner_id 
						) 
					);
					$post_owner_data[$post_type][] = $post_owner_id;
				}
				break;
			case 'this_page':
				if (
					isset( $view_settings['post_relationship_id'] ) 
					&& intval( $view_settings['post_relationship_id'] ) > 0
				) {
					$post_owner_id = intval( $view_settings['post_relationship_id'] );
					$post_owner_id_type = $wpdb->get_var( 
						$wpdb->prepare( 
							"SELECT post_type FROM {$wpdb->posts} 
							WHERE ID = %d 
							LIMIT 1", 
							$post_owner_id 
						) 
					);
					// Adjust for WPML support
					$post_owner_id = apply_filters( 'translate_object_id', $post_owner_id, $post_owner_id_type, true, null );
					$post_owner_data[$post_owner_id_type][] = $post_owner_id;
				}
				break;
			case 'shortcode_attribute':
				if (
					isset( $view_settings['post_relationship_shortcode_attribute'] ) 
					&& '' != $view_settings['post_relationship_shortcode_attribute']
				) {
					$post_relationship_shortcode = $view_settings['post_relationship_shortcode_attribute'];
					$view_attrs = apply_filters( 'wpv_filter_wpv_get_view_shortcodes_attributes', false );
					if ( 
						isset( $view_attrs[$post_relationship_shortcode] ) 
						&& intval( $view_attrs[$post_relationship_shortcode] ) > 0
					) {
						$post_owner_id = intval( $view_attrs[$post_relationship_shortcode] );
						$post_owner_id_type = $wpdb->get_var( 
							$wpdb->prepare( 
								"SELECT post_type FROM {$wpdb->posts} 
								WHERE ID = %d 
								LIMIT 1", 
								$post_owner_id 
							) 
						);
						// Adjust for WPML support
						$post_owner_id = apply_filters( 'translate_object_id', $post_owner_id, $post_owner_id_type, true, null );
						$post_owner_data[$post_owner_id_type][] = $post_owner_id;
					}
				}
				break;
			case 'url_parameter':
				if (
					isset( $view_settings['post_relationship_url_parameter'] ) 
					&& '' != $view_settings['post_relationship_url_parameter']
				) {
					$post_relationship_url_parameter = $view_settings['post_relationship_url_parameter'];
					if ( isset( $_GET[$post_relationship_url_parameter] ) 
						&& $_GET[$post_relationship_url_parameter] != array( 0 ) 
						&& $_GET[$post_relationship_url_parameter] != 0 
					) {
						$post_owner_ids_from_url = $_GET[$post_relationship_url_parameter];
						$post_owner_ids_sanitized = array();
						if ( is_array( $post_owner_ids_from_url ) ) {
							foreach ( $post_owner_ids_from_url as $id_value ) {
								$id_value = (int) esc_attr( trim( $id_value ) );
								if ( $id_value > 0 ) {
									$post_owner_ids_sanitized[] = $id_value;
								}
							}
						} else {
							$post_owner_ids_from_url = (int) esc_attr( $post_owner_ids_from_url );
							if ( $post_owner_ids_from_url > 0 ) {
								$post_owner_ids_sanitized[] = $post_owner_ids_from_url;
							}
						}
						if ( count( $post_owner_ids_sanitized ) ) {
							// We do not need to prepare this query as $post_owner_ids_sanitized only contains numeric natural IDs
							$post_types_from_url = $wpdb->get_results( 
								"SELECT ID, post_type FROM {$wpdb->posts} 
								WHERE ID IN ('" . implode("','", $post_owner_ids_sanitized) . "')" 
							);
							foreach ( $post_types_from_url as $ptfu_key => $ptfu_values ) {
								$post_owner_id_item = $ptfu_values->ID;
								// Adjust for WPML support
								$post_owner_id_item = apply_filters( 'translate_object_id', $post_owner_id_item, $ptfu_values->post_type, true, null );
								$post_owner_data[$ptfu_values->post_type][] = $post_owner_id_item;
							}
						}
					} else if ( function_exists( 'wpcf_pr_get_belongs' ) ) {
						/*
						1. get the returned post type parents
						2. get the tree applied here, will be stored in $view_settings['post_relationship_url_tree']
						3. reverse the tree so the real parent is the first one now; this parent has no value since the url param is not set
						4. get up in the tree until you find the first one element with a value
							4.1 if we get to the latest ancestor and even it does not hold any value, there is no value at all so filter by nothing: $post_owner_data = array() empty
							4.2 if we get to an ancestor with value, filter the last ancestor by the value of this one
							4.3 go down the tree following this filter until the real parent and populate the $post_owner_data[real-parent-slug]
						*/
						
						/*
						1
						*/
						$returned_post_type_parents = array();
						if ( empty( $returned_post_types ) ) {
							$returned_post_types = array( 'any' );
						}
						foreach ( $returned_post_types as $returned_post_type_slug ) {
							$parent_parents_array = wpcf_pr_get_belongs( $returned_post_type_slug );
							if ( $parent_parents_array != false && is_array( $parent_parents_array ) ) {
								$returned_post_type_parents = array_merge( $returned_post_type_parents, array_values( array_keys( $parent_parents_array ) ) );
							}
						}
						
						/*
						2 + 3
						*/
						if ( isset( $view_settings['post_relationship_url_tree'] ) ) {
							$relationship_tree = $view_settings['post_relationship_url_tree'];
						} else {
							$relationship_tree = '';
						}
						$relationship_tree_array = array_reverse( explode( '>', $relationship_tree ) );
						
						$tree_root = end( $relationship_tree_array );
						$tree_ground = reset( $relationship_tree_array );
						
						if ( $tree_root && $tree_ground 
							&& isset( $_GET[$post_relationship_url_parameter . '-' . $tree_root] ) 
							&& !empty( $_GET[$post_relationship_url_parameter . '-' . $tree_root] ) 
							&& $_GET[$post_relationship_url_parameter . '-' . $tree_root] != array( 0 ) 
							&& $_GET[$post_relationship_url_parameter . '-' . $tree_root] != 0  
						) {
							// There are influencer values: let's get the last one
							$ancestor_influence = array();
							$starting_key = 0;
							array_shift( $relationship_tree_array ); // take out the first element as it is a real parent and has no value
							foreach ( $relationship_tree_array as $tree_key => $tree_ancestor ) {
								if ( $tree_ancestor != $tree_ground ) { // just check ancestors that are not the direct parent, as is has no value
							//	if ( !in_array( $tree_ancestor, $returned_post_type_parents ) ) { // just check ancestors that are not direct parents, as they have no value
									if ( isset( $_GET[$post_relationship_url_parameter . '-' . $tree_ancestor] ) && !empty( $_GET[$post_relationship_url_parameter . '-' . $tree_ancestor] ) && $_GET[$post_relationship_url_parameter . '-' . $tree_ancestor] != array( 0 ) ) {
										// This ancestor has a value. Yay!
										$post_owner_ids_from_url = $_GET[$post_relationship_url_parameter . '-' . $tree_ancestor];
										$post_owner_ids_sanitized = array();
										if ( is_array( $post_owner_ids_from_url ) ) {
											foreach ( $post_owner_ids_from_url as $id_key => $id_value ) {
												$id_value = (int) esc_attr( trim( $id_value ) );
												if ( $id_value > 0 ) {
													$post_owner_ids_sanitized[$id_key] = $id_value;
												}
											}
										} else {
											$post_owner_ids_from_url = (int) esc_attr( $post_owner_ids_from_url );
											if ( $post_owner_ids_from_url > 0 ) {
												$post_owner_ids_sanitized[] = $post_owner_ids_from_url;
											}
										}
										$ancestor_influence[$tree_ancestor] = array(
											'key' => $tree_key,
											'ids' => $post_owner_ids_sanitized
										);
										$starting_key = $tree_key;
										break;
									}
								}
							}
							if ( !empty( $ancestor_influence ) ) { // Should not be empty, but better check anyway
								$ancestor_influence = array_slice( $ancestor_influence, 0, 1 ); // It shuold have just one value, but check it anyway
								$i = 0;
								$no_results = false;
								while ( $i < $tree_key ) {
									$this_key = $tree_key - $i;
									if ( $this_key > 0 ) {
										$current_post_type = $relationship_tree_array[$this_key-1];
									} else {
										$current_post_type = $tree_ground;
									}
									$current_influencer = end( $ancestor_influence );
									$query_here = array();
									$query_here['posts_per_page'] = -1;
									$query_here['paged'] = 1;
									$query_here['offset'] = 0;
									$query_here['fields'] = 'ids';
									$query_here['cache_results'] = false;
									$query_here['update_post_meta_cache'] = false;
									$query_here['update_post_term_cache'] = false;
									$query_here['post_type'] = $current_post_type;
									$query_here['meta_query'][] = array(
										'key' => '_wpcf_belongs_' . $relationship_tree_array[$this_key] . '_id',
										'value' => $current_influencer['ids']
									);
									$aux_relationship_query = new WP_Query( $query_here );
									if ( is_array( $aux_relationship_query->posts ) && count( $aux_relationship_query->posts ) ) {
										$ancestor_influence[$current_post_type] = array(
											'key' => $this_key-1,
											'ids' => $aux_relationship_query->posts
										);
										$i++;
									} else {
										$no_results = true;
										break;
									}
									$i++;
								}
								if ( $no_results ) {
									// Along the intermediate filters, no posts were returned
									$post_relationship_query['post__in'] = array( '0' );
								} else {
									$real_parent_filter = end( $ancestor_influence );
									$query_here = array();
									$query_here['posts_per_page'] = -1;
									$query_here['paged'] = 1;
									$query_here['offset'] = 0;
									$query_here['fields'] = 'ids';
									$query_here['cache_results'] = false;
									$query_here['update_post_meta_cache'] = false;
									$query_here['update_post_term_cache'] = false;
									$query_here['post_type'] = $tree_ground;
									$query_here['meta_query'][] = array(
										'key' => '_wpcf_belongs_' . $relationship_tree_array[0] . '_id',
										'value' => $real_parent_filter['ids']
									);
									$aux_relationship_query = new WP_Query( $query_here );
									if ( is_array( $aux_relationship_query->posts ) && count( $aux_relationship_query->posts ) ) {
										$post_owner_data[$tree_ground] = $aux_relationship_query->posts;
									} else {
										// Just on the late filter, no posts were returned
										$post_relationship_query['post__in'] = array( '0' );
									}
								}
							}
						} else {
							// There are no values set, so filter by nothing
							// $post_owner_data = array() already;
						}
					}
				}
				break;
			case 'framework':
				global $WP_Views_fapi;
				if ( $WP_Views_fapi->framework_valid ) {
					if (
						isset( $view_settings['post_relationship_framework'] ) 
						&& '' != $view_settings['post_relationship_framework']
					) {
						$post_relationship_framework = $view_settings['post_relationship_framework'];
						$post_relationship_candidates = $WP_Views_fapi->get_framework_value( $post_relationship_framework, array() );
						if ( ! is_array( $post_relationship_candidates ) ) {
							$post_relationship_candidates = explode( ',', $post_relationship_candidates );
						}
						$post_relationship_candidates = array_map( 'esc_attr', $post_relationship_candidates );
						$post_relationship_candidates = array_map( 'trim', $post_relationship_candidates );
						// is_numeric does sanitization
						$post_relationship_candidates = array_filter( $post_relationship_candidates, 'is_numeric' );
						$post_relationship_candidates = array_map( 'intval', $post_relationship_candidates );
						if ( count( $post_relationship_candidates ) ) {
							// We do not need to prepare this query as $post_relationship_candidates only contains numeric natural IDs
							$post_types_from_framework = $wpdb->get_results( 
								"SELECT ID, post_type FROM {$wpdb->posts} 
								WHERE ID IN ('" . implode("','", $post_relationship_candidates) . "')" 
							);
							foreach ( $post_types_from_framework as $ptfu_key => $ptfu_values ) {
								$post_owner_id_item = $ptfu_values->ID;
								// Adjust for WPML support
								$post_owner_id_item = apply_filters( 'translate_object_id', $post_owner_id_item, $ptfu_values->post_type, true, null );
								$post_owner_data[$ptfu_values->post_type][] = $post_owner_id_item;
							}
						}
					}
				}
				break;
		}
		if ( ! empty( $post_owner_data ) ) {
			$query_here = array();
			$query_here['posts_per_page'] = -1;
			$query_here['paged'] = 1;
			$query_here['offset'] = 0;
			$query_here['post_type'] = 'any';
			$query_here['fields'] = 'ids';
			$query_here['cache_results'] = false;
			$query_here['update_post_meta_cache'] = false;
			$query_here['update_post_term_cache'] = false;
			$query_here['post_type'] = $returned_post_types;
			$query_here['meta_query']['relation'] = 'AND';
			// Set the post status, although I am not sure tis is needed as we do have the post status filter anyway
			// @todo use this for setting the 'post_type' query argument, depending on the current $query and $view_settings
			$query_here = apply_filters( 'wpv_filter_wpv_filter_auxiliar_post_relationship_query', $query_here, $view_settings, $view_id );
			foreach ( $post_owner_data as $type => $ides ) {
				$query_here['meta_query'][] = array(
					'key' => '_wpcf_belongs_' . $type . '_id',
					'value' => $ides,
				);
			}
			$query_here['suppress_wpml_where_and_join_filter'] = true;

			$aux_relationship_query = new WP_Query( $query_here );
			
			if ( is_array( $aux_relationship_query->posts ) ) {
				if ( count( $aux_relationship_query->posts ) > 0 ) {
					if ( count( $post_relationship_query['post__in'] ) > 0 ) {
						$post_relationship_query['post__in'] = array_intersect( (array) $post_relationship_query['post__in'], $aux_relationship_query->posts );
						$post_relationship_query['post__in'] = array_values( $post_relationship_query['post__in'] );
						if ( empty( $post_relationship_query['post__in'] ) ) {
							$post_relationship_query['post__in'] = array( '0' );
						}
					} else {
						$post_relationship_query['post__in'] = $aux_relationship_query->posts;
					}
					$post_relationship_query['pr_filter_post__in'] = $aux_relationship_query->posts;
				} else {
					$post_relationship_query['post__in'] = array( '0' );
				}
			}
		}
		return $post_relationship_query;
	}
	
	static function requires_current_page( $state, $view_settings ) {
		if ( $state ) {
			return $state; // Already set
		}
		if ( isset( $view_settings['post_relationship_mode'][0] ) ) {   
			if ( in_array( $view_settings['post_relationship_mode'][0], array( 'current_page', 'top_current_post' ) ) ) {
				$state = true;
			}
		}
		return $state;
	}
	
	static function requires_parent_post( $state, $view_settings ) {
		if ( $state ) {
			return $state; // Already set
		}
		if ( isset( $view_settings['post_relationship_mode'][0] ) ) {   
			if ( in_array( $view_settings['post_relationship_mode'][0], array( 'parent_view', 'current_post_or_parent_post_view' ) ) ) {
				$state = true;
			}
		}
		return $state;
	}
	
	/**
	* requires_framework_values
	*
	* Check if the current filter by post relationship needs info about the framework values
	*
	* @since 1.10
	* @since 2.1	Renamed from wpv_filter_post_relationship_requires_framework_values and moved to a proper static method
	*/
	
	static function requires_framework_values( $state, $view_settings ) {
		if ( $state ) {
			return $state;
		}
		if ( isset( $view_settings['post_relationship_mode'][0] ) ) {
			if ( $view_settings['post_relationship_mode'][0] == 'framework' ) {
				$state = true;
			}
		}
		return $state;
	}
	
	/**
	* shortcode_attributes
	*
	* Register the filter by post relationship on the method to get View shortcode attributes
	*
	* @since 1.10
	* @since 2.1	Renamed from wpv_filter_register_post_relationship_shortcode_attributes and moved to a proper static method
	*/
	
	static function shortcode_attributes( $attributes, $view_settings ) {
		if (
			isset( $view_settings['post_relationship_mode'] ) 
			&& isset( $view_settings['post_relationship_mode'][0] ) 
			&& $view_settings['post_relationship_mode'][0] == 'shortcode_attribute' 
		) {
			$attributes[] = array(
				'query_type'	=> $view_settings['query_type'][0],
				'filter_type'	=> 'post_relationship',
				'filter_label'	=> __( 'Post relationship', 'wpv-views' ),
				'value'			=> 'ancestor_id',
				'attribute'		=> $view_settings['post_relationship_shortcode_attribute'],
				'expected'		=> 'number',
				'placeholder'	=> '103',
				'description'	=> __( 'Please type a post ID to get its children', 'wpv-views' )
			);
		}
		return $attributes;
	}
	
	/**
	 * Register the filter by post relationship on the method to get View URL parameters.
	 *
	 * @since 1.11.0
	 * @since 2.1.0	Renamed from wpv_filter_register_post_relationship_url_parameters and moved to a static method.
	 * @since 2.3.0 Ensured that each ancestor gets a proper 'filter_type' key, since we then 
	 *     wp_list_pluck by that key and having repeated values produced some unexpected issues. Also, 
	 *     make sure that we do not get duplicates, since first-level parents shoudl be covered by the 
	 *     default $view_settings['post_relationship_url_parameter'] attribute.
	 */
	
	static function url_parameters( $attributes, $view_settings ) {
		if (
			isset( $view_settings['post_relationship_mode'] ) 
			&& isset( $view_settings['post_relationship_mode'][0] ) 
			&& $view_settings['post_relationship_mode'][0] == 'url_parameter' 
		) {
			$attributes[] = array(
				'query_type'	=> $view_settings['query_type'][0],
				'filter_type'	=> 'post_relationship',
				'filter_label'	=> __( 'Post relationship', 'wpv-views' ),
				'value'			=> 'ancestor_id',
				'attribute'		=> $view_settings['post_relationship_url_parameter'],
				'expected'		=> 'number',
				'placeholder'	=> '103',
				'description'	=> __( 'Please type a post ID to get its children', 'wpv-views' )
			);
			
			$returned_post_types = WPV_Post_Relationship_Frontend_Filter::get_returned_post_types( $view_settings );
			
			$ancestor_post_types = array();
			if ( 
				! empty( $returned_post_types ) 
				&& function_exists( 'wpcf_pr_get_belongs' )
			) {
				$returned_post_types_parents = array();
				foreach ( $returned_post_types as $ground_post_type ) {
					$ground_post_type_parents = wpcf_pr_get_belongs( $ground_post_type );
					if ( 
						$ground_post_type_parents != false 
						&& is_array( $ground_post_type_parents ) 
					) {
						$ground_post_type_parents = array_values( array_keys( $ground_post_type_parents ) );
						$returned_post_types_parents = array_merge( $returned_post_types_parents, $ground_post_type_parents );
					}
				}
				$returned_post_types_parents = array_unique( $returned_post_types_parents );
				$returned_post_types_parents = array_values( $returned_post_types_parents );
				if ( ! empty( $returned_post_types_parents ) ) {
					$ancestor_post_types = wpv_get_post_type_ancestors( $returned_post_types_parents );
				}
			}
			foreach ( $ancestor_post_types as $ancestor_slug ) {
				$attributes[] = array(
					'query_type'	=> $view_settings['query_type'][0],
					'filter_type'	=> 'post_relationship_' . $ancestor_slug,
					'filter_label'	=> __( 'Post relationship', 'wpv-views' ),
					'value'			=> 'ancestor_id',
					'attribute'		=> $view_settings['post_relationship_url_parameter'] . '-' . $ancestor_slug,
					'expected'		=> 'number',
					'placeholder'	=> '103',
					'description'	=> __( 'Please type a post ID to get its children', 'wpv-views' )
				);
			}
		}
		return $attributes;
	}
	
	/**
	* get_returned_post_types
	*
	* Get the post types displayed by the current View or WordPress Archive.
	*
	* @param $view_settings
	*
	* @return array
	*
	* @since 2.1
	*/
	
	static function get_returned_post_types( $view_settings ) {
		$returned_post_types = array();
		if ( 
			isset( $view_settings['view-query-mode'] ) 
			&& $view_settings['view-query-mode'] == 'normal'
		) {
			$returned_post_types = $view_settings['post_type'];
		} else {
			// we assume 'archive' or 'layouts-loop'
			global $wp_query;
			$returned_post_types = $wp_query->get( 'post_type' );
			if ( ! is_array( $returned_post_types ) ) {
				$returned_post_types = array( $returned_post_types );
			}
		}
		return $returned_post_types;
	}
	
	static function wpv_before_display_post_post_relationship( $post, $view_id ) {
		static $related = array();
		global $WP_Views;
		if ( function_exists( 'wpcf_pr_get_belongs' ) ) {
			if ( ! isset( $related[$post->post_type] ) ) {
				$related[$post->post_type] = wpcf_pr_get_belongs( $post->post_type );
			}
			if ( is_array( $related[$post->post_type] ) ) {
				foreach( $related[$post->post_type] as $post_type => $data ) {
					$related_id = wpcf_pr_post_get_belongs( $post->ID, $post_type );
					if ( $related_id ) {
						$WP_Views->set_variable( $post_type . '_id', $related_id );
					}
				}
			}
		}
		
	}
	
	/**
	 * Auxiliar method to calculate some dependency and counters data, and store cache if needed, for the frontend filter by postmeta fields.
	 *
	 * @param attributes array(
	 *		'field'		string	The field slug to use
	 *		'type'		string	The filter type to render
	 *		'url_param'	string	The URL parameter to listen to
	 *		'view_settings'	array	The invlved View settings
	 *		'fomat'		string	The format of the frontend filter
	 * )
	 *
	 * @return array(
	 *		'use_query_cache'	string	Whether we need to use the cache, meaning whether there is dependency or counters. 'enabled'|'disabled'.
	 *		'dependency'		string	Whether there is dependency. 'enabled'|'disabled'
	 *		'counters'			string	Whether there are counters. 'enabled'|'disabled'
	 *		'empty_action'		string	Action to execute on items without matching results. 'hide'|'disable'
	 *		'relationship_cache'	array	Cache structure for post relationship filters
	 *		'auxiliar_query_count'	number|bool	Count of the auxiliar query used for counters on empty values
	 * )
	 *
	 * @since 2.4.0
	 */
	
	static function setup_frontend_dependency_and_counters_data( $attributes ) {
		$attributes = wp_parse_args(
			$attributes, 
			array(
				'type'		=> '',
				'default_label'	=> '',
				'url_param'	=> '',
				'view_settings'	=> array(),
				'format'	=> '',
				'parent_post_type'	=> '',
				'ancestor_tree'	=> ''
			)
		);
		
		$data = array(
			'use_query_cache'	=> 'disabled',
			'dependency'	=> 'disabled',
			'counters'		=> 'disabled',
			'empty_action'	=> 'hide',
			'relationship_cache'	=> array(),
			'auxiliar_query_count'	=> false
		);
		
		$view_settings = $attributes['view_settings'];
		
		if ( 
			isset( $view_settings['dps'] ) 
			&& is_array( $view_settings['dps'] ) 
			&& isset( $view_settings['dps']['enable_dependency'] ) 
			&& $view_settings['dps']['enable_dependency'] == 'enable' 
		) {
			$data['dependency'] = 'enabled';
			$force_disable_dependant = apply_filters( 'wpv_filter_wpv_get_force_disable_dps', false );
			if ( $force_disable_dependant ) {
				$data['dependency'] = 'disabled';
			}
		}
		if ( 
			strpos( $attributes['format'], '%%COUNT%%' ) !== false 
			|| strpos( $attributes['default_label'], '%%COUNT%%' ) !== false 
		) {
			$data['counters'] = 'enabled';
		}
		
		$data['use_query_cache'] = ( 'enabled' == $data['dependency'] || 'enabled' == $data['counters'] ) ? 'enabled' : 'disabled';
		
		if ( 'enabled' == $data['use_query_cache'] ) {
			
			// Empty action
			$empty_action_type = $attributes['type'];
			switch ( $empty_action_type ) {
				case 'radio':
					$empty_action_type = 'radios';
					break;
				case 'multi-select':
				case 'multiselect':
					$empty_action_type = 'multi_select';
					break;
			}
			if ( isset( $view_settings['dps'][ 'empty_' . $empty_action_type ] ) ) {
				$data['empty_action'] = $view_settings['dps'][ 'empty_' . $empty_action_type ];
			}
			
			if ( 
				empty( $_GET[ $attributes['url_param'] ] ) 
				|| $_GET[ $attributes['url_param'] ] === 0 
				|| ( 
					is_array( $_GET[ $attributes['url_param'] ] ) 
					&& in_array( (string) 0, $_GET[ $attributes['url_param'] ] ) 
				) 
			) {
				// This is when there is no value selected
				// And will return the natural cache
				WPV_Cache::generate_cache_extended_for_post_relationship();
			} else {
				// When there is a selected value, create a pseudo-cache based on all the other filters
				// Note that as this is a hierarquical filter, disabling the current means leaving the ancestors ones
				$current_filter = $_GET[ $attributes['url_param'] ];
				unset( $_GET[ $attributes['url_param'] ] );
				$query = apply_filters( 'wpv_filter_wpv_get_dependant_extended_query_args', array(), $view_settings, array( 'relationship' => 'pr_filter_post__in' ) );//wpv_get_dependant_view_query_args();
				$aux_cache_query = null;
				if ( 
					isset( $query['post__in'] ) 
					&& is_array( $query['post__in'] ) 
					&& isset( $query['pr_filter_post__in'] ) 
					&& is_array( $query['pr_filter_post__in'] ) 
				) {
					$diff = array_diff( $query['post__in'], $query['pr_filter_post__in'] );
					if ( empty( $diff ) ) {// TODO maybe we can skip the query here
						unset( $query['post__in'] );
					} else {
						$query['post__in'] = $diff;
					}
				}
				$aux_cache_query = new WP_Query( $query );
				if ( 
					is_array( $aux_cache_query->posts ) 
					&& ! empty( $aux_cache_query->posts ) 
				) {
					$f_fields = array( '_wpcf_belongs_' . $attributes['parent_post_type'] . '_id' );
					WPV_Cache::generate_cache_extended_for_post_relationship( $aux_cache_query->posts, array( 'cf' => $f_fields ) );
					$data['auxiliar_query_count'] = count( $aux_cache_query->posts );
				} else {
					// Just in case, this will return the natural cache
					WPV_Cache::generate_cache_extended_for_post_relationship();
				}
				$_GET[ $attributes['url_param'] ] = $current_filter;
			}
			// Now, generate the WPV_Cache::$stored_relationship_cache from the current ancestor data
			// Notice that this just extends the native cache with the one generated by the current relationship data
			// so it clears previous iteration automatically, as it is non-permanent data
			$calculate_counters = ( 'enabled' == $data['counters'] );
			WPV_Cache::generate_post_relationship_tree_cache( $attributes['ancestor_tree'], $calculate_counters );
			$data['relationship_cache'] = WPV_Cache::$stored_relationship_cache;
		}
		
		return $data;
		
	}
	
	/**
	 * Callback to display the custom search filter by post relationship.
	 *
	 * @param $atts array
	 *
	 * @uses wpv_shortcode_wpv_control_set
	 *
	 * @since 2.4.0
	 */
	
	public static function wpv_shortcode_wpv_control_post_relationship( $atts, $content ) {
		return wpv_shortcode_wpv_control_set( $atts, $content );
	}
	
	/**
	 * Callback to display each ancestor instance for the custom search filter by post relationship.
	 *
	 * @param $atts array
	 *		'ancestor_type'	string	The slug of the ancestor post type to use
	 * 		'url_param'		string	URL parameter to listen to
	 *		'type'			'select'|'multi-select'|'radios'|'checbboxes'
	 *		'format'		string.	Placeholders: '%%NAME%%', '%%COUNT%%'
	 *		'default_label'	string	Label for the default empty option in select dropdowns
	 '		'orderby'		string	Order of the options
	 '		'order'			string	Direction of the options
	 *		'style'			string	Styles to add to the control
	 *		'class'			string	Classnames to add to the control
	 *		'label_style'	string
	 *		'label_class'	string
	 *		'output'		string	The kind of output to produce: 'legacy'|'bootstrap'. Defaults to 'bootstrap'.
	 * The following attributes are not passed to the shortcode from the content, but from the wrapper wpv-control-post-relationship shortcode
	 *		'ancestor_tree'	string	>-separated list with the currently displaying post relationship ancestors tree
	 *		'returned_pt'	string	Comma-separated list of post types returned by the current query
	 *		'returned_pt_parents'	string	Comma-separated list of Types parents for the post types returned by the current query
	 *
	 * @since 2.4.0
	 */
	
	public static function wpv_shortcode_wpv_control_post_ancestor( $atts ) {
		
		$atts = shortcode_atts( 
			array(
				'type'					=> '', // select, multi-select, checkboxes, radio/radios
				'url_param'				=> '', // URL parameter to be used
				'ancestor_type'			=> '',
				'ancestor_tree'			=> '',
				'default_label'			=> '',
				'returned_pt'			=> '',
				'returned_pt_parents'	=> '',
				'format'				=> '%%NAME%%',
				'orderby'				=> 'title', // can be any key of $allowed_orderby_values
				'order'					=> 'ASC', // ASC or DESC
                'style'					=> '', // inline styles for input
                'class'					=> '', // input classes
                'label_style'			=> '', // inline styles for input label
                'label_class'			=> '', // classes for input label
				'output'				=> 'bootstrap'
			),
			$atts
		);
		
		// Legacy output
		if ( 'legacy' == $atts['output'] ) {
			return wpv_shortcode_wpv_control_item( $atts );
		}
		
		if ( empty( $atts['type'] ) ) {
			return;
		}
		
		$ancestor_tree_array = explode( '>', $atts['ancestor_tree'] ); // NOTE this makes it useful for just one-branch scenarios, might extend this
		if ( ! in_array( $atts['ancestor_type'], $ancestor_tree_array ) ) {
			return __( 'The ancestor_type argument refers to a post type that is not included in the ancestors tree.', 'wpv-views' );
		}
		$returned_post_types = explode( ',', $atts['returned_pt'] );
		$returned_post_type_parents = explode( ',', $atts['returned_pt_parents'] );
			
		$this_tree_ground = end( $ancestor_tree_array );
		$this_tree_roof = reset( $ancestor_tree_array );
		if ( ! in_array( $this_tree_ground, $returned_post_type_parents ) ) {
			return __( 'The ancestors argument does not end with a valid parent for the returned post types on this View.', 'wpv-views' );
		}
		
		// Allowed values and their translation into names of wp_posts columns.
		$allowed_orderby_values = array(
			'id'			=> 'ID',
			'title'			=> 'post_title',
			'date'			=> 'post_date',
			'date_modified'	=> 'post_modified',
			'comment_count'	=> 'comment_count' 
		);
		if ( ! isset( $allowed_orderby_values[ $atts['orderby'] ] ) ) {
			$atts['orderby'] = 'title';
		}
		// Now $orderby contains a valid column name at all times.
		$orderby = $allowed_orderby_values[ $atts['orderby'] ];
		// Default to ASC on invalid $order value.
		$order = ( 'DESC' == strtoupper( $atts['order'] ) ) ? 'DESC' : 'ASC';
		
		// Extra classnames depending on the possition in the tree
		$extra_class_values = array();
		if ( $this_tree_ground == $atts['ancestor_type'] ) {
			$extra_class_values[] = 'js-wpv-filter-trigger';
		} else {
			$extra_class_values[] = 'js-wpv-post-relationship-update';
		}
		
		// Query to get options for the filter
		global $wpdb;
		if ( $this_tree_roof == $atts['ancestor_type'] ) {
			$values_to_prepare = array();
			// Adjust query for WPML support
			global $sitepress;
			$wpml_join = $wpml_where = "";
			if (
				isset( $sitepress ) 
				&& function_exists( 'icl_object_id' )
			) {
				$current_pt_translatable = $sitepress->is_translated_post_type( $atts['ancestor_type'] );
				if ( $current_pt_translatable ) {
					$wpml_current_language = $sitepress->get_current_language();
					$wpml_join = " JOIN {$wpdb->prefix}icl_translations t ";
					$wpml_where = " AND p.ID = t.element_id AND t.language_code = %s ";
					$values_to_prepare[] = $wpml_current_language;
				}
			}
			$values_to_prepare[] = $atts['ancestor_type'];
			$pa_results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT p.ID, p.post_title
					FROM {$wpdb->posts} p {$wpml_join}
					WHERE p.post_status = 'publish' 
					{$wpml_where} 
					AND p.post_type = %s 
					ORDER BY p.{$orderby} {$order}",
					$values_to_prepare
				)
			);
		} else {
			$aux_position_array = array_keys( $ancestor_tree_array, $atts['ancestor_type'] );
			if ( count( $aux_position_array ) > 1 ) {
				return __( 'There seems to be some kind of infinite loop happening.', 'wpv-views' );
			}
			$this_type_parents = array_slice( $ancestor_tree_array, 0, $aux_position_array[0] );
			foreach ( $this_type_parents as $ttp_item )  {
				$extra_class_values[] = 'js-wpv-' . $ttp_item . '-watch';
			}
			
			$this_parent_parents = array();
			$this_parent_parents = wpcf_pr_get_belongs( $atts['ancestor_type'] );
			if ( 
				$this_parent_parents != false 
				&& is_array( $this_parent_parents ) 
			) {
				$this_parent_parents_array = array_merge( array_values( array_keys( $this_parent_parents ) ) );
			}
			
			$real_influencer_array = array_intersect( $this_parent_parents_array, $this_type_parents );
			$query_here = array();
			$query_here['posts_per_page'] = -1;
			$query_here['paged'] = 1;
			$query_here['offset'] = 0;
			$query_here['fields'] = 'ids';
			$query_here['post_type'] = $atts['ancestor_type'];
			foreach ( $real_influencer_array as $real_influencer ) {
				if ( 
					isset( $_GET[ $atts['url_param'] . '-' . $real_influencer ] ) 
					&& ! empty( $_GET[ $atts['url_param'] . '-' . $real_influencer ] ) 
					&& $_GET[ $atts['url_param'] . '-' . $real_influencer ] != array( 0 ) 
				) {
					$query_here['meta_query'][] = array(
						'key' => '_wpcf_belongs_' . $real_influencer . '_id',
						'value' => $_GET[ $atts['url_param'] . '-' . $real_influencer ]
					);
				}
			}
			if ( isset( $query_here['meta_query'] ) ) {
				$query_here['meta_query']['relation'] = 'AND';
				$aux_relationship_query = new WP_Query( $query_here );
				if ( 
					is_array( $aux_relationship_query->posts ) 
					&& count( $aux_relationship_query->posts ) 
				) {
					// If there are posts with those requirements, get their ID and post_title
					// We do not really need sanitization here, as $aux_relationship_query->posts only contains IDs come from the database, but still
					$values_to_prepare = array();
					$aux_rel_count = count( $aux_relationship_query->posts );
					$aux_rel_placeholders = array_fill( 0, $aux_rel_count, '%d' );
					foreach ( $aux_relationship_query->posts as $aux_rel_id ) {
						$values_to_prepare[] = $aux_rel_id;
					}
					$pa_results = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT ID, post_title
							FROM {$wpdb->posts}
							WHERE post_status = 'publish' AND ID IN (" . implode( ",", $aux_rel_placeholders ) . ")
							ORDER BY {$orderby} {$order}",
							$values_to_prepare
						)
					);
				} else {
					//If there are no posts with those requeriments, render no posts
					$pa_results = array();
				}
			} else {
				$pa_results = array();
			}
		}
		
		// Create the right url_param that will be used by this control
		if ( $atts['ancestor_type'] == $this_tree_ground ) {
			$current_url_param = $atts['url_param'];
		} else {
			$current_url_param = $atts['url_param'] . '-' . $atts['ancestor_type'];
		}
		
		if ( ! empty( $atts['default_label'] ) ) {
			$aux_array = apply_filters( 'wpv_filter_wpv_get_rendered_views_ids', array() );
			$view_name = get_post_field( 'post_name', end( $aux_array ) );
			$atts['default_label'] = wpv_translate( $atts['ancestor_type'] . '_default_label', $atts['default_label'], false, 'View ' . $view_name );
		}
		
		$view_settings = apply_filters( 'wpv_filter_wpv_get_object_settings', array() );
		
		$dependency_and_counters_attributes = array(
			'type'		=> $atts['type'],
			'default_label'	=> $atts['default_label'],
			'url_param'	=> $current_url_param,
			'view_settings'	=> $view_settings,
			'format'	=> $atts['format'],
			'parent_post_type'	=> $this_tree_ground,
			'ancestor_tree'	=> $atts['ancestor_tree']
		);
		$dependency_and_counters_data = WPV_Post_Relationship_Frontend_Filter::setup_frontend_dependency_and_counters_data( $dependency_and_counters_attributes );
		
		$walker_args = array(
			'name'				=> $current_url_param,
			'selected'			=> array( 0 ),
			'format'			=> $atts['format'], //%%NAME%%, %%COUNT%%
			'style'				=> $atts['style'],
			'class'				=> $atts['class'],
			'label_style'		=> $atts['label_style'],
			'label_class'		=> $atts['label_class'],
			'extra_class'		=> $extra_class_values,
			'output'			=> $atts['output'],
			'type'				=> $atts['type'], //'select'|'multi-select'|'radio'|'radios'|'checkboxes'
			'ancestor_type'		=> $atts['ancestor_type'],
			'use_query_cache'	=> $dependency_and_counters_data['use_query_cache'], //'disabled'|'enabled'
			'dependency'		=> $dependency_and_counters_data['dependency'], //'disabled'|'enabled'
			'counters'			=> $dependency_and_counters_data['counters'], //'disabled'|'enabled'
			'empty_action'		=> $dependency_and_counters_data['empty_action'], //'hide'|'disable'
			'query_cache'		=> $dependency_and_counters_data['relationship_cache'],
			'auxiliar_query_count'	=> $dependency_and_counters_data['auxiliar_query_count']
		);
		
		if ( isset( $_GET[ $current_url_param ] ) ) {
			if ( is_array( $_GET[ $current_url_param ] ) ) {
				$walker_args['selected'] = $_GET[ $current_url_param ];
			} else {
				$walker_args['selected'] = array( $_GET[ $current_url_param ] );
			}
		}
		
		$return = '';
		
		// Call the walkers per type, care with the default label and the output value... mind the 'js-wpv-' . $ttp_item . '-watch' class above
		
		switch ( $atts['type'] ) {
			case 'select':
			case 'multi-select':
				$select_args = array(
					'id'	=> 'wpv_control_' . $atts['type'] . '_' . $atts['url_param'] . '_' . $atts['ancestor_type'],
					'name'	=> $walker_args['name'],
					'class'	=> ( empty( $walker_args['class'] ) ) ? array() : explode( ' ', $walker_args['class'] )
				);
				
				$minimum_options_count_to_disable = 0;
				if ( ! empty( $atts['default_label'] ) ) {
					$default_option = new stdClass();
					$default_option->ID = 0;
					$default_option->post_title = $atts['default_label'];
					array_unshift( $pa_results, $default_option );
					$minimum_options_count_to_disable = 1;
				}
				
				if ( 
					$atts['ancestor_type'] != $this_tree_roof
					&& count( $pa_results ) == $minimum_options_count_to_disable 
					&& $atts['type'] == 'select' 
				) {
					$select_args['disabled'] = 'disabled';
				}
				
				$post_relationship_walker = new WPV_Walker_Post_Relationship_Select( $walker_args );
				
				// Backwards compatibility: for the actual parent post type, we do nto add the type name to the id attribute
				if ( $atts['ancestor_type'] == $this_tree_ground ) {
					$select_args['id'] = 'wpv_control_' . $atts['type'] . '_' . $atts['url_param'];
				}
				
				$select_args['class'] = array_merge( $select_args['class'], $extra_class_values );
				$select_args['class'] = array_unique( $select_args['class'] );
				
				switch ( $walker_args['output'] ) {
					case 'bootstrap':
						$select_args['class'][] = 'form-control';
						break;
					case 'legacy':
					default:
						$select_args['class'][] = 'wpcf-form-select form-select select';
						break;
				}
				
				if ( ! empty( $walker_args['style'] ) ) {
					$select_args['style'] = $walker_args['style'];
				}
				
				if ( 'multi-select' == $walker_args['type'] ) {
					$select_args['name'] = $walker_args['name'] . '[]';
					$select_args['multiple'] = 'multiple';
				}
				
				$return .= '<select';
				foreach ( $select_args as $att_key => $att_value ) {
					if ( 
						in_array( $att_key, array( 'style', 'class' ) ) 
						&& empty( $att_value )
					) {
						continue;
					}
					$return .= ' ' . $att_key . '="';
					if ( is_array( $att_value ) ) {
						$att_real_value = implode( ' ', $att_value );
						$return .= $att_real_value;
					} else {
						$return .= $att_value;
					}
					$return .= '"';
				}
				$return .=  '>';
				$return .=  call_user_func_array( array( &$post_relationship_walker, 'walk' ), array( $pa_results, 0 ) );
				$return .=  '</select>';
				return $return;
				break;
			case 'radio':
			case 'radios':
				if ( ! empty( $atts['default_label'] ) ) {
					$default_option = new stdClass();
					$default_option->ID = 0;
					$default_option->post_title = $atts['default_label'];
					array_unshift( $pa_results, $default_option );
				}
				$post_relationship_walker = new WPV_Walker_Post_Relationship_Radios( $walker_args );
				$return .=  call_user_func_array( array( &$post_relationship_walker, 'walk' ), array( $pa_results, 0 ) );
				return $return;
				break;
			case 'checkboxes':
				$post_relationship_walker = new WPV_Walker_Post_Relationship_Checkboxes( $walker_args );
				$return .=  call_user_func_array( array( &$post_relationship_walker, 'walk' ), array( $pa_results, 0 ) );
				return $return;
				break;
		}
		
	}
	
	/**
	 * Register the wpv-control-post-relationship shortcode attributes in the shortcodes GUI API.
	 *
	 * @note Some options are different when adding and when editing, mainly for BETWEEN comparisons.
	 *
	 * @since 2.4.0
	 */
	
	public static function wpv_shortcodes_register_wpv_control_post_relationship_data( $views_shortcodes ) {
		$views_shortcodes['wpv-control-post-relationship'] = array(
			'callback' => array( 'WPV_Post_Relationship_Frontend_Filter', 'wpv_shortcodes_get_wpv_control_post_relationship_data' )
		);
		return $views_shortcodes;
	}
	
	public static function wpv_shortcodes_get_wpv_control_post_relationship_data( $parameters = array(), $overrides = array() ) {
		$data = array(
			'name' => __( 'Filter by post relationship', 'wpv-views' ),
			'label' => __( 'Filter by post relationship', 'wpv-views' ),
			'attributes' => array(
				'display-options' => array(
					'label'		=> __( 'Display options', 'wpv-views' ),
					'header'	=> __( 'Display options', 'wpv-views' ),
					'fields' => array(
						'type' => array(
							'label'			=> __( 'Type of control', 'wpv-views'),
							'type'			=> 'select',
							'options'		=> array(
												'select'		=> __( 'Select dropdown', 'wpv-views' ),
												'multi-select'	=> __( 'Select multiple', 'wpv-views' ),
												'radios'		=> __( 'Set of radio buttons', 'wpv-views' ),
												'checkboxes'	=> __( 'Set of checkboxes', 'wpv-views' ),
											),
							'default_force' => 'select'
						),
						'ancestors' => array(
							'label'			=> __( 'Post relationship', 'wpv-views' ),
							'type'			=> 'select',
							'options'		=> array(),
							'description'	=> __( 'Select which ancestors should be part of this filter.', 'wpv-views' ),
							'required'		=> true
						),
						'default_label' => array(
							'label'			=> __( 'Label for the first \'default\' option', 'wpv-views'),
							'type'			=> 'text',
							'default'		=> '',
						),
						'format' => array(
							'label'			=> __( 'Format', 'wpv-views' ),
							'type'			=> 'text',
							'placeholder'	=> '%%NAME%%',
							'description'	=> __( 'You can use %%NAME%% or %%COUNT%% as placeholders.', 'wpv-views' ),
						),
						'relationship_order_combo' => array(
							'label'			=> __( 'Options sorting', 'wpv-views' ),
							'type'			=> 'grouped',
							'fields'		=> array(
								'orderby' => array(
									'pseudolabel'	=> __( 'Order by', 'wpv-views'),
									'type'			=> 'select',
									'default'		=> 'title',
									'options'		=> array(
														'title'	=> __( 'Post title', 'wpv-views' ),
														'id'	=> __( 'Post ID', 'wpv-views' ),
														'date'	=> __( 'Post date', 'wpv-views' ),
														'date_modified'	=> __( 'Post last modified date', 'wpv-views' ),
														'comment_count'	=> __( 'Post comment count', 'wpv-views' ),
													),
									'description'	=> __( 'Order options by this parameter.', 'wpv-views' ),
								),
								'order' => array(
									'pseudolabel'	=> __( 'Order', 'wpv-views' ),
									'type'			=> 'select',
									'default'		=> 'ASC',
									'options'		=> array(
														'ASC'	=> __( 'Ascending', 'wpv-views' ),
														'DESC'	=> __( 'Descending', 'wpv-views' ),
													),
									'description'	=> __( 'Order options in this direction.', 'wpv-views' ),
								)
							)
						),
						'url_param' => array(
							'label'			=> __( 'URL parameter to use', 'wpv-views' ),
							'type'			=> 'text',
							'default_force'	=> 'wpv-relationship-filter',
							'required'		=> true
						),
					),
				),
				'style-options' => array(
					'label'		=> __( 'Style options', 'wpv-views' ),
					'header'	=> __( 'Style options', 'wpv-views' ),
					'fields' => array(
						'output' => array(
							'label'		=> __( 'Output style', 'wpv-views' ),
							'type'		=> 'radio',
							'options'		=> array(
								'legacy'	=> __( 'Raw output', 'wpv-views' ),
								'bootstrap'	=> __( 'Fully styled output', 'wpv-views' ),
							),
							'default'		=> 'bootstrap',
						),
						'input_frontend_combo' => array(
							'label'			=> __( 'Element styling', 'wpv-views' ),
							'type'			=> 'grouped',
							'fields'		=> array(
								'class' => array(
									'pseudolabel'	=> __( 'Element classnames', 'wpv-views' ),
									'type'			=> 'text',
									'description'	=> __( 'Space-separated list of classnames to apply. For example: classone classtwo', 'wpv-views' )
								),
								'style' => array(
									'pseudolabel'	=> __( 'Element inline style', 'wpv-views' ),
									'type'			=> 'text',
									'description'	=> __( 'Raw inline styles to apply. For example: color:red;background:none;', 'wpv-views' )
								),
							),
						),
						'label_frontend_combo' => array(
							'label'			=> __( 'Label styling', 'wpv-views' ),
							'type'			=> 'grouped',
							'fields'		=> array(
								'label_class' => array(
									'pseudolabel'	=> __( 'Label classnames', 'wpv-views' ),
									'type'			=> 'text',
									'description'	=> __( 'Space-separated list of classnames to apply to the labels. For example: classone classtwo', 'wpv-views' )
								),
								'label_style' => array(
									'pseudolabel'	=> __( 'Label inline style', 'wpv-views' ),
									'type'			=> 'text',
									'description'	=> __( 'Raw inline styles to apply to the labels. For example: color:red;background:none;', 'wpv-views' )
								),
							),
						),
					)
				),
			),
		);
		return $data;
	}

}