// Immediately-invoked function expression
(function() {
    // Load the script
    var script = document.createElement("SCRIPT");
    script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js';
    script.type = 'text/javascript';

    script.onload = function() {

    	$.fn.isOnScreen = function(){
    
		    var win = $(window);
		    
		    var viewport = {
		        top : win.scrollTop(),
		        left : win.scrollLeft()
		    };
		    viewport.right = viewport.left + win.width();
		    viewport.bottom = viewport.top + win.height();
		    
		    var bounds = this.offset();
		    bounds.right = bounds.left + this.outerWidth();
		    bounds.bottom = bounds.top + this.outerHeight();
		    
		    return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
		    
		};
    	
        jQuery(document).ready(function(){



            function slugify(string) {
              const a = 'àáäâãåăæąçćčđďèéěėëêęğǵḧìíïîįłḿǹńňñòóöôœøṕŕřßşśšșťțùúüûǘůűūųẃẍÿýźžż·/_,:;'
              const b = 'aaaaaaaaacccddeeeeeeegghiiiiilmnnnnooooooprrsssssttuuuuuuuuuwxyyzzz------'
              const p = new RegExp(a.split('').join('|'), 'g')

              return string.toString().toLowerCase()
                .replace(/\s+/g, '-') // Replace spaces with -
                .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
                .replace(/&/g, '-and-') // Replace & with 'and'
                .replace(/[^\w\-]+/g, '') // Remove all non-word characters
                .replace(/\-\-+/g, '-') // Replace multiple - with single -
                .replace(/^-+/, '') // Trim - from start of text
                .replace(/-+$/, '') // Trim - from end of text
            }

        	//good to, load the first pages
            var tocs = [];
            var toc_link = [];

            var last_title = false;

        	jQuery('.pdf-page').each(function( index, value ) {

        		//Build out the TOC arrays

        		//Always ignore the first page
        		if (index > 0)
        		{
        			var toc = jQuery(this).find('.pdf-obj-fixed').last().prev();
	        		var title = jQuery(toc).find('span').html();

	        		if (title && title != last_title)
	        		{
                        last_title = title;

                        var l = jQuery(this).attr("id");

                        var toc_node = {
                            title: title,
                            link: l,
                            subs: []
                        };
       			
                        //Look up sub elements
                        jQuery(this).find(".pdf-obj-fixed").each(function( i, v){

                            if (jQuery(v).height() >= 32)
                            {
                                var sub = jQuery(v).find("span");

                                if (sub.html().length > 2)
                                {
                                    var slug_nav_id = slugify(title + toc_node.subs.length);

                                    sub.attr("id", slug_nav_id);
                                    toc_node.subs.push(sub.html());
                                }
                            }
                        });


                        tocs.push(toc_node);

                        toc_link.push(l);

	        		}	
        		}
        		
        		
        		var _this = this;
        		setTimeout(function(){
        			jQuery(_this).show();
        			//Update the layout
        			pdfixUpdateLayout();
        		}, index * 100);
			});


			//Build the actual floating TOC

        	jQuery("#toc_container").append('<div id="toc_toggle"></div>')
        	jQuery("#toc_container").append('<div id="toc"><ul id="toc_list"></ul></div>');

        	jQuery("#toc_toggle").click(function(){
        		if (jQuery(this).css("right") == "250px")
        		{
        			//TOC is open
        			jQuery(this).css("right", "0");
        			jQuery(this).addClass("open");
        			jQuery("#toc").hide();
        		} 
        		else
        		{
        			//TOC is closed
        			jQuery(this).css("right", "250px");
        			jQuery(this).removeClass("open");
        			jQuery("#toc").show();
        		}
        	});

        	jQuery("#toc_list").append('<li><a href="#">Home</a></li>');

        	for (var i = 0; i < tocs.length; i++)
        	{
        		var title = tocs[i].title;
        		var link = tocs[i].link;

                var subs = tocs[i].subs;

                var subs_content = "<ul>";

                if (subs && subs.length > 0)
                {
                    for (var x = 0; x < subs.length; x++)
                    {
                        var sub = subs[x];

                        if (sub != title)
                        {
                            var slug_nav_id = slugify(title + x);
                            subs_content = subs_content + '<li><a href="#' + slug_nav_id + '">' + sub + '</a></li>';
                        }
                       
                    }

                }

                subs_content = subs_content + "</ul>";

                var content = '<li><a href="#' + link + '">' + title + '</a>' + subs_content + '</li>';

        		jQuery("#toc_list").append(content);

        	}

        	//Set first to active
        	jQuery("#toc li:first").addClass('active')

        	//Add the click handler
        	jQuery("#toc li").click(function(){
        		jQuery("#toc li").removeClass("active");
        		jQuery(this).addClass("active");
        	});

        	jQuery(window).bind("scroll", function() {
			   jQuery(".pdf-page").each(function(){
			   	if (jQuery(this).isOnScreen())
			   	{
			   		var active_id = jQuery(this).attr("id");

			   		if (toc_link.indexOf(active_id) > -1)
			   		{
			   			var all_toc = jQuery("#toc li")
			   			all_toc.removeClass("active");

			   			var toc_index = toc_link.indexOf(active_id) + 1
			   			jQuery(all_toc[toc_index]).addClass("active");
			   		}

			   		return false;
			   	}
			   });
			});

        });
    };
    document.getElementsByTagName("head")[0].appendChild(script);
})();
