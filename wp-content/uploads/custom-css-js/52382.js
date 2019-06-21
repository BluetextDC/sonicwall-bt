<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
	jQuery(document).ready(function () {
		var H_sel = "#RH";
		var P_sel = ".row-";
		jQuery.each( [ "3", "5", "7", "9", "11", "13" ], function( i, l ){
			jQuery(H_sel + l).click(function () {
				jQuery(P_sel + l).toggle();
				if (jQuery(H_sel + l).attr('class') === 'fold') {
					jQuery(H_sel + l).attr('class', 'unfold');
				}else{
					jQuery(H_sel + l).attr('class', 'fold');
				}
			});
		});
	}); 
</script>
<!-- end Simple Custom CSS and JS -->
