<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
jQuery(document).ready(function () {

        var redirectURL = "";
        jQuery('select').eq(1).on('change', function () {

            var redirectTo = getRedirectTo();

            if(getRedirectType() == "trial"){
                if(~jQuery(this).val().indexOf("TZ Series")){
                    redirectTo += "tz-virtual-trial";
                }
                else if(~jQuery(this).val().indexOf("NSA")){
                    redirectTo += "nsa-virtual-trial";
                }
                else if(~jQuery(this).val().indexOf("Analyzer")){
                    redirectTo += "analyzer-virtual-trial";
                }
                else if(~jQuery(this).val().indexOf("SuperMassive")){
                    redirectTo += "supermassive-virtual-trial";
                }
                else if(~jQuery(this).val().indexOf("SRA Series")){
                    redirectTo += "sra-virtual-trial";
                }
                else if(~jQuery(this).val().indexOf("Aventail")){
                    redirectTo += "sra-virtual-trial";
                }
                else if(~jQuery(this).val().indexOf("ESA")){
                    redirectTo += "anti-spam-service-virtual-trial";
                }
                else{ //default to trials page if cant auto select landing page
                    redirectTo = "/resources?resourceID=660";
                }
            }
            else{
                redirectTo += "?product=" + jQuery(this).val();
            }
            redirectURL = redirectTo;
        });


        function getRedirectType(){
            var redirectType =  jQuery('select').eq(0).val();
            return redirectType;
        }

        function getRedirectTo(){
            var redirectTo = "";

            if (getRedirectType() == "trial") {
                redirectTo = "/lp/";
            }
            else {
                redirectTo = "/customers/contact-sales/"
            }

            return redirectTo;
        }

        jQuery('input[type="submit"]').on('click',function(e){
            e.preventDefault();

            window.location.replace(redirectURL);
        });

    });
</script>
<!-- end Simple Custom CSS and JS -->
