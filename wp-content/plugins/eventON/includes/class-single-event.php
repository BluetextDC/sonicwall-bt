<?php
/**
 * Single Event Template Related Class
 * Initiated only on single event page
 * @version 2.5.4
 */
class evo_sinevent{

	public $RI = 0;
	public $L = 'L1';

	public function __construct(){
		$this->evo_opt = get_option('evcal_options_evcal_1');

		// single event template hooks
		add_action('eventon_before_main_content', array($this, 'before_main_content'), 10);
		add_action('eventon_single_content_wrapper', array($this, 'content_wrapper'), 10);

		add_action('eventon_single_content', array($this, 'after_content'), 10);
		add_action('eventon_single_after_loop', array($this, 'after_content_loop'), 10);
		add_action('eventon_single_sidebar', array($this, 'sidebar_placement'), 10);
		add_action('eventon_after_main_content', array($this, 'after_main_content'), 10);

		add_action('eventon_oneevent_wrapper', array($this, 'oneevent_wrapper'), 10);
		add_action('eventon_oneevent_evodata', array($this, 'oneevent_evodata'), 10);
		add_action('eventon_oneevent_head', array($this, 'oneevent_head'), 10);
		add_action('eventon_oneevent_repeat_header', array($this, 'oneevent_repeat_header'), 10);
		add_action('eventon_oneevent_event_data', array($this, 'oneevent_event_data'), 10);

		// Set query Repeat instance for page
		global $wp_query;

		if( isset($_GET['ri']))	$this->RI = (int)$_GET['ri'];
		if( isset($_GET['l'])) $this->L = $_GET['l'];

		if(isset($wp_query->query["var"])){
			$url_var = $wp_query->query["var"];
			
			$url_var = explode('.', $url_var);
			$vars = array();
			
			foreach($url_var as $var){
				$split = explode('-', $var);

				// RI
				if($split[0] == 'ri') $this->RI = (int)$split[1];
				if($split[0] == 'l') $this->L = $split[1];
			}
		}
	}

	// hook for single event page
		function before_main_content(){
			$this->page_header();
			EVO()->frontend->load_evo_scripts_styles();		
		}
		function after_content(){
			$this->page_content();
			$this->comments();
		}
		function sidebar_placement(){
			$this->sidebar();
			?><div class="clear"></div><?php
		}
		function after_content_loop(){			
			?></div><!-- #content --><?php
		}
		function after_main_content(){
			get_footer();
		}

		function content_wrapper(){
			?>
			<div class='evo_page_content <?php echo ($this->has_evo_se_sidebar())? 'evo_se_sidarbar':null;?>'>
			<?php
		}

	// hook for one event inside loop
		function oneevent_wrapper(){
			$rtl = evo_settings_check_yn($this->evo_opt, 'evo_rtl');
			?>
			<div id='evcal_single_event_<?php echo get_the_ID();?>' class='ajde_evcal_calendar eventon_single_event evo_sin_page<?php echo $rtl?'evortl':'';?>' >
			<?php
		}
		function oneevent_evodata(){
			?><div class='evo-data' <?php echo $this->get_evo_data();?>></div><?php
		}
		function oneevent_head(){

			$repeati = $this->RI;
			$lang = $this->L;	


			?><div id='evcal_head' class='calendar_header'><p id='evcal_cur'><?php echo $this->get_single_event_header(get_the_ID(), $repeati, $lang);?></p></div><?php
		}
		function oneevent_repeat_header(){
			$repeati = $this->RI;
			$this->repeat_event_header($repeati, get_the_ID() );
		}

		function oneevent_event_data(){
			global $eventon;

			$repeati = $this->RI;
			$lang = $this->L;

			$single_events_args = apply_filters('eventon_single_event_page_data',array());

			$content =  $eventon->evo_generator->get_single_event_data( get_the_ID(), $lang, $repeati, $single_events_args);			
			echo $content[0]['content'];
		}

	function page_header(){
		wp_enqueue_style( 'evo_single_event');	
		global $post;
			
		get_header();
	}

	// page content
		function page_content(){
			global $eventon, $post;

			$cal_args = $eventon->evo_generator->shortcode_args;
			$lang = !empty($cal_args['lang'])? $cal_args['lang']:'L1';

			//_onlyloggedin
			$epmv = get_post_meta($post->ID);

			// only loggedin users can see single events
			$onlylogged_cansee = (!empty($this->evo_opt['evosm_loggedin']) && $this->evo_opt['evosm_loggedin']=='yes') ? true:false;
			$thisevent_onlylogged_cansee = (!empty($epmv['_onlyloggedin']) && $epmv['_onlyloggedin'][0]=='yes')? true:false;

			if( (!$onlylogged_cansee || ($onlylogged_cansee && is_user_logged_in() ) ) && 
				( !$thisevent_onlylogged_cansee || $thisevent_onlylogged_cansee && is_user_logged_in())  
			){				
				eventon_get_template_part( 'content', 'single-event' );	

			}else{
				echo "<p>".evo_lang('You must login to see this event', $lang)."<br/><a class='button' href=". wp_login_url() ." title='".evo_lang('Login', $lang)."'>".evo_lang('Login', $lang)."</a></p>";
			}
		}
	// sidebar 
		function sidebar(){
			// sidebar
			if(!evo_settings_check_yn($this->evo_opt, 'evosm_1')) return false;	
				
			if ( is_active_sidebar( 'evose_sidebar' ) ){

				?>
				<?php //get_sidebar('evose_sidebar'); ?>
				<div class='evo_page_sidebar'>
					<ul id="sidebar">
						<?php dynamic_sidebar( 'evose_sidebar' ); ?>
					</ul>
				</div>
				<?php
			}
		}
		public function has_evo_se_sidebar(){
			return evo_settings_check_yn($this->evo_opt, 'evosm_1')? true: false;
		}

