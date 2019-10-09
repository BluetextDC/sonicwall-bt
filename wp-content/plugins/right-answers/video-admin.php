<?php 

	if ( !is_admin() ){
		require_once( 'wp-content/plugins/brightcove-video-connect/includes/api/class-bc-api.php' );
		require_once( 'wp-content/plugins/brightcove-video-connect/includes/api/class-bc-cms-api.php' );		

		class SW_VIDS extends BC_API {

			const CMS_BASE_URL = 'https://cms.api.brightcove.com/v1/accounts/';
			const CMS_PLAYBACK_URL = 'https://edge-elb.api.brightcove.com/playback/v1/accounts/';

			public function __construct() {

				parent::__construct();

			}

			// made changes of taking folder id to access cms-api(brightcove) videos in folder based on tags of the videos(All videos instead of specific video folder)
			public function video_folder( $video_folder_tag = '', $taxonomy_term = '', $search = '', $limit = 12, $offset = 0, $video_id = '' ) {
				$params = 'q=%2Bstate:ACTIVE';
				$params .= ($video_folder_tag != '' ? ('%20%2Btags:' . urlencode($video_folder_tag) ) : '' );
				$params .= ($taxonomy_term != '' ? ('%20taxonomyvalue:' . urlencode($taxonomy_term) )  : '' );
				$params .= ($search != '' ? ('%20%2Bname:' . urlencode($search)) : '' );
				$params .= ($video_id != '' ? ('%20-id:' . urlencode($video_id)) : '' );
				$params .= ('&limit=' . $limit . '&offset=' . $offset);

				return $this->send_request( esc_url_raw( self::CMS_BASE_URL . $this->get_account_id() . '/videos?' .$params ));
				// return $this->send_request( esc_url_raw( self::CMS_BASE_URL . $this->get_account_id() . '/folders/' . $video_folder_tag . '/videos?limit=12&offset=' . $offset ) );
				// return $this->send_request( esc_url_raw( 'https://cms.api.brightcove.com/v1/accounts/5380177764001/folders/' . $video_folder_tag . '/videos?limit=12' ) );
			}

			public function video_folder_count( $video_folder_tag = '', $taxonomy_term = '', $search = '' ) {
				$params = 'q=state:ACTIVE';
				$params .= ($video_folder_tag != '' ? ('%20%2Btags:' . urlencode($video_folder_tag) ) : '' );
				$params .= ($taxonomy_term != '' ? ('%20taxonomyvalue:' . urlencode($taxonomy_term) )  : '' );
				$params .= ($search != '' ? ('%20%2Bname:' . urlencode($search)) : '' );

				return $this->send_request( esc_url_raw( self::CMS_BASE_URL . $this->get_account_id() . '/counts/videos?' . $params ));
				// return $this->send_request( esc_url_raw( self::CMS_BASE_URL . $this->get_account_id() . '/folders/' . $video_folder_tag ) );
				// return $this->send_request( esc_url_raw( 'https://cms.api.brightcove.com/v1/accounts/5380177764001/folders/' . $video_folder_tag ) );
			}

			// public function video_taxonomy_search( $taxonomy_term, $limit = 100, $offset = 0, $search = '' ) { // limit change
			// 	$params = ('&q=%2Bstate:ACTIVE%20taxonomyvalue:' . $taxonomy_term) . ($search != '' ? ('%20%2Bname:' . $search) : '');
			// 	$params .= '&limit=' . $limit;
			// 	return $this->send_request( esc_url_raw( self::CMS_BASE_URL . $this->get_account_id() . '/videos?' ) );
			// 	// return $this->send_request( esc_url_raw( 'https://cms.api.brightcove.com/v1/accounts/5380177764001/folders/' . $video_folder_tag ) );
			// }

			public function related_videos( $orig_vid_id ) {

				return $this->send_request( esc_url_raw( self::CMS_PLAYBACK_URL . $this->get_account_id() . '/videos/' . $orig_vid_id . '/related') );
			}
		}

	}