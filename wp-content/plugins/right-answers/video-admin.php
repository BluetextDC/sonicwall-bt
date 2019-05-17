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

			public function video_folder( $video_folder_id, $offset = 0 ) {

				return $this->send_request( esc_url_raw( self::CMS_BASE_URL . $this->get_account_id() . '/folders/' . $video_folder_id . '/videos?limit=12&offset=' . $offset ) );
				// return $this->send_request( esc_url_raw( 'https://cms.api.brightcove.com/v1/accounts/5380177764001/folders/' . $video_folder_id . '/videos?limit=12' ) );
			}

			public function video_folder_count( $video_folder_id ) {

				return $this->send_request( esc_url_raw( self::CMS_BASE_URL . $this->get_account_id() . '/folders/' . $video_folder_id ) );
				// return $this->send_request( esc_url_raw( 'https://cms.api.brightcove.com/v1/accounts/5380177764001/folders/' . $video_folder_id ) );
			}

			public function video_taxonomy_search( $taxonomy_term, $limit = 100 ) {

				return $this->send_request( esc_url_raw( self::CMS_BASE_URL . $this->get_account_id() . '/videos?limit=' . $limit . '&q=taxonomyvalue:"' . $taxonomy_term . '"' ) );
				// return $this->send_request( esc_url_raw( 'https://cms.api.brightcove.com/v1/accounts/5380177764001/folders/' . $video_folder_id ) );
			}

			public function related_videos( $orig_vid_id ) {

				return $this->send_request( esc_url_raw( self::CMS_PLAYBACK_URL . $this->get_account_id() . '/videos/' . $orig_vid_id . '/related') );
			}
		}

	}