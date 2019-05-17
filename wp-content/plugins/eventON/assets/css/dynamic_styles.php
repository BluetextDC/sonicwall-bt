<?php
/**
 * dynamic styles for front end
 *
 * @version		0.2
 * @updated 	2.2.24
 * @package		eventon/Styles
 * @author 		AJDE
 */


	// Load variables
	$opt= get_option('evcal_options_evcal_1');
	

	// complete styles array
	$style_array = apply_filters('eventon_inline_styles_array', array(
		array(
			'item'=>'.eventon_events_list .eventon_list_event .desc_trig',
			'css'=>'background-color:#$', 'var'=>'evcal__bgc4',	'default'=>'fafafa'
		),array(
			'item'=>'.eventon_events_list .eventon_list_event .desc_trig:hover',
			'css'=>'background-color:#$', 'var'=>'evcal__bgc4h',	'default'=>'f4f4f4'
		),

		array(
			'item'=>apply_filters('evo_styles_primary_font',
				'.ajde_evcal_calendar .calendar_header p,
				.ajde_evcal_calendar .evcal_evdata_row .evcal_evdata_cell h3.evo_h3,
				.evo_lightbox_content h3.evo_h3,
				body .ajde_evcal_calendar h4.evo_h4, 
				.evo_content_in h4.evo_h4,
				.evo_metarow_ICS .evcal_evdata_cell p a,
				.eventon_events_list .eventon_list_event .evcal_desc span.evcal_desc2, 
				.eventon_list_event .evcal_desc span.evcal_event_title,
				.evcal_desc span.evcal_desc2,	
				.evo_metarow_ICS .evcal_evdata_cell p a, 
				.evo_metarow_learnMICS .evcal_col50 .evcal_evdata_cell p a,
				.eventon_list_event .evo_metarow_locImg p.evoLOCtxt .evo_loc_text_title,		
				.evo_clik_row .evo_h3,
				.evotax_term_card .evotax_term_details h2, 
				.evotax_term_card h3.evotax_term_subtitle,
				.ajde_evcal_calendar .evo_sort_btn,
				.eventon_main_section  #evcal_cur,	
				.ajde_evcal_calendar .calendar_header p.evo_month_title,		
				.ajde_evcal_calendar .eventon_events_list .evcal_month_line p,
				.eventon_events_list .eventon_list_event .evcal_cblock,			
				.ajde_evcal_calendar .evcal_month_line,
				.eventon_event .event_excerpt_in h3'),
			'css'=>'font-family:$', 
			'var'=>'evcal_font_fam',	
			'default'=>"roboto, 'arial narrow'"
		),
		array(
			'item'=>apply_filters('evo_styles_secondary_font',
				'.ajde_evcal_calendar .eventon_events_list p,
				.eventon_events_list .eventon_list_event .evcal_desc span, 
				.evo_pop_body .evcal_desc span,
				.eventon_events_list .eventon_list_event .evcal_desc span.evcal_event_subtitle, 
				.evo_pop_body .evcal_desc span.evcal_event_subtitle,
				.ajde_evcal_calendar .eventon_list_event .event_description .evcal_btn, 
				.evo_pop_body .evcal_btn, .evcal_btn,
				.eventon_events_list .eventon_list_event .cancel_event .evo_event_headers, 
				.evo_pop_body .evo_event_headers.canceled,
				.eventon_events_list .eventon_list_event .evcal_list_a .evo_above_title span,
				.evo_pop_body .evcal_list_a .evo_above_title span,
				.evcal_evdata_row.evcal_event_details .evcal_evdata_cell p,
				#evcal_list .eventon_list_event .evoInput, .evo_pop_body .evoInput,
				.evcal_evdata_row .evcal_evdata_cell p, 
				#evcal_list .eventon_list_event p.no_events,
				.ajde_evcal_calendar .eventon_events_list .eventon_desc_in ul,
				.evoGuideCall em,
				.evo_cal_above span,
				.ajde_evcal_calendar .calendar_header .evo_j_dates .legend,
				.eventon_sort_line p, .eventon_filter_line p'),
			'css'=>'font-family:$', 
			'var'=>'evcal_font_fam_secondary',	
			'default'=>"'open sans', 'arial narrow'"
		),
		array(
			'item'=>'.ajde_evcal_calendar .evo_sort_btn, .eventon_sf_field p, .evo_srt_sel p.fa',
			'css'=>'color:#$', 'var'=>'evcal__sot',	'default'=>'B8B8B8'
		),array(
			'item'=>'.ajde_evcal_calendar .evo_sort_btn:hover',
			'css'=>'color:#$', 'var'=>'evcal__sotH',	'default'=>'d8d8d8'
		),array(
			'item'=>'#evcal_list .eventon_list_event .evcal_desc em',
			'css'=>'color:#$', 'var'=>'evcal__fc6',	'default'=>'8c8c8c'
		),array(
			'item'=>'#evcal_list .eventon_list_event .evcal_desc em a',
			'css'=>'color:#$', 'var'=>'evcal__fc7',	'default'=>'c8c8c8'
		),

		array(
			'item'=> apply_filters('evo_appearance_button_elms','
				#evcal_list .eventon_list_event .event_description .evcal_btn, .evo_pop_body .evcal_btn'),
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal_gen_btn_fc',	'default'=>'ffffff'),
				array('css'=>'background:#$', 'var'=>'evcal_gen_btn_bgc',	'default'=>'237ebd')
			)	
		),
		array(
			'item'=>apply_filters('evo_appearance_button_elms_hover',
				'#evcal_list .eventon_list_event .event_description .evcal_btn:hover, .evo_pop_body .evcal_btn:hover'),
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal_gen_btn_fcx',	'default'=>'fff'),
				array('css'=>'background:#$', 'var'=>'evcal_gen_btn_bgcx',	'default'=>'237ebd')
			)	
		),array(
			'item'=>'.evcal_evdata_row .evcal_evdata_icons i, .evcal_evdata_row .evcal_evdata_custometa_icons i',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__ecI',	'default'=>'6B6B6B'),
				array('css'=>'font-size:$', 'var'=>'evcal__ecIz',	'default'=>'18px')
			)	
		),array(
			'item'=>'.evcal_evdata_row .evcal_evdata_cell h3, .evo_clik_row .evo_h3',
			'css'=>'font-size:$', 'var'=>'evcal_fs_001',	'default'=>'18px'
		),array(
			'item'=>'#evcal_list .eventon_list_event .evcal_cblock',
			'css'=>'color:#$', 'var'=>'evcal__fc2',	'default'=>'ABABAB'
		),array(
			'item'=>'.evcal_evdata_row .evcal_evdata_cell h2, .evcal_evdata_row .evcal_evdata_cell h3',
			'css'=>'color:#$', 'var'=>'evcal__fc4',	'default'=>'6B6B6B'
		),array(
			'item'=>'#evcal_list .eventon_list_event .evcal_eventcard p, .ajde_evcal_calendar .eventon_events_list .eventon_desc_in ul',
			'css'=>'color:#$', 'var'=>'evcal__fc5',	'default'=>'656565'
		),array(
			'name'=>'Event Card color',
			'item'=>'.ajde_evcal_calendar #evcal_head.calendar_header #evcal_cur, .ajde_evcal_calendar .evcal_month_line p',
			'css'=>'color:#$', 'var'=>'evcal_header1_fc',	'default'=>'C6C6C6'
		),array(
			'name'=>'Event Card color',
			'item'=>'.eventon_events_list .eventon_list_event .evcal_eventcard, .evcal_evdata_row, .evorow .tbrow, .dark1, .evo_pop_body',
			'css'=>'background-color:#$', 'var'=>'evcal__bc1',	'default'=>'f5f5f5'
		),array(
			'item'=>'.dark1:hover',
			'css'=>'background-color:#$', 'var'=>'evcal__bc1H',	'default'=>'d8d8d8'
		)

		//border color for event card
		,array(
			'item'=>'.event_description .bordb, #evcal_list .bordb, .eventon_events_list .eventon_list_event .event_description, .bordr, #evcal_list, .evo_pop_body .bordb',
			'css'=>'border-color:#$', 'var'=>'evcal__evcbrb',	'default'=>'e5e5e5'
		)
		//eventtop
		,array(
			'item'=>'.eventon_events_list .eventon_list_event .evcal_list_a:after,  .evcal_list_a:after',
			'css'=>'background-color:#$', 'var'=>'evcal__evcbrb0',	'default'=>'cdcdcd'
		),array(
			'item'=>'.eventon_events_list .eventon_list_event .evcal_list_a.featured_event',
			'css'=>'background-color:#$', 'var'=>'evcal__bgc5',	'default'=>'F9ECE4'
		),array(
			'item'=>'.eventon_events_list .eventon_list_event .evcal_list_a.featured_event:hover',
			'css'=>'background-color:#$', 'var'=>'evcal__bgc5h',	'default'=>'FAE4D7'
		),array(
			'item'=>'#eventon_loadbar_section',
			'css'=>'border-color:#$', 'var'=>'evcal__evcbrb0',	'default'=>'cdcdcd'
		)
			/* featured events tag */
			,array(
				'item'=>'.eventon_events_list .eventon_list_event .evcal_desc span.evo_above_title span.featured, .evo_pop_body .evcal_desc span.evo_above_title span.featured',
				'multicss'=>array(
					array('css'=>'color:#$', 'var'=>'fs_eventtop_featured_2',	'default'=>'ffffff'),
					array('css'=>'background-color:#$', 'var'=>'fs_eventtop_featured_1',	'default'=>'ffcb55')
				)			
			)

		// close button for eventcard
		,array(
			'item'=>'.event_description .evcal_close',
			'css'=>'background-color:#$', 'var'=>'evcal_closebtn',	'default'=>'eaeaea'
		),array(
			'item'=>'.event_description .evcal_close:hover',
			'css'=>'background-color:#$', 'var'=>'evcal_closebtnx',	'default'=>'c7c7c7'
		)

		// get directions section
		,array(
			'item'=>'#evcal_list .evorow.getdirections, .evo_pop_body .evorow.getdirections',
			'css'=>'background-color:#$', 'var'=>'evcal_getdir_001',	'default'=>'ffffff'
		),array(
			'item'=>'#evcal_list .evorow.getdirections .evoInput, .evo_pop_body .evorow.getdirections .evoInput',
			'css'=>'color:#$', 'var'=>'evcal_getdir_002',	'default'=>'888888'
		),array(
			'item'=>'#evcal_list .evorow.getdirections .evcalicon_9 i, .evo_pop_body .evorow.getdirections .evcalicon_9 i',
			'css'=>'color:#$', 'var'=>'evcal_getdir_003',	'default'=>'858585'
		)

		,array(
			'name'=>'Event title color',
			'item'=>'#evcal_list .eventon_list_event .evcal_desc span.evcal_event_title, .evo_popup .eventon_events_list .eventon_list_event .evcal_desc span.evcal_event_title',
			'css'=>'color:#$', 'var'=>'evcal__fc3',	'default'=>'6B6B6B'
		),array(
			'name'=>'Event sub title color',
			'item'=>'.eventon_events_list .eventon_list_event .evcal_desc span.evcal_event_subtitle, .evo_pop_body .evcal_desc span.evcal_event_subtitle',
			'css'=>'color:#$', 'var'=>'evcal__fc3st',	'default'=>'6B6B6B'
		),array(
			'item'=>'.fp_popup_option i',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'fp__f1',	'default'=>'999'),
				array('css'=>'font-size:$', 'var'=>'fp__f1b',	'default'=>'22px')
			)			
		),array(
			'item'=>'.evo_cal_above span',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__jm001',	'default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm002',	'default'=>'ADADAD')
			)			
		),array(
			'item'=>'.evo_cal_above span:hover',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__jm001H','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm002H',	'default'=>'C8C8C8')
			)			
		),
		// this month button
			array(
				'item'=>'.evo_cal_above span.evo-gototoday-btn',
				'multicss'=>array(
					array('css'=>'color:#$', 'var'=>'evcal__thm001',	'default'=>'ffffff'),
					array('css'=>'background-color:#$', 'var'=>'evcal__thm002',	'default'=>'ADADAD')
				)			
			),array(
				'item'=>'.evo_cal_above span.evo-gototoday-btn:hover',
				'multicss'=>array(
					array('css'=>'color:#$', 'var'=>'evcal__thm001H','default'=>'ffffff'),
					array('css'=>'background-color:#$', 'var'=>'evcal__thm002H',	'default'=>'d3d3d3')
				)			
			),
		array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evo_j_dates .legend a',
			'css'=>'color:#$', 'var'=>'evcal__jm003','default'=>'a0a09f'					
		),
		array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evo_j_dates .legend',
			'css'=>'background-color:#$', 'var'=>'evcal__jm004',	'default'=>'f5f5f5'
		)
		,array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evo_j_dates .legend a:hover',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__jm003H','default'=>'a0a09f'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm004H',	'default'=>'e6e6e6')
			)			
		),array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evo_j_dates .legend a.current',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__jm006','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm007',	'default'=>'CFCFCF')
			)			
		),array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evo_j_dates .legend a.set',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__jm008','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm009',	'default'=>'f79191')
			)			
		),array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evcal_arrows',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__jm010','default'=>'e2e2e2'),
				array('css'=>'border-color:#$', 'var'=>'evcal__jm010','default'=>'e2e2e2'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm011','default'=>'ffffff'),				
			)			
		),array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evcal_arrows:hover',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__jm010H','default'=>'e2e2e2'),
				array('css'=>'border-color:#$', 'var'=>'evcal__jm010H','default'=>'e2e2e2'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm011H','default'=>'e2e2e2'),			
			)			
		),array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evcal_arrows .fa',
			'css'=>'color:#$', 'var'=>'evcal__jm01A','default'=>'e2e2e2'
		),array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evcal_arrows:hover .fa',
			'css'=>'color:#$', 'var'=>'evcal__jm01AH','default'=>'e2e2e2'
		)
		,array(
			'item'=>'.eventon_events_list .eventon_list_event .evcal_desc span.evo_above_title span, .evo_pop_body .evcal_desc span.evo_above_title span',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'fs_eventtop_tag_2','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'fs_eventtop_tag_1','default'=>'F79191'),			
			)			
		),array(
			'item'=>'.eventon_events_list .eventon_list_event .evcal_desc span.evo_above_title span.canceled, .evo_pop_body .evcal_desc span.evo_above_title span.canceled',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__cancel_event_2','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'evcal__cancel_event_1','default'=>'F79191'),			
			)			
		)
		,array(
			'item'=>'.eventon_events_list .eventon_list_event .cancel_event.evcal_list_a',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__cancel_event_2','default'=>'ffffff'),
				array('replace'=>'background:repeating-linear-gradient(45deg,#$0,#$0 10px,#$1 10px,#$1 20px)', 'var'=>array('evcal__cancel_event_3', 'evcal__cancel_event_4'),'default'=>array('FDF2F2','FAFAFA')),			
			)			
		),
		/* loader */
		array(
			'item'=>'#eventon_loadbar',
			'css'=>'background-color:#$', 'var'=>'evcal_loader_001','default'=>'efefef',
		),array(
			'item'=>'#eventon_loadbar:before',
			'css'=>'background-color:#$', 'var'=>'evcal_loader_002','default'=>'f5b87a',
		),
		/* event top */
		array(
			'item'=>'.evcal_desc .evcal_desc3 em.evocmd_button, #evcal_list .evcal_desc .evcal_desc3 em.evocmd_button',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evoeventtop_cmd_btnA','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'evoeventtop_cmd_btn','default'=>'237dbd'),			
			)			
		),

		// single events
			array(
				'item'=>'.evo_metarow_socialmedia a.evo_ss',
				'multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evose_3','default'=>'f5f5f5'),
				)						
			),array(
				'item'=>'.evo_metarow_socialmedia a.evo_ss:hover',
				'multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evose_4','default'=>'9d9d9d'),
				)						
			),array(
				'item'=>'.evo_metarow_socialmedia a.evo_ss i',
				'multicss'=>array(
					array('css'=>'color:#$', 'var'=>'evose_1','default'=>'858585')
				)						
			),array(
				'item'=>'.evo_metarow_socialmedia a.evo_ss:hover i',
				'multicss'=>array(
					array('css'=>'color:#$', 'var'=>'evose_2','default'=>'ffffff')
				)						
			),array(
				'item'=>'.evo_metarow_socialmedia .evo_sm',
				'css'=>'border-color:#$', 'var'=>'evose_5','default'=>'cdcdcd'
			),

		// search
			array(
				'item'=>'body .EVOSR_section a.evo_do_search, body a.evosr_search_btn, .evo_search_bar_in a.evosr_search_btn',
				'css'=>'color:#$', 'var'=>'evosr_4',	'default'=>'3d3d3d'
			),array(
				'item'=>'body .EVOSR_section a.evo_do_search:hover, body a.evosr_search_btn:hover, .evo_search_bar_in a.evosr_search_btn:hover',
				'css'=>'color:#$', 'var'=>'evosr_5',	'default'=>'bbbbbb'
			),array(
				'item'=>'.EVOSR_section input, .evo_search_bar input','multicss'=>array(
					array('css'=>'border-color:#$', 'var'=>'evosr_1','default'=>'ededed'),
					array('css'=>'background-color:#$', 'var'=>'evosr_2','default'=>'ffffff')
				)	
			),array(
				'item'=>'.evosr_blur','multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evosr_6','default'=>'f9d789'),
					array('css'=>'color:#$', 'var'=>'evosr_7','default'=>'14141E')
				)	
			),array(
				'item'=>'.evo_search_results_count span','multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evosr_9','default'=>'d2d2d2'),
					array('css'=>'color:#$', 'var'=>'evosr_10','default'=>'f9d789')
				)	
			),array(
				'item'=>'.EVOSR_section input:hover, .evo_search_bar input:hover',
				'css'=>'color:#$', 'var'=>'evosr_3',	'default'=>'c5c5c5'
			),array(
				'item'=>'.evo_search_results_count',
				'css'=>'color:#$', 'var'=>'evosr_8',	'default'=>'14141E'
			),
	));

	
	if(sizeof($style_array)>0){
		foreach($style_array as $sa){
			if(!empty($sa['multicss']) && is_array($sa['multicss'])){

				echo $sa['item'].'{';

				foreach($sa['multicss'] as $sin_CSS){
					if(!empty($sin_CSS['replace'])){
						$css = $sin_CSS['replace'];
						foreach($sin_CSS['var'] as $index=>$var){
							$css_val = (!empty($opt[ $var] ))? 
								$opt[ $var ] : $sin_CSS['default'][$index];

							$css = str_replace('$'.$index ,$css_val, $css );
						}
						
						echo $css.';';
					}else{
						$css_val  = (!empty($opt[ $sin_CSS['var'] ] ))? $opt[ $sin_CSS['var'] ] : $sin_CSS['default'];
						$css = str_replace('$',$css_val,$sin_CSS['css'] );
						echo $css.';';
					}
					
				}
				echo '}';
			}else{
				$css_val  = (!empty($opt[ $sa['var'] ] ))? $opt[ $sa['var'] ] : $sa['default'];
				$css = str_replace('$',$css_val,$sa['css'] );
				echo $sa['item'].'{'.$css.'}';
			}
		}
	}
	

	// more less gradient
	$_grad_rgb = (!empty($opt['evcal__bc1_rgb']))? $opt['evcal__bc1_rgb']: '245,245,245';
	$_grad_hex = (!empty($opt['evcal__bc1']))? $opt['evcal__bc1']: 'f5f5f5';

	// if hex code passed instead of rgb
	if( strpos($_grad_rgb, ',')=== false){
		$_grad_rgb = eventon_hex2rgb($_grad_rgb);
	}


	echo ".evcal_event_details .evcal_evdata_cell .eventon_details_shading_bot{
		background: -moz-linear-gradient(top,  rgba({$_grad_rgb},0) 0%, rgba({$_grad_rgb},0.01) 1%, rgba({$_grad_rgb},1) 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba({$_grad_rgb},0)), color-stop(1%,rgba({$_grad_rgb},0.01)), color-stop(100%,rgba({$_grad_rgb},1))); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  rgba({$_grad_rgb}4,0) 0%,rgba({$_grad_rgb},0.01) 1%,rgba({$_grad_rgb},1) 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  rgba({$_grad_rgb},0) 0%,rgba({$_grad_rgb},0.01) 1%,rgba({$_grad_rgb},1) 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  rgba({$_grad_rgb},0) 0%,rgba({$_grad_rgb},0.01) 1%,rgba({$_grad_rgb},1) 100%); /* IE10+ */
background: linear-gradient(to bottom,  rgba({$_grad_rgb},0) 0%,rgba({$_grad_rgb},0.01) 1%,rgba({$_grad_rgb},1) 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00{$_grad_hex}', endColorstr='#{$_grad_hex}',GradientType=0 ); /* IE6-9 */
	}";	

	
	// STYLES
	echo (!empty($opt['evo_ftimgheight']))?
			".evcal_evdata_img{height:".$opt['evo_ftimgheight']."px}":null ;
		
		if(!empty($opt['evcal__fc6'])){
			echo "#evcal_widget .eventon_events_list .eventon_list_event .evcal_desc .evcal_desc_info em{
				color:#". $opt['evcal__fc6']."
			}";
		}
		
		// featured event styles
		if(!empty($opt['evo_fte_override']) && $opt['evo_fte_override']=='yes'){
			echo "#evcal_list .eventon_list_event .evcal_list_a.featured_event{border-left-color:#".eventon_styles('ca594a','evcal__ftec', $opt)."!important;}";
		}

	// (---) Hook for addons
	do_action('eventon_inline_styles');
	
	echo get_option('evcal_styles');