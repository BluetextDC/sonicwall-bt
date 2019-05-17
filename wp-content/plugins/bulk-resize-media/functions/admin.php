<?php

/** 
 *  ADMIN/SETTINGS UI
 */

	/**
	 * Create the settings menu item in the WordPress admin navigation and 
	 * link it to the plugin settings page
	 */
	function bulk_resize_create_menu() {
		if ( BULK_RESIZE_SUPER_ADMIN_ONLY ) {
			if ( is_super_admin() ) {
				add_media_page( 'Bulk Resize Images', 'Bulk Resize', 'add_users', 'bulk_resize_admin' , 'bulk_resize_settings_page' );
			}
		} else {
			add_media_page( 'Bulk Resize Images', 'Bulk Resize', 'add_users', 'bulk_resize_admin' , 'bulk_resize_settings_page' );
		}
	}
	
	/**
	 * Register the network settings page
	 */
	function bulk_resize_network_menu() {
      	add_submenu_page( 'settings.php', 'Bulk Resize Images' ,'Bulk Resize' , 'add_users' , 'bulk_resize_network', 'bulk_resize_network_settings');
	}
	
		
	/**
	 * Gets the option setting for the given key, first checking to see if it has been
	 * set globally for multi-site.  Otherwise checking the site options.
	 * @param string $key
	 * @param string $ifnull value to use if the requested option returns null
	 */
	function bulk_resize_get_option( $key , $ifnull ) {
		$result = null;
		
		$settings = get_site_option( 'bulk_resize_site_options' );
		
		if ( $settings['sitewide_override'] ) {
			$result = $settings[$key];
			if ($result == null) $result = $ifnull;
		} else {
			$result = get_option($key,$ifnull);
		}
		
		return $result;
	}
	
	/**
	 * Register the configuration settings that the plugin will use
	 */
	function bulk_resize_register_settings() {
		register_setting( 'bulk-resize-settings-group', 'bulk_resize_max_height' , 'intval' );
		register_setting( 'bulk-resize-settings-group', 'bulk_resize_max_width' , 'intval'  );
		register_setting( 'bulk-resize-settings-group', 'bulk_resize_max_height_library' , 'intval' );
		register_setting( 'bulk-resize-settings-group', 'bulk_resize_max_width_library' , 'intval' );
		register_setting( 'bulk-resize-settings-group', 'bulk_resize_max_height_other' , 'intval' );
		register_setting( 'bulk-resize-settings-group', 'bulk_resize_max_width_other' , 'intval' );
		register_setting( 'bulk-resize-settings-group', 'bulk_resize_quality' , 'intval' );
		register_setting( 'bulk-resize-settings-group', 'bulk_resize_bmp_to_jpg' , 'intval' );
	}
	
	/**
	 * Render the settings page by writing directly to stdout.  if multi-site is enabled
	 * and bulk_resize_override_site is true, then display a notice message that settings
	 * are not editable instead of the settings form
	 */
	function bulk_resize_settings_page() {
		?>
		<div class="wrap">		
			<div class="icon32" id="icon-upload"><br></div>
			<h2>Bulk Resize Media</h2>
				
			<?php $settings = get_site_option( 'bulk_resize_network_settings' ); ?>
			
			<?php ( $settings['bulk_resize_override_site'] || defined( 'BULK_RESIZE_SUPER_ADMIN_ONLY' ) ? bulk_resize_settings_page_notice() : bulk_resize_settings_page_form() ); ?>
			<div style="padding: 0 10px; margin: 20px 20px 0 0; float: left;">
				<h2 style="margin-top: 0px;">Bulk Resize Images</h2>
				
				<div id="bulk_resize_header">
					<p>If you have existing images, you may resize them all in bulk to recover disk space.  
					To begin, click the "Search Images" button to search all existing attachments for images that are larger than the configured limit.</p>
					<p>Limitations: For performance reasons a maximum of 250 images will be returned at one time.</p>
				</div>
				
				<p class="submit" id="bulk-resize-examine-button">
					<button class="button-primary" onclick="bulk_resize_load_images('bulk_resize_image_list');">Search Images...</button>
				</p>
				
				<div id='bulk_resize_image_list'></div>
			</div>
			
		</div>
		
		<?php 
	}
	
	/**
	 * Multi-user config file exists so display a notice
	 */
	function bulk_resize_settings_page_notice() {
		$settings = get_site_option( 'bulk_resize_site_options' );
		?>
		<div class="updated settings-error">
			<p><strong>Bulk Resize Media will help you reduce disk space and improve page load speeds.</strong></p>
			<ul>
				<li>Images larger than these dimensions will be resized to: W: <?php echo $settings['bulk_resize_max_width']; ?> by H: <?php echo $settings['bulk_resize_max_height']; ?></li>
			</ul>
		</div>
		<?php if ( is_super_admin() || !$settings['bulk_resize_sitewide_override'] ) { ?>
		
			<h3>You can alter settings for this blog alone:</h3>
		
			<?php bulk_resize_settings_page_form(); ?>
		
		
		<?php }; ?>
		
		<?php 
	}
	
	/**
	* Render the site settings form.  This is processed by
	* WordPress built-in options persistance mechanism
	*/
	function bulk_resize_settings_page_form() {
		?>
		<form method="post" action="options.php" style="width:300px; padding: 0 20px; margin: 20px 20px 0 0 ; float: left; background: #f6f6f6; border: 1px solid #e5e5e5; ">
			
			<h2 style="margin-top: 0px;">Settings</h2>
			
			<?php settings_fields( 'bulk-resize-settings-group' ); ?>
			
			<h4>Posts, Pages, Custom Post Types</h4>

			W: <input type="text" style="width:40px;" name="bulk_resize_max_width" value="<?php echo bulk_resize_get_option('bulk_resize_max_width',_DEFAULT_MAX_WIDTH); ?>" />
			/ H: <input type="text" style="width:40px;" name="bulk_resize_max_height" value="<?php echo bulk_resize_get_option('bulk_resize_max_height',_DEFAULT_MAX_HEIGHT); ?>" /> (Enter 0 to disable)
			
			<h4>Images uploaded directly to the Media Library</h4>
			
			W: <input type="text" style="width:40px;" name="bulk_resize_max_width_library" value="<?php echo bulk_resize_get_option('bulk_resize_max_width_library',_DEFAULT_MAX_WIDTH); ?>" />
			/ H: <input type="text" style="width:40px;" name="bulk_resize_max_height_library" value="<?php echo bulk_resize_get_option('bulk_resize_max_height_library',_DEFAULT_MAX_HEIGHT); ?>" /> (Enter 0 to disable)
			
	
			<h4>Images uploaded elsewhere (Theme headers, backgrounds, logos, etc)</h4>
			
			W: <input type="text" style="width:40px;" name="bulk_resize_max_width_other" value="<?php echo bulk_resize_get_option('bulk_resize_max_width_other',_DEFAULT_MAX_WIDTH); ?>" />
			/ H: <input type="text" style="width:40px;" name="bulk_resize_max_height_other" value="<?php echo bulk_resize_get_option('bulk_resize_max_height_other',_DEFAULT_MAX_HEIGHT); ?>" /> (Enter 0 to disable)
	
	
			<h4>JPG image quality</h4>
			<select name="bulk_resize_quality">
				<?php 
				$q = bulk_resize_get_option( 'bulk_resize_quality' , _DEFAULT_QUALITY );
				
				for ($x = 50; $x <= 100; $x = $x + 10) {
					echo "<option". ($q == $x ? " selected='selected'" : "") .">$x</option>";
				}
				?>
			</select> (WordPress default is 90)
			
			<h4>Convert BMP To JPG</h4>
			<select name="bulk_resize_bmp_to_jpg">
				<option <?php if ( bulk_resize_get_option( 'bulk_resize_bmp_to_jpg' , _DEFAULT_BMP_TO_JPG ) == "1" ) { echo "selected='selected'"; } ?> value="1">Yes</option>
				<option <?php if ( bulk_resize_get_option( 'bulk_resize_bmp_to_jpg' , _DEFAULT_BMP_TO_JPG ) == "0" ) { echo "selected='selected'"; } ?> value="0">No</option>
			</select>
		
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
		
		</form>
		<?php 
	}
	
	/**
	 * display the form for the multi-site settings page
	 */
	function bulk_resize_network_settings() {
		?>
		<div class="wrap">
		<div id="icon-options-general" class="icon32"><br></div>
		<h2>Bulk Resize Media Network Settings</h2>

		<?php bulk_resize_network_settings_update(); ?>
			
		<?php $settings = get_site_option( 'bulk_resize_site_options' ); ?>
		
		<form method="post" action="settings.php?page=bulk_resize_network">
		
		<?php wp_nonce_field( 'bulk_resize_site_options' , 'bulk_resize_site_options_nonce' ); ?>
		
		<input type="hidden" name="update_settings" value="1" />
	
		<h4>Global Settings Override</h4>
		<select name="bulk_resize_site[sitewide_override]">
			<option value="0" <?php if ( $settings['bulk_resize_sitewide_override'] == '0' ) echo "selected='selected'" ?> >Allow each site to configure settings</option>
			<option value="1" <?php if ( $settings['bulk_resize_sitewide_override'] == '1' ) echo "selected='selected'" ?> >Force global settings for all sites</option>
		</select>
		
		<h4>Max width/height for images uploaded within a Page/Post</h4>
		W: <input name="bulk_resize_site[max_width]" value="<?php echo ( isset( $settings['bulk_resize_max_width'] ) ? $settings['bulk_resize_max_width'] : _DEFAULT_MAX_WIDTH ) ?>" style="width: 45px;" /> 
		/ H: <input name="bulk_resize_site[max_height]" value="<?php echo ( isset( $settings['bulk_resize_max_height'] ) ? $settings['bulk_resize_max_height'] : _DEFAULT_MAX_HEIGHT ) ?>" style="width: 45px;" /> (Enter 0 to disable)
		
		<h4>Max width/height for images uploaded directly to the Media Library</h4>
		W: <input name="bulk_resize_site[max_width_library]" value="<?php echo ( isset( $settings['bulk_resize_max_width_library'] ) ? $settings['bulk_resize_max_width_library'] : _DEFAULT_MAX_WIDTH ) ?>" style="width: 45px;" /> 
		/ H: <input name="bulk_resize_site[max_height_library]" value="<?php echo ( isset( $settings['bulk_resize_max_height_library'] ) ? $settings['bulk_resize_max_height_library'] : _DEFAULT_MAX_HEIGHT ) ?>" style="width: 45px;" /> (Enter 0 to disable)

		<h4>Max width/height for images uploaded elsewhere (Theme headers, backgrounds, logos, etc)</h4>
		W: <input name="bulk_resize_site[max_width_other]" value="<?php echo ( isset( $settings['bulk_resize_max_width_other'] ) ? $settings['bulk_resize_max_width_other'] : _DEFAULT_MAX_WIDTH ) ?>" style="width: 45px;" /> 
		/ H: <input name="bulk_resize_site[max_height_other]" value="<?php echo ( isset( $settings['bulk_resize_max_height_other'] ) ? $settings['bulk_resize_max_height_other'] : _DEFAULT_MAX_HEIGHT ) ?>" style="width: 45px;" /> (Enter 0 to disable)
		
		<h4>Convert BMP to JPG </h4>
		<select name="bulk_resize_site[bmp_to_jpg]">
			<option value="1" <?php if ($settings['bulk_resize_bmp_to_jpg'] == '1') echo "selected='selected'" ?> >Yes</option>
			<option value="0" <?php if ($settings['bulk_resize_bmp_to_jpg'] == '0') echo "selected='selected'" ?> >No</option>
		</select>
		
		<h4>JPG Quality</h4>
		<select name="bulk_resize_site[quality]">
			<?php 
			$q = $settings['bulk_resize_quality'];
			
			for ($x = 50; $x <= 100; $x = $x + 10)
			{
				echo "<option". ($q == $x ? " selected='selected'" : "") .">$x</option>";
			}
			?>
		</select> (WordPress default is 90)

		<p class="submit"><input type="submit" class="button-primary" value="Update Settings" /></p>
	
		</form>
		</div>
		
		<?php
	}
	
	function bulk_resize_network_settings_update() {
		if ( isset($_REQUEST['update_settings'] ) && wp_verify_nonce( $_POST['bulk_resize_site_options_nonce'] , 'bulk_resize_site_options' ) ) {
			// validate and save settings
			$settings = get_site_option( 'bulk_resize_site_options' );
			
			if ( isset( $_POST['bulk_resize_site'] ) && is_array( $_POST['bulk_resize_site'] ) ) {
			
				foreach ( $_POST['bulk_resize_site'] as $key => $posted ) {
					$posted = intval( $posted );
					$settings['bulk_resize_'.$key] = $posted;
				}
			
			}
			update_site_option( 'bulk_resize_site_options' , $settings );
			echo "<div class='updated settings-error'><p><strong>Bulk Resize Media network settings saved.</strong></p></div>";
		}
	}	
?>