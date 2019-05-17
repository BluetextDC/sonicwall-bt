<?php
global $wpdb;
if ( current_user_can( 'activate_plugins' ) ) {
	?>
	<script type="text/javascript" language="javascript">
		function PO_submit_search_plugins(){
			var disabledListForSubmit = {};
			var disabledMobileListForSubmit = {};
			var disabledGroupListForSubmit = {};
			var disabledMobileGroupListForSubmit = {};

			jQuery('.PO-disabled-std-plugin-list').each(function() {
				var roleName = jQuery(this).prop('name').replace('PO_disabled_std_plugin_list[', '').replace('][]', '');
				if (typeof(disabledListForSubmit[roleName]) == 'undefined' || disabledListForSubmit[roleName].constructor != Array) {
					disabledListForSubmit[roleName] = new Array();
				}
				disabledListForSubmit[roleName].push(jQuery(this).val());
			});
			
			jQuery('.PO-disabled-mobile-plugin-list').each(function() {
				var roleName = jQuery(this).prop('name').replace('PO_disabled_mobile_plugin_list[', '').replace('][]', '');
				if (typeof(disabledMobileListForSubmit[roleName]) == 'undefined' || disabledMobileListForSubmit[roleName].constructor != Array) {
					disabledMobileListForSubmit[roleName] = new Array();
				}
				disabledMobileListForSubmit[roleName].push(jQuery(this).val());
			});
			
			jQuery('.PO-disabled-std-group-list').each(function() {
				var roleName = jQuery(this).prop('name').replace('PO_disabled_std_group_list[', '').replace('][]', '');
				if (typeof(disabledGroupListForSubmit[roleName]) == 'undefined' || disabledGroupListForSubmit[roleName].constructor != Array) {
					disabledGroupListForSubmit[roleName] = new Array();
				}
				disabledGroupListForSubmit[roleName].push(jQuery(this).val());
			});
			
			jQuery('.PO-disabled-mobile-group-list').each(function() {
				var roleName = jQuery(this).prop('name').replace('PO_disabled_mobile_group_list[', '').replace('][]', '');
				if (typeof(disabledMobileGroupListForSubmit[roleName]) == 'undefined' || disabledMobileGroupListForSubmit[roleName].constructor != Array) {
					disabledMobileGroupListForSubmit[roleName] = new Array();
				}
				disabledMobileGroupListForSubmit[roleName].push(jQuery(this).val());
			});

			var postVars = { 'PO_disabled_std_plugin_list': disabledListForSubmit, 'PO_disabled_mobile_plugin_list': disabledMobileListForSubmit, 'PO_disabled_std_group_list': disabledGroupListForSubmit, 'PO_disabled_mobile_group_list': disabledMobileGroupListForSubmit, PO_nonce: '<?php print $this->PO->nonce; ?>' };
			PO_submit_ajax('PO_save_search_plugins', postVars, '#PO-search-plugins-container', function(){});
		}
	</script>
	<?php
}
?>