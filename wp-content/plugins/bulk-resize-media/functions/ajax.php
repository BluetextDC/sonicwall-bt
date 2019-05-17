<?php

/**
*  AJAX FUNCTIONS
*/
	/**
	 * Output the javascript needed for making ajax calls into the header
	 */
	function bulk_resize_admin_javascript() {
		echo "<script type=\"text/javascript\">var bulk_resize_plugin_url = '".BULK_RESIZE_URL."';</script>\n";
		echo "<script type=\"text/javascript\" src=\"".BULK_RESIZE_URL."/scripts/bulk-resize.js\" ></script>\n";
	}	
		
	/**
	 * Verifies that the current user has administrator permission and, if not, 
	 * renders a json warning and dies
	 */
	function bulk_resize_verify_permission() {
		if (!current_user_can('administrator')) {
			$results = array('success'=>false,'message' => 'Administrator permission is required');
			echo json_encode($results);
			die();
		}
	}
	
	/**
	 * Searches for up to 250 images that are candidates for resize and renders them
	 * to the browser as a json array, then dies
	 */
	function bulk_resize_get_images() {
		bulk_resize_verify_permission();
		
		global $wpdb;
		
		// maybe limit this query, rather than wait for end of size checking?
		$query = $wpdb->prepare( 
			"select
				$wpdb->posts.ID as ID,
				$wpdb->posts.guid as guid,
				$wpdb->postmeta.meta_value as file_meta
				from $wpdb->posts
				inner join $wpdb->postmeta on $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = %s
				where $wpdb->posts.post_type = %s
				and $wpdb->posts.post_mime_type like %s
				and $wpdb->posts.post_mime_type != 'image/bmp'", 
			array('_wp_attachment_metadata', 'attachment', 'image%','image/bmp')
		);
		
		$images = $wpdb->get_results($query);
		$results = array();
		
		if ($images) {
			$maxW = bulk_resize_get_option('bulk_resize_max_width',_DEFAULT_MAX_WIDTH);
			$maxH = bulk_resize_get_option('bulk_resize_max_height',_DEFAULT_MAX_HEIGHT);
			$count = 0;
			
			foreach ($images as $image) {
				$meta = unserialize($image->file_meta);
				
				$file = $meta['file'];
				$upload_dir = wp_upload_dir();
				
				$path = trailingslashit( $upload_dir['basedir'] ) . $file;
				
				if ( $meta['file'] && file_exists( $path ) ) {
					
					list($width, $height) = @getimagesize( $path );
				
					if ( $width > $maxW || $height > $maxH ) {
						$count++;	
						$results[] = array(
							'id'=>$image->ID,
							'width'=>$width,
							'height'=>$height,
							'file'=>$path
						);
					}
				}
						
				// make sure we only return a limited number of records so we don't overload the ajax features
				if ($count >= 250) 
					break;
			}
		}
		
		echo json_encode($results);
		die(); // required by wordpress
	}
	
	/**
	* Resizes the image with the given id according to the configured max width and height settings
	* renders a json response indicating success/failure and dies
	*/
	function bulk_resize_resize_image() {
		bulk_resize_verify_permission();
		
		global $wpdb;
		
		$id = intval( $_POST['id'] );
		
		if (!$id) {
			$results = array('success'=>false,'message' => 'Missing ID Parameter');
			echo json_encode($results);
			die();
		}
	
		// @TODO: probably doesn't need the join...?
		$query = $wpdb->prepare(
		"select
					$wpdb->posts.ID as ID,
					$wpdb->posts.guid as guid,
					$wpdb->postmeta.meta_value as file_meta
					from $wpdb->posts
					inner join $wpdb->postmeta on $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = %s
					where  $wpdb->posts.ID = %d
					and $wpdb->posts.post_type = %s
					and $wpdb->posts.post_mime_type like %s", 
		array('_wp_attachment_metadata', $id, 'attachment', 'image%')
		);
		
		$images = $wpdb->get_results($query);
		
		if ($images) {
			$image = $images[0];
			$meta = unserialize($image->file_meta);
			$uploads = wp_upload_dir();
			$oldPath = $uploads['basedir'] . "/" . $meta['file'];
			
			$maxW = bulk_resize_get_option('bulk_resize_max_width',_DEFAULT_MAX_WIDTH);
			$maxH = bulk_resize_get_option('bulk_resize_max_height',_DEFAULT_MAX_HEIGHT);
			
			if ( file_exists( $oldPath ) ) 
				list($oldW, $oldH) = @getimagesize( $oldPath );

			if ( ( $oldW > $maxW && $maxW > 0) || ($oldH > $maxH && $maxH > 0 ) ) {
				$quality = bulk_resize_get_option('bulk_resize_quality',_DEFAULT_QUALITY);
					
				list($newW, $newH) = wp_constrain_dimensions($oldW, $oldH, $maxW, $maxH);
					
				$resizeResult = image_resize( $oldPath, $newW, $newH, false, null, null, $quality);
				// $resizeResult = new WP_Error('invalid_image', __('Could not read image size'), $oldPath);  // uncommend to debug fail condition
				
				if (!is_wp_error($resizeResult)) {
					$newPath = $resizeResult;
					
					if ( $newPath != $oldPath && file_exists( $oldPath ) && !is_dir( $oldPath ) ) {
						// remove original and replace with re-sized image
						unlink($oldPath);
						rename($newPath, $oldPath);
					
						$meta['width'] = $newW;
						$meta['height'] = $newH;
					
						update_post_meta( $image->ID , '_wp_attachment_metadata' , $meta );
						$results = array('success'=>true,'id'=> $id, 'message' => '<strong>OK:</strong> ' . $oldPath);				
					}
				} else {
					$results = array('success'=>false,'id'=> $id, 'message' => '<strong>ERROR:</strong> ' . $oldPath . ' (' . htmlentities($resizeResult->get_error_message()) . ')');
				}
			} else {
				$results = array('success'=>true,'id'=> $id, 'message' => '<strong>SKIPPED:</strong> <span style="color:#777;">' . $oldPath . '</span> <em style="color:#21759B">(Resize not required)</em>');
			}
	
		} else {
			$results = array('success'=>false,'id'=> $id, 'message' => '<strong>ERROR:</strong> (Attachment with ID of ' . htmlentities($id) . ' not found)');
		}
		
	
		echo json_encode($results);
		die(); // required by wordpress
	}
?>