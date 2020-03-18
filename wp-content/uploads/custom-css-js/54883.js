<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
jQuery(document).ready(function(){
  
  window.clone_timeout = false;
  
  jQuery('#tablepress-99').on( 'draw.dt', function () {
  	  
    if (window.clone_timeout)
    {
    	 clearTimeout(window.clone_timeout);    
    }
    window.clone_timeout = window.setTimeout(function(){
      var clone = jQuery("#tablepress-99").clone();
      clone.attr("id","tablepress-99-clone");
    
      //Append or replace
      if (jQuery("#tablepress-99_wrapper #tablepress-99-clone").length)
      {
      	//Replace
        jQuery("#tablepress-99-clone").replaceWith(clone);
      }
      else
      {
      	jQuery("#tablepress-99_wrapper").append(clone);    
      }
    
      jQuery("#tablepress-99").hide();
      jQuery("#tablepress-99-clone").show();
      
      window.clone_timeout = false;
    
	  window.setTimeout(function(){
		while(flip()){
			//loop through all items
		}
	
		clean();
	  }, 100);
    }, 100); 
    });
  });
	function flip(){
		var previous = null;
		jQuery("#tablepress-99-clone tr:not(.row-1)").each(function(){
			
			var children = 0; 
			var moveable_elems = [];
			jQuery(this).children().each(function(){ 
				if(jQuery(this).children().length > 0){
					children = children + 1;
					moveable_elems.push(jQuery(this));
				}
			});

			if (children < 3)
			{
				//Get the previous 
				var previous_children = 0; 
				jQuery(previous).children().each(function(){ 
					if(jQuery(this).children().length > 0){
						previous_children = previous_children + 1;
					}
				});

				if (previous_children < 3)
				{
					var diff = 3 - previous_children;

					for (var i = 0; i < diff; i++)
					{
						var to_move = moveable_elems[i];

						if (to_move)
						{
							//Remove the empty TDs in the previous

							if (i == 0)
							{
								jQuery(previous).children('[data-th^="Column"]').each(function(){ 
									if(jQuery(this).children().length == 0){
										jQuery(this).remove();
									}
								});
							}

							jQuery(previous).append(to_move);
							return true;
						}
						else
						{
							break;
						}
					}
				}
			}
			
			previous = this;
		});	

		return false;
	}

	function clean(){
		jQuery("#tablepress-99-clone tr:not(.row-1)").each(function(){
			var children = 0;
			jQuery(this).children().each(function(){ 
				if (jQuery(this).children().length > 0)
				{
					children = children + 1;
				}
			});
			if (children <= 0)
			{
				jQuery(this).remove();
			}
		});
	}
</script>
<!-- end Simple Custom CSS and JS -->
