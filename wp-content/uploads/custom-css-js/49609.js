<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#input_60_5').change(function(){	
		p_interest = jQuery(this).val();
		asset_name = jQuery('#input_60_19').val();
		jQuery('#input_60_19').val(asset_name+ ' - ' + p_interest);
	});
});
</script>
<!-- end Simple Custom CSS and JS -->
