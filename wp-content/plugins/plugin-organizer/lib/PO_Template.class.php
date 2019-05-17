<?php
class PO_Template {
	var $PO;
	function __construct($PO) {
		$this->PO = $PO;
	}
	
	function common_js() {
		require_once($this->PO->absPath . "/tpl/common_js.php");
	}
	
	function search_plugins_js() {
		require_once($this->PO->absPath . "/tpl/search_plugins_js.php");
	}

	function pt_plugins_js() {
		require_once($this->PO->absPath . "/tpl/pt_plugins_js.php");
	}
	
	function group_and_order_plugins_js() {
		wp_enqueue_style('PO-dash-icons');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
		require_once($this->PO->absPath . "/tpl/group_and_order_js.php");
	}
	
	function global_plugins_js() {
		require_once($this->PO->absPath . "/tpl/global_plugins_js.php");
	}

	function settings_page_js() {
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-tabs');
		if (get_option('PO_disable_admin_notices') == 1) {
			add_action('admin_notices', array($this->PO, 'admin_notices'));
		}
		require_once($this->PO->absPath . "/tpl/settings_page_js.php");
	}

	function admin_css() {
		wp_enqueue_style('PO-admin');
		if (get_option('PO_disable_admin_notices') != 1) {
			add_action('admin_notices', array($this->PO, 'admin_notices'));
		}
		require_once($this->PO->absPath . "/tpl/admin_css.php");
	}

	function settings_page() {
		if ( current_user_can( 'activate_plugins' ) ) {
			$installedPlugins = get_plugins();
			require_once($this->PO->absPath . "/tpl/settings.php");
		} else {
			wp_die("You dont have permissions to access this page.");
		}
	}

	function search_plugins_page() {
		global $wpdb;
		if ( current_user_can( 'activate_plugins' ) ) {
			$plugins = $this->PO->reorder_plugins(get_plugins());
			
			$availableRoles = $this->PO->get_available_roles();
			$pluginLists = array();
			
			foreach ($availableRoles as $roleName=>$roleName) {
				$pluginLists[$roleName] = array();
				$sql = "SELECT disabled_plugins, enabled_plugins, disabled_mobile_plugins, enabled_mobile_plugins, disabled_groups, enabled_groups, disabled_mobile_groups, enabled_mobile_groups FROM ".$wpdb->prefix."po_plugins WHERE post_type='search_plugin_lists' AND post_id=0 AND user_role=%s";
				$storedPluginLists = $wpdb->get_row($wpdb->prepare($sql, $roleName), ARRAY_A);
				
				$pluginLists[$roleName]['enabled_plugin_list'] = (is_array(@unserialize($storedPluginLists['enabled_plugins'])))? @unserialize($storedPluginLists['enabled_plugins']):array();
				$pluginLists[$roleName]['disabled_plugin_list'] = (is_array(@unserialize($storedPluginLists['disabled_plugins'])))? @unserialize($storedPluginLists['disabled_plugins']):array();
				$pluginLists[$roleName]['enabled_mobile_plugin_list'] = (is_array(@unserialize($storedPluginLists['enabled_mobile_plugins'])))? @unserialize($storedPluginLists['enabled_mobile_plugins']):array();
				$pluginLists[$roleName]['disabled_mobile_plugin_list'] = (is_array(@unserialize($storedPluginLists['disabled_mobile_plugins'])))? @unserialize($storedPluginLists['disabled_mobile_plugins']):array();
				$pluginLists[$roleName]['enabled_group_list'] = (is_array(@unserialize($storedPluginLists['enabled_groups'])))? @unserialize($storedPluginLists['enabled_groups']):array();
				$pluginLists[$roleName]['disabled_group_list'] = (is_array(@unserialize($storedPluginLists['disabled_groups'])))? @unserialize($storedPluginLists['disabled_groups']):array();
				$pluginLists[$roleName]['enabled_mobile_group_list'] = (is_array(@unserialize($storedPluginLists['enabled_mobile_groups'])))? @unserialize($storedPluginLists['enabled_mobile_groups']):array();
				$pluginLists[$roleName]['disabled_mobile_group_list'] = (is_array(@unserialize($storedPluginLists['disabled_mobile_groups'])))? @unserialize($storedPluginLists['disabled_mobile_groups']):array();
			}

			$activePlugins = get_option("active_plugins");
			$activeSitewidePlugins = array_keys((array) get_site_option('active_sitewide_plugins', array()));
			$groupList = get_posts(array('posts_per_page'=>-1, 'post_type'=>'plugin_group'));
			
			$sql = "SELECT disabled_plugins, disabled_mobile_plugins, disabled_groups, disabled_mobile_groups FROM ".$wpdb->prefix."po_plugins WHERE post_type='global_plugin_lists' AND post_id=0";
			$storedPluginLists = $wpdb->get_row($sql, ARRAY_A);
			
			$globalPlugins = (is_array(@unserialize($storedPluginLists['disabled_plugins'])))? @unserialize($storedPluginLists['disabled_plugins']):array();
			$globalMobilePlugins = (is_array(@unserialize($storedPluginLists['disabled_mobile_plugins'])))? @unserialize($storedPluginLists['disabled_mobile_plugins']):array();
			$globalGroups = (is_array(@unserialize($storedPluginLists['disabled_groups'])))? @unserialize($storedPluginLists['disabled_groups']):array();
			$globalMobileGroups = (is_array(@unserialize($storedPluginLists['disabled_mobile_groups'])))? @unserialize($storedPluginLists['disabled_mobile_groups']):array();
			
			require_once($this->PO->absPath . "/tpl/searchPlugins.php");
		}
	}

