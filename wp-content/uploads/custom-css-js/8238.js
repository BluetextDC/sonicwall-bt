<!-- start Simple Custom CSS and JS -->
<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">



jQuery(document).ready(function () {
  	//obtain url paramater values
    var el_tid = getUrlParameter('elqTrackId');
    var el_cid = getUrlParameter('elqCampaignId');
	var el_utm_campaign = getUrlParameter('utm_campaign');
    var el_utm_medium = getUrlParameter('utm_medium');
	var el_utm_source = getUrlParameter('utm_source');

    
    



  	//Write cookies
    if(el_tid !="" ){//elqTrackId
		setCookie('el_tid',el_tid,90)
    }
   if(el_cid !="" ){//elqCampaignId
		setCookie('el_cid',el_cid,90)
    }
     if(el_utm_campaign !="" ){//utm_campaign
		setCookie('utm_campaign',utm_campaign,90)
    }
     if(utm_medium !="" ){//utm_medium
		setCookie('utm_medium',utm_medium,90)
    }
     if(el_utm_source !="" ){//utm_source
		setCookie('el_utm_source',el_utm_source,90)
    }
});

    

    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i &lt; ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==&#039; &#039;) c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
    function eraseCookie(name) {   
        document.cookie = name+&#039;=; Max-Age=-99999999;&#039;;  
    }

    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split(&#039;&amp;&#039;),
            sParameterName,
            i;

        for (i = 0; i &lt; sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split(&#039;=&#039;);

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };


</script>
<!-- end Simple Custom CSS and JS --><!-- end Simple Custom CSS and JS -->