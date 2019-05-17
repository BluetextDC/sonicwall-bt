
jQuery( document ).ready( function($) {

	jQuery("#lock_all_posts_btn").click(function( event ){
		
		event.preventDefault();

		if (confirm("Are you sure you want to lock all pages?"))
		{
			var data = {
			'action': 'lock_all_posts'
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				if (response != "success")
				{
					alert('Error: ' + response);
				}
				else
				{
					alert("All lockable pages have been locked");
				}
			});
		}
	});

	jQuery("#unlock_all_posts_btn").click(function( event ){
		
		event.preventDefault();

		if (confirm("Are you sure you want to unlock all pages?"))
		{
			var data = {
			'action': 'unlock_all_posts'
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				if (response != "success")
				{
					alert('Error: ' + response);
				}
				else
				{
					alert("All pages have been unlocked");
				}
			});
		}
	});

	jQuery("#lock_all_english_posts_btn").click(function( event ){
		
		event.preventDefault();

		if (confirm("Are you sure you want to lock all English pages?"))
		{
			var data = {
			'action': 'lock_all_english_posts'
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				if (response != "success")
				{
					alert('Error: ' + response);
				}
				else
				{
					alert("All lockable English pages have been locked");
				}
			});
		}
	});

	jQuery("#unlock_post_type_btn").click(function( event ){
		
		event.preventDefault();

		var post_type = jQuery("#unlock_post_type_select").val();

		if (confirm("Are you sure you want to unlock all pages with the type: " + post_type + "?"))
		{
			var data = {
			'action': 'unlock_post_type',
			'post_type': post_type
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				if (response != "success")
				{
					alert('Error: ' + response);
				}
				else
				{
					alert("All " + post_type + " pages have been unlocked");
				}
			});
		}
	});

	jQuery("#lock_post_type_btn").click(function( event ){
		
		event.preventDefault();

		var post_type = jQuery("#lock_post_type_select").val();

		if (confirm("Are you sure you want to lock all pages with the type: " + post_type + "?"))
		{
			var data = {
			'action': 'lock_post_type',
			'post_type': post_type
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				if (response != "success")
				{
					alert('Error: ' + response);
				}
				else
				{
					alert("All " + post_type + " pages have been locked");
				}
			});
		}
	});


	//Override lock / unlock with ajax to really prevent pages from being updated

	jQuery(".lock_toggle").change(function( event ){
		event.preventDefault();

		var action = this.checked ? "lock" : "unlock";

		if (confirm("Are you sure you want to " + action + " this page?"))
		{
			var data = {
			'action': action + '_post_id',
			'post_id': jQuery("#post_ID").val()
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				if (response != "success")
				{
					alert('Error: ' + response);
				}
				else
				{
					alert("This page has been " + action + "ed\nThe page will now reload");
					location.reload();
				}
			});
		}
		else
		{
			$(this).prop('checked', !this.checked);
		}
	});

	if (window.page_is_locked)
	{
		$("#publish").attr("disabled", "disabled");
		$("#post-preview").attr("disabled", "disabled");
		$("#submitdiv").prepend("<div style='position: absolute; top: 0; bottom: 0; left: 0; right: 0; background-color: rgba(0,0,0,0.2); z-index: 009999;'><p style='text-align: center; color: red;'>Post Locked!</p></div>");
	}
	
});