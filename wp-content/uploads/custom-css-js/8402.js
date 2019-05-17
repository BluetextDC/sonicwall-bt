<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
/*
 * A script to save the eloqua tracking and campaign id in cookies
 * By Frank Feruch fferuch@sonicwall.com
 */


jQuery(document).ready(function () {
  	//obtain url paramater values
    
    console.log("Reading cookie data");
  
    var el_tid = getUrlParameter('elqTrackId');
    console.log(el_tid);
    var el_cid = getUrlParameter('elqCampaignId');
	var el_utm_campaign = getUrlParameter('utm_campaign');
    var el_utm_medium = getUrlParameter('utm_medium');
	var el_utm_source = getUrlParameter('utm_source');
    
    	//Write cookies
    if(el_tid !=undefined ){//elqTrackId
		setCookie('el_tid',el_tid,90)
    }
   if(el_cid !=undefined ){//elqCampaignId
		setCookie('el_cid',el_cid,90)
    }
     if(el_utm_campaign !=undefined ){//utm_campaign
		setCookie('utm_campaign',el_utm_campaign,90)
    }
     if(el_utm_medium !=undefined ){//utm_medium
		setCookie('utm_medium',el_utm_medium,90)
    }
     if(el_utm_source !=undefined ){//utm_source
		setCookie('utm_source',el_utm_source,90)
    }
    
    
    
 console.log("tid:"+el_tid);
  
  	if(el_tid ==undefined){
         console.log("tid is undefined");
  
    	el_tid = getCookie('el_tid');
         console.log("tid is now"+el_tid);
        jQuery(".elqCampaignId > div > input").val(el_tid)
    }
    if(el_cid ==undefined){
    	el_cid = getCookie('el_cid');
        jQuery(".elqCampaignId > div > input").val(el_cid)
    }
    if(el_utm_campaign ==undefined){
    	el_utm_campaign = getCookie('utm_campaign');
        jQuery(".elqCampaignId > div > input").val(el_utm_campaign)
    }
    if(el_utm_medium ==undefined){
    	el_utm_medium = getCookie('utm_medium');
        jQuery(".utm_medium > div > input").val(el_utm_medium)
    }
     if(el_utm_source ==undefined){
    	el_utm_source = getCookie('utm_source');
        jQuery(".utm_source > div > input").val(el_utm_source)
    }
  
  
});

    /*Functions */

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
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
    function eraseCookie(name) {   
        document.cookie = name+'=; Max-Age=-99999999;';  
    }

    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };



<!-- end Simple Custom CSS and JS -->
</script>
<!-- end Simple Custom CSS and JS -->
