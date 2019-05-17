<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
    jQuery(document).ready(function () {

        var redirectURL = "";
        jQuery('select').eq(1).on('change', function () {

            var redirectTo = getRedirectTo();

            if(getRedirectType() == "trial"){
                if(~jQuery(this).val().indexOf("TZ Series")){
                    redirectTo += "tz600.demo.sonicwall.com/main.html";
                }
                else if(~jQuery(this).val().indexOf("NSA")){
                    redirectTo += "nsa6600.demo.sonicwall.com/main.html";
                }
                else if(~jQuery(this).val().indexOf("Analyzer")){
                    redirectTo += "analyzer.demo.sonicwall.com/sgms/login";
                }
                else if(~jQuery(this).val().indexOf("SuperMassive")){
                    redirectTo += "topdog.demo.sonicwall.com/main.html";
                }
                else if(~jQuery(this).val().indexOf("SRA Series")){
                    redirectTo += "livedemo.sonicwall.com/#SRA";
                }
                else if(~jQuery(this).val().indexOf("Aventail")){
                    redirectTo += "livedemo.sonicwall.com/#SRA";
                }
                else if(~jQuery(this).val().indexOf("ESA")){
                    redirectTo += "cass.demo.sonicwall.com/main.html";
                }
                else{ //default to trials page if cant auto select landing page
                    redirectTo = "/resources";
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
                redirectTo = "https://";
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
