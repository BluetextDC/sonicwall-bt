<?php

//Load the new, enhanced KB request class
require_once('kb-requests.php');
//Load the legacy, Russel RA request class
require_once('ra-requests.php');

function setRACacheItem($key, $data, $time = 900)
{
    return set_transient( $key, $data, $time );
}
     
function getRACacheItem($key)
{
    return get_transient( $key );
}


function calc_last_page( $totalhits ){

	$calc = $totalhits / 10;
	$pagenum = ceil($calc);

	return $pagenum; 
}


function page_number( $cp, $lp ){

	$pnums = '<div class="page-numbers">';
	if ( $cp > 1) {
		$pnums .= '<a href="#" id="prev-page-link" class="prev search-nav-link">&#60; Previous</a>&nbsp;';
	}
	$pnums .= '<span id="curpage">' . $cp . '</span> of <span id="lastpage">' . $lp . '</span> ';
	if ( $cp != $lp ){
		$pnums .= '<a href="#" id="next-page-link" class="next search-nav-link">Next &#62;</a>';
	}
	$pnums .= '</div>';
	return $pnums;
}

function trim_excerpt($raw_content, $trim_limit, $full_sent = true) {
    	
    $wpse_excerpt = strip_tags($raw_content); /*IF you need to allow just certain tags. Delete if all tags are allowed */

	$tokens = array();
    $excerptOutput = '';
    $count = 0;

    // Divide the string into tokens; HTML tags, or words, followed by any whitespace
    preg_match_all('/(<[^>]+>|[^<>\s]+)\s*/u', $wpse_excerpt, $tokens);

    if ( $full_sent == false ){
    	foreach ($tokens[0] as $tk) {
    		if ( $count < $trim_limit ){
    			$excerptOutput .= $tk;
    			$count++;
    		}
    		
    	}
    	$excerptOutput .= ' ... ';
    	return $excerptOutput;
    }
    else {

    	foreach ($tokens[0] as $token) { 

	        if ($count >= $trim_limit && preg_match('/[\?\.\!]\s*$/uS', $token)) { 
	        // Limit reached, continue until , ; ? . or ! occur at the end
	            $excerptOutput .= trim($token);
	            break;
	        }

	        // Add words to complete sentence
	        $count++;

	        // Append what's left of the token
	        $excerptOutput .= $token;
	    }

    }    

    $wpse_excerpt = trim(force_balance_tags($excerptOutput));

    return $wpse_excerpt;   

}

function category_slug_translator($slug) {
    
    $ra = new KBRequests();
    $categories = $ra->get_ra_categories();

    if ($categories && $categories[0] && $categories[0]->value && $categories[0]->value == "Alerts")
    {
        array_shift($categories);
    }
    
    foreach ($categories as $category)
    {
        if ($ra->build_category_slug($category) == $slug)
        {
            return $category->value;
        }
    }
    
    return false;
}

function cat_translator($cid) {
    
    
    $ra = new KBRequests();
    $categories = $ra->get_ra_categories();

    if ($categories && $categories[0] && $categories[0]->value && $categories[0]->value == "Alerts")
    {
        array_shift($categories);
    }
    
    $cid = intval($cid);
    
    if (isset($categories[$cid]))
    {
        return $categories[$cid]->value;
    }
    else
    {
        return $categories[0]->value;
    }

}

if ( is_admin() ){
	add_action( 'wp_ajax_np_alert_search', 'ajax_alerts' );
	add_action( 'wp_ajax_nopriv_np_alert_search', 'ajax_alerts' );
	add_action( 'wp_ajax_get_ra_cat', 'ajax_cats' );
	add_action( 'wp_ajax_nopriv_get_ra_cat', 'ajax_cats' );
	add_action( 'wp_ajax_get_filter_menu', 'ajax_filter_menu' );
	add_action( 'wp_ajax_nopriv_get_filter_menu', 'ajax_filter_menu' );
	add_action( 'wp_ajax_gen_ra_search', 'ajax_general_ra_search' );
	add_action( 'wp_ajax_nopriv_gen_ra_search', 'ajax_general_ra_search' );
	add_action( 'wp_ajax_single_solve', 'ajax_single_search' );
	add_action( 'wp_ajax_nopriv_single_solve', 'ajax_single_search' );
	add_action( 'wp_ajax_single_upvote', 'ajax_upvote_answer' );
	add_action( 'wp_ajax_nopriv_single_upvote', 'ajax_upvote_answer' );
    add_action( 'wp_ajax_single_downvote', 'ajax_downvote_answer' );
    add_action( 'wp_ajax_nopriv_single_downvote', 'ajax_downvote_answer' );
	add_action( 'wp_ajax_video_popup', 'ajax_video_popup' );
	add_action( 'wp_ajax_nopriv_video_popup', 'ajax_video_popup' );

}