	function pt_plugins_page() {
		global $wpdb;
		if ( current_user_can( 'activate_plugins' ) ) {
			$plugins = $this->PO->reorder_plugins(get_plugins());
			
			$availableRoles = $this->PO->get_available_roles();
			$pluginLists = array();
			
			foreach ($availableRoles as $roleID=>$roleName) {
				$pluginLists[$roleID] = array(
					'enabled_plugin_list'=>array(),
					'disabled_plugin_list'=>array(),
					'enabled_mobile_plugin_list'=>array(),
					'disabled_mobile_plugin_list'=>array(),
					'enabled_group_list'=>array(),
					'disabled_group_list'=>array(),
					'enabled_mobile_group_list'=>array(),
					'disabled_mobile_group_list'=>array()
				);
			}
			
			$activePlugins = get_option("active_plugins");
			$activeSitewidePlugins = array_keys((array) get_site_option('active_sitewide_plugins', array()));
			$groupList = get_posts(array('posts_per_page'=>-1, 'post_type'=>'plugin_group'));
			
			
			$sql = "SELECT disabled_plugins, disabled_mobile_plugins, disabled_groups, disabled_mobile_groups FROM ".$wpdb->prefix."po_plugins WHERE post_type='global_plugin_lists' AND post_id=0";
			$storedPluginLists = $wpdb->get_row($sql, ARRAY_A);
			
			$globalPlugins = (is_array(@unserialize($storedPluginLists['disabled_plugins'])))? @unserialize($storedPluginLists['disabled_plugins']):array();
			$globalMobilePlugins = (is_array(@unserialize($storedPluginLists['disabled_mobile_plugins'])))? @unserialize($storedPluginLists['disabled_mobile_plugins']):array();
			$globalGroups = (is_array(@unserialize($storedPluginLists['disabled_groups'])))? @unserialize($storedPluginLists['disabled_groups']):array();
			$globalMobileGroups = (is_array(@unserialize($storedPluginLists['disabled_mobile_groups'])))? @unserialize($storedPluginLists['disabled_mobile_groups']):array();
			

			$ptOverride = 0;
			require_once($this->PO->absPath . "/tpl/ptPlugins.php");
		}
	}
	
	function group_and_order_plugins_page() {
		require_once($this->PO->absPath . "/tpl/groupAndOrder.php");
	}
	
