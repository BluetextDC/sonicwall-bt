<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
    jQuery(function(){
        var screenWidth = jQuery(document).width();
        var $copyright = jQuery('.copyright');
        var copyright_val = $copyright.text();
        copyright_mobile();
        jQuery(window).resize(function(){
            screenWidth = jQuery(document).width();
            copyright_mobile();
        });
        function copyright_mobile(){
            if(screenWidth <= 420){
                var copyright_arr =copyright_val.split('.',2);
                copyright_arr[1] = copyright_arr[1].trim();
                $copyright.html(copyright_arr[0] + '<br>' + copyright_arr[1]);
            }else {
                $copyright.html(copyright_val);
            }
        }

    })
</script>
<!-- end Simple Custom CSS and JS -->
