<?php
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-droppable');
wp_enqueue_script('jquery-ui-draggable');
wp_enqueue_script('jquery-ui-tooltip');
wp_enqueue_script('jquery-ui-dialog');
?>
<script language="javascript" src="<?php print $this->PO->urlPath; ?>/js/validation.js"></script>
<script language="javascript" type="text/javascript">
	var tmpObjectCount = 0;
	<?php
	global $wpdb;
	if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'PO_global_plugins') {
		$storedPluginLists = array();
	} else {
		$sql = "SELECT disabled_plugins, disabled_mobile_plugins, disabled_groups, disabled_mobile_groups FROM ".$wpdb->prefix."po_plugins WHERE post_type='global_plugin_lists' AND post_id=0";
		$storedPluginLists = $wpdb->get_row($sql, ARRAY_A);
	}
	$globalPluginLists = array(
		'std_plugins'=>(is_array(@unserialize($storedPluginLists['disabled_plugins'])))? @unserialize($storedPluginLists['disabled_plugins']):array(), 
		'mobile_plugins'=>(is_array(@unserialize($storedPluginLists['disabled_mobile_plugins'])))? @unserialize($storedPluginLists['disabled_mobile_plugins']):array(),
		'std_groups'=>(is_array(@unserialize($storedPluginLists['disabled_groups'])))? @unserialize($storedPluginLists['disabled_groups']):array(),
		'mobile_groups'=>(is_array(@unserialize($storedPluginLists['disabled_mobile_groups'])))? @unserialize($storedPluginLists['disabled_mobile_groups']):array()
	);
	?>
	var globalPlugins = <?php print json_encode($globalPluginLists); ?>;

	var toggleButtonOptions = [['Off','On'], ['No','Yes']];
	function PO_reverse_toggle_buttons() {
		toggleButtonOptions = [['On','Off'], ['Yes','No']];
	}
	
	jQuery(function() {
		PO_attach_help_dialog();
		jQuery('#post').submit(function(e) {
			jQuery('.PO-permalink-input').removeClass('badInput');
			jQuery('.PO-permalink-input').each(function() {
				var thisPermalinkInput = jQuery(this);
				var thisPermalinkVal = jQuery(this).val();
				var thisPermalinkName = jQuery(this).prop('name');
				jQuery('.PO-permalink-input').each(function() {
					if (jQuery(this).prop('name') != thisPermalinkName && jQuery(this).val() == thisPermalinkVal) {
						jQuery(this).addClass('badInput');
						thisPermalinkInput.addClass('badInput');
						e.preventDefault();
						PO_display_ui_dialog("Duplicate Permalinks", "You have 2 or more permalinks that are the same.  Each permalink must be unique.")
					}
				});

			});
		});
		
		jQuery('#PO-activate-pt-override').change(function() {
			PO_activate_pt_override();
		});

		jQuery('#PO-pt-override').change(function() {
			PO_deactivate_pt_override();
		});
		
		PO_set_expand_info_action();
		
		PO_attach_ui_handlers();

		jQuery('.outerPluginWrap.disabledPlugins').droppable({
			tolerance: "pointer",
			accept: '.pluginWrap, .groupWrap',
			drop: function(event, ui) {
				var targetContainer = jQuery(this);
				jQuery('#draggingContainer .ui-draggable').each(function() {
					var newElement = jQuery(this).clone();
					var roleName = targetContainer.find('.user_role_name').val();
					if (newElement.hasClass('pluginWrap')) {
						var pluginID = newElement.find('.PO-plugin-id').val();
						if (targetContainer.find(':hidden[value="'+pluginID+'"]').length == 0) {
							var targetType = '';
							if (targetContainer.hasClass('PO-disabled-mobile-plugin-wrap')) {
								targetType = 'mobile';
								newElement.find('.PO-disabled-item-id').remove();
								newElement.append('<input type="hidden" class="PO-disabled-item-id PO-disabled-mobile-plugin-list" name="PO_disabled_mobile_plugin_list['+roleName+'][]" value="'+pluginID+'" />');
								if (jQuery.inArray(pluginID, globalPlugins['mobile_plugins']) > -1) {
									newElement.addClass('globalPluginWrap');
								}
							} else if (targetContainer.hasClass('PO-disabled-std-plugin-wrap')) {
								targetType = 'std';
								newElement.find('.PO-disabled-item-id').remove();
								newElement.append('<input type="hidden" class="PO-disabled-item-id PO-disabled-std-plugin-list" name="PO_disabled_std_plugin_list['+roleName+'][]" value="'+pluginID+'" />');
								if (jQuery.inArray(pluginID, globalPlugins['std_plugins']) > -1) {
									newElement.addClass('globalPluginWrap');
								}
							}
							var orderPosition = parseInt(jQuery(newElement).find('.PO-plugin-order').val(), 10);
							var itemAdded = 0;
							jQuery('#PO-disabled-'+targetType+'-'+roleName+'-plugin-wrap .PO-plugin-order').each(function() {
								if (parseInt(jQuery(this).val()) < orderPosition) {
									jQuery(this).closest('.pluginWrap').after(newElement);
									itemAdded = 1;
								}
							});

							if (itemAdded == 0) {
								targetContainer.find('.pluginListSubHead.plugins').after(newElement);
							}
							PO_attach_ui_handlers();

							PO_activate_indicator('plugin', targetType, pluginID, roleName);
						} else {
							var pluginWrapper = targetContainer.find(':hidden[value="'+pluginID+'"]').closest('.pluginWrap');
							pluginWrapper.fadeOut(100);
							pluginWrapper.fadeIn(100);
							pluginWrapper.fadeOut(100);
							pluginWrapper.fadeIn(100);
						}
					} else if (newElement.hasClass('groupWrap')) {
						var groupID = newElement.find('.PO-group-id').val();
						if (targetContainer.find(':hidden[value="'+newElement.find('.PO-group-id').val()+'"]').length == 0) {
							var targetType = '';
							if (targetContainer.hasClass('PO-disabled-mobile-plugin-wrap')) {
								targetType = 'mobile';
								newElement.find('.PO-disabled-item-id').remove();
								newElement.append('<input type="hidden" class="PO-disabled-item-id PO-disabled-mobile-group-list" name="PO_disabled_mobile_group_list['+roleName+'][]" value="'+newElement.find('.PO-group-id').val()+'" />');
								if (jQuery.inArray(newElement.find('.PO-group-id').val(), globalPlugins['mobile_groups']) > -1) {
									newElement.addClass('globalGroupWrap');
								}
							} else if (targetContainer.hasClass('PO-disabled-std-plugin-wrap')) {
								targetType = 'std';
								newElement.find('.PO-disabled-item-id').remove();
								newElement.append('<input type="hidden" class="PO-disabled-item-id PO-disabled-std-group-list" name="PO_disabled_std_group_list['+roleName+'][]" value="'+newElement.find('.PO-group-id').val()+'" />');
								if (jQuery.inArray(newElement.find('.PO-group-id').val(), globalPlugins['std_groups']) > -1) {
									newElement.addClass('globalGroupWrap');
								}
							}
							var orderPosition = parseInt(jQuery(newElement).find('.PO-group-order').val(), 10);
							var itemAdded = 0;
							jQuery('#PO-disabled-'+targetType+'-'+roleName+'-plugin-wrap .PO-group-order').each(function() {
								if (parseInt(targetContainer.val()) < orderPosition) {
									targetContainer.closest('.groupWrap').after(newElement);
									itemAdded = 1;
								}
							});

							if (itemAdded == 0) {
								targetContainer.find('.pluginListSubHead.groups').after(newElement);
							}
							
							PO_attach_ui_handlers();

							PO_activate_indicator('group', targetType, groupID, roleName);
						} else {
							var pluginWrapper = targetContainer.find(':hidden[value="'+newElement.find('.PO-group-id').val()+'"]').closest('.groupWrap');
							pluginWrapper.fadeOut(100);
							pluginWrapper.fadeIn(100);
							pluginWrapper.fadeOut(100);
							pluginWrapper.fadeIn(100);
						}
					}
				});
			}
		});

		jQuery('#PO-all-plugin-wrap.outerPluginWrap').droppable({
			tolerance: "pointer",
			accept: '.pluginWrap, .groupWrap',
			drop: function(event, ui) {
				if (ui.draggable.closest('#PO-all-plugin-wrap').length < 1) {
					var sourceContainer = ui.draggable.closest('.outerPluginWrap.disabledPlugins');
					var targetType = '';
					if (ui.draggable.hasClass('pluginWrap')) {
						targetType = 'plugin';
					} else {
						targetType = 'group';
					}

					var targetPlatform = '';
					if (ui.draggable.find('.PO-disabled-item-id').hasClass('PO-disabled-mobile-'+targetType+'-list')) {
						targetPlatform = 'mobile';
					} else {
						targetPlatform = 'std';
					}
					
					jQuery('#draggingContainer .ui-draggable').each(function() {
						PO_deactivate_indicator(targetType, targetPlatform, jQuery(this).find('.PO-disabled-item-id').val(), sourceContainer.find('.user_role_name').val());
						sourceContainer.find('.PO-disabled-item-id[value="'+jQuery(this).find('.PO-disabled-item-id').val()+'"]').closest('.'+targetType+'Wrap').remove();
					});
				}
				
			}
		});


		jQuery('#PO-ui-notices').dialog({
			dialogClass: 'PO-ui-dialog',
			closeText: 'X',
			autoOpen: false,
			resizable: false,
			height: "auto",
			width: (jQuery(window).width() > 400)?'400':jQuery(window).width()-20,
			modal: true,
			position: {within: '.PO-content-wrap'},
			buttons: {
				"Ok": function() {
					jQuery(this).dialog("close");
				}
			},
			open: function(event, ui) {
				jQuery('.ui-widget-overlay.ui-front').css('position', 'fixed');
				jQuery('.ui-widget-overlay.ui-front').css('left', '0px');
				jQuery('.ui-widget-overlay.ui-front').css('right', '0px');
				jQuery('.ui-widget-overlay.ui-front').css('top', '0px');
				jQuery('.ui-widget-overlay.ui-front').css('bottom', '0px');
				jQuery('.ui-widget-overlay.ui-front').css('background', '#000');
				jQuery('.ui-widget-overlay.ui-front').css('opacity', '.5');
				jQuery('.ui-widget-overlay.ui-front').css('zIndex', '9998');
			}
		});


		jQuery('.outerPluginWrap.disabledPlugins .pluginListHead').click(function() {
			var disableToggle = jQuery(this).find('.disabledListToggle');
			if (disableToggle.hasClass('fa-plus-square-o')) {
				disableToggle.removeClass('fa-plus-square-o');
				disableToggle.addClass('fa-minus-square-o');
				jQuery(this).closest('.outerPluginWrap').find('.disabledList').slideDown(300);
			} else {
				disableToggle.removeClass('fa-minus-square-o');
				disableToggle.addClass('fa-plus-square-o');
				jQuery(this).closest('.outerPluginWrap').find('.disabledList').slideUp(300);
			}
		});

		jQuery('.move-all-button').tooltip({
			content: function() {
				return PO_format_tooltip(jQuery(this).attr('title'));
			},
			tooltipClass: "PO-ui-button-tooltip"
		});

		jQuery('.outerPluginWrap.disabledPlugins .PO-plugin-id').each(function() {
			jQuery(this).closest('.pluginNameContainer').find('.PO-plugin-order').val(jQuery('#PO-all-plugin-wrap .PO-plugin-id[value="'+jQuery(this).val()+'"]').closest('.pluginNameContainer').find('.PO-plugin-order').val());
		});

		jQuery('.outerPluginWrap.disabledPlugins .PO-group-id').each(function() {
			jQuery(this).closest('.pluginNameContainer').find('.PO-group-order').val(jQuery('#PO-all-plugin-wrap .PO-group-id[value="'+jQuery(this).val()+'"]').closest('.pluginNameContainer').find('.PO-group-order').val());
		});

		jQuery('#PO-add-permalink').click(function() {
			PO_add_permalink();
		});
	});
	
	function PO_attach_help_dialog() {
		jQuery('.PO-help-dialog').tooltip({
			content: function() {
				return PO_format_tooltip(jQuery(this).attr('title'));
			},
			show: {
				effect: "slideDown",
				delay: 50
			},
			hide: {
				effect: "slideUp",
				delay: 50
			},
			tooltipClass: "PO-ui-tooltip"
		});
	}
	
	function PO_format_tooltip(tooltipTxt) {
		var formattedTxt = tooltipTxt.replace(/__ts__/g, '<div class="tooltip-title">');
		formattedTxt = formattedTxt.replace(/__rs__/g, '<div class="tooltip-row">');
		return formattedTxt.replace(/(__te__|__re__)/g, '</div>');
	}
	
	function PO_add_permalink() {
		jQuery('#PO-permalink-container').append('<div class="PO-permalink-wrapper"><input type="hidden" name="PO_pl_id[]" value="tmp_'+tmpObjectCount+'"><input type="text" class="PO-permalink-input" size="25" name="PO_permalink_filter_tmp_'+tmpObjectCount+'" value=""><input type="button" class="PO-delete-permalink" value="X"></div>');
		tmpObjectCount++;
		PO_attach_ui_handlers();
	}
	
	function PO_activate_indicator(itemType, itemPlatform, itemID, roleID) {
		if (jQuery('#PO-all-plugin-wrap .PO-'+itemType+'-id[value="'+itemID+'"]').closest('.'+itemType+'Wrap').find('.disabled-'+itemPlatform+'-roles[value="'+roleID+'"]').length == 0) {
			jQuery('#PO-all-plugin-wrap .PO-'+itemType+'-id[value="'+itemID+'"]').closest('.'+itemType+'Wrap').find('.pluginNameContainer').append('<input type="hidden" class="disabled-roles disabled-'+itemPlatform+'-roles" value="'+roleID+'">');
		}
		var fullPlatform = 'Standard';
		if (itemPlatform == 'mobile') {
			fullPlatform = 'Mobile';
		}

		jQuery('#PO-all-plugin-wrap .PO-'+itemType+'-id[value="'+itemID+'"]').closest('.'+itemType+'Wrap').addClass('disabled'+fullPlatform);
	}
	
	function PO_deactivate_indicator(itemType, itemPlatform, itemID, roleID) {
		jQuery('#PO-all-plugin-wrap .PO-'+itemType+'-id[value="'+itemID+'"]').closest('.'+itemType+'Wrap').find('.disabled-'+itemPlatform+'-roles[value="'+roleID+'"]').remove();
		var fullPlatform = 'Standard';
		if (itemPlatform == 'mobile') {
			fullPlatform = 'Mobile';
		}
		jQuery('#PO-all-plugin-wrap .PO-'+itemType+'-id[value="'+itemID+'"]').closest('.'+itemType+'Wrap').removeClass('disabled'+fullPlatform);
	}
	
	function PO_display_ui_dialog(dialogTitle, dialogText) {
		jQuery('.PO-ui-dialog .ui-dialog-title').html(dialogTitle);
		jQuery('#PO-ajax-notices-container').html(dialogText);
		jQuery('#PO-ui-notices').dialog('open');
	}
	
	function PO_attach_ui_handlers() {
		jQuery('.show-disabled-roles').tooltip({
			content: function() {
				var roleDialog = '<div class="tooltip-title">Standard Roles</div>';
				if (jQuery(this).closest('.pluginNameContainer').find('.disabled-std-roles').length == 0) {
					roleDialog += '<div class="tooltip-row">&nbsp;</div>';
				} else {
					jQuery(this).closest('.pluginNameContainer').find('.disabled-std-roles').each(function() {
						roleDialog += '<div class="tooltip-row">'+PO_role_array[jQuery(this).val()]+'</div>';
					});
				}

				roleDialog += '<div class="tooltip-title">Mobile Roles</div>';
				if (jQuery(this).closest('.pluginNameContainer').find('.disabled-mobile-roles').length == 0) {
					roleDialog += '<div class="tooltip-row">&nbsp;</div>';
				} else {
					jQuery(this).closest('.pluginNameContainer').find('.disabled-mobile-roles').each(function() {
						roleDialog += '<div class="tooltip-row">'+PO_role_array[jQuery(this).val()]+'</div>';
					});
				}
				
				return roleDialog;
			},
			show: {
				effect: "slideDown",
				delay: 50
			},
			hide: {
				effect: "slideUp",
				delay: 50
			},
			tooltipClass: "PO-role-ui-tooltip"
		});
		
		jQuery('.groupWrap').tooltip({
			content: function() {
				return PO_format_tooltip(jQuery(this).attr('title'));
			},
			show: {
				effect: "slideDown",
				delay: 50
			},
			hide: {
				effect: "slideUp",
				delay: 50
			},
			tooltipClass: "PO-ui-tooltip"
		});
		
		jQuery( ".pluginWrap, .groupWrap" ).draggable({
			cursor: 'move',
			cursorAt: { top: 10, left: 10 },
			helper: function(){
				var targetType = '';
				if (jQuery(this).hasClass('pluginWrap')) {
					targetType = 'plugin';
				} else if (jQuery(this).hasClass('groupWrap')) {
					targetType = 'group';
				}
				
				if (jQuery(this).hasClass('selected')) {
					var selected = jQuery(this).closest('.outerPluginWrap').find('.'+targetType+'Wrap.selected').clone();
					if (selected.length === 0) {
					  selected = jQuery(this).clone();
					}
				} else {
					var selected = jQuery(this).clone();
				}
				selected.find('.show-disabled-roles, .disbaled-roles').remove();
				selected.removeClass('selected');
				var container = jQuery('<div/>').attr('id', 'draggingContainer');
				container.append(selected.clone());
				return container; 
			},
			containment:'document',
			start: function(ev, ui) {
				ui.helper.addClass('PO-ui-draggable-dragging');
			}
		});

		jQuery('.outerPluginWrap .pluginWrap, .outerPluginWrap .groupWrap').off('click.pluginOrganizer');
		jQuery('.outerPluginWrap .pluginWrap, .outerPluginWrap .groupWrap').on('click.pluginOrganizer', function() {
			if (jQuery(this).hasClass('selected')) {
				jQuery(this).removeClass('selected');
			} else {
				jQuery(this).addClass('selected');
			}
		});

		jQuery('.PO-delete-permalink').off('click.pluginOrganizer');
		jQuery('.PO-delete-permalink').on('click.pluginOrganizer', function() {
			jQuery(this).closest('.PO-permalink-wrapper').remove();
		});
	}
	
	
	function PO_activate_pt_override() {
		jQuery('#PO-pt-override-msg-container').hide();
		jQuery('#PO-post-meta-box-wrapper').show();
		jQuery('#PO-pt-override').prop('checked', true);
	}

	function PO_deactivate_pt_override() {
		if (jQuery('#PO-activate-pt-override').prop('checked')) {
			jQuery('#PO-pt-override-msg-container').show();
			jQuery('#PO-post-meta-box-wrapper').hide();
			jQuery('#PO-pt-override').prop('checked', false);
			jQuery('#PO-activate-pt-override').prop('checked', false);
		}
	}
	
	function PO_set_expand_info_action() {
		jQuery('.expand-info-icon').each(function() {
			jQuery(this).unbind();
			var targetID = jQuery(this).prop('id').replace('PO-expand-info-', '');
			var infoContainer = jQuery('#PO-info-container-' + targetID);
			if (!jQuery(infoContainer).find('.PO-info-inner').html().match(/^\s*$/)) {
				jQuery(this).click(function() {
					if (jQuery(this).hasClass('fa-plus-square-o')) {
						jQuery(this).removeClass('fa-plus-square-o');
						jQuery(this).addClass('fa-minus-square-o');
						infoContainer.slideDown(300);
					} else {
						jQuery(this).removeClass('fa-minus-square-o');
						jQuery(this).addClass('fa-plus-square-o');
						infoContainer.slideUp(300);
					}
				});
			}
		});
	}
	
	function PO_toggle_loading(container) {
		jQuery(container+' .PO-loading-container').toggle();
		jQuery(container+' .inside').toggle();
	}
	
	function PO_add_all(sourceType, targetType, targetClass, targetRole) {
		if (targetClass == '') {
			jQuery('#PO-disabled-'+targetType+'-'+targetRole+'-plugin-wrap .'+sourceType+'Wrap').remove();
		}

		jQuery(jQuery('#PO-all-plugin-wrap .'+sourceType+'Wrap'+targetClass).get().reverse()).each(function() {
			var newElement = jQuery(this).clone();
			newElement.find('.show-disabled-roles, .disbaled-roles').remove();
			if (targetClass != '') {
				newElement.removeClass(targetClass.replace('.', ''));
			}
			//alert('#PO-disabled-'+targetType+'-plugin-wrap');
			if (jQuery('#PO-disabled-'+targetType+'-'+targetRole+'-plugin-wrap').find(':hidden[value="'+newElement.find('.PO-'+sourceType+'-id').val()+'"]').length == 0) {
				newElement.append('<input type="hidden" class="PO-disabled-item-id PO-disabled-'+targetType+'-'+sourceType+'-list" name="PO_disabled_'+targetType+'_'+sourceType+'_list['+targetRole+'][]" value="'+newElement.find('.PO-'+sourceType+'-id').val()+'" />');
				if (jQuery.inArray(newElement.find('.PO-'+sourceType+'-id').val(), globalPlugins[targetType+'_'+sourceType+'s']) > -1) {
					newElement.addClass('global'+sourceType.charAt(0).toUpperCase()+sourceType.slice(1)+'Wrap');
				}
				if (targetClass != '') {
					var orderPosition = parseInt(jQuery(this).find('.PO-'+sourceType+'-order').val());
					var itemAdded = 0;
					jQuery('#PO-disabled-'+targetType+'-'+targetRole+'-plugin-wrap .PO-'+sourceType+'-order').each(function() {
						if (parseInt(jQuery(this).val()) < orderPosition) {
							jQuery(this).closest('.'+sourceType+'Wrap').after(newElement);
							itemAdded = 1;
						}
					});

					if (itemAdded == 0) {
						jQuery('#PO-disabled-'+targetType+'-'+targetRole+'-plugin-wrap').find('.pluginListSubHead.'+sourceType+'s').after(newElement);
					}
				} else {
					jQuery('#PO-disabled-'+targetType+'-'+targetRole+'-plugin-wrap').find('.pluginListSubHead.'+sourceType+'s').after(newElement);
				}
				if (targetType == 'std') {
					PO_activate_indicator(sourceType, 'std', newElement.find('.PO-'+sourceType+'-id').val(), targetRole);
				} else {
					PO_activate_indicator(sourceType, 'mobile', newElement.find('.PO-'+sourceType+'-id').val(), targetRole);
				}
			}
		});
		PO_attach_ui_handlers();
	}

	function PO_remove_all(sourceType, targetType, targetClass, targetRole) {
		jQuery('#PO-disabled-'+targetType+'-'+targetRole+'-plugin-wrap .'+sourceType+'Wrap'+targetClass).each(function() {
			if (targetType == 'std') {
				PO_deactivate_indicator(sourceType, 'std', jQuery(this).find('.PO-'+sourceType+'-id').val(), targetRole);
			} else {
				PO_deactivate_indicator(sourceType, 'mobile', jQuery(this).find('.PO-'+sourceType+'-id').val(), targetRole);
			}
			jQuery(this).remove();
		});
	}

	function PO_toggle_button(checkboxID, buttonPrefix, optionIndex) {
		if (jQuery('#'+checkboxID).prop('checked') == false) {
			PO_set_button(jQuery('#'+checkboxID), 1, buttonPrefix, optionIndex);
		} else {
			PO_set_button(jQuery('#'+checkboxID), 0, buttonPrefix, optionIndex);
		}
	}
	
	function PO_set_button(checkbox, onOff, buttonPrefix, optionIndex) {
		if (onOff == 1) {
			jQuery(checkbox).prop('checked', true);
		} else {
			jQuery(checkbox).prop('checked', false);
		}
		jQuery(checkbox).parent().find("input[type='button']").removeClass();
		jQuery(checkbox).parent().find("input[type='button']").addClass(buttonPrefix+'toggle-button-'+toggleButtonOptions[optionIndex][onOff].toLowerCase());
		jQuery(checkbox).parent().find("input[type='button']").attr('value',toggleButtonOptions[optionIndex][onOff]);
	}
	
	function PO_reset_post_settings(postID) {
		PO_toggle_loading('#PO-meta-wrap');
		jQuery.post(encodeURI(ajaxurl + '?action=PO_reset_post_settings'), { 'postID': postID, PO_nonce: '<?php print $this->PO->nonce; ?>' }, function (result) {
			try {
				var responseObj = jQuery.parseJSON(result);
			} catch(err) {
				var responseObj = {'success': 0, 'alerts': ['There was an issue removing the settings.']};
			}
			
			if (responseObj['success'] == '1') {
				PO_display_ui_dialog('Submission Result', 'The settings were successfully reset.');
				location.reload(true);
			} else {
				var submissionAlerts = '';
				for (var i=0; i<responseObj['alerts'].length; i++) {
					if (submissionAlerts != '') {
						submissionAlerts += '<br />';
					}
					submissionAlerts += responseObj['alerts'][i];
				}
				PO_toggle_loading('#PO-meta-wrap');
				PO_display_ui_dialog('Submission Result', submissionAlerts);
			}
		});
	}

	function PO_submit_ajax(action, postVars, container, callback) {
		PO_toggle_loading(container);
		jQuery.post(encodeURI(ajaxurl + '?action='+action), postVars, function (result) {
			try {
				var responseObj = jQuery.parseJSON(result);
			} catch(err) {
				var responseObj = {'success': 0, 'alerts': ['There was an issue saving the changes you made']};
			}


			PO_toggle_loading(container);
			if (responseObj['alerts'].length > 0) {
				var submissionAlerts = '';
				for (var i=0; i<responseObj['alerts'].length; i++) {
					if (submissionAlerts != '') {
						submissionAlerts += '<br />';
					}
					submissionAlerts += responseObj['alerts'][i];
				}
				PO_display_ui_dialog('Submission Result', submissionAlerts);
			}
			
			if (typeof(callback) == 'function') {
				callback(responseObj);
			}
		});
	}
	
	<?php
	print "var regex = new Array();\n";
	foreach ($this->PO->regex as $key=>$val) {
		print "regex['$key'] = $val;\n";
	}
	?>
</script>