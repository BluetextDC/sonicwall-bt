<?php 

	if ( isset( $_REQUEST['vid_id'] ) ){
		$video_id = $_REQUEST['vid_id'];
	}

	$vidcms = new BC_CMS_API();
	$vid = $vidcms->video_get( $video_id );
    if($vid["state"] != "ACTIVE") {
        include( get_404_template() );
    }
	// print_r($vid['custom_fields']);

	$upstring = strstr($vid['updated_at'], 'T', true);

	$vidvids = new SW_VIDS();

	// $vid_tax_list = explode( ',', $vid['custom_fields']['taxonomyvalue'] );
	// 	$rel_vids = array();
	$rel_vids = $vidvids->video_folder( '', $vid['custom_fields']['taxonomyvalue'], '', 6, 0,  $video_id); // count($vid_tax_list)
	// foreach ($vid_tax_list as $li) {
	// 	array_push($rel_vids, $vidvids->video_folder( '', urlencode( $li ), 1 )); // change this 
	// 	// $vidvids->video_taxonomy_search( urlencode( $li ), 1 )
	// }

	$vid_holder = '<div id="single-video-container">';
	$vid_holder .= '<div id="video-info-container"><h2>' . $vid['name'] . '</h2><p>Updated: ' . date( 'F, j, Y', strtotime($upstring) ) . '</p></div>'; 
	$vid_holder .= '<div id="single-video-player">';
	$vid_holder .= ajax_video_popup( $video_id );
	$vid_holder .= '</div>';
	$vid_holder .= '</div>';

	$vid_holder .= '<h4 style="margin-top: 40px;">Related Videos</h4>';
	

	foreach ($rel_vids as $rvid) {
		// print_r($vid);
		// echo '<hr />';
	
		// foreach ($rel_vids $rv) {

			$secs = floor( ( $rvid['duration'] / 1000 ) % 60 );
			$mins = floor( ( $rvid['duration'] / 1000 ) / 60 % 60 );
			if ( $secs < 10 ){
				$secs = '0' . $secs;
			}
			if ( $mins < 10 ){
				$mins = '0' . $mins;
			}

			$dur = $mins . ':' . $secs;

			$vid_holder .= '<div class="video-holder"><p class="video-time-stamp">' . $dur . '</p>';
			$vid_holder .= '<a href="/support/video-tutorials/single-video/?vid_id=' . $rvid['id'] . '"><img id="' . $rvid['id'] . '" class="sw-support-vid" src="' . $rvid['images']['poster']['src'] . '" /><br />' . $rvid['name'] . '</a>'; 
			$vid_holder .= '</div>';
		// }
	}

	$vid_holder .= '</div>';

	
	echo $vid_holder;