<?php
class PO_Ajax {
	var $PO;
	private $postedData;
	
	function __construct($PO) {
		$this->PO = $PO;
		if (is_admin()) {
			$this->postedData = $_POST;
		}
	}

	function save_order() {
		if ( !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		if ( current_user_can( 'activate_plugins' ) ) {
			$plugins = get_option("active_plugins");
			if (preg_match("/^(([0-9])+[,]*)*$/", implode(",", $this->postedData['orderList'])) && preg_match("/^(([0-9])+[,]*)*$/", implode(",", $this->postedData['startOrder']))) {
				$newPlugArray = $this->PO->sanitize_post_array($this->postedData['orderList']);
				$startOrderArray = $this->PO->sanitize_post_array($this->postedData['startOrder']);
				if (sizeof(array_unique($newPlugArray)) == sizeof($plugins) && sizeof(array_unique($startOrderArray)) == sizeof($plugins)) {
					array_multisort($startOrderArray, $newPlugArray);
					array_multisort($newPlugArray, $plugins);
					update_option("active_plugins", $plugins);
					update_option("PO_saved_plugin_order", $plugins);
					$jsonResponse['alerts'][] = "The plugin load order has been changed.";
				} else {
					$jsonResponse['alerts'][] = "The order values were not unique so no changes were made.";
				}
			} else {
				$jsonResponse['alerts'][] = "Did not recieve the proper variables.  No changes made.";
			}
		} else {
			$jsonResponse['alerts'][] = "You dont have permissions to access this page.";
		}
		print json_encode($jsonResponse);
		die();
	}



	function create_group() {
		if ( !$this->PO->verify_nonce($this->postedData['PO_nonce']) || !current_user_can('activate_plugins')) {
			print "You dont have permissions to access this page.";
			die();
		}
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		$plugins = get_option("active_plugins");
		if (is_array($this->postedData['PO_group_list']) && $this->PO->validate_field('PO_group_name')) {
			$groupID = wp_insert_post(array('post_title'=>sanitize_text_field($this->postedData['PO_group_name']), 'post_type'=>'plugin_group', 'post_status'=>'publish'));
			if (!is_wp_error($groupID)) {
				update_post_meta($groupID, '_PO_group_members', $this->PO->sanitize_post_array($this->postedData['PO_group_list']));
				$jsonResponse['alerts'][] = "The " . get_the_title($groupID) . " group was created and the selected plugins have been added to it.";
				$jsonResponse['success'] = 1;
			} else {
				$jsonResponse['alerts'][] = "There was a problem creating the group.";
			}
			
		} else {
			$jsonResponse['alerts'][] = "Did not recieve the proper variables.  No changes made.";
		}
		print json_encode($jsonResponse);
		die();
	}


	function delete_group() {
		if ( !$this->PO->verify_nonce($this->postedData['PO_nonce']) || !current_user_can('activate_plugins')) {
			print "You dont have permissions to access this page.";
			die();
		}
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		if (is_array($this->postedData['PO_group_ids'])) {
			foreach($this->postedData['PO_group_ids'] as $groupID) {
				$groupID = sanitize_text_field($groupID);
				if (is_numeric($groupID)) {
					$groupName = get_the_title($groupID);
					$result = wp_delete_post($groupID, true);
					if ($result) {
						$jsonResponse['alerts'][] = "The " . $groupName . " plugin group has been deleted.";
					} else {
						$jsonResponse['alerts'][] = "There was a problem deleting the " . $groupName . " plugin group.";
					}
				}
			}
		}
		print json_encode($jsonResponse);
		die();
	}

	function remove_plugins_from_group() {
		if ( !$this->PO->verify_nonce($this->postedData['PO_nonce']) || !current_user_can('activate_plugins')) {
			print "You dont have permissions to access this page.";
			die();
		}
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		if (is_array($this->postedData['PO_group_ids']) && is_array($this->postedData['PO_group_list'])) {
			foreach($this->postedData['PO_group_ids'] as $groupID) {
				$groupID = sanitize_text_field($groupID);
				if (is_numeric($groupID)) {
					$members = get_post_meta($groupID, '_PO_group_members', $single=true);
					if (!is_array($members)) {
						$members = array();
					}
					foreach($this->postedData['PO_group_list'] as $key=>$pluginToRemove) {
						$key = sanitize_text_field($key);
						$pluginToRemove = sanitize_text_field($pluginToRemove);
						if (array_search($pluginToRemove, $members) !== FALSE) {
							unset($members[array_search($pluginToRemove, $members)]);
						}
					}
					$members = array_values($members);
					if ($members === get_post_meta($groupID, '_PO_group_members', $single=true)) {
						$jsonResponse['alerts'][] = "The selected plugins were not found in the " . get_the_title($groupID) . " group.";
					} else {
						$result = update_post_meta($groupID, "_PO_group_members", $members);
						if ($result) {
							$jsonResponse['alerts'][] = "The selected plugins were removed from the " . get_the_title($groupID) . " group.";
							$jsonResponse['success'] = 1;
						} else {
							$jsonResponse['alerts'][] = "There was a problem removing the plugins from the " . get_the_title($groupID) . " group.";
						}
					}
				}
			}
		}
		print json_encode($jsonResponse);
		die();

	}

	function add_to_group() {
		if ( !$this->PO->verify_nonce($this->postedData['PO_nonce']) || !current_user_can('activate_plugins')) {
			print "You dont have permissions to access this page.";
			die();
		}
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		$plugins = get_option("active_plugins");
		if (is_array($this->postedData['PO_group_list']) && is_array($this->postedData['PO_group_ids'])) {
			foreach($this->postedData['PO_group_ids'] as $groupID) {
				$groupID = sanitize_text_field($groupID);
				if (is_numeric($groupID)) {
					$members = get_post_meta($groupID, '_PO_group_members', $single=true);
					$members = stripslashes_deep($members);
					if (!is_array($members)) {
						$members = array();
					}
					
					foreach($this->postedData['PO_group_list'] as $newGroupMember) {
						$newGroupMember = sanitize_text_field($newGroupMember);
						#print $newGroupMember . " - " . array_search($newGroupMember, $members) . "\n";
						if (array_search($newGroupMember, $members) === FALSE) {
							$members[]=$newGroupMember;
						}
					}
					if ($members === get_post_meta($groupID, '_PO_group_members', $single=true)) {
						$jsonResponse['alerts'][] = "The selected plugins were not added to the " . get_the_title($groupID) . " group because they already belong to it.";
					} else {
						update_post_meta($groupID, "_PO_group_members", $members);
						$jsonResponse['alerts'][] = "The selected plugins were added to the " . get_the_title($groupID) . " group.";
						$jsonResponse['success'] = 1;
					}
				}
			}
		} else {
			$jsonResponse['alerts'][] = "Did not recieve the proper variables.  No changes made.";
		}
		print json_encode($jsonResponse);
		die();
	}

	function edit_plugin_group_name() {
		if ( !$this->PO->verify_nonce($this->postedData['PO_nonce']) || !current_user_can( 'activate_plugins' ) ) {
			print "You dont have permissions to access this page.";
			die();
		}
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		if (is_numeric($this->postedData['PO_group_id']) && $this->PO->validate_field('PO_group_name')) {
			$postedGroupID = sanitize_text_field($this->postedData['PO_group_id']);
			$postedGroupName = sanitize_text_field($this->postedData['PO_group_name']);
			$oldGroupTitle = get_the_title($postedGroupID);
			$post_id = wp_update_post(array('ID'=>$postedGroupID, 'post_title'=>$postedGroupName));
			if ($post_id > 0) {
				$newGroupTitle = get_the_title($postedGroupID);
				$jsonResponse['alerts'][] = "The " . $oldGroupTitle . " group was successfully changed to " . $newGroupTitle . ".";
				$jsonResponse['success'] = 1;
			} else {
				$jsonResponse['alerts'][] = "There was an error and the " . $oldGroupTitle . " group was not changed.";
			}
		} else {
			$jsonResponse['alerts'][] = "No changes were made because the correct variables were not received.";
		}
		print json_encode($jsonResponse);
		die();
	}

	function save_global_plugins() {
		global $wpdb;
		if ( !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		if ( current_user_can( 'activate_plugins' ) ) {
			if (isset($this->postedData['PO_disabled_std_plugin_list']) && is_array($this->postedData['PO_disabled_std_plugin_list']) && $this->postedData['PO_disabled_std_plugin_list'][0] != 'EMPTY') {
				$disabledPlugins = $this->PO->sanitize_post_array($this->postedData['PO_disabled_std_plugin_list']);
			} else {
				$disabledPlugins = array();
			}
			
			if (isset($this->postedData['PO_disabled_std_group_list']) && is_array($this->postedData['PO_disabled_std_group_list']) && $this->postedData['PO_disabled_std_group_list'][0] != 'EMPTY') {
				$disabledGroups = $this->PO->sanitize_post_array($this->postedData['PO_disabled_std_group_list']);
			} else {
				$disabledGroups = array();
			}
			
			if (get_option('PO_disable_plugins_mobile') == 1) {
				if (isset($this->postedData['PO_disabled_mobile_plugin_list']) && is_array($this->postedData['PO_disabled_mobile_plugin_list']) && $this->postedData['PO_disabled_mobile_plugin_list'][0] != 'EMPTY') {
					$disabledMobilePlugins = $this->PO->sanitize_post_array($this->postedData['PO_disabled_mobile_plugin_list']);
				} else {
					$disabledMobilePlugins = array();
				}

				if (isset($this->postedData['PO_disabled_mobile_group_list']) && is_array($this->postedData['PO_disabled_mobile_group_list']) && $this->postedData['PO_disabled_mobile_group_list'][0] != 'EMPTY') {
					$disabledMobileGroups = $this->PO->sanitize_post_array($this->postedData['PO_disabled_mobile_group_list']);
				} else {
					$disabledMobileGroups = array();
				}
			} else {
				$disabledMobilePlugins = array();
				$disabledMobileGroups = array();
			}

			$globalPluginsExist = ($wpdb->get_var("SELECT count(*) FROM ".$wpdb->prefix."po_plugins WHERE post_type='global_plugin_lists' AND post_id=0") > 0) ? 1 : 0;
		
			if ($globalPluginsExist == 1) {
				$result = $wpdb->update($wpdb->prefix."po_plugins", array("enabled_plugins"=>serialize(array()), "disabled_plugins"=>serialize($disabledPlugins), "enabled_mobile_plugins"=>serialize(array()), "disabled_mobile_plugins"=>serialize($disabledMobilePlugins), "enabled_groups"=>serialize(array()), "disabled_groups"=>serialize($disabledGroups), "enabled_mobile_groups"=>serialize(array()), "disabled_mobile_groups"=>serialize($disabledMobileGroups)), array("post_type"=>"global_plugin_lists", "post_id"=>0));
			} else {
				$result = $wpdb->insert($wpdb->prefix."po_plugins", array("post_id"=>0, "enabled_plugins"=>serialize(array()), "disabled_plugins"=>serialize($disabledPlugins), "enabled_mobile_plugins"=>serialize(array()), "disabled_mobile_plugins"=>serialize($disabledMobilePlugins), "enabled_groups"=>serialize(array()), "disabled_groups"=>serialize($disabledGroups), "enabled_mobile_groups"=>serialize(array()), "disabled_mobile_groups"=>serialize($disabledMobileGroups), "post_type"=>"global_plugin_lists"));
			}

			if ($result === false) {
				$jsonResponse['alerts'][] = "There was an error saving the global plugin lists.";
			} else {
				$jsonResponse['alerts'][] = "Global plugins successfully saved.";
				$jsonResponse['success'] = 1;
			}
		} else {
			$jsonResponse['alerts'][] = "You dont have permissions to access this page.";
		}
		print json_encode($jsonResponse);
		die();
	}

	function save_search_plugins() {
		global $wpdb;
		if ( !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		$availableRoles = $this->PO->get_available_roles();
		
		foreach($availableRoles as $roleID=>$roleName) {
			
			$submittedPlugins = $this->PO->get_submitted_plugin_lists($roleID);
		
			$searchPluginsExist = ($wpdb->get_var($wpdb->prepare("SELECT count(*) FROM ".$wpdb->prefix."po_plugins WHERE post_type='search_plugin_lists' AND post_id=0 AND user_role=%s", $roleID)) > 0) ? 1 : 0;
		
			if ($searchPluginsExist == 1) {
				$wpdb->update($wpdb->prefix."po_plugins", array("enabled_plugins"=>serialize($submittedPlugins[1]), "disabled_plugins"=>serialize($submittedPlugins[0]), "enabled_mobile_plugins"=>serialize($submittedPlugins[3]), "disabled_mobile_plugins"=>serialize($submittedPlugins[2]), "enabled_groups"=>serialize($submittedPlugins[5]), "disabled_groups"=>serialize($submittedPlugins[4]), "enabled_mobile_groups"=>serialize($submittedPlugins[7]), "disabled_mobile_groups"=>serialize($submittedPlugins[6])), array("post_type"=>"search_plugin_lists", "post_id"=>0, "user_role"=>$roleID));
			} else {
				$wpdb->insert($wpdb->prefix."po_plugins", array("post_id"=>0, "enabled_plugins"=>serialize($submittedPlugins[1]), "disabled_plugins"=>serialize($submittedPlugins[0]), "enabled_mobile_plugins"=>serialize($submittedPlugins[3]), "disabled_mobile_plugins"=>serialize($submittedPlugins[2]), "enabled_groups"=>serialize($submittedPlugins[5]), "disabled_groups"=>serialize($submittedPlugins[4]), "enabled_mobile_groups"=>serialize($submittedPlugins[7]), "disabled_mobile_groups"=>serialize($submittedPlugins[6]), "post_type"=>"search_plugin_lists", "user_role"=>$roleID));
			}
		}
		
		$jsonResponse['alerts'][] = "Search plugins updated!";
		print json_encode($jsonResponse);
		die();
	}

	function save_pt_plugins() {
		global $wpdb;
		$availableRoles = $this->PO->get_available_roles();
			
		$returnVals = array('success'=>0, 'msg'=>'', 'total'=>0, 'offset'=>0);
		if ( !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			$returnVals['msg'] = "You dont have permissions to access this page.";
			print json_encode($returnVals);
			die();
		}

		if (isset($this->postedData['selectedPostType']) && $this->postedData['selectedPostType'] != '') {
			$postType = sanitize_text_field($this->postedData['selectedPostType']);
		} else {
			$returnVals['msg'] = "Plugins were not updated because no post type was recieved!";
			print json_encode($returnVals);
			die();
		}

		$supportedPostTypes = get_option("PO_custom_post_type_support");
		if (!is_array($supportedPostTypes)) {
			$supportedPostTypes = array();
		}
		
		if (is_numeric($this->postedData['PO_post_offset'])) {
			$returnVals['offset'] = sanitize_text_field($this->postedData['PO_post_offset']);
		} else {
			$returnVals['msg'] = "Plugins were not updated because there was a problem calculating the number of posts to update.";
			print json_encode($returnVals);
			die();
		}
		
		if (!in_array($postType, $supportedPostTypes)) {
			$returnVals['msg'] = "Plugins were not updated because you have not selected this post type on the settings page.";
			print json_encode($returnVals);
			die();
		}
		
		$submittedPlugins = array();
		
		
		
		if ($returnVals['offset'] == 0) {
			foreach($availableRoles as $roleID=>$roleName) {
				$postTypeExists = ($wpdb->get_var($wpdb->prepare("SELECT count(*) FROM ".$wpdb->prefix."po_plugins WHERE post_type=%s AND post_id=0 AND user_role=%s", "pt_".$postType."_plugin_lists", $roleID)) > 0) ? 1 : 0;

				$submittedPlugins[$roleID] = $this->PO->get_submitted_plugin_lists($roleID);
				
				if ($postTypeExists == 1) {
					$wpdb->update($wpdb->prefix."po_plugins", array("enabled_plugins"=>serialize($submittedPlugins[$roleID][1]), "disabled_plugins"=>serialize($submittedPlugins[$roleID][0]), "enabled_mobile_plugins"=>serialize($submittedPlugins[$roleID][3]), "disabled_mobile_plugins"=>serialize($submittedPlugins[$roleID][2]), "enabled_groups"=>serialize($submittedPlugins[$roleID][5]), "disabled_groups"=>serialize($submittedPlugins[$roleID][4]), "enabled_mobile_groups"=>serialize($submittedPlugins[$roleID][7]), "disabled_mobile_groups"=>serialize($submittedPlugins[$roleID][6])), array("post_type"=>"pt_".$postType."_plugin_lists", "post_id"=>0, "user_role"=>$roleID));
				} else {
					$wpdb->insert($wpdb->prefix."po_plugins", array("post_id"=>0, "enabled_plugins"=>serialize($submittedPlugins[$roleID][1]), "disabled_plugins"=>serialize($submittedPlugins[$roleID][0]), "enabled_mobile_plugins"=>serialize($submittedPlugins[$roleID][3]), "disabled_mobile_plugins"=>serialize($submittedPlugins[$roleID][2]), "enabled_groups"=>serialize($submittedPlugins[$roleID][5]), "disabled_groups"=>serialize($submittedPlugins[$roleID][4]), "enabled_mobile_groups"=>serialize($submittedPlugins[$roleID][7]), "disabled_mobile_groups"=>serialize($submittedPlugins[$roleID][6]), "post_type"=>"pt_".$postType."_plugin_lists", "user_role"=>$roleID));
				}
			}

				
			$ptStored = get_option('PO_pt_stored');
			if (!is_array($ptStored)) {
				$ptStored = array();
			}
			
			if (!in_array($postType, $ptStored)) {
				$ptStored[] = $postType;
				update_option('PO_pt_stored', $ptStored);
			}
		} else {
			foreach($availableRoles as $roleID=>$roleName) {
				$sql = "SELECT disabled_plugins, enabled_plugins, disabled_mobile_plugins, enabled_mobile_plugins, disabled_groups, enabled_groups, disabled_mobile_groups, enabled_mobile_groups FROM ".$wpdb->prefix."po_plugins WHERE post_type=%s AND post_id=0 AND user_role=%s";
				$storedPluginLists = $wpdb->get_row($wpdb->prepare($sql, "pt_".$postType."_plugin_lists", $roleID), ARRAY_A);
				
				$submittedPlugins[$roleID] = array();
				$submittedPlugins[$roleID][] = (is_array(@unserialize($storedPluginLists['disabled_plugins'])))? @unserialize($storedPluginLists['disabled_plugins']):array();
				$submittedPlugins[$roleID][] = (is_array(@unserialize($storedPluginLists['enabled_plugins'])))? @unserialize($storedPluginLists['enabled_plugins']):array();
				$submittedPlugins[$roleID][] = (is_array(@unserialize($storedPluginLists['disabled_mobile_plugins'])))? @unserialize($storedPluginLists['disabled_mobile_plugins']):array();
				$submittedPlugins[$roleID][] = (is_array(@unserialize($storedPluginLists['enabled_mobile_plugins'])))? @unserialize($storedPluginLists['enabled_mobile_plugins']):array();
				$submittedPlugins[$roleID][] = (is_array(@unserialize($storedPluginLists['disabled_groups'])))? @unserialize($storedPluginLists['disabled_groups']):array();
				$submittedPlugins[$roleID][] = (is_array(@unserialize($storedPluginLists['enabled_groups'])))? @unserialize($storedPluginLists['enabled_groups']):array();
				$submittedPlugins[$roleID][] = (is_array(@unserialize($storedPluginLists['disabled_mobile_groups'])))? @unserialize($storedPluginLists['disabled_mobile_groups']):array();
				$submittedPlugins[$roleID][] = (is_array(@unserialize($storedPluginLists['enabled_mobile_groups'])))? @unserialize($storedPluginLists['enabled_mobile_groups']):array();
			}
		}

		if (!is_numeric($this->postedData['PO_total_post_count']) || $this->postedData['PO_total_post_count'] == 0) {
			$query = "SELECT COUNT(*) AS num_posts FROM ".$wpdb->posts." WHERE post_type = %s";
			$returnVals['total'] = $wpdb->get_var($wpdb->prepare($query, $postType));
		} else {
			$returnVals['total'] = sanitize_text_field($this->postedData['PO_total_post_count']);
		}
		
		$allPosts = get_posts(array('post_type'=>$postType, 'posts_per_page'=>100, 'offset'=>$returnVals['offset'], 'orderby'=>'post_id'));
		foreach($allPosts as $post) {
			$postStatus = get_post_status($post->ID);
			if (!$postStatus) {
				$postStatus = 'publish';
			}

			$ptOverride = $wpdb->get_var($wpdb->prepare("SELECT pt_override FROM ".$wpdb->prefix."po_plugins WHERE post_id=%d", $post->ID));

			$secure=0;
			if (preg_match('/^.{1,5}:\/\//', get_permalink($post->ID), $matches)) {
				switch ($matches[0]) {
					case "https://":
						$secure=1;
						break;
					default:
						$secure=0;
				}
			}
			$permalink = preg_replace('/^.{1,5}:\/\//', '', get_permalink($post->ID));
				
			$permalinkNoArgs = preg_replace('/\?.*$/', '', $permalink);
		
			$dirCount = substr_count($permalink, "/");
			foreach($availableRoles as $roleID=>$roleName) {
				
				if ($ptOverride == '0') {
					$postTypeExists = ($wpdb->get_var($wpdb->prepare("SELECT count(*) FROM ".$wpdb->prefix."po_plugins WHERE post_id=%d AND user_role=%s", $post->ID, $roleID)) > 0) ? 1 : 0;
					if ($postTypeExists == '1') {
						$wpdb->update($wpdb->prefix."po_plugins", array("permalink"=>$permalink, "permalink_hash"=>md5($permalinkNoArgs), "permalink_hash_args"=>md5($permalink), "enabled_plugins"=>serialize($submittedPlugins[$roleID][1]), "disabled_plugins"=>serialize($submittedPlugins[$roleID][0]), "enabled_mobile_plugins"=>serialize($submittedPlugins[$roleID][3]), "disabled_mobile_plugins"=>serialize($submittedPlugins[$roleID][2]), "enabled_groups"=>serialize($submittedPlugins[$roleID][5]), "disabled_groups"=>serialize($submittedPlugins[$roleID][4]), "enabled_mobile_groups"=>serialize($submittedPlugins[$roleID][7]), "disabled_mobile_groups"=>serialize($submittedPlugins[$roleID][6]), "secure"=>$secure, "post_type"=>get_post_type($post->ID), "status"=>$postStatus, "dir_count"=>$dirCount), array("post_id"=>$post->ID, "user_role"=>$roleID));
					} else {
						$wpdb->insert($wpdb->prefix."po_plugins", array("post_id"=>$post->ID, "permalink"=>$permalink, "permalink_hash"=>md5($permalinkNoArgs), "permalink_hash_args"=>md5($permalink), "enabled_plugins"=>serialize($submittedPlugins[$roleID][1]), "disabled_plugins"=>serialize($submittedPlugins[$roleID][0]), "enabled_mobile_plugins"=>serialize($submittedPlugins[$roleID][3]), "disabled_mobile_plugins"=>serialize($submittedPlugins[$roleID][2]), "enabled_groups"=>serialize($submittedPlugins[$roleID][5]), "disabled_groups"=>serialize($submittedPlugins[$roleID][4]), "enabled_mobile_groups"=>serialize($submittedPlugins[$roleID][7]), "disabled_mobile_groups"=>serialize($submittedPlugins[$roleID][6]), "secure"=>$secure, "post_type"=>get_post_type($post->ID), "status"=>$postStatus, "dir_count"=>$dirCount, "user_role"=>$roleID));
					}
				} else if ($ptOverride == '') {
					$wpdb->insert($wpdb->prefix."po_plugins", array("post_id"=>$post->ID, "permalink"=>$permalink, "permalink_hash"=>md5($permalinkNoArgs), "permalink_hash_args"=>md5($permalink), "enabled_plugins"=>serialize($submittedPlugins[$roleID][1]), "disabled_plugins"=>serialize($submittedPlugins[$roleID][0]), "enabled_mobile_plugins"=>serialize($submittedPlugins[$roleID][3]), "disabled_mobile_plugins"=>serialize($submittedPlugins[$roleID][2]), "enabled_groups"=>serialize($submittedPlugins[$roleID][5]), "disabled_groups"=>serialize($submittedPlugins[$roleID][4]), "enabled_mobile_groups"=>serialize($submittedPlugins[$roleID][7]), "disabled_mobile_groups"=>serialize($submittedPlugins[$roleID][6]), "secure"=>$secure, "post_type"=>get_post_type($post->ID), "status"=>$postStatus, "dir_count"=>$dirCount, "user_role"=>$roleID));
				}
			}
		}
		
		$returnVals['success'] = 1;
		$returnVals['msg'] = "Plugins updated for " . sanitize_text_field($this->postedData['selectedPostType']) . ".";
		print json_encode($returnVals);
		die();
	}

	function get_pt_plugins() {
		global $wpdb;
		if ( !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		if (!isset($this->postedData['selectedPostType']) || !in_array($this->postedData['selectedPostType'], get_option('PO_custom_post_type_support'))) {
			print "post_type_not_supported";
			die();
		} else {
			$postType=sanitize_text_field($this->postedData['selectedPostType']);
		}
		
		$availableRoles = $this->PO->get_available_roles();
		
		$pluginLists = array();
		foreach($availableRoles as $roleID=>$roleName) {
			$sql = "SELECT disabled_plugins, enabled_plugins, disabled_mobile_plugins, enabled_mobile_plugins, disabled_groups, enabled_groups, disabled_mobile_groups, enabled_mobile_groups FROM ".$wpdb->prefix."po_plugins WHERE post_type=%s AND post_id=0 AND user_role=%s";
			$storedPluginLists = $wpdb->get_row($wpdb->prepare($sql, "pt_".$postType."_plugin_lists", $roleID), ARRAY_A);
			
			$pluginLists[$roleID] = array();
			$pluginLists[$roleID][] = (is_array(@unserialize($storedPluginLists['disabled_plugins'])))? @unserialize($storedPluginLists['disabled_plugins']):array();
			$pluginLists[$roleID][] = (is_array(@unserialize($storedPluginLists['enabled_plugins'])))? @unserialize($storedPluginLists['enabled_plugins']):array();
			$pluginLists[$roleID][] = (is_array(@unserialize($storedPluginLists['disabled_mobile_plugins'])))? @unserialize($storedPluginLists['disabled_mobile_plugins']):array();
			$pluginLists[$roleID][] = (is_array(@unserialize($storedPluginLists['enabled_mobile_plugins'])))? @unserialize($storedPluginLists['enabled_mobile_plugins']):array();
			$pluginLists[$roleID][] = (is_array(@unserialize($storedPluginLists['disabled_groups'])))? @unserialize($storedPluginLists['disabled_groups']):array();
			$pluginLists[$roleID][] = (is_array(@unserialize($storedPluginLists['enabled_groups'])))? @unserialize($storedPluginLists['enabled_groups']):array();
			$pluginLists[$roleID][] = (is_array(@unserialize($storedPluginLists['disabled_mobile_groups'])))? @unserialize($storedPluginLists['disabled_mobile_groups']):array();
			$pluginLists[$roleID][] = (is_array(@unserialize($storedPluginLists['enabled_mobile_groups'])))? @unserialize($storedPluginLists['enabled_mobile_groups']):array();
		}
		print json_encode($pluginLists);
		die();
	}

	function reset_pt_settings() {
		global $wpdb;
		if ( !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		if (!isset($this->postedData['selectedPostType']) || !in_array($this->postedData['selectedPostType'], get_option('PO_custom_post_type_support'))) {
			print "post_type_not_supported";
			die();
		} else {
			$postType=sanitize_text_field($this->postedData['selectedPostType']);
		}
		
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		
		$wpdb->delete($wpdb->prefix."po_plugins", array("post_type"=>"pt_".$postType."_plugin_lists", "post_id"=>0));

		$ptStored = get_option('PO_pt_stored');
		if (!is_array($ptStored)) {
			$ptStored = array();
		}
		
		if (in_array($postType, $ptStored)) {
			unset($ptStored[array_search($postType, $ptStored)]);
			update_option('PO_pt_stored', array_values($ptStored));
		}
		
		$jsonResponse['alerts'][] = "Plugin settings have been reset to default for the " . htmlentities(strip_tags($postType)) . " post type.";
		if (isset($this->postedData['PO_reset_all_pt']) && $this->postedData['PO_reset_all_pt'] == "1") {
			$wpdb->delete($wpdb->prefix."po_plugins", array("post_type"=>$postType, "pt_override"=>0));

			$jsonResponse['alerts'][] = "Plugin settings were also reset on each " . $postType . ".";
		}
		
		print json_encode($jsonResponse);
		die();
	}
	
	function redo_permalinks() {
		global $wpdb;
		if (!empty($this->postedData['old_site_address'])) {
			$oldSiteAddress = preg_quote($this->PO->fix_trailng_slash(sanitize_text_field($this->postedData['old_site_address'])), "/");
		} else {
			$oldSiteAddress = "";
		}

		if (!empty($this->postedData['new_site_address'])) {
			$newSiteAddress = $this->PO->fix_trailng_slash(sanitize_text_field($this->postedData['new_site_address']));
		} else {
			$newSiteAddress = "";
		}
		
		$failedCount = 0;
		$updatedCount = 0;
		$noUpdateCount = 0;
		if ( !current_user_can( 'activate_plugins' ) || !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		
		$postIDsCountQuery = "SELECT count(DISTINCT post_id) FROM ".$wpdb->prefix."po_plugins WHERE post_type != 'plugin_filter' AND post_id != 0";
		$postIDsCount = $wpdb->get_var($postIDsCountQuery);
		
		$postIDsQuery = "SELECT post_id FROM ".$wpdb->prefix."po_plugins WHERE post_type != 'plugin_filter' AND post_id != 0 AND permalink NOT LIKE [ESC_LIKE] GROUP BY post_id";
		$postIDsQuery = preg_replace('/\[ESC_LIKE\]/', "'".$wpdb->esc_like($newSiteAddress)."%'", $postIDsQuery);
		
		$postIDs = $wpdb->get_results($postIDsQuery, ARRAY_A);
		foreach ($postIDs as $postID) {
			$post = get_post($postID['post_id']);
			if (!is_null($post)) {
				$secure=0;
				if (preg_match('/^.{1,5}:\/\//', get_permalink($post->ID), $matches)) {
					switch ($matches[0]) {
						case "https://":
							$secure=1;
							break;
						default:
							$secure=0;
					}
				}
				$permalink = preg_replace('/^.{1,5}:\/\//', '', get_permalink($post->ID));
				
				$dirCount = substr_count($permalink, "/");
				$storedPermalink = $wpdb->get_row("SELECT permalink, permalink_hash, permalink_hash_args FROM ".$wpdb->prefix."po_plugins WHERE post_id=".$post->ID, ARRAY_A);
				if (!is_null($storedPermalink) && ($permalink != $storedPermalink['permalink'] || md5($permalink) != $storedPermalink['permalink_hash'] || md5($permalink) != $storedPermalink['permalink_hash_args'])) {
					
					if($wpdb->update($wpdb->prefix."po_plugins", array('permalink'=>$permalink, 'permalink_hash'=>md5($permalink), 'permalink_hash_args'=>md5($permalink), 'secure'=>$secure, "dir_count"=>$dirCount), array("post_id"=>$post->ID))) {
						$updatedCount++;
					} else {
						$failedCount++;
					}
				}
			} else {
				$failedCount++;
			}
		}

		$noUpdateCount = $postIDsCount - $updatedCount - $failedCount;
		
		if ($oldSiteAddress != "" && $newSiteAddress != "") {
			$filterQuery = "SELECT * FROM ".$wpdb->prefix."po_plugins WHERE post_type = 'plugin_filter'";
			$filters = $wpdb->get_results($filterQuery, ARRAY_A);
			foreach ($filters as $filter) {
				$filterObject = get_post($filter['post_id']);
				if (!is_null($filterObject)) {
					if (preg_match('/^'.$oldSiteAddress.'/', $filter['permalink'])) {
						$permalink = preg_replace("/^".$oldSiteAddress."/", "".$newSiteAddress."", $filter['permalink']);
						$dirCount = substr_count($permalink, "/");
						if ($wpdb->update($wpdb->prefix."po_plugins", array('permalink'=>$permalink, 'permalink_hash'=>md5($permalinkNoArgs), 'permalink_hash_args'=>md5($permalink), "dir_count"=>$dirCount), array("post_id"=>$filter['post_id']))) {
							$updatedCount++;
						} else {
							$failedCount++;
						}
					} else {
						$noUpdateCount++;
					}
				}
			}
		} else {
			$jsonResponse['alerts'][] = "Plugin Filters were not updated since the new or old address was blank.";
		}

		if ($failedCount > 0) {
			$jsonResponse['alerts'][] = $failedCount . " permalinks failed to update!";
			$jsonResponse['alerts'][] = $updatedCount . " permalinks were updated successfully.";
			$jsonResponse['alerts'][] = $noUpdateCount . " permalinks were already up to date.";
		} else {
			$jsonResponse['alerts'][] = $updatedCount . " permalinks were updated successfully.";
			$jsonResponse['alerts'][] = $noUpdateCount . " permalinks were already up to date.";
		}
		$jsonResponse['success'] = 1;
		print json_encode($jsonResponse);
		die();
	}

	function manage_mu_plugin() {
		if ( !current_user_can( 'activate_plugins' ) || !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		if ($this->postedData['selected_action'] == 'delete') {
			if (file_exists(WPMU_PLUGIN_DIR . "/PluginOrganizerMU.class.php")) {
				if (@unlink(WPMU_PLUGIN_DIR . "/PluginOrganizerMU.class.php")) {
					$jsonResponse['alerts'][] = "The MU plugin component has been removed.";
				} else {
					$jsonResponse['alerts'][] = "There was an issue removing the MU plugin component!";
				}
			} else {
				$jsonResponse['alerts'][] = "There was an issue removing the MU plugin component!";
			}
		} else if ($this->postedData['selected_action'] == 'move') {
			if (!file_exists(WPMU_PLUGIN_DIR)) {
				@mkdir(WPMU_PLUGIN_DIR);
			}
			if (file_exists($this->PO->pluginDirPath . "/" . plugin_basename(dirname(__FILE__)) . "/PluginOrganizerMU.class.php")) {
				@unlink(WPMU_PLUGIN_DIR . "/PluginOrganizerMU.class.php");
				@copy($this->PO->pluginDirPath . "/" . plugin_basename(dirname(__FILE__)) . "/PluginOrganizerMU.class.php", WPMU_PLUGIN_DIR . "/PluginOrganizerMU.class.php");
			}
			if (file_exists(WPMU_PLUGIN_DIR . "/PluginOrganizerMU.class.php")) {
				$jsonResponse['alerts'][] = "The MU plugin component has been moved to the mu-plugins folder.";
			} else {
				$jsonResponse['alerts'][] = "There was an issue moving the MU plugin component!";
			}
		}
		$jsonResponse['success'] = 1;
		print json_encode($jsonResponse);
		die();
	}

	function reset_plugin_order() {
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		$activePlugins = get_option("active_plugins");
		usort($activePlugins, array($this->PO, 'custom_sort_plugins'));
		$sortedPlugins = array();
		foreach ($activePlugins as $plugin) {
			if (is_plugin_active_for_network($plugin)) {
				$sortedPlugins[] = $plugin;
			}
		}

		foreach ($activePlugins as $plugin) {
			if (!is_plugin_active_for_network($plugin)) {
				$sortedPlugins[] = $plugin;
			}
		}
		update_option("active_plugins", $sortedPlugins);
		update_option("PO_saved_plugin_order", $sortedPlugins);
		$jsonResponse['alerts'][] = "The order has been reset.";
		$jsonResponse['success'] = 1;
		print json_encode($jsonResponse);
		die();
	}

	function save_mobile_user_agents() {
		if ( !current_user_can( 'activate_plugins' ) || !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		
		$jsonResponse = array('success'=>0, 'alerts'=>array(), 'user_agent_list'=>'');
		
		$userAgents = preg_replace("/\\r\\n/", "\n", sanitize_textarea_field($this->postedData['PO_mobile_user_agents']));
		$userAgents = explode("\n", $userAgents);
		if (!is_array($userAgents)) {
			$userAgents = array();
		}
		foreach ($userAgents as $key=>$agent) {
			if ($agent == '') {
				unset($userAgents[$key]);
			}
		}
		
		if (get_option('PO_mobile_user_agents') == $userAgents) {
			$jsonResponse['alerts'][] = "The user agent list matches the database.";
			$jsonResponse['success'] = 1;
		} else if (update_option('PO_mobile_user_agents', $userAgents)) {
			$jsonResponse['alerts'][] = "The user agents were saved.";
			$jsonResponse['success'] = 1;
		} else {
			$jsonResponse['alerts'][] = "There was a problem saving the user agents.";
		}
		$jsonResponse['user_agent_list'] = implode("\n", get_option('PO_mobile_user_agents'));
		print json_encode($jsonResponse);
		die();
	}

	function disable_compat_notices() {
		if (!current_user_can('activate_plugins') || !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		update_option('PO_disable_compat_notices', 1);
		die();
	}
	
	function disable_admin_warning() {
		if (!current_user_can('activate_plugins') || !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		update_option('PO_disable_admin_warning', 1);
		die();
	}
	
	function disable_admin_notices() {
		if (!current_user_can('activate_plugins') || !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		update_option('PO_disable_admin_notices', 1);
		die();
	}

	function disable_debug_msg() {
		if (!current_user_can('activate_plugins') || !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		update_option('PO_display_debug_msg', 0);
		die();
	}
	
	function submit_admin_css_settings() {
		if ( !current_user_can( 'activate_plugins' ) || !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		
		$POAdminStyles = get_option('PO_admin_styles');
		if (!is_array($POAdminStyles)) {
			$POAdminStyles = array();
		}
		
		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_network_plugins_bg_color'])) {
			$POAdminStyles['network_plugins_bg_color'] = sanitize_text_field($this->postedData['PO_network_plugins_bg_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Network Plugin Background is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_network_plugins_font_color'])) {
			$POAdminStyles['network_plugins_font_color'] = sanitize_text_field($this->postedData['PO_network_plugins_font_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Network Plugin Font is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_active_plugins_bg_color'])) {
			$POAdminStyles['active_plugins_bg_color'] = sanitize_text_field($this->postedData['PO_active_plugins_bg_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Active Plugin Background is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_active_plugins_font_color'])) {
			$POAdminStyles['active_plugins_font_color'] = sanitize_text_field($this->postedData['PO_active_plugins_font_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Active Plugin Font is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_active_plugins_border_color'])) {
			$POAdminStyles['active_plugins_border_color'] = sanitize_text_field($this->postedData['PO_active_plugins_border_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Active Plugin Border is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_inactive_plugins_bg_color'])) {
			$POAdminStyles['inactive_plugins_bg_color'] = sanitize_text_field($this->postedData['PO_inactive_plugins_bg_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Inactive Plugin Background is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_inactive_plugins_font_color'])) {
			$POAdminStyles['inactive_plugins_font_color'] = sanitize_text_field($this->postedData['PO_inactive_plugins_font_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Inactive Plugin Font is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_inactive_plugins_border_color'])) {
			$POAdminStyles['inactive_plugins_border_color'] = sanitize_text_field($this->postedData['PO_inactive_plugins_border_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Inactive Plugin Border is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_plugin_groups_bg_color'])) {
			$POAdminStyles['plugin_groups_bg_color'] = sanitize_text_field($this->postedData['PO_plugin_groups_bg_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Plugin Group Background is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_plugin_groups_font_color'])) {
			$POAdminStyles['plugin_groups_font_color'] = sanitize_text_field($this->postedData['PO_plugin_groups_font_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Plugin Group Font is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_plugin_groups_border_color'])) {
			$POAdminStyles['plugin_groups_border_color'] = sanitize_text_field($this->postedData['PO_plugin_groups_border_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Plugin Group Border is not a valid css hex color.<br />";
		}
		
		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_global_plugins_bg_color'])) {
			$POAdminStyles['global_plugins_bg_color'] = sanitize_text_field($this->postedData['PO_global_plugins_bg_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Global Plugin Background is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_global_plugins_font_color'])) {
			$POAdminStyles['global_plugins_font_color'] = sanitize_text_field($this->postedData['PO_global_plugins_font_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Global Plugin Font is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_global_plugins_border_color'])) {
			$POAdminStyles['global_plugins_border_color'] = sanitize_text_field($this->postedData['PO_global_plugins_border_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Global Plugin Border is not a valid css hex color.<br />";
		}



		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_selected_plugins_bg_color'])) {
			$POAdminStyles['selected_plugins_bg_color'] = sanitize_text_field($this->postedData['PO_selected_plugins_bg_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Selected Plugin Background is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_selected_plugins_font_color'])) {
			$POAdminStyles['selected_plugins_font_color'] = sanitize_text_field($this->postedData['PO_selected_plugins_font_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Selected Plugin Font is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_selected_plugins_border_color'])) {
			$POAdminStyles['selected_plugins_border_color'] = sanitize_text_field($this->postedData['PO_selected_plugins_border_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Selected Plugin Border is not a valid css hex color.<br />";
		}




		##Buttons 
		
		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_on_btn_bg_color'])) {
			$POAdminStyles['on_btn_bg_color'] = sanitize_text_field($this->postedData['PO_on_btn_bg_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for On Button Background is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_on_btn_font_color'])) {
			$POAdminStyles['on_btn_font_color'] = sanitize_text_field($this->postedData['PO_on_btn_font_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for On Button Font is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_off_btn_bg_color'])) {
			$POAdminStyles['off_btn_bg_color'] = sanitize_text_field($this->postedData['PO_off_btn_bg_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Off Button Background is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_off_btn_font_color'])) {
			$POAdminStyles['off_btn_font_color'] = sanitize_text_field($this->postedData['PO_off_btn_font_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Off Button Font is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_yes_btn_bg_color'])) {
			$POAdminStyles['yes_btn_bg_color'] = sanitize_text_field($this->postedData['PO_yes_btn_bg_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Yes Button Background is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_yes_btn_font_color'])) {
			$POAdminStyles['yes_btn_font_color'] = sanitize_text_field($this->postedData['PO_yes_btn_font_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for Yes Button Font is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_no_btn_bg_color'])) {
			$POAdminStyles['no_btn_bg_color'] = sanitize_text_field($this->postedData['PO_no_btn_bg_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for No Button Background is not a valid css hex color.<br />";
		}

		if (preg_match("/^(#[0-9A-Fa-f]{0,6}|)$/", $this->postedData['PO_no_btn_font_color'])) {
			$POAdminStyles['no_btn_font_color'] = sanitize_text_field($this->postedData['PO_no_btn_font_color']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for No Button Font is not a valid css hex color.<br />";
		}

		if (preg_match("/^[#:; %\-0-9A-Za-z]*$/", $this->postedData['PO_front_debug_style'])) {
			$POAdminStyles['front_debug_style'] = sanitize_text_field($this->postedData['PO_front_debug_style']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for the frontend debug container style is invalid CSS.<br />";
		}

		if (preg_match("/^[#:; %\-0-9A-Za-z]*$/", $this->postedData['PO_admin_debug_style'])) {
			$POAdminStyles['admin_debug_style'] = sanitize_text_field($this->postedData['PO_admin_debug_style']);
		} else {
			$jsonResponse['alerts'][] = "The text submitted for the admin debug container style is invalid CSS.<br />";
		}



		update_option('PO_admin_styles', $POAdminStyles);
		
		if (count($jsonResponse['alerts']) == 0) {
			$jsonResponse['alerts'][] = "All settings saved successfully!";
			$jsonResponse['success'] = 1;
		}

		print json_encode($jsonResponse);
		die();
	}
		
	function reset_post_settings() {
		global $wpdb;
		if ( !current_user_can( 'activate_plugins' ) || !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}

		$jsonResponse = array('success'=>0, 'alerts'=>array());
		if (is_numeric($this->postedData['postID'])) {
			if ($wpdb->get_var($wpdb->prepare("SELECT count(*) FROM ".$wpdb->prefix."po_plugins WHERE post_id=%d", $this->postedData['postID'])) > 0) {
				$deletePluginQuery = "DELETE FROM ".$wpdb->prefix."po_plugins WHERE post_id = %d";
				if ($wpdb->query($wpdb->prepare($deletePluginQuery, sanitize_text_field($this->postedData['postID']))) !== false) {
					$jsonResponse['success'] = 1;
				}
			} else {
				$jsonResponse['alerts'][] = "There were no settings found in the database.";
			}
		}
		print json_encode($jsonResponse);
		die();
	}
	
	function save_gen_settings() {
		if ( !current_user_can( 'activate_plugins' ) || !$this->PO->verify_nonce($this->postedData['PO_nonce'])) {
			print "You dont have permissions to access this page.";
			die();
		}
		$jsonResponse = array('success'=>0, 'alerts'=>array());
		
		$result = "";
		
		##Fuzzy URL matching
		if ($this->postedData['PO_fuzzy_url_matching'] == "true") {
			update_option("PO_fuzzy_url_matching", 1);
			$jsonResponse['alerts'][] = "Fuzzy URL matching is enabled.<br />";
		} else {
			update_option("PO_fuzzy_url_matching", 0);
			$jsonResponse['alerts'][] = "Fuzzy URL matching is disabled.<br />";
		}

		##Ignore protocol
		if ($this->postedData['PO_ignore_protocol'] == "true") {
			update_option("PO_ignore_protocol", 1);
			$jsonResponse['alerts'][] = "URL Protocol will be ignored.  http:// will be treated the same as https://<br />";
		} else {
			update_option("PO_ignore_protocol", 0);
			$jsonResponse['alerts'][] = "URL Protocol will NOT be ignored.  http:// will NOT be treated the same as https://<br />";
		}
		
		
		##Ignore arguments
		if ($this->postedData['PO_ignore_arguments'] == "true") {
			update_option("PO_ignore_arguments", 1);
			$jsonResponse['alerts'][] = "URL Arguments will be ignored.  " . home_url() . "?test=test will be treated the same as " . home_url() . "<br />";
		} else {
			update_option("PO_ignore_arguments", 0);
			$jsonResponse['alerts'][] = "URL Arguments will NOT be ignored.  " . home_url() . "?test=test will NOT be treated the same as " . home_url() . "<br />";
		}

		##Network admin access
		if ($this->postedData['PO_order_access_net_admin'] == "true") {
			update_option('PO_order_access_net_admin', 1);
			$jsonResponse['alerts'][] = "Only network admins will be able to change the plugin load order.<br />";
		} else {
			update_option('PO_order_access_net_admin', 0);
			$jsonResponse['alerts'][] = "Any admin will be able to change the plugin load order.<br />";
		}

		##Auto trailing slash
		if ($this->postedData['PO_auto_trailing_slash'] == "true") {
			update_option("PO_auto_trailing_slash", 1);
			$jsonResponse['alerts'][] = "Trailing slashes will automatically be removed or added based on your premalink structure.<br />";
		} else {
			update_option("PO_auto_trailing_slash", 0);
			$jsonResponse['alerts'][] = "Trailing slashes will NOT automatically be removed or added.<br />";
		}


		##Custom post type support
		if (isset($this->postedData['PO_cutom_post_type']) && is_array($this->postedData['PO_cutom_post_type'])) {
			$submittedPostTypes = $this->PO->sanitize_post_array($this->postedData['PO_cutom_post_type']);
		} else {
			$submittedPostTypes = array();
		}
		
		update_option("PO_custom_post_type_support", $submittedPostTypes);
		if (sizeof(array_diff(get_option("PO_custom_post_type_support"), $submittedPostTypes)) == 0) {
			$jsonResponse['alerts'][] = "Post types saved.";
		} else {
			$jsonResponse['alerts'][] = "Saving post types failed!";
		}

		##Role support
		if (isset($this->postedData['PO_supported_roles']) && is_array($this->postedData['PO_supported_roles'])) {
			$submittedRoles = $this->PO->sanitize_post_array($this->postedData['PO_supported_roles']);
		} else {
			$submittedRoles = array();
		}

		update_option("PO_enabled_roles", $submittedRoles);
		if (sizeof(array_diff(get_option("PO_enabled_roles"), $submittedRoles)) == 0) {
			$jsonResponse['alerts'][] = "Enabled roles saved.";
		} else {
			$jsonResponse['alerts'][] = "Saving enabled roles failed!";
		}

		##Debug Roles
		if (isset($this->postedData['PO_debug_roles']) && is_array($this->postedData['PO_debug_roles'])) {
			$submittedDebugRoles = $this->PO->sanitize_post_array($this->postedData['PO_debug_roles']);
		} else {
			$submittedDebugRoles = array();
		}

		update_option("PO_debug_roles", $submittedDebugRoles);
		if (sizeof(array_diff(get_option("PO_debug_roles"), $submittedDebugRoles)) == 0) {
			$jsonResponse['alerts'][] = "Debug roles saved.";
		} else {
			$jsonResponse['alerts'][] = "Saving debug roles failed!";
		}
		
		##Selective Plugin Loading
		if ($this->postedData['PO_disable_plugins_frontend'] == "true") {
			update_option("PO_disable_plugins_frontend", 1);
			$jsonResponse['alerts'][] = "Selective Plugin Loading is enabled.";
		} else {
			update_option("PO_disable_plugins_frontend", 0);
			$jsonResponse['alerts'][] = "Selective Plugin Loading is disabled.";
		}

		##Selective Mobile Plugin Loading
		if ($this->postedData['PO_disable_plugins_mobile'] == "true") {
			update_option("PO_disable_plugins_mobile", 1);
			$jsonResponse['alerts'][] = "Selective Mobile Plugin loading is enabled.";
		} else {
			update_option("PO_disable_plugins_mobile", 0);
			$jsonResponse['alerts'][] = "Selective Mobile Plugin loading is disabled.";
		}

		##Selective Admin Plugin Loading
		if ($this->postedData['PO_disable_plugins_admin'] == "true") {
			update_option("PO_disable_plugins_admin", 1);
			$jsonResponse['alerts'][] = "Selective Admin Plugin Loading is enabled.";
		} else {
			update_option("PO_disable_plugins_admin", 0);
			$jsonResponse['alerts'][] = "Selective Admin Plugin Loading is disabled.";
		}

		##Disable By Role
		if ($this->postedData['PO_disable_plugins_by_role'] == "true") {
			update_option("PO_disable_plugins_by_role", 1);
			$jsonResponse['alerts'][] = "Disable Plugins By Role is enabled.";
		} else {
			update_option("PO_disable_plugins_by_role", 0);
			$jsonResponse['alerts'][] = "Disable Plugins By Role is disabled.";
		}

		##Disable By Role
		if ($this->postedData['PO_display_debug_msg'] == "true") {
			update_option("PO_display_debug_msg", 1);
			$jsonResponse['alerts'][] = "Debug messages will be dispayed.";
		} else {
			update_option("PO_display_debug_msg", 0);
			$jsonResponse['alerts'][] = "Debug messages will NOT be dispayed.";
		}
		
		$jsonResponse['success'] = 1;
		print json_encode($jsonResponse);
		die();
	}

	function get_plugin_group_container() {
		$groups = get_posts(array('post_type'=>'plugin_group', 'posts_per_page'=>-1));
		$assignedGroups = "";
		$postedPluginPath = sanitize_text_field($this->postedData['PO_plugin_path']);
			
		$jsonResponse = array('success'=>0, 'alerts'=>array(), 'assigned_groups'=>'');
		foreach($groups as $group) {
			$members = $this->PO->get_group_members($group->ID);
			$members = stripslashes_deep($members);
			if (is_array($members) && array_search($postedPluginPath, $members) !== FALSE) {
				$jsonResponse['assigned_groups'] .= '<a href="'.get_admin_url().'plugins.php?PO_group_view='.$group->ID.'">'.$group->post_title.'</a><br /><hr>';
			}
		}
		$jsonResponse['success'] = 1;
		print json_encode($jsonResponse);
		die();
	}

	function get_group_list() {
		$groups = get_posts(array('post_type'=>'plugin_group', 'posts_per_page'=>-1));
		$groupNames = array();
		foreach($groups as $group) {
			$groupNames[] = array($group->ID, $group->post_title);
		}
		print json_encode($groupNames);
		die();
	}
	
	function perform_plugin_search() {
		global $wpdb;
		$returnArray = array();
		$availableRoles = array('_');
		if (isset($this->postedData['PO_searchable_roles']) && is_array($this->postedData['PO_searchable_roles']) && count($this->postedData['PO_searchable_roles']) > 0) {
			$availableRoles = $this->PO->sanitize_post_array($this->postedData['PO_searchable_roles']);
		} else if (get_option("PO_disable_plugins_by_role") == '1') {
			$availableRoles = get_option("PO_enabled_roles");
			
			$availableRoles = array_merge(array('_', '-'), $availableRoles);
		}

		$groups = get_posts(array('post_type'=>'plugin_group', 'posts_per_page'=>-1));
		$assignedGroups = array();
		$postedPluginPath = sanitize_text_field($this->postedData['PO_plugin_path']);
		
		foreach($groups as $group) {
			$members = $this->PO->get_group_members($group->ID);
			$members = stripslashes_deep($members);
			if (is_array($members) && array_search($postedPluginPath, $members) !== FALSE) {
				$assignedGroups[] = '"'.$group->ID.'"';
			}
		}

		$groupSearch = '';
		if (count($assignedGroups) > 0) {
			$groupSearch = " OR disabled_groups REGEXP '(".implode('|', $assignedGroups).")' OR disabled_mobile_groups REGEXP '(".implode('|', $assignedGroups).")'";
		}
		
		$sql = $this->PO->prepare_in("SELECT COUNT(*) FROM ".$wpdb->prefix."po_plugins WHERE post_type='global_plugin_lists' AND post_id=0 AND user_role IN ([R_IN]) AND (disabled_plugins LIKE [ESC_LIKE] OR disabled_mobile_plugins LIKE [ESC_LIKE]".$groupSearch.")", $availableRoles, '[R_IN]');
		$sql = preg_replace('/\[ESC_LIKE\]/', "'%".$wpdb->esc_like($postedPluginPath)."%'", $sql);
		$pluginSearchResult = $wpdb->get_var($sql);

		if ($pluginSearchResult > 0) {
			$returnArray[] = array('name'=>'Global Plugins', 'url'=>get_admin_url().'admin.php?page=PO_global_plugins');
		}
		
		$sql = $this->PO->prepare_in("SELECT COUNT(*) FROM ".$wpdb->prefix."po_plugins WHERE post_type='search_plugin_lists' AND post_id=0 AND user_role IN ([R_IN]) AND (disabled_plugins LIKE [ESC_LIKE] OR disabled_mobile_plugins LIKE [ESC_LIKE]".$groupSearch.")", $availableRoles, '[R_IN]');
		$sql = preg_replace('/\[ESC_LIKE\]/', "'%".$wpdb->esc_like($postedPluginPath)."%'", $sql);
		$pluginSearchResult = $wpdb->get_var($sql);

		if ($pluginSearchResult > 0) {
			$returnArray[] = array('name'=>'Search Plugins', 'url'=>get_admin_url().'admin.php?page=PO_search_plugins');
		}

		$sql = $this->PO->prepare_in("SELECT post_id FROM ".$wpdb->prefix."po_plugins WHERE post_id!=0 AND user_role IN ([R_IN]) AND (disabled_plugins LIKE [ESC_LIKE] OR disabled_mobile_plugins LIKE [ESC_LIKE]".$groupSearch.") GROUP BY post_id", $availableRoles, '[R_IN]');
		$sql = preg_replace('/\[ESC_LIKE\]/', "'%".$wpdb->esc_like($postedPluginPath)."%'", $sql);
		$pluginSearchResult = $wpdb->get_var($sql);
		$pluginSearchResult = $wpdb->get_results($sql, ARRAY_A);

		if ($pluginSearchResult  !== false) {
			foreach($pluginSearchResult as $postDetail) {
				$returnArray[] = array('name'=>get_the_title($postDetail['post_id']), 'url'=>get_edit_post_link($postDetail['post_id']));
			}
		}

		print json_encode($returnArray);
		die();
	}
}
?>