function ajax_video_popup( $vid = null ){

	$vidplayer = '';

	if ( $vid == null ){
		$video = $_REQUEST['video_id'];
		$vidplayer = '<div class="video-player">';
	}
	else {
		$video = $vid;
	}

	$vidplayer .= do_shortcode( '[bc_video video_id="' . $video . '" account_id="5380177764001" player_id="default" embed="in-page" padding_top="56%" autoplay="" min_width="0px" max_width="720px" mute="" width="100%" height="100%"]' );
	

	if ( $vid == null ){
		$vidplayer .= '</div>';
		echo $vidplayer;
		wp_die();
	}
	else {
		return $vidplayer;
	}

}

function solLink($solution)
{
    if ($solution && isset($solution->title) && isset($solution->id))
    {
        return slugifyRA($solution->title)."/".$solution->id."/";
    }
    else if ($solution && isset($solution->title))
    {
        return slugifyRA($solution->title)."/";
    }
    else if ($solution && isset($solution->id))
    {
        return "?sol_id=".$solution->id;
    }
    else
    {
        return "";
    }
}

function ajax_alerts(){

	$ra = new KBRequests();
	$get_the_alerts = $ra->alert_search( $_REQUEST['cpg'] );

	$alert_source = $get_the_alerts;

	$last = calc_last_page( $alert_source->totalHits );

	$pn = page_number( $_REQUEST['cpg'], $last );
    

    
	$alert_html = $pn;
	foreach ($alert_source->solutions as $al_sol) {
		$alert_solu = json_decode($ra->get_single_solution( $al_sol->id ));
        
		$alert_solu_lastupdate = str_replace('000', '', $alert_solu->lastModifiedDate);
		$alert_html .= '<a class="alert-title-link" href="/support/product-notification/'.solLink($al_sol).'" id="' . $al_sol->id . '"><div class="alert-block">';
		$alert_html .= '<div class="alert-block-header"><div class="alert-icon-holder">';
		if ( strpos($al_sol->title, 'Notice:') !== false ){
			$alert_html .= '<i style="margin-right:10px;" class="fa fa-exclamation-circle" aria-hidden="true"></i>';
		}
		else {
			$alert_html .= '<i style="margin-right:10px;" class="fa fa-exclamation-triangle" aria-hidden="true"></i>';
		}
		$alert_html .= '</div>'; // ends the icon holder
		$alert_html .= '<div class="alert-title-holder">' . $al_sol->title . '</div>';
		$alert_html .= '<div style="clear: both; "></div></div>'; //ends the alert block header
		$alert_html .= '<div class="alert-last-updated">' . date( 'm/d/Y',  $alert_solu_lastupdate ) . '</div>';
		$alert_html .= '<p class="alert-summary">' . trim_excerpt( $alert_solu->fields[0]->content, 15, false) . '</p>';
		$alert_html .= '</div></a>';
	}

	// $alert_html .= '<form id="single-alert-search" method="get" action="/support/product-notifications"><input type="hidden" name="alertpg" id="alert-page-number" value="" /></form>';

	$alert_html .= $pn;
	$alert_html .= '<div id="search-name" class="alert" style="display: none;"></div>';
	

	echo $alert_html;
	wp_die();
}


function ajax_cats( $spit = false ){
	$get_the_cats = show_ra_cat( $_REQUEST['cnam'], $_REQUEST['cpg'], true );
	if ( $spit ){
		return $get_the_cats;
	}
	else {
		echo $get_the_cats;
		wp_die();
	}
	
}

function ajax_filter_menu( $spit = false ){
	$get_the_filter_menu = drilldown_menu( $_REQUEST['curpath'] );
	if ( $spit ){
		return $get_the_filter_menu;
	} 
	else {
		if ( $get_the_filter_menu ){
			echo $get_the_filter_menu;
			wp_die();
		}
		else {
			echo 'false';
			wp_die();
		}
	}
}