	function global_plugins_page() {
		global $wpdb;
		if ( current_user_can( 'activate_plugins' ) ) {
			$plugins = $this->PO->reorder_plugins(get_plugins());
			
			$sql = "SELECT disabled_plugins, disabled_mobile_plugins, disabled_groups, disabled_mobile_groups FROM ".$wpdb->prefix."po_plugins WHERE post_type='global_plugin_lists' AND post_id=0";
			$storedPluginLists = $wpdb->get_row($sql, ARRAY_A);
			
			$pluginLists = array('_'=>array(
				'enabled_plugin_list'=>array(),
				'disabled_plugin_list'=>(is_array(@unserialize($storedPluginLists['disabled_plugins'])))? @unserialize($storedPluginLists['disabled_plugins']):array(),
				'enabled_mobile_plugin_list'=>array(),
				'disabled_mobile_plugin_list'=>(is_array(@unserialize($storedPluginLists['disabled_mobile_plugins'])))? @unserialize($storedPluginLists['disabled_mobile_plugins']):array(),
				'enabled_group_list'=>array(),
				'disabled_group_list'=>(is_array(@unserialize($storedPluginLists['disabled_groups'])))? @unserialize($storedPluginLists['disabled_groups']):array(),
				'enabled_mobile_group_list'=>array(),
				'disabled_mobile_group_list'=>(is_array(@unserialize($storedPluginLists['disabled_mobile_groups'])))? @unserialize($storedPluginLists['disabled_mobile_groups']):array()
			));

			$activePlugins = get_option("active_plugins");
			$activeSitewidePlugins = array_keys((array) get_site_option('active_sitewide_plugins', array()));
			$groupList = get_posts(array('posts_per_page'=>-1, 'post_type'=>'plugin_group'));
			$globalPlugins = array();
			$globalMobilePlugins = array();
			$globalGroups = array();
			$globalMobileGroups = array();
			
			require_once($this->PO->absPath . "/tpl/globalPlugins.php");
		} else {
			wp_die("You dont have permissions to access this page.");
		}
	}

