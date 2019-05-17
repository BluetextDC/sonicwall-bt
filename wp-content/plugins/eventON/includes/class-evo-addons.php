<?php
/**
 * 
 * eventon addons class
 * connected from each addons
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Classes
 * @version     2.6.1
 */

if(class_exists('evo_addons')) return;

class evo_addons{

	private $addon_data;
	private $urls;
	private $notice_code;
	private $addon = false;

	function __construct($arr=''){
		// assign initial values for instance of addon
		$this->addon_data = $arr;
	
		// set up addon instance
		if(isset($arr['slug'])){
			$this->addon = new EVO_Product($arr['slug']);
		}

		// when first time addon installed and updated from old version
		$this->version_comparison();
		
		// once version change check is done run main updater
		if(is_admin()){
			$this->updater();
		}	
	}

	// check if addon updated or installed
		function version_comparison(){

			if(!$this->addon) return false;

			// new install
			if( !$this->addon->get_version() || 
				( $this->addon->get_version() && version_compare($this->addon->get_version() , $this->addon_data['version'], '<' ) && isset($this->addon_data['version']))
			){
				// update version
				$this->addon->set_version( $this->addon_data['version'] );

				do_action('evo_addon_version_change', $this->addon_data['version']);
			}
		}
		
	// return eventon version
		public function get_eventon_version(){
			global $eventon;
			return $eventon->version;
		}

	/// the MAIN updater function
		public function updater(){
			global $pagenow;
			
			// only for admin
			if(is_admin() && !empty($pagenow) && in_array($pagenow, array(
				'plugins.php',
				'update-core.php',
				'admin.php',
				'admin-ajax.php',				
				'plugin-install.php',				
			) ) ){
				
				$ADDON = new EVO_Product($this->addon_data['slug'], true);

				if( ($pagenow == 'admin.php' && isset($_GET['tab']) && $_GET['tab']=='evcal_4' )
					|| $pagenow!='admin.php'
				){
					
					// set up the new addon product for eventon					
					$ADDON->setup(
						array(
							'ID'=> (!empty($this->addon_data['ID'])? $this->addon_data['ID']: ''),
							'version'=>$this->addon_data['version'], 
							'slug'=>$this->addon_data['slug'],
							'plugin_slug'=>$this->addon_data['plugin_slug'],
							'name'=>$this->addon_data['name'],
							'guide_file'=> isset($this->addon_data['guide_file'])?
								$this->addon_data['guide_file']:
								(( isset($this->addon_data['plugin_path']) && file_exists($this->addon_data['plugin_path'].'/guide.php') )? 
								$this->addon_data['plugin_url'].'/guide.php':null),
						)
					);	
				}
			}
		}

	// Check for eventon compatibility
		function evo_version_check(){
			global $eventon;
			
			if( version_compare($eventon->version, $this->addon_data['evo_version']) == -1 ){
				$this->notice_code = '01';
				add_action('admin_notices', array($this, 'notice'));
				return false;
			}

			return true;
		}
		public function notice(){
			if( empty($this->notice_code) ) return false;
			?>
	        <div class="message error"><p><?php printf(__('EventON %s is disabled! - '), $this->addon_data['name']); echo $this->notice_message($this->notice_code);?></p></div>
	        <?php
		}
		public function notice_message($code){
			$decypher = array(
				'01'=>	$this->addon_data['name'].' need EventON version <b>'.$this->addon_data['evo_version'].'</b> or higher to work correctly, please update EventON.',
				'02'=>	'EventON version is older than what is suggested for this addon. Please update EventON.',
			);
			return $decypher[$code];
		}

	// Deactivate Addon from eventon products
		public function remove_addon(){
			$PROD = new EVO_Product_Lic($this->addon_data['slug']);
			return $PROD->deactivate();
		}
	


}

?>