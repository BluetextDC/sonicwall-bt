<?php

	if ( !isset( $_REQUEST['alertpg'] ) || $_REQUEST['alertpg'] = '' ){
		$alert_pg_num = 1;
	}
	else {
		$alert_pg_num = (int)$_REQUEST['alertpg'];
	}

	$ra = new RARequests();



  // It wasn't there, so regenerate the data and save the transient
  $alerts =  $ra->alert_search( $alert_pg_num ) ;


	$last = calc_last_page( $alerts->totalHits );

	$pn = page_number( $alert_pg_num, $last );

	// need to find a way to handle next page search for the alert search - it is not working at the moment. 
	
	$html = '<div id="results-content-holder">';
	$html .= '<div id="cats-results-holder">';
	$html .= $pn;
	foreach ($alerts->solutions as $al) {
		$alert_sol = json_decode( $ra->get_single_solution( $al->id ) );
		$alert_sol_lastupdate = str_replace('000', '', $alert_sol->lastModifiedDate);
		$html .= '<a class="alert-title-link" href="/support/product-notification/' . slugifyRA($al->title) . '/'.$al->id.'/" id="' . $al->id . '"><div class="alert-block">';
		$html .= '<div class="alert-block-header"><div class="alert-icon-holder">';
		if ( strpos($al->title, 'Notice:') !== false ){
			$html .= '<i style="margin-right:10px;" class="fa fa-exclamation-circle" aria-hidden="true"></i>';
		}
		else {
			$html .= '<i style="margin-right:10px;" class="fa fa-exclamation-triangle" aria-hidden="true"></i>';
		}
		$html .= '</div>'; // ends the icon holder
		$html .= '<div class="alert-title-holder">' . $al->title . '</div>';
		$html .= '<div style="clear: both; "></div></div>'; //ends the alert block header
		$html .= '<div class="alert-last-updated">' . date( 'm/d/Y',  $alert_sol_lastupdate ) . '</div>';
		$html .= '<p class="alert-summary">' . trim_excerpt( $alert_sol->fields[0]->content, 15, false) . '</p>';
		$html .= '</div></a>';
	}

	$html .= $pn;
	$html .= '<div id="search-name" class="alert" style="display: none;"></div>';
	$html .= '</div></div>';

	echo $html;