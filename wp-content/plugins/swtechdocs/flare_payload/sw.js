(function() {
    
    setTimeout(function(){
        var flare_nav = document.getElementsByClassName("sidenav-container");

        if (flare_nav && flare_nav.length > 0)
        {
            flare_nav = flare_nav[0];

            if (flare_nav)
            {
                //Now get the SW Nav and place inside the flare nav
                var sw_nav = document.getElementById("toc_container");
                
                if (sw_nav)
                {
                    flare_nav.appendChild(sw_nav);
                }
            }
        } 
        
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
                console.log("Added button!");
                
            }
        }
    });
})();