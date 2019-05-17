<?php 

	$offset = 0;
	$curpg = 1;
	$no_pages = false;

	$vidcms = new BC_CMS_API();

	$vidclass = new SW_VIDS();

	if ( isset( $_REQUEST['video-taxonomy'] ) && !empty( $_REQUEST['video-taxonomy'] ) ){
		$cur_vid_tax = urlencode( $_REQUEST['video-taxonomy'] );
		$vid_folder = $vidclass->video_taxonomy_search( urlencode( $cur_vid_tax ) );
		// var_dump($vid_folder);
		$no_pages = true;
	}
	else if ( isset( $_REQUEST['page_num'] ) && $_REQUEST['page_num'] != 1 ){
		$curpg = $_REQUEST['page_num'];
		$offset = $curpg * 12;
		$vid_folder = $vidclass->video_folder( '58f7c0359e2ac9106eb62780', $offset );
	}
	else {
		$curpg = 1;
		$offset = 0;
		$vid_folder = $vidclass->video_folder( '58f7c0359e2ac9106eb62780', $offset );
	}
	

	$video_count = $vidclass->video_folder_count( '58f7c0359e2ac9106eb62780' );
	$total_pages = ceil( $video_count['video_count'] / 12 );

	if ( $curpg == $total_pages ){
		$next_page = $curpg;
	}
	else {
		$next_page = $curpg + 1;
	}

	if ( $curpg > 1 ){
		$prev_page = $curpg - 1;
	}
	else {
		$prev_page = 1;
	}
	
	

	$pages = '<p class="page-numbers"><a href="/support/video-tutorials/?page_num=' . $prev_page . '" class="video-prev-page">&#60; Prev</a> Page <span id="video-cur-page">' . $curpg . '</span> of <span id="video-total-pages">' . $total_pages . '</span> <a href="/support/video-tutorials/?page_num=' . $next_page . '" class="video-next-page">Next  &#62;</a></p>'; 

	$vid_holder = '<div id="video-container">';
	if ( !$no_pages ){
		$vid_holder .= $pages;
	}

	if ( !empty($vid_folder) ) {
		foreach ($vid_folder as $vid) {
			if ( $vid['state'] == 'ACTIVE' ){

				$secs = floor( ( $vid['duration'] / 1000 ) % 60 );
				$mins = floor( ( $vid['duration'] / 1000 ) / 60 % 60 );
				if ( $secs < 10 ){
					$secs = '0' . $secs;
				}
				if ( $mins < 10 ){
					$mins = '0' . $mins;
				}

				$dur = $mins . ':' . $secs;

				$vid_holder .= '<div class="video-holder"><p class="video-time-stamp">' . $dur . '</p>';
				$vid_holder .= '<a class="video-image-link" href="#"><img id="' . $vid['id'] . '" class="sw-support-vid" src="' . $vid['images']['poster']['src'] . '" /></a>'; 
				$vid_holder .= '<div class="vid-name"><p class="video-title"><a href="/support/video-tutorials/single-video/?vid_id=' . $vid['id'] . '">' . $vid['name'] . '</a></p></div>';
				$vid_holder .= '<div class="vid-description"><p class="video-desc">' . $vid['description'] . '</p></div>';
				$vid_holder .= '</div>';

			}
			

			
		}
	}
	else {
		$vid_holder = '<p style="text-align: center;">No videos available for this specific product. Please choose another option from the list above.</p>';
	}
	
	$vid_holder .= '<div id="cur-vid-tax" style="display: none;">' . $cur_vid_tax . '</div>';
	$vid_holder .= '</div>';

	if ( !wp_is_mobile() ){
		$vid_holder .= '<div id="video_background"></div>';
		$vid_holder .= '<div id="video_frame"></div>';
	}
	else {
		$vid_holder .= '<div id="mobile-video-frame"></div>';
	}

	echo $vid_holder; 