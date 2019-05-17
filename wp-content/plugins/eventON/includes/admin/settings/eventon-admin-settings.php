<?php
/**
 * Functions for the settings page in admin.
 *
 * The settings page contains options for the EventON plugin - this file contains functions to display
 * and save the list of options.
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin/Settings
 * @version     2.2.28
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Store settings in this array */
global $eventon_settings;

if ( ! function_exists( 'eventon_settings' ) ) {
	
	// Settings page
	function eventon_settings() {
		global $eventon, $ajde;
		
		do_action('eventon_settings_start');

		$ajde_settings = new  ajde_settings('evcal_1');
				
		// Settings Tabs array
		$evcal_tabs = apply_filters('eventon_settings_tabs',array(
			'evcal_1'=>__('Settings', 'eventon'), 
			'evcal_2'=>__('Language', 'eventon'),
			'evcal_3'=>__('Styles', 'eventon'),
			'evcal_4'=>__('Licenses', 'eventon'),
			'evcal_5'=>__('Support', 'eventon'),
		));		
		
		// Get current tab/section
			$focus_tab = (isset($_GET['tab']) )? sanitize_text_field( urldecode($_GET['tab'])):'evcal_1';
			$current_section = (isset($_GET['section']) )? sanitize_text_field( urldecode($_GET['section'])):'';	

		// Update or add options
			$ajde_settings->evo_save_settings($focus_tab, $current_section);
			
		// Load eventon settings values for current tab
			$evcal_opt = $ajde_settings->get_current_tab_values('evcal_options_');	
		
		// activation notification
			$EVO_prod = new EVO_Product_Lic('eventon');
			if(!$EVO_prod->kriyathmakada()){
				echo '<div class="update-nag">'.__('EventON is not activated, it must be activated to use! <a href="'.get_admin_url().'admin.php?page=eventon&tab=evcal_4">Enter License Now</a>','eventon').'</div>';
			}

		// OTHER options
			$genral_opt = get_option('evcal_options_evcal_1');

// TABBBED HEADER	
	$ajde_settings->header_wraps(array(
		'version'=>get_option('eventon_plugin_version'),
		'title'=>__('EventON Settings','eventon'),
		'tabs'=>$evcal_tabs,
		'tab_page'=>'?page=eventon&tab=',
		'tab_attr_field'=>'evcal_meta',
		'tab_attr_pre'=>'evcal_',
		'tab_id'=>'evcal_settings'
	));	
?>	
<div class='evo_settings_box <?php echo (!empty($genral_opt['evo_rtl']) && $genral_opt['evo_rtl']=='yes')?'adminRTL':'';?>'>	
<?php
// SETTINGS SAVED MESSAGE
	$updated_code = (isset($_POST['settings-updated']) && $_POST['settings-updated']=='true')? '<div class="updated fade"><p>'.__('Settings Saved','eventon').'</p></div>':null;
	echo $updated_code;	
	
// TABS
switch ($focus_tab):	
	case "evcal_1":		
		// Event type custom taxonomy NAMES
		$event_type_names = evo_get_ettNames($evcal_opt[1]);
		$evt_name = $event_type_names[1];
		$evt_name2 = $event_type_names[2];

		$ajde_settings->settings_tab_start(array(
			'field_group'=>'evcal_field_group',
			'nonce_key'=>AJDE_EVCAL_BASENAME,
			'nonce_field'=>'evcal_noncename',
			'tab_id'=>'evcal_1',
			'classes'=>array('evcal_admin_meta'. 'evcal_focus'),
			'inside_classes'=> array('evo_inside')
		));		
					
			require_once(AJDE_EVCAL_PATH.'/includes/admin/settings/class-settings-settings.php');
			$settings = new evo_settings_settings($evcal_opt);
			
			$ajde->load_ajde_backender();
			print_ajde_customization_form($settings->content(), $evcal_opt[1]);
		
		$ajde_settings->settings_tab_end();
		?>

		<div class='evo_diag'>
			<!-- save settings -->
			<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" /> <a id='resetColor' style='display:none' class='evo_admin_btn btn_secondary'><?php _e('Reset to default colors','eventon')?></a><br/><br/>
			<a target='_blank' href='http://www.myeventon.com/support/'><img src='<?php echo AJDE_EVCAL_URL;?>/assets/images/myeventon_resources.png'/></a>
		</div>		
		</form>

		<div class="evo_lang_export">
			<?php
				$nonce = wp_create_nonce('evo_export_settings');
				// url to export settings
				$exportURL = add_query_arg(array(
				    'action' => 'eventon_export_settings',
				    'nonce'=>$nonce
				), admin_url('admin-ajax.php'));

			?>
			<h3><?php _e('Import/Export General EventON Settings','eventon');?></h3>
			<p><i><?php _e('NOTE: Make sure to save changes after importing. This will import/export the general settings saved for eventon.','eventon');?></i></p>

			<div class='import_box' id="import_box" style='display:none'>
				<span id="close">X</span>
				<form id="evo_settings_import_form" action="" method="POST" data-link='<?php echo AJDE_EVCAL_PATH;?> '>
					<input type="file" id="file-select" name="settings[]" multiple accept=".json" />
					<button type="submit" id="upload_settings_button"><?php _e('Upload','eventon');?></button>
				</form>
				<p class="msg" style='display:none'><?php _e('File Uploading','eventon');?></p>
			</div>
			<p>
				<a id='evo_settings_import' class='evo_admin_btn btn_triad'><?php _e('Import','eventon');?></a> 
				<a href='<?php echo $exportURL;?>' class='evo_admin_btn btn_triad'><?php _e('Export','eventon');?></a>
			</p>
		</div>
	
<?php  
	break;
		
	// LANGUAGE TAB
	case "evcal_2":		
			
		require_once(AJDE_EVCAL_PATH.'/includes/admin/settings/settings_language_tab.php');

		$settings_lang = new evo_settings_lang($evcal_opt);
		$settings_lang->get_content();
	
	break;
	
	// STYLES TAB
	case "evcal_3":
		
		echo '<form method="post" action="">';
		
		//settings_fields('evcal_field_group'); 
		wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' );
				
		// styles settings tab content
		require_once(AJDE_EVCAL_PATH.'/includes/admin/settings/settings_styles_tab.php');
	
	break;
	
	// ADDON TAB
	case "evcal_4":
		
		// Addons settings tab content
		require_once(AJDE_EVCAL_PATH.'/includes/admin/settings/settings_addons_tab.php');

	
	break;
	
	// support TAB
	case "evcal_5":
		
		// Addons settings tab content
		require_once(AJDE_EVCAL_PATH.'/includes/admin/settings/settings_troubleshoot_tab.php');

	
	break;
	
	
		
	// ADVANDED extra field
	case "extra":
	
		// advanced tab content
		require_once(AJDE_EVCAL_PATH.'/includes/admin/settings/settings_advanced_tab.php');		
		
	break;
	
		default:
			do_action('eventon_settings_tabs_'.$focus_tab);
		break;
		
endswitch;

echo "</div>";
echo "</div>";

	} // function
} // * function exists 

?>