function drilldown_menu( $cpaths ) {
	$catname = rawurlencode( $cpaths );
	$ra = new KBRequests();
    $language = $ra->getRALanguage();
    
    $drilldown_key = 'ra_'.$cpaths . '_' . $language . '_drilldown';
    
    if ( false === ( $filter_menu = getRACacheItem($drilldown_key) ) ) {
		  // It wasn't there, so regenerate the data and save the transient

	$cats = json_decode( $ra->search_by_category( $catname, 1 ) );

	$drill_down = array();
	if ( $cats->browsePaths !== null ){
		foreach ($cats->browsePaths as $bp) {	
			// $bpname = str_replace('//', '',  stristr($bp->value, '//') );
			$bpname = $bp->value;
			$bpaths = json_decode( $ra->search_by_category( rawurlencode($bp->value), 1 ) ); 
			// var_dump($bpaths);

			if ( $bpaths->browsePaths == null ){
				$drill_down[$bpname] = $bp->value;
			}
			else {
				foreach ($bpaths->browsePaths as $path) {
					$drill_down[$bpname][] = $path->value;
				}
			}
		}
		$filter_menu = '';

		$filter_menu .= '<div id="support-home-link"><button id="support-home-url">Support Home Page</button></div>';

		// $filter_menu .= '<div id="filter-sidebar" class="one-quarter">';
		$filter_menu .= '<p><strong>Filter By: </strong></p>';	
		$filter_menu .= '<select id="sub-category-list" class="filter-list">';
		$filter_menu .= '<option value="' . $cpaths . '">' . $cpaths . '</option>';
		foreach ($drill_down as $dkey => $dd) {
			$filter_name = $filter_surname = '';
			$filter_surname = $cpaths;
			if ( is_array( $dd ) ) {
				// $dd_arr = explode('//', $dd);
				$filter_top_cat = str_replace($cpaths . '//', '&nbsp;&nbsp;', $dkey);
				$filter_menu .= '<option value="' . $dkey . '"> ' . $filter_top_cat . '</option>';
					foreach ($dd as $d) {
						$d_arr = explode('//', $d);
						$filter_name = '&nbsp;&nbsp;&nbsp;&nbsp;' . end($d_arr);
						$filter_menu .= '<option value="' . $d . '"> ' . $filter_name . '</option>';
					}
			}
			else {
				$dd_arr = explode('//', $dd);
				$filter_name = '&nbsp;&nbsp;' . end($dd_arr);
				$filter_menu .= '<option value="' . $dd . '"> ' . $filter_name . '</option>';
			}
		}
		$filter_menu .= '</select>';
		// $filter_menu .= '</div>';

		$filter_menu .= '<p><strong>Jump To Category: </strong></p>';
		$filter_menu .= '<select id="main-category-list" class="cat-list">';
		$filter_menu .= '<option value="">Choose Category</option>';
        
        $categories = $ra->get_ra_categories();

        if ($categories && $categories[0] && $categories[0]->value && $categories[0]->value == "Alerts")
        {
            array_shift($categories);
        }
    
        foreach ($categories as $category)
        {
            $filter_menu .= '<option value="'.$ra->build_category_slug($category).'">'.$category->value.'</option>';
        }

		$filter_menu .= '</select>';
        
        setRACacheItem($drilldown_key, $filter_menu);
        
		return $filter_menu;
	}
	
    }
    
    return $filter_menu;

	
}

