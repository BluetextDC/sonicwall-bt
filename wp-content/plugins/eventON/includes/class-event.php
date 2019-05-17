<?php
/**
 * Event Class for one event
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVO_Event{
	public $event_id;
	public $ri = 0;
	private $pmv ='';

	public function __construct($event_id, $event_pmv='', $ri = 0){
		$this->event_id = $this->ID = (int)$event_id;
		$this->set_event_data($event_pmv);
		$this->ri = $ri;
	}

	// permalinks
		function get_permalink($ri=0){
			$event_link = get_the_permalink($this->event_id);
			if($ri==0) return $event_link;

			return strpos($event_link, '?')=== false? $event_link.'?ri='.$ri: $event_link.'&ri='.$ri;
		}

	// time and date related
		function is_current_event( $cutoff='end'){
			date_default_timezone_set('UTC');	
			$current_time = current_time('timestamp');

			$event_time = $this->get_event_time($cutoff);
			return $event_time>$current_time? true: false;
		}

		function is_past_event($cutoff = 'end'){
			date_default_timezone_set('UTC');	
			$current_time = current_time('timestamp');

			$event_time = $this->get_event_time($cutoff);

			return $event_time < $current_time? true: false;
		}

	// DATE TIME
		function get_event_time($type='start', $custom_ri=''){
			if($this->is_repeating_event() ){	

				$repeat_interval = !empty($custom_ri)? (int)$custom_ri: (int)$this->ri;
				$intervals = $this->get_prop('repeat_intervals');

				if(sizeof($intervals)>0 && isset($intervals[$repeat_interval])){
					return ($type=='start')? 
						$intervals[$repeat_interval][0]:
						$intervals[$repeat_interval][1];
				}else{
					return ($type=='start')? $this->get_prop('evcal_srow'):$this->get_prop('evcal_erow');
				}
				
			}else{
				return ($type=='start')? $this->get_prop('evcal_srow'):$this->get_prop('evcal_erow');
			}
		}

		function get_start_end_times($custom_ri=''){
			if($this->is_repeating_event() ){	

				$repeat_interval = !empty($custom_ri)? (int)$custom_ri: (int)$this->ri;
				$intervals = $this->get_prop('repeat_intervals');

				if(sizeof($intervals)>0 ){
					return array(
						'start'=> (isset($intervals[$repeat_interval][0])? 
							$intervals[$repeat_interval][0]:
							$intervals[0][0]),
						'end'=> (isset($intervals[$repeat_interval][1])? 
							$intervals[$repeat_interval][1]:
							$intervals[0][1]) ,
					);
				}				
			}

			return array(
				'start'=> $this->get_prop('evcal_srow'),
				'end'=> ( $this->get_prop('evcal_erow')? $this->get_prop('evcal_erow'): $this->get_prop('evcal_srow'))
			);
		}

	// repeating events
		function is_repeating_event(){
			if(!$this->check_yn('evcal_repeat')) return false;
			if(empty($this->pmv['repeat_intervals'])) return false;
			return true;
		}
		function get_repeats(){
			if(empty($this->pmv['repeat_intervals'])) return false;
			return unserialize($this->pmv['repeat_intervals'][0]);
		}
		function get_next_current_repeat($current_ri_index){
			$repeats = $this->get_repeats();

			if(!$repeats) return false;
			
			foreach($repeats as $index=>$repeat){
				if($index<= $current_ri_index) continue;

				if($this->is_current_event($index)) return array('ri'=>$index, 'times'=>$repeat);			
			}

			return false;
		}

	// event post meta values
		private function set_event_data($pmv = ''){
			if(array_key_exists('EVO_props', $GLOBALS) ){
				global $EVO_props;
				if(isset($EVO_props[$this->event_id])){
					$this->pmv = $EVO_props[$this->event_id];
					return true;
				}				
			}

			$this->pmv = (!empty($pmv))? $pmv : get_post_custom($this->event_id);
			$GLOBALS['EVO_props'][$this->event_id] = $this->pmv;
		}
		function get_data(){ return $this->pmv;}
		function get_prop($field){
			if(empty($this->pmv[$field])) return false;
			return maybe_unserialize($this->pmv[$field][0]);
		}

		function set_prop($field, $value, $update = true, $update_obj = false){
			$this->pmv[$field] = $value;

			if($update) update_post_meta($this->ID, $field, $value);
			if($update_obj)	$this->set_event_data();
		}

		function check_yn($field){
			if(empty($this->pmv[$field])) return false;

			if($this->pmv[$field][0]=='yes') return true;
			return false;
		}
		function del_prop($field){
			delete_post_meta($this->ID, $field);
		}
		function set_global(){
			$data = array(
				'id'=>$this->ID,
				'pmv'=>$this->pmv
			);
			$GLOBALS['EVO_Event'] = (object)$data;
		}
		function get_event_post(){
			global $wpdb;

			$results = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID='{$this->event_id}'");
			
			if($results && count($results)>0){
				$results = $results[0];
				$this->author = $results->post_author;
				$this->post_date = $results->post_date;
				$this->content = $results->post_content;
				$this->excerpt = $results->post_excerpt;
			}
		}
		function get_start_unix(){	return (int)$this->get_prop('evcal_srow');	}
		function get_end_unix(){	return (int)$this->get_prop('evcal_erow');	}

	// Location data for an event
		public function get_location_data(){
			$event_id = $this->event_id;
			$location_terms = wp_get_post_terms($event_id, 'event_location');

			if ( $location_terms && ! is_wp_error( $location_terms ) ){

				$output = array();

				$evo_location_tax_id =  $location_terms[0]->term_id;
				$event_tax_meta_options = get_option( "evo_tax_meta");
				
				// check location term meta values on new and old
				$LocTermMeta = evo_get_term_meta( 'event_location', $evo_location_tax_id, $event_tax_meta_options);
				
				// location name
					$output['name'] = stripslashes( $location_terms[0]->name );

				// description
					if(!empty($location_terms[0]->description))
						$output['description'] = $location_terms[0]->description;

				// meta values
				foreach(array(
					'location_address','location_lat','location_lon','evo_loc_img'
				) as $key){
					if(empty($LocTermMeta[$key])) continue;
					$output[$key] = $LocTermMeta[$key];
				}				

				return $output;
				
			}else{
				return false;
			}
		}

}