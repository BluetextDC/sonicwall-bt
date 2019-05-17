<?php
require_once('PO_Template.class.php');
require_once('PO_Ajax.class.php');

class PluginOrganizer {
	var $pluginPageActions = "1";
	var $regex, $absPath, $urlPath, $nonce, $ajax, $tpl, $pluginDirPath, $pluginBase;
	private $postedData;
	
	function __construct($mainFile) {
		$this->pluginBase = plugin_basename($mainFile);
		$this->pluginDirPath = $this->get_plugin_dir();
		$this->absPath = $this->pluginDirPath . "/" . plugin_basename(dirname($mainFile));
		$this->urlPath = plugins_url("", $mainFile);
		$this->regex = array(
			"permalink" => "/^((https?):((\/\/)|(\\\\))+[\w\d:#@%\/;$()~_?\+-=\\\.&]*)$/",
			"PO_group_name" => "/^[A-Za-z0-9_\-]+$/",
			"default" => "/^(.|\\n)*$/"
		);
		$this->ajax = new PO_Ajax($this);
		$this->tpl = new PO_Template($this);
		$this->addHooks($mainFile);
		if (is_admin()) {
			$this->postedData = $_POST;
		}
	}

	function get_plugin_dir() {
		return preg_replace('/\\' . DIRECTORY_SEPARATOR . 'plugin-organizer\\' . DIRECTORY_SEPARATOR . 'lib$/', '', dirname(__FILE__));
	}
	
	function addHooks($mainFile) {
		$this->ajax = new PO_Ajax($this);

		register_activation_hook($mainFile,array($this, 'activate'));
		register_deactivation_hook($mainFile, array($this, 'deactivate'));
		
		add_action( 'activated_plugin', array($this, 'activated_plugin' ), 10, 2 );
		add_action( 'deactivated_plugin', array($this, 'deactivated_plugin' ), 10, 2 );
		add_action('admin_menu', array($this, 'check_version'), 9);

		if (is_admin()) {
			add_action('admin_notices', array($this, 'display_admin_debug'));
		} else {
			add_action('get_footer', array($this, 'display_footer_debug'), 100);
		}
		
		if (get_option('PO_disable_compat_notices') != '1') {
			add_action('network_admin_notices', array($this, 'compatibility_notices'), 1);
			add_action('admin_notices', array($this, 'compatibility_notices'), 1);
		}

		add_filter("plugin_row_meta", array($this, 'get_custom_meta_links'), 10, 2);

		if (!is_network_admin()) {
			add_action('admin_init', array($this, 'register_admin_style'));
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_style'));
			add_action('init', array($this, 'init'));
			add_filter('views_plugins', array($this, 'add_group_views'));
			add_action('admin_menu', array($this, 'admin_menu'), 9);
			
			add_action('all_plugins', array($this, 'get_requested_group'));
			
			add_action('admin_menu', array($this, 'disable_plugin_box'));
			add_action('save_post', array($this, 'save_post_meta_box'));
			
			add_action('delete_post', array($this, 'delete_plugin_lists'));
			
			add_action('manage_plugins_columns', array($this, 'get_column_headers'));
			add_filter('manage_plugins_custom_column', array($this, 'set_custom_column_values'), 10, 3);
			add_action('manage_edit-plugin_filter_columns', array($this, 'get_pf_column_headers'));
			add_filter('manage_plugin_filter_posts_custom_column', array($this, 'set_pf_custom_column_values'), 10, 3);
			if (isset($_REQUEST['PO_group_view']) && is_numeric($_REQUEST['PO_group_view'])) {
				add_filter('gettext', array($this, 'change_page_title'), 10, 2);
			}
			add_filter('title_save_pre', array($this, 'change_plugin_filter_title'));
			add_filter('post_updated_messages', array($this, 'custom_updated_messages'));
			add_filter('transition_post_status', array($this, 'update_post_status'), 10, 3);
			
			add_filter("manage_edit-plugin_filter_sortable_columns", array($this, 'plugin_filter_sort'));
			add_action('admin_notices', array($this, 'add_ajax_notices'));

			##Login/Logout
			add_action('wp_login', array($this, 'user_logged_in'), 99, 2);
			add_action('wp_logout', array($this, 'user_logged_out'));
	
			##Ajax functions
			add_action('wp_ajax_PO_plugin_organizer', array($this->ajax, 'save_order'));
			add_action('wp_ajax_PO_create_new_group', array($this->ajax, 'create_group'));
			add_action('wp_ajax_PO_delete_group', array($this->ajax, 'delete_group'));
			add_action('wp_ajax_PO_remove_plugins_from_group', array($this->ajax, 'remove_plugins_from_group'));
			add_action('wp_ajax_PO_add_to_group', array($this->ajax, 'add_to_group'));
			add_action('wp_ajax_PO_edit_plugin_group_name', array($this->ajax, 'edit_plugin_group_name'));
			add_action('wp_ajax_PO_save_global_plugins', array($this->ajax, 'save_global_plugins'));
			add_action('wp_ajax_PO_save_search_plugins', array($this->ajax, 'save_search_plugins'));
			add_action('wp_ajax_PO_get_pt_id_list', array($this->ajax, 'get_pt_id_list'));
			add_action('wp_ajax_PO_save_pt_plugins', array($this->ajax, 'save_pt_plugins'));
			add_action('wp_ajax_PO_get_pt_plugins', array($this->ajax, 'get_pt_plugins'));
			add_action('wp_ajax_PO_reset_pt_settings', array($this->ajax, 'reset_pt_settings'));
			add_action('wp_ajax_PO_redo_permalinks', array($this->ajax, 'redo_permalinks'));
			add_action('wp_ajax_PO_manage_mu_plugin', array($this->ajax, 'manage_mu_plugin'));
			add_action('wp_ajax_PO_reset_to_default_order', array($this->ajax, 'reset_plugin_order'));
			add_action('wp_ajax_PO_submit_mobile_user_agents', array($this->ajax, 'save_mobile_user_agents'));
			add_action('wp_ajax_PO_disable_admin_notices', array($this->ajax, 'disable_admin_notices'));
			add_action('wp_ajax_PO_disable_compat_notices', array($this->ajax, 'disable_compat_notices'));
			add_action('wp_ajax_PO_disable_admin_warning', array($this->ajax, 'disable_admin_warning'));
			add_action('wp_ajax_PO_disable_debug_msg', array($this->ajax, 'disable_debug_msg'));
			add_action('wp_ajax_PO_submit_admin_css_settings', array($this->ajax, 'submit_admin_css_settings'));
			add_action('wp_ajax_PO_reset_post_settings', array($this->ajax, 'reset_post_settings'));
			add_action('wp_ajax_PO_submit_gen_settings', array($this->ajax, 'save_gen_settings'));
			add_action('wp_ajax_PO_get_plugin_group_container', array($this->ajax, 'get_plugin_group_container'));
			add_action('wp_ajax_PO_get_group_list', array($this->ajax, 'get_group_list'));
			add_action('wp_ajax_PO_perform_plugin_search', array($this->ajax, 'perform_plugin_search'));
		}
	}

	function get_custom_meta_links($meta, $file) {
		if ($this->pluginBase == $file) {
			$meta[] = '<a href="http://www.sterupdesign.com/dev/wordpress/plugins/plugin-organizer/documentation/" target="_blank">Documentation</a>';
			$meta[] = '<a href="http://www.sterupdesign.com/dev/wordpress/plugins/plugin-organizer/faq/" target="_blank">FAQ</a>';
		}
		return $meta;
	}

	function display_admin_debug($calledFromMeta=0) {
		if (get_option('PO_display_debug_msg') == 1) {
			global $pagenow, $PluginOrganizerMU;
			if ($calledFromMeta != 1 && in_array($pagenow, array('post.php', 'post-new.php'))) {
				add_action('PO_display_meta_debug', array($this, 'display_admin_debug'));
			}
			$debugRoles = get_option("PO_debug_roles");
			if (!is_array($debugRoles)) {
				$debugRoles = array('administrator');
			}
			
			$roles = array();
			if (is_user_logged_in()) {
				$user = wp_get_current_user();
				$roles[] = '-';
				foreach($user->roles as $role) {
					$roles[] = $role;
				}
			} else {
				$roles[] = '_';
			}
			if (array_diff($debugRoles, $roles) !== $debugRoles && get_class($PluginOrganizerMU) == 'PluginOrganizerMU' && sizeof($PluginOrganizerMU->debugMsg) > 0) {
				if ($calledFromMeta == 1) {
					?>
					<style type="text/css">
						.PO-debug-header {display:none;}
					</style>
					<?php
				}
				?>
				<div class="notice notice-warning PO-debug-msg-container <?php print ($calledFromMeta != 1)? 'PO-debug-header':''; ?>" style="<?php print (isset($POAdminStyles['admin_debug_style']))? $POAdminStyles['admin_debug_style'] : 'padding: 20px;'; ?>">
					<div style="font-weight: bold;margin-bottom:10px;">Plugin Organizer Debug Messages</div>
					<hr>
					<?php
					foreach($PluginOrganizerMU->debugMsg as $debugMsg) {
						print '<div>'.$debugMsg.'</div>';
					}
					
					if (current_user_can('activate_plugins')) {
						?>
						<a href="#" class="PO-disable-debug-msg">Disable Debug Messages</a>
						<script type="text/javascript" language="javascript">
							jQuery('.PO-disable-debug-msg').click(function() {
								jQuery.post(encodeURI(ajaxurl + '?action=PO_disable_debug_msg'), {PO_nonce: '<?php print $this->nonce; ?>'}, function (result) {
									jQuery('.PO-debug-msg-container').remove();
								});
								return false;
							});
						</script>
						<?php
					}
					?>
				</div>
				<?php
			}
		}
	}
	
	function display_footer_debug() {
		if (get_option('PO_display_debug_msg') == 1) {
			global $PluginOrganizerMU;
			$debugRoles = get_option("PO_debug_roles");
			if (!is_array($debugRoles)) {
				$debugRoles = array('administrator');
			}
			
			$roles = array();
			if (is_user_logged_in()) {
				$user = wp_get_current_user();
				$roles[] = '-';
				foreach($user->roles as $role) {
					$roles[] = $role;
				}
			} else {
				$roles[] = '_';
			}
			if (array_diff($debugRoles, $roles) !== $debugRoles && get_class($PluginOrganizerMU) == 'PluginOrganizerMU' && sizeof($PluginOrganizerMU->debugMsg) > 0) {
				$POAdminStyles = get_option('PO_admin_styles');
				?>
				<div class="PO-debug-msg-container" style="<? print (isset($POAdminStyles['front_debug_style']))? $POAdminStyles['front_debug_style'] : 'position: relative;z-index: 99999;background: #fff;width: 100%;border: 4px solid #000;padding: 10px;'; ?>">
					<div style="font-weight: bold;margin-bottom:10px;">Plugin Organizer Debug Messages</div>
					<hr>
					<?php
					foreach($PluginOrganizerMU->debugMsg as $debugMsg) {
						print '<div>'.$debugMsg.'</div>';
					}
					
					if (current_user_can('activate_plugins')) {
						?>
						<a href="#" class="PO-disable-debug-msg">Disable Debug Messages</a>
						<script type="text/javascript" language="javascript">
							jQuery('.PO-disable-debug-msg').click(function() {
								jQuery.post(encodeURI('<?php print admin_url('admin-ajax.php'); ?>?action=PO_disable_debug_msg'), {PO_nonce: '<?php print $this->nonce; ?>'}, function (result) {
									jQuery('.PO-debug-msg-container').remove();
								});
								return false;
							});
						</script>
						<?php
					}
					?>
				</div>
				<?php
			}
		}
	}
	