function show_ra_cat( $cat_name, $curpg, $jax = false, $preload = false ){

    if (!$curpg)
    {
        $curpg = 1;
    }
    
    $ra = new KBRequests();
    
    $language = $ra->getRALanguage();
    
    $main_key = 'ra_'.$language.$cat_name . '_' . $curpg . '_main_data';
    
    $answer_form = getRACacheItem($main_key);
         
    if ( $answer_form === false || $preload ) {
		  // It wasn't there, so regenerate the data and save the transient
		      
        $cat_title = str_replace('//', ' > ', $cat_name );

        $encoded_cat_name = rawurlencode( $cat_name );

        $cats = json_decode( $ra->search_by_category( $encoded_cat_name, $curpg ) );

        $lpage = calc_last_page( $cats->totalHits );

        $pn = page_number($curpg, $lpage);	

        if ( $cats->solutions !== NULL ){
            $answer_form = '';

            $answer_form .= '<h4 class="search-results-header">CATEGORY: ' . $cat_title . '</h4>';

            foreach ($cats->solutions as $cat) {

                $cat_sol = json_decode( $ra->get_single_solution( $cat->id ) );
                $lastupdate = str_replace('000', '', $cat_sol->lastModifiedDate);

                $language = $cat->language; 
                $answer_form .= '<p>' . $language . '</p>';

                // print_r($cat);

                $answer_form .= '<a href="/support/knowledge-base/'.solLink($cat).'" id="' . $cat->id . '" class="result_link">' . $cat->title . '</a>';
                $answer_form .= '<p class="results-data-relevance"><span style="margin-right: 25px;"><img src="'. plugins_url('/img/calendar.png', __FILE__) . '"> ' . date( 'm/d/Y', $lastupdate ) . '</span> <span style="margin-right: 25px;"><img src="'. plugins_url('/img/thumbs-up.png', __FILE__) . '"> ' . $cat_sol->solvedCount . '</span> <img src="'. plugins_url('/img/eye-con.png', __FILE__) . '"> ' . $cat_sol->viewCount . '</p>';
                $answer_form .= '<p class="results-excerpt">' . trim_excerpt( $cat_sol->fields[0]->content, 150 ) . '</p>';
                $answer_form .= '<p class="product-list">Product(s):<br />';
                foreach ($cat_sol->taxonomy as $cstax) {
                    $answer_form .= str_replace('//', '>', $cstax ) . '<br />';
                }
                $answer_form .= '<hr />';

            }

        }
        $answer_form .= '<div id="search-name" class="ra-cat-search" style="display: none;">' . $cat_name . '</div>';
        $answer_form .= $pn;
        
        $cache_time = 900;
        
        if ($preload)
        {
            //Set the cache_time to 24 hours
            $cache_time = 86400;
        }
        
        setRACacheItem($main_key, $answer_form, $cache_time);
    }
   
    

	return $answer_form;
	
}


function redirect_support_home()
{
    //Redirect to support home
    $lang_home_url = apply_filters( 'wpml_home_url', get_option( 'home' ) );
    if (!(substr($lang_home_url, -strlen("/")) === "/"))
    {
        $lang_home_url = $lang_home_url."/";
    }
    $url = $lang_home_url."support";
    if (wp_redirect($url, 301))
    {
        exit();
    }
}
function ajax_general_ra_search( $spit = false ){
	$search_results = general_ra_search( $_REQUEST['qterm'], $_REQUEST['cpage'] );
	if ( $spit ){
		return $search_results;
	}
	else {
		echo $search_results;
		wp_die();
	}
	
}

