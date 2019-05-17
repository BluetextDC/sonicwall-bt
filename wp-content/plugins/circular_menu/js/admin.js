jQuery(document).ready(function(){
	//Get initial status



	var enabled = jQuery("#circular_menu_enabled");

	if (enabled)
	{
		enabled.change(function(){
			prepareForm($(this));
		});

		prepareForm(enabled);
		function prepareForm(enabled)
		{
			if (enabled.val() != "disabled")
			{
				var linkGroups = $(".circularMenuForm").find('.link-group');

				$(linkGroups).each(function(){

					var isDefault = $(this).find(".toggle-default").is(':checked');
					
					if (!isDefault)
					{
						$(this).find(':input').prop('disabled', false);
					}

				});
			}
			else
			{
				$('.circularMenuForm').find(':input').prop('disabled', true);
			}

			//Enable the toggle-default buttons
			$('.circularMenuForm').find('.toggle-default').prop('disabled', false);

			enabled.prop('disabled', false);
		}
	}

	var toggle_default = jQuery(".toggle-default");

	if (toggle_default)
	{
		toggle_default.change(function(){

			var linkGroup = $(this).parents(".link-group")[0];

			if ($(this).is(':checked'))
			{
				$(linkGroup).find(':input').each(function(){
					var defaultValue = $(this).data('default');
					
					if (defaultValue)
					{
						if ($(this).is(':checkbox'))
						{
							var checked = defaultValue === "on";
							$(this).prop('checked', checked);
						}
						else
						{
							$(this).val(defaultValue);
						}
					}					
				});

				$(linkGroup).find(':input').prop('disabled', true);
				$(linkGroup).find('.toggle-default').prop('disabled', false);
			}
			else
			{
				$(linkGroup).find(':input').prop('disabled', false);
			}
		});
	}



	var datasheetSelector = jQuery(".datasheet-selector");
	
	//Loop through and set the select if it is enabled
	for (var i = 0; i < datasheetSelector.length; i++)
	{
		var ds = datasheetSelector[i];
		var linkGroup = jQuery(ds).parents('tr')[0];
		var link = jQuery(linkGroup).find(".circular_menu_link").val();
		jQuery(ds).val(link);
	}

	datasheetSelector.change(function(){
		var link = jQuery(this).val();

		var linkGroup = jQuery(this).parents('tr')[0];
		jQuery(linkGroup).find(".circular_menu_title").val("Datasheet");
		jQuery(linkGroup).find(".circular_menu_link").val(link);
		jQuery(linkGroup).find(".circular_menu_icon").val("icon-file-pdf");
		jQuery(linkGroup).find(".circular_menu_enabled").prop('checked', true);
		jQuery(linkGroup).find(".circular_menu_lightbox").prop('checked', true);
		jQuery(linkGroup).find(".circular_menu_new_window").prop('checked', false);		
	});


	
	
});