	function user_logged_in($login, $user=null) {
		if (is_null($user)) {
			$user=wp_get_current_user();
		}
		
		foreach($user->roles as $key=>$role) {
			setcookie('po_assigned_roles['.$key.']', $role, 0 ,"/");
		}
	}
	
	function user_logged_out() {
		if (isset($_COOKIE['po_assigned_roles']) && is_array($_COOKIE['po_assigned_roles'])) {
			foreach($_COOKIE['po_assigned_roles'] as $key=>$cookieVal) {
				setcookie('po_assigned_roles['.$key.']', '', time()-999999 ,"/");
			}
		}
	}
	
	function check_version() {
		global $pagenow;
		##Check version and activate if needed.
		if (get_option("PO_version_num") != "9.6.4" && !in_array($pagenow, array("plugins.php", "update-core.php", "update.php"))) {
			$this->activate();
		}
	}
	
	function init() {
		global $wpdb;
		
		##Create nonce
		$this->nonce = wp_create_nonce(plugin_basename(__FILE__));
		
		$this->check_version();


		##Check for posts that have been deleted
		if (false === get_site_transient('PO_delete_missing_posts')) {
			$allPostsQuery = "SELECT DISTINCT(post_id) FROM ".$wpdb->prefix."po_plugins WHERE post_id != 0";
			$allPosts = $wpdb->get_results($allPostsQuery, ARRAY_A);
			foreach ($allPosts as $post) {
				if (false === get_post_status($post['post_id'])) {
					$deletePluginQuery = "DELETE FROM ".$wpdb->prefix."po_plugins WHERE post_id = %d";
					$wpdb->query($wpdb->prepare($deletePluginQuery, $post['post_id']));
				}
			}
			set_site_transient('PO_delete_missing_posts', 1, 604800);
		}

		if (is_multisite() && get_option('PO_order_access_net_admin') == 1 && !current_user_can('manage_network')) {
			$this->pluginPageActions = "0";
		}

		$this->register_type();
		$this->register_taxonomy();
		$this->fix_active_plugins();
		$this->recreate_plugin_order();
	}

	function verify_nonce($nonce) {
		return wp_verify_nonce(sanitize_text_field($nonce), plugin_basename(__FILE__) );
	}
	
	function move_old_posts($oldPosts) {
		global $wpdb;
		foreach($oldPosts as $post) {
			$enabledMobilePlugins = get_post_meta($post->ID, '_PO_enabled_mobile_plugins', $single=true);
			$disabledMobilePlugins = get_post_meta($post->ID, '_PO_disabled_mobile_plugins', $single=true);
			$enabledPlugins = get_post_meta($post->ID, '_PO_enabled_plugins', $single=true);
			$disabledPlugins = get_post_meta($post->ID, '_PO_disabled_plugins', $single=true);
			$children = get_post_meta($post->ID, '_PO_affect_children', $single=true);
			
			$secure=0;
			if (preg_match('/^.{1,5}:\/\//', get_post_meta($post->ID, '_PO_permalink', $single=true), $matches)) {
				switch ($matches[0]) {
					case "https://":
						$secure=1;
						break;
					default:
						$secure=0;
				}
			}
			
			$permalink = preg_replace('/^.{1,5}:\/\//', '', get_post_meta($post->ID, '_PO_permalink', $single=true));
			
			$splitPermalink = explode('?', $permalink);
			$permalinkNoArgs = $splitPermalink[0];

			$dirCount = substr_count($permalink, "/");
			
			$wpdb->insert($wpdb->prefix."po_plugins", array("enabled_mobile_plugins"=>serialize($enabledMobilePlugins), "disabled_mobile_plugins"=>serialize($disabledMobilePlugins), "enabled_plugins"=>serialize($enabledPlugins), "disabled_plugins"=>serialize($disabledPlugins), "post_type"=>get_post_type($post->ID), "permalink"=>$permalink, "permalink_hash"=>md5($permalinkNoArgs), "permalink_hash_args"=>md5($permalink), "children"=>$children, "secure"=>$secure, "post_id"=>$post->ID, "post_priority"=>0, "dir_count"=>$dirCount));
		}
		update_option('PO_old_posts_moved', 1);
		

		delete_post_meta_by_key('_PO_affect_children');
		delete_post_meta_by_key('_PO_disabled_plugins');
		delete_post_meta_by_key('_PO_enabled_plugins');
		delete_post_meta_by_key('_PO_disabled_mobile_plugins');
		delete_post_meta_by_key('_PO_enabled_mobile_plugins');
		delete_post_meta_by_key('_PO_permalink');
	}
	
	function move_disabled_plugin_options() {
		global $wpdb;
		$pluginLists = array();
		$deleteOptions = array();

		$pluginLists['global_plugin_lists'] = array(get_option('PO_disabled_plugins', array()), array(), get_option('PO_disabled_mobile_plugins', array()), array(), get_option('PO_disabled_groups', array()), array(), get_option('PO_disabled_mobile_groups', array()), array());

		$deleteOptions[] = 'PO_disabled_plugins';
		$deleteOptions[] = 'PO_disabled_mobile_plugins';
		$deleteOptions[] = 'PO_disabled_groups';
		$deleteOptions[] = 'PO_disabled_mobile_groups';

		$pluginLists['search_plugin_lists'] = array(get_option('PO_disabled_search_plugins', array()), get_option('PO_enabled_search_plugins', array()), get_option('PO_disabled_mobile_search_plugins', array()), get_option('PO_enabled_mobile_search_plugins', array()), get_option('PO_disabled_search_groups', array()), get_option('PO_enabled_search_groups', array()), get_option('PO_disabled_mobile_search_groups', array()), get_option('PO_disabled_mobile_search_groups', array()));

		$deleteOptions[] = 'PO_disabled_search_plugins';
		$deleteOptions[] = 'PO_enabled_search_plugins';
		$deleteOptions[] = 'PO_disabled_mobile_search_plugins';
		$deleteOptions[] = 'PO_enabled_mobile_search_plugins';
		$deleteOptions[] = 'PO_disabled_search_groups';
		$deleteOptions[] = 'PO_enabled_search_groups';
		$deleteOptions[] = 'PO_disabled_mobile_search_groups';
		$deleteOptions[] = 'PO_enabled_mobile_search_groups';
		
		$postTypeSupport = get_option('PO_custom_post_type_support');
		
		foreach($postTypeSupport as $postType) {
			if (get_option('PO_disabled_pt_plugins_'.$postType) !== false) {
				$pluginLists['pt_'.$postType.'_plugin_lists'] = array(get_option('PO_disabled_pt_plugins_'.$postType, array()), get_option('PO_enabled_pt_plugins_'.$postType, array()), get_option('PO_disabled_mobile_pt_plugins_'.$postType, array()), get_option('PO_enabled_mobile_pt_plugins_'.$postType, array()), get_option('PO_disabled_pt_groups_'.$postType, array()), get_option('PO_enabled_pt_groups_'.$postType, array()), get_option('PO_disabled_mobile_pt_groups_'.$postType, array()), get_option('PO_disabled_mobile_pt_groups_'.$postType, array()));

				$deleteOptions[] = 'PO_disabled_pt_plugins_'.$postType;
				$deleteOptions[] = 'PO_enabled_pt_plugins_'.$postType;
				$deleteOptions[] = 'PO_disabled_mobile_pt_plugins_'.$postType;
				$deleteOptions[] = 'PO_enabled_mobile_pt_plugins_'.$postType;
				$deleteOptions[] = 'PO_disabled_pt_groups_'.$postType;
				$deleteOptions[] = 'PO_enabled_pt_groups_'.$postType;
				$deleteOptions[] = 'PO_disabled_mobile_pt_groups_'.$postType;
				$deleteOptions[] = 'PO_enabled_mobile_pt_groups_'.$postType;
			}
		}
		
		foreach($pluginLists as $postType=>$lists) {
			$wpdb->insert($wpdb->prefix."po_plugins", array('post_id'=>0, 'post_type'=>$postType, 'disabled_plugins'=>serialize($lists[0]), 'enabled_plugins'=>serialize($lists[1]), 'disabled_mobile_plugins'=>serialize($lists[2]), 'enabled_mobile_plugins'=>serialize($lists[3]), 'disabled_groups'=>serialize($lists[4]), 'enabled_groups'=>serialize($lists[5]), 'disabled_mobile_groups'=>serialize($lists[6]), 'enabled_mobile_groups'=>serialize($lists[7])));
		}

		foreach($deleteOptions as $optionName) {
			delete_option($optionName);
		}
	}
	
