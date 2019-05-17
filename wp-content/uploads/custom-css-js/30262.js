<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
jQuery(function(){
        var screenWidth = jQuery(document).width();
        copyright_mobile();
        jQuery(window).resize(function(){
            screenWidth = jQuery(document).width();
            copyright_mobile();
        });
        function copyright_mobile(){
            if(screenWidth <= 420){
                var $copyright = jQuery('.copyright');
                var copyright_arr =$copyright.text().split('.',2);
                copyright_arr[1] = copyright_arr[1].trim();
                $copyright.html(copyright_arr[0] + '<br>' + copyright_arr[1]);
            }

        }

    })
</script>
<!-- end Simple Custom CSS and JS -->
