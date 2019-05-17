<script type="application/javascript">
jQuery(document).ready(function() {
	jQuery('#input_60_5').change(function(){	
		p_interest = jQuery(this).val();
		asset_name = "Contact Sales";
		jQuery('#input_60_19').val(asset_name+ ' - ' + p_interest);
	});
});
</script>