	// comments
		function comments(){
			if(evo_settings_check_yn($this->evo_opt, 'evosm_comments_hide')) return;	
			?>
			<div id='eventon_comments'>
			<?php comments_template( '', true );	?>
			</div>
			<?php
		}

	// redirect script
		function redirect_script(){
			ob_start();
			?>
			<script> 
				href = window.location.href;
				var cleanurl = href.split('#');
				hash =  window.location.hash.substr(1);
				hash_ri = hash.split('=');

				if(hash_ri[1]){
					repeatInterval = parseInt(hash_ri[1]);
					if(href.indexOf('?') >0){
						redirect = cleanurl[0]+'&ri='+repeatInterval;
					}else{
						redirect = cleanurl[0]+'?ri='+repeatInterval;
					}
					window.location.replace( redirect );
				}
			</script>
			<?php

			echo ob_get_clean();
		}

	// get month year for event header
		function get_single_event_header($event_id, $repeat_interval='', $lang='L1'){
			
			$event_datetime = new evo_datetime();
			$pmv = get_post_custom($event_id);

			$adjusted_start_time = $event_datetime->get_int_correct_event_time($pmv,$repeat_interval);					
			$formatted_time = eventon_get_formatted_time($adjusted_start_time);				
			return get_eventon_cal_title_month($formatted_time['n'], $formatted_time['Y'], $lang);
		}
	// get repeat event page header
		function repeat_event_header($ri, $eventid){
			
			$ev_vals = get_post_meta($eventid);

			if( !evo_check_yn($ev_vals, 'evcal_repeat')) return false;

			$repeat_intervals = (!empty($ev_vals['repeat_intervals']))? 
				(is_serialized($ev_vals['repeat_intervals'][0])? unserialize($ev_vals['repeat_intervals'][0]): $ev_vals['repeat_intervals'][0] ) :false;		

			// if there are no repeat intervals or only one interval
			if($repeat_intervals && !is_array($repeat_intervals) && (is_array($repeat_intervals) && count($repeat_intervals)==1)) return false;

			$repeat_count = (count($repeat_intervals)-1)   ;

			// if there is only one time range in the repeats that means there are no repeats
			if($repeat_count == 0) return false;
			$date = new evo_datetime();

			$event_permalink = get_permalink($eventid);
			
			echo "<div class='evose_repeat_header'><p><span class='title'>".evo_lang('This is a repeating event'). "</span>";
			echo "<span class='ri_nav'>";

			// previous link
			if($ri>0){ 
				$prev = $date->get_correct_formatted_event_repeat_time($ev_vals, ($ri-1));
				// /print_r($prev);
				$prev_link = $this->get_repeat_event_url($event_permalink, ($ri-1) );
				echo "<a href='{$prev_link}' class='prev' title='{$prev['start_']}'><b class='fa fa-angle-left'></b><em>{$prev['start_']}</em></a>";
			}

			// next link 
			if($ri<$repeat_count){
				$ri = (int)$ri;
				$next = $date->get_correct_formatted_event_repeat_time($ev_vals, ($ri+1));
				//print_r($next); 
				$next_link = $this->get_repeat_event_url($event_permalink, ($ri+1) );
				echo "<a href='{$next_link}' class='next' title='{$next['start_']}'><em>{$next['start_']}</em><b class='fa fa-angle-right'></b></a>";
			}
			
			echo "</span><span class='clear'></span></p></div>";
		}

		function get_repeat_event_url($permalink, $ri){
			if(strpos($permalink, '?')!== false){ // ? exists
				return $permalink. '&ri='.$ri;
			}else{
				return $permalink. '?ri='.$ri;
			}
		}


		function get_evo_data(){
			$evopt1 = $this->evo_opt;
			$sin_event_evodata = apply_filters('evosin_evodata_vals',array(
				'mapformat'=> (($evopt1['evcal_gmap_format']!='')?$evopt1['evcal_gmap_format']:'roadmap'),
				'mapzoom'=> ( ($evopt1['evcal_gmap_zoomlevel']!='')?$evopt1['evcal_gmap_zoomlevel']:'12' ),
				'mapscroll'=> ( !evo_settings_val('evcal_gmap_scroll' ,$evopt1)?'true':'false'),
				'evc_open'=>'1',
				'mapiconurl'=> ( !empty($evopt1['evo_gmap_iconurl'])? $evopt1['evo_gmap_iconurl']:''),
			));
			$_cd = '';
			foreach ($sin_event_evodata as $f=>$v){
				$_cd .='data-'.$f.'="'.$v.'" ';
			}

			return $_cd;
		}

		function get_event_data($event_id){
			$output = array();

			$output['name'] = get_the_title($event_id);
			return $output;
		}


}

