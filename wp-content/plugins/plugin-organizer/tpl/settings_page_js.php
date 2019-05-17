<?php
if ( current_user_can( 'activate_plugins' ) ) {
	?>
	<script type="text/javascript" language="javascript">
		jQuery(function() {
			jQuery('#PO-custom-post-type-container .PO-post-type-container').sortable();
			jQuery('#PO-role-container .PO-available-roles-container').sortable();
			jQuery('#PO-tab-1').removeClass('ui-tabs-active ui-state-active');
			jQuery("#PO-tab-menu-container").tabs();


			jQuery('.PO-color-wrapper').each(function() {
				var colorWrapper = jQuery(this);
				jQuery(this).find('.PO-color-preview').css('backgroundColor', jQuery(this).find('.PO-colorpicker').val());
				
				jQuery(this).find('.PO-color-preview').ColorPicker({
					onSubmit: function(hsb, hex, rgb, el) {
						jQuery(el).css('backgroundColor', 'rgb('+rgb.r+', '+rgb.g+', '+rgb.b+')');
						jQuery(el).closest('.PO-color-wrapper').find('.PO-colorpicker').val('#'+hex);
						jQuery(el).ColorPickerHide();
					},
					onChange: function (hsb, hex, rgb) {
						jQuery(colorWrapper).find('.PO-color-preview').css('backgroundColor', 'rgb('+rgb.r+', '+rgb.g+', '+rgb.b+')');
						jQuery(colorWrapper).find('.PO-colorpicker').val('#'+hex);
					},
					onBeforeShow: function () {
						jQuery(this).ColorPickerSetColor(jQuery(colorWrapper).find('.PO-colorpicker').val());
					}
				});
					
				jQuery(this).find('.PO-colorpicker').ColorPicker({
					onSubmit: function(hsb, hex, rgb, el) {
						jQuery(el).parent().find('.PO-color-preview').css('backgroundColor', 'rgb('+rgb.r+', '+rgb.g+', '+rgb.b+')');
						jQuery(el).val('#'+hex);
						jQuery(el).ColorPickerHide();
					},
					onChange: function (hsb, hex, rgb) {
						jQuery(colorWrapper).find('.PO-color-preview').css('backgroundColor', 'rgb('+rgb.r+', '+rgb.g+', '+rgb.b+')');
						jQuery(colorWrapper).find('.PO-colorpicker').val('#'+hex);
					},
					onBeforeShow: function () {
						var colorVal = jQuery(colorWrapper).find('.PO-colorpicker').val();
						if (colorVal.length == 4) {
							colorVal = '#';
							for (var i=1; i<jQuery(colorWrapper).find('.PO-colorpicker').val().length; i++) {
								colorVal = colorVal + jQuery(colorWrapper).find('.PO-colorpicker').val()[i] + jQuery(colorWrapper).find('.PO-colorpicker').val()[i];
							}
						}
						jQuery(this).ColorPickerSetColor(colorVal);
					}
				});
			});
		});
		
		function PO_submit_mobile_user_agents() {
			var mobileUserAgents = jQuery('#PO-mobile-user-agents').val();
			var postVars = { 'PO_mobile_user_agents': mobileUserAgents, PO_nonce: '<?php print $this->PO->nonce; ?>' };
			PO_submit_ajax('PO_submit_mobile_user_agents', postVars, '#PO-browser-string-div', function(responseObj){
				if (responseObj['success'] == 1) {
					jQuery('#PO-mobile-user-agents').val(responseObj['user_agent_list']);
				}
			});
		}
	
		function PO_submit_gen_settings() {
			//Debug Roles
			var PO_debug_roles = new Array();
			jQuery('.PO-debug-roles').each(function() {
				if (this.checked) {
					PO_debug_roles.push(this.value);
				}
			});
			
			//Supported Post Types
			var PO_cutom_post_type = new Array();
			jQuery('.PO-cutom-post-type').each(function() {
				if (this.checked) {
					PO_cutom_post_type.push(this.value);
				}
			});

			//Supported Roles
			var PO_supported_roles = new Array();
			jQuery('.PO-available-roles').each(function() {
				if (this.checked) {
					PO_supported_roles.push(this.value);
				}
			});

			var postVars = {
				'PO_disable_plugins_frontend': jQuery('#PO-disable-plugins-frontend').prop('checked'),
				'PO_disable_plugins_mobile': jQuery('#PO-disable-plugins-mobile').prop('checked'),
				'PO_disable_plugins_admin': jQuery('#PO-disable-plugins-admin').prop('checked'),
				'PO_fuzzy_url_matching': jQuery('#PO-fuzzy-url-matching').prop('checked'),
				'PO_ignore_protocol': jQuery('#PO-ignore-protocol').prop('checked'),
				'PO_ignore_arguments': jQuery('#PO-ignore-arguments').prop('checked'),
				'PO_debug_roles[]': PO_debug_roles,
				'PO_cutom_post_type[]': PO_cutom_post_type,
				'PO_supported_roles[]': PO_supported_roles,
				'PO_order_access_net_admin': jQuery('#PO-order-access-net-admin').prop('checked'),
				'PO_auto_trailing_slash': jQuery('#PO-auto-trailing-slash').prop('checked'),
				'PO_disable_plugins_by_role': jQuery('#PO-disable-plugins-by-role').prop('checked'),
				'PO_display_debug_msg': jQuery('#PO-display-debug-msg').prop('checked'),
				'PO_nonce': '<?php print $this->PO->nonce; ?>'
			};
			
			PO_submit_ajax('PO_submit_gen_settings', postVars, '#PO-gen-settings-div', PO_reorder_post_types);
		}
		
		function PO_submit_redo_permalinks() {
			var old_site_address = jQuery('#PO-old-site-address').val();
			var new_site_address = jQuery('#PO-new-site-address').val();
			var postVars = { PO_nonce: '<?php print $this->PO->nonce; ?>', 'old_site_address': old_site_address, 'new_site_address': new_site_address };
			PO_submit_ajax('PO_redo_permalinks', postVars, '#PO-redo-permalinks-div', function(responseObj){});
		}

		function PO_manage_mu_plugin_file(selected_action) {
			if (selected_action != '') {
				var postVars = { 'selected_action': selected_action, PO_nonce: '<?php print $this->PO->nonce; ?>' };
				PO_submit_ajax('PO_manage_mu_plugin', postVars, '#PO-manage-mu-div', function(responseObj){});
			}
		}

		function PO_submit_admin_css_settings() {
			var postVars = {
				'PO_network_plugins_bg_color': jQuery('#PO-network-plugins-bg-color').val(),
				'PO_network_plugins_font_color': jQuery('#PO-network-plugins-font-color').val(),
				'PO_active_plugins_bg_color': jQuery('#PO-active-plugins-bg-color').val(),
				'PO_active_plugins_font_color': jQuery('#PO-active-plugins-font-color').val(),
				'PO_active_plugins_border_color': jQuery('#PO-active-plugins-border-color').val(),
				'PO_inactive_plugins_bg_color': jQuery('#PO-inactive-plugins-bg-color').val(),
				'PO_inactive_plugins_font_color': jQuery('#PO-inactive-plugins-font-color').val(),
				'PO_inactive_plugins_border_color': jQuery('#PO-inactive-plugins-border-color').val(),
				'PO_global_plugins_bg_color': jQuery('#PO-global-plugins-bg-color').val(),
				'PO_global_plugins_font_color': jQuery('#PO-global-plugins-font-color').val(),
				'PO_global_plugins_border_color': jQuery('#PO-global-plugins-border-color').val(),
				'PO_selected_plugins_bg_color': jQuery('#PO-selected-plugins-bg-color').val(),
				'PO_selected_plugins_font_color': jQuery('#PO-selected-plugins-font-color').val(),
				'PO_selected_plugins_border_color': jQuery('#PO-selected-plugins-border-color').val(),
				'PO_plugin_groups_bg_color': jQuery('#PO-plugin-groups-bg-color').val(),
				'PO_plugin_groups_font_color': jQuery('#PO-plugin-groups-font-color').val(),
				'PO_plugin_groups_border_color': jQuery('#PO-plugin-groups-border-color').val(),
				'PO_on_btn_bg_color': jQuery('#PO-on-btn-bg-color').val(),
				'PO_on_btn_font_color': jQuery('#PO-on-btn-font-color').val(),
				'PO_off_btn_bg_color': jQuery('#PO-off-btn-bg-color').val(),
				'PO_off_btn_font_color': jQuery('#PO-off-btn-font-color').val(),
				'PO_yes_btn_bg_color': jQuery('#PO-yes-btn-bg-color').val(),
				'PO_yes_btn_font_color': jQuery('#PO-yes-btn-font-color').val(),
				'PO_no_btn_bg_color': jQuery('#PO-no-btn-bg-color').val(),
				'PO_no_btn_font_color': jQuery('#PO-no-btn-font-color').val(),
				'PO_front_debug_style': jQuery('#PO-front-debug-style').val(),
				'PO_admin_debug_style': jQuery('#PO-admin-debug-style').val(),
				'PO_nonce': '<?php print $this->PO->nonce; ?>'
			};
			PO_submit_ajax('PO_submit_admin_css_settings', postVars, '#PO-manage-css-div', function(responseObj){});
		}

		function PO_reorder_post_types(responseObj) {
			jQuery(jQuery('#PO-custom-post-type-container .PO-post-type-container .PO-post-type-row').get().reverse()).each(function() {
				if (jQuery(this).find('.PO-cutom-post-type').is(':checked')) {
					var clonedRow = jQuery(this).clone();
					jQuery(this).remove();
					jQuery('#PO-custom-post-type-container .PO-post-type-container').prepend(clonedRow);
				}

			});
		}

		function PO_perform_plugin_search() {
			PO_toggle_loading('#PO-plugin-search-div');
			jQuery('#plugin-search-results').html('');
			var searchableRoles = new Array();
			jQuery('.PO-searchable-roles:checked').each(function() {
				searchableRoles.push(jQuery(this).val());
			});
			jQuery.post(encodeURI(ajaxurl + '?action=PO_perform_plugin_search'), {'PO_plugin_path': jQuery('#PO-installed-plugins').val(), 'PO_searchable_roles': searchableRoles, PO_nonce: '<?php print $this->PO->nonce; ?>'}, function (result) {
				jQuery('#plugin-search-results').show();
				var parsedResult = jQuery.parseJSON(result);
				if (parsedResult.length > 0) {
					jQuery('#plugin-search-results').append('<div id="PO-plugin-search-result-header">Results</div>');
					for (var i=0; i<parsedResult.length; i++) {
						jQuery('#plugin-search-results').append('<p><a href="'+parsedResult[i]['url']+'" target="_blank">'+parsedResult[i]['name']+'</a></p>');
					}
				} else {
					jQuery('#plugin-search-results').html('<h3>Plugin Not Found</h3>');
				}
				
				PO_toggle_loading('#PO-plugin-search-div');
			});
		}

	</script>
	<?php
}
?>