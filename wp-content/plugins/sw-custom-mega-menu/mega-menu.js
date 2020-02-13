jQuery(document).ready(function(){

	//Products menu

	//Initialize the first one on load
	setTimeout(function(){
		setLeftActive(jQuery(".menu-container .menu-left .menu-item:first"));
	}, 500);
	

	jQuery(".menu-left .menu-item").click(function(event){
        
        if (window && window.lang && window.lang == "en_US")
        {
            jQuery(this).find("a").get(0).click();
        }
        else
        {
            event.preventDefault();
		    setLeftActive(this);
        }
		
	});

	jQuery(".menu-left .menu-item").hover(function(event){
		
        if (window && window.lang && window.lang == "en_US")
        {
            event.preventDefault();

            if (jQuery(this).attr('id') == "menu-item-16546")
            {
                var firewalls = jQuery("#menu-item-11595");
               setLeftActive(firewalls, true); 
            }
            else
            {
                setLeftActive(this);
            }
        }
	});

    jQuery(".menu-right .menu-item").hover(function(){
		setRightActive(this);
	});

	jQuery(".menu-right .menu-item").click(function(){
		window.location = jQuery(this).find('a').attr('href');
    	event.preventDefault();
	});

	jQuery('.menu-right').on('show', function() {
      //Select the first element
      setRightActive(jQuery(this).find(".menu-item:first"));
	});

	jQuery('.menu-item-pop').click(function(){
		var href = jQuery(this).find('a').attr('href');
		if (href)
		{
			location.href = href;
		}
	});

	function setLeftActive(elem, skip_selected)
	{
		jQuery(".menu-left .menu-item").removeClass("selected");
        
        if (!skip_selected)
        {
            jQuery(elem).addClass('selected');
        }
		

		var menu_id = jQuery(elem).attr("id");

		jQuery(".menu-right").hide();
		jQuery(".menu-right." + menu_id).show();

		var total_items = jQuery(".menu-right." + menu_id).children(".menu-item").length;

		if (total_items <= 4 && jQuery(elem).index() < 5)
		{
			jQuery(".menu-right." + menu_id).addClass("small");
		}
		
	}
	function setRightActive(elem)
	{
		//Remove all the selected classes
		jQuery(".menu-right .menu-item").removeClass("selected");

		//Select the new menu-item
		jQuery(elem).addClass('selected');

		//Set the middle display box
		var details_container = jQuery(elem).parent().find('.details-container')[0];
		var item_container = jQuery(elem).find('.item-container')[0];

		if (details_container && item_container)
		{
			var image = jQuery(item_container).data("product-image");
			var description = jQuery(item_container).data("product-description");

			jQuery(details_container).find("img").attr("src", image);
			jQuery(details_container).find("p").html(description);
		}
	}

	//end products menu


	//Solutions menu 
	jQuery(".menu-solutions .solutions-header .menu-item").click(function(){
		event.preventDefault();
		if (jQuery(this).find('a').attr('target') == "_blank")
		{
			window.open(jQuery(this).find('a').attr('href'),'_blank');		
		}
		else
		{
			window.location = jQuery(this).find('a').attr('href');	
		}
	});

	jQuery(".menu-solutions .solutions-header .menu-item").hover(function(){
		jQuery(".menu-solutions .solutions-header .menu-item").removeClass("selected");
		jQuery(this).addClass('selected');
	});

	//Partners menu 
	jQuery(".menu-partners .partners-header").click(function(){
		event.preventDefault();
		if (jQuery(this).find('a').attr('target') == "_blank")
		{
			window.open(jQuery(this).find('a').attr('href'),'_blank');		
		}
		else
		{
			window.location = jQuery(this).find('a').attr('href');	
		}
	});

	jQuery(".menu-partners .partners-header").hover(function(){
		jQuery(".menu-partners .partners-header").removeClass("selected");
		jQuery(this).addClass('selected');
	});

	//Show & hide the circular menu when opened
	(function(){
    // Your base, I'm in it!
    var originalAddClassMethod = jQuery.fn.addClass;

    jQuery.fn.addClass = function(){
        // Execute the original method.
        var result = originalAddClassMethod.apply( this, arguments );

        // trigger a custom event
        jQuery(this).trigger('cssClassAdded');

        // return the original result
        return result;
    }

    var originalRemoveClassMethod = jQuery.fn.removeClass;

    jQuery.fn.removeClass = function(){
        // Execute the original method.
        var result = originalRemoveClassMethod.apply( this, arguments );

        // trigger a custom event
        jQuery(this).trigger('cssClassRemoved');

        // return the original result
        return result;
    }
	})();


	jQuery(".menu-item a").bind('cssClassAdded', function(){ 
	    //Hide the circular menu
	    jQuery("#circular-menu").hide();
	});

	jQuery(".menu-item a").bind('cssClassRemoved', function(){ 
	    //Show the circular menu
	    jQuery("#circular-menu").show();
	});
});

//Jquery show / hide events

(function ($) {
  $.each(['show', 'hide'], function (i, ev) {
    var el = $.fn[ev];
    $.fn[ev] = function () {
      this.trigger(ev);
      return el.apply(this, arguments);
    };
  });
})(jQuery);