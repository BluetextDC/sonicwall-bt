<?php 
$adminPage = '';
if (isset($_GET['page'])) {
	$adminPage = $_GET['page'];
}

if ($adminPage == 'PO_global_plugins') {
	$availableRoles = array('_'=>'All Users');
} else {
	$availableRoles = $this->PO->get_available_roles();
}
?>

<script type="text/javascript" language="javascript">
	var PO_role_array = <?php print json_encode($availableRoles); ?>;
	PO_reverse_toggle_buttons();
</script>

<?php
if (isset($errMsg) && $errMsg != "") {
	?>
	<h3 style="color: #CC0066;"><?php print $errMsg; ?></h3>
	<?php
}
	
$ptStored = get_option('PO_pt_stored');
if (isset($post) && in_array(get_post_type($post->ID), $ptStored)) {
	?>
	<div id="PO-pt-override-msg-container">
		Settings for this post type have been overridden by the post type settings.  You can edit them by going <a href="<?php print get_admin_url(); ?>admin.php?page=PO_pt_plugins&PO_target_post_type=<?php print get_post_type($post->ID); ?>">here</a>.  You can also override them by checking the box below and saving the post.
		<br /><input type="checkbox" id="PO-activate-pt-override" name="PO_activate_pt_override" value="1" <?php print ($ptOverride == "1")? 'checked="checked"':''; ?>>Override Post Type settings
		<a href="#" onclick="PO_display_ui_dialog('Override Post Type Settings', 'By checking this box the changes you make here will not be overwritten by the settings that have been set for the <?php print get_post_type($post->ID); ?> post type.  You will be able to see the plugins disabled/enabled on this page and make changes to them.');return false;">
		  <span class="dashicons PO-dashicon dashicons-editor-help"></span>
		</a>
		<?php if ($ptOverride == 0) { ?>
			<style type="text/css">
				#PO-post-meta-box-wrapper {display:none;}
			</style>
		<?php } else { ?>
			<style type="text/css">
				#PO-pt-override-msg-container {display:none;}
			</style>
		<?php } ?>
		
	</div>
	<?php
}
?>
<div id="PO-post-meta-box-wrapper" class="PO-content-wrap">
	<?php
	do_action('PO_display_meta_compatibility', 1);
	do_action('PO_display_meta_debug', 1);
	if ($adminPage != 'PO_search_plugins' && $adminPage != 'PO_global_plugins') { ?>
		<?php if(isset($post) && get_post_type($post->ID) == 'plugin_filter') { ?>
			<div class="metaBoxLabel">
				Name
			</div>
			<div class="metaBoxContent">
				<input type="text" class="PO-filter-name-input" size="25" name="PO_filter_name" value="<?php print $filterName; ?>">
			</div>
			<div class="metaBoxLabel">
				Permalinks
				<a href="#" onclick="PO_display_ui_dialog('Permalinks', 'Click the Add Permalink button to add new permalinks to this plugin filter. All of them will have the same settings that you select on this page.<br /><br />You can use limited wildcards in the permalink structure. For instance you can match the url http://www.foo.foo/some/pretty/permalink/ by entering http://www.foo.foo/some/*/permalink/.  You can also match the url by entering http://www.foo.foo/*/pretty/permalink/ as the permalink. The only character that is recognized is the * character. It can only replace one piece of the url in between the / characters.');return false;">
					<span class="dashicons PO-dashicon dashicons-editor-help"></span>
				 </a>
			</div>
			<div class="metaBoxContent">
				<div style="text-align: center;">
					<input type="button" id="PO-add-permalink" value="Add Permalink">
				</div>
				<div id="PO-permalink-container">
					<?php if (sizeof($permalinkFilters) > 0) { ?>
						<?php foreach($permalinkFilters as $permalinkFilter) { ?>
							<div class="PO-permalink-wrapper">
								<input type="hidden" name="PO_pl_id[]" value="<?php print $permalinkFilter['pl_id']; ?>">
								<input type="text" class="PO-permalink-input" size="25" name="PO_permalink_filter_<?php print $permalinkFilter['pl_id']; ?>" value="<?php print ($permalinkFilter['permalink'] != "") ? (($permalinkFilter['secure'] == 1)? "https://":"http://") . $permalinkFilter['permalink'] : ""; ?>"><input type="button" class="PO-delete-permalink" value="X">
							</div>
						<?php } ?>
					<?php } else { ?>
						<script type="text/javascript" language="javascript">
							jQuery(function() {
								PO_add_permalink();
							});
						</script>
					<?php } ?>
				</div>
			</div>
		<?php } ?>

		<div id="settingsMetaBox" class="metaBoxContent">
			<div class="pluginListHead">Settings<?php if(isset($ajaxSaveFunction)) { ?><input type=button name=submit value="Save" onmousedown="<?php print $ajaxSaveFunction; ?>" class="PO-ajax-save-btn"><?php } ?></div>
			<?php if ($adminPage == 'PO_pt_plugins') { ?>
				<div style="padding-left: 10px;">
				Post Type: <select id="PO-selected-post-type" name="PO_selected_post_type">
				<?php
					$supportedPostTypes = get_option("PO_custom_post_type_support");
					if (!is_array($supportedPostTypes)) {
						$supportedPostTypes = array();
					}
					if (isset($_REQUEST['PO_target_post_type'])) {
						$targetPostType = $_REQUEST['PO_target_post_type'];
					} else {
						$targetPostType = '';
					}
					
					foreach($supportedPostTypes as $postType) {
						print '<option value="' . $postType . '" ' . (($targetPostType == $postType)? 'selected="selected" ':'') . '>' . $postType . '</option>';
					}
				?>
				</select>
				</div>
				<hr>
				<input type="button" class="button" style="float: left;margin: 5px;" id="resetPostTypeSettings" value="Reset settings for this post type" onclick="PO_reset_pt_settings();">
				<div style="float: left;margin: 10px 5px 0px 0px;">
					<input type="checkbox" id="PO-reset-all-pt" name="PO-reset-all-pt" value="1"><label for="PO-reset-all-pt">Reset All</label>
				</div>
				<a href="#" onclick="PO_display_ui_dialog('Reset all matching posts', 'By checking this box all posts that match the selected post type will be reset.  If the box isn\'t checked the post type setting will be reset but the individual posts will still keep the settings until they are changed individually.  You can go directly to each post matching this post type and override this setting.  Then the changes you make here will not affect that post.');return false;">
					<span class="dashicons PO-dashicon dashicons-editor-help"></span>
				 </a>
				<div style="clear: both;"></div>
			<?php } else { ?>
				<?php if (isset($post)) { ?>
					<input type="checkbox" id="affectChildren" name="affectChildren" value="1" <?php print ($affectChildren == "1")? 'checked="checked"':''; ?>>Also Affect Children
					<a href="#" onclick="PO_display_ui_dialog('Also Affect Children', 'By checking this box the plugins disabled or enabled for this page will be used for its children if they have nothing set.');return false;">
					  <span class="dashicons PO-dashicon dashicons-editor-help"></span>
					</a>
					<hr>
					<?php if(isset($post) && in_array(get_post_type($post->ID), get_option('PO_custom_post_type_support'))) { ?>
						<input type="checkbox" id="PO-pt-override" name="PO_pt_override" value="1" <?php print ($ptOverride == "1")? 'checked="checked"':''; ?>>Override Post Type settings
						<a href="#" onclick="PO_display_ui_dialog('Override Post Type Settings', 'By checking this box the changes you make here will not be overwritten by the settings that have been set for the <?php print get_post_type($post->ID); ?> post type.');return false;">
						  <span class="dashicons PO-dashicon dashicons-editor-help"></span>
						</a>
						<hr>
					<?php } ?>
					<?php if (get_post_type($post->ID) == 'plugin_filter') { ?>
						<input type="text" id="postPriority" name="PO_post_priority" value="<?php print $postPriority; ?>" maxlength="3" size="4">Priority
						<a href="#" onclick="PO_display_ui_dialog('Priority', 'This will set the priority of the post when fuzzy url matching is used.  If multiple plugin filters are found this will decide which is used.  Higher priority takes precedence.');return false;">
						  <span class="dashicons PO-dashicon dashicons-editor-help"></span>
						</a>
						<hr>
					<?php } ?>
					<input type="button" class="button" style="margin: 5px;" id="resetPostSettings" value="Reset settings for this post" onclick="PO_reset_post_settings(<?php print $post->ID; ?>);">
				<?php } ?>
			<?php } ?>
		</div>
	<?php } ?>
	<div id="pluginContainer" class="metaBoxContent">
		<div class="pluginListHead">Plugins<?php if(isset($ajaxSaveFunction)) { ?><input type=button name=submit value="Save" onmousedown="<?php print $ajaxSaveFunction; ?>" class="PO-ajax-save-btn"><?php } ?></div>
		<div id="PO-plugin-legend-wrap">
			<div id="PO-plugin-legend-header">
				Legend:
			</div>
			<div id="PO-plugin-legend-active">Active</div>
			<div id="PO-plugin-legend-group">Group</div>
			<div id="PO-plugin-legend-inactive">Inactive</div>
			<div id="PO-plugin-legend-global">Globally Disabled</div>
			<div id="PO-plugin-legend-selected">Selected</div>
			<div style="clear: both;"></div>
			<?php if (get_option("PO_disable_plugins_by_role") != '1') { ?>
				<div id="PO-plugin-legend-standard-indicator">Disabled Standard: <img src="<?php print $this->PO->urlPath; ?>/image/sm-red-dot.png" /></div>
				<?php if (get_option('PO_disable_plugins_mobile') == 1) { ?>
					<div id="PO-plugin-legend-mobile-indicator">Disabled Mobile: <img src="<?php print $this->PO->urlPath; ?>/image/sm-blue-dot.png" /></div>
				<?php } ?>
			<?php } ?>
			<div id="PO-plugin-legend-notes">Drag plugins and groups from the container to the left and drop them on the container to the right to disable them.  Drag plugins from the right and drop them on the container to the left to enable them.<br /><br />You can also select individual plugins and groups by clicking them.  They will change color when they have been selected.  Then use the "&lt;" "&gt;" buttons to add or remove them.<br /><br />Hover over group names to see which plugins are part of the group.</div>
		</div>
		<div id="PO-all-plugin-wrap" class="outerPluginWrap">
			<div class="pluginListHead">Available Items</div>
			<div class="pluginListSubHead plugins">Plugins</div>
			<?php
			$orderCount=0;
			foreach ($plugins as $key=>$plugin) {
				$pluginWrapClass = (in_array($key, $activeSitewidePlugins) || in_array($key, $activePlugins))? "activePluginWrap" : "inactivePluginWrap";
				$pluginWrapClass .= ((in_array($key, $globalPlugins) && !in_array($key, $pluginLists['_']['enabled_plugin_list'])) || in_array($key, $pluginLists['_']['disabled_plugin_list']))? ' disabledStandard' : '';
				$pluginWrapClass .= ((in_array($key, $globalMobilePlugins) && !in_array($key, $pluginLists['_']['enabled_mobile_plugin_list'])) || in_array($key, $pluginLists['_']['disabled_mobile_plugin_list']))? ' disabledMobile' : '';
				?>
				<div class="pluginWrap <?php print $pluginWrapClass; ?>">
					<?php 
					$disabledStdRoles = array();
					$disabledMobileRoles = array();
					if (is_array($availableRoles)) {
						foreach($availableRoles as $roleID=>$roleName) {
							if ((in_array($key, $globalPlugins) && !in_array($key, $pluginLists[$roleID]['enabled_plugin_list'])) || in_array($key, $pluginLists[$roleID]['disabled_plugin_list'])) {
								$disabledStdRoles[] = $roleID;
							}
							if ((in_array($key, $globalMobilePlugins) && !in_array($key, $pluginLists[$roleID]['enabled_mobile_plugin_list'])) || in_array($key, $pluginLists[$roleID]['disabled_mobile_plugin_list'])) {
								$disabledMobileRoles[] = $roleID;
							}
						}
					}
					?>
					
					<div class="pluginNameContainer">
						<input type="hidden" class="PO-plugin-id" value="<?php print $key; ?>" />
						<input type="hidden" class="PO-plugin-order" value="<?php print $orderCount; ?>" />
						<?php print $plugin['Name']; ?>
						<?php
						foreach($disabledStdRoles as $roleID) {
							print '<input type="hidden" class="disabled-roles disabled-std-roles" value="'.$roleID.'">';
						}
						foreach($disabledMobileRoles as $roleID) {
							print '<input type="hidden" class="disabled-roles disabled-mobile-roles" value="'.$roleID.'">';
						}
						?>
						<?php if (get_option("PO_disable_plugins_by_role") == '1') { ?>
							<span class="show-disabled-roles fa fa-user-circle" title=""></span>
						<?php } else { ?>
							<?php if (get_option('PO_disable_plugins_mobile') == 1) { ?>
								<div class="disabledMobileIndicator"><img src="<?php print $this->PO->urlPath; ?>/image/sm-blue-dot.png" /></div>
							<?php } ?>
							<div class="disabledStandardIndicator"><img src="<?php print $this->PO->urlPath; ?>/image/sm-red-dot.png" /></div>
						<?php } ?>
					</div>

					
					<div style="clear: both;"></div>
				</div>
				<?php
				$orderCount++;
			} ?>

			<div class="pluginListSubHead groups">Groups</div>
			<?php if (sizeOf($groupList) > 0) {
				$orderCount=0;
				foreach ($groupList as $key=>$group) {
					$groupWrapClass = ((in_array($group->ID, $globalGroups) && !in_array($group->ID, $pluginLists['_']['enabled_group_list'])) || in_array($group->ID, $pluginLists['_']['disabled_group_list']))? 'disabledStandard' : '';
					$groupWrapClass .= ((in_array($group->ID, $globalMobileGroups) && !in_array($group->ID, $pluginLists['_']['enabled_mobile_group_list'])) || in_array($group->ID, $pluginLists['_']['disabled_mobile_group_list']))? ' disabledMobile' : '';
					?>
					<div class="groupWrap <?php print $groupWrapClass; ?>">
						
						<?php 
						$disabledStdRoles = array();
						$disabledMobileRoles = array();
						if (is_array($availableRoles)) {
							foreach($availableRoles as $roleID=>$roleName) {
								if ((in_array($group->ID, $globalGroups) && !in_array($group->ID, $pluginLists[$roleID]['enabled_group_list'])) || in_array($group->ID, $pluginLists[$roleID]['disabled_group_list'])) {
									$disabledStdRoles[] = $roleID;
								}
								if ((in_array($group->ID, $globalMobileGroups) && !in_array($group->ID, $pluginLists[$roleID]['enabled_mobile_group_list'])) || in_array($group->ID, $pluginLists[$roleID]['disabled_mobile_group_list'])) {
									$disabledMobileRoles[] = $roleID;
								}
							}
						}
						?>
						<div class="pluginNameContainer">
							<input type="hidden" class="PO-group-id" value="<?php print $group->ID; ?>" />
							<input type="hidden" class="PO-group-order" value="<?php print $orderCount; ?>" />
							<?php 
							$membersTip = '__ts__Members__te__';
							$groupMembers = get_post_meta($group->ID, "_PO_group_members", $single=true);
							if (is_array($groupMembers)) {
								foreach($groupMembers as $plugin) {
									$membersTip .= '__rs__'.$plugins[$plugin]['Name'].'__re__';
								}
							}
							?>
							<span class="PO-group-members" title="<?php print $membersTip; ?>"><?php print $group->post_title; ?></span>
							
							<?php
							foreach($disabledStdRoles as $roleID) {
								print '<input type="hidden" class="disabled-roles disabled-std-roles" value="'.$roleID.'">';
							}
							foreach($disabledMobileRoles as $roleID) {
								print '<input type="hidden" class="disabled-roles disabled-mobile-roles" value="'.$roleID.'">';
							}
							?>
							<?php if (get_option("PO_disable_plugins_by_role") == '1') { ?>
								<span class="show-disabled-roles fa fa-user-circle" title=""></span>
							<?php } else { ?>
								<?php if (get_option('PO_disable_plugins_mobile') == 1) { ?>
									<div class="disabledMobileIndicator"><img src="<?php print $this->PO->urlPath; ?>/image/sm-blue-dot.png" /></div>
								<?php } ?>
								<div class="disabledStandardIndicator"><img src="<?php print $this->PO->urlPath; ?>/image/sm-red-dot.png" /></div>
							<?php } ?>
						</div>
						<div style="clear: both;"></div>
					</div>
					<?php
					$orderCount++;
				}
			} ?>
		</div>
		
		<?php foreach ($availableRoles as $roleID=>$roleName) { ?>
			<div id="PO-disabled-std-<?php print $roleID; ?>-plugin-wrap" class="PO-disabled-std-plugin-wrap outerPluginWrap disabledPlugins">
				<input type="hidden" class="user_role_name" value="<?php print $roleID; ?>">
				<div class="pluginListHead">Disabled Standard - <?php print $roleName; ?><div class="disabledListToggle fa fa-plus-square-o"></div></div>
				<div class="disabledList" style="display: none;">
					<div class="disableAllContainer">
						<div style="padding: 5px 0px 0px 0px;">
							Plugins: 
							<div class="pluginControlDivider">
								<input type="button" id="PO-remove-all-std-plugins-button" class="button button-small toggle-button" value="<<" onclick="PO_remove_all('plugin', 'std', '', '<?php print $roleID; ?>');" title="Remove All" />
								<input type="button" id="PO-remove-selected-std-plugins-button" class="button button-small toggle-button" value="<" onclick="PO_remove_all('plugin', 'std', '.selected', '<?php print $roleID; ?>');" title="Remove Selected" />
							</div>
							<div class="pluginControlDivider">
								<input type="button" id="PO-add-selected-std-plugins-button" class="button button-small toggle-button" value=">" onclick="PO_add_all('plugin', 'std', '.selected', '<?php print $roleID; ?>');" title="Add Selected" />
								<input type="button" id="PO-add-all-std-plugins-button" class="button button-small toggle-button" value=">>" onclick="PO_add_all('plugin', 'std', '', '<?php print $roleID; ?>');" title="Add All" />
							</div>
						</div>
						<div style="padding: 15px 0px 5px;">
							Groups:
							<div class="pluginControlDivider">
								<input type="button" id="PO-remove-all-std-groups-button" class="button button-small toggle-button" value="<<" onclick="PO_remove_all('group', 'std', '', '<?php print $roleID; ?>');" title="Remove All" />
								<input type="button" id="PO-remove-selected-std-groups-button" class="button button-small toggle-button" value="<" onclick="PO_remove_all('group', 'std', '.selected', '<?php print $roleID; ?>');" title="Remove Selected" />
							</div>
							<div class="pluginControlDivider">
								<input type="button" id="PO-add-selected-std-groups-button" class="button button-small toggle-button" value=">" onclick="PO_add_all('group', 'std', '.selected', '<?php print $roleID; ?>');" title="Add Selected" />
								<input type="button" id="PO-add-all-std-groups-button" class="button button-small toggle-button" value=">>" onclick="PO_add_all('group', 'std', '', '<?php print $roleID; ?>');" title="Add All" />
							</div>
						</div>
					</div>
					<div class="pluginListSubHead plugins">Plugins</div>
					<?php
					foreach ($plugins as $key=>$plugin) {
						$pluginWrapClass = (in_array($key, $activeSitewidePlugins) || in_array($key, $activePlugins))? "activePluginWrap" : "inactivePluginWrap";
						$pluginWrapClass .= (in_array($key, $globalPlugins))? " globalPluginWrap": "";
						

						if ((in_array($key, $globalPlugins) && !in_array($key, $pluginLists[$roleID]['enabled_plugin_list'])) || in_array($key, $pluginLists[$roleID]['disabled_plugin_list'])) {
							?>
							<div class="pluginWrap <?php print $pluginWrapClass; ?>">
								<div class="pluginNameContainer">
									<input type="hidden" class="PO-plugin-id" value="<?php print $key; ?>" />
									<input type="hidden" class="PO-plugin-order" value="" />
									<input type="hidden" class="PO-disabled-item-id PO-disabled-std-plugin-list" name="PO_disabled_std_plugin_list[<?php print $roleID; ?>][]" value="<?php print $key; ?>" />
									<?php print $plugin['Name']; ?>
								</div>
								<div style="clear: both;"></div>
							</div>
							<?php
						}
					}
					?>
					<div class="pluginListSubHead groups">Groups</div>
					<?php
					foreach ($groupList as $key=>$group) {
						if ((in_array($group->ID, $globalGroups) && !in_array($group->ID, $pluginLists[$roleID]['enabled_group_list'])) || in_array($group->ID, $pluginLists[$roleID]['disabled_group_list'])) {
							$groupWrapClass = (in_array($group->ID, $globalGroups))? "globalGroupWrap": "";
							?>
							<div class="groupWrap <?php print $groupWrapClass; ?>">
								<div class="pluginNameContainer">
									<input type="hidden" class="PO-group-id" value="<?php print $group->ID; ?>" />
									<input type="hidden" class="PO-group-order" value="" />
									<input type="hidden" class="PO-disabled-item-id PO-disabled-std-group-list" name="PO_disabled_std_group_list[<?php print $roleID; ?>][]" value="<?php print $group->ID; ?>" />
									<?php 
									$membersTip = '';
									$groupMembers = get_post_meta($group->ID, "_PO_group_members", $single=true);
									if (is_array($groupMembers)) {
										foreach($groupMembers as $plugin) {
											$membersTip .= '__rs__'.$plugins[$plugin]['Name'].'__re__';
										}
									}
									?>
									<span class="PO-group-members" title="<?php print $membersTip; ?>"><?php print $group->post_title; ?></span>
								</div>
								<div style="clear: both;"></div>
							</div>
							<?php
						}
					}
					?>
				</div>
			</div>
			<div style="clear: right;"></div>
		<?php } ?>

		<?php if (get_option('PO_disable_plugins_mobile') == 1) { ?>
			<?php foreach ($availableRoles as $roleID=>$roleName) { ?>
				<div id="PO-disabled-mobile-<?php print $roleID; ?>-plugin-wrap" class="PO-disabled-mobile-plugin-wrap outerPluginWrap disabledPlugins">
					<input type="hidden" class="user_role_name" value="<?php print $roleID; ?>">
					<div class="pluginListHead">Disabled Mobile - <?php print $roleName; ?><div class="disabledListToggle fa fa-plus-square-o"></div></div>
					<div class="disabledList" style="display: none;">
						<div class="disableAllContainer">
							<div style="padding: 5px 0px 0px 0px;">
								Plugins:
								<div class="pluginControlDivider">
									<input type="button" id="PO-remove-all-mobile-plugins-button" class="button button-small toggle-button" value="<<" onclick="PO_remove_all('plugin', 'mobile', '', '<?php print $roleID; ?>');" title="Remove All" />
									<input type="button" id="PO-remove-all-mobile-plugins-button" class="button button-small toggle-button" value="<" onclick="PO_remove_all('plugin', 'mobile', '.selected', '<?php print $roleID; ?>');" title="Remove Selected" />
								</div>
								<div class="pluginControlDivider">
									<input type="button" id="PO-add-all-mobile-plugins-button" class="button button-small toggle-button" value=">" onclick="PO_add_all('plugin', 'mobile', '.selected', '<?php print $roleID; ?>');" title="Add Selected" />
									<input type="button" id="PO-add-all-mobile-plugins-button" class="button button-small toggle-button" value=">>" onclick="PO_add_all('plugin', 'mobile', '', '<?php print $roleID; ?>');" title="Add All" />
								</div>
							</div>
							<div style="padding: 5px 0px;">
								Groups:
								<div class="pluginControlDivider">
									<input type="button" id="PO-remove-all-mobile-groups-button" class="button button-small toggle-button" value="<<" onclick="PO_remove_all('group', 'mobile', '', '<?php print $roleID; ?>');" title="Remove All" />
									<input type="button" id="PO-remove-all-mobile-groups-button" class="button button-small toggle-button" value="<" onclick="PO_remove_all('group', 'mobile', '.selected', '<?php print $roleID; ?>');" title="Remove Selected" />
								</div>
								<div class="pluginControlDivider">
									<input type="button" id="PO-add-all-mobile-groups-button" class="button button-small toggle-button" value=">" onclick="PO_add_all('group', 'mobile', '.selected', '<?php print $roleID; ?>');" title="Add Selected" />
									<input type="button" id="PO-add-all-mobile-groups-button" class="button button-small toggle-button" value=">>" onclick="PO_add_all('group', 'mobile', '', '<?php print $roleID; ?>');" title="Add All" />
								</div>
							</div>
						</div>
						<div class="pluginListSubHead plugins">Plugins</div>
						<?php
						foreach ($plugins as $key=>$plugin) {
							$pluginWrapClass = (in_array($key, $activeSitewidePlugins) || in_array($key, $activePlugins))? "activePluginWrap" : "inactivePluginWrap";
							$pluginWrapClass .= (in_array($key, $globalMobilePlugins))? " globalPluginWrap": "";
							
							if ((in_array($key, $globalMobilePlugins) && !in_array($key, $pluginLists[$roleID]['enabled_mobile_plugin_list'])) || in_array($key, $pluginLists[$roleID]['disabled_mobile_plugin_list'])) {
								?>
								<div class="pluginWrap <?php print $pluginWrapClass; ?>">
									<div class="pluginNameContainer">
										<input type="hidden" class="PO-plugin-id" value="<?php print $key; ?>" />
										<input type="hidden" class="PO-plugin-order" value="" />
										<input type="hidden" class="PO-disabled-item-id PO-disabled-mobile-plugin-list" name="PO_disabled_mobile_plugin_list[<?php print $roleID; ?>][]" value="<?php print $key; ?>" />
										<?php print $plugin['Name']; ?>
									</div>
									<div style="clear: both;"></div>
								</div>
								<?php
							}
						}
						?>

						
						<div class="pluginListSubHead groups">Groups</div>

						<?php
						foreach ($groupList as $key=>$group) {
							if ((in_array($group->ID, $globalMobileGroups) && !in_array($group->ID, $pluginLists[$roleID]['enabled_mobile_group_list'])) || in_array($group->ID, $pluginLists[$roleID]['disabled_mobile_group_list'])) {
								$groupWrapClass = (in_array($group->ID, $globalGroups))? "globalGroupWrap": "";
								?>
								<div class="groupWrap <?php print $groupWrapClass; ?>">
									<div class="pluginNameContainer">
										<input type="hidden" class="PO-group-id" value="<?php print $group->ID; ?>" />
										<input type="hidden" class="PO-group-order" value="" />
										<input type="hidden" class="PO-disabled-item-id PO-disabled-mobile-group-list" name="PO_disabled_mobile_group_list[<?php print $roleID; ?>][]" value="<?php print $group->ID; ?>" />
										<?php 
										$membersTip = '';
										$groupMembers = get_post_meta($group->ID, "_PO_group_members", $single=true);
										if (is_array($groupMembers)) {
											foreach($groupMembers as $plugin) {
												$membersTip .= '__rs__'.$plugins[$plugin]['Name'].'__re__';
											}
										}
										?>
										<span class="PO-group-members" title="<?php print $membersTip; ?>"><?php print $group->post_title; ?></span>
									</div>
									<div style="clear: both;"></div>
								</div>
								<?php
							}
						}
						?>
					</div>
				</div>
				<div style="clear: right;"></div>
			<?php } ?>
		<?php } ?>
		<div style="clear: both;"></div>
	</div>
	<div style="clear: both;"></div>
</div>
<div style="clear: both;"></div>
<input type="hidden" name="poSubmitPostMetaBox" value="1" />