<?php
/*
Plugin Name: Bulk Resize Media
Plugin URI: http://martythornley.com
Description: Resize images to help speed up your site and make export/import easier.
Author: Marty Thornley
Version: 1.1
Author URI: http://martythornley.com
License: GPLv2 or later
*/

/*
Based on "Imsanity" by Jason Hinkle
http://verysimple.com/products/imsanity/
http://verysimple.com/

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

	add_action( 'init' , 'bulk_resize_init_actions' );
	
	function bulk_resize_init_actions() {
	
		/**
		 * Define Constants
		 */
		define( 'BULK_RESIZE_URL' , trailingslashit( plugins_url() ) ."bulk-resize-media" );

		define( '_DEFAULT_MAX_WIDTH' , 	1024 );
		define( '_DEFAULT_MAX_HEIGHT' , 1024 );
		define( '_DEFAULT_BMP_TO_JPG' , 0 );
		define( '_DEFAULT_QUALITY' , 	80 );
		
		define( '_SOURCE_POST' , 		1 );
		define( '_SOURCE_LIBRARY' , 	2 );
		define( '_SOURCE_OTHER' , 		4 );
		
		// configure these for multisite
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			define( 'BULK_RESIZE_SUPER_ADMIN_ONLY' , false );		
		}
	
		/**
		 * import supporting libraries
		 */
		include_once( plugin_dir_path( __FILE__ ) . 'functions/admin.php');
		include_once( plugin_dir_path( __FILE__ ) . 'functions/ajax.php');
		
		add_action( 'admin_init', 			'bulk_resize_register_settings' );
		add_action( 'admin_menu', 			'bulk_resize_create_menu');
		add_action( 'admin_notices',		'bulk_resize_pre_export' , 90 );

		//add_action( 'export_fix_messages_before', 	'bulk_resize_export_fix_messages_after' , 1 );
		add_action( 'network_admin_menu', 			'bulk_resize_network_menu' );
		
		// in ajax.php
		add_action( 'admin_head', 							'bulk_resize_admin_javascript' );
		add_action( 'wp_ajax_bulk_resize_get_images', 		'bulk_resize_get_images' );
		add_action( 'wp_ajax_bulk_resize_resize_image', 	'bulk_resize_resize_image' );
	
		/* add filters to hook into uploads */
		add_filter( 'wp_handle_upload', 					'bulk_resize_handle_upload' , 0);

		$message = '<h4>Please Resize Your Images</h4>';
		$message .= '<p>You may want to resize large images before export/import. It will help save bandwidth during the import and prevent the import from crashing.';
		$message .= '<p><a class="button-secondary" href="'.admin_url('upload.php?page=bulk_resize_admin').'">Resize Your Images</a></p>';
	
		define( 'BULK_RESIZE_MESSAGE' , $message ); 
	
	}		

	function bulk_resize_pre_export(){
		global $pagenow;
		
		if ( $pagenow == 'export.php' ) {
			echo '<div class="updated fade">';
			echo BULK_RESIZE_MESSAGE;
			echo '</div>';
		}
	}
	
	function bulk_resize_export_fix_messages_after() {
		
		echo '<div style="background: #FFFFE0; border:1px solid #E6DB55; padding: 0 10px; margin: 10px 0;">';
		echo BULK_RESIZE_MESSAGE;
		echo '</div>';
	}
	
	/**
	 * Inspects the request and determines where the upload came from
	 * @return _SOURCE_POST | _SOURCE_LIBRARY | _SOURCE_OTHER
	 */
	function bulk_resize_get_source() {
		return array_key_exists('post_id', $_REQUEST) 
			?  ($_REQUEST['post_id'] == 0 ? _SOURCE_LIBRARY : _SOURCE_POST)
			: _SOURCE_OTHER;
	}
	
	/**
	 * Given the source, returns the max width/height
	 * 
	 * @example:  list($w,$h) = bulk_resize_get_max_width_height(_SOURCE_LIBRARY);
	 * @param int _SOURCE_POST | _SOURCE_LIBRARY | _SOURCE_OTHER
	 */
	function bulk_resize_get_max_width_height($source) {
		$w = bulk_resize_get_option('bulk_resize_max_width',_DEFAULT_MAX_WIDTH);
		$h = bulk_resize_get_option('bulk_resize_max_height',_DEFAULT_MAX_HEIGHT);
							
		switch ($source) {
			case _SOURCE_POST:
				break;
			case _SOURCE_LIBRARY:
				$w = bulk_resize_get_option('bulk_resize_max_width_library',$w);
				$h = bulk_resize_get_option('bulk_resize_max_height_library',$h);
				break;
			default:
				$w = bulk_resize_get_option('bulk_resize_max_width_other',$w);
				$h = bulk_resize_get_option('bulk_resize_max_height_other',$h);
				
				break;
		}
		
		return array($w,$h);
	}
	
	/**
	 * Handler after a file has been uploaded.  If the file is an image, check the size
	 * to see if it is too big and, if so, resize and overwrite the original
	 * @param Array $params
	 */
	function bulk_resize_handle_upload($params) {
		
		$option_convert_bmp = bulk_resize_get_option( 'bulk_resize_bmp_to_jpg' , _DEFAULT_BMP_TO_JPG );
		
		update_option( 'bulk_resize_check' , $params );
		
		if ( $params['type'] == 'image/bmp' && $option_convert_bmp ) {
			$params = bulk_resize_bmp_to_jpg( $params );
		}
		
		if ( ( !is_wp_error( $params ) ) && in_array($params['type'], array( 'image/png','image/gif','image/jpeg' ) ) ) {
			$oldPath = $params['file'];
			
			$source = bulk_resize_get_source();
			
			list($maxW,$maxH) = bulk_resize_get_max_width_height($source);
			
			list($oldW, $oldH) = getimagesize( $oldPath );
			
			if ( ( $oldW > $maxW && $maxW > 0) || ( $oldH > $maxH && $maxH > 0 ) ) {
				$quality = bulk_resize_get_option( 'bulk_resize_quality' , _DEFAULT_QUALITY );
				
				list($newW, $newH) = wp_constrain_dimensions($oldW, $oldH, $maxW, $maxH);
				
				$newPath = image_resize( $oldPath, $newW, $newH, false, null, null, $quality);
								
				if ( !is_wp_error( $newPath ) && !is_dir( $newPath ) ) {
					
					rename( $newPath, $oldPath );
					$params['file'] = $oldPath;
					
				} else {
					$params = wp_handle_upload_error( $oldPath , "Oh Snap! Bulk Resize Media was unable to resize this image "
						. "for the following reason: '" . $resizeResult->get_error_message() . "'
						.  If you continue to see this error message, you may need to either install missing server"
						. " components or disable the Bulk Resize Media plugin."
						. "  If you think you have discovered a bug, please report it on the Bulk Resize Media support forum." );
					
				}
			}
			
		}
		update_option( 'bulk_check_end' , $source );
		return $params;
	}
	
	
	/**
	 * If the uploaded image is a bmp this function handles the details of converting
	 * the bmp to a jpg, saves the new file and adjusts the params array as necessary
	 * 
	 * @param array $params
	 */
	function bulk_resize_bmp_to_jpg($params) {

		include_once( 'functions/imagecreatefrombmp.php' );
		
		$bmp = imagecreatefrombmp($params['file']);
		
		// we need to change the extension from .bmp to .jpg so we have to ensure it will be a unique filename
		$uploads = wp_upload_dir();
		$oldFileName = basename($params['file']);
		$newFileName = basename(str_ireplace(".bmp", ".jpg", $oldFileName));
		$newFileName = wp_unique_filename( $uploads['path'], $newFileName );
		
		$quality = bulk_resize_get_option('bulk_resize_quality',_DEFAULT_QUALITY);
		
		if ( imagejpeg( $bmp,$uploads['path'] . '/' . $newFileName, $quality) ) {
			// conversion succeeded.  remove the original bmp & remap the params
			unlink($params['file']);
			
			$params['file'] = $uploads['path'] . '/' . $newFileName;
			$params['url'] = $uploads['url'] . '/' . $newFileName;
			$params['type'] = 'image/jpeg';
		} else {
			unlink( $params['file'] );
			
			return wp_handle_upload_error( $oldPath, 
				"Oh Snap! Bulk Resize Media was Unable to process the BMP file.  "
				."If you continue to see this error you may need to disable the BMP-To-JPG "
				."feature in Bulk Resize Media settings.");
		}
		
		return $params;
	}
?>