	function activate() {
		global $wpdb;
		//Prevent disabling selective plugin loading during update
		update_option('PO_updating_plugin', '1');
		
		##Remove the capital letters from the plugins table if it already exists.
		if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."PO_plugins'") == $wpdb->prefix."PO_plugins" && $wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."po_plugins'") != $wpdb->prefix."po_plugins") {
			$wpdb->query("RENAME TABLE ".$wpdb->prefix."PO_plugins TO ".$wpdb->prefix."po_plugins");
		}
		
		$poPluginTableSQL = "CREATE TABLE ".$wpdb->prefix."po_plugins (
			pl_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			post_id bigint(20) unsigned NOT NULL,
			permalink longtext NOT NULL,
			permalink_hash varchar(32) NOT NULL default '',
			permalink_hash_args varchar(32) NOT NULL default '',
			post_type varchar(50) NOT NULL default '',
			status varchar(20) NOT NULL default 'publish',
			secure int(1) NOT NULL default 0,
			children int(1) NOT NULL default 0,
			pt_override int(1) NOT NULL default 0,
			disabled_plugins longtext NOT NULL,
			enabled_plugins longtext NOT NULL,
			disabled_mobile_plugins longtext NOT NULL,
			enabled_mobile_plugins longtext NOT NULL,
			disabled_groups longtext NOT NULL,
			enabled_groups longtext NOT NULL,
			disabled_mobile_groups longtext NOT NULL,
			enabled_mobile_groups longtext NOT NULL,
			post_priority int(3) NOT NULL default 0,
			dir_count int(3) NOT NULL default 0,
			user_role varchar(100) NOT NULL default '_',
			PRIMARY KEY (pl_id),
			KEY PO_post_id (post_id),
			KEY PO_permalink_idx (permalink_hash,status,secure,post_type,user_role),
			KEY PO_permalink_args_idx (permalink_hash_args,status,secure,post_type,user_role),
			KEY PO_page_lists (post_id,post_type,user_role)
		);";
		if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."po_plugins'") != $wpdb->prefix."po_plugins") {
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($poPluginTableSQL);
		}

		$primaryKey = $wpdb->get_row("SHOW KEYS FROM ".$wpdb->prefix."po_plugins WHERE key_name = 'PRIMARY'", ARRAY_A);
		if ($primaryKey['Column_name'] == 'post_id') {
			$deletePrimaryResult = $wpdb->query("ALTER TABLE ".$wpdb->prefix."po_plugins DROP PRIMARY KEY");
		}
		
		//Add new columns to po_plugins table
		$showColumnSql = "SHOW COLUMNS FROM ".$wpdb->prefix."po_plugins";
		$showColumnResults = $wpdb->get_results($showColumnSql);
		$newColumns = array(
			'pt_override' => array(0, 'int(1) NOT NULL default 0'),
			'disabled_groups' => array(0, 'longtext NOT NULL'),
			'enabled_groups' => array(0, 'longtext NOT NULL'),
			'disabled_mobile_groups' => array(0, 'longtext NOT NULL'),
			'enabled_mobile_groups' => array(0, 'longtext NOT NULL'),
			'post_priority' => array(0, 'int(3) NOT NULL default 0'),
			'dir_count' => array(0, 'int(3) NOT NULL default 0'),
			'pl_id' => array(0, 'bigint(20) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT'),
			'user_role' => array(0, "varchar(100) NOT NULL default '_'")
		);
		$updatedColumns = array(
			'post_type' => array(0, "varchar(50)", "NOT NULL", "DEFAULT ''"),
			'user_role' => array(0, "varchar(100)", "NOT NULL", "DEFAULT '_'")
		);
		foreach ($showColumnResults as $column) {
			if (array_key_exists($column->Field, $newColumns)) {
				$newColumns[$column->Field][0] = 1;
			}
			if (array_key_exists($column->Field, $updatedColumns) && $column->Type != $updatedColumns[$column->Field][1]) {
				$updatedColumns[$column->Field][0] = 1;
			}
		}

		foreach ($newColumns as $column=>$value) {
			if ($value[0] == 0) {
				$addColumnSql = "ALTER TABLE ".$wpdb->prefix."po_plugins ADD COLUMN " . $column . " " . $value[1] . ";";
				$addColumnResult = $wpdb->query($addColumnSql);
			}
		}

		foreach ($updatedColumns as $column=>$value) {
			if ($value[0] == 1) {
				$updateColumnSql = "ALTER TABLE ".$wpdb->prefix."po_plugins MODIFY " . $column . " " . $value[1] . " " . $value[2] . " " . $value[3] . ";";
				$updateColumnResult = $wpdb->query($updateColumnSql);
			}
		}
		
		$dropIndex = array('PO_permalink_hash', 'PO_permalink_hash_args');
		
		foreach ($dropIndex as $index) {
			$checkIndexSql = "SHOW INDEX FROM ".$wpdb->prefix."po_plugins WHERE key_name = '".$index."';";
			$checkIndexResult = $wpdb->query($checkIndexSql);
			if ($checkIndexResult == '1') {
				$dropIndexSql = "DROP INDEX ".$index." ON ".$wpdb->prefix."po_plugins;";
				$dropIndexResult = $wpdb->query($dropIndexSql);
			}
		}
		
		$newIndex = array(
			'PO_post_id' => 'post_id',
			'PO_permalink_idx' => 'permalink_hash,status,secure,post_type,user_role',
			'PO_permalink_args_idx' => 'permalink_hash_args,status,secure,post_type,user_role',
			'PO_page_lists' => 'post_id,post_type,user_role'
		);
		
		foreach ($newIndex as $index=>$value) {
			$checkIndexSql = "SHOW INDEX FROM ".$wpdb->prefix."po_plugins WHERE key_name = '".$index."';";
			$checkIndexResult = $wpdb->query($checkIndexSql);
			if ($checkIndexResult == '0') {
				$addIndexSql = "ALTER TABLE ".$wpdb->prefix."po_plugins ADD INDEX ".$index." (".$value.");";
				$addIndexResult = $wpdb->query($addIndexSql);
			}
		}
		
		##Cleanup from previous versions
		delete_option('PO_old_posts_moved');
		delete_option('PO_old_urls_moved');
		delete_option('PO_old_groups_moved');
		delete_option('PO_preserve_settings');
		delete_option('PO_group_members_corrected');

		if (get_option("PO_admin_disable_plugins") != "") {
			update_option('PO_disable_plugins_admin', get_option("PO_admin_disable_plugins"));
			delete_option("PO_admin_disable_plugins");
		}

		if (get_option("PO_disable_by_role") != "") {
			update_option('PO_disable_plugins_by_role', get_option("PO_disable_by_role"));
			delete_option("PO_disable_by_role");
		}
		
		if (get_option("PO_disable_mobile_plugins") != "") {
			update_option('PO_disable_plugins_mobile', get_option("PO_disable_mobile_plugins"));
			delete_option("PO_disable_mobile_plugins");
		}
		
		if (get_option("PO_disable_plugins") != "") {
			update_option('PO_disable_plugins_frontend', get_option("PO_disable_plugins"));
			delete_option("PO_disable_plugins");
		}
		
		if (get_option("PO_plugin_order") != "") {
			update_option('PO_saved_plugin_order', get_option("PO_plugin_order"));
			delete_option("PO_plugin_order");
		}
		
		$postTypeSupport = get_option("PO_custom_post_type_support");
		if (!is_array($postTypeSupport)) {
			$postTypeSupport = array('plugin_filter');
		} else {
			$postTypeSupport[] = 'plugin_filter';
		}
		
		$existingPosts = get_posts(array('posts_per_page' => -1, 'post_type'=>$postTypeSupport, 'meta_key'=>'_PO_permalink'));
		if (sizeof($existingPosts) > 0) {
			$this->move_old_posts($existingPosts);
		}
		
		if (!file_exists(WPMU_PLUGIN_DIR)) {
			@mkdir(WPMU_PLUGIN_DIR);
		}

		if (file_exists(WPMU_PLUGIN_DIR . "/PluginOrganizerMU.class.php")) {
			@unlink(WPMU_PLUGIN_DIR . "/PluginOrganizerMU.class.php");
		}
		
		if (file_exists($this->pluginDirPath . "/" . plugin_basename(dirname(__FILE__)) . "/PluginOrganizerMU.class.php")) {
			@copy($this->pluginDirPath . "/" . plugin_basename(dirname(__FILE__)) . "/PluginOrganizerMU.class.php", WPMU_PLUGIN_DIR . "/PluginOrganizerMU.class.php");
		}
		
		if (!is_array(get_option("PO_custom_post_type_support"))) {
			update_option("PO_custom_post_type_support", array("post", "page"));
		}
		
		if (get_option('PO_fuzzy_url_matching') == "") {
			update_option('PO_fuzzy_url_matching', "1");
		}
		
		if (get_option('PO_disable_plugins_frontend') == "2") {
			update_option('PO_disable_plugins_frontend', 1);
		}
		
		if (get_option("PO_version_num") != "9.6.4") {
			update_option("PO_version_num", "9.6.4");
		}

		if (get_option('PO_disable_plugins_by_role') == "") {
			update_option("PO_disable_plugins_by_role", "1");
		}

		if (!is_array(get_option("PO_enabled_roles"))) {
			update_option("PO_enabled_roles", array());
		}

		if (get_option("PO_disable_compat_notices") != "") {
			update_option("PO_disable_compat_notices", "");
		}
		
		if (!is_array(get_option('PO_pt_stored'))) {
			update_option('PO_pt_stored', array());
		}

		if (get_option("PO_display_debug_msg") == "") {
			update_option("PO_display_debug_msg", "1");
		}
		
		if (!is_array(get_option("PO_debug_roles"))) {
			update_option('PO_debug_roles', array('administrator'));
		}
		
		if (get_option('PO_mobile_user_agents') == '' || (is_array(get_option('PO_mobile_user_agents')) && sizeof(get_option('PO_mobile_user_agents')) == 0)) {
			update_option('PO_mobile_user_agents', array('mobile', 'bolt', 'palm', 'series60', 'symbian', 'fennec', 'nokia', 'kindle', 'minimo', 'netfront', 'opera mini', 'opera mobi', 'semc-browser', 'skyfire', 'teashark', 'uzard', 'android', 'blackberry', 'iphone', 'ipad'));
		}

		//Update dir_count on all saved posts
		$savedPosts = $wpdb->get_results("SELECT pl_id, permalink FROM ".$wpdb->prefix."po_plugins WHERE post_id != 0", ARRAY_A);
		
		foreach($savedPosts as $savedPost) {
			$dirCount = substr_count($savedPost['permalink'], "/");
			$wpdb->update($wpdb->prefix."po_plugins", array("dir_count"=>$dirCount), array("pl_id"=>$savedPost['pl_id']));
		}
			
		//Add capabilities to the administrator role
		$administrator = get_role( 'administrator' );
		if ( is_object($administrator) ) {			
			$administrator->add_cap('edit_plugin_filter');
			$administrator->add_cap('edit_plugin_filters');
			$administrator->add_cap('edit_private_plugin_filters');
			$administrator->add_cap('delete_plugin_filter');
			$administrator->add_cap('delete_plugin_filters');
			$administrator->add_cap('edit_others_plugin_filters');
			$administrator->add_cap('read_plugin_filters');
			$administrator->add_cap('read_private_plugin_filters');
			$administrator->add_cap('publish_plugin_filters');
			$administrator->add_cap('delete_others_plugin_filters');
			$administrator->add_cap('delete_published_plugin_filters');
			$administrator->add_cap('delete_private_plugin_filters');
			$administrator->add_cap('edit_filter_group');
			$administrator->add_cap('manage_filter_groups');
			

			$administrator->add_cap('edit_plugin_group');
			$administrator->add_cap('edit_plugin_groups');
			$administrator->add_cap('edit_private_plugin_groups');
			$administrator->add_cap('delete_plugin_group');
			$administrator->add_cap('delete_plugin_groups');
			$administrator->add_cap('edit_others_plugin_groups');
			$administrator->add_cap('read_plugin_groups');
			$administrator->add_cap('read_private_plugin_groups');
			$administrator->add_cap('publish_plugin_groups');
			$administrator->add_cap('delete_others_plugin_groups');
			$administrator->add_cap('delete_published_plugin_groups');
			$administrator->add_cap('delete_private_plugin_groups');
		}

		//Make sure all active plugins are valid
		$activePlugins = get_option("active_plugins");
		$newActivePlugins = array();
		$pluginDisabled = 0;
		foreach ($activePlugins as $key=>$plugin) {
			if (file_exists($this->pluginDirPath . "/" . $plugin)) {
				$newActivePlugins[] = $plugin;
			} else {
				$pluginDisabled = 1;
			}
		}
		if ($pluginDisabled == 1) {
			update_option("active_plugins", $newActivePlugins);
		}

		if (get_option('PO_disabled_plugins') !== false) {
			$this->move_disabled_plugin_options();
		}

		//Prevent disabling selective plugin loading during update
		update_option('PO_updating_plugin', '');
	}
	
	function deactivate() {
		update_option("PO_disable_plugins_frontend", 2);
		

		$administrator = get_role( 'administrator' );
		if ( is_object($administrator) ) {			
			$administrator->remove_cap('edit_plugin_filter');
			$administrator->remove_cap('edit_plugin_filters');
			$administrator->remove_cap('edit_private_plugin_filters');
			$administrator->remove_cap('delete_plugin_filter');
			$administrator->remove_cap('delete_plugin_filters');
			$administrator->remove_cap('edit_others_plugin_filters');
			$administrator->remove_cap('read_plugin_filters');
			$administrator->remove_cap('read_private_plugin_filters');
			$administrator->remove_cap('publish_plugin_filters');
			$administrator->remove_cap('delete_others_plugin_filters');
			$administrator->remove_cap('delete_published_plugin_filters');
			$administrator->remove_cap('delete_private_plugin_filters');
			$administrator->remove_cap('edit_filter_group');
			$administrator->remove_cap('manage_filter_groups');

			$administrator->remove_cap('edit_plugin_group');
			$administrator->remove_cap('edit_plugin_groups');
			$administrator->remove_cap('edit_private_plugin_groups');
			$administrator->remove_cap('delete_plugin_group');
			$administrator->remove_cap('delete_plugin_groups');
			$administrator->remove_cap('edit_others_plugin_groups');
			$administrator->remove_cap('read_plugin_groups');
			$administrator->remove_cap('read_private_plugin_groups');
			$administrator->remove_cap('publish_plugin_groups');
			$administrator->remove_cap('delete_others_plugin_groups');
			$administrator->remove_cap('delete_published_plugin_groups');
			$administrator->remove_cap('delete_private_plugin_groups');
		}
	}
	
	function create_default_group() {
		$post_id = wp_insert_post(array('post_title'=>"Default", 'post_type'=>'plugin_group', 'post_status'=>'publish'));
		if (!is_wp_error($post_id)) {
			update_post_meta($post_id, '_PO_group_members', array());
		}
		update_option("PO_default_group", $post_id);
	}
	
	function validate_field($fieldname) {
		if (isset($this->postedData[$fieldname])) {
			if (isset($this->regex[$fieldname]) && preg_match($this->regex[$fieldname], $this->postedData[$fieldname])) {
				return true;
			} else if (preg_match($this->regex['default'], $this->postedData[$fieldname])) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	function admin_menu() {
		if ( current_user_can( 'activate_plugins' ) ) {
			$this->tpl = new PO_Template($this);
			
			$plugin_page=add_menu_page( 'Plugin Organizer', 'Plugin Organizer', 'activate_plugins', 'Plugin_Organizer', array($this->tpl, 'settings_page'), 'dashicons-PO-icon-puzzle-piece');
			
			$settings_page=add_submenu_page('Plugin_Organizer', 'Settings', 'Settings', 'activate_plugins', 'Plugin_Organizer', array($this->tpl, 'settings_page'));
			add_action('admin_head-'.$settings_page, array($this->tpl, 'admin_css'));
			add_action('admin_head-'.$settings_page, array($this->tpl, 'settings_page_js'));
			add_action('admin_head-'.$settings_page, array($this->tpl, 'common_js'));
			
			$plugin_page=add_submenu_page('Plugin_Organizer', 'Global Plugins', 'Global Plugins', 'activate_plugins', 'PO_global_plugins', array($this->tpl, 'global_plugins_page'));
			add_action('admin_head-'.$plugin_page, array($this->tpl, 'admin_css'));
			add_action('admin_head-'.$plugin_page, array($this->tpl, 'global_plugins_js'));
			add_action('admin_head-'.$plugin_page, array($this->tpl, 'common_js'));
			
			$plugin_page=add_submenu_page('Plugin_Organizer', 'Search Plugins', 'Search Plugins', 'activate_plugins', 'PO_search_plugins', array($this->tpl, 'search_plugins_page'));
			add_action('admin_head-'.$plugin_page, array($this->tpl, 'admin_css'));
			add_action('admin_head-'.$plugin_page, array($this->tpl, 'search_plugins_js'));
			add_action('admin_head-'.$plugin_page, array($this->tpl, 'common_js'));

			$plugin_page=add_submenu_page('Plugin_Organizer', 'Post Type Plugins', 'Post Type Plugins', 'activate_plugins', 'PO_pt_plugins', array($this->tpl, 'pt_plugins_page'));
			add_action('admin_head-'.$plugin_page, array($this->tpl, 'admin_css'));
			add_action('admin_head-'.$plugin_page, array($this->tpl, 'pt_plugins_js'));
			add_action('admin_head-'.$plugin_page, array($this->tpl, 'common_js'));

			$plugin_page=add_submenu_page('Plugin_Organizer', 'Group And Order Plugins', 'Group And Order Plugins', 'activate_plugins', 'PO_group_and_order_plugins', array($this->tpl, 'group_and_order_plugins_page'));
			add_action('admin_head-'.$plugin_page, array($this->tpl, 'admin_css'));
			add_action('admin_head-'.$plugin_page, array($this->tpl, 'group_and_order_plugins_js'));
			add_action('admin_head-'.$plugin_page, array($this->tpl, 'common_js'));
			
			add_submenu_page('Plugin_Organizer', 'Filter Groups', 'Filter Groups', 'activate_plugins', 'edit-tags.php?taxonomy=filter_group&post_type=plugin_filter');
		}

	}

	function register_admin_style() {
		wp_register_style('font-awesome-4.7.0', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
		wp_register_style('PO-icons', $this->urlPath . '/css/PO-icons.css');
		wp_register_style('PO-admin', $this->urlPath . '/css/PO-admin.css');
		wp_register_style('PO-colorpicker', $this->urlPath . '/css/colorpicker.css');
		
		wp_register_script('PO-colorpicker', $this->urlPath . '/js/colorpicker.js');
	}

	function enqueue_admin_style() {
		wp_enqueue_style('font-awesome-4.7.0');
		wp_enqueue_style('PO-icons');
		wp_enqueue_style('PO-colorpicker');
		wp_enqueue_script('PO-colorpicker');
	}
		
	function add_ajax_notices() {
		?>
		<div id="PO-ui-notices" title="Basic dialog">
			<div id="PO-ajax-notices-container">
			</div>
		</div>
		<?php
	}
	
	function compatibility_notices($calledFromMeta=0) {
		global $pagenow;
		if ($calledFromMeta == 1) {
			?>
			<style type="text/css">
				.PO-compatibility-header {display:none;}
			</style>
			<?php
		}

		if ($calledFromMeta != 1 && in_array($pagenow, array('post.php', 'post-new.php'))) {
			add_action('PO_display_meta_compatibility', array($this, 'compatibility_notices'));
		}
		
		$activeSitePlugins = get_site_option('active_sitewide_plugins', array());
		$activePlugins = get_option("active_plugins");
		$noticesArray = array();
		if (in_array('wp-spamshield/wp-spamshield.php', $activePlugins) || array_key_exists('wp-spamshield/wp-spamshield.php', $activeSitePlugins)) {
			$noticesArray[] = "<strong>WARNING:</strong> You are currently running WP Spamshield. This plugin was removed from the wordpress plugin repository for containing malicious code that targeted other developers. You should immediately remove this plugin. It has attempted to modify the settings for Plugin Organizer as well as other plugins. Because of this malicious activity you can't run Plugin Organizer and WP Spamshield together.";
		}

		if (in_array('woocommerce-smart-coupons/woocommerce-smart-coupons.php', $activePlugins) || array_key_exists('woocommerce-smart-coupons/woocommerce-smart-coupons.php', $activeSitePlugins)) {
			$noticesArray[] = "<strong>WARNING:</strong> You are currently running Woocommerce Smart Coupons. To run this plugin you must go to the <a href=\"".get_admin_url()."admin.php?page=PO_group_and_order_plugins\">Group and Order plugins</a> page and set Woocommerce Smart Coupons to load before Woocommerce or it's functionality will not be available.";
		}

		if (count($noticesArray) > 0) {
			?>
			<div class="notice notice-error PO-compat-notices <?php print ($calledFromMeta != 1)? 'PO-compatibility-header':''; ?>" style="padding: 20px;">
				<?php
				foreach($noticesArray as $notice) {
					print $notice . '<br />';
				}
				?>
				<a href="#" class="PO-disable-compat-notices">Disable this warning</a>
			</div>
			<script type="text/javascript" language="javascript">
				jQuery('.PO-disable-compat-notices').click(function() {
					jQuery.post(encodeURI(ajaxurl + '?action=PO_disable_compat_notices'), {PO_nonce: '<?php print $this->nonce; ?>'}, function (result) {
						jQuery('.PO-compat-notices').remove();
					});
					return false;
				});
			</script>
			<?php
		}
	}
	
	function admin_notices() {
		if (get_option('PO_disable_admin_warning') != '1') {
			?>
			<div class="notice notice-warning" id="PO_admin_warning" style="padding: 20px;">
				<strong>WARNING:</strong> Reordering or disabling plugins can have catastrophic affects on your site. It can cause issues with plugins and can render your site inaccessible. Make sure you know what you are doing and always make a backup of your database before changing the load order or disabling plugins. <a href="#" id="PO_disable_admin_warning">Disable this warning</a>
			</div>
			<script type="text/javascript" language="javascript">
				jQuery('#PO_disable_admin_warning').click(function() {
					jQuery.post(encodeURI(ajaxurl + '?action=PO_disable_admin_warning'), {PO_nonce: '<?php print $this->nonce; ?>'}, function (result) {
						jQuery('#PO_admin_warning').remove();
					});
					return false;
				});
			</script>
			<?php
		}
		
		$errMsg = $this->check_mu_plugin();
		
		if ($errMsg != '') {
			?>
			<div class="updated" id="PO_admin_notices">
				<h2>Plugin Organizer is not set up correctly.</h2>
				<?php _e( $errMsg, 'plugin-organizer' ); ?>
				<a href="#" id="PO_disable_admin_notices">Disable admin notices</a> - You will still recieve admin notices when you visit the Plugin Organizer settings page.
			</div>
			<script type="text/javascript" language="javascript">
				jQuery('#PO_disable_admin_notices').click(function() {
					jQuery.post(encodeURI(ajaxurl + '?action=PO_disable_admin_notices'), {PO_nonce: '<?php print $this->nonce; ?>'}, function (result) {
						jQuery('#PO_admin_notices').remove();
					});
					return false;
				});
			</script>
			<?php
		}
	}
	
	function check_mu_plugin() {
		$muPlugins = get_mu_plugins();
		if (!isset($muPlugins['PluginOrganizerMU.class.php']['Version'])) {
			return "<p>You are missing the MU Plugin.  Please use the tool provided on the settings page to move the plugin into place or manually copy ".$this->absPath."/lib/PluginOrganizerMU.class.php to ".WPMU_PLUGIN_DIR."/PluginOrganizerMU.class.php.  If you don't do this the plugin will not work.  This message will disappear when everything is correct.</p>";
		} else if (isset($muPlugins['PluginOrganizerMU.class.php']['Version']) && $muPlugins['PluginOrganizerMU.class.php']['Version'] != get_option("PO_version_num")) {
			return "<p>You are running an old version of the MU Plugin.  Please use the tool provided on the settings page to move the updated version into place or manually copy ".$this->absPath."/lib/PluginOrganizerMU.class.php to ".WPMU_PLUGIN_DIR."/PluginOrganizerMU.class.php.  If you don't do this the plugin will not work.  This message will disappear when everything is correct.</p>";
		} else {
			return "";
		}
	}
	
	function add_group_views($views) {
		$groups = get_posts(array('post_type'=>'plugin_group', 'posts_per_page'=>-1));
		if (!array_key_exists('all', $views)) {
			$views = array_reverse($views, true);
			$views['all'] = '<a href="'.get_admin_url().'plugins.php?plugin_status=all">All <span class="count">('.count(get_plugins()).')</span></a>';
			$views = array_reverse($views, true);
		}
		foreach ($groups as $group) {
			$groupMembers = $this->get_group_members($group->ID);
			if (isset($groupMembers[0]) && $groupMembers[0] != 'EMPTY') {
				$groupCount = sizeof($groupMembers);
			} else {
				$groupCount = 0;
			}
			$groupName = $group->post_title;
			$loopCount = 0;
			while(array_key_exists($groupName, $views) && $loopCount < 10) {
				$groupName = $group->post_title.$loopCount;
				$loopCount++;
			}
			$views[$groupName] = '<a href="'.get_admin_url().'plugins.php?PO_group_view='.$group->ID.'">'.$group->post_title.' <span class="count">('.$groupCount.')</span></a> ';
		}
		return $views;
	}
	
	function get_group_members($groupID) {
		$groupMembers = get_post_meta($groupID, '_PO_group_members', $single=true);
		$allPlugins = get_plugins();
		$groupCount = sizeof($groupMembers);

		if (isset($groupMembers[0]) && $groupMembers[0] != 'EMPTY') {
			foreach($groupMembers as $key=>$memberPlugin) {
				if (!array_key_exists($memberPlugin, $allPlugins)) {
					unset($groupMembers[$key]);
				}
			}
			if (sizeof($groupMembers) != $groupCount) {
				update_post_meta($groupID, '_PO_group_members', $groupMembers);
			}
		}

		return($groupMembers);
	}
	
	function create_plugin_lists($pluginList, $pluginExludeList) {
		$returnArray = array(array(), array());
		if (is_array($pluginList)) {
			foreach ($pluginList as $plugin) {
				if (!in_array($plugin, $pluginExludeList)) {
					$returnArray[0][] = $plugin;
				}
			}

			foreach ($pluginExludeList as $plugin) {
				if (!in_array($plugin, $pluginList)) {
					$returnArray[1][] = $plugin;
				}
			}
		} else {
			foreach ($pluginExludeList as $plugin) {
				$returnArray[1][] = $plugin;
			}
		}
		return $returnArray;
	}
	
	function fix_active_plugins() {
		$plugins = get_option('active_plugins');
		$networkPlugins = get_site_option('active_sitewide_plugins');
		if (is_array($networkPlugins)) {
			$networkPluginMissing = 0;
			foreach($networkPlugins as $key=>$pluginFile) {
				if (array_search($key, $plugins) === FALSE && file_exists($this->pluginDirPath . "/" . $key)) {
					$plugins[] = $key;
					$networkPluginMissing = 1;
				}
			}
			if ($networkPluginMissing == 1) {
				update_option("active_plugins", $plugins);
			}
		}
	}
	
	function get_requested_group($allPluginList) {
		if (isset($_REQUEST['PO_group_view']) && is_numeric($_REQUEST['PO_group_view'])) {
			$plugins = get_option("active_plugins");
		
			$activePlugins = Array();
			$newPluginList = Array();
			$activePluginOrder = Array();
			
			$members = $this->get_group_members($_REQUEST['PO_group_view']);
			$members = stripslashes_deep($members);
			foreach ($allPluginList as $key=>$val) {
				if (is_array($members) && in_array($key, $members)) {
					$activePlugins[$key] = $val;
					$activePluginOrder[] = array_search($key, $plugins);
				}
			}
			array_multisort($activePluginOrder, $activePlugins);
			$newPluginList = $activePlugins;
		} else {
			$newPluginList = $allPluginList;
		}
		return $newPluginList;
	}
	
	function reorder_plugins($allPluginList) {
		global $pagenow;
		$plugins = get_option("active_plugins");
		
		
		if (is_admin() && $this->pluginPageActions == 1 && in_array($pagenow, array("plugins.php"))) {
			$perPage = get_user_option("plugins_per_page");
			if (!is_numeric($perPage)) {
				$perPage = 999;
			}
			if (sizeOf($plugins) > $perPage) {
				$this->pluginPageActions = 0;
				return $allPluginList;
			}
		}
		$activePlugins = Array();
		$inactivePlugins = Array();
		$newPluginList = Array();
		$activePluginOrder = Array();
		
		foreach ($allPluginList as $key=>$val) {
			if (in_array($key, $plugins)) {
				$activePlugins[$key] = $val;
				$activePluginOrder[] = array_search($key, $plugins);
			} else {
				$inactivePlugins[$key] = $val;
			}
		}
		array_multisort($activePluginOrder, $activePlugins);
		
		$newPluginList = array_merge($activePlugins, $inactivePlugins);	
		return $newPluginList;
	}


	function get_pf_column_headers($columns) {
		$columns['PO_PF_permalink'] = __('Permalinks');
		return $columns;
	}

	function set_pf_custom_column_values($column_name, $post_id ) {
		global $wpdb;
		switch ($column_name) {
			case 'PO_PF_permalink' :
				$postSettingsQuery = "SELECT permalink, secure FROM ".$wpdb->prefix."po_plugins WHERE post_id = %d GROUP BY permalink, secure";
				$permalinks = $wpdb->get_results($wpdb->prepare($postSettingsQuery, $post_id), ARRAY_A);
				if (sizeof($permalinks) > 0) {
					foreach($permalinks as $permalink) {
						print (($permalink['secure'] == '1')? 'https://':'http://' ) . $permalink['permalink'] . "<br />";
					}
				}
				break;
			default:
		}
	}
	
	
	function get_column_headers($columns) {
		$count = 0;
		$columns['PO_groups'] = __('Groups');
		return $columns;
	}

	function set_custom_column_values($column_name, $pluginPath, $plugin ) {
		switch ($column_name) {
			case 'PO_groups' :
				$groups = get_posts(array('post_type'=>'plugin_group', 'posts_per_page'=>-1));
				$assignedGroups = "";
				foreach ($groups as $group) {
					$members = $this->get_group_members($group->ID);
					$members = stripslashes_deep($members);
					if (is_array($members) && array_search($pluginPath, $members) !== FALSE) {
						$assignedGroups .= '<a href="'.get_admin_url().'plugins.php?PO_group_view='.$group->ID.'">'.$group->post_title.'</a> ,';
					}
				}
				print rtrim($assignedGroups, ',');
				break;
			default:
		}
	}

	
	function change_page_title($translation, $original) {
		global $pagenow;
		if ($pagenow == "plugins.php" && $original == 'Plugins') {
			$group = get_posts(array('ID'=>$_REQUEST['PO_group_view'], 'post_type'=>'plugin_group'));
			if (is_array($group[0])) {
				return 'Plugin Group: '.$group[0]->post_title;
			}
		}
		return $translation;
	}

	function disable_plugin_box() {
		if ( current_user_can( 'activate_plugins' ) ) {
			$supportedPostTypes = get_option("PO_custom_post_type_support");
			$supportedPostTypes[] = 'plugin_filter';
			if (is_array($supportedPostTypes) && get_option('PO_disable_plugins_frontend') == 1) {
				foreach ($supportedPostTypes as $postType) {
					add_meta_box(
						'plugin_organizer',
						'Plugin Organizer',
						array(new PO_Template($this), 'get_post_meta_box'),
						$postType,
						'normal'
					);
				}
			}
		}
	}

	function find_parent_plugins($currID, $permalink, $mobile, $secure, $role) {
		global $wpdb;
		$availableRoles = array($role, '');
		$postTypeSupport = get_option('PO_custom_post_type_support');
		$postTypeSupport[] = 'plugin_filter';
		
		$fuzzyPlugins = array(
			'post_id'=>0,
			'plugins'=>array(
				'disabled_plugins'=>array(),
				'enabled_plugins'=>array(),
				'disabled_groups'=>array(),
				'enabled_groups'=>array()
			)
		);
		
		$permalink = preg_replace('/\?.*$/', '', $permalink);
		$permalinkSearchField = 'permalink_hash';
		
		$endChar = (preg_match('/\/$/', get_option('permalink_structure')) || is_admin())? '/':'';
		$lastUrl = $_SERVER['HTTP_HOST'].$endChar;
		
		$fuzzyPost = array();
		//Dont allow an endless loop
		$loopCount = 0;

		$permalinkHashes = array();
		$fuzzyPostQuery = "SELECT * FROM ".$wpdb->prefix."po_plugins WHERE (";
		$previousIndex = 8;
		$lastOcc = strrpos($permalink, "/");
		while ($loopCount < 25 && $previousIndex < $lastOcc) {
			$startReplace = strpos($permalink, '/', $previousIndex);
			$endReplace = strpos($permalink, '/', $startReplace+1);
			if ($endReplace === false) {
				$endReplace = strlen($permalink);
			}
			$permalinkHashes[] = $wpdb->prepare('%s', md5(substr_replace($permalink, "/*/", $startReplace, ($endReplace-$startReplace)+1)));
			$previousIndex = $endReplace;
			$loopCount++;
		}

		if (sizeof($permalinkHashes) > 0) {
			if (get_option('PO_ignore_protocol') == '0') {
				$fuzzyPostQuery = "SELECT * FROM ".$wpdb->prefix."po_plugins WHERE (".$permalinkSearchField." = ".implode(" OR ".$permalinkSearchField." = ", $permalinkHashes).") AND status IN ('publish','private') AND secure = %d AND post_type IN ([IN]) AND user_role IN ([R_IN]) AND post_id != %d ORDER BY dir_count DESC, FIELD(post_type, [IN]), FIELD(user_role, [R_IN]), post_priority DESC";
				$fuzzyPostQuery = $wpdb->prepare($fuzzyPostQuery, $secure, $currID);
				$fuzzyPostQuery = $this->prepare_in($fuzzyPostQuery, $availableRoles, '[R_IN]');
				$fuzzyPost = $wpdb->get_results($this->prepare_in($fuzzyPostQuery, $postTypeSupport, '[IN]'), ARRAY_A);
				
			} else {
				$fuzzyPostQuery = "SELECT * FROM ".$wpdb->prefix."po_plugins WHERE (".$permalinkSearchField." = ".implode(" OR ".$permalinkSearchField." = ", $permalinkHashes).") AND status IN ('publish','private') AND post_type IN ([IN]) AND user_role IN ([R_IN]) AND post_id != %d ORDER BY dir_count DESC, FIELD(post_type, [IN]), FIELD(user_role, [R_IN]), post_priority DESC";
				$fuzzyPostQuery = $wpdb->prepare($fuzzyPostQuery, $currID);
				$fuzzyPostQuery = $this->prepare_in($fuzzyPostQuery, $availableRoles, '[R_IN]');
				$fuzzyPost = $wpdb->get_results($this->prepare_in($fuzzyPostQuery, $postTypeSupport), ARRAY_A);
			}
		}


		#print $this->prepare_in($fuzzyPostQuery, $postTypeSupport);
		if (sizeof($fuzzyPost) == 0) {
			$permalinkHashes = array();
			$loopCount = 0;
			while ($loopCount < 25 && $permalink != $lastUrl && ($permalink = preg_replace('/\/[^\/]+\/?$/', $endChar, $permalink))) {
				$loopCount++;
				$permalinkHashes[] = $wpdb->prepare('%s', md5($permalink));
			}
			
			if (sizeof($permalinkHashes) > 0) {
				if (get_option('PO_ignore_protocol') == '0') {
					$fuzzyPostQuery = "SELECT * FROM ".$wpdb->prefix."po_plugins WHERE ('permalink_hash' = ".implode(" OR 'permalink_hash' = ", $permalinkHashes).") AND status IN ('publish','private') AND secure = %d AND children = 1 AND post_type IN ([IN]) AND user_role IN ([R_IN]) AND post_id != %d ORDER BY dir_count DESC, FIELD(post_type, [IN]), FIELD(user_role, [R_IN]), post_priority DESC";
					$fuzzyPostQuery = $wpdb->prepare($fuzzyPostQuery, $secure, $currID);
					$fuzzyPostQuery = $this->prepare_in($fuzzyPostQuery, $availableRoles, '[R_IN]');
					$fuzzyPost = $wpdb->get_results($this->prepare_in($fuzzyPostQuery, $postTypeSupport, '[IN]'), ARRAY_A);
					
				} else {
					$fuzzyPostQuery = "SELECT * FROM ".$wpdb->prefix."po_plugins WHERE ('permalink_hash' = ".implode(" OR 'permalink_hash' = ", $permalinkHashes).") AND status IN ('publish','private') AND children = 1 AND post_type IN ([IN]) AND user_role IN ([R_IN]) AND post_id != %d ORDER BY dir_count DESC, FIELD(post_type, [IN]), FIELD(user_role, [R_IN]), post_priority DESC";
					$fuzzyPostQuery = $wpdb->prepare($fuzzyPostQuery, $currID);
					$fuzzyPostQuery = $this->prepare_in($fuzzyPostQuery, $availableRoles, '[R_IN]');
					$fuzzyPost = $wpdb->get_results($this->prepare_in($fuzzyPostQuery, $postTypeSupport, '[IN]'), ARRAY_A);
				}
			}
		}

			
		#print $this->prepare_in($fuzzyPostQuery, $postTypeSupport);
		#print_r($fuzzyPost);
		$matchFound = 0;
		if (sizeof($fuzzyPost) > 0) {
			foreach($fuzzyPost as $currPost) {
				if ($mobile == 0) {
					$disabledFuzzyPlugins = @unserialize($currPost['disabled_plugins']);
					$enabledFuzzyPlugins = @unserialize($currPost['enabled_plugins']);
					$disabledFuzzyGroups = @unserialize($currPost['disabled_groups']);
					$enabledFuzzyGroups = @unserialize($currPost['enabled_groups']);
				} else {
					$disabledFuzzyPlugins = @unserialize($currPost['disabled_mobile_plugins']);
					$enabledFuzzyPlugins = @unserialize($currPost['enabled_mobile_plugins']);
					$disabledFuzzyGroups = @unserialize($currPost['disabled_mobile_groups']);
					$enabledFuzzyGroups = @unserialize($currPost['enabled_mobile_groups']);
				}
				if ((is_array($disabledFuzzyPlugins) && sizeof($disabledFuzzyPlugins) > 0) || (is_array($enabledFuzzyPlugins) && sizeof($enabledFuzzyPlugins) > 0) || (is_array($disabledFuzzyGroups) && sizeof($disabledFuzzyGroups) > 0) || (is_array($enabledFuzzyGroups) && sizeof($enabledFuzzyGroups) > 0)) {
					$matchFound = 1;
					break;
				}
			}
			
			if ($matchFound > 0) {
				if (!is_array($disabledFuzzyPlugins)) {
					$disabledFuzzyPlugins = array();
				}

				if (!is_array($enabledFuzzyPlugins)) {
					$enabledFuzzyPlugins = array();
				}

				if (!is_array($disabledFuzzyGroups)) {
					$disabledFuzzyGroups = array();
				}

				if (!is_array($enabledFuzzyGroups)) {
					$enabledFuzzyGroups = array();
				}

				$fuzzyPlugins['plugins']['disabled_plugins'] = $disabledFuzzyPlugins;
				$fuzzyPlugins['plugins']['enabled_plugins'] = $enabledFuzzyPlugins;
				$fuzzyPlugins['plugins']['disabled_groups'] = $disabledFuzzyGroups;
				$fuzzyPlugins['plugins']['enabled_groups'] = $enabledFuzzyGroups;

				$fuzzyPlugins['post_id'] = $currPost['post_id'];
			}
		}
		return $fuzzyPlugins;
	}
	
	
	function find_duplicate_permalinks($postID, $permalink, $secure) {
		global $wpdb;
		$returnDup = array();
		$dupPostQuery = "SELECT post_id FROM ".$wpdb->prefix."po_plugins WHERE permalink = %s AND post_id != %d AND secure = %d AND status != 'trash' GROUP BY post_id";
		$dupPosts = $wpdb->get_results($wpdb->prepare($dupPostQuery, $permalink, $postID, $secure), ARRAY_A);
		if (sizeOf($dupPosts) > 0) {
			foreach ($dupPosts as $dup) {
				$returnDup[] = $dup['post_id'];
			}
		}
		return $returnDup;
	}

	function change_plugin_filter_title($title) {
		global $post;
		$supportedPostTypes = get_option("PO_custom_post_type_support");
		$supportedPostTypes[] = 'plugin_filter';
		if ( is_object($post) && ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($post->ID) || !current_user_can( 'edit_post', $post->ID ) || !current_user_can( 'activate_plugins' ) || !in_array(get_post_type($post->ID), $supportedPostTypes) || !isset($this->postedData['poSubmitPostMetaBox']))) {
			return $title;
		}
		
		if (is_object($post) && get_post_type($post->ID) == 'plugin_filter') {
			if (isset($this->postedData['PO_filter_name']) && $this->postedData['PO_filter_name'] != '') {
				return sanitize_text_field($this->postedData['PO_filter_name']);
			} else if (!isset($this->postedData['PO_permalink_filter']) || $this->postedData['PO_permalink_filter'] == '') {
				$randomTitle = "";
				for($i=0; $i<10; $i++) {
					$randomTitle .= chr(mt_rand(109,122));
				}
				return $randomTitle;
			} else {
				return sanitize_text_field($this->postedData['PO_permalink_filter']);
			}
		} else {
			return $title;
		}
	}
	
	function save_post_meta_box($postID) {
		global $wpdb;

		$supportedPostTypes = get_option("PO_custom_post_type_support");
		$supportedPostTypes[] = 'plugin_filter';
		
		$postType = get_post_type($postID);
		if (isset($this->postedData['PO_pt_override'])) {
			$ptOverride = 1;
		} else {
			$ptOverride = 0;
		}
		
		$ptStored = get_option('PO_pt_stored');
		if ($ptOverride == 0 && is_array($ptStored) && in_array($postType, $ptStored)) {
			$ptSettingsFound = 1;
		} else {
			$ptSettingsFound = 0;
		}

		$storedPTOverride = $wpdb->get_var($wpdb->prepare("SELECT pt_override FROM ".$wpdb->prefix."po_plugins WHERE post_id=%d GROUP BY post_id", $postID));

		if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($postID) || !current_user_can('edit_post', $postID) || !in_array(get_post_type($postID), $supportedPostTypes) || (!isset($this->postedData['poSubmitPostMetaBox']) && ($storedPTOverride == 1 || $ptSettingsFound !=1))) {
			return $postID;
		}
		
		$availableRoles = $this->get_available_roles();
		
		if (isset($this->postedData['affectChildren'])) {
			$affectChildren = 1;
		} else {
			$affectChildren = 0;
		}
		
		$submittedPlugins = array();
		
		if ($ptSettingsFound == 1) {
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
		} else {
			foreach($availableRoles as $roleID=>$roleName) {
				$submittedPlugins[$roleID] = $this->get_submitted_plugin_lists($roleID);
			}
		}
		
		
		$postStatus = get_post_status($postID);
		if (!$postStatus) {
			$postStatus = 'publish';
		}

		$permalinks = array();
		
		$deletePermalinks = array();
		$deletePermalinksRes = $wpdb->get_results($wpdb->prepare("SELECT permalink, secure FROM ".$wpdb->prefix."po_plugins WHERE post_id=%d GROUP BY permalink, secure", $postID), ARRAY_A);
		if (is_array($deletePermalinks)) {
			foreach($deletePermalinksRes as $storedPermalink) {
				$deletePermalinks[] = (($storedPermalink['secure'] == '1')? 'https://':'http://' ) . $storedPermalink['permalink'];
			}
		}
		
		if (get_post_type($postID) != 'plugin_filter') {
			foreach($availableRoles as $roleID=>$roleName) {
				$pluginListID = $wpdb->get_var($wpdb->prepare("SELECT pl_id FROM ".$wpdb->prefix."po_plugins WHERE post_id=%d AND user_role=%s", $postID, $roleID));
				if (is_numeric($pluginListID)) {
					$permalinks[] = array($pluginListID, get_permalink($postID), $roleID);
				} else {
					$permalinks[] = array('tmp', get_permalink($postID), $roleID);
				}
			}
		} else {
			if (isset($this->postedData['PO_pl_id'])) {
				foreach($this->postedData['PO_pl_id'] as $plID) {
					$oldPermalink = $wpdb->get_row($wpdb->prepare("SELECT permalink, secure FROM ".$wpdb->prefix."po_plugins WHERE pl_id=%d", $plID), ARRAY_A);
					foreach($availableRoles as $roleID=>$roleName) {
						if (!is_numeric($plID)) {
							$permalinks[] = array($plID, $this->fix_trailng_slash(sanitize_text_field($this->postedData['PO_permalink_filter_'.$plID])), $roleID);
						} else {
							$pluginListID = $wpdb->get_var($wpdb->prepare("SELECT pl_id FROM ".$wpdb->prefix."po_plugins WHERE post_id=%d AND user_role=%s AND permalink=%s AND secure=%d", $postID, $roleID, $oldPermalink['permalink'], $oldPermalink['secure']));
							if (!is_numeric($pluginListID)) {
								$permalinks[] = array('tmp_tmp', $this->fix_trailng_slash(sanitize_text_field($this->postedData['PO_permalink_filter_'.$plID])), $roleID);
							} else {
								$permalinks[] = array($pluginListID, $this->fix_trailng_slash(sanitize_text_field($this->postedData['PO_permalink_filter_'.$plID])), $roleID);
							}
						}
					}
				}
			}
		}

		if (isset($this->postedData['PO_post_priority']) && is_numeric($this->postedData['PO_post_priority'])) {
			$postPriority = sanitize_text_field($this->postedData['PO_post_priority']);
		} else {
			$postPriority = 0;
		}
		
		foreach($permalinks as $permalink) {
			$deletePermalinks = array_diff($deletePermalinks, array($permalink[1]));
			
			$disabledPlugins = $submittedPlugins[$permalink[2]][0];
			$enabledPlugins = $submittedPlugins[$permalink[2]][1];
			$disabledMobilePlugins = $submittedPlugins[$permalink[2]][2];
			$enabledMobilePlugins = $submittedPlugins[$permalink[2]][3];
			$disabledGroups = $submittedPlugins[$permalink[2]][4];
			$enabledGroups = $submittedPlugins[$permalink[2]][5];
			$disabledMobileGroups = $submittedPlugins[$permalink[2]][6];
			$enabledMobileGroups = $submittedPlugins[$permalink[2]][7];
				
			$secure=0;
			if (preg_match('/^.{1,5}:\/\//', $permalink[1], $matches)) {
				switch ($matches[0]) {
					case "https://":
						$secure=1;
						break;
					default:
						$secure=0;
				}
			}

			if (is_numeric($permalink[0])) {
				$postExists = ($wpdb->get_var($wpdb->prepare("SELECT count(*) FROM ".$wpdb->prefix."po_plugins WHERE pl_id=%d", $permalink[0])) > 0) ? 1 : 0;
			} else {
				$postExists = 0;
			}
			
			$permalink[1] = preg_replace('/^.{1,5}:\/\//', '', $permalink[1]);
			
			$permalinkNoArgs = preg_replace('/\?.*$/', '', $permalink[1]);
			
			$disabledPluginsAfterParent = array();
			$enabledPluginsAfterParent = array();
			$disabledGroupsAfterParent = array();
			$enabledGroupsAfterParent = array();
			if ($permalink[1] != '' && get_option("PO_fuzzy_url_matching") == "1" && get_post_type($postID) != 'plugin_filter') {
				$fuzzyPluginList = $this->find_parent_plugins($postID, $permalink[1], 0, $secure, $permalink[2]);
				foreach ($disabledPlugins as $plugin) {
					if (!in_array($plugin, $fuzzyPluginList['plugins']['disabled_plugins'])) {
						$disabledPluginsAfterParent[] = $plugin;
					}
				}

				foreach ($enabledPlugins as $plugin) {
					if (!in_array($plugin, $fuzzyPluginList['plugins']['enabled_plugins'])) {
						$enabledPluginsAfterParent[] = $plugin;
					}
				}

				foreach ($disabledGroups as $group) {
					if (!in_array($group, $fuzzyPluginList['plugins']['disabled_groups'])) {
						$disabledGroupsAfterParent[] = $group;
					}
				}

				foreach ($enabledGroups as $group) {
					if (!in_array($group, $fuzzyPluginList['plugins']['enabled_groups'])) {
						$enabledGroupsAfterParent[] = $group;
					}
				}

				if (sizeof($disabledPluginsAfterParent) == 0 && sizeof($enabledPluginsAfterParent) == 0 && sizeof($disabledGroupsAfterParent) == 0 && sizeof($enabledGroupsAfterParent) == 0) {
					$disabledPlugins = array();
					$enabledPlugins = array();
					$disabledGroups = array();
					$enabledGroups = array();
				}

				$disabledMobilePluginsAfterParent = array();
				$enabledMobilePluginsAfterParent = array();
				$disabledMobileGroupsAfterParent = array();
				$enabledMobileGroupsAfterParent = array();
				$fuzzyMobilePluginList = $this->find_parent_plugins($postID, $permalink[1], 1, $secure, $permalink[2]);
				foreach ($disabledMobilePlugins as $plugin) {
					if (!in_array($plugin, $fuzzyMobilePluginList['plugins']['disabled_plugins'])) {
						$disabledMobilePluginsAfterParent[] = $plugin;
					}
				}

				foreach ($enabledMobilePlugins as $plugin) {
					if (!in_array($plugin, $fuzzyMobilePluginList['plugins']['enabled_plugins'])) {
						$enabledMobilePluginsAfterParent[] = $plugin;
					}
				}

				foreach ($disabledMobileGroups as $group) {
					if (!in_array($group, $fuzzyMobilePluginList['plugins']['disabled_groups'])) {
						$disabledMobileGroupsAfterParent[] = $group;
					}
				}

				foreach ($enabledMobileGroups as $group) {
					if (!in_array($group, $fuzzyMobilePluginList['plugins']['enabled_groups'])) {
						$enabledMobileGroupsAfterParent[] = $group;
					}
				}

				if (sizeof($disabledMobilePluginsAfterParent) == 0 && sizeof($enabledMobilePluginsAfterParent) == 0 && sizeof($disabledMobileGroupsAfterParent) == 0 && sizeof($enabledMobileGroupsAfterParent) == 0) {
					$disabledMobilePlugins = array();
					$enabledMobilePlugins = array();
					$disabledMobileGroups = array();
					$enabledMobileGroups = array();
				}
			}
			
			$dirCount = substr_count($permalink[1], "/");
			
			if (sizeof($enabledPlugins) > 0 || sizeof($disabledPlugins) > 0 || sizeof($enabledMobilePlugins) > 0 || sizeof($disabledMobilePlugins) > 0 || sizeof($enabledGroups) > 0 || sizeof($disabledGroups) > 0 || sizeof($enabledMobileGroups) > 0 || sizeof($disabledMobileGroups) > 0 || get_post_type($postID) == "plugin_filter" || $ptOverride == 1) {
				if ($postExists > 0) {
					$wpdb->update($wpdb->prefix."po_plugins", array("permalink"=>$permalink[1], "permalink_hash"=>md5($permalinkNoArgs), "permalink_hash_args"=>md5($permalink[1]), "children"=>$affectChildren, "pt_override"=>$ptOverride, "enabled_plugins"=>serialize($enabledPlugins), "disabled_plugins"=>serialize($disabledPlugins), "enabled_mobile_plugins"=>serialize($enabledMobilePlugins), "disabled_mobile_plugins"=>serialize($disabledMobilePlugins), "enabled_groups"=>serialize($enabledGroups), "disabled_groups"=>serialize($disabledGroups), "enabled_mobile_groups"=>serialize($enabledMobileGroups), "disabled_mobile_groups"=>serialize($disabledMobileGroups), "secure"=>$secure, "post_type"=>get_post_type($postID), "status"=>$postStatus, "post_priority"=>$postPriority, "dir_count"=>$dirCount), array("pl_id"=>$permalink[0]));
				} else {
					$wpdb->insert($wpdb->prefix."po_plugins", array("post_id"=>$postID, "permalink"=>$permalink[1], "permalink_hash"=>md5($permalinkNoArgs), "permalink_hash_args"=>md5($permalink[1]), "children"=>$affectChildren, "pt_override"=>$ptOverride, "enabled_plugins"=>serialize($enabledPlugins), "disabled_plugins"=>serialize($disabledPlugins), "enabled_mobile_plugins"=>serialize($enabledMobilePlugins), "disabled_mobile_plugins"=>serialize($disabledMobilePlugins), "enabled_groups"=>serialize($enabledGroups), "disabled_groups"=>serialize($disabledGroups), "enabled_mobile_groups"=>serialize($enabledMobileGroups), "disabled_mobile_groups"=>serialize($disabledMobileGroups), "secure"=>$secure, "post_type"=>get_post_type($postID), "status"=>$postStatus, "post_priority"=>$postPriority, "dir_count"=>$dirCount, 'user_role'=>$permalink[2]));
				}
			} else if ($postExists == 1) {
				$deletePluginQuery = "DELETE FROM ".$wpdb->prefix."po_plugins WHERE pl_id=%d";
				$wpdb->query($wpdb->prepare($deletePluginQuery, $permalink[0]));
			}
		}

		foreach($deletePermalinks as $deletePermalink) {
			$secure=0;
			if (preg_match('/^.{1,5}:\/\//', $deletePermalink, $matches)) {
				switch ($matches[0]) {
					case "https://":
						$secure=1;
						break;
					default:
						$secure=0;
				}
			}

			$permalink = preg_replace('/^.{1,5}:\/\//', '', $deletePermalink);

			$deletePluginQuery = "DELETE FROM ".$wpdb->prefix."po_plugins WHERE permalink=%s AND secure=%d AND post_id=%d";
			$wpdb->query($wpdb->prepare($deletePluginQuery, $permalink, $secure, $postID));
		}
	}


	function get_available_roles() {
		if (get_option("PO_disable_plugins_by_role") == '1') {
			$enabledRoles = get_option("PO_enabled_roles");
			$availableRoles = array();
			
			if (function_exists('get_editable_roles')) {
				$editableRoles = get_editable_roles();
				foreach($enabledRoles as $roleID) {
					$availableRoles[$roleID] = $editableRoles[$roleID]['name'];
				}
			} else {
				foreach($enabledRoles as $roleID) {
					$availableRoles[$roleID] = '';
				}
			}
			

			$availableRoles = array_merge(array('_'=>'Not Logged In', '-'=>'Default Logged In'), $availableRoles);
		} else {
			$availableRoles = array('_'=>'All Users');
		}

		return $availableRoles;
	}
	
	function get_submitted_plugin_lists($role) {
		global $wpdb;
		$returnPluginArray = array();
		
		$sql = "SELECT disabled_plugins, disabled_mobile_plugins, disabled_groups, disabled_mobile_groups FROM ".$wpdb->prefix."po_plugins WHERE post_type='global_plugin_lists' AND post_id=0";
		$pluginLists = $wpdb->get_row($sql, ARRAY_A);
		
		if ($pluginLists === null) {
			$globalPlugins = array();
		} else {
			$globalPlugins = @unserialize($pluginLists['disabled_plugins']);
		}

		$checkPluginList = (isset($this->postedData['PO_disabled_std_plugin_list'][$role])) ? $this->sanitize_post_array($this->postedData['PO_disabled_std_plugin_list'][$role]) : '';
		
		$tempPluginLists = $this->create_plugin_lists($checkPluginList, $globalPlugins);
		##Add plugin lists to return array
		$returnPluginArray[] = $tempPluginLists[0];
		$returnPluginArray[] = $tempPluginLists[1];
		
		
		### Mobile plugins
		if (get_option('PO_disable_plugins_mobile') == 1) {
			if ($pluginLists === null) {
				$globalMobilePlugins = array();
			} else {
				$globalMobilePlugins = @unserialize($pluginLists['disabled_mobile_plugins']);
			}
			$checkPluginList = (isset($this->postedData['PO_disabled_mobile_plugin_list'][$role])) ? $this->sanitize_post_array($this->postedData['PO_disabled_mobile_plugin_list'][$role]) : '';
			
			##Add plugin lists to return array
			$tempPluginLists = $this->create_plugin_lists($checkPluginList, $globalMobilePlugins);
			$returnPluginArray[] = $tempPluginLists[0];
			$returnPluginArray[] = $tempPluginLists[1];
		} else {
			$returnPluginArray[] = array();
			$returnPluginArray[] = array();
		}


		
		##Groups
		if ($pluginLists === null) {
			$globalGroups = array();
		} else {
			$globalGroups = @unserialize($pluginLists['disabled_groups']);
		}
		$checkPluginList = (isset($this->postedData['PO_disabled_std_group_list'][$role])) ? $this->sanitize_post_array($this->postedData['PO_disabled_std_group_list'][$role]) : '';
		
		##Add plugin lists to return array
		$tempPluginLists = $this->create_plugin_lists($checkPluginList, $globalGroups);
		$returnPluginArray[] = $tempPluginLists[0];
		$returnPluginArray[] = $tempPluginLists[1];

		##Mobile Groups
		if (get_option('PO_disable_plugins_mobile') == 1) {
			if ($pluginLists === null) {
				$globalMobileGroups = array();
			} else {
				$globalMobileGroups = @unserialize($pluginLists['disabled_mobile_groups']);
			}
			$checkPluginList = (isset($this->postedData['PO_disabled_mobile_group_list'][$role])) ? $this->sanitize_post_array($this->postedData['PO_disabled_mobile_group_list'][$role]) : '';
		
			##Add plugin lists to return array
			$tempPluginLists = $this->create_plugin_lists($checkPluginList, $globalMobileGroups);
			$returnPluginArray[] = $tempPluginLists[0];
			$returnPluginArray[] = $tempPluginLists[1];
		} else {
			$returnPluginArray[] = array();
			$returnPluginArray[] = array();
		}

		return $returnPluginArray;
	}
	
	function delete_plugin_lists($postID) {
		global $wpdb;
		if ( !current_user_can( 'activate_plugins', $postID ) ) {
			return $postID;
		}
		if (is_numeric($postID)) {
			$deletePluginQuery = "DELETE FROM ".$wpdb->prefix."po_plugins WHERE post_id = %d";
			$wpdb->query($wpdb->prepare($deletePluginQuery, $postID));
		}
	}
	
	function recreate_plugin_order() {
		$sitewidePlugins = get_site_option('active_sitewide_plugins', array());
		$plugins = get_option("active_plugins");
		$pluginOrder = get_option("PO_saved_plugin_order", array());
		
		$newPluginOrder = array();
		if (is_array($pluginOrder) && count($pluginOrder) > 0) {
			$missingPlugins = array_diff($plugins, $pluginOrder);
		
			if (count($missingPlugins) > 0) {
				foreach($missingPlugins as $newPlugin) {
					if (!in_array($newPlugin, $pluginOrder)) {
						$alphaPluginOrder = $pluginOrder;
						$alphaPluginOrder[] = $newPlugin;
						usort($alphaPluginOrder, array($this, 'custom_sort_plugins'));
						$pluginPosition = array_search($newPlugin, $alphaPluginOrder);
						array_splice($pluginOrder, $pluginPosition, 0, array($newPlugin));
					}
				}
			}
			
			foreach ($pluginOrder as $newPlug) {
				if (array_key_exists($newPlug, $sitewidePlugins) && in_array($newPlug, $plugins) && !in_array($newPlug, $newPluginOrder)) {
					$newPluginOrder[] = $newPlug;
				}
			}

			foreach ($pluginOrder as $newPlug) {
				if (!array_key_exists($newPlug, $sitewidePlugins) && in_array($newPlug, $plugins) && !in_array($newPlug, $newPluginOrder)) {
					$newPluginOrder[] = $newPlug;
				}
			}
			
			if (sizeof(array_diff_assoc($plugins, $newPluginOrder)) > 0) {
				$pluginLoadOrder = array();
				$activePlugins = array();
				foreach ($plugins as $val) {
					if (!in_array($val, $activePlugins)) {
						$activePlugins[] = $val;
						$pluginLoadOrder[] = array_search($val, $newPluginOrder);
					}
				}
				array_multisort($pluginLoadOrder, $activePlugins);
				if (count(array_unique($plugins)) == count($activePlugins)) {
					update_option("active_plugins", $activePlugins);
					update_option("PO_saved_plugin_order", $activePlugins);
				}
			}
		}
	}

	function plugin_filter_sort($columns) {
		$custom = array(
			'taxonomy-filter_group' => 'taxonomy-filter_group',
			'PO_PF_permalink' => 'PO_PF_permalink'
		);
		return wp_parse_args($custom, $columns);
	}

	function register_taxonomy() {
		$labels = array(
			'name' => _x( 'Filter Groups', 'taxonomy general name' ),
			'singular_name' => _x( 'Filter Group', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Filter Groups' ),
			'all_items' => __( 'All Filter Groups' ),
			'parent_item' => __( 'Parent Filter Group' ),
			'parent_item_colon' => __( 'Parent Filter Group:' ),
			'edit_item' => __( 'Edit Filter Group' ),
			'update_item' => __( 'Update Filter Group' ),
			'add_new_item' => __( 'Add New Filter Group' ),
			'new_item_name' => __( 'New Filter Group Name' )
		);

		$settings = array(
			'hierarchical' => true,
			'public' => false,
			'capability_type' => 'filter_group',
			'show_admin_column' => true,
			'labels' => $labels,
			'show_ui' => true,
			'capabilities' => array('assign_terms'=>'edit_filter_group','manage_terms' => 'manage_filter_groups','edit_terms' => 'manage_filter_groups','delete_terms' => 'manage_filter_groups'),
			'rewrite' => array( 'slug' => 'filter_group' )
		);

		register_taxonomy('filter_group', array('plugin_filter'), $settings);
	}
	
	function register_type() {
		$labels = array(
			'name' => _x('Plugin Filters', 'post type general name'),
			'singular_name' => _x('Plugin Filter', 'post type singular name'),
			'add_new' => _x('Add Plugin Filter', 'neo_theme'),
			'add_new_item' => __('Add New Plugin Filter'),
			'edit_item' => __('Edit Plugin Filter'),
			'new_item' => __('New Plugin Filter'),
			'view_item' => __('View Plugin Filter'),
			'search_items' => __('Search Plugin Filter'),
			'not_found' =>  __('No Plugin Filters found'),
			'not_found_in_trash' => __('No Plugin Filters found in Trash'), 
			'parent_item_colon' => 'Parent Plugin Filter:',
			'parent' => 'Parent Plugin Filter'
		);
		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => false,
			'show_ui' => true, 
			'menu_icon' => $this->urlPath . '/image/po-icon-16x16.png', 		
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('custom-fields'),
			'capability_type' => 'plugin_filter',
			'capabilities' => array( 'delete_posts' => 'delete_plugin_filters' ),
			'show_in_menu' => 'Plugin_Organizer'
		); 
		register_post_type('plugin_filter',$args);
		
		$labels = array(
			'name' => _x('Plugin Groups', 'post type general name'),
			'singular_name' => _x('Plugin Group', 'post type singular name'),
			'add_new' => _x('Add Plugin Group', 'neo_theme'),
			'add_new_item' => __('Add New Plugin Group'),
			'edit_item' => __('Edit Plugin Group'),
			'new_item' => __('New Plugin Group'),
			'view_item' => __('View Plugin Group'),
			'search_items' => __('Search Plugin Group'),
			'not_found' =>  __('No PPlugin Groups found'),
			'not_found_in_trash' => __('No Plugin Groups found in Trash'), 
			'parent_item_colon' => 'Parent Plugin Group:',
			'parent' => 'Parent Plugin Group'
		);
		$args = array(
			'labels' => $labels,
			'public' => false,
			'publicly_queryable' => false,
			'show_ui' => false, 
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('custom-fields'),
			'capability_type' => 'plugin_group'
		); 
		register_post_type('plugin_group',$args);
	}
	
	function custom_updated_messages( $messages ) {
		global $post, $post_ID;
		$messages['plugin_filter'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __('Plugin Filter updated.'), esc_url( get_permalink($post_ID) ) ),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('Plugin Filter updated.'),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Plugin Filter restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Plugin Filter published.'), esc_url( get_permalink($post_ID) ) ),
			7 => __('theme saved.'),
			8 => sprintf( __('Plugin Filter submitted.'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __('Plugin Filter scheduled for: <strong>%1$s</strong>.'),
			  // translators: Publish box date format, see http://php.net/date
			  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __('Plugin Filter draft updated.'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		);

		$messages['plugin_group'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __('Plugin Group updated.'), esc_url( get_permalink($post_ID) ) ),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('Plugin Group updated.'),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Plugin Group restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Plugin Group published.'), esc_url( get_permalink($post_ID) ) ),
			7 => __('theme saved.'),
			8 => sprintf( __('Plugin Group submitted.'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __('Plugin Group scheduled for: <strong>%1$s</strong>.'),
			  // translators: Publish box date format, see http://php.net/date
			  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __('Plugin Group draft updated.'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		);
		return $messages;
	}

	function deactivated_plugin($plugin, $networkWide = null) {
		global $wpdb;
		if ($networkWide != null) {
			$sites = $wpdb->get_results("SELECT blog_id FROM ".$wpdb->base_prefix."blogs");
			foreach ($sites as $site) {
				if (switch_to_blog($site->blog_id)) {
					$activePlugins = get_option("active_plugins");
					$activePlugins = array_values(array_diff($activePlugins, array($plugin)));
					update_option('active_plugins', $activePlugins);
				}
			}
			restore_current_blog();
		}
	}

	function activated_plugin($plugin, $networkWide = null) {
		global $wpdb;
		if ($networkWide != null) {
			$sites = $wpdb->get_results("SELECT blog_id FROM ".$wpdb->base_prefix."blogs");
			foreach ($sites as $site) {
				if (switch_to_blog($site->blog_id)) {
					$activePlugins = get_option("active_plugins");
					if (!in_array($plugin, $activePlugins)) {
						$activePlugins[] = $plugin;
						update_option('active_plugins', $activePlugins);
					}
				}
			}
			restore_current_blog();
		} else {
			$pluginOrder = get_option("PO_saved_plugin_order", array());
			if (is_array($pluginOrder) && count($pluginOrder) > 0) {
				$alphaPluginOrder = $pluginOrder;
				$alphaPluginOrder[] = $plugin;
				usort($alphaPluginOrder, array($this, 'custom_sort_plugins'));
				$pluginPosition = array_search($plugin, $alphaPluginOrder);
				array_splice($pluginOrder, $pluginPosition, 0, array($plugin));
				update_option("PO_saved_plugin_order", $pluginOrder);
			}
		}
	}

	function update_post_status($newStatus, $oldStatus, $post) {
		global $wpdb;
		$postExists = ($wpdb->get_var($wpdb->prepare("SELECT count(*) FROM ".$wpdb->prefix."po_plugins WHERE post_id=%d", $post->ID)) > 0) ? 1 : 0;
		
		if ($postExists) {
			$postSettingsQuery = "SELECT * FROM ".$wpdb->prefix."po_plugins WHERE post_id = %d GROUP BY permalink, secure";
			$postSettings = $wpdb->get_results($wpdb->prepare($postSettingsQuery, $post->ID), ARRAY_A);
				
			foreach($postSettings as $currPostSettings) {
				if (get_post_type($post->ID) != 'plugin_filter') {
					$permalink = get_permalink($post->ID);
					$secure=0;
					if (preg_match('/^.{1,5}:\/\//', $permalink, $matches)) {
						switch ($matches[0]) {
							case "https://":
								$secure=1;
								break;
							default:
								$secure=0;
						}
					}
				} else {
					$permalink = $currPostSettings['permalink'];
					$secure = $currPostSettings['secure'];
				}

				$permalink = preg_replace('/^.{1,5}:\/\//', '', $permalink);
				
				$permalinkNoArgs = preg_replace('/\?.*$/', '', $permalink);
				
				$dirCount = substr_count($permalink, "/");
				
				$wpdb->update($wpdb->prefix."po_plugins", array("status"=>$newStatus, "permalink"=>$permalink, "permalink_hash"=>md5($permalinkNoArgs), "permalink_hash_args"=>md5($permalink), "secure"=>$secure, "dir_count"=>$dirCount), array("pl_id"=>$currPostSettings['pl_id']));
			}
		}
	}

	function fix_trailng_slash($permalink) {
		global $wpdb;
		if ($permalink == '' || get_option('PO_auto_trailing_slash') == 0) { return $permalink; }
		
		$wpDomain = preg_replace(array('/^(https?:\/\/)?/', '/\/$/'), array('',''), get_bloginfo('url'));
		$wpAdminURL = preg_replace('/^(https?:\/\/)?/', '', admin_url());
		
		$permalinkNoProtocol = preg_replace('/^(https?:\/\/)?/', '', $permalink);
		$filePath = preg_replace('/^(https?:\/\/)?'.preg_quote($wpDomain, '/').'\/?/', '', $permalink);
		
		##get unfiltered siteurl value directly from database since wordpress won't let you have it any other way.  This includes the trailing slash if set in the options.
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", 'siteurl' ) );
		$realSiteUrl = '';
		if (is_object($row)) {
			$realSiteUrl = preg_replace('/^(https?:\/\/)?/', '', $row->option_value);
			if ($_SERVER['HTTP_HOST'] == preg_replace('/\/$/', '', $realSiteUrl)) {
				$realSiteUrl = trailingslashit($realSiteUrl);
			}
		}
		if (!is_file(get_home_path() . $filePath) && !preg_match('/^'.preg_quote($wpAdminURL, '/').'/', $permalinkNoProtocol) && strpos($permalink, "?") === FALSE) {
			if (preg_replace('/\/$/', '', $realSiteUrl) == preg_replace('/\/$/', '', $permalinkNoProtocol)) {
				if (preg_match('/\/$/', $realSiteUrl)) {
					$permalink = trailingslashit($permalink);
				} else {
					$permalink = untrailingslashit($permalink);
				}
			} else {
				$permalink = user_trailingslashit($permalink);
			}
		}
		return $permalink;
	}

	function sort_posts($a, $b) {
		if ($a['post_type'] == 'plugin_filter' && $b['post_type'] != 'plugin_filter') {
			return 1;
		} else if($a['post_type'] != 'plugin_filter' && $b['post_type'] == 'plugin_filter') {
			return -1;
		} else {
			return 0;
		}
	}

	function prepare_in($sql, $vals, $replaceText='[IN]'){
		global $wpdb;
		$in_count = substr_count($sql, $replaceText);
		if ( $in_count > 0 ){
			$args = array( str_replace($replaceText, implode(', ', array_fill(0, count($vals), '%s')), str_replace('%', '%%', $sql)));
			// This will populate ALL the [IN]'s with the $vals, assuming you have more than one [IN] in the sql
			for ($i=0; $i < substr_count($sql, $replaceText); $i++) {
				$args = array_merge($args, $vals);
			}
			$sql = call_user_func_array(array($wpdb, 'prepare'), array_merge($args));
		}
		return $sql;
	}
	
	function custom_sort_plugins($a, $b) { 
		$aData = get_file_data($this->pluginDirPath.'/'.$a, array('Name' => 'Plugin Name'));
		$bData = get_file_data($this->pluginDirPath.'/'.$b, array('Name' => 'Plugin Name'));
		return strcasecmp($aData['Name'], $bData['Name']);
	}

	function sanitize_post_array($postedArray) {
		foreach($postedArray as $postedKey=>$postedVal) {
			$postedArray[$postedKey] = sanitize_text_field($postedVal);
		}
		return $postedArray;
	}
}
?>