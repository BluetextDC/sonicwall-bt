<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
jQuery(document).ready(function( $ ){

    var screenWidth = jQuery(document).width();
    var data_th = jQuery('.tablepress thead th');
    var len = data_th.length;
    var flag = true;
    for(var i=1;i&amp;lt;len; i++){
        jQuery('.column-'+ (i+1)).attr('data-th',data_th.eq(i).text());
    }


}); 


</script>
<!-- end Simple Custom CSS and JS -->