function general_ra_search( $qterm, $cpag ) {

	$clean_search_term = strip_tags( $qterm );
	$search_term = rawurlencode( $clean_search_term );
	// $search_term = htmlentities($search_term);

	$ra = new KBRequests();
	$search = $ra->search_for_solution($search_term, $cpag );

    
	$search_results = json_decode( $search );
    
    $language = $ra->getRALanguage();
    
    if ($search_results->solutions === NULL && $language != "English")
    {
        $search = $ra->search_for_solution($search_term, $cpag, "English" );
	    $search_results = json_decode( $search );
    }
    
	if ( $search_results->solutions === NULL ){
		$favs = $ra->get_frequent_searches();
		$favs = json_decode( $favs );
	}

	$lpage = calc_last_page( $search_results->totalHits );

	$pn = page_number($cpag, $lpage);

	$answer_form = '';
    		

	if ( $search_results->solutions !== NULL ){
		// $answer_form = $pn;
		$answer_form .= '<h4 class="search-results-header">SEARCH RESULTS: ' . $clean_search_term . '</h4>';
		foreach ($search_results->solutions as $res) {

			$res_sol = json_decode( $ra->get_single_solution( $res->id ) );
			$res_sol_lastupdate = str_replace('000', '', $res_sol->lastModifiedDate);

			$answer_form .= '<a href="/support/knowledge-base/'.solLink($res). '" id="' . $res->id . '" class="result_link">' . $res->title . '</a>';
			$answer_form .= '<p class="results-data-relevance"><span style="margin-right: 25px;"><img src="'. plugins_url('/img/calendar.png', __FILE__) . '"> ' . date( 'm/d/Y', $res_sol_lastupdate ) . '</span> <span style="margin-right: 25px;"><img src="'. plugins_url('/img/thumbs-up.png', __FILE__) . '"> ' . $res_sol->solvedCount . '</span> <img src="'. plugins_url('/img/eye-con.png', __FILE__) . '"> ' . $res_sol->viewCount . '</p>';
			$answer_form .= '<p class="results-excerpt">' . trim_excerpt( $res_sol->fields[0]->content, 150 ) . '</p>';
			$answer_form .= '<p class="product-list">Product(s):<br />';
			foreach ($res_sol->taxonomy as $rstax) {
				$answer_form .= str_replace('//', '>', $rstax) . '<br />';
			}
			
			$answer_form .= '<hr />';
		}
		$answer_form .= '<div id="search-name" class="gen-ra-search" style="display: none;">' . $clean_search_term . '</div>';
		$answer_form .= $pn;
		return $answer_form;
		
	}
	else {
		$answer_form .= '<p>No results found for that search.</p><p>Perhaps one of the articles below will be helpful.</p>';
		if ( isset( $favs ) && !empty( $favs ) ){
			foreach ($favs as $fav) {

                $solution_id = $fav->solutionID;
                
                if (!$solution_id)
                {
                    $solution_id = $fav->id;
                }
                
                $description = $fav->description;
                
                if (!$description)
                {
                    $description = $fav->title;
                }
                
				$fav_sol = json_decode( $ra->get_single_solution( $solution_id ) );
				$fav_sol_lastupdate = str_replace('000', '', $fav_sol->lastModifiedDate);

				$answer_form .= '<a href="/support/knowledge-base/'.solLink($fav_sol) . '" id="' . $solution_id . '" class="result_link">' . $description . '</a>';
				$answer_form .= '<p>' . strip_tags( $fav_sol->fields[0]->content ) . '</p>';
				$answer_form .= '<p>Product(s):<br />';
				foreach ($fav_sol->taxonomy as $fstax) {
					$answer_form .= str_replace('//', '>', $fstax) . '<br />';
				}
				$answer_form .= 'Last Updated on ' . date( 'm/d/Y', $fav_sol_lastupdate ) . '</p>';
				$answer_form .= '<p class="ra-search-useful"><span style="margin-right: 25px;"><img src="'. plugins_url('/img/calendar.png', __FILE__) . '"> ' . date( 'm/d/Y', $fav_sol_lastupdate ) . ' <img src="'. plugins_url('/img/thumbs-up.png', __FILE__) . '"> ' . $fav_sol->solvedCount . ' <img src="'. plugins_url('/img/eye-con.png', __FILE__) . '">  ' . $fav_sol->viewCount . '</p>';
				$answer_form .= '<hr />';

			}
		}
		return $answer_form;
	}
}

function ajax_single_search( $spit = false ){
	if ( isset( $_REQUEST['alert'] ) && !empty( $_REQUEST['alert'] ) ) {
		$alert = $_REQUEST['alert'];
	}
	else {
		$alert = false;
	}
	$single_sol = single_solution_search( $_REQUEST['sol_id'], $alert );
	if ( !$spit ){
		echo $single_sol->html;
		wp_die();
	}
	else {
		return $single_sol;
	}
	
}

function collections_check( $col_array ){
	if ( count( $col_array ) == 1 ){
		if ( $col_array[0] == 'custom_Support' ){
			return true;
		}
	}
	return false;
}

