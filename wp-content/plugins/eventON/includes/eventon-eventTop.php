<?php
/**
 * Event Top section
 * process content as html output
 * @since  eventon 2.4.8
 * @version  0.1
 */
function eventon_get_eventtop_print($array, $evOPT, $evOPT2){
	
	$OT = '';
	$_additions = apply_filters('evo_eventtop_adds' , array());

	if(!is_array($array)) return $OT;

	foreach($array as $element =>$elm){

		// convert to an object
		$object = new stdClass();
		foreach ($elm as $key => $value){
			$object->$key = $value;
		}

		$boxname = (in_array($element, $_additions))? $element: null;

		switch($element){
			case has_filter("eventon_eventtop_{$boxname}"):
				$helpers = array(
					'evOPT'=>$evOPT,
					'evoOPT2'=>$evOPT2,
				);

				$OT.= apply_filters("eventon_eventtop_{$boxname}", $object, $helpers);	
			break;
			case 'ft_img':
				$url = !empty($object->url_med)? $object->url_med: $object->url;
				//$url = !empty($object->url_full)? $object->url_full[0]: $url;
				$url = apply_filters('eventon_eventtop_image_url', $url);
				$OT.= "<span class='ev_ftImg' data-img='".(!empty($object->url_full)? $object->url_full[0]: '')."' data-thumb='".$url."' style='background-image:url(\"".$url."\")'></span>";
			break;
			case 'day_block':

				//print_r($object);
				
				$OT.="<span class='evcal_cblock ".( $object->yearlong?'yrl ':null).( $object->monthlong?'mnl ':null)."' data-bgcolor='".$object->color."' data-smon='".$object->start['F']."' data-syr='".$object->start['Y']."'>";
				
				// include dayname if allowed via settings
				$dayname = '';
				if( is_array($object->eventtop_fields) && in_array('dayname', $object->eventtop_fields)){
					$dayname = (!empty($object->html['start']['day'])? $object->html['start']['day']:'');
				}

				$time_data = apply_filters('evo_eventtop_dates', array(
					'start'=>array(
						'year'=> ($object->show_start_year=='yes'? $object->html['start']['year']:''),	
						'day'=>$dayname,
						'date'=> (!empty($object->html['start']['date'])?$object->html['start']['date']:''),
						'month'=>  (!empty($object->html['start']['month'])?$object->html['start']['month']:''),
						'time'=>  (!empty($object->html['start']['time'])?$object->html['start']['time']:''),
					),
					'end'=>array(
						'year'=> (($object->show_end_year=='yes' && !empty($object->html['end']['year']) )? $object->html['end']['year']:''),	
						'date'=> (!empty($object->html['end']['date'])?$object->html['end']['date']:''),
						'month'=> (!empty($object->html['end']['month'])? $object->html['end']['month']:''),
						'time'=> (!empty($object->html['end']['time'])? $object->html['end']['time']:''),
					),
				), $object->show_start_year, $object );

				$class_add = '';
				foreach($time_data as $type=>$data){					
					$end_content = '';
					foreach($data as $field=>$value){
						if(empty($value)) continue;
						$end_content .= "<em class='{$field}'>{$value}</em>";
					}

					if($type == 'end' && empty($data['year']) && empty($data['month']) && empty($data['date']) && !empty($data['time'])){
						$class_add = 'only_time';
					}
					if(empty($end_content)) continue;
					$OT .= "<span class='evo_{$type} {$class_add}'>";
					$OT .= $end_content;
					$OT .= "</span>";
				}
			
				/*	
				$day_content = apply_filters('evo_eventtop_day_block', array(
						'year'=> ($object->showyear=='yes'?$object->start['Y']:''),
						'date'=> $object->day_name.$object->html['html_date'],
						'time'=> $object->html['html_time']
					), $object);

					foreach($day_content as $index=>$value){
						if(empty($value)) continue;

						$OT.= "<em class='evo_".$index."' >".$value.'</em>';
					}
				*/
				
				$OT.= "<em class='clear'></em>";
				$OT .= "</span>";

			break;

			// title section of the event top
			case 'titles':
				$show_widget_eventtops = (!empty($evOPT['evo_widget_eventtop']) && $evOPT['evo_widget_eventtop']=='yes')? '':'hide_eventtopdata ';
				$OT.= "<span class='evcal_desc evo_info ".$show_widget_eventtops. ( $object->yearlong?'yrl ':null).( $object->monthlong?'mnl ':null)."' {$object->loc_vars} >";
				
				// above title inserts
				$OT.= "<span class='evo_above_title'>";
					$OT .= apply_filters("eventon_eventtop_abovetitle", '', $object);
					
					if($object->cancel){
						$OT.= "<span class='evo_event_headers canceled' title='".(!empty($object->cancel_reason)? $object->cancel_reason: null)."'>".( eventon_get_custom_language( $evOPT2,'evcal_evcard_evcancel', 'Event Cancelled')  )."</span>";
					}
					if($object->featured){
						$OT.= "<span class='evo_event_headers featured'>".( evo_lang('Featured','', $evOPT2)  )."</span>";
					}


				$OT.="</span>";

				// event edit button
					$editBTN = '';
					if( current_user_can('manage_options') && !empty($evOPT['evo_showeditevent']) && $evOPT['evo_showeditevent']=='yes')
						$editBTN = "<i href='".get_edit_post_link($object->eventid)."' class='editEventBtnET fa fa-pencil'></i>";
				
				$OT.= "<span class='evcal_desc2 evcal_event_title' itemprop='name'>". apply_filters('eventon_eventtop_maintitle',$object->title) . $editBTN."</span>";
				
				// below title inserts
				$OT.= "<span class='evo_below_title'>";
					if($object->subtitle)
						$OT.= "<span class='evcal_event_subtitle' >" . apply_filters('eventon_eventtop_subtitle' , $object->subtitle) ."</span>";
				$OT.="</span>";
			break;

			case 'belowtitle':

				$OT.= "<span class='evcal_desc_info' >";

				// time
				if($object->fields_ && in_array('time',$object->fields) && isset($object->html)){
					$OT.= "<em class='evcal_time'>".$object->html['html_fromto'].(!empty($object->timezone)? ' <em class="evo_etop_timezone">'.$object->timezone. '</em>':null)."</em> ";
				}
				
				
				// location information
				if($object->fields_){
					// location name
					$LOCname = (in_array('locationame',$object->fields) && isset($object->locationname) )? $object->locationname: false;

					// location address
					$LOCadd = (in_array('location',$object->fields) && !empty($object->locationaddress))? stripslashes($object->locationaddress): false;

					if($LOCname || $LOCadd){
						$OT.= '<em class="evcal_location" '.( ($object->lonlat)? $object->lonlat:null ).' data-add_str="'.$LOCadd.'">'.($LOCname? '<em class="event_location_name">'.$LOCname.'</em>':'').
							( ($LOCname && $LOCadd)?', ':'').
							$LOCadd.'</em>';
					}
				}

				$OT.="</span>";
				$OT.="<span class='evcal_desc3'>";

				//organizer
					$org = ($object->organizer_name)? $object->organizer_name: ((!empty($object->evvals['evcal_organizer']))? $object->evvals['evcal_organizer'][0]:false);

					if($object->fields_ && in_array('organizer',$object->fields) && $org){
						$OT.="<span class='evcal_oganizer'>
							<em><i>".( eventon_get_custom_language( $evOPT2,'evcal_evcard_org', 'Event Organized By')  ).':</i></em>
							<em>'.$org."</em>
							</span>";
					}
				//event type
				if($object->tax)
					$OT.= $object->tax;

				// event tags
				if($object->fields_ && in_array('tags',$object->fields) && !empty($object->tags) ){
					$OT.="<span class='evo_event_tags'>
						<em><i>".eventon_get_custom_language( $evOPT2,'evo_lang_eventtags', 'Event Tags')."</i></em>";

					$count = count($object->tags);
					$i = 1;
					foreach($object->tags as $tag){
						$OT.="<em data-tagid='{$tag->term_id}'>{$tag->name}".( ($count==$i)?'':',')."</em>";
						$i++;
					}
					$OT.="</span>";
				}

				// custom fields
				for($x=1; $x<$object->cmdcount+1; $x++){
					if($object->fields_ && in_array('cmd'.$x,$object->fields) 
						&& !empty($object->evvals['_evcal_ec_f'.$x.'a1_cus'])){

						$def = $evOPT['evcal_ec_f'.$x.'a1']; // default custom meta field name
						$i18n_nam = eventon_get_custom_language( $evOPT2,'evcal_cmd_'.$x, $def);

						// custom fiels icon
							$icon_string = '';
							if(!empty($evOPT['evcal__fai_00c'. $x]) && !empty($evOPT['evo_eventtop_customfield_icons']) && $evOPT['evo_eventtop_customfield_icons']=='yes'){
								$icon_string ='<i class="fa '. $evOPT['evcal__fai_00c'. $x] .'"></i>'; 
							}

						//$OT.= ( ($x==1)? "<b class='clear'></b>":null );

						// type button 
							if(!empty($evOPT['evcal_ec_f'.$x.'a2']) && $evOPT['evcal_ec_f'.$x.'a2']=='button'){
								$href = !empty($object->evvals['_evcal_ec_f'.$x.'a1_cusL'])? 
									$object->evvals['_evcal_ec_f'.$x.'a1_cusL'][0]:'';
								$target = !empty($object->evvals['_evcal_ec_f'.$x.'_onw'])? 
									$object->evvals['_evcal_ec_f'.$x.'_onw'][0]:'no';

								$OT.= "<span><em class='evcal_cmd evocmd_button' data-href='". ($href). "' data-target='". ($target). "'>" . $icon_string .$object->evvals['_evcal_ec_f'.$x.'a1_cus'][0]."</em></span>";
							}else{
								$OT.= "<span>
									<em class='evcal_cmd'>". $icon_string ."<i>".$i18n_nam.':</i></em>
									<em>'.$object->evvals['_evcal_ec_f'.$x.'a1_cus'][0]."</em>
									</span>";
							}
					}
				}
			break;

			case 'close1':
				$OT.="</span>";// span.evcal_desc3
			break;

			case 'close2':
				$OT.= "</span>";// span.evcal_desc 
				$OT.="<em class='clear'></em>";
			break;
		}
	}	

	return $OT;
}


?>