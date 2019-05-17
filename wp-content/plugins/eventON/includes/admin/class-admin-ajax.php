<?php
/**
 * Function ajax for backend
 * @version   2.5.1
 */
class EVO_admin_ajax{
	public function __construct(){
		$ajax_events = array(
			'deactivate_product'	=>'deactivate_product',	
			'validate_license'		=>'validate_license',					
			'revalidate_license'	=>'revalidate_license',					
			'export_events'			=>'export_events',			
			'get_addons_list'		=>'get_addons_list',
			'export_settings'		=>'export_settings',
			'import_settings'		=>'import_settings',
			'get_event_tax_term_section'=>'get_event_tax_term_section',
			'event_tax_list'		=>'event_tax_list',
			'event_tax_save_changes'=>'event_tax_save_changes',
			'event_tax_remove'		=>'event_tax_remove',
			'eventpost_update_meta'	=>'evo_eventpost_update_meta',
			'admin_test_email'		=>'admin_test_email',
			'admin_get_views'		=>'admin_get_views',
			'activate_subscription'		=>'activate_subscription',
			'deactivate_subscription'	=>'deactivate_subscription',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {

			$prepend = 'eventon_';
			add_action( 'wp_ajax_'. $prepend . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'. $prepend . $ajax_event, array( $this, $class ) );
		}

		add_action('wp_ajax_eventon-feature-event', array($this, 'eventon_feature_event'));
	}

	// get HTML views
		function admin_get_views(){
			if(!isset($_POST['type'])){
				echo 'failed'; exit;
			} 

			$type = $_POST['type'];
			$data = isset($_POST['data'])? $_POST['data']: array();

			$views = new EVO_Views();

			echo json_encode(array(
				'status'=>'good','html'=>$views->get_html($type, $data)
			)); exit;
		}

	// subscription
		function activate_subscription(){
			if(!isset($_POST['subscription_key']) && !isset($_POST['myeventon_username'])){
				echo json_encode(array(
					'status'=>	'bad',
					'msg'=>	__('Required Information Missing!','eventon')
				)); exit;
			}

			$SUB = new EVO_Product_Lic('evo_subscription');
			$SUB->save_subscription($_POST['subscription_key'], $_POST['myeventon_username']);

			$verify = $SUB->verify_active_subscription();

			if( $verify['status'] == 'bad'){
				$msg = EVO_Error()->error_code( 150 );
				$result_error_code = isset($verify['error_code'])? $verify['error_code']: false;
				if($result_error_code && !in_array($result_error_code, array(21,23,30))){

					$msg = EVO_Error()->error_code( $verify['error_code'] );
				}				

				echo json_encode(array(
					'status'=>	'bad',
					'msg'=>	$msg,
					'error_remote_var'=> ( isset($verify['error_remote_var']) ? $verify['error_remote_var']: ''),
					'error_code'=>$verify['error_code']
				)); exit;
			}else{
				$views = new EVO_Views();
				$html =  $views->get_html('evo_subsription_view');
				echo json_encode(array(					
					'status'=>	'good',
					'msg'=>	EVO_Error()->error_code( 155 ),
					'html'=> $html,
				)); exit;
			}
		}
		function deactivate_subscription(){
			$SUB = new EVO_Product_Lic('evo_subscription');
			$views = new EVO_Views();
			$status = 'bad'; $msg = '';
			$SUB->deactivate(); // deactivate locally
			
			if( !$SUB->get_prop('key')){
				$msg = EVO_Error()->error_code( 160 );				
			}else{
				$JSON = $SUB->remote_deactivate_subscription();
				$returned_error_code = isset($JSON['error_code'])? (int)$JSON['error_code']:false;

				$msg = ($returned_error_code && $returned_error_code == 162 ) ? 
					EVO_Error()->error_code( 162 ): EVO_Error()->error_code( 161 );
			}

			$return_content = array(
				'status'=> $status,
				'msg'=> $msg,
				'html'=> $views->get_html('evo_subsription_view'),	
				'url'=>	(isset($JSON['url'])? $JSON['url']: '')		
			);
			echo json_encode($return_content);		
			exit;
		}

	// update event post meta
		function evo_eventpost_update_meta(){
			if(isset($_POST['eid']) && isset($_POST['values']) ){
			
				$post = array();
				foreach($_POST['values'] as $key=>$val){
					update_post_meta($_POST['eid'], $key, $val);

					do_action('eventon_saved_event_metadata', $_POST['eid'], $key, $val);
				}
				echo json_encode(array(
					'status'=>	'good',
					'msg'=>	__('Successfully saved event meta data!','eventon')
				)); exit;
			}else{
				echo 'Event ID not available!'; exit;
			}
		}

	// get event singular tax term form or list
		function get_event_tax_term_section(){
			
			echo json_encode(array(
				'status'=>'good',
				'content'=> EVO()->evo_admin->metaboxes->get_tax_form()
			)); exit;
		}

		// tax term list
		function event_tax_list(){
			$terms = get_terms(
				$_POST['tax'],
				array(
					'orderby'           => 'name', 
				    'order'             => 'ASC',
				    'hide_empty'=>false
				) 
			);

			ob_start();
			echo "<div class='evo_tax_entry' data-eventid='{$_POST['eventid']}' data-tax='{$_POST['tax']}' data-type='list'>";

			if(count($terms)>0){
				
				?><select class='field' name='event_tax_termid'><?php

				if(empty($_POST['termid'])){
					?><option value=""><?php _e('Select from the list','eventon');?></option><?php
				}

				foreach ( $terms as $term ) {
					$selected = (!empty($_POST['termid']) && $term->term_id == $_POST['termid'])? 'selected="selected"':'';
					?><option <?php echo $selected;?> value="<?php echo $term->term_id;?>"><?php echo $term->name;?></option><?php
				}
				?></select>
				<p style='text-align:center; padding-top:10px;'><span class='evo_btn evo_term_submit'><?php _e('Save Changes','eventon');?></span></p>
				<?php
			}else{
				?><p><?php _e('You do not have any items saved! Please add new!','eventon');?></p><?php
			}

			echo "</div>";

			echo json_encode(array(
				'status'=>'good',
				'content'=>ob_get_clean()
			)); exit;
		}

		// save changes
		function event_tax_save_changes(){
			$status = 'bad';
			$content = '';
			$tax = $_POST['tax'];

			switch($_POST['type']){
			case 'list':
				if(!empty($_POST['event_tax_termid'])){
					wp_set_object_terms( $_POST['eventid'], (int)$_POST['event_tax_termid'], $tax , false);
					$status = 'good';
					$content = __('Changes successfully saved!','eventon');	
				}else{
					$content = __('Term ID was not passed!','eventon');	
				}
			break;
			case 'new':
			case 'edit':
				
				$term_name = esc_attr(stripslashes($_POST[ 'term_name' ]));
				$term = term_exists( $term_name, $tax );
				if($term !== 0 && $term !== null){
					$taxtermID = (int)$term['term_id'];
					wp_set_object_terms( $_POST['eventid'], $taxtermID, $tax );
				}else{
					// create slug from term name
						$trans = array(" "=>'-', ","=>'');
						$term_slug= strtr($term_name, $trans);

					// create wp term
					$new_term_ = wp_insert_term( $term_name, $tax , array('slug'=>$term_slug) );

					if(!is_wp_error($new_term_)){
						$taxtermID = (int)$new_term_['term_id'];
					}	
				}

				$fields = EVO()->taxonomies->get_event_tax_fields_array($_POST['tax'],'');

				
				// if a term ID is present
				if($taxtermID){
					$term_meta = array();

					// save description
					$term_description = isset($_POST['description'])? sanitize_text_field($_POST['description']):'';
					$tt = wp_update_term($taxtermID, $tax, array( 'description'=>$term_description ));
					
					// lat and lon values saved in the form
						if(isset($_POST['location_lon'])) $term_meta['location_lon'] = str_replace('"', "'", $_POST['location_lon']); 
						if(isset($_POST['location_lat'])) $term_meta['location_lat'] = str_replace('"', "'", $_POST['location_lat']); 

					foreach($fields as $key=>$value){
						if(in_array($key, array('description', 'submit','term_name','evcal_lat','evcal_lon'))) continue;

						if(isset($_POST[$value['var']])){

							do_action('evo_tax_save_each_field',$value['var'], $_POST[$value['var']]);

							
							if($value['var']=='location_address'){
								if(isset($_POST['location_address']))
									$latlon = eventon_get_latlon_from_address($_POST['location_address']);

								// longitude
								$term_meta['location_lon'] = isset($term_meta['location_lon']) ? $term_meta['location_lon']:
									(!empty($latlon['lng'])? floatval($latlon['lng']): null);

								// latitude
								$term_meta['location_lat'] = isset($term_meta['location_lat']) ? $term_meta['location_lat']:
									(!empty($latlon['lat'])? floatval($latlon['lat']): null);

								$term_meta['location_address' ] = (isset($_POST[ 'location_address' ]))? $_POST[ 'location_address' ]:null;

								continue;
							}


							$term_meta[ $value['var'] ] = str_replace('"', "'", $_POST[$value['var']]); 

						}else{
							$term_meta[ $value['var'] ] = ''; 
						}
					}

					//print_r($term_meta);

					// save meta values
						evo_save_term_metas($tax, $taxtermID, $term_meta);
					// assign term to event & replace
						wp_set_object_terms( $_POST['eventid'], $taxtermID, $tax , false);	

					$status = 'good';
					$content = __('Changes successfully saved!','eventon');	
				}

			break;
			}

			echo json_encode(array(
				'status'=>$status,
				'content'=>$content,
				'htmldata'=> EVO()->evo_admin->metaboxes->event_edit_tax_section($tax , $_POST['eventid'] )
			)); exit;
		}
		// remove a taxonomy term
		function event_tax_remove(){
			$status = 'bad';
			$content = '';
			
			if(!empty($_POST['termid'])){
				wp_remove_object_terms( $_POST['eventid'], (int)$_POST['termid'], $_POST['tax'] , false);
				$status = 'good';
				$content = __('Changes successfully saved!','eventon');	
			}else{
				$content = __('Term ID was not passed!','eventon');	
			}

			echo json_encode(array(
				'status'=>$status,
				'content'=>$content,
				'htmldata'=> EVO()->evo_admin->metaboxes->event_edit_tax_section($_POST['tax'] , $_POST['eventid'] )
			)); exit;
		}

	// export eventon settings
		function export_settings(){
			// check if admin and loggedin
				if(!is_admin() && !is_user_logged_in()) die('User not loggedin!');

			// verify nonce
				if(!wp_verify_nonce($_REQUEST['nonce'], 'evo_export_settings')) die('Security Check Failed!');

			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=Evo_settings__".date("d-m-y").".json");
			
			$json = array();
			$evo_options = get_option('evcal_options_evcal_1');
			foreach($evo_options as $field=>$option){
				// skip fields
				if(in_array($field, array('option_page','action','_wpnonce','_wp_http_referer'))) continue;
				$json[$field] = $option;
			}

			echo json_encode($json);
			exit;
		}
	// import settings
		function import_settings(){
			$output = array('status'=>'','msg'=>'');
			// verify nonce
				$output['success'] =wp_create_nonce('eventon_admin_nonce');
				if(!wp_verify_nonce($_POST['nonce'], 'eventon_admin_nonce')) $output['msg'] = __('Security Check Failed!','eventon');

			// check if admin and loggedin
				if(!is_admin() && !is_user_logged_in()) $output['msg'] = __('User not loggedin!','eventon');

			$JSON_data = $_POST['jsondata'];

			// check if json array present
			if(!is_array($JSON_data))  $output['msg'] = __('Not correct json format!','eventon');

			// if all good
			if( empty($output['msg'])){
				update_option('evcal_options_evcal_1', $JSON_data);
				$output['success'] = 'good';
				$output['msg'] = 'Successfully updated settings!';
			}
			
			echo json_encode($output);
			exit;

		}

	// export events as CSV
	// @version 2.2.30
		function export_events(){

			// check if admin and loggedin
				if(!is_admin() && !is_user_logged_in()) die('User not loggedin!');

			// verify nonce
				if(!wp_verify_nonce($_REQUEST['nonce'], 'eventon_download_events')) die('Security Check Failed!');

			header('Content-Encoding: UTF-8');
        	header('Content-type: text/csv; charset=UTF-8');
			header("Content-Disposition: attachment; filename=Eventon_events_".date("d-m-y").".csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			echo "\xEF\xBB\xBF"; // UTF-8 BOM
			
			$evo_opt = get_option('evcal_options_evcal_1');
			$event_type_count = evo_get_ett_count($evo_opt);
			$cmd_count = evo_calculate_cmd_count($evo_opt);

			$fields = apply_filters('evo_csv_export_fields',array(
				'publish_status',	
				'event_id',			
				'evcal_event_color'=>'color',
				'event_name',				
				'event_description','event_start_date','event_start_time','event_end_date','event_end_time',

				'evcal_allday'=>'all_day',
				'evo_hide_endtime'=>'hide_end_time',
				'evcal_gmap_gen'=>'event_gmap',
				'evo_year_long'=>'yearlong',
				'_featured'=>'featured',

				'evo_location_id'=>'evo_location_id',
				'evcal_location_name'=>'location_name',				
				'evcal_location'=>'event_location',				
				
				'evo_organizer_id'=>'evo_organizer_id',
				'evcal_organizer'=>'event_organizer',

				'evcal_subtitle'=>'evcal_subtitle',
				'evcal_lmlink'=>'learnmore link',
				'image_url',

				'evcal_repeat'=>'repeatevent',
				'evcal_rep_freq'=>'frequency',
				'evcal_rep_num'=>'repeats',
				'evp_repeat_rb'=>'repeatby',
			));
			
			// Print out the CSV file header
				$csvHeader = '';
				foreach($fields as $var=>$val){	$csvHeader.= $val.',';	}

				// event types
					for($y=1; $y<=$event_type_count;  $y++){
						$_ett_name = ($y==1)? 'event_type': 'event_type_'.$y;
						$csvHeader.= $_ett_name.',';
						$csvHeader.= $_ett_name.'_slug,';
					}
				// for event custom meta data
					for($z=1; $z<=$cmd_count;  $z++){
						$_cmd_name = 'cmd_'.$z;
						$csvHeader.= $_cmd_name.",";
					}

				$csvHeader = apply_filters('evo_export_events_csv_header',$csvHeader);
				$csvHeader.= "\n";
				
				echo (function_exists('iconv'))? iconv("UTF-8", "ISO-8859-2", $csvHeader): $csvHeader;
 	
 			// events
			$events = new WP_Query(array(
				'posts_per_page'=>-1,
				'post_type' => 'ajde_events',
				'post_status'=>'any'			
			));

			if($events->have_posts()):
				date_default_timezone_set('UTC');

				// allow processing content for html readability
				$process_html_content = true;

				// for each event
				while($events->have_posts()): $events->the_post();
					$__id = get_the_ID();
					$pmv = get_post_meta($__id);

					$csvRow = '';
					$csvRow.= get_post_status($__id).",";
					$csvRow.= $__id.",";
					//echo (!empty($pmv['_featured'])?$pmv['_featured'][0]:'no').",";
					$csvRow.= (!empty($pmv['evcal_event_color'])? $pmv['evcal_event_color'][0]:'').",";

					// event name
						$eventName = get_the_title();
						$eventName = $this->html_process_content($eventName, $process_html_content);
						//$output = iconv("utf-8", "ascii//TRANSLIT//IGNORE", $eventName);
						//$output =  preg_replace("/^'|[^A-Za-z0-9\s-]|'$/", '', $output); 
						$csvRow.= '"'.$eventName.'",';

					$event_content = get_the_content();
						$event_content = str_replace('"', "'", $event_content);
						$event_content = str_replace(',', "\,", $event_content);
						$event_content = $this->html_process_content( $event_content, $process_html_content);
					$csvRow.= '"'.$event_content.'",';

					// start time
						$start = (!empty($pmv['evcal_srow'])?$pmv['evcal_srow'][0]:'');
						if(!empty($start)){
							// date and time as separate columns
							$csvRow.= '"'. date( apply_filters('evo_csv_export_dateformat','m/d/Y'), $start) .'",';
							$csvRow.= '"'. date( apply_filters('evo_csv_export_timeformat','h:i:A'), $start) .'",';
							//$csvRow.= '"'. date($date_time_format, $start) .'",';
						}else{ $csvRow.= "'','',";	}

					// end time
						$end = (!empty($pmv['evcal_erow'])?$pmv['evcal_erow'][0]:'');
						if(!empty($end)){
							// date and time as separate columns
							$csvRow.= '"'. date( apply_filters('evo_csv_export_dateformat','m/d/Y'), $end) .'",';
							$csvRow.= '"'. date( apply_filters('evo_csv_export_timeformat','h:i:A'), $end) .'",';
							//$csvRow.= '"'. date($date_time_format,$end) .'",';
						}else{ $csvRow.= "'','',";	}

					// taxonomy meta
						$taxopt = get_option( "evo_tax_meta");
						

					// FOR EACH field
					$loctaxid = $orgtaxid = '';
					$loctaxname = $orgtaxname = '';
					foreach($fields as $var=>$val){

						// yes no values
						if(in_array($val, array('featured','all_day','hide_end_time','event_gmap','evo_year_long','_evo_month_long','repeatevent'))){
							$csvRow.= ( (!empty($pmv[$var]) && $pmv[$var][0]=='yes') ? 'yes': 'no').',';
						}

						// organizer field
							if($val == 'evo_organizer_id'){
								$Orgterms = wp_get_object_terms( $__id, 'event_organizer' );
								if ( $Orgterms && ! is_wp_error( $Orgterms ) ){
									$orgtaxid = $Orgterms[0]->term_id;
									$orgtaxname = $Orgterms[0]->name;
									$csvRow.= '"'.$orgtaxid . '",';
								}else{	$csvRow.= ",";	}
							}
							if($val == 'evcal_organizer'){
								if($orgtaxname){
									$csvRow.= '"'. $this->html_process_content($orgtaxname, $process_html_content) . '",';									
								}elseif(!empty($pmv[$var]) ){
									$value = $this->html_process_content($pmv[$var][0], $process_html_content);
									$csvRow.= '"'.$value.'"';
								}else{	$csvRow.= ",";	}
								continue;
							}
						// location tax field
							if($val == 'evo_location_id'){
								$Locterms = wp_get_object_terms( $__id, 'event_location' );
								if ( $Locterms && ! is_wp_error( $Locterms ) ){
									$loctaxid = $Locterms[0]->term_id;
									$loctaxname = $Locterms[0]->name;
									$csvRow.= '"'.$loctaxid . '",';
								}else{	$csvRow.= ",";	}
							}
							if($val == 'location_name'){
								if($loctaxname){
									$csvRow.= '"'. $this->html_process_content($loctaxname, $process_html_content) . '",';									
								}elseif(!empty($pmv[$var]) ){
									$value = $this->html_process_content($pmv[$var][0], $process_html_content);
									$csvRow.= '"'.$value.'"';
								}else{	$csvRow.= ",";	}
								update_post_meta(3089,'aa',$loctaxname.'yy');
								continue;
							}
							if($val == 'event_location'){
								if($loctaxid){
									$termMeta = evo_get_term_meta('event_location',$loctaxid, $taxopt, true);
									$csvRow.= !empty($termMeta['location_address'])? 
										'"'. $this->html_process_content($termMeta['location_address'], $process_html_content) . '",':
										",";									
								}elseif(!empty($pmv[$var]) ){
									$value = $this->html_process_content($pmv[$var][0], $process_html_content);
									$csvRow.= '"'.$value.'"';
								}else{	$csvRow.= ",";	}
								continue;
							}

						// skip fields
						if(in_array($val, array('featured','all_day','hide_end_time','event_gmap','evo_year_long','_evo_month_long','repeatevent','color','publish_status','event_name','event_description','event_start_date','event_start_time','event_end_date','event_end_time','evo_organizer_id', 'evo_location_id'
							)
						)) continue;

						// image
						if($val =='image_url'){
							$img_id =get_post_thumbnail_id($__id);
							if($img_id!=''){
								$img_src = wp_get_attachment_image_src($img_id,'full');
								$csvRow.= $img_src[0].",";
							}else{ $csvRow.= ",";}
						}else{
							if(!empty($pmv[$var])){
								$value = $this->html_process_content($pmv[$var][0], $process_html_content);
								$csvRow.= '"'.$value.'"';
							}else{ $csvRow.= '';}
							$csvRow.= ',';
						}
					}
					
					// event types
						for($y=1; $y<=$event_type_count;  $y++){
							$_ett_name = ($y==1)? 'event_type': 'event_type_'.$y;
							$terms = get_the_terms( $__id, $_ett_name );

							if ( $terms && ! is_wp_error( $terms ) ){
								$csvRow.= '"';
								foreach ( $terms as $term ) {
									$csvRow.= $term->term_id.',';
									//$csvRow.= $term->name.',';
								}
								$csvRow.= '",';

								// slug version
								$csvRow.= '"';
								foreach ( $terms as $term ) {
									$csvRow.= $term->slug.',';
								}
								$csvRow.= '",';
							}else{ $csvRow.= ",";}
						}
					// for event custom meta data
						for($z=1; $z<=$cmd_count;  $z++){
							$cmd_name = '_evcal_ec_f'.$z.'a1_cus';
							$csvRow.= (!empty($pmv[$cmd_name])? 
								'"'.str_replace('"', "'", $this->html_process_content($pmv[$cmd_name][0], $process_html_content) ) .'"'
								:'');
							$csvRow.= ",";
						}

					$csvRow = apply_filters('evo_export_events_csv_row',$csvRow, $__id, $pmv);
					$csvRow.= "\n";

				echo (function_exists('iconv'))? iconv("UTF-8", "ISO-8859-2", $csvRow): $csvRow;

				endwhile;
			endif;

			wp_reset_postdata();
		}

		function html_process_content($content, $process = true){
			return ($process)? htmlentities($content): $content;
		}

	// Validation of eventon products
		function validate_license(){
			global $eventon;

			$status = 'bad'; 
			$error_code = 11; 
			$error_msg_add = $html = '';
			
			// check for required information
			if(empty($_POST['type']) && isset($_POST['key']) && isset($_POST['slug']) ){ 
				echo json_encode(array('status'=>'bad','error_msg'=> EVO_Error()->error_code(14) ));		
				exit;
			}

			// Initial values
			$type = $_POST['type'];
			$license_key = $_POST['key'];
			$slug = $_POST['slug'];

			$PROD = new EVO_Product_Lic($slug);
			
			// check for key format validation
			$verifyformat = $PROD->purchase_key_format($license_key );
			if(!$verifyformat) $error_code = '02';	
			
			
			// if license key format is validated
			if($verifyformat){

				// save eventon data
				if($type=='main') $PROD->save_license_data();

				$status = 'good';
				$msg = ($slug=='eventon')?
					'Excellent! Purchase key verified and saved. Thank you for activating EventON!':
					'Excellent! License key verified and saved. Thank you for activating EventON addon!';

				$data_args = array(
					'type'		=>(!empty($_POST['type'])?$_POST['type']:'main'),
					'key'		=> addslashes( str_replace(' ','',$license_key) ),
					'email'		=>(!empty($_POST['email'])? $_POST['email']: null),
					'product_id'=>(!empty($_POST['product_id'])?$_POST['product_id']:''),
				);
				$validation = $PROD->remote_validation($data_args);

				// Other update tasks
				if($type=='addon'){	
					// update other addon fields
					foreach(array(
						'email','product_id','instance','key'
					) as $field){
						if(!empty($_POST[$field])){
							$PROD->set_prop( $field, $_POST[$field], false);
						}
					}
					$PROD->save();
				}

				$results = $this->get_remote_validation_results($validation, $PROD, $type);
			
				if(isset($results['error_code'])) $error_code = $results['error_code'];

				$status = $results['status'];
					
				if($error_code != 11){
					$msg = EVO_Error()->error_code( $error_code);
				}

				if($results['status'] == 'bad' && in_array( $error_code, array(11,21,23) )){
					$msg = EVO_Error()->error_code( 120 );
				}

			}else{
				// Invalid license key format
				$status = 'bad';
				$msg = 'License Key format is not a valid format!';
			}

			$return_content = array(
				'status'=>	$status,
				'msg'=> 	$msg,				
				'code'=> 	$error_code,
				'html'=>	$this->get_html_view( $type,$slug),
			);
			echo json_encode($return_content);		
			exit;
		}

		// RE-VALIDATE
			function revalidate_license(){
				$slug = $_POST['slug'];

				$PROD = new EVO_Product_Lic($slug);

				//echo $PROD->get_prop('key');

				if( !$PROD->get_prop('key') || !$PROD->get_prop('email')){
					echo json_encode(array(
						'status'=>'bad',
						'msg'=>'Required fields for remote validation are missing! try deactivating and reactivating again.'
					));		
					exit;
				}else{

					$ERR = new EVO_Error();
					$ERR->record_gen_log('Re-activating', $slug,'','',false);

					$data_args = array(
						'type'		=>(!empty($_POST['type'])?$_POST['type']:'main'),
						'key'		=> $PROD->get_prop('key'),
						'email'		=> $PROD->get_prop('email'),
						'product_id'=>(!empty($_POST['product_id'])?$_POST['product_id']:''),						
						'instance'	=> md5(get_site_url()),
					);
					$validation = $PROD->remote_validation($data_args);

					
					$results = $this->get_remote_validation_results( $validation, $PROD , $_POST['type']);
					$output_error_code = isset($results['error_code'])? (int) $results['error_code']: false;

					if($results['status'] == 'bad'){
						$ERR->record_gen_log('Re-activating failed', $slug, $results['error_code'],'',false);
					}
					
					$ERR->save();

					// Message intepretation
					$msg = ($results['status']=='bad'? EVO_Error()->error_code(15): EVO_Error()->error_code(16));
					if($results['status'] == 'bad' && $output_error_code && in_array( $output_error_code, array(11,21,23) )){
						$msg = EVO_Error()->error_code( 121 );
					}

					if($output_error_code && in_array( $output_error_code, array(100,101,102,103)) ){
						$msg = EVO_Error()->error_code( $output_error_code );

						if( $output_error_code == 103){
							$msg = EVO_Error()->error_code( '103r' );
							EVO_Error()->record_deactivation_loc($slug);
							$PROD->deactivate();
						}
					}

					echo json_encode(array(
						'status'=> $results['status'],
						'msg'=> $msg,
						//'error_msg'=> EVO_Error()->error_code( $results['error_code']),
						'html'=> $this->get_html_view( 'addon',$slug),					
					));		
					exit;
				}
			}

	// REMOTE RESULTS
		function get_remote_validation_results($validation, $PROD, $type){
			// validation contain // status, error_remote_msg, error_code, api_url
			// invalid remote validation
			$output = array();
			$error_code = false;
			if($validation['status'] =='good'){
				$output['status'] = 'good';
				EVO_Prods()->get_remote_prods_data();
				$PROD->evo_kriyathmaka_karanna();
				EVO_Error()->record_activation_rem();

			}else{
				$output['status'] = 'bad';	
				if(!empty($validation['error_code'])) $error_code =  (int)$validation['error_code'];
				
				$output['error_code'] = $error_code;
				$output['error_msg'] = isset($validation['error_remote_msg'])? $validation['error_remote_msg']: '';

				// local kriyathmaka karala nehe
				if(!$PROD->kriyathmaka_localda() && $error_code && in_array( $error_code, array(11,21,23) ) ){
					$PROD->evo_kriyathmaka_karanna_athulen();
					EVO_Error()->record_activation_loc($error_code);
				}
			}

			return $output;
		}

	// Deactivate EventON Products
		function deactivate_product(){
			$error_msg = $status = $html = '';
			$error_code = '00';
			
			if($_POST['type'] == 'main'){
				$PROD = new EVO_Product_Lic('eventon');
				$status = $PROD->deactivate();

				$slug = 'eventon';

				// not able to deactivate
				if(!$status){
				 	$error_code = '07';	
				}else{ // deactivated
					EVO_Error()->record_deactivation_loc($slug);
					$html = $this->get_html_view('main',$slug);
					$error_code = 32;
				}
				
			}else{// for addons

				if(!isset($_POST['slug'])){
					echo json_encode(array(
						'status'=>'bad',
						'error_msg'=> EVO_Error()->error_code(14)
					)); exit;
				}

				$PROD = new EVO_Product_Lic($_POST['slug']);
			
				// passing data
					$remote_data = array(
						'key'		=> addslashes( str_replace(' ','',$_POST['key']) ),
						'email'		=>(!empty($_POST['email'])? $_POST['email']: null),
						'product_id'=>(!empty($_POST['product_id'])? $_POST['product_id']: null),
					);

				// deactivate addon from remote server
					$deactive_remotely = $PROD->remote_deactivate($remote_data);

					$returned_error_code = isset($deactive_remotely['error_code'])? (int)$deactive_remotely['error_code']:false;

					if($returned_error_code && in_array( $returned_error_code, array(30,31) ) ){
						
						EVO_Error()->record_deactivation_fail($returned_error_code);
						EVO_Error()->record_deactivation_loc($_POST['slug']);
						$PROD->deactivate();
						$error_code = 32;
					}else{
						$error_code = 33;
						EVO_Error()->record_deactivation_rem();
						$PROD->deactivate();
					}


					$html = $this->get_html_view('addon',$_POST['slug']);
					$status = 'success';
			}

			$return_content = array(
				'status'=> ($status?'success':'bad'),
				'msg'=>EVO_Error()->error_code($error_code),
				'html'=> $html,					
			);
			echo json_encode($return_content);		
			exit;
		}

		function get_html_view($type,$slug){
			$views = new EVO_Views();
			$var = ($type=='main')? 'evo_box': 'evo_addon_box';
			return $views->get_html(	$var,array('slug'	=>$slug) );
		}
		
	// get all addon details
		public function get_addons_list(){

			// verifications
			if(!is_admin()) return false;

			$active_plugins = get_option( 'active_plugins' );

			ob_start();
			// installed addons		

				$addons_list = new EVO_Addons_List();

				$count=1;
				// EACH ADDON
				foreach($addons_list->get_list() as $slug=>$product){

					if($slug=='eventon') continue; // skip for eventon
					$_has_addon = false;

					$views = new EVO_Views();

					echo $views->get_html(
						'evo_addon_box',
						array(
							'slug'				=>$slug,
							'product'			=>$product,
							'active_plugins'	=>$active_plugins
						)
					);
					
					$count++;
				} //endforeach

			$content = ob_get_clean();

			$return_content = array(
				'content'=> $content,
				'status'=>true
			);
			
			echo json_encode($return_content);		
			exit;	
		}

	/** Feature an event from admin */
		function eventon_feature_event() {

			if ( ! is_admin() ) die;

			if ( ! current_user_can('edit_eventons') ) wp_die( __( 'You do not have sufficient permissions to access this page.', 'eventon' ) );

			if ( ! check_admin_referer('eventon-feature-event')) wp_die( __( 'You have taken too long. Please go back and retry.', 'eventon' ) );

			$post_id = isset( $_GET['eventID'] ) && (int) $_GET['eventID'] ? (int) $_GET['eventID'] : '';

			if (!$post_id) die;

			$post = get_post($post_id);

			if ( ! $post || $post->post_type !== 'ajde_events' ) die;

			$featured = get_post_meta( $post->ID, '_featured', true );

			if ( $featured == 'yes' )
				update_post_meta($post->ID, '_featured', 'no');
			else
				update_post_meta($post->ID, '_featured', 'yes');

			wp_safe_redirect( remove_query_arg( array('trashed', 'untrashed', 'deleted', 'ids'), wp_get_referer() ) );
		}
	
	// Diagnose
		// send test email
		function admin_test_email(){
			$email_address = $_POST['email'];

			$result = wp_mail($email_address, 'This is a Test Email', 'Test Email Body', array('Content-Type: text/html; charset=UTF-8') );
			
			$ts_mail_errors = array();
			if(!$result){
				global $ts_mail_errors;
				global $phpmailer;

				if (!isset($ts_mail_errors)) $ts_mail_errors = array();

				if (isset($phpmailer)) {
					$ts_mail_errors[] = $phpmailer->ErrorInfo;
				}
			}

			echo json_encode(array(
				'msg'=> ($result?'Email Sent': 'Email was not sent'),
				'error'=>$ts_mail_errors
			));		
			exit;
		}
}
new EVO_admin_ajax();