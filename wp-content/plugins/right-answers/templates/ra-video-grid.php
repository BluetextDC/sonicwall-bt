<?php 

	$offset = 0;
	$curpg = 1;
	$video_folder_tag = '';
	$taxonomy_term = '';
	$search = '';

	$vidcms = new BC_CMS_API();

	$vidclass = new SW_VIDS();

	if ( isset( $_REQUEST['video-taxonomy'] ) && !empty( $_REQUEST['video-taxonomy'] ) ) {
		$taxonomy_term = urlencode( $_REQUEST['video-taxonomy'] );
	} else {
		$video_folder_tag = 'support';
	}
	$search = isset( $_REQUEST['search'] ) && !empty( $_REQUEST['search'] ) ? urlencode( $_REQUEST['search'] ) : '';
	$curpg = isset( $_REQUEST['page_num'] ) && !empty( $_REQUEST['page_num'] ) ? urlencode( $_REQUEST['page_num'] ) : 1;
	$curpg = isset( $_REQUEST['page_num'] ) && !empty( $_REQUEST['page_num'] ) ? urlencode( $_REQUEST['page_num'] ) : 1;
	$offset = (($curpg - 1) * 12); // $curpg > 1 ? (($curpg - 1) * 12) : 0;

	$vid_folder = $vidclass->video_folder( $video_folder_tag, $taxonomy_term, $search, 12, $offset ); // '58f7c0359e2ac9106eb62780'
	$video_count = $vidclass->video_folder_count( $video_folder_tag, $taxonomy_term, $search );
	$total_pages = ceil( $video_count['count'] / 12 );

	$next_page = $curpg == $total_pages ? $curpg : $curpg + 1;
	$prev_page = $curpg > 1 ? $curpg - 1 : 1;

	$pages = '<p class="page-numbers"><a class="video-prev-page">&#60; Prev</a> Page <span id="video-cur-page">' . $curpg . '</span> of <span id="video-total-pages">' . $total_pages . '</span> <a class="video-next-page">Next  &#62;</a></p>'; 
	// href="/support/video-tutorials/?page_num=' . $prev_page . '"    href="/support/video-tutorials/?page_num=' . $next_page . '" 

	$vid_holder = '<div id="video-container">';

	if ( !empty($vid_folder) ) {
		foreach ($vid_folder as $vid) {

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
			$vid_holder .= '<div class="vid-description"><p class="video-desc">' . $vid['description'] .'</p></div>';
			$vid_holder .= '</div>';
		}
		if ((int)$total_pages > 1) {
			$vid_holder .= $pages;
		}
	}
	else {
		$vid_holder = '<p style="text-align: center;">No videos available for this specific product. Please choose another option from the list above.</p>';
	}
	
	$vid_holder .= '<div id="cur-vid" class="cur-vid"  style="display: none;"><span id="cur-vid-tax">' . $taxonomy_term . '</span>';
	$vid_holder .= '<span id="cur-vid-search">' . $search . '</span>';
	$vid_holder .= '</div>';
	$vid_holder .= '</div>';

	if ( !wp_is_mobile() ){
		$vid_holder .= '<div id="video_background"></div>';
		$vid_holder .= '<div id="video_frame"></div>';
	}
	else {
		$vid_holder .= '<div id="mobile-video-frame"></div>';
	}

	echo $vid_holder; 