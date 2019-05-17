<?php
/**
 * Admin functions for the ajde_events post type
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventON/Admin/ajde_events
 * @version     2.4.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class evo_ajde_events{
	public function __construct(){
		add_filter( 'manage_edit-ajde_events_columns', array($this,'eventon_edit_event_columns') );

		// custom filters
		add_action('restrict_manage_posts',array($this,'evo_restrict_manage_posts'));
		add_filter('query_vars', array($this,'wpse57344_register_query_vars' ));
		add_action( 'pre_get_posts', array($this,'wpse57351_pre_get_posts' ));

		add_action('manage_ajde_events_posts_custom_column', array($this,'eventon_custom_event_columns'), 10, 2 );
		add_filter( 'manage_edit-ajde_events_sortable_columns', array($this,'eventon_custom_events_sort'));
		add_filter( 'request', array($this,'eventon_custom_event_orderby') );

		add_filter( 'list_table_primary_column', array( $this, 'list_table_primary_column' ), 10, 2 );
		add_filter( 'post_row_actions', array($this,'eventon_duplicate_event_link_row'),10,2 );
		add_action( 'post_submitbox_misc_actions', array($this,'eventon_duplicate_event_post_button') );

		add_action( 'quick_edit_custom_box',  array($this,'eventon_admin_event_quick_edit'), 10, 2 );
		add_action( 'admin_enqueue_scripts', array($this,'eventon_admin_events_quick_edit_scripts'), 10 );
		add_action( 'save_post', array($this,'eventon_admin_event_quick_edit_save'), 10, 2 );
	}

	// Columns for events page
		function eventon_edit_event_columns( $existing_columns ) {
			global $eventon;
			
			// GET event type custom names
			$evcal_opt1= get_option('evcal_options_evcal_1');
			$evt_name = (!empty($evcal_opt1['evcal_eventt']))?$evcal_opt1['evcal_eventt']:'Event Type';
			$evt_name2 = (!empty($evcal_opt1['evcal_eventt2']))?$evcal_opt1['evcal_eventt2']:'Event Type 2';
			
			if ( empty( $existing_columns ) && ! is_array( $existing_columns ) )
				$existing_columns = array();

			unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

			$columns = array();
			$columns["cb"] = "<input type=\"checkbox\" />";
			
			$columns["name"] = __( 'Event Name', 'eventon' );

			$columns["event_location"] = __( 'Location', 'eventon' );
			$columns["event_type"] = __( $evt_name, 'eventon' );
			$columns["event_type_2"] = __( $evt_name2, 'eventon' );
			$columns["event_start_date"] = __( 'Start Date', 'eventon' );
			$columns["event_end_date"] = __( 'End Date', 'eventon' );
			$columns["evo_featured"] = '<img src="' . AJDE_EVCAL_URL . '/assets/images/icons/featured.png" title="' . __( 'Featured', 'eventon' ) . '" title="' . __( 'Featured', 'eventon' ) . '" width="12" height="12" />';
			$columns["repeat"] = '<img src="' . AJDE_EVCAL_URL . '/assets/images/icons/evo_repeat.png" alt="' . __( 'Event Repeat', 'eventon' ) . '" title="' . __( 'Event Repeat', 'eventon' ) . '" class="tips" />';
			//$columns["date"] = __( 'Date', 'eventon' );

			$columns = apply_filters('evo_event_columns', $columns);	

			return array_merge( $columns, $existing_columns );
		}

	// Custom filters for all events
		function evo_restrict_manage_posts() {
			global $typenow;

			if ($typenow=='ajde_events'){
	           	$event_date_type = (isset($_GET['event_date_type'])? $_GET['event_date_type']:null);
				?>
				<select name="event_date_type">
					<option value="all"><?php _e('All Events','eventon');?></option>
					<option value="past" <?php echo ($event_date_type=='past')?"selected='selected'":'';?>><?php _e('Past Events','eventon');?></option>
					<option value="live" <?php echo ($event_date_type=='live')?"selected='selected'":'';?>><?php _e('Current Events','eventon');?></option>
				</select>
				<?php
	        }
		}
		function wpse57344_register_query_vars( $qvars ){
		    //Add these query variables
		    $qvars[] = 'event_date_type';
		    return $qvars;
		}
		function wpse57351_pre_get_posts( $query ) {

		    //Only alter query if custom variable is set.
		    $event_date_type = $query->get('event_date_type');
		    if( !empty($event_date_type) ){

		         //Be careful not override any existing meta queries.
		        $meta_query = $query->get('meta_query');
		        if( empty($meta_query) )
		            $meta_query = array();

		        //Get posts with date between the first and last of given month
		        date_default_timezone_set('UTC');	
		        $timenow = current_time('timestamp');


		        if($event_date_type=='past'){
		        	$meta_query[] = array(
			            'key' => 'evcal_erow',
			            'value' => $timenow,
			            'compare' => '<',
			        );
		        }elseif($event_date_type=='live'){
		        	$meta_query[] = array(
			            'key' => 'evcal_erow',
			            'value' => $timenow,
			            'compare' => '>=',
			        );
		        }
		        
		        $query->set('meta_query',$meta_query);
		    }
		}

	// Custom Columns for event page
		function eventon_custom_event_columns( $column , $post_id) {
			global $post, $eventon;

			//if ( empty( $ajde_events ) || $ajde_events->id != $post->ID )
				//$ajde_events = get_product( $post );
			$pmv = get_post_custom($post->ID);

			switch ($column) {
				case has_filter("evo_column_type_{$column}"):
						$content = apply_filters("evo_column_type_{$column}", $post_id);
						echo $content;
					break;
				case "thumb" :
					//echo '<a href="' . get_edit_post_link( $post->ID ) . '">' . $ajde_events->get_image() . '</a>';
				break;
				
				case "name" :
					$edit_link = get_edit_post_link( $post->ID );
					$title = _draft_or_post_title();
					$post_type_object = get_post_type_object( $post->post_type );
					$can_edit_post = current_user_can( $post_type_object->cap->edit_post, $post->ID );


					echo "<div class='evoevent_item'>";
						$img_src = $eventon->evo_admin->get_image('thumbnail',false);
						$event_color = eventon_get_hex_color($pmv);
						echo '<a class="evoevent_image" href="' . get_edit_post_link( $post_id ) . '">';
						if($img_src){
							echo '<img class="evoEventCirc" src="' . $img_src . '"/>';
						}else{
							echo '<span class="evoEventCirc" style="background-color:' . $event_color . '"></span>';
						}
						echo '</a><div class="evo_item_details">';
					
						
					if($can_edit_post){
						echo '<strong><a class="row-title" href="'.$edit_link.'">' . $title.'</a>';
					}else{
						echo '<strong>' . $title.'';
					}

					_post_states( $post );

					echo '</strong>';
					
					if ( $post->post_parent > 0 )
						echo '&nbsp;&nbsp;&larr; <a href="'. get_edit_post_link($post->post_parent) .'">'. get_the_title($post->post_parent) .'</a>';

					// Excerpt view
					if (isset($_GET['mode']) && $_GET['mode']=='excerpt') echo apply_filters('the_excerpt', $post->post_excerpt);

					// Get actions
						$actions = array();

						$actions['id'] = 'ID: ' . $post->ID;

						if ( $can_edit_post && 'trash' != $post->post_status ) {
							$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( __( 'Edit this item' ) ) . '">' . __( 'Edit' ) . '</a>';
							$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr( __( 'Edit this item inline' ) ) . '">' . __( 'Quick&nbsp;Edit' ) . '</a>';
						}
						if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
							if ( 'trash' == $post->post_status )
								$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . "'>" . __( 'Restore' ) . "</a>";
							elseif ( EMPTY_TRASH_DAYS )
								$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash' ) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash' ) . "</a>";
							if ( 'trash' == $post->post_status || !EMPTY_TRASH_DAYS )
								$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently' ) ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently' ) . "</a>";
						}
						if ( $post_type_object->public ) {
							if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
								if ( $can_edit_post )
									$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'Preview' ) . '</a>';
							} elseif ( 'trash' != $post->post_status ) {
								$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'View' ) . '</a>';
							}
						}

						$actions = apply_filters( 'post_row_actions', $actions, $post );

					echo '<div class="row-actions">';

					$i = 0;
					$action_count = sizeof($actions);

					foreach ( $actions as $action => $link ) {
						++$i;
						( $i == $action_count ) ? $sep = '' : $sep = ' | ';
						echo "<span class='$action'>$link$sep</span>";
					}
					echo '</div>';
					
					get_inline_data( $post );
				
					$event = $eventon->evo_event_item->get_event($post->ID);
					
					//print_r($event);
					
					/* Custom inline data for eventon */
					echo '<div class="hidden" id="eventon_inline_' . $post->ID . '">';
					foreach($eventon->evo_event_item->get_event_fields_edit()  as $field){
						$value = (!empty($event->$field))? $event->$field: null;
						echo "<div class='{$field}'>{$value}</div>";
					}
					echo "<div class='_menu_order'>".$post->menu_order."</div>";
					echo '</div>';
					echo '</div>';
					
				break;
				
				case "event_type" :		
					if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
						echo '<span class="na">&ndash;</span>';
					} else {
						foreach ( $terms as $term ) {
							$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=ajde_events' ) . ' ">' . $term->name . '</a>';
						}

						echo implode( ', ', $termlist );
					}
				break;
				case "event_type_2" :		
					if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
						echo '<span class="na">&ndash;</span>';
					} else {
						foreach ( $terms as $term ) {
							$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=ajde_events' ) . ' ">' . $term->name . '</a>';
						}

						echo implode( ', ', $termlist );
					}
				break;
				case "event_location":
					
					if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
						echo '<span class="na">&ndash;</span>';
					} else {
						foreach ( $terms as $term ) {
							$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=ajde_events' ) . ' ">' . $term->name . '</a>';
						}

						echo implode( ', ', $termlist );
					}
						
				break;	

				case "event_start_date":
					
					if(evo_check_yn($pmv, 'evo_year_long')){
						echo date('Y', $pmv['evcal_srow'][0]);
					}elseif(evo_check_yn($pmv, '_evo_month_long')){
						echo date_i18n('F, Y', $pmv['evcal_srow'][0]);
					}else{
						if(!empty($pmv['evcal_srow'])){
							$_START = eventon_get_editevent_kaalaya($pmv['evcal_srow'][0]);
							if(evo_check_yn($pmv, 'evcal_allday')){
								echo $_START[0]. ' -'. __('All Day','eventon');
							}else{
								echo $_START[0].' - '.$_START[1].':'.$_START[2]. (!empty($_START[3])? $_START[3]:'');
							}		
							
							
						}else{	echo "--";	}	
					}					
						
				break;		
				
				case "event_end_date":	
					
					if(evo_check_yn($pmv, 'evo_year_long')){
						echo date('Y', $pmv['evcal_srow'][0]);
					}elseif(evo_check_yn($pmv, '_evo_month_long')){
						echo date_i18n('F, Y', $pmv['evcal_srow'][0]);
					}else{
						if(!empty($pmv['evcal_erow'])){	
							$_END = eventon_get_editevent_kaalaya($pmv['evcal_erow'][0]);		
							if(evo_check_yn($pmv, 'evcal_allday')){
								echo $_END[0]. ' -'. __('All Day','eventon');
							}else{
								echo $_END[0].' - '.$_END[1].':'.$_END[2]. (!empty($_END[3])? $_END[3]:'');
							}	
						}else{	echo "--";	}
					}		
				break;
				
				case "evo_featured":
					
					$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=eventon-feature-event&eventID=' . $post->ID ), 'eventon-feature-event' );
					echo '<a href="' . $url . '" title="'. __( 'Toggle featured', 'eventon' ) . '">';
					if ( get_post_meta($post->ID, '_featured', true)=='yes' ) {
						echo '<img src="' . AJDE_EVCAL_URL . '/assets/images/icons/featured.png" title="'. __( 'Yes', 'eventon' ) . '" height="14" width="14" />';
					} else {
						echo '<img src="' . AJDE_EVCAL_URL . '/assets/images/icons/featured-off.png" title="'. __( 'No', 'eventon' ) . '" height="14" width="14" />';
					}
					echo '</a>';
					
					//echo get_post_meta($post->ID, '_featured', true);		
				break;
				
				case 'repeat':
					
					$repeat = get_post_meta($post->ID, 'evcal_repeat',true);		
					
					if(!empty($repeat) && $repeat=='yes'){
						$repeat_freq = get_post_meta($post->ID, 'evcal_rep_freq',true);
						$output_repeat = '<span>'.$repeat_freq.'</span>';
					}else{
						$output_repeat = '<span class="na">&ndash;</span>';
					}
					
					echo $output_repeat;
				break;
			}
		}
		function eventon_custom_events_sort($columns) {
			$custom = array(
				'event_start_date'		=> 'evcal_start_date',
				'event_end_date'		=> 'evcal_end_date',
				'event_location'		=> 'event_location',
				'name'					=> 'title',
				'evo_featured'			=> 'featured',
				//'repeat'				=> 'repeat',
			);
			return wp_parse_args( $custom, $columns );
		}
		function eventon_custom_event_orderby( $vars ) {
			if (isset( $vars['orderby'] )) :
				if ( 'evcal_start_date' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> 'evcal_srow',
						'orderby' 	=> 'meta_value_num'
					) );
				endif;
				if ( 'evcal_end_date' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> 'evcal_erow',
						'orderby' 	=> 'meta_value'
					) );
				endif;
				if ( 'featured' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> '_featured',
						'orderby' 	=> 'meta_value'
					) );
				endif;
				if ( 'event_location' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> 'evcal_location',
						'orderby' 	=> 'meta_value'
					) );
				endif;
			endif;

			return $vars;
		}

	// Set list table primary column for events
		function list_table_primary_column( $default, $screen_id ) {

			if ( 'edit-ajde_events' === $screen_id ) {
				return 'name';
			}

			return $default;
		}

	// Duplicate event
		function eventon_duplicate_event_link_row($actions, $post) {

			if ( function_exists( 'duplicate_post_plugin_activation' ) ) return $actions;
			
			if ( $post->post_type != 'ajde_events' )	return $actions;

			$post_type = get_post_type_object( $post->post_type );

			if ( current_user_can( $post_type->cap->edit_post, $post->ID ) ){

				$actions['duplicate'] = '<a href="' . wp_nonce_url( admin_url( 'admin.php?action=duplicate_event&amp;post=' . $post->ID ), 'eventon-duplicate-event_' . $post->ID ) . '" title="' . __( 'Make a duplicate from this event', 'eventon' )
				. '" rel="permalink">' .  __( 'Duplicate', 'eventon' ) . '</a>';
			}

			return $actions;
		}
		function eventon_duplicate_event_post_button() {
			global $post;

			if ( function_exists( 'duplicate_post_plugin_activation' ) ) return;
			
			if ( ! is_object( $post ) ) return;

			if ( $post->post_type != 'ajde_events' ) return;

			if ( isset( $_GET['post'] ) ) {
				$notifyUrl = wp_nonce_url( admin_url( "admin.php?action=duplicate_event&post=" . absint( $_GET['post'] ) ), 'eventon-duplicate-event_' . $_GET['post'] );
				?>
				<div class="misc-pub-section" >
					<div id="duplicate-action"><a class="submitduplicate duplication button" href="<?php echo esc_url( $notifyUrl ); ?>"><?php _e( 'Duplicate this event', 'eventon' ); ?></a></div>
					
				</div>
				<?php
			}
		}

	// Custom quick edit - form
		function eventon_admin_events_quick_edit_scripts( $hook ) {
			global $eventon, $post_type;

			if ( $hook == 'edit.php' && $post_type == 'ajde_events' )
		    	wp_enqueue_script( 'eventon_quick-edit', AJDE_EVCAL_URL. '/assets/js/admin/quick-edit.js', array('jquery') );
		}
		function eventon_admin_event_quick_edit( $column_name, $post_type ) {
			if ($column_name != 'event_start_date' || $post_type != 'ajde_events') return;

				$evcal_date_format = eventon_get_time_format('24');

				global $ajde;
			?>
		    <fieldset class="inline-edit-col-left">
				<div id="eventon-fields" class="inline-edit-col">

					<legend class='inline-edit-legend'><?php _e( 'Event Data', 'eventon' ); ?></legend>
					
					<div class="event_fields inline-edit-col">
						<input type='hidden' name='_evo_date_format' value=''/>
						<input type='hidden' name='_evo_time_format' value=''/>
						<label>
						    <span class="title"><?php _e( 'Start Date', 'eventon' ); ?></span>
						    <span class="input-text-wrap">
								<input type="text" name="evcal_start_date" class="text" placeholder="<?php _e( 'Event Start Date', 'eventon' ); ?>" value="">
							</span>
						</label>	
						<label>
						    <span class="title"><?php _e( 'Start Time', 'eventon' ); ?></span>
						    <span class="input-text-wrap">
								<span class='input_time'>
									<input type="text" name="evcal_start_time_hour" class="text" placeholder="<?php _e( 'Event Start Hour', 'eventon' ); ?>" value="">
									<em>Hr</em>
								</span>
								<span class='input_time'>
									<input type="text" name="evcal_start_time_min" class="text" placeholder="<?php _e( 'Event Start Minutes', 'eventon' ); ?>" value="">
									<em>Min</em>
								</span>
								<?php if($evcal_date_format=='12h'):?>
								<span class='input_time'>
									<input type="text" name="evcal_st_ampm" class="text" placeholder="<?php _e( 'Event Start AM/PM', 'eventon' ); ?>" value="">
									<em>AM/PM</em>
								</span>
								<?php endif;?>
							</span>
						</label>
						
						<?php // end time date?>
						<label>
						    <span class="title"><?php _e( 'End Date', 'eventon' ); ?></span>
						    <span class="input-text-wrap">
								<input type="text" name="evcal_end_date" class="text" placeholder="<?php _e( 'Event End Date', 'eventon' ); ?>" value="">
							</span>
						</label>	
						<label>
						    <span class="title"><?php _e( 'End Time', 'eventon' ); ?></span>
						    <span class="input-text-wrap">
								<span class='input_time'>
									<input type="text" name="evcal_end_time_hour" class="text" placeholder="<?php _e( 'Event End Hour', 'eventon' ); ?>" value="">
									<em>Hr</em>
								</span>
								<span class='input_time'>
									<input type="text" name="evcal_end_time_min" class="text" placeholder="<?php _e( 'Event End Minutes', 'eventon' ); ?>" value="">
									<em>Min</em>
								</span>
								<?php if($evcal_date_format=='12h'):?>
								<span class='input_time'>
									<input type="text" name="evcal_et_ampm" class="text" placeholder="<?php _e( 'Event End AM/PM', 'eventon' ); ?>" value="">
									<em>AM/PM</em>
								</span>
								<?php endif;?>
							</span>
						</label>

						<label>
						    <span class="title"><?php _e( 'Subtitle', 'eventon' ); ?></span>
						    <span class="input-text-wrap">
								<input type="text" name="evcal_subtitle" class="text" placeholder="<?php _e( 'Event Sub Title', 'eventon' ); ?>" value="">
							</span>
						</label>
						
						<?php

							$fields = array(
								'evcal_allday'=> array(
									'type'=>'yesno',
									'label'=>__('All day event','eventon')
								),
								'evo_hide_endtime'=> array(
									'type'=>'yesno',
									'label'=>__('Hide end time from calendar','eventon')
								),								
								'_featured'=> array(
									'type'=>'yesno',
									'label'=>__('Featured event','eventon')
								),
								'evo_exclude_ev'=> array(
									'type'=>'yesno',
									'label'=>__('Exclude from calendar','eventon')
								),
								'location'=> array(
									'type'=>'subheader',
									'label'=>__('Location Data','eventon')
								),
								'evcal_gmap_gen'=> array(
									'type'=>'yesno',
									'label'=>__('Generate google map from the address','eventon')
								),
								'evcal_hide_locname'=> array(
									'type'=>'yesno',
									'label'=>__('Hide location name from the event card','eventon')
								),
								'evo_access_control_location'=> array(
									'type'=>'yesno',
									'label'=>__('Make location information only visible to logged-in users','eventon')
								),
								'organizer'=> array(
									'type'=>'subheader',
									'label'=>__('Organizer Data','eventon')
								),
								'evo_evcrd_field_org'=> array(
									'type'=>'yesno',
									'label'=>__('Hide organizer field from event card','eventon')
								),
							);

							foreach($fields as $field=>$val){
								switch($val['type']){
									case 'yesno': ?>
										<p class="yesno_row evo">
										<?php
											echo $ajde->wp_admin->html_yesnobtn(array(
												'id'=>$field,
												'label'=> $val['label'],
												'input'=>true
											));
										?>
										</p>	
									<?php
									break;
									case 'subheader':
										?><p><?php echo $val['label'];?></p><?php
									break;
								}
							}
						?>

					</div>

					<input type="hidden" name="eventon_quick_edit_nonce" value="<?php echo wp_create_nonce( 'eventon_quick_edit_nonce' ); ?>" />

				</div>
			</fieldset>
			<?php
		}

		// SAVE QUICK EDIT
		function eventon_admin_event_quick_edit_save( $post_id, $post ) {

			if ( ! $_POST || is_int( wp_is_post_revision( $post_id ) ) || is_int( wp_is_post_autosave( $post_id ) ) ) return $post_id;
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;
			if ( ! isset( $_POST['eventon_quick_edit_nonce'] ) || ! wp_verify_nonce( $_POST['eventon_quick_edit_nonce'], 'eventon_quick_edit_nonce' ) ) return $post_id;
			if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
			if ( $post->post_type != 'ajde_events' ) return $post_id;

			global $eventon, $wpdb;

			// Save fields
			if ( isset( $_POST['evcal_subtitle'] ) ) 
				update_post_meta( $post_id, 'evcal_subtitle', eventon_clean( $_POST['evcal_subtitle'] ) );
					
			
			$proper_time = 	evoadmin_get_unix_time_fromt_post($post_id);
			
			// start time
			//$proper_time = eventon_get_unix_time();	
			// full time converted to unix time stamp
			if ( !empty($proper_time['unix_start']) )
				update_post_meta( $post_id, 'evcal_srow', $proper_time['unix_start']);
			
			if ( !empty($proper_time['unix_end']) )
				update_post_meta( $post_id, 'evcal_erow', $proper_time['unix_end']);
			

			foreach( apply_filters('eventon_quick_save_fields', array(
				'_featured',
				'evo_hide_endtime',
				'evcal_allday',
				'evo_exclude_ev',
				'evcal_gmap_gen',
				'evcal_hide_locname',
				'evo_access_control_location',
				'evo_evcrd_field_org',
			)) as $field){
				if( !isset($_POST[ $field ])) continue;
				update_post_meta( $post_id, $field, $_POST[ $field ]  );
			}
			
			
			// menu order
			if( isset( $_POST['_menu_order'] ) ){				
				$newpostdata['menu_order'] = 5;
		        $newpostdata['ID'] = $post_id;
				//wp_update_post($newpostdata);
			}
		}
}
new evo_ajde_events();

?>