	function get_post_meta_box($post) {
		global $wpdb;
		$errMsg = "";
		$this->admin_css();
		$this->common_js();
		if ($post->ID != "" && is_numeric($post->ID)) {
			$filterName = $post->post_title;
			$postSettingsQuery = "SELECT children, pt_override, secure, post_priority FROM ".$wpdb->prefix."po_plugins WHERE post_id = %d";
			$postSettings = $wpdb->get_row($wpdb->prepare($postSettingsQuery, $post->ID), ARRAY_A);
			
			$affectChildren = $postSettings['children'];
			$ptOverride = $postSettings['pt_override'];
			
			$availableRoles = $this->PO->get_available_roles();
			$pluginLists = array();
			
			foreach ($availableRoles as $roleID=>$roleName) {
				$pluginLists[$roleID] = array();
				$sql = "SELECT disabled_plugins, enabled_plugins, disabled_mobile_plugins, enabled_mobile_plugins, disabled_groups, enabled_groups, disabled_mobile_groups, enabled_mobile_groups FROM ".$wpdb->prefix."po_plugins WHERE post_id=%d AND user_role=%s";
				$storedPluginLists = $wpdb->get_row($wpdb->prepare($sql, $post->ID, $roleID), ARRAY_A);
				
				$pluginLists[$roleID]['enabled_plugin_list'] = (is_array(@unserialize($storedPluginLists['enabled_plugins'])))? @unserialize($storedPluginLists['enabled_plugins']):array();
				$pluginLists[$roleID]['disabled_plugin_list'] = (is_array(@unserialize($storedPluginLists['disabled_plugins'])))? @unserialize($storedPluginLists['disabled_plugins']):array();
				$pluginLists[$roleID]['enabled_mobile_plugin_list'] = (is_array(@unserialize($storedPluginLists['enabled_mobile_plugins'])))? @unserialize($storedPluginLists['enabled_mobile_plugins']):array();
				$pluginLists[$roleID]['disabled_mobile_plugin_list'] = (is_array(@unserialize($storedPluginLists['disabled_mobile_plugins'])))? @unserialize($storedPluginLists['disabled_mobile_plugins']):array();
				$pluginLists[$roleID]['enabled_group_list'] = (is_array(@unserialize($storedPluginLists['enabled_groups'])))? @unserialize($storedPluginLists['enabled_groups']):array();
				$pluginLists[$roleID]['disabled_group_list'] = (is_array(@unserialize($storedPluginLists['disabled_groups'])))? @unserialize($storedPluginLists['disabled_groups']):array();
				$pluginLists[$roleID]['enabled_mobile_group_list'] = (is_array(@unserialize($storedPluginLists['enabled_mobile_groups'])))? @unserialize($storedPluginLists['enabled_mobile_groups']):array();
				$pluginLists[$roleID]['disabled_mobile_group_list'] = (is_array(@unserialize($storedPluginLists['disabled_mobile_groups'])))? @unserialize($storedPluginLists['disabled_mobile_groups']):array();
			}

			$permalinksQuery = "SELECT pl_id, permalink, secure FROM ".$wpdb->prefix."po_plugins WHERE post_id = %d GROUP BY permalink, secure";
			$permalinksResult = $wpdb->get_results($wpdb->prepare($permalinksQuery, $post->ID), ARRAY_A);
			
			$permalinkFilters = array();
			$duplicateList = array();
			foreach($permalinksResult as $permalink) {
				$permalinkFilters[] = array('pl_id'=>$permalink['pl_id'], 'permalink'=>$permalink['permalink'], 'secure'=>$permalink['secure']);

				$duplicates = $this->PO->find_duplicate_permalinks($post->ID, $permalink['permalink'], $permalink['secure']);
				if (sizeOf($duplicates) > 0) {
					foreach($duplicates as $dup) {
						if (!in_array($dup, $duplicateList)) {
							$duplicateList[] = $dup;
							$errMsg .= 'There is a '.get_post_type($dup).' with the same permalink.  <a href="' . get_admin_url() . 'post.php?post=' . $dup . '&action=edit">Edit Duplicate</a><br />';
						}
					}
				}
			}

			if (sizeof($permalinkFilters) == 0) {
				$permalinkFilters = array(array('pl_id'=>null, 'permalink'=>'', 'secure'=>0));
			}
			
			$postPriority = $postSettings['post_priority'];
			
			$postType = get_post_type($post->ID);
			$postTypeObject = get_post_type_object($postType);
			if (isset($postTypeObject->labels->name)) {
				$postTypeName = $postTypeObject->labels->singular_name;
			} else {
				$postTypeName = 'Post';
			}
		} else {
			$filterName = "";
			$affectChildren = 0;
			
			$availableRoles = $this->PO->get_available_roles();
			$pluginLists = array();
			
			foreach ($availableRoles as $roleID=>$roleName) {
				$pluginLists[$roleID] = array(
					'enabled_plugin_list'=>array(),
					'disabled_plugin_list'=>array(),
					'enabled_mobile_plugin_list'=>array(),
					'disabled_mobile_plugin_list'=>array(),
					'enabled_group_list'=>array(),
					'disabled_group_list'=>array(),
					'enabled_mobile_group_list'=>array(),
					'disabled_mobile_group_list'=>array()
				);
			}


			$permalinkFilters = array(array('pl_id'=>null, 'permalink'=>'', 'secure'=>0));
			$postPriority=0;
			$postTypeName = 'Post';
		}
		
		if ($post->post_type != 'plugin_filter') {
			$fuzzyPermalink = $permalinkFilters[0]['permalink'];
		
			$secure = $permalinkFilters[0]['secure'];

			foreach($availableRoles as $roleID=>$roleName) {
				#Find and apply parent settings
				if ($fuzzyPermalink != '' && get_option("PO_fuzzy_url_matching") == "1" && sizeof($pluginLists[$roleID]['disabled_plugin_list']) == 0 && sizeof($pluginLists[$roleID]['enabled_plugin_list']) == 0 && sizeof($pluginLists[$roleID]['disabled_group_list']) == 0 && sizeof($pluginLists[$roleID]['enabled_group_list']) == 0) {
					$fuzzyPluginList = $this->PO->find_parent_plugins($post->ID, $fuzzyPermalink, 0, $secure, $roleID);
					$pluginLists[$roleID]['disabled_plugin_list'] = $fuzzyPluginList['plugins']['disabled_plugins'];
					$pluginLists[$roleID]['enabled_plugin_list'] = $fuzzyPluginList['plugins']['enabled_plugins'];
					$pluginLists[$roleID]['disabled_group_list'] = $fuzzyPluginList['plugins']['disabled_groups'];
					$pluginLists[$roleID]['enabled_group_list'] = $fuzzyPluginList['plugins']['enabled_groups'];
					if ($fuzzyPluginList['post_id'] > 0) {
						$errMsg .= 'There is a parent affecting the standard plugins for the '.$roleName.' role on this '. $postTypeName . '.  To edit it click <a href="' . get_admin_url() . 'post.php?post=' . $fuzzyPluginList['post_id'] . '&action=edit">here</a>.<br />';
					}
				}



				#Find and apply parent settings to mobile plugins
				if ($fuzzyPermalink != '' && get_option('PO_disable_plugins_mobile') == '1' && get_option("PO_fuzzy_url_matching") == "1" && sizeof($pluginLists[$roleID]['disabled_mobile_plugin_list']) == 0 && sizeof($pluginLists[$roleID]['enabled_mobile_plugin_list']) == 0 && sizeof($pluginLists[$roleID]['disabled_mobile_group_list']) == 0 && sizeof($pluginLists[$roleID]['enabled_mobile_group_list']) == 0) {
					$fuzzyPluginList = $this->PO->find_parent_plugins($post->ID, $fuzzyPermalink, 1, $secure, $roleID);
					$pluginLists[$roleID]['disabled_mobile_plugin_list'] = $fuzzyPluginList['plugins']['disabled_plugins'];
					$pluginLists[$roleID]['enabled_mobile_plugin_list'] = $fuzzyPluginList['plugins']['enabled_plugins'];
					$pluginLists[$roleID]['disabled_mobile_group_list'] = $fuzzyPluginList['plugins']['disabled_groups'];
					$pluginLists[$roleID]['enabled_mobile_group_list'] = $fuzzyPluginList['plugins']['enabled_groups'];
					if ($fuzzyPluginList['post_id'] > 0) {
						$errMsg .= 'There is a parent affecting the mobile plugins for the '.$roleName.' role on this '. $postTypeName . '.  To edit it click <a href="' . get_admin_url() . 'post.php?post=' . $fuzzyPluginList['post_id'] . '&action=edit">here</a>.<br />';
					}
				}
			}
		}




		
		$sql = "SELECT disabled_plugins, disabled_mobile_plugins, disabled_groups, disabled_mobile_groups FROM ".$wpdb->prefix."po_plugins WHERE post_type='global_plugin_lists' AND post_id=0";
		$storedPluginLists = $wpdb->get_row($sql, ARRAY_A);
		
		$globalPlugins = (is_array(@unserialize($storedPluginLists['disabled_plugins'])))? @unserialize($storedPluginLists['disabled_plugins']):array();
		$globalMobilePlugins = (is_array(@unserialize($storedPluginLists['disabled_mobile_plugins'])))? @unserialize($storedPluginLists['disabled_mobile_plugins']):array();
		$globalGroups = (is_array(@unserialize($storedPluginLists['disabled_groups'])))? @unserialize($storedPluginLists['disabled_groups']):array();
		$globalMobileGroups = (is_array(@unserialize($storedPluginLists['disabled_mobile_groups'])))? @unserialize($storedPluginLists['disabled_mobile_groups']):array();
		
		
		$plugins = $this->PO->reorder_plugins(get_plugins());
		
		$activePlugins = get_option("active_plugins");
		$activeSitewidePlugins = array_keys((array) get_site_option('active_sitewide_plugins', array()));
		
		$groupList = get_posts(array('posts_per_page'=>-1, 'post_type'=>'plugin_group'));
		if (get_option("PO_disable_plugins_frontend") != 1) {
			$errMsg .= 'You currently have Selective Plugin Loading disabled.  None of the changes you make here will have any affect on what plugins are loaded until you enable it.  You can enable it by going to the <a href="' . get_admin_url() . 'admin.php?page=Plugin_Organizer">settings page</a> and clicking enable under Selective Plugin Loading.';
		}
		
		require_once($this->PO->absPath . "/tpl/postMetaBox.php");
	}
}
?>