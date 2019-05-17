<div id="wrap" class="PO-content-wrap">
    <div class="po-setting-icon fa fa-cogs" id="icon-po-settings"> <br /> </div>

    <h2 class="po-setting-title">Settings</h2>
    <div style="clear: both;"></div>
	
	<div id="PO-tab-container">
		<div id="PO-tab-menu-container">
			<ul id="PO-tab-menu">
				<li id="PO-tab-1" class="ui-tabs-active ui-state-active"><a href="#PO-tab-1-content">General Settings</a></li>
				<li id="PO-tab-2"><a href="#PO-tab-2-content">Admin CSS</a></li>
				<li id="PO-tab-3"><a href="#PO-tab-3-content">Recreate Permalinks</a></li>
				<li id="PO-tab-4"><a href="#PO-tab-4-content">Mobile User Agents</a></li>
				<li id="PO-tab-5"><a href="#PO-tab-5-content">Manage MU plugin file</a></li>
				<li id="PO-tab-6"><a href="#PO-tab-6-content">Plugin Search</a></li>
			</ul>
			<div id="PO-tab-1-content" class="PO-tab-content" style="display: block;">

				<div id="PO-gen-settings-div">
					<div class="PO-loading-container fa fa-spinner fa-pulse"></div>
					<div class="inside">
						<div class="stuffbox">
							<?php $fuzzyUrlMatching = get_option("PO_fuzzy_url_matching"); ?>
							<div class="PO-settings-button-container">
								<input type="checkbox" id="PO-fuzzy-url-matching" name="PO_fuzzy_url_matching" class="hidden-checkbox" <?php print ($fuzzyUrlMatching === "1")? 'checked="checked"':""; ?>>
								<input type="button" id="PO-fuzzy-url-matching-button" class="toggle-button-<?php print ($fuzzyUrlMatching === "1")? "on":"off"; ?>" value="<?php print ($fuzzyUrlMatching === "1")? "On":"Off"; ?>"  onclick="PO_toggle_button('PO-fuzzy-url-matching', '', 0);" />
							</div>
							<h4>
							  <label for="PO_fuzzy_url_matching">Fuzzy URL matching</label>
							  <span class="PO-help-dialog fa fa-question-circle" title="__ts__Fuzzy URL matching__te____rs__This gives any URL the ability to affect children of that URL.  This is not the same as wordpress children.  It is using the URL structure to determine children.  So <?php print home_url($path='/'); ?>page/ will affect <?php print home_url($path='/'); ?>page/child/ and <?php print home_url($path='/'); ?>page/child2/.__re__"></span>
							</h4>
						</div>

							
							
						<div class="stuffbox">
							<?php $ignoreProtocol = get_option("PO_ignore_protocol"); ?>
							<div class="PO-settings-button-container">
								<input type="checkbox" id="PO-ignore-protocol" name="PO_ignore_protocol" class="hidden-checkbox" <?php print ($ignoreProtocol === "1")? 'checked="checked"':""; ?>>
								<input type="button" id="PO-ignore-protocol-button" class="toggle-button-<?php print ($ignoreProtocol === "1")? "on":"off"; ?>" value="<?php print ($ignoreProtocol === "1")? "On":"Off"; ?>"  onclick="PO_toggle_button('PO-ignore-protocol', '', 0);" />
							</div>
							<h4>
							  <label for="PO_ignore_protocol">Ignore URL Protocol</label>
							  <span class="PO-help-dialog fa fa-question-circle" title="__ts__Ignore URL Protocol__te____rs__This allows you to ignore the protocol (http, https) of a URL when trying to match it in the database at page load time.  With this turned on <?php print home_url($path='/', $scheme='https'); ?>page/ will have the same plugins loaded as <?php print home_url($path='/'); ?>page/.  If it is turned off they can be set seperately using plugin filters.__re__"></span>
							</h4>
						</div>


							
						<div class="stuffbox">
							<?php $ignoreArguments = get_option("PO_ignore_arguments"); ?>
							<div class="PO-settings-button-container">
								<input type="checkbox" id="PO-ignore-arguments" name="PO_ignore_arguments" class="hidden-checkbox" <?php print ($ignoreArguments === "1")? 'checked="checked"':""; ?>>
								<input type="button" id="PO-ignore-arguments-button" class="toggle-button-<?php print ($ignoreArguments === "1")? "on":"off"; ?>" value="<?php print ($ignoreArguments === "1")? "On":"Off"; ?>"  onclick="PO_toggle_button('PO-ignore-arguments', '', 0);" />
							</div>
							<h4>
							  <label for="PO_ignore_arguments">Ignore URL Arguments</label>
							  <span class="PO-help-dialog fa fa-question-circle" title="__ts__Ignore URL Arguments__te____rs__This allows you to ignore the arguments of a URL when trying to match it in the database at page load time.  With this turned on <?php print home_url($path='/'); ?>page/?foo=2&bar=3 will have the same plugins loaded as <?php print home_url($path='/'); ?>page/.  If it is turned off you can enter URLs with arguments included to load different plugins depending on what arguments are used.__re__"></span>
							</h4>
						</div>


						<div class="stuffbox">
							<?php $orderAccessNetAdmin = get_option("PO_order_access_net_admin"); ?>
							<div class="PO-settings-button-container">
								<input type="checkbox" id="PO-order-access-net-admin" title="Network Admin Access" name="PO_order_access_net_admin" class="hidden-checkbox" value="1" <?php print ($orderAccessNetAdmin === "1")? 'checked="checked"':""; ?>>
								<input type="button" id="PO-order-access-net-admin-button" class="toggle-button-<?php print ($orderAccessNetAdmin === "1")? "yes":"no"; ?>" value="<?php print ($orderAccessNetAdmin === "1")? "Yes":"No"; ?>"  onclick="PO_toggle_button('PO-order-access-net-admin', '', 1);" />
							</div>
							<h4>
							  <label for="PO_order_access_net_admin">Only allow network admins to change plugin load order?</label>
							  <span class="PO-help-dialog fa fa-question-circle" title="__ts__Only allow network admins to change plugin load order?__te____rs__When this option is turned on only a network admin will be able to reorder plugins.  All other user types will not see the options to change the load order.__re__"></span>
							</h4>
						</div>


						<div class="stuffbox">
							<?php $autoTrailingSlash = get_option("PO_auto_trailing_slash"); ?>
							<div class="PO-settings-button-container">
								<input type="checkbox" id="PO-auto-trailing-slash" name="PO_auto_trailing_slash" class="hidden-checkbox" <?php print ($autoTrailingSlash === "0")? '':'checked="checked"'; ?>>
								<input type="button" id="PO-auto-trailing-slash-button" class="toggle-button-<?php print ($autoTrailingSlash === "0")? "off":"on"; ?>" value="<?php print ($autoTrailingSlash === "0")? "Off":"On"; ?>"  onclick="PO_toggle_button('PO-auto-trailing-slash', '', 0);" />
							</div>
							<h4>
							  <label for="PO_auto_trailing_slash">Auto Trailing Slash</label>
							  <span class="PO-help-dialog fa fa-question-circle" title="__ts__Auto Trailing Slash__te____rs__When this option is turned on Plugin Organizer will either remove or add a trailing slash to your plugin filter permalinks based on your permalink structure.  If you are having issues with your plugin filters not matching you can disable it by turning this off.__re__"></span>
							</h4>
						</div>
						
						
						<div class="stuffbox">
							<?php $selectiveLoad = get_option("PO_disable_plugins_frontend"); ?>
							<div class="PO-settings-button-container">
								<input type="checkbox" id="PO-disable-plugins-frontend" name="PO_disable_plugins_frontend" class="hidden-checkbox" <?php print ($selectiveLoad === "1")? 'checked="checked"':""; ?>>
								<input type="button" id="PO-disable-plugins-frontend-button" class="toggle-button-<?php print ($selectiveLoad === "1")? "on":"off"; ?>" value="<?php print ($selectiveLoad === "1")? "On":"Off"; ?>"  onclick="PO_toggle_button('PO-disable-plugins-frontend', '', 0);" />
							</div>
							<h4>
							  <label for="PO_disable_plugins_frontend">Selective Plugin Loading</label>
							  <span class="PO-help-dialog fa fa-question-circle" title="__ts__Selective Plugin Loading__te____rs__When this option is turned on you must copy the PluginOrganizerMU.class.php file from /wp-content/plugins/plugin_organizer/lib and place it in <?php print WPMU_PLUGIN_DIR; ?> before it will work.  If you don\'t have an mu-plugins folder you need to create it.__re__"></span>
							</h4>
						</div>
							
						<div class="stuffbox">
							<?php $selectiveMobileLoad = get_option("PO_disable_plugins_mobile"); ?>
							<div class="PO-settings-button-container">
								<input type="checkbox" id="PO-disable-plugins-mobile" name="PO_disable_plugins_mobile" class="hidden-checkbox" <?php print ($selectiveMobileLoad === "1")? 'checked="checked"':""; ?>>
								<input type="button" id="PO-disable-plugins-mobile-button" class="toggle-button-<?php print ($selectiveMobileLoad === "1")? "on":"off"; ?>" value="<?php print ($selectiveMobileLoad === "1")? "On":"Off"; ?>"  onclick="PO_toggle_button('PO-disable-plugins-mobile', '', 0);" />
							</div>
							<h4>
							  <label for="PO_disable_plugins_mobile">Selective Mobile Plugin Loading</label>
							  <span class="PO-help-dialog fa fa-question-circle" title="__ts__Selective Mobile Plugin Loading__te____rs__When this option is turned on plugins will be disabled differently for mobile browsers. Selective Plugin Loading must be turned on before this one will be applied.__re__"></span>
							</h4>
						</div>
							
						<div class="stuffbox">
							<?php $selectiveAdminLoad = get_option("PO_disable_plugins_admin"); ?>
							<div class="PO-settings-button-container">
								<input type="checkbox" id="PO-disable-plugins-admin" name="PO_disable_plugins_admin" class="hidden-checkbox" <?php print ($selectiveAdminLoad === "1")? 'checked="checked"':""; ?>>
								<input type="button" id="PO-disable-plugins-admin-button" class="toggle-button-<?php print ($selectiveAdminLoad === "1")? "on":"off"; ?>" value="<?php print ($selectiveAdminLoad === "1")? "On":"Off"; ?>"  onclick="PO_toggle_button('PO-disable-plugins-admin', '', 0);" />
							</div>
							<h4>
							  <label for="PO_disable_plugins_admin">Selective Admin Plugin Loading</label>
							  <span class="PO-help-dialog fa fa-question-circle" title="__ts__Selective Admin Plugin Loading__te____rs__When this option is turned on plugin filters will also apply to the admin area. Selective Plugin Loading must be turned on before this one will be applied.__re__"></span>
							</h4>
						</div>

						<div class="stuffbox">
							<?php $disableByRole = get_option("PO_disable_plugins_by_role"); ?>
							<div class="PO-settings-button-container">
								<input type="checkbox" id="PO-disable-plugins-by-role" name="PO_disable_plugins_by_role" class="hidden-checkbox" <?php print ($disableByRole === "1")? 'checked="checked"':""; ?>>
								<input type="button" id="PO-disable-plugins-by-role-button" class="toggle-button-<?php print ($disableByRole === "1")? "on":"off"; ?>" value="<?php print ($disableByRole === "1")? "On":"Off"; ?>"  onclick="PO_toggle_button('PO-disable-plugins-by-role', '', 0);" />
							</div>
							<h4>
							  <label for="PO_disable_plugins_by_role">Disable Plugins By Role</label>
							  <span class="PO-help-dialog fa fa-question-circle" title="__ts__Disable Plugins By Role__te____rs__This gives you the ability to disable/enable plugins differently based on the users role and if they are logged in.__re__"></span>
							</h4>
						</div>

						<div class="stuffbox">
							<?php $displayDebugMsg = get_option("PO_display_debug_msg"); ?>
							<div class="PO-settings-button-container">
								<input type="checkbox" id="PO-display-debug-msg" name="PO_display_debug_msg" class="hidden-checkbox" <?php print ($displayDebugMsg === "1")? 'checked="checked"':""; ?>>
								<input type="button" id="PO-display-debug-msg-button" class="toggle-button-<?php print ($displayDebugMsg === "1")? "on":"off"; ?>" value="<?php print ($displayDebugMsg === "1")? "On":"Off"; ?>"  onclick="PO_toggle_button('PO-display-debug-msg', '', 0);" />
							</div>
							<h4>
							  <label for="PO_display_debug_msg">Display Debug Messages</label>
							  <span class="PO-help-dialog fa fa-question-circle" title="__ts__Display Debug Messages__te____rs__Turning this option on will display debug messages about how plugins are being disabled on the current page you are viewing.__re____rs__The messages will either be displayed in an admin notice or at the bottom of the page.__re____rs__Only users with the roles you have selected will see these messages.__re__"></span>
							</h4>
						</div>


						<div id="PO-debug-role-container" class="stuffbox scrolling-container">
							<h4>
							  <label for="PO_debug_roles">Debugging Roles</label>
							  <span class="PO-help-dialog fa fa-question-circle" title="__ts__Debugging Roles__te____rs__This is a list of roles on your wordpress install.  Select the checkbox next to the role you would like to allow debug messages for.  Only the roles you select here will be able to see debug messages.  Use of this feature requires that Display Debug Messages be turned on.__re__"></span>
							</h4><br />
							<?php
							$debugRoles = get_option("PO_debug_roles");
							if (!is_array($debugRoles)) {
								$debugRoles = array();
							}
							?>
							
							<div class="PO-debug-roles-row"><input type="checkbox" class="PO-debug-roles" name="PO_debug_roles[]" value="_" <?php print (in_array('_', $debugRoles))? 'checked="checked"':''; ?> />Not Logged In</div>
							<div class="PO-debug-roles-row"><input type="checkbox" class="PO-debug-roles" name="PO_debug_roles[]" value="-" <?php print (in_array('-', $debugRoles))? 'checked="checked"':''; ?> />Default Logged In</div>
							<div class="PO-debug-roles-container">
								<?php
								$availableRoles = get_editable_roles();
								if (is_array($availableRoles)) {
									foreach($debugRoles as $roleID) {
										if (array_key_exists($roleID, $availableRoles)) {
											print '<div class="PO-debug-roles-row"><input type="checkbox" class="PO-debug-roles" name="PO_debug_roles[]" value="'.$roleID.'" checked="checked" />'.$availableRoles[$roleID]['name'].'</div>';
										}
									}
									
									foreach ($availableRoles as $roleID=>$roleDetails) {
										if (!in_array($roleID, $debugRoles)) {
											print '<div class="PO-debug-roles-row"><input type="checkbox" class="PO-debug-roles" name="PO_debug_roles[]" value="'.$roleID.'" />'.$roleDetails['name'].'</div>';
										}
									}
								}
								?>
							</div>
						</div>
						
						
						<div id="PO-role-container" class="stuffbox scrolling-container">
							<h4>
							  <label for="PO_enabled_roles">Role Support</label>
							  <span class="PO-help-dialog fa fa-question-circle" title="__ts__Role Support__te____rs__This is a list of roles on your wordpress install.  Select the checkbox next to the role you would like to disable/enable plugins for.  Only the roles you select here will be available on the post edit screen.  You can drag and drop the different roles to set their priority.  The first one in the list has the highest priority.  If you don\'t select any roles you will still be able to disable/enable plugins based on whether a person is logged in or not.  Use of this feature requires that Disable Plugins By Role be turned on.__re__"></span>
							</h4>
							<div class="PO-available-roles-container">
								<?php
								$supportedRoles = get_option("PO_enabled_roles");
								if (!is_array($supportedRoles)) {
									$supportedRoles = array();
								}
								
								$availableRoles = get_editable_roles();
								if (is_array($availableRoles)) {
									foreach($supportedRoles as $roleID) {
										print '<div class="PO-available-roles-row"><input type="checkbox" class="PO-available-roles" name="PO_available_roles[]" value="'.$roleID.'" checked="checked" />'.$availableRoles[$roleID]['name'].'</div>';
									}
									
									foreach ($availableRoles as $roleID=>$roleDetails) {
										if (!in_array($roleID, $supportedRoles)) {
											print '<div class="PO-available-roles-row"><input type="checkbox" class="PO-available-roles" name="PO_available_roles[]" value="'.$roleID.'" />'.$roleDetails['name'].'</div>';
										}
									}
								}
								?>
							</div>
						</div>


						<div id="PO-custom-post-type-container" class="stuffbox scrolling-container">
							<h4>
							  <label for="PO_cutom_post_type">Custom Post Type Support</label>
							  <span class="PO-help-dialog fa fa-question-circle" title="__ts__Custom Post Type Support__te____rs__This is a list of registered post types on your wordpress install.  Select the checkbox next to the post types you would like to disable/enable plugins on.  If a post type is not selected then the list of plugins will not appear on the post edit screen.  You can drag and drop the different post types to set their priority.  The first one in the list has the highest priority.__re__"></span>
							</h4>
							<div class="PO-post-type-container">
								<?php
								$supportedPostTypes = get_option("PO_custom_post_type_support");
								if (!is_array($supportedPostTypes)) {
									$supportedPostTypes = array();
								}
								
								$customPostTypes = get_post_types();
								if (is_array($customPostTypes)) {
									foreach($supportedPostTypes as $postType) {
										if (in_array($postType, $customPostTypes)) {
											print '<div class="PO-post-type-row"><input type="checkbox" class="PO-cutom-post-type" name="PO_cutom_post_type[]" value="'.$postType.'" checked="checked" />'.$postType.'</div>';
										}
									}
									
									$notAllowedTypes = array("attachment", "revision", "nav_menu_item", "plugin_group", "plugin_filter");
									$notAllowedTypes = array_merge($notAllowedTypes, $supportedPostTypes);
									foreach ($customPostTypes as $postType) {
										if (!in_array($postType, $notAllowedTypes)) {
											print '<div class="PO-post-type-row"><input type="checkbox" class="PO-cutom-post-type" name="PO_cutom_post_type[]" value="'.$postType.'" '.((in_array($postType, $supportedPostTypes))? 'checked="checked"' : '').' />'.$postType.'</div>';
										}
									}
								}
								?>
							</div>
						</div>

						<div style="clear: both;"></div>



						<input type="button" name="submit-gen-settings" value="Save Settings" onmousedown="PO_submit_gen_settings();" class="button button-primary">
					</div>
				</div>

				
			</div>
			
			<?php
			$POAdminStyles = get_option('PO_admin_styles');
			if (!is_array($POAdminStyles)) {
				$POAdminStyles = array();
			}
			?>

			<div id="PO-tab-2-content" class="PO-tab-content">
				<div id="PO-manage-css-div" style="width: 98%">
				  <div class="PO-loading-container fa fa-spinner fa-pulse"></div>
				  <div class="inside">
					<h4 class="PO-settings-section-title first">Plugin Lists</h4>
					<div class="PO-settings-left-column">
					  Network plugins:
					</div>
					<div class="PO-settings-right-column">
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-network-plugins-bg-color" name="PO_network_plugins_bg_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['network_plugins_bg_color']) && $POAdminStyles['network_plugins_bg_color'] != '')? $POAdminStyles['network_plugins_bg_color'] : '#D7DF9E'; ?>" />  Background<br />
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-network-plugins-font-color" name="PO_network_plugins_font_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['network_plugins_font_color']) && $POAdminStyles['network_plugins_font_color'] != '')? $POAdminStyles['network_plugins_font_color'] : '#444'; ?>" />  Font
					  </div>
					</div>
					<div style="clear: both;"></div>
					<hr>
					<div class="PO-settings-left-column">
					  Active plugins:
					</div>
					<div class="PO-settings-right-column">
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-active-plugins-bg-color" name="PO_active_plugins_bg_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['active_plugins_bg_color']) && $POAdminStyles['active_plugins_bg_color'] != '')? $POAdminStyles['active_plugins_bg_color'] : '#fff'; ?>" />  Background<br />
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-active-plugins-font-color" name="PO_active_plugins_font_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['active_plugins_font_color']) && $POAdminStyles['active_plugins_font_color'] != '')? $POAdminStyles['active_plugins_font_color'] : '#444'; ?>" />  Font
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-active-plugins-border-color" name="PO_active_plugins_border_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['active_plugins_border_color']) && $POAdminStyles['active_plugins_border_color'] != '')? $POAdminStyles['active_plugins_border_color'] : '#ccc'; ?>" />  Border
					  </div>
					</div>
					<div style="clear: both;"></div>
					<hr>
					<div class="PO-settings-left-column">
					  Inactive plugins:
					</div>
					<div class="PO-settings-right-column">
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-inactive-plugins-bg-color" name="PO_inactive_plugins_bg_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['inactive_plugins_bg_color']) && $POAdminStyles['inactive_plugins_bg_color'] != '')? $POAdminStyles['inactive_plugins_bg_color'] : '#ddd'; ?>" />  Background<br />
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-inactive-plugins-font-color" name="PO_inactive_plugins_font_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['inactive_plugins_font_color']) && $POAdminStyles['inactive_plugins_font_color'] != '')? $POAdminStyles['inactive_plugins_font_color'] : '#444'; ?>" />  Font
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-inactive-plugins-border-color" name="PO_inactive_plugins_border_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['inactive_plugins_border_color']) && $POAdminStyles['inactive_plugins_border_color'] != '')? $POAdminStyles['inactive_plugins_border_color'] : '#fff'; ?>" />  Border
					  </div>
					</div>
					<div style="clear: both;"></div>
					<hr>
					<div class="PO-settings-left-column">
					  Global plugins:
					</div>
					<div class="PO-settings-right-column">
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-global-plugins-bg-color" name="PO_global_plugins_bg_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['global_plugins_bg_color']) && $POAdminStyles['global_plugins_bg_color'] != '')? $POAdminStyles['global_plugins_bg_color'] : '#660011'; ?>" />  Background<br />
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-global-plugins-font-color" name="PO_global_plugins_font_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['global_plugins_font_color']) && $POAdminStyles['global_plugins_font_color'] != '')? $POAdminStyles['global_plugins_font_color'] : '#fff'; ?>" />  Font
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-global-plugins-border-color" name="PO_global_plugins_border_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['global_plugins_border_color']) && $POAdminStyles['global_plugins_border_color'] != '')? $POAdminStyles['global_plugins_border_color'] : '#ccc'; ?>" />  Border
					  </div>
					</div>
					<div style="clear: both;"></div>
					<hr>
					<div class="PO-settings-left-column">
					  Selected plugins:
					</div>
					<div class="PO-settings-right-column">
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-selected-plugins-bg-color" name="PO_selected_plugins_bg_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['selected_plugins_bg_color']) && $POAdminStyles['selected_plugins_bg_color'] != '')? $POAdminStyles['selected_plugins_bg_color'] : '#cccc66'; ?>" />  Background<br />
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-selected-plugins-font-color" name="PO_selected_plugins_font_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['selected_plugins_font_color']) && $POAdminStyles['selected_plugins_font_color'] != '')? $POAdminStyles['selected_plugins_font_color'] : '#444'; ?>" />  Font
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-selected-plugins-border-color" name="PO_selected_plugins_border_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['selected_plugins_border_color']) && $POAdminStyles['selected_plugins_border_color'] != '')? $POAdminStyles['selected_plugins_border_color'] : '#ccc'; ?>" />  Border
					  </div>
					</div>
					<div style="clear: both;"></div>
					<hr>
					<div class="PO-settings-left-column">
					  Plugin Groups:
					</div>
					<div class="PO-settings-right-column">
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-plugin-groups-bg-color" name="PO_plugin_groups_bg_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['plugin_groups_bg_color']) && $POAdminStyles['plugin_groups_bg_color'] != '')? $POAdminStyles['plugin_groups_bg_color'] : '#fff'; ?>" />  Background<br />
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-plugin-groups-font-color" name="PO_plugin_groups_font_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['plugin_groups_font_color']) && $POAdminStyles['plugin_groups_font_color'] != '')? $POAdminStyles['plugin_groups_font_color'] : '#444'; ?>" />  Font
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-plugin-groups-border-color" name="PO_plugin_groups_border_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['plugin_groups_border_color']) && $POAdminStyles['plugin_groups_border_color'] != '')? $POAdminStyles['plugin_groups_border_color'] : '#ccc'; ?>" />  Border
					  </div>
					</div>
					<div style="clear: both;"></div>
					<hr>




					<h4 class="PO-settings-section-title">Buttons</h4>
					<div class="PO-settings-left-column">
					  On Button:
					</div>
					<div class="PO-settings-right-column">
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-on-btn-bg-color" name="PO_on_btn_bg_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['on_btn_bg_color']) && $POAdminStyles['on_btn_bg_color'] != '')? $POAdminStyles['on_btn_bg_color'] : '#336600'; ?>" />  Background<br />
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-on-btn-font-color" name="PO_on_btn_font_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['on_btn_font_color']) && $POAdminStyles['on_btn_font_color'] != '')? $POAdminStyles['on_btn_font_color'] : '#ffffff'; ?>" />  Font
					  </div>
					</div>
					<div style="clear: both;"></div>
					<hr>
					<div class="PO-settings-left-column">
					  Off Button:
					</div>
					<div class="PO-settings-right-column">
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-off-btn-bg-color" name="PO_off_btn_bg_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['off_btn_bg_color']) && $POAdminStyles['off_btn_bg_color'] != '')? $POAdminStyles['off_btn_bg_color'] : '#990000'; ?>" />  Background<br />
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-off-btn-font-color" name="PO_off_btn_font_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['off_btn_font_color']) && $POAdminStyles['off_btn_font_color'] != '')? $POAdminStyles['off_btn_font_color'] : '#ffffff'; ?>" />  Font
					  </div>
					</div>
					<div style="clear: both;"></div>
					<hr>

					<div class="PO-settings-left-column">
					  Yes Button:
					</div>
					<div class="PO-settings-right-column">
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-yes-btn-bg-color" name="PO_yes_btn_bg_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['yes_btn_bg_color']) && $POAdminStyles['yes_btn_bg_color'] != '')? $POAdminStyles['yes_btn_bg_color'] : '#336600'; ?>" />  Background<br />
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-yes-btn-font-color" name="PO_yes_btn_font_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['yes_btn_font_color']) && $POAdminStyles['yes_btn_font_color'] != '')? $POAdminStyles['yes_btn_font_color'] : '#ffffff'; ?>" />  Font
					  </div>
					</div>
					<div style="clear: both;"></div>
					<hr>

					<div class="PO-settings-left-column">
					  No Button:
					</div>
					<div class="PO-settings-right-column">
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-no-btn-bg-color" name="PO_no_btn_bg_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['no_btn_bg_color']) && $POAdminStyles['no_btn_bg_color'] != '')? $POAdminStyles['no_btn_bg_color'] : '#990000'; ?>" />  Background<br />
					  </div>
					  <div class="PO-color-wrapper">
					    <div class="PO-color-preview"></div>
					    <input type="text" id="PO-no-btn-font-color" name="PO_no_btn_font_color" class="PO-colorpicker" value="<?php print (isset($POAdminStyles['no_btn_font_color']) && $POAdminStyles['no_btn_font_color'] != '')? $POAdminStyles['no_btn_font_color'] : '#ffffff'; ?>" />  Font
					  </div>
					</div>
					<div style="clear: both;"></div>
					<hr>

					<h4 class="PO-settings-section-title">Debug Container CSS</h4>
					<div class="PO-settings-left-column">
					  Frontend:
					</div>
					<div class="PO-settings-right-column">
					  <input type="text" id="PO-front-debug-style" value="<?php print ($POAdminStyles['front_debug_style'] != '')? $POAdminStyles['front_debug_style'] : 'position: relative;z-index: 99999;background: #fff;width: 100%;border: 4px solid #000;padding: 10px;'; ?>" />
					</div>
					<div style="clear: both;"></div>
					<hr>
					<div class="PO-settings-left-column">
					  Backend:
					</div>
					<div class="PO-settings-right-column">
					  <input type="text" id="PO-admin-debug-style" value="<?php print ($POAdminStyles['admin_debug_style'] != '')? $POAdminStyles['admin_debug_style'] : 'padding: 20px;'; ?>" />
					</div>
					<div style="clear: both;"></div>
					<hr>
					<input type=button name="submit_admin_css_settings" value="Submit" onmousedown="PO_submit_admin_css_settings();" class="button button-primary">
				  </div>
				</div>
			</div>
			<div id="PO-tab-3-content" class="PO-tab-content">
				<div id="PO-redo-permalinks-div" style="width: 98%">
				  <div class="PO-loading-container fa fa-spinner fa-pulse"></div>
				  <div class="inside">
					Old site address (optional): <input type="text" name="PO_old_site_address" id="PO-old-site-address" /><br />
					New site address (optional): <input type="text" name="PO_new_site_address" id="PO-new-site-address" value="<?php print preg_replace('/^.{1,5}:\/\//', '', get_site_url()); ?>" /><br />
					<br />
					If you are changing your site address you can enter your new and old addresses to update your plugin filters.  If you don't enter the new and old site addresses your plugin filters will not be updated.  All other post types will be updated by getting the new permalink from wordpress.<br />
					WARNING:  This does a regular expression search on your permalinks for the string you enter in the old address box and replaces it with the string you put in the new addres box so be careful what you enter.  This can't be undone.<br />
					<input type="button" name="redo-permalinks" value="Recreate Permalinks" onmousedown="PO_submit_redo_permalinks();" class="button button-primary">
				  </div>
				</div>
			</div>

			<div id="PO-tab-4-content" class="PO-tab-content">
				<div id="PO-browser-string-div" style="width: 98%">
				  <div class="PO-loading-container fa fa-spinner fa-pulse"></div>
				  <div class="inside">
					<div class="PO-help-container">
				      <span class="PO-help-dialog fa fa-question-circle" title="__ts__Mobile User Agents__te____rs__This is the list of strings that will be used to determine if a visitor is using a mobile browser.  If the browser string they send contains one of these words then the mobile set of plugins will be loaded.__re__"></span>
				    </div>
					<textarea name="PO_mobile_user_agents" id="PO-mobile-user-agents" rows="20" cols="50" style="width: 100%;"><?php
						$userAgents = get_option("PO_mobile_user_agents");
						if (is_array($userAgents)) {
							foreach ($userAgents as $key=>$agent) {
								if ($key > 0) {
									print "\n";
								}
								print $agent;
							}
						}
					?></textarea>
					<br />
					<input type="button" name="save-user-agents" value="Save User Agents" onmousedown="PO_submit_mobile_user_agents();" class="button button-primary">
				  </div>
				</div>
			</div>

			<div id="PO-tab-5-content" class="PO-tab-content">
				<div id="PO-manage-mu-div" style="width: 98%">
				  <div class="PO-loading-container fa fa-spinner fa-pulse"></div>
				  <div class="inside">
					<input type=button name="manage-mu-plugin" value="Delete" onmousedown="PO_manage_mu_plugin_file('delete');" class="button button-primary">
					<input type=button name="manage-mu-plugin" value="Copy" onmousedown="PO_manage_mu_plugin_file('move');" class="button button-primary">
				  </div>
				</div>
			</div>

			<div id="PO-tab-6-content" class="PO-tab-content">
				<div id="PO-plugin-search-div" style="width: 98%">
				  <div class="PO-loading-container fa fa-spinner fa-pulse"></div>
				  <div class="inside">
					<select name="PO_installed_plugins" id="PO-installed-plugins">
					  <?php foreach($installedPlugins as $pluginPath=>$pluginDetails) { ?>
					    <option value="<?php print $pluginPath; ?>"><?php print $pluginDetails['Name']; ?></option>
					  <?php } ?>
					</select>
					<span class="PO-help-dialog fa fa-question-circle" title="__ts__Plugin Search__te____rs__You can use this tool to search the database to find anywhere that a plugin has been disabled.  Select the plugin from the dropdown and then click the Start Search button.  If you want to search for a particular role you can select that role in the list of checkboxes below the plugin dropdown.  If you don\'t select a role then all roles will be searched.__re__"></span>
					<?php if (get_option("PO_disable_plugins_by_role") == '1') { ?>
						<div class="PO-searchable-roles-container">
						  <?php
						  $supportedRoles = get_option("PO_enabled_roles");
						  if (!is_array($supportedRoles)) {
							  $supportedRoles = array();
						  }
						  print '<div class="PO-searchable-roles-row"><input type="checkbox" class="PO-searchable-roles" name="PO_searchable_roles[]" value="_" />Not Logged In</div>';
						  print '<div class="PO-searchable-roles-row"><input type="checkbox" class="PO-searchable-roles" name="PO_searchable_roles[]" value="-" />Default Logged In</div>';
					
						  $availableRoles = get_editable_roles();
						  if (is_array($availableRoles)) {
							  foreach($supportedRoles as $roleID) {
								  print '<div class="PO-searchable-roles-row"><input type="checkbox" class="PO-searchable-roles" name="PO_searchable_roles[]" value="'.$roleID.'" />'.$availableRoles[$roleID]['name'].'</div>';
							  }
						  }
						  ?>
						</div>
					<?php } ?>
					<input type="button" name="PO_search_for_plugin" value="Start Search" onmousedown="PO_perform_plugin_search();" class="button button-primary">
					<div id="plugin-search-results"></div>
				  </div>
				</div>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
</div>

