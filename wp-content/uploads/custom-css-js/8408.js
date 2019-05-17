<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
/*
 * A script to save the eloqua tracking and campaign id in cookies
 * By Frank Feruch fferuch@sonicwall.com
 */


jQuery(document).ready(function () {
    
  //Check if the cookies are present
    
  	//console.log("obtain url paramater values");  
    
  	var elqTrackId = getUrlParameter('elqTrackId');
  	var elqCampaignId = getUrlParameter('elqCampaignId');
	var utm_campaign = getUrlParameter('utm_campaign');
    var utm_medium = getUrlParameter('utm_medium');
	var utm_source = getUrlParameter('utm_source');
  
 	process_params('elqTrackId',elqTrackId);
 	process_params('elqCampaignId',elqCampaignId);
 	process_params('utm_campaign',utm_campaign);
 	process_params('utm_medium',utm_medium);
 	process_params('utm_source',utm_source);
  
  
  function  process_params(key,value){
    console.log("in pp with:"+key+" "+value);
    jquery_selector = "."+key+" > div > input";
    if(value !=undefined ){//elqTrackId
          setCookie(key,value,1);    
        //  console.log("read param exist and is : "+value);  
       jQuery(jquery_selector).val(value)
      }else{
          el_tid = getCookie(key);
      //    console.log("read param not exist loading from cookie : "+el_tid);  
          jQuery(jquery_selector).val(el_tid)
        console.log("selector:"+jquery_selector); 
      }
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
