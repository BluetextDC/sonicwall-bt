
/**
 * Bulk Resize admin javascript functions
 */
	
	/**
	 * Begin the process of re-sizing all of the checked images
	 */
	function bulk_resize_resize_images()
	{
		var images = [];
		jQuery('.bulk_resize_image_cb:checked').each(function(i) {
	       images.push(this.value);
	    });
	
		var target = jQuery('#resize_results'); 
		target.html('');
		target.show();
		jQuery(document).scrollTop(target.offset().top);
	
		// start the recursion
		bulk_resize_resize_next(images,0);
	}
	
	/** 
	 * recursive function for resizing images
	 */
	function bulk_resize_resize_next(images,next_index)
	{
		if (next_index >= images.length) return bulk_resize_resize_complete();
		
		jQuery.post(
			ajaxurl, { // (defined by wordpress - points to admin-ajax.php)
				action: 'bulk_resize_resize_image', 
				id: images[next_index]
			}, 
			function(response) {
				var result = JSON.parse(response);
	
				var target = jQuery('#resize_results'); 
				target.append('<div>'+ result['message'] +'</div>');
				target.animate({
					scrollTop: target.height()}, 
					500
				);
	
				// recurse
				bulk_resize_resize_next(images,next_index+1);
			}
		);
	}
	
	/**
	 * fired when all images have been resized
	 */
	function bulk_resize_resize_complete()
	{
		var target = jQuery('#resize_results'); 
		target.prepend('<div style="color:#21759B; margin: 10px 0; padding: 10px; background-color: #FFFFE0; border:1px solid #E6DB55;">RESIZE COMPLETE</div>');
		target.animate(
			{ scrollTop: target.height() }, 
			500
		);
	}
	
	/** 
	 * ajax post to return all images that are candidates for resizing
	 * @param string the id of the html element into which results will be appended
	 */
	function bulk_resize_load_images(container_id) {
		var container = jQuery('#'+container_id);
		container.html('<div id="bulk_resize_target" style="background: #fff; border: solid 1px #d5d5d5; padding: 10px; height: 0px; overflow: auto; margin: 0 15px;" />');
	
		var target = jQuery('#bulk_resize_target');
	
		target.html('<div><image src="'+ bulk_resize_plugin_url  +'/images/ajax-loader.gif" style="margin-bottom: .25em; vertical-align:middle;" /> Examining existing attachments.  This may take a few moments...</div>');
	
		target.animate(
			{ height: [170,'swing'] },
			500, 
			function() {		
			//	jQuery(document).scrollTop(container.offset().top);
				jQuery.post(
						ajaxurl, // (global defined by wordpress - points to admin-ajax.php)
						{ action: 'bulk_resize_get_images' }, 
						function(response) {
							var images = JSON.parse(response); 
		
							if (images.length > 0) {
								target.html('<div><input id="bulk_resize_check_all" type="checkbox" checked="checked" onclick="jQuery(\'.bulk_resize_image_cb\').attr(\'checked\', this.checked);" /> Select All</div>');
								
								for (var i = 0; i < images.length; i++)
								{
									target.append('<div><input class="bulk_resize_image_cb" name="bulk_resize_images" value="' + images[i].id + '" type="checkbox" checked="checked" /> '+ images[i].file +' ('+images[i].width+' x '+images[i].height+')</div>');
								}
		
								container.append('<p class="submit"><button class="button-primary" onclick="bulk_resize_resize_images();">Resize Checked Images...</button></p>');
								container.append('<div id="resize_results" style="display: none; background: #fff; border: solid 1px #d5d5d5; padding: 10px; overflow: auto; margin: 0 15px;" />');
							} else {
								target.html('<div>There are no existing attachments that require resizing.  Blam!</div>');
							}
						}
					);
			}
		);
	}