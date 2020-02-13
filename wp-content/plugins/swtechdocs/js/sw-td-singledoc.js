(function() {
    
    jQuery(document).ready(function(){
        
        
        if (screen.width < 768)
        {            
             var top = jQuery('#book_container').offset().top - 50;
             jQuery('html,body').animate({scrollTop: top}, 1000);
            
        }
        
        function setTOCSize()
        {
            var footer_top = jQuery('#footer').offset().top; //get the offset top of the element
            var footer_offset = footer_top - (jQuery(window).scrollTop() + screen.height) + 166;
            var footer_distance = screen.height - footer_offset;
            
            var toc = document.getElementById("toc_container")
            var rect = toc.getBoundingClientRect();
            //Get the height of the TOC minus a 100 pixel pad
            var footer_max_height = screen.availHeight - rect.y - 100;

            var new_footer_height = footer_max_height + footer_offset - 100;
            
            var footer_height = Math.min(footer_max_height, new_footer_height);
            
            jQuery("#toc_container").height(footer_height + "px");
        }
        setTOCTop();
        setTOCSize();
        
        function setTOCTop()
        {
            var eTop = jQuery('.td-breadcrumbs').offset().top; //get the offset top of the element
            var offset = eTop - jQuery(window).scrollTop();
            var offset_pad = 70;

            if (offset > 0)
            {
                var loffset = offset + offset_pad;
                jQuery('#toc_container').css('top', loffset + 'px');
            }
            else
            {
                jQuery('#toc_container').css('top', offset_pad + 'px');      
            }
        }

        jQuery(window).scroll(function() { //when window is scrolled
            
            if (screen.width > 767)
            {
                setTOCTop();
                setTOCSize(); 
            }        
            
        });
    });
    
    setTimeout(function(){
        
        var toggles = document.getElementsByClassName('toc-collapse-toggle');

        for(var i = 0; i < toggles.length; i++) {
          (function(index) {
            toggles[index].addEventListener("click", function() {
                var toggle = toggles[index];
                var parentLi = toggle.parentElement;
                
                if ((' ' + parentLi.className + ' ').indexOf(' open ') > -1)
                {
                    //Remove the open class
                    parentLi.classList.remove("open");
                }
                else
                {
                    //Add the open class  
                    parentLi.classList.add("open");
                }
              
             })
          })(i);
        }
        
        //Loop throught the TOC and open any parent LI elements on load
        var to_open = document.getElementsByClassName('toggle-on-open');

        for(var i = 0; i < to_open.length; i++) {
          (function(index) {
            var open = to_open[index];              
              var elem = open.parentElement;
              
              while (elem.parentElement.nodeName != "DIV")
              {
                  elem = elem.parentElement;
                  
                  if (elem.nodeName == "LI")
                  {
                      elem.classList.add("open");
                  }
              }   
              
          })(i);
        }
        
        
        //Set a download link if the pdf variable exits
        if (window && window.pdf && window.pdf.length > 0)
        {
            var flare_search = document.getElementsByClassName("nav-search-wrapper");

            if (flare_search && flare_search.length > 0)
            {
                var flare_search = flare_search[0];
                var btn = document.createElement("A");
                btn.classList.add('td-download-btn');
                btn.href = window.pdf;
                btn.setAttribute("target", "_blank");
                btn.innerHTML = "Download PDF";
                
                flare_search.appendChild(btn);
                
            }
        }
        

        jQuery(".td-voting .td-helpfulness-voting .yes").click(function(){
            jQuery(".td-help-improve").hide();
            jQuery("#td-upvote-response").show();
            jQuery(".td-voting .td-helpfulness-voting .buttons").hide();
            jQuery("#gform_submit_button_73").css("opacity", "0").css("height", "0").click().hide();
            
        });
        
        jQuery(".td-voting .td-helpfulness-voting .no").click(function(){
           jQuery(".td-help-improve").hide();
           jQuery("#td-downvote-response").show();
           jQuery(".td-voting .td-helpfulness-voting .buttons").hide();
        });
    });
})();