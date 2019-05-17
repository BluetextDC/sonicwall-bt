<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
jQuery(document).ready(function(){
        setTimeout(function(){
            scroll_top();
            jQuery('div[role="tab"]').click(function(){
                scroll_top();
            });
            function scroll_top(){
                if(window.location.hash != ""){
                    jQuery('html,body').animate({
                        scrollTop:jQuery(window.location.hash+'-container').offset().top-150
                    });
                }
            }
        },50);
    });
</script>
<!-- end Simple Custom CSS and JS -->