function single_solution_search( $res_id, $is_alert = false ){
    
        if ($is_alert)
        {
            $key = 'ra_'.$res_id . '_alert';
        }
        else
        {
            $key = 'ra_'.$res_id . '_solution';
        }
    
        
        if ( false === ( $answer_form = getRACacheItem($key) ) ) {
        
    
            $ra = new KBRequests();
            $solution = $ra->get_single_solution( $res_id );
            
            $sol = json_decode( $solution );
            
            //Save the last modified date inside the answerform to access with RA-JS
            $answer_form = '<script>var ra_sol_last_modified = '.$sol->lastModifiedDate.'</script>';

            if ( $sol->status == 'Published' && collections_check( $sol->collections ) == false ){

                $sol_id = $sol->id;
                $lastupdate = str_replace('000', '', $sol->lastModifiedDate);		
                $answer_form .= '<h1 class="result_link">' . $sol->title . '</h1>';
                $answer_form .= '<p class="results-data-relevance"><span style="margin-right: 25px;"><img src="'. plugins_url('/img/calendar.png', __FILE__) . '"> ' . date( 'm/d/Y', $lastupdate ) . '</span>';
                if ( !$is_alert ){
                    $answer_form .= ' <span style="margin-right: 25px;"><img src="'. plugins_url('/img/thumbs-up.png', __FILE__) . '"> ' . $sol->solvedCount . '</span> <img src="'. plugins_url('/img/eye-con.png', __FILE__) . '"> ' . $sol->viewCount;
                }
                $answer_form .= '</p>';
                $answer_form .= '<p class="single-solution-heading">DESCRIPTION: <br />';
                $answer_form .= $sol->fields[0]->content . '</p>';
                if ( !empty( $sol->fields[1]->content ) ){
                    $answer_form .= '<p class="single-solution-heading">CAUSE: <br />';
                    $answer_form .= $sol->fields[1]->content . '</p>';

                }
                if ( !empty( $sol->fields[2]->content ) ) {
                    $answer_form .= '<p class="single-solution-heading">RESOLUTION: <br />';
                    $answer_form .= $sol->fields[2]->content . '</p>';
                }


                if ( !empty( trim( $sol->fields[3]->content ) ) ){
                    $answer_form .= '<p class="single-solution-heading">ISSUE ID: ';
                    $answer_form .= $sol->fields[3]->content;
                    $answer_form .= '</p>';
                }
                else if ( !empty( trim( $sol->fields[4]->content ) ) ){
                    if ( strpos($sol->fields[4]->name, 'Legacy') === false ){
                        $answer_form .= '<p class="single-solution-heading">ISSUE ID: ' . $sol->fields[4]->content . '</p>';
                    }
                }


                if ( !$is_alert ){

                    $answer_form .= '<div class="ra-solution-footer">';

                    $answer_form .= '<div class="ra-product-categories-list">';
                        $answer_form .= '<h4>Categories</h4>';
                        foreach ( $sol->taxonomy as $staxo ) {
                            $answer_form .= '<p class="ra-sol-taxonomy-item">' . str_replace('//', '>', $staxo) . '</p>';
                        }
                    $answer_form .= '</div>';

                    $answer_form .= '<div id="' . $res_id . '" class="ra-helpfulness-voting">';
                        $answer_form .= '<h4>Was This Article Helpful?</h4>';
                    
                        $answer_form .= '<p class="helpful-buttons"><a href="#" class="yes"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 82.3 82.3" style="enable-background:new 0 0 82.3 82.3;" xml:space="preserve"><style type="text/css">.st0{fill:#FFFFFF;}</style><g><circle cx="41" cy="41.3" r="39.8"></circle><path class="st0" d="M66.4,33.8c0-2.1-1.7-3.5-3.9-3.5H43.1c0.9-4,3-8.8,2.4-11.3c-0.9-3.5-2.4-7.1-4.2-8.3s-4.9-0.3-4.9,1.3
        s0,9.2,0,9.2l-9,13.2H16.6l2.2,24.2l8.5,0.3c1.9,1.1,5.7,2.5,10.8,2.5h19.1c2.1,0,3.9-1.9,3.9-4s-1.7-4-3.9-4h1.9
        c2.1,0,3.9-1.9,3.9-4s-1.7-4-3.9-4h2.1c2.1,0,3.9-1.9,3.9-4s-1.7-4-3.9-4h1.3C64.7,37.3,66.4,36,66.4,33.8z"></path></g></svg><span style="color: #000;"> Yes </span></a>'; 
                        $answer_form .= '<a id="RA_article_downvote" class="no"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 82.3 82.3" style="enable-background:new 0 0 82.3 82.3;" xml:space="preserve"><style type="text/css">.st0{fill:#FFFFFF;}</style><g><circle cx="41" cy="41.3" r="39.8"></circle><path class="st0" d="M66.4,33.8c0-2.1-1.7-3.5-3.9-3.5H43.1c0.9-4,3-8.8,2.4-11.3c-0.9-3.5-2.4-7.1-4.2-8.3s-4.9-0.3-4.9,1.3
        s0,9.2,0,9.2l-9,13.2H16.6l2.2,24.2l8.5,0.3c1.9,1.1,5.7,2.5,10.8,2.5h19.1c2.1,0,3.9-1.9,3.9-4s-1.7-4-3.9-4h1.9
        c2.1,0,3.9-1.9,3.9-4s-1.7-4-3.9-4h2.1c2.1,0,3.9-1.9,3.9-4s-1.7-4-3.9-4h1.3C64.7,37.3,66.4,36,66.4,33.8z"></path></g></svg><span style="color: #000;"> No</span></a></p>';
                    $answer_form .= '<div id="ra-upvote-response"></div>';
                    $answer_form .= '<div id="RA_article_downvote_form" style="display: none;">'.do_shortcode('[gravityform id="65" ajax="true" description="false" '. 'field_values="sol_id='.$sol_id.'"' .']').'</div>';
                    $answer_form .= '</div>';

                $answer_form .= '</div>';
                $answer_form .= '<div id="ra-downvote-feedback-form"><div class="ra-not-finding-answers"><h4>Not Finding Your Answer?</h4><a id="ra-request-new-article" data-fancybox data-type="iframe" data-hide-copy-button="true" href="/support/request-new-article-form/"><p class="button">REQUEST NEW ARTICLE</p></a></div></div>';

                }
            }
            else {

                $favs = $ra->get_frequent_searches();
                $favs = json_decode( $favs );

                $answer_form .= '<div id="ra-no-solution-available"><h2>We cannot find the article ' . $res_id . '</h2><p>The site has returned a 404 error. You can view other articles below.</p></div>';

                if ( isset( $favs ) && !empty( $favs ) ){
                    foreach ($favs as $fav) {

                        $fav_sol = json_decode( $ra->get_single_solution( $fav->solutionID ) );
                        $fav_sol_lastupdate = str_replace('000', '', $fav_sol->lastModifiedDate);

                        $answer_form .= '<a href="/support/knowledge-base/'.solLink($fav). '" id="' . $fav->solutionID . '" class="result_link">' . $fav->description . '</a>';
                        $answer_form .= '<p>' . strip_tags( $fav_sol->fields[0]->content ) . '</p>';
                        $answer_form .= '<p>Product(s):<br />';
                        foreach ($fav_sol->taxonomy as $fstax) {
                            $answer_form .= str_replace('//', '>', $fstax) . '<br />';
                        }
                        $answer_form .= 'Last Updated on ' . date( 'm/d/Y', $fav_sol_lastupdate ) . '</p>';
                        $answer_form .= '<p class="ra-search-useful"><span style="margin-right: 25px;"><img src="'. plugins_url('/img/calendar.png', __FILE__) . '"> ' . date( 'm/d/Y', $fav_sol_lastupdate ) . ' <img src="'. plugins_url('/img/thumbs-up.png', __FILE__) . '"> ' . $fav_sol->solvedCount . ' <img src="'. plugins_url('/img/eye-con.png', __FILE__) . '">  ' . $fav_sol->viewCount . '</p>';
                        $answer_form .= '<hr />';

                    }
                }

            }
            
                 $answer_form_html = $answer_form;
            
                 $answer_form = new stdClass();
                 $answer_form->type = $sol->templateName;
                 $answer_form->html = $answer_form_html;
            
                 $answer_form = json_encode($answer_form);
            
                 setRACacheItem($key, $answer_form);
        }

    
		return json_decode($answer_form);
}

function ajax_upvote_answer(){
	$s_id = $_REQUEST['the-sol-id'];
	$ra = new KBRequests();
	$resp = $ra->upvote_answer($s_id);
	if ( $resp == true ){
		echo $resp;
	}
	else {
		echo 'failed';
	}
	wp_die();
}

function ajax_downvote_answer(){
    $downvote_email = $_REQUEST['email_id'];
    $s_id = $_REQUEST['sol_id'];
	$down_comment = $_REQUEST['comment'];
	$_comment = stripslashes($down_comment);
	 $ra = new KBRequests();
    $resp = $ra->downvote_answer($downvote_email, $s_id, $_comment);
    if ( $resp == true ){
        echo $resp;
    }
    else {
        echo 'failed';
    }
    wp_die();
}


	
