<?php
/**
 * EventON Ajax Handlers
 *
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	EventON/Functions/AJAX
 * @version     2.5.3
 */

class evo_ajax{
	/**
	 * Hook into ajax events
	 */
	public function __construct(){
		$ajax_events = array(
			'ics_download'=>'eventon_ics_download',
			'the_ajax_hook'=>'evcal_ajax_callback',			
			'export_events_ics'=>'export_events_ics',
			'search_evo_events'=>'search_evo_events',
			'get_local_event_time'=>'get_local_event_time',
			//'evo_dynamic_css'=>'eventon_dymanic_css',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {

			$prepend = ( in_array($ajax_event, array('the_ajax_hook','evo_dynamic_css','the_post_ajax_hook_3','the_post_ajax_hook_2')) )? '': 'eventon_';

			add_action( 'wp_ajax_'. $prepend . $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'. $prepend . $ajax_event, array( $this, $class ) );
		}

		//add_action('wp_ajax_eventon-feature-event', array($this, 'eventon_feature_event'));
	}

	// OUTPUT: json headers
		private function json_headers() {
			header( 'Content-Type: application/json; charset=utf-8' );
		}

	// for event post repeat intervals 
	// @return converted unix time stamp on UTC timezone
		public function repeat_interval(){
			$date_format = $_POST['date_format'];
		}

	// Primary function to load event data 
		function evcal_ajax_callback(){
			global $eventon;
			$shortcode_args='';
			$status = 'GOOD';

			$evodata = !empty($_POST['evodata'])? $_POST['evodata']: false;
			
			// Initial values
				$current_month = (int)(!empty($evodata['cmonth'])? ($evodata['cmonth']): $_POST['current_month']);
				$current_year = (int)(!empty($evodata['cyear'])? $evodata['cyear']: $_POST['current_year']);	

				$send_unix = (isset($evodata['send_unix']))? $evodata['send_unix']:null;
				$direction = $_POST['direction'];
				$sort_by = (!empty($_POST['sort_by']))? $_POST['sort_by']: 
					( !empty($evodata['sort_by'])? $evodata['sort_by'] :'sort_date');
			
			// generate new UNIX range dates for calendar
				if($send_unix=='1'){
					$focus_start_date_range = (isset($evodata['range_start']))? (int)($evodata['range_start']):null;
					$focus_end_date_range = (isset($evodata['range_end']))? (int)($evodata['range_end']):null;	
					
					$focused_month_num = $current_month;
					$focused_year = $current_year;

				}else{
					if($direction=='none'){
						$focused_month_num = $current_month;
						$focused_year = $current_year;
					}else{
						$focused_month_num = ($direction=='next')?
							(($current_month==12)? 1:$current_month+1):
							(($current_month==1)? 12:$current_month-1);
						
						$focused_year = ($direction=='next')? 
							(($current_month==12)? $current_year+1:$current_year):
							(($current_month==1)? $current_year-1:$current_year);
					}	
					
					date_default_timezone_set('UTC');
						
					$focus_start_date_range = mktime( 0,0,0,$focused_month_num,1,$focused_year );
					$time_string = $focused_year.'-'.$focused_month_num.'-1';		
					$focus_end_date_range = mktime(23,59,59,($focused_month_num),(date('t',(strtotime($time_string) ))), ($focused_year));
				}
				
			// base calendar arguments at this stage
				$eve_args = array(
					'focus_start_date_range'=>$focus_start_date_range,
					'focus_end_date_range'=>$focus_end_date_range,
					'sort_by'=>$sort_by,		
					'event_count'=>(!empty($_POST['event_count']))? $_POST['event_count']: 
						( !empty($evodata['ev_cnt'])? $evodata['ev_cnt']: '' ),
					'filters'=>((isset($_POST['filters']))? $_POST['filters']:null)
				);
				//print_r($eve_args);
			
			// shortcode arguments USED to build calendar
				if(!empty($_POST['shortcode']) && count($_POST['shortcode'])>0){
					$shortcode_args = array();
					foreach($_POST['shortcode'] as $f=>$v){
						$shortcode_args[$f]=$v;
					}
					$eve_args = array_merge($eve_args, $shortcode_args);
					$lang = $_POST['shortcode']['lang'];
				}else{
					$lang ='';
				}
				
					
			// GET calendar header month year values
				$calendar_month_title = get_eventon_cal_title_month($focused_month_num, $focused_year, $lang);
					
			// AJAX Addon hook
				$eve_args = apply_filters('eventon_ajax_arguments',$eve_args, $_POST);

			// Calendar content		
				$EVENTlist = $eventon->evo_generator->evo_get_wp_events_array('', $eve_args, $eve_args['filters']);

				$EVENTlist  = $eventon->evo_generator->move_ft_to_top($EVENTlist, $eve_args);
				$EVENTlist  = $eventon->evo_generator->order_past_future_events($EVENTlist, $eve_args['filters']);

				$total_events = count($EVENTlist);

				if(!empty($eve_args['sep_month']) && $eve_args['sep_month']=='yes' && $eve_args['number_of_months']>1){
					$content_li = $eventon->evo_generator->separate_eventlist_to_months($EVENTlist, $eve_args['event_count'], $eve_args);
				}else{

					$EVENTlist = $eventon->evo_generator->raw_event_list_filter_pagination($EVENTlist);

					$date_range_events_array = $eventon->evo_generator->generate_event_data( 
						$EVENTlist, 
						$focus_start_date_range,
						$focused_month_num , $focused_year 
					);
					$content_li = $eventon->evo_generator->evo_process_event_list_data($date_range_events_array, $eve_args);
				}
				
			//$content_li = $eventon->evo_generator->eventon_generate_events( $eve_args);

			// Update the events list to remove post meta values to reduce load on AJAX
				$NEWevents = array();
				foreach($EVENTlist as $event_id=>$event){
					unset($event['event_pmv']);
					$NEWevents[$event_id]= $event;
				}


			// RETURN VALUES
			// Array of content for the calendar's AJAX call returned in JSON format
				$return_content = array(
					'status'=>(!$evodata? 'Need updated':$status),
					'eventList'=>$NEWevents,					
					'cal_month_title'=>$calendar_month_title,
					'month'=>$focused_month_num,
					'year'=>$focused_year,
					'focus_start_date_range'=>$focus_start_date_range,
					'focus_end_date_range'=>$focus_end_date_range,	
					'total_events'=>$total_events,
					'content'=>$content_li,
				);			
			
			
			echo json_encode($return_content);
			exit;
		}

	// ICS file generation for add to calendar buttons
		function eventon_ics_download(){
			$event_id = (int)($_GET['event_id']);
			
			// Location information
				$location_name = !empty($_GET['locn']) ? $_GET['locn'] : false;
				$location_address = !empty($_GET['loca']) ? $_GET['loca'] : false;
				$location = ($location_name? $location_name . ' ':'') . ($location_address?$location_address:'');
				$location = $this->esc_ical_text( stripslashes($location) );

			//error_reporting(E_ALL);
			//ini_set('display_errors', '1');
			
			//$the_event = get_post($event_id);
			$ev_vals = get_post_custom($event_id);
			
			// start and end time
				$start = $_GET['sunix'];
				$end = (!empty($_GET['eunix']))? $_GET['eunix'] : $sunix;
			
			$name = $summary = (get_the_title($event_id));

			// summary for ICS file
			$event = get_post($event_id);
			if(empty($event)) return false;
			
			$content = (!empty($event->post_content))? $event->post_content:'';
			if(!empty($content)){
				$content = strip_tags($content);
				$content = str_replace(']]>', ']]&gt;', $content);
				$summary = wp_trim_words($content, 50, '[..]');
				//$summary = substr($content, 0, 500).' [..]';
			}			
							
			
			$uid = uniqid();
			//$description = $the_event->post_content;
			
			//ob_clean();
			
			//$slug = strtolower(str_replace(array(' ', "'", '.'), array('_', '', ''), $name));
			$slug = $event->post_name;
						
			header("Content-Type: text/Calendar; charset=utf-8");
			header("Content-Disposition: inline; filename={$slug}.ics");
			echo "BEGIN:VCALENDAR\r\n";
			echo "VERSION:2.0\r\n";
			echo "PRODID:-//eventon.com NONSGML v1.0//EN\n";
			//echo "METHOD:REQUEST\n"; // requied by Outlook
			echo "BEGIN:VEVENT\n";
			echo "UID:{$uid}\n"; // required by Outlok
			echo "DTSTAMP:".date_i18n('Ymd').'T'.date_i18n('His')."\n"; // required by Outlook
			//echo "DTSTART:{$start}\n"; 
			//echo "DTEND:{$end}\n";
			echo "DTSTART:". 	( strpos($start, 'T')===false? date_i18n('Ymd\THis',$start): $start)."\n";
			echo "DTEND:".		( strpos($start, 'T')===false? date_i18n('Ymd\THis',$end): $end)."\n";
			echo "LOCATION:{$location}\n";
			echo "SUMMARY:".html_entity_decode( $this->esc_ical_text($name))."\n";
			echo "DESCRIPTION: ".$this->esc_ical_text($summary)."\n";
			echo "END:VEVENT\n";
			echo "END:VCALENDAR";
			exit;
		}
		function esc_ical_text( $text='' ) {
			$fnc = new evo_fnc();
			
		    $text = str_replace("\\", "", $text);
		    $text = str_replace("\r", "\r\n ", $text);
		    $text = str_replace("\n", "\r\n ", $text);
		    $text = str_replace(",", "\, ", $text);
		    $text = $fnc->htmlspecialchars_decode($text);
		    return $text;
		}

	// download all event data as ICS
		function export_events_ics(){
			global $eventon;

			$fnc = new evo_fnc();

			if(!wp_verify_nonce($_REQUEST['nonce'], 'eventon_download_events')) die('Nonce Security Failed.');

			$events = $eventon->evo_generator->get_all_event_data();
			if(!empty($events)):
				$taxopt = get_option( "evo_tax_meta"); // taxonomy options values;
				$slug = 'eventon_events';
				header("Content-Type: text/Calendar; charset=utf-8");
				header("Content-Disposition: inline; filename={$slug}.ics");
				echo "BEGIN:VCALENDAR\n";
				echo "VERSION:2.0\n";
				echo "PRODID:-//eventon.com NONSGML v1.0//EN\n";
				echo "CALSCALE:GREGORIAN\n";
				echo "METHOD:PUBLISH\n";

				foreach($events as $event_id=>$event){
					$location = $summary = '';

					if(!empty($event['details'])){
						$summary = wp_trim_words($event['details'], 50, '[..]');
					}

					// location 
						$Locterms = wp_get_object_terms( $event_id, 'event_location' );
						$location_name = $locationAddress = '';
						if ( $Locterms && ! is_wp_error( $Locterms ) ){
							$location_name = $Locterms[0]->name;
							$termMeta = evo_get_term_meta('event_location',$Locterms[0]->term_id, $taxopt, true);
							$locationAddress = !empty($termMeta['location_address'])? 
								$termMeta['location_address']:
								(!empty($event['location_address'])? $event['location_address']:'');
						}
						$location = (!empty($location_name)? $location_name:'').' '. (!empty($locationAddress)? $locationAddress:'');

					$uid = uniqid();
					echo "BEGIN:VEVENT\n";
					echo "UID:{$uid}\n"; // required by Outlok
					echo "DTSTAMP:".date_i18n('Ymd').'T'.date_i18n('His')."\n"; // required by Outlook
					echo "DTSTART:" . evo_get_adjusted_utc($event['start']) ."\n"; 
					echo "DTEND:" . evo_get_adjusted_utc($event['end']) ."\n";
					if(!empty($location)) echo "LOCATION:". $this->esc_ical_text($location) ."\n";
					echo "SUMMARY:". $fnc->htmlspecialchars_decode($event['name'])."\n";
					if(!empty($summary)) echo "DESCRIPTION: ".$this->esc_ical_text($summary)."\n";
					echo "END:VEVENT\n";

					// repeating instances
						if(!empty($event['repeats']) && is_array($event['repeats'])){
							foreach( $event['repeats'] as $interval=>$repeats){
								if($interval==0) continue;

								$uid = uniqid();
								echo "BEGIN:VEVENT\n";
								echo "UID:{$uid}\n"; // required by Outlok
								echo "DTSTAMP:".date_i18n('Ymd').'T'.date_i18n('His')."\n"; // required by Outlook
								echo "DTSTART:" . evo_get_adjusted_utc($repeats[0]) ."\n"; 
								echo "DTEND:" . evo_get_adjusted_utc($repeats[1]) ."\n";
								if(!empty($location)) echo "LOCATION:". $this->esc_ical_text($location) ."\n";
								echo "SUMMARY:". $fnc->htmlspecialchars_decode($event['name'])."\n";
								if(!empty($summary)) echo "DESCRIPTION: ".$this->esc_ical_text($summary)."\n";
								echo "END:VEVENT\n";
							}
						}

				}
				echo "END:VCALENDAR";
				exit;

			endif;
		}

	// get event time based on local time on browswr
		function get_local_event_time(){
			$datetime = new evo_datetime();
			$offset = $datetime->get_UTC_offset();
			$brosweroffset = (int)$_POST['browser_offset'] *60;
			echo $brosweroffset.' '.$offset.' '.$object->evvals['evcal_srow'][0];

			$newunix = $object->evvals['evcal_srow'][0] + ($offset + $brosweroffset);
			echo date('Y-m-d h:ia', $newunix);
		}


	// Search results for ajax search of events from search box
	function search_evo_events(){
		$searchfor = $_POST['search'];
		$shortcode = $_POST['shortcode'];

		global $eventon;

		// if search all events regardless of date
		if( !empty($shortcode['search_all'] ) && $shortcode['search_all']=='yes'){
			$__focus_start_date_range = $__focus_end_date_range = 0;
		}else{
			$current_timestamp = current_time('timestamp');

			// restrained time unix
				$number_of_months = !empty($shortcode['number_of_months'])? $shortcode['number_of_months']:12;
				$month_dif = '+';
				$unix_dif = strtotime($month_dif.($number_of_months-1).' months', $current_timestamp);

				$restrain_monthN = ($number_of_months>0)?				
					date('n',  $unix_dif):
					date('n',$current_timestamp);

				$restrain_year = ($number_of_months>0)?				
					date('Y', $unix_dif):
					date('Y',$current_timestamp);			

			// upcoming events list 
				$restrain_day = date('t', mktime(0, 0, 0, $restrain_monthN+1, 0, $restrain_year));
				$__focus_start_date_range = $current_timestamp;
				$__focus_end_date_range =  mktime(23,59,59,($restrain_monthN),$restrain_day, ($restrain_year));
		}
		

		// Add extra arguments to shortcode arguments			
			$new_arguments = array(
				'focus_start_date_range'=>$__focus_start_date_range,
				'focus_end_date_range'=>$__focus_end_date_range,
				's'=>$searchfor
			);

			$args = (!empty($args) && is_array($args))? 
				wp_parse_args($new_arguments, $args): $new_arguments;

			// merge passed shortcode values
				if(!empty($shortcode))
					$args= wp_parse_args($shortcode, $args);

			$args__ = $eventon->evo_generator->process_arguments($args);

			$this->shortcode_args=$args__;

			$content =$eventon->evo_generator->calendar_shell_header(
				array(
					'month'=>$restrain_monthN,
					'year'=>$restrain_year, 
					'date_header'=>false,
					'sort_bar'=>false,
					'date_range_start'=>$__focus_start_date_range,
					'date_range_end'=>$__focus_end_date_range,
					'title'=>'',
					'send_unix'=>true
				)
			);

			$content .=$eventon->evo_generator->eventon_generate_events($args__);
			
			$content .=$eventon->evo_generator->calendar_shell_footer();
			
			echo json_encode(array('content'=>$content));
			exit;

	}
	/* dynamic styles */
		/*function eventon_dymanic_css(){
			//global $foodpress_menus;
			require('admin/inline-styles.php');
			exit;
		}*/

}
new evo_ajax();