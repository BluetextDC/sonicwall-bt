<?php
/**
 * Meta boxes for ajde_events
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin/ajde_events
 * @version     2.5.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_event_metaboxes{
	public function __construct(){
		add_action( 'add_meta_boxes', array($this,'metabox_init') );
		add_action( 'save_post', array($this,'eventon_save_meta_data'), 1, 2 );
		add_action( 'post_submitbox_misc_actions', array($this,'ajde_events_settings_per_post' ));
	}

	// INIT meta boxes
		function metabox_init(){

			$evcal_opt1= get_option('evcal_options_evcal_1');

			// ajde_events meta boxes
			add_meta_box('ajdeevcal_mb2',__('Event Color','eventon'), array($this,'meta_box_event_color'),'ajde_events', 'side', 'core');
			add_meta_box('ajdeevcal_mb1', __('Event Details','eventon'), array($this,'ajde_evcal_show_box'),'ajde_events', 'normal', 'high');	
			
			// if third party is enabled
			if(!empty($evcal_opt1['evcal_paypal_pay']) && $evcal_opt1['evcal_paypal_pay']=='yes' )
				add_meta_box('ajdeevcal_mb3','Third Party Settings', array($this,'ajde_evcal_show_box_3'),'ajde_events', 'normal', 'core');
			
			do_action('eventon_add_meta_boxes');
		}

	// EXTRA event settings for the page
		function ajde_events_settings_per_post(){
			global $post, $eventon, $ajde;

			if ( ! is_object( $post ) ) return;

			if ( $post->post_type != 'ajde_events' ) return;

			if ( isset( $_GET['post'] ) ) {

				// Global Event Props will be set initially right here
				$event = new EVO_Event($post->ID);
			?>
				<div class="misc-pub-section" >
				<div class='evo_event_opts'>
					<p class='yesno_row evo'>
						<?php 	echo $ajde->wp_admin->html_yesnobtn(
							array(
								'id'=>'evo_exclude_ev', 
								'var'=> $event->get_prop('evo_exclude_ev'),
								'input'=>true,
								'label'=>__('Exclude from calendar','eventon'),
								'guide'=>__('Set this to Yes to hide event from showing in all calendars','eventon'),
								'guide_position'=>'L'
							));
						?>
					</p>
					<p class='yesno_row evo'>
						<?php 	echo $ajde->wp_admin->html_yesnobtn(
							array(
								'id'=>'_featured', 
								'var'=> $event->get_prop('_featured'),
								'input'=>true,
								'label'=>__('Featured Event','eventon'),
								'guide'=>__('Make this event a featured event','eventon'),
								'guide_position'=>'L'
							));
						?>	
					</p>
					<p class='yesno_row evo'>
						<?php 	echo $ajde->wp_admin->html_yesnobtn(
							array(
								'id'=>'_completed', 
								'var'=> $event->get_prop('_completed'),
								'input'=>true,
								'label'=>__('Event Completed','eventon'),
								'guide'=>__('Mark this event as completed','eventon'),
								'guide_position'=>'L'
							));
						?>	
					</p>
					<p class='yesno_row evo'>
						<?php 	echo $ajde->wp_admin->html_yesnobtn(
							array(
								'id'=>'_cancel', 
								'var'=> $event->get_prop('_cancel'),
								'input'=>true,
								'label'=>__('Cancel Event','eventon'),
								'guide'=>__('Cancel this event','eventon'),
								'guide_position'=>'L',
								'attr'=>array('afterstatement'=>'evo_editevent_cancel_text')
							));
						?>	
					</p><p class='yesno_row evo'>
						<?php 	echo $ajde->wp_admin->html_yesnobtn(
							array(
								'id'=>'_onlyloggedin', 
								'var'=> $event->get_prop('_onlyloggedin'),
								'input'=>true,
								'label'=>__('Only for loggedin users','eventon'),
								'guide'=>__('This will make this event only visible if the users are loggedin to this site','eventon'),
								'guide_position'=>'L',
							));
						?>	
					</p>
					<?php
						$_cancel_reason = $event->get_prop('_cancel_reason');
					?>
					<p id='evo_editevent_cancel_text' style='display:<?php echo (!empty($_cancel) && $_cancel=='yes')? 'block':'none';?>'><textarea name="_cancel_reason" style='width:100%' rows="3" placeholder='<?php _e('Type the reason for cancelling','eventon');?>'><?php echo $_cancel_reason;?></textarea></p>
					<?php
						// @since 2.2.28
						do_action('eventon_event_submitbox_misc_actions',$event);
					?>
				</div>
				</div>
			<?php
			}
		}
	
	// Event Color Meta Box	
		function meta_box_event_color(){
				
			// Use nonce for verification
			wp_nonce_field( plugin_basename( __FILE__ ), 'evo_noncename_2' );
			$event_id = get_the_ID();

			$event = new EVO_Event($event_id);
			$ev_vals = $event->get_data();
			
			$evOpt = get_option('evcal_options_evcal_1');

		?>		
				<table id="meta_tb2" class="form-table meta_tb" >
				<tr>
					<td>
					<?php
						// Hex value cleaning
						$hexcolor = eventon_get_hex_color($ev_vals,'', $evOpt );	
					?>			
					<div id='color_selector' >
						<em id='evColor' style='background-color:<?php echo (!empty($hexcolor) )? $hexcolor: 'na'; ?>'></em>
						<p class='evselectedColor'>
							<span class='evcal_color_hex evcal_chex'  ><?php echo (!empty($hexcolor) )? $hexcolor: 'Hex code'; ?></span>
							<span class='evcal_color_selector_text evcal_chex'><?php _e('Click here to pick a color','eventon');?></span>
						</p>
					</div>
					<p style='margin-bottom:0; padding-bottom:0'><i><?php _e('OR Select from other colors','eventon');?></i></p>
					
					<div id='evcal_colors'>
						<?php 

							global $wpdb;
							$tableprefix = $wpdb->prefix;

							$results = $wpdb->get_results(
								"SELECT {$tableprefix}posts.ID, mt0.meta_value AS color, mt1.meta_value AS color_num
								FROM {$tableprefix}posts 
								INNER JOIN {$tableprefix}postmeta AS mt0 ON ( {$tableprefix}posts.ID = mt0.post_id )
								INNER JOIN {$tableprefix}postmeta AS mt1 ON ( {$tableprefix}posts.ID = mt1.post_id )
								WHERE 1=1 
								AND ( mt0.meta_key = 'evcal_event_color' )
								AND {$tableprefix}posts.post_type = 'ajde_events'
								AND (({$tableprefix}posts.post_status = 'publish'))
								GROUP BY {$tableprefix}posts.ID
								ORDER BY {$tableprefix}posts.post_date DESC"
							, ARRAY_A);

							if($results){
								$other_colors = array();
								
								foreach($results as $color){
									// hex color cleaning
									$hexval = ($color['color']=='#')? substr($color['color'],1):$color['color'];
									$hexval_num = !empty($color['color_num'])? $color['color_num']: 0;
									
									
									if(!empty( $hexval) && (empty($other_colors) || (is_array($other_colors) && !in_array($hexval, $other_colors)	)	)	){
										echo "<div class='evcal_color_box' style='background-color:#".$hexval."'color_n='".$hexval_num."' color='".$hexval."'></div>";
										
										$other_colors[]=$hexval;
									}
								}
							}
							
						?>				
					</div>
					<div class='clear'></div>
										
					
					<input id='evcal_event_color' type='hidden' name='evcal_event_color' 
						value='<?php echo str_replace('#','',$hexcolor); ?>'/>
					<input id='evcal_event_color_n' type='hidden' name='evcal_event_color_n' 
						value='<?php echo (!empty($ev_vals["evcal_event_color_n"]) )? $ev_vals["evcal_event_color_n"][0]: null ?>'/>
					</td>
				</tr>
				<?php do_action('eventon_metab2_end'); ?>
				</table>
		<?php }

	// MAIN META BOX CONTENT
		function event_edit_tax_section($tax, $eventid, $tax_string=''){
			$event_tax_term = wp_get_post_terms($eventid, $tax);

			$tax_name = !empty($tax_string)? $tax_string: str_replace('event_', '', $tax);

			ob_start();
			// If a tax term is already set
			if ( $event_tax_term && ! is_wp_error( $event_tax_term ) ){	

			?>
				<p class='evo_selected_tax_term'><em><?php echo $tax_name;?>:</em> <span><?php echo $event_tax_term[0]->name;?></span> 
					<i class='fa fa-pencil evo_tax_term_form ajde_popup_trig' data-popc='evo_term_lightbox' data-type='edit' data-id='<?php echo $event_tax_term[0]->term_id;?>' title='<?php _e('Edit','eventon');?>'></i> 
					<i class='fa fa-close evo_tax_remove' data-type='delete' data-id='<?php echo $event_tax_term[0]->term_id;?>' title='<?php _e('Delete','eventon');?>'></i>
				</p>
				<p class='evo_selected_tax_actions'>
					<a class='evo_tax_term_list evo_btn ajde_popup_trig' data-type='list' data-popc='evo_term_lightbox' data-eventid='<?php echo $eventid;?>' data-id='<?php echo $event_tax_term[0]->term_id;?>'><?php printf(__('Select different %s from list','eventon'),  $tax_name);?></a>
					<a class='evo_tax_term_form evo_btn ajde_popup_trig' data-popc='evo_term_lightbox' data-type='new' ><?php printf(__('Create a new %s','eventon'),$tax_name);?></a>
				</p>
				
				<?php
			}else{
				?>
				<p class='evo_selected_tax_actions'>
					<a class='evo_tax_term_list evo_btn ajde_popup_trig' data-type='list' data-popc='evo_term_lightbox' data-eventid='<?php echo $eventid;?>'><?php printf(__('Select a %s from list','eventon'), $tax_name);?></a>
					<a class='evo_tax_term_form evo_btn ajde_popup_trig' data-popc='evo_term_lightbox' data-eventid='<?php echo $eventid;?>' data-type='new' data-tax='event_location'><?php printf(__('Create a new %s','eventon'),$tax_name);?></a>
				</p>
				<?php
			}
			return ob_get_clean();
		}

		function ajde_evcal_show_box(){
			global $eventon, $ajde, $post;
			
			$evcal_opt1= get_option('evcal_options_evcal_1');
			$evcal_opt2= get_option('evcal_options_evcal_2');
			
			// Use nonce for verification
			wp_nonce_field( plugin_basename( __FILE__ ), 'evo_noncename' );
			
			// The actual fields for data entry
			$p_id = get_the_ID();
			$ev_vals = get_post_custom($p_id);
						
			$evcal_allday = (!empty($ev_vals["evcal_allday"]))? $ev_vals["evcal_allday"][0]:null;		
			$show_style_code = ($evcal_allday=='yes') ? "style='display:none'":null;

			$select_a_arr= array('AM','PM');
						
		// array of all meta boxes
			$metabox_array = apply_filters('eventon_event_metaboxs', array(
				array(
					'id'=>'ev_subtitle',
					'name'=>__('Event SubTitle','eventon'),
					'variation'=>'customfield',	
					'hiddenVal'=>'',	
					'iconURL'=>'fa-pencil',
					'iconPOS'=>'',
					'type'=>'code',
					'content'=>'',
					'slug'=>'ev_subtitle'
				),array(
					'id'=>'ev_timedate',
					'name'=>__('Time and Date','eventon'),	
					'hiddenVal'=>'',	
					'iconURL'=>'fa-clock-o','variation'=>'customfield','iconPOS'=>'',
					'type'=>'code',
					'content'=>'',
					'slug'=>'ev_timedate'
				),array(
					'id'=>'ev_location',
					'name'=>__('Location and Venue','eventon'),	
					'iconURL'=>'fa-map-marker','variation'=>'customfield','iconPOS'=>'',
					'type'=>'code',
					'slug'=>'ev_location',
				),array(
					'id'=>'ev_organizer',
					'name'=>__('Organizer','eventon'),	
					'hiddenVal'=>'',	
					'iconURL'=>'fa-microphone','variation'=>'customfield','iconPOS'=>'',
					'type'=>'code',
					'content'=>'',
					'slug'=>'ev_organizer'
				),array(
					'id'=>'ev_uint',
					'name'=>__('User Interaction for event click','eventon'),	
					'hiddenVal'=>'',	
					'iconURL'=>'fa-street-view','variation'=>'customfield','iconPOS'=>'',
					'type'=>'code',
					'content'=>'',
					'slug'=>'ev_uint',
					'guide'=>__('This define how you want the events to expand following a click on the eventTop by a user','eventon')
				),array(
					'id'=>'ev_learnmore',
					'name'=>__('Learn more about event link','eventon'),	
					'hiddenVal'=>'',	
					'iconURL'=>'fa-random','variation'=>'customfield','iconPOS'=>'',
					'type'=>'code',
					'content'=>'',
					'slug'=>'ev_learnmore',
					'guide'=>__('This will create a learn more link in the event card. Make sure your links start with http://','eventon')
				)
			));

		// if language corresponding enabled
			if(evo_settings_check_yn($evcal_opt1,'evo_lang_corresp')){
				$metabox_array[] = array(
					'id'=>'ev_lang',
					'name'=>__('Language for Event','eventon'),	
					'hiddenVal'=>'',	
					'iconURL'=>'fa-font','variation'=>'customfield','iconPOS'=>'',
					'type'=>'code',
					'content'=>'',
					'slug'=>'ev_lang',
				);
			}

		// Custom Meta fields for events
			$num = evo_calculate_cmd_count($evcal_opt1);
			for($x =1; $x<=$num; $x++){	
				if(!eventon_is_custom_meta_field_good($x)) continue;

				$fa_icon_class = $evcal_opt1['evcal__fai_00c'.$x];
				$visibility_type = (!empty($evcal_opt1['evcal_ec_f'.$x.'a4']) )? $evcal_opt1['evcal_ec_f'.$x.'a4']:'all' ;
				$metabox_array[] = array(
					'id'=>'evcal_ec_f'.$x.'a1',
					'variation'=>'customfield',
					'name'=>$evcal_opt1['evcal_ec_f'.$x.'a1'],		
					'iconURL'=>$fa_icon_class,
					'iconPOS'=>'',
					'fieldtype'=>'custommetafield',
					'x'=>$x,
					'visibility_type'=>$visibility_type,
					'type'=>'code',
					'content'=>'',
					'slug'=>'evcal_ec_f'.$x.'a1'
				);
			}
		
		// combine array with custom fields
		// $metabox_array = (!empty($evMB_custom) && count($evMB_custom)>0)? array_merge($metabox_array , $evMB_custom): $metabox_array;
		
		$closedmeta = eventon_get_collapse_metaboxes($p_id);
		
		?>	
			
			<div id='evo_mb' class='eventon_mb'>
				<input type='hidden' id='evo_collapse_meta_boxes' name='evo_collapse_meta_boxes' value=''/>
			<?php
				// initial values
					$visibility_types = array('all'=>__('Everyone','eventon'),'admin'=>__('Admin Only','eventon'),'loggedin'=>__('Loggedin Users Only','eventon'));

				// FOREACH metabox item
				foreach($metabox_array as $mBOX):
					
					// initials
						$icon_style = (!empty($mBOX['iconURL']))?
							'background-image:url('.$mBOX['iconURL'].')'
							:'background-position:'.$mBOX['iconPOS'];
						$icon_class = (!empty($mBOX['iconPOS']))? 'evIcons':'evII';
						
						$guide = (!empty($mBOX['guide']))? 
							$ajde->wp_admin->tooltips($mBOX['guide']):null;
						
						$hiddenVal = (!empty($mBOX['hiddenVal']))?
							'<span class="hiddenVal">'.$mBOX['hiddenVal'].'</span>':null;

						// visibility type ONLY for custom meta fields
							$visibility_type = (!empty($mBOX['visibility_type']))? "<span class='visibility_type'>".__('Visibility Type:','eventon').' '.$visibility_types[$mBOX['visibility_type']] .'</span>': false;
					
						$closed = (!empty($closedmeta) && in_array($mBOX['id'], $closedmeta))? 'closed':null;
			?>
				<div class='evomb_section' id='<?php echo $mBOX['id'];?>'>			
					<div class='evomb_header'>
						<?php // custom field with icons
							if(!empty($mBOX['variation']) && $mBOX['variation']	=='customfield'):?>	
							<span class='evomb_icon <?php echo $icon_class;?>'><i class='fa <?php echo $mBOX['iconURL']; ?>'></i></span>
							
						<?php else:	?>
							<span class='evomb_icon <?php echo $icon_class;?>' style='<?php echo $icon_style?>'></span>
						<?php endif; ?>
						<p><?php echo $mBOX['name'];?><?php echo $hiddenVal;?><?php echo $guide;?><?php echo $visibility_type;?></p>
					</div>
					<div class='evomb_body <?php echo $closed;?>' box_id='<?php echo $mBOX['id'];?>'>
					<?php 

					if(!empty($mBOX['content'])){
						echo $mBOX['content'];
					}else{
						switch($mBOX['id']){
							case 'ev_learnmore':
								echo "<div class='evcal_data_block_style1'>
								<div class='evcal_db_data'>
									<input type='text' id='evcal_lmlink' name='evcal_lmlink' value='". ((!empty($ev_vals["evcal_lmlink"]) )? $ev_vals["evcal_lmlink"][0]:null)."' style='width:100%'/><br/>";
									?>
									<span class='yesno_row evo'>
										<?php 	
										$openInNewWindow = (!empty($ev_vals["evcal_lmlink_target"]))? $ev_vals["evcal_lmlink_target"][0]: null;
										echo $ajde->wp_admin->html_yesnobtn(array(
											'id'=>'evcal_lmlink_target',
											'var'=>$openInNewWindow,
											'input'=>true,
											'label'=>__('Open in New window','eventon')
										));?>											
									</span>

								<?php echo "</div></div>";
							break;
							case 'ev_lang':
								echo "<div class='evcal_data_block_style1'>
								<div class='evcal_db_data'>";
									?>
									<p><?php _e('You can select the eventon language corresponding to this event. Eg. If you have eventon language L2 in French and this event is in french select L2 as eventon language correspondant for this event.','eventon');?></p>
									<p>
										<label for="_evo_lang"><?php _e('Corresponding eventON language','eventon');?></label>
										<select name="_evo_lang">
										<?php 

										$_evo_lang = (!empty($ev_vals["_evo_lang"]))? $ev_vals["_evo_lang"][0]: 'L1';

										$lang_variables = apply_filters('eventon_lang_variation', array('L1','L2', 'L3'));

										foreach($lang_variables as $lang){
											$slected = ($lang == $_evo_lang)? 'selected="selected"':null;
											echo "<option value='{$lang}' {$slected}>{$lang}</option>";
										}
										?></select>
									</p>

								<?php echo "</div></div>";
							break;
							case 'ev_uint':
								?>
								<div class='evcal_data_block_style1'>
									<div class='evcal_db_data'>										
										<?php
											$exlink_option = (!empty($ev_vals["_evcal_exlink_option"]))? $ev_vals["_evcal_exlink_option"][0]:1;
											$exlink_target = (!empty($ev_vals["_evcal_exlink_target"]) && $ev_vals["_evcal_exlink_target"][0]=='yes')?
												$ev_vals["_evcal_exlink_target"][0]:null;
										?>										
										<input id='evcal_exlink_option' type='hidden' name='_evcal_exlink_option' value='<?php echo $exlink_option; ?>'/>
										
										<input id='evcal_exlink_target' type='hidden' name='_evcal_exlink_target' value='<?php echo ($exlink_target) ?>'/>
										
										
										<p <?php echo ($exlink_option=='1' || $exlink_option=='3')?"style='display:none'":null;?> id='evo_new_window_io' class='<?php echo ($exlink_target=='yes')?'selected':null;?>'><span></span> <?php _e('Open in new window','eventon');?></p>
										
										<!-- external link field-->
										<input id='evcal_exlink' placeholder='<?php _e('Type the URL address','eventon');?>' type='text' name='evcal_exlink' value='<?php echo (!empty($ev_vals["evcal_exlink"]) )? $ev_vals["evcal_exlink"][0]:null?>' style='width:100%; <?php echo ($exlink_option !='1' && $exlink_option != '3')? 'display:block':'display:none'?>'/>
										
										<div class='evcal_db_uis'>
											<a link='no'  class='evcal_db_ui evcal_db_ui_0 <?php echo ($exlink_option=='X')?'selected':null;?>' title='<?php _e('Do nothing','eventon');?>' value='X'></a>

											<a link='no'  class='evcal_db_ui evcal_db_ui_1 <?php echo ($exlink_option=='1')?'selected':null;?>' title='<?php _e('Slide Down Event Card','eventon');?>' value='1'></a>
											
											<!-- open as link-->
											<a link='yes' class='evcal_db_ui evcal_db_ui_2 <?php echo ($exlink_option=='2')?'selected':null;?>' title='<?php _e('External Link','eventon');?>' value='2'></a>	
											
											<!-- open as popup -->
											<a link='yes' class='evcal_db_ui evcal_db_ui_3 <?php echo ($exlink_option=='3')?' selected':null;?>' title='<?php _e('Popup Window','eventon');?>' value='3'></a>
											
											<!-- single event -->
											<a link='yes' linkval='<?php echo get_permalink($p_id);?>' class='evcal_db_ui evcal_db_ui_4 <?php echo (($exlink_option=='4')?'selected':null);?>' title='<?php _e('Open Event Page','eventon');?>' value='4'></a>
											
											<?php
												// (-- addon --)
												//if(has_action('evcal_ui_click_additions')){do_action('evcal_ui_click_additions');}
											?>							
											<div class='clear'></div>
										</div>
									</div>
								</div>
								<?php
							break;

							case 'ev_location':

								// $opt = get_option( "evo_tax_meta");
								// print_r($opt);
								?>
								<div class='evcal_data_block_style1'>
									<p class='edb_icon evcal_edb_map'></p>
									<div class='evcal_db_data'>
										<div class='evcal_location_data_section'>										
											<div class='evo_singular_tax_for_event event_location' data-tax='event_location' data-eventid='<?php echo $p_id;?>'>
											<?php
												echo $this->event_edit_tax_section( __('event_location','eventon'),$p_id, __('location','eventon'));
											?>
											</div>									
										</div>
										
										<?php

											// yea no options for location
											foreach(array(
												'evo_access_control_location'=>array('evo_access_control_location',__('Make location information only visible to logged-in users','eventon')),
												'evcal_hide_locname'=>array('evo_locname',__('Hide Location Name from Event Card','eventon')),
												'evcal_gmap_gen'=>array('evo_genGmap',__('Generate Google Map from the address','eventon')),
												'evcal_name_over_img'=>array('evcal_name_over_img',__('Show location information over location image (If location image exist)','eventon')),
											) as $key=>$val){

												?>
												<p class='yesno_row evo'>
													<?php 	
													$variable_val = (!empty($ev_vals[$key]))? $ev_vals[$key][0]: 'no';
													echo $ajde->wp_admin->html_yesnobtn(array('id'=>'evo_locname', 'var'=>$variable_val));?>												
													<input type='hidden' name='<?php echo $key;?>' value="<?php echo (!empty($ev_vals[$key]) && $ev_vals[$key][0]=='yes')?'yes': 'no';?>"/>
													<label for='<?php echo $key;?>'><?php echo $val[1]; ?></label>
												</p>
												<p style='clear:both'></p>
												<?php
											}
										?>									
									</div>
								</div>
								<?php
							break;

							case 'ev_organizer':
								?>
								<div class='evcal_data_block_style1'>
									<p class='edb_icon evcal_edb_map'></p>
									<div class='evcal_db_data'>
										<div class='evcal_location_data_section'>	

										<div class='evo_singular_tax_for_event event_organizer' data-tax='event_organizer' data-eventid='<?php echo $p_id;?>'>
										<?php
											echo $this->event_edit_tax_section( __('event_organizer','eventon'),$p_id, __('organizer','eventon'));
										?>
										</div>
										
					                    </div><!--.evcal_location_data_section-->
										
										<!-- yea no field - hide organizer field from eventcard -->
										<p class='yesno_row evo'>
											<?php 	
											$evo_evcrd_field_org = (!empty($ev_vals["evo_evcrd_field_org"]))? $ev_vals["evo_evcrd_field_org"][0]: null;
											echo $ajde->wp_admin->html_yesnobtn(array('id'=>'evo_org_field_ec', 'var'=>$evo_evcrd_field_org));?>
											
											<input type='hidden' name='evo_evcrd_field_org' value="<?php echo (!empty($ev_vals["evo_evcrd_field_org"]) && $ev_vals["evo_evcrd_field_org"][0]=='yes')?'yes':'no';?>"/>
											<label for='evo_evcrd_field_org'><?php _e('Hide Organizer field from EventCard','eventon')?></label>
										</p>
										<p style='clear:both'></p>
									</div>
								</div>
								<?php
							break;

							case 'ev_timedate':
								// Minute increment	
								$minIncre = !empty($evcal_opt1['evo_minute_increment'])? (int)$evcal_opt1['evo_minute_increment']:60;
								$minADJ = 60/$minIncre;
								

								// --- TIME variations
								//$evcal_date_format = eventon_get_timeNdate_format($evcal_opt1);
								$wp_time_format = get_option('time_format');
								$hr24 = (strpos($wp_time_format, 'H')!==false || strpos($wp_time_format, 'G')!==false)? true:false;
								$evcal_date_format = array(
									'yy/mm/dd','Y/m/d',$hr24
								);
								$time_hour_span= ($evcal_date_format[2])?25:13;
								
								
								// GET DATE and TIME values
								$_START=(!empty($ev_vals['evcal_srow'][0]))?
									eventon_get_editevent_kaalaya($ev_vals['evcal_srow'][0],$evcal_date_format[1], $evcal_date_format[2]):false;
								$_END=(!empty($ev_vals['evcal_erow'][0]))?
									eventon_get_editevent_kaalaya($ev_vals['evcal_erow'][0],$evcal_date_format[1], $evcal_date_format[2]):false;

								// date and time formats used in edit page
								$used_js_dateFormat = $evcal_date_format[0];
								$used_dateFormat = $evcal_date_format[1];
								$used_timeFormat = $evcal_date_format[2]?'24h':'12h';

								ob_start();
								?>
								<!-- date and time formats to use -->
								<input type='hidden' name='_evo_date_format' value='<?php echo $used_dateFormat;?>'/>
								<input type='hidden' name='_evo_time_format' value='<?php echo $used_timeFormat;?>'/>	
								<div id='evcal_dates' date_format='<?php echo $used_js_dateFormat;?>'>	
									<p class='yesno_row evo fcw'>
										<?php 	echo $ajde->wp_admin->html_yesnobtn(array(
											'id'=>'evcal_allday_yn_btn', 
											'var'=>$evcal_allday, 
											'attr'=>array('allday_switch'=>'1',)
											));?>			
										<input type='hidden' name='evcal_allday' value="<?php echo ($evcal_allday=='yes')?'yes':'no';?>"/>
										<label for='evcal_allday_yn_btn'><?php _e('All Day Event', 'eventon')?></label>
									</p><p style='clear:both'></p>
									
									<!-- START TIME-->
									<div class='evo_start_event evo_datetimes'>
										<div class='evo_date'>
											<p id='evcal_start_date_label'><?php _e('Event Start Date', 'eventon')?></p>
											<input id='evo_dp_from' class='evcal_data_picker datapicker_on' type='text' id='evcal_start_date' name='evcal_start_date' value='<?php echo ($_START)?$_START[0]:null?>' placeholder='<?php echo $used_dateFormat;?>'/>					
											<span><?php _e('Select a Date', 'eventon')?></span>
										</div>					
										<div class='evcal_date_time switch_for_evsdate evcal_time_selector' <?php echo $show_style_code?>>
											<div class='evcal_select'>
												<select id='evcal_start_time_hour' class='evcal_date_select' name='evcal_start_time_hour'>
													<?php
														//echo "<option value=''>--</option>";
														$start_time_h = ($_START)?$_START[1]:null;						
													for($x=1; $x<$time_hour_span;$x++){	
														$y = ($time_hour_span==25)? sprintf("%02d",($x-1)): $x;							
														echo "<option value='$y'".(($start_time_h==$y)?'selected="selected"':'').">$y</option>";
													}?>
												</select>
											</div><p style='display:inline; font-size:24px;padding:4px 2px'>:</p>
											<div class='evcal_select'>						
												<select id='evcal_start_time_min' class='evcal_date_select' name='evcal_start_time_min'>
													<?php	
														//echo "<option value=''>--</option>";
														$start_time_m = ($_START)?	$_START[2]: null;
														for($x=0; $x<$minIncre;$x++){
															$min = $minADJ * $x;
															$min = ($min<10)?('0'.$min):$min;
															echo "<option value='$min'".(($start_time_m==$min)?'selected="selected"':'').">$min</option>";
														}?>
												</select>
											</div>
											
											<?php if(!$evcal_date_format[2]):?>
											<div class='evcal_select evcal_ampm_sel'>
												<select name='evcal_st_ampm' id='evcal_st_ampm' >
													<?php
														$evcal_st_ampm = ($_START)?$_START[3]:null;
														foreach($select_a_arr as $sar){
															echo "<option value='".$sar."' ".(($evcal_st_ampm==$sar)?'selected="selected"':'').">".$sar."</option>";
														}
													?>								
												</select>
											</div>	
											<?php endif;?>
											<br/>
											<span><?php _e('Select a Time', 'eventon')?></span>
										</div><div class='clear'></div>
									</div>
									
									<!-- END TIME -->
									<?php 
										$evo_hide_endtime = (!empty($ev_vals["evo_hide_endtime"]) )? $ev_vals["evo_hide_endtime"][0]:null;
									?>
									<div class='evo_end_event evo_datetimes switch_for_evsdate'>
										<div class='evo_enddate_selection' style='<?php echo ($evo_hide_endtime=='yes')?'opacity:0.5':null;?>'>
										<div class='evo_date'>
											<p><?php _e('Event End Date','eventon')?></p>
											<input id='evo_dp_to' class='evcal_data_picker datapicker_on' type='text' id='evcal_end_date' name='evcal_end_date' value='<?php echo ($_END)? $_END[0]:null; ?>' placeholder='<?php echo $used_dateFormat;?>'/>					
											<span><?php _e('Select a Date','eventon')?></span>					
										</div>
										<div class='evcal_date_time evcal_time_selector' <?php echo $show_style_code?>>
											<div class='evcal_select'>
												<select class='evcal_date_select' name='evcal_end_time_hour'>
													<?php	
														//echo "<option value=''>--</option>";
														$end_time_h = ($_END)?$_END[1]:null;
														for($x=1; $x<$time_hour_span;$x++){
															$y = ($time_hour_span==25)? sprintf("%02d",($x-1)): $x;								
															echo "<option value='$y'".(($end_time_h==$y)?'selected="selected"':'').">$y</option>";
														}
													?>
												</select>
											</div><p style='display:inline; font-size:24px;padding:4px'>:</p>
											<div class='evcal_select'>
												<select class='evcal_date_select' name='evcal_end_time_min'>
													<?php	
														//echo "<option value=''>--</option>";
														$end_time_m = ($_END[2])?$_END[2]:null;
														for($x=0; $x<$minIncre;$x++){
															$min = $minADJ * $x;
															$min = ($min<10)?('0'.$min):$min;
															echo "<option value='$min'".(($end_time_m==$min)?'selected="selected"':'').">$min</option>";
														}
													?>
												</select>
											</div>					
											<?php if(!$evcal_date_format[2]):?>
											<div class='evcal_select evcal_ampm_sel'>
												<select name='evcal_et_ampm'>
													<?php
														$evcal_et_ampm = ($_END)?$_END[3]:null;								
														foreach($select_a_arr as $sar){
															echo "<option value='".$sar."' ".(($evcal_et_ampm==$sar)?'selected="selected"':'').">".$sar."</option>";
														}
													?>								
												</select>
											</div>
											<?php endif;?>
											<br/>
											<span><?php _e('Select the Time','eventon')?></span>
										</div><div class='clear'></div>
									</div>
								</div>

								<!-- how time look on frontend -->
								<?php
									if(!empty($ev_vals['evcal_srow'])):
										$dtime = new evo_datetime();
										$val = $dtime->get_formatted_smart_time_piece($ev_vals['evcal_srow'][0],$ev_vals);
										echo "<p class='evo_datetime_frontendview' style='margin-top:10px'>".__('Default Date/time format:','eventon').' '.$val."</p>";
									endif;
								?>

									<!-- timezone value -->				
										<p style='padding-top:10px'><input type='text' name='evo_event_timezone' value='<?php echo (!empty($ev_vals["evo_event_timezone"]) )? $ev_vals["evo_event_timezone"][0]:null;?>' placeholder='<?php _e('Timezone text eg.PST','eventon');?>'/><label for=""><?php _e('Event timezone','eventon');?><?php $ajde->wp_admin->tooltips( __('Timezone text you type in here ex. PST will show next to event time on calendar.','eventon'),'',true);?></label></p>
										
										<!-- end time yes/no option -->					
										<p class='yesno_row evo '>
											<?php 	echo $ajde->wp_admin->html_yesnobtn(array('id'=>'evo_endtime', 'var'=>$evo_hide_endtime, 'attr'=>array('afterstatement'=>'evo_span_hidden_end')));?>
											
											<input type='hidden' name='evo_hide_endtime' value="<?php echo ($evo_hide_endtime=='yes')?'yes':'no';?>"/>
											<label for='evo_hide_endtime'><?php _e('Hide End Time from calendar', 'eventon')?></label>
										</p>
										<?php 
											// span event to hidden end time
											$evo_span_hidden_end = (!empty($ev_vals["evo_span_hidden_end"]) )? $ev_vals["evo_span_hidden_end"][0]:null;
											$evo_span_hidd_display = ($evo_hide_endtime && $evo_hide_endtime=='yes')? 'block':'none';
										?>
										<p class='yesno_row evo ' id='evo_span_hidden_end' style='display:<?php echo $evo_span_hidd_display;?>'>
											<?php 	echo $ajde->wp_admin->html_yesnobtn(array('id'=>'evo_span_hidden_end', 'var'=>$evo_span_hidden_end));?>
											
											<input type='hidden' name='evo_span_hidden_end' value="<?php echo ($evo_span_hidden_end=='yes')?'yes':'no';?>"/>
											<label for='evo_span_hidden_end'><?php _e('Span the event until hidden end time','eventon')?><?php $ajde->wp_admin->tooltips( __('If event end time goes beyond start time +  and you want the event to show in the calendar until end time expire, select this.','eventon'),'',true);?></label>
										</p>

										<?php 
											// month long event
											$_evo_month_long = (!empty($ev_vals["_evo_month_long"]) )? $ev_vals["_evo_month_long"][0]:null;
											$_event_month = (!empty($ev_vals["_event_month"]) )? $ev_vals["_event_month"][0]:null;
											
										?>
										<p class='yesno_row evo ' id='_evo_month_long' >
											<?php 	echo $ajde->wp_admin->html_yesnobtn(array('id'=>'_evo_month_long', 'var'=>$_evo_month_long));?>
											
											<input type='hidden' name='_evo_month_long' value="<?php echo ($_evo_month_long=='yes')?'yes':'no';?>"/>					
											<label for='_evo_month_long'><?php _e('Show this event for the entire start event Month','eventon')?><?php $ajde->wp_admin->tooltips( __('This will show this event for the entire month that the event start date is set to.','eventon'),'',true);?></label>
										</p>
										<input id='evo_event_month' type='hidden' name='_event_month' value="<?php echo $_event_month;?>"/><p style='clear:both'></p>
										

										<?php 
											// Year long event
											$evo_year_long = (!empty($ev_vals["evo_year_long"]) )? $ev_vals["evo_year_long"][0]:null;
											$event_year = (!empty($ev_vals["event_year"]) )? $ev_vals["event_year"][0]:null;
											
										?>
										<p class='yesno_row evo ' id='evo_year_long' >
											<?php 	echo $ajde->wp_admin->html_yesnobtn(array('id'=>'evo_year_long', 'var'=>$evo_year_long));?>
											
											<input type='hidden' name='evo_year_long' value="<?php echo ($evo_year_long=='yes')?'yes':'no';?>"/>					
											<label for='evo_year_long'><?php _e('Show this event for the entire start event Year','eventon')?><?php $ajde->wp_admin->tooltips( __('This will show this event on every month of the year. The year will be based off the start date you choose above. If year long is set, month long will be overridden.','eventon'),'',true);?></label>
										</p>
										<input id='evo_event_year' type='hidden' name='event_year' value="<?php echo $event_year;?>"/><p style='clear:both'></p>

									</div>
									<div style='clear:both'></div>			
									<?php 
										// Recurring events 
										$evcal_repeat = (!empty($ev_vals["evcal_repeat"]) )? $ev_vals["evcal_repeat"][0]:null;
									?>
									<div id='evcal_rep' class='evd'>
										<div class='evcalr_1'>
											<p class='yesno_row evo '>
												<?php 	
												echo $ajde->wp_admin->html_yesnobtn(array(
													'id'=>'evd_repeat', 
													'var'=>$evcal_repeat,
													'attr'=>array(
														'afterstatement'=>'evo_editevent_repeatevents'
													)
												));
												?>						
												<input type='hidden' name='evcal_repeat' value="<?php echo ($evcal_repeat=='yes')?'yes':'no';?>"/>
												<label for='evcal_repeat'><?php _e('Repeating event', 'eventon')?></label>
											</p><p style='clear:both'></p>
										</div>
										<p class='eventon_ev_post_set_line'></p>
										<?php
											// initial values
											$display = (!empty($ev_vals["evcal_repeat"]) && $evcal_repeat=='yes')? '':'none';
											// repeat frequency array
											$repeat_freq= apply_filters('evo_repeat_intervals', array(
												'daily'=>__('days','eventon'),
												'weekly'=>__('weeks','eventon'),
												'monthly'=>__('months','eventon'),
												'yearly'=>__('years','eventon'),
												'custom'=>__('custom','eventon')) 
											);
											$evcal_rep_gap = (!empty($ev_vals['evcal_rep_gap']) )?$ev_vals['evcal_rep_gap'][0]:1;
											$freq = (!empty($ev_vals["evcal_rep_freq"]) )?
													 ($repeat_freq[ $ev_vals["evcal_rep_freq"][0] ]): null;
										?>
										<div id='evo_editevent_repeatevents' class='evcalr_2 evo_repeat_options' style='display:<?php echo $display ?>'>
											
											<!-- REPEAT SERIES -->
											<div class='repeat_series'>
												<p class='yesno_row evo '>
													<?php 	
													$_evcal_rep_series = evo_meta($ev_vals, '_evcal_rep_series');
													$display = evo_meta_yesno($ev_vals, '_evcal_rep_series','yes','','none');

													echo $ajde->wp_admin->html_yesnobtn(array(
														'id'=>'evo_repeat', 
														'var'=>$_evcal_rep_series,	
														'afterstatement'=>'_evcal_rep_series_clickable'
													));
													?>						
													<input type='hidden' name='_evcal_rep_series' value="<?php echo ($_evcal_rep_series=='yes')?'yes':'no';?>"/>
													<label for='_evcal_rep_series'><?php _e('Show other future repeating instances of this event on event card', 'eventon')?></label>
												</p><p style='clear:both'></p>
												<div id='_evcal_rep_series_clickable' style='display:<?php echo $display ?>'>
													<p class='yesno_row evo '>
														<?php 	
														$_evcal_rep_endt = evo_meta($ev_vals, '_evcal_rep_endt');
														
														echo $ajde->wp_admin->html_yesnobtn(array(
															'id'=>'_evcal_rep_endt', 
															'var'=>$_evcal_rep_endt,	
														));
														?>						
														<input type='hidden' name='_evcal_rep_endt' value="<?php echo ($_evcal_rep_endt=='yes')?'yes':'no';?>"/>
														<label for='_evcal_rep_endt'><?php _e('Show end time of repeating instances as well on eventcard', 'eventon')?></label>
													</p><p style='clear:both'></p>
													<p class='yesno_row evo '>
														<?php 	
														$_evcal_rep_series_clickable = evo_meta($ev_vals, '_evcal_rep_series_clickable');

														echo $ajde->wp_admin->html_yesnobtn(array(
															'id'=>'evo_repeat', 
															'var'=>$_evcal_rep_series_clickable,	
														));
														?>						
														<input type='hidden' name='_evcal_rep_series_clickable' value="<?php echo ($_evcal_rep_series_clickable=='yes')?'yes':'no';?>"/>
														<label for='_evcal_rep_series_clickable'><?php _e('Allow repeat dates to be clickable', 'eventon')?></label>
													</p><p style='clear:both'></p>
												</div>
											</div>

											<p class='repeat_type evcalr_2_freq evcalr_2_p'><span class='evo_form_label'><?php _e('Event Repeat Type','eventon');?>:</span> <select id='evcal_rep_freq' name='evcal_rep_freq'>
											<?php
												$evcal_rep_freq = (!empty($ev_vals['evcal_rep_freq']))?$ev_vals['evcal_rep_freq'][0]:null;
												foreach($repeat_freq as $refv=>$ref){
													echo "<option field='".$ref."' value='".$refv."' ".(($evcal_rep_freq==$refv)?'selected="selected"':'').">".$refv."</option>";
												}						
											?></select></p><!--.repeat_type-->
											
											<div class='evo_preset_repeat_settings' style='display:<?php echo (!empty($ev_vals['evcal_rep_freq']) && $ev_vals['evcal_rep_freq'][0]=='custom')? 'none':'block';?>'>		
												<p class='gap evcalr_2_rep evcalr_2_p'><span class='evo_form_label'><?php _e('Gap between repeats','eventon');?>:</span>
												<input type='number' name='evcal_rep_gap' min='1' max='100' value='<?php echo $evcal_rep_gap;?>' placeholder='1'/>	 <span id='evcal_re'><?php echo $freq;?></span></p>
											<?php
												// repeat number
													$evcal_rep_num = (!empty($ev_vals['evcal_rep_num']) )?  $ev_vals['evcal_rep_num'][0]:1;
											?>
												<p class='evcalr_2_numr evcalr_2_p'><span class='evo_form_label'><?php _e('Number of repeats','eventon');?>:</span>
													<input type='number' name='evcal_rep_num' min='1' value='<?php echo $evcal_rep_num;?>' placeholder='1'/>						
												</p>
											
											<?php 
												// Weekly view only 
												$__display_weekly_mode = (!empty($ev_vals['evcal_rep_freq']) && $ev_vals['evcal_rep_freq'][0] =='weekly')? 'block':'none';
												$evp_repeat_rb_wk = evo_meta($ev_vals, 'evp_repeat_rb_wk');
												$evo_rep_WKwk = (!empty($ev_vals['evo_rep_WKwk']) )? unserialize($ev_vals['evo_rep_WKwk'][0]): array();
											?>
												<div class='repeat_weekly_only repeat_section_extra' style='display:<?php echo $__display_weekly_mode;?>'>
													<p class='repeat_by evcalr_2_p evo_rep_week' >
														<span class='evo_form_label'><?php _e('Repeat Mode','eventon');?>:</span>
														<select id='evo_rep_by_wk' class='repeat_mode_selection' name='evp_repeat_rb_wk'>
															<option value='sing' <?php echo ('sing'==$evp_repeat_rb_wk)? 'selected="selected"':null;?>><?php _e('Single Day','eventon');?></option>
															<option value='dow' <?php echo ('dow'==$evp_repeat_rb_wk)? 'selected="selected"':null;?>><?php _e('Days of the week','eventon');?></option>
														</select>
													</p>
													<p class='evo_days_list evo_rep_week_dow repeat_modes'  style='display:<?php echo ($evp_repeat_rb_wk=='dow'?'block':'none');?>'>
														<span class='evo_form_label'><?php _e('Repeat on selected days','eventon');?>: </span>
														<?php
															$days = array('S','M','T','W','T','F','S');
															for($x=0; $x<7; $x++){
																echo "<em><input type='checkbox' name='evo_rep_WKwk[]' value='{$x}' ". ((in_array($x, $evo_rep_WKwk))? 'checked="checked"':null)."><label>".$days[$x]."</label></em>";
															}
														?>
													</p>
												</div>
											<?php 
												// monthly only 
												$__display_none_1 =  (!empty($ev_vals['evcal_rep_freq']) && $ev_vals['evcal_rep_freq'][0] =='monthly')? 'block': 'none';
												$__display_none_2 =  ($__display_none_1=='block' && !empty($ev_vals['evp_repeat_rb']) && $ev_vals['evp_repeat_rb'][0] =='dow')? 'block': 'none';

												// repeat by
													$evp_repeat_rb = (!empty($ev_vals['evp_repeat_rb']) )? $ev_vals['evp_repeat_rb'][0]: null;	
													$evo_rep_WK = (!empty($ev_vals['evo_rep_WK']) )? unserialize($ev_vals['evo_rep_WK'][0]): array();
													$evo_repeat_wom = (!empty($ev_vals['evo_repeat_wom']) )? $ev_vals['evo_repeat_wom'][0]: null;
											?>
												<div class='repeat_monthly_only repeat_section_extra'>
													<p class='repeat_by evcalr_2_p evo_rep_month' style='display:<?php echo $__display_none_1;?>'>
														<span class='evo_form_label'><?php _e('Repeat by','eventon');?>:</span>
														<select id='evo_rep_by' class='repeat_mode_selection' name='evp_repeat_rb'>
															<option value='dom' <?php echo ('dom'==$evp_repeat_rb)? 'selected="selected"':null;?>><?php _e('Day of the month','eventon');?></option>
															<option value='dow' <?php echo ('dow'==$evp_repeat_rb)? 'selected="selected"':null;?>><?php _e('Days of the week','eventon');?></option>
														</select>
													</p>
													<div class='repeat_modes repeat_monthly_modes' style='display:<?php echo $__display_none_2;?>'>
														<p class='evo_days_list evo_rep_month_2 evo_rep_month_dow'  >
															<span class='evo_form_label'><?php _e('Repeat on selected days','eventon');?>: </span>
															<?php
																$days = array('S','M','T','W','T','F','S');
																for($x=0; $x<7; $x++){
																	echo "<em><input type='checkbox' name='evo_rep_WK[]' value='{$x}' ". ((in_array($x, $evo_rep_WK))? 'checked="checked"':null)."><label>".$days[$x]."</label></em>";
																}
															?>
														</p>
														<p class='evcalr_2_p evo_rep_month_2'>
															<span class='evo_form_label'><?php _e('Week of month to repeat','eventon');?>: </span>
															<select id='evo_wom' name='evo_repeat_wom'>
																<?php

																// week of the month
																	foreach( array(
																		'1'=>__('First','eventon'),
																		'2'=>__('Second','eventon'),
																		'3'=>__('Third','eventon'),
																		'4'=>__('Fourth','eventon'),
																		'5'=>__('Fifth','eventon'),
																		'-1'=>__('Last','eventon'),
																	) as $key=>$value){
																		echo "<option value='{$key}' ".(($evo_repeat_wom == $key)? 'selected="selected"':null).">".$value."</option>";
																	}
																
																?>
															</select>
														</p>
													</div>
												</div>									
												
											</div><!--evo_preset_repeat_settings-->
											
											<!-- Custom repeat -->
											<div class='repeat_information' style='display:<?php echo (!empty($ev_vals['evcal_rep_freq']) && $ev_vals['evcal_rep_freq'][0]=='custom')? 'block':'none';?>'>
												<p><?php _e('CUSTOM REPEAT TIMES','eventon');?><br/><i style='opacity:0.7'><?php _e('NOTE: Below repeat intervals are in addition to the above main event time.','eventon');?></i></p>										
												<?php

													// Important messages about repeats
													$important_msg_for_repeats = apply_filters('evo_repeats_admin_notice','', $ev_vals);
													if($important_msg_for_repeats)	echo "<p><i style='opacity:0.7'>".$important_msg_for_repeats."</i></p>";


													//print_r(unserialize($ev_vals['aaa'][0]));					
													date_default_timezone_set('UTC');	

													echo "<p id='no_repeats' style='display:none;opacity:0.7'>There are no additional custom repeats!</p>";

													echo "<ul class='evo_custom_repeat_list'>";
													$count =0;
													if(!empty($ev_vals['repeat_intervals'])){								
														$repeat_times = (unserialize($ev_vals['repeat_intervals'][0]));
														//print_r($repeat_times);

														// datre format sting to display for repeats
														$date_format_string = $evcal_date_format[1].' '.( $evcal_date_format[2]? 'G:i':'h:ia');
														
														foreach($repeat_times as $rt){
															$startUNIX = (int)$rt[0];
															$endUNIX = (int)$rt[1];
															echo '<li data-cnt="'.$count.'" style="display:'.(( $count>3)?'none':'block').'" class="'.($count==0?'initial':'').($count>3?' over':'').'">'. ($count==0? '<dd>'.__('Initial','eventon').'</dd>':'').'<span>'.__('from','eventon').'</span> '.date($date_format_string,$startUNIX).' <span class="e">End</span> '.date($date_format_string,$endUNIX).'<em alt="Delete">x</em>
															<input type="hidden" name="repeat_intervals['.$count.'][0]" value="'.$startUNIX.'"/><input type="hidden" name="repeat_intervals['.$count.'][1]" value="'.$endUNIX.'"/></li>';
															$count++;
														}								
													}
													echo "</ul>";
													echo ( !empty($ev_vals['repeat_intervals']))? 
														"<p class='evo_custom_repeat_list_count' data-cnt='{$count}' style='padding-bottom:20px'>There are ".($count-1)." repeat intervals. ". ($count>3? "<span class='evo_repeat_interval_view_all' data-show='no'>".__('View All','eventon')."</span>":'') ."</p>"
														:null;
												?>
												<div class='evo_repeat_interval_new' style='display:none'>
													<p><span><?php _e('FROM','eventon');?>:</span><input class='ristD' name='repeat_date'/> <input class='ristT' name='repeat_time'/><br/><span><?php _e('TO','eventon');?>:</span><input class='rietD' name='repeat_date'/> <input class='rietT' name='repeat_time'/></p>
												</div>
												<p class='evo_repeat_interval_button'><a id='evo_add_repeat_interval' class='button_evo'>+ <?php _e('Add New Repeat Interval','eventon');?></a><span></span></p>
											</div>	
										</div>
									</div>	
								<?php
							break;

							case 'ev_subtitle':
								echo "<div class='evcal_data_block_style1'>
									<div class='evcal_db_data'>
										<input type='text' id='evcal_subtitle' name='evcal_subtitle' value=\"".evo_meta($ev_vals, 'evcal_subtitle', true)."\" style='width:100%'/>
									</div>
								</div>";
							break;
						}

						// for custom meta field for evnet
						if(!empty($mBOX['fieldtype']) && $mBOX['fieldtype']=='custommetafield'){

							$x = $mBOX['x'];

							echo "<div class='evcal_data_block_style1'>
									<div class='evcal_db_data'>";

								// FIELD
								$__saved_field_value = (!empty($ev_vals["_evcal_ec_f".$x."a1_cus"]) )? $ev_vals["_evcal_ec_f".$x."a1_cus"][0]:null ;
								$__field_id = '_evcal_ec_f'.$x.'a1_cus';

								// wysiwyg editor
								if(!empty($evcal_opt1['evcal_ec_f'.$x.'a2']) && 
									$evcal_opt1['evcal_ec_f'.$x.'a2']=='textarea'){
									
									wp_editor($__saved_field_value, $__field_id);
									
								// button
								}elseif(!empty($evcal_opt1['evcal_ec_f'.$x.'a2']) && 
									$evcal_opt1['evcal_ec_f'.$x.'a2']=='button'){
									
									$__saved_field_link = (!empty($ev_vals["_evcal_ec_f".$x."a1_cusL"]) )? $ev_vals["_evcal_ec_f".$x."a1_cusL"][0]:null ;

									echo "<input type='text' id='".$__field_id."' name='_evcal_ec_f".$x."a1_cus' ";
									echo 'value="'. $__saved_field_value.'"';						
									echo "style='width:100%' placeholder='Button Text' title='Button Text'/>";

									echo "<input type='text' id='".$__field_id."' name='_evcal_ec_f".$x."a1_cusL' ";
									echo 'value="'. $__saved_field_link.'"';						
									echo "style='width:100%' placeholder='Button Link' title='Button Link'/>";

										$onw = (!empty($ev_vals["_evcal_ec_f".$x."_onw"]) )? $ev_vals["_evcal_ec_f".$x."_onw"][0]:null ;
									?>

									<span class='yesno_row evo'>
										<?php 	
										$openInNewWindow = (!empty($ev_vals['_evcal_ec_f'.$x . '_onw']))? $ev_vals['_evcal_ec_f'.$x . '_onw'][0]: null;

										echo $ajde->wp_admin->html_yesnobtn(array(
											'id'=>'_evcal_ec_f'.$x . '_onw',
											'var'=>$openInNewWindow,
											'input'=>true,
											'label'=>__('Open in New window','eventon')
										));?>											
									</span>
									<?php
								
								// text	
								}else{
									echo "<input type='text' id='".$__field_id."' name='_evcal_ec_f".$x."a1_cus' ";										
									echo 'value="'. $__saved_field_value.'"';						
									echo "style='width:100%'/>";								
								}

							echo "</div></div>";
						}
					}
					?>					
					</div>
				</div>
			<?php	endforeach;	?>
					<div class='evomb_section' id='<?php echo $mBOX['id'];?>'>			
						<div class='evomb_header'>
							<span class="evomb_icon evII"><i class="fa fa-plug"></i></span>
							<p><?php _e('Additional Functionality','eventon');?></p>
						</div>
						<p style='padding:15px 25px; margin:0' class="evomb_body_additional"><?php echo sprintf(__('Looking for additional functionality including event tickets, frontend event submissions, RSVP to events, photo gallery and more? Check out <a href="%s" target="_blank">eventON addons</a>','eventon'), 'http://www.myeventon.com/addons/');?>.</p>
					</div>	
				<div class='evMB_end'></div>
			</div>
		<?php  
			global $ajde;
			
			// Lightbox
			echo $ajde->wp_admin->lightbox_content(array(
				'class'=>'evo_term_lightbox', 
				'content'=>"<p class='evo_lightbox_loading'></p>",
				'title'=>__('Event Data','eventon'),
				'width'=>'500'
				)
			);
		}

	// THIRD PARTY event related settings 
		function ajde_evcal_show_box_3(){	
			
			global $eventon, $ajde;
			
			$evcal_opt1= get_option('evcal_options_evcal_1');
				$evcal_opt2= get_option('evcal_options_evcal_2');
				
				// Use nonce for verification
				wp_nonce_field( plugin_basename( __FILE__ ), 'evo_noncename' );
				
				// The actual fields for data entry
				$p_id = get_the_ID();
				$ev_vals = get_post_custom($p_id);
			
			?>
			<table id="meta_tb" class="form-table meta_tb evoThirdparty_meta" >
				<?php
					// (---) hook for addons
					if(has_action('eventon_post_settings_metabox_table'))
						do_action('eventon_post_settings_metabox_table');
				
					if(has_action('eventon_post_time_settings'))
						do_action('eventon_post_time_settings');

				// PAYPAL
					if($evcal_opt1['evcal_paypal_pay']=='yes'):
					?>
					<tr>
						<td colspan='2' class='evo_thirdparty_table_td'>
							<div class='evo3rdp_header'>
								<span class='evo3rdp_icon'><i class='fa fa-paypal'></i></span>
								<p><?php _e('Paypal "BUY NOW" button','eventon');?></p>
							</div>	
							<div class='evo_3rdp_inside'>
								<p class='evo_thirdparty'>
									<label for='evcal_paypal_text'><?php _e('Text to show above buy now button','eventon')?></label><br/>			
									<input type='text' id='evcal_paypal_text' name='evcal_paypal_text' value='<?php echo (!empty($ev_vals["evcal_paypal_text"]) )? $ev_vals["evcal_paypal_text"][0]:null?>' style='width:100%'/>
								</p>
								<p class='evo_thirdparty'><label for='evcal_paypal_item_price'><?php _e('Enter the price for paypal buy now button <i>eg. 23.99</i> (WITHOUT currency symbol)')?><?php $ajde->wp_admin->tooltips(__('Type the price without currency symbol to create a buy now button for this event. This will show on front-end calendar for this event','eventon'),'',true);?></label><br/>			
									<input placeholder='eg. 29.99' type='text' id='evcal_paypal_item_price' name='evcal_paypal_item_price' value='<?php echo (!empty($ev_vals["evcal_paypal_item_price"]) )? $ev_vals["evcal_paypal_item_price"][0]:null?>' style='width:100%'/>
								</p>
								<p class='evo_thirdparty'>
									<label for='evcal_paypal_email'><?php _e('Custom Email address to receive payments','eventon')?><?php $ajde->wp_admin->tooltips('This email address will override the email saved under eventON settings for paypal to accept payments to this email instead of paypal email saved in eventon settings.','',true);?></label><br/>			
									<input type='text' id='evcal_paypal_email' name='evcal_paypal_email' value='<?php echo (!empty($ev_vals["evcal_paypal_email"]) )? $ev_vals["evcal_paypal_email"][0]:null?>' style='width:100%'/>
								</p>
							</div>		
						</td>			
					</tr>
					<?php endif; ?>
				</table>
			<?php
		}
		
	// Save the Event data meta box
		function eventon_save_meta_data($post_id, $post){
			if($post->post_type!='ajde_events')
				return;
				
			// Stop WP from clearing custom fields on autosave
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
				return;

			// Prevent quick edit from clearing custom fields
			if (defined('DOING_AJAX') && DOING_AJAX)
				return;

			
			// verify this came from the our screen and with proper authorization,
			// because save_post can be triggered at other times
			if( isset($_POST['evo_noncename']) ){
				if ( !wp_verify_nonce( $_POST['evo_noncename'], plugin_basename( __FILE__ ) ) ){
					return;
				}
			}
			// Check permissions
			if ( !current_user_can( 'edit_post', $post_id ) )
				return;	

			global $pagenow;
			$_allowed = array( 'post-new.php', 'post.php' );
			if(!in_array($pagenow, $_allowed)) return;
						
			// $_POST FIELDS array
				$fields_ar =apply_filters('eventon_event_metafields', array(
					'evcal_allday','evcal_event_color','evcal_event_color_n',
					'evcal_exlink','evcal_lmlink','evcal_subtitle',
					'evcal_hide_locname','evcal_gmap_gen','evcal_name_over_img', 'evo_access_control_location',
					'evcal_mu_id','evcal_paypal_item_price','evcal_paypal_text','evcal_paypal_email',
					'evcal_repeat','_evcal_rep_series','_evcal_rep_endt','_evcal_rep_series_clickable','evcal_rep_freq','evcal_rep_gap','evcal_rep_num',
					'evp_repeat_rb','evo_repeat_wom','evo_rep_WK','evp_repeat_rb_wk','evo_rep_WKwk',
					'evcal_lmlink_target','_evcal_exlink_target','_evcal_exlink_option',
					'evo_hide_endtime','evo_span_hidden_end','evo_year_long','event_year','_evo_month_long','_event_month',
					'evo_evcrd_field_org','evo_event_timezone',

					'_evo_lang',
					'evo_exclude_ev',
					'_featured',
					'_completed',
					'_cancel','_cancel_reason',
					'_onlyloggedin',					
					//'evcal_lat','evcal_lon',
				), $post_id);

			// append custom fields based on activated number
				$evcal_opt1= get_option('evcal_options_evcal_1');
				$num = evo_calculate_cmd_count($evcal_opt1);
				for($x =1; $x<=$num; $x++){	
					if(eventon_is_custom_meta_field_good($x)){
						$fields_ar[]= '_evcal_ec_f'.$x.'a1_cus';
						$fields_ar[]= '_evcal_ec_f'.$x.'a1_cusL';
						$fields_ar[]= '_evcal_ec_f'.$x.'_onw';
					}
				}

			// array of post meta fields that should be deleted from event post meta
				$deleted_fields = array(
					'evo_location_tax_id','evo_organizer_tax_id'
				);

			$proper_time = 	evoadmin_get_unix_time_fromt_post($post_id);

			// if Repeating event save repeating intervals
				if( eventon_is_good_repeat_data() && !empty($proper_time['unix_start']) ){

					$unix_E = (!empty($proper_time['unix_end']))? $proper_time['unix_end']: $proper_time['unix_start'];
					$repeat_intervals = eventon_get_repeat_intervals($proper_time['unix_start'], $unix_E);

					// save repeat interval array as post meta
					if ( !empty($repeat_intervals) ){
						asort($repeat_intervals);
						update_post_meta( $post_id, 'repeat_intervals', $repeat_intervals);
					}else{
						delete_post_meta( $post_id, 'repeat_intervals');
					}
				}

				//update_post_meta($post_id, 'aaa', $_POST['repeat_intervals']);

			// run through all the custom meta fields
				foreach($fields_ar as $f_val){

					// delete none used post meta values
					if(in_array($f_val, $deleted_fields)){
						delete_post_meta($post_id, $f_val);
					}
					
					if(!empty($_POST[$f_val])){

						$post_value = ( $_POST[$f_val]);
						update_post_meta( $post_id, $f_val,$post_value);

						// ux val for single events linking to event page	
						if($f_val=='evcal_exlink' && $_POST['_evcal_exlink_option']=='4'){
							update_post_meta( $post_id, 'evcal_exlink',get_permalink($post_id) );
						}

					}else{
						//if(defined('DOING_AUTOSAVE') && !DOING_AUTOSAVE){
						
						delete_post_meta($post_id, $f_val);
					}
					
				}

				
			
			// Other data	
				// full time converted to unix time stamp
					if ( !empty($proper_time['unix_start']) )
						update_post_meta( $post_id, 'evcal_srow', $proper_time['unix_start']);
					
					if ( !empty($proper_time['unix_end']) )
						update_post_meta( $post_id, 'evcal_erow', $proper_time['unix_end']);

				// save event year if not set
					if( (empty($_POST['event_year']) && !empty($proper_time['unix_start'])) || 
						(!empty($_POST['event_year']) &&
							$_POST['event_year']=='yes')
					){
						$year = date('Y', $proper_time['unix_start']);
						update_post_meta( $post_id, 'event_year', $year);
					}

				// save event month if not set
					if( (empty($_POST['_event_month']) && !empty($proper_time['unix_start'])) || 
						(!empty($_POST['_event_month']) &&
							$_POST['_event_month']=='yes')
					){
						$month = date('n', $proper_time['unix_start']);
						update_post_meta( $post_id, '_event_month', $month);
					}
						
				//set event color code to 1 for none select colors
					if ( !isset( $_POST['evcal_event_color_n'] ) )
						update_post_meta( $post_id, 'evcal_event_color_n',1);
									
				// save featured event data default value no
					$_featured = get_post_meta($post_id, '_featured',true);
					if(empty( $_featured) )
						update_post_meta( $post_id, '_featured','no');
			
			// save location and organizer taxonomy data for the event
			// deprecated since 2.5.3
				//evoadmin_save_event_tax_termmeta($post_id);
						
			// (---) hook for addons
			do_action('eventon_save_meta', $fields_ar, $post_id);

			// save user closed meta field boxes
			if(!empty($_POST['evo_collapse_meta_boxes']))
				eventon_save_collapse_metaboxes($post_id, $_POST['evo_collapse_meta_boxes'],true );
				
		}

	// GET event taxonomy form for new and edit term
	// request by AJAX
	// $_POST is present
		function get_tax_form(){
			global $ajde;

			$is_new = (isset($_POST['type']) && $_POST['type']=='new')? true: false;

			// definitions
				$termMeta = $event_tax_term = false;

			// if edit
			if(!$is_new){
				$event_tax_term = wp_get_post_terms($_POST['eventid'], $_POST['tax']);
				if ( $event_tax_term && ! is_wp_error( $event_tax_term ) ){	
					$event_tax_term = $event_tax_term[0];
				}
				$termMeta = evo_get_term_meta($_POST['tax'],$_POST['termid'], '', true);
			}

			ob_start();

			echo "<div class='evo_tax_entry' data-eventid='{$_POST['eventid']}' data-tax='{$_POST['tax']}' data-type='{$_POST['type']}'>";

			
			// pass term id if editing
				if($event_tax_term && !$is_new):?>
					<p><input class='field' type='hidden' name='termid' value="<?php echo $_POST['termid'];?>" /></p>
				<?php endif;

			// for each fields
			$fields = EVO()->taxonomies->get_event_tax_fields_array($_POST['tax'], $event_tax_term);
			foreach( $fields as $key=>$value){
				$field_value = '';

				if(empty($value['value'])){
					if(!empty($value['var']) && !empty( $termMeta[$value['var']] )){
						if( !is_array($termMeta[$value['var']]) && !is_object($termMeta[$value['var']])){
							$field_value = stripslashes(str_replace('"', "'", (esc_attr( $termMeta[$value['var']] )) ));
						}						
					}

				}else{
					$field_value = $value['value'];
				}

				switch ($value['type']) {
					case 'text':
						?>
						<p>	
							<label for='<?php echo $key;?>'><?php echo $value['name']?></label>
							<input id='<?php echo $key;?>' class='field' type='text' name='<?php echo $value['var'];?>' value="<?php echo $field_value?>" style='width:100%' placeholder='<?php echo !empty($value['placeholder'])? $value['placeholder']:'';?>'/>
							<?php if(!empty($value['legend'])):?>
								<em class='evo_legend'><?php echo $value['legend']?></em>
							<?php endif;?>
						</p>
						<?php
					break;
					case 'textarea':
						?>
						<p>	
							<label for='<?php echo $key;?>'><?php echo $value['name']?></label>	
							<textarea id='<?php echo $key;?>' class='field' type='text' name='<?php echo $value['var'];?>' style='width:100%'><?php echo $field_value?></textarea>						
							
							<?php if(!empty($value['legend'])):?>
								<em class='evo_legend'><?php echo $value['legend']?></em>
							<?php endif;?>
						</p>
						<?php
					break;
					case 'image':
						$image_id = $termMeta? $field_value: false;

						// image soruce array
						$img_src = ($image_id)? 	wp_get_attachment_image_src($image_id,'medium'): null;
							$img_src = (!empty($img_src))? $img_src[0]: null;

						$__button_text = ($image_id)? __('Remove Image','eventon'): __('Choose Image','eventon');
						$__button_text_not = ($image_id)? __('Remove Image','eventon'): __('Choose Image','eventon');
						$__button_class = ($image_id)? 'removeimg':'chooseimg';
						?>
						<p class='evo_metafield_image'>
							<label><?php echo $value['name']?></label>
							<input class='field <?php echo $key;?> custom_upload_image evo_meta_img' name="<?php echo $key;?>" type="hidden" value="<?php echo ($image_id)? $image_id: null;?>" /> 
                    		<input class="custom_upload_image_button button <?php echo $__button_class;?>" data-txt='<?php echo $__button_text_not;?>' type="button" value="<?php echo $__button_text;?>" /><br/>
                    		<span class='evo_loc_image_src image_src'>
                    			<img src='<?php echo $img_src;?>' style='<?php echo !empty($image_id)?'':'display:none';?>'/>
                    		</span>
                    		
                    	</p>
						<?php
					break;
					case 'yesno':
						?>
						<p>
							<span class='yesno_row evo'>
								<?php 	
								echo $ajde->wp_admin->html_yesnobtn(array(
									'id'=>$key, 
									'var'=>$field_value,
									'input'=>true,
									'inputAttr'=>array('class'=>'field'),
									'label'=>$value['name']
								));?>											
							</span>
						</p>
						<?php
					break;
					case 'button':
						?>
						<p style='text-align:center; padding-top:10px'><span class='evo_btn evo_term_submit'><?php echo $is_new? __('Add New','eventon'): __('Save Changes','eventon');?></span></p>
						<?php
					break;
				}
			}

			echo "</div>";

			return ob_get_clean();
		}


	// Supporting functions
		function termmeta($term_meta, $var){
			if(!empty( $term_meta[$var] )){
				if( is_array($term_meta[$var]) || is_object($term_meta[$var])) return null;
				return stripslashes(str_replace('"', "'", (esc_attr( $term_meta[$var] )) ));
				
			}else{
				return null;
			}
		}
}
