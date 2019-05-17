<?php
/**
 * EventON Product class
 * @version 	2.5
 * @updated 	2016-2
 */
class EVO_Product{

	public $products;
	public $prod_loc;
	public $prod_name;
	public $prod = array();
	public $slug;

	public function __construct($slug, $init = false, $args=''){
		$this->slug = $slug;

		$this->init = $init;
		
		$this->load();	

		// The initiate codes that will only run once for each eventon product on all backend pages
		if($this->init && is_admin()){

			// API Update checking
	        add_filter('pre_set_site_transient_update_plugins', array(&$this, 'set_transient'));
	        add_filter('plugins_api', array(&$this, 'prod_information_for_wp'), 10, 3);
	        add_filter("upgrader_pre_install", array($this, "preInstall"));
	        add_filter("upgrader_post_install", array( $this, "postInstall" ), 10, 3 );

	      	$this->update_notice();
		}		
	}

	// load product data 
		function load(){
			// Load product data
			$data = EVO_Prods()->get_prods_data();
			
			if(empty($data) || empty($data[$this->slug]) || !isset($data[$this->slug])){
				$data[$this->slug] = array('slug'=>$this->slug);
			}

			$this->prod = $data[$this->slug];
			$this->prod_name = $this->get_prop('name')? $this->get_prop('name'): $this->slug;
			$plugin_slug = $this->get_prop('plugin_slug')? $this->get_prop('plugin_slug'): 
				$this->slug.'/'.$this->slug.'.php';

			if(!isset($this->prod['plugin_slug'])){
				$this->prod['plugin_slug'] = $plugin_slug;
				$this->set_prop('plugin_slug', $plugin_slug);
			}
		}

// SETUP
	// Product Updates
		// custom update notices
			function update_notice(){
				if($this->get_prop('version') && $this->get_prop('remote_version') && 
					version_compare( $this->get_prop('version'), $this->get_prop('remote_version'), '<' )
				){
					add_action( 'in_plugin_update_message-' . $this->get_prop('plugin_slug'), array($this, 'in_plugin_update_message'), 10, 2 );
				}
			}
			function in_plugin_update_message($plugin_data, $response){
				if($response->slug != $this->slug) return false;
				if($this->is_evo()){
					if($this->kriyathmakada('eventon')):
			    		$url = 'http://www.myeventon.com/documentation/update-eventon/';
			    		$redirect = sprintf( '<a href="%s" target="_blank">%s</a>', $url, __( 'update eventon via FTP', 'eventon' ) );
			    		echo '<br/><b>NOTE:</b> '.sprintf( ' ' . __( 'If you are unable to auto update please visit %s to learn how to update manually.', 'eventon' ), $redirect );
			    	else:
			    	
						$url = esc_url( ( is_multisite() ? network_admin_url( 'admin.php?page=eventon&tab=evcal_4' ) : admin_url( 'admin.php?page=eventon&tab=evcal_4' ) ) );
						$redirect = sprintf( '<a href="%s" target="_blank">%s</a>', $url, __( 'settings', 'eventon' ) );
						echo '<br/><b>NOTE:</b> '. sprintf( ' ' . __( 'To receive automatic updates license activation is required. Please visit %s to activate your EventON.', 'eventon' ), $redirect );
			    			// sprintf( ' <a href="http://go.wpbakery.com/faq-update-in-theme" target="_blank">%s</a>', __( 'Got EventON in theme?', 'eventon' ) );
									    	
			    	endif;
				}else{
					$url = 'http://www.myeventon.com/documentation/can-download-addon-updates/';
		    		$redirect = sprintf( '<a href="%s" target="_blank">%s</a>', $url, __( 'updating eventon addons', 'eventon' ) );
		    		
		    		echo '<br/><b>NOTE:</b> '.sprintf( ' ' . __( 'Please visit %s to learn how to update eventon addons.', 'eventon' ), $redirect );
				}
				
			}

		// Transients
		function force_package_update($action){			

			// if there is a new version and download package link exists
			if( $this->get_prop('version') && 
				$this->get_prop('remote_version') && 
				version_compare( $this->get_prop('version'), $this->get_prop('remote_version'), '<' ) &&
				$this->get_prop('package') 				
			){				
				$transient = false;

				// ITEM Base
				$this->plugindata_from_file();		
				$item = array(
					'id'	=> 'evo-'.$this->slug,
					'slug'	=> $this->slug,
					'plugin'=> $this->get_prop('plugin_slug'),
					'new_version'=> ($this->get_prop('remote_version')? $this->get_prop('remote_version'):''),
					'version'=> $this->get_prop('version'),
					'url'=> $this->get_prop('plugin_uri'),						
				);
				$plugin_slug = $this->get_prop('plugin_slug');

				if($action == 'append'){
					if(!$this->is_transient_ready_for_update()) return false;

					$transient = get_site_option('_site_transient_update_plugins');
					$transient_package = isset($transient->response[ $plugin_slug]->package)? 
						$transient->response[ $plugin_slug]->package: false;
					
					// if the transient have a package or package is not same as package on product
					if( (!$transient_package || $transient_package!=  $this->get_prop('package')) && $this->is_active() ){	
						$this->set_prop('transient_updated', current_time('timestamp'));				
						$item['package'] = $this->get_prop('package');
					}
				}

				if($action=='dettach'){
					$transient = get_site_option('_site_transient_update_plugins');
					$transient_package = isset($transient->response[ $plugin_slug]->package)? 
						$transient->response[ $plugin_slug]->package: false;
					if(!$transient_package) return false;

					$this->set_prop('package','');
					$item['package'] = '';
					$item['package_expiration'] = '';
				}

				if(!$transient) return false;

				$transient->checked[ $this->get_prop('plugin_slug')] = $this->get_prop('remote_version');
				$transient->response[ $this->get_prop('plugin_slug')] = (object) $item;
				update_site_option('_site_transient_update_plugins',$transient);

				EVO_Error()->record_gen_log('Transient Updated', $this->slug,'','Package '.$action);
				
			}
		}

		function is_transient_ready_for_update(){
			$last_update = $this->get_prop('transient_updated');
			if(!$last_update) return true;

			if( ($last_update + (3 * 86400)) < (current_time('timestamp'))) return true;
			return false;
		}

		function set_transient($transient){

			$this->plugindata_from_file();

			$item = array(
				'id'	=> 'evo-'.$this->slug,
				'slug'	=> $this->slug,
				'plugin'=> $this->get_prop('plugin_slug'),
				'new_version'=> ($this->get_prop('remote_version')? $this->get_prop('remote_version'):''),
				'version'=>$this->get_prop('version'),
				'url'=>$this->get_prop('plugin_uri')
			);	

			if($this->get_prop('package') && $this->is_active()){
				$item['package'] = $this->get_prop('package');
			}

			// if newer version available
			if($this->get_prop('version') && $this->get_prop('remote_version') && 
				version_compare( $this->get_prop('version'), $this->get_prop('remote_version'), '<' )
			){
				$transient->response[ $this->get_prop('plugin_slug') ] = (object) $item;
				unset($transient->no_update[ $this->get_prop('plugin_slug') ]);
			}else{
				$transient->no_update[ $this->get_prop('plugin_slug') ] = (object) $item;
				unset($transient->response[ $this->get_prop('plugin_slug') ]);
			}

            return $transient;
		}

		// product information for details lightbox
			public function prod_information_for_wp($false, $action, $response){

				// Load prod data from file
				$this->plugindata_from_file();
				
				// if nothing is found
				if(empty($response->slug) || $response->slug !== $this->slug) return $false;

				// plugin information
				$response->last_updated = ($this->get_prop('last_updated') ? $this->get_prop('last_updated'): null);
				$response->slug = 		$this->get_prop('plugin_slug');
				$response->name  = 		$this->get_prop('name');
				$response->plugin_name  = $this->get_prop('name');
				$response->version = 	$this->get_prop('remote_version');
				$response->author = 	'<a href="http://www.ashanjay.com">Ashan Jay</a>';
				$response->homepage = 	($this->get_prop('plugin_uri') ? $this->get_prop('plugin_uri'): null);
				$response->requires =  	($this->get_prop('requires') ? $this->get_prop('requires'): null);
				$response->tested =  	($this->get_prop('tested') ? $this->get_prop('tested'): null);
				$response->rating =  	(4.43*20);			
				$response->num_ratings =  	1520;
				$response->contributors =  	array('ashanjay'=>'ashanjay');

				// setup 
				$EVO_Plugin_Data = new EVO_Plugins_API_Data();
					
				$plugin_data = $EVO_Plugin_Data->get_data( $this->prod_name, $this->slug );

				foreach(array(
					'description',
					'register_license',
					'changelog',
					'latest_news',
					'eventon_reviews',
					'installation'
				) as $field){
					if(!isset($plugin_data[$field])) continue;
					
					$section[$field] = $plugin_data[$field];
				}
				
				
				// append the sections
				$response->sections = $section;

				// This is our release download zip file
				$response->download_link = ($this->get_prop('package'))? $this->get_prop('package'): null;
	 
		        return $response; 
			}

		// additional checks
			// perform check before install
				public function preInstall($true, $args = null) {
					// Check to see if the plugin was previously installed
					$this->pluginActivated = is_plugin_active( $this->get_prop('plugin_slug') );

				    return $true;
			    }
			// Perform additional actions to successfully install our plugin
			    public function postInstall($true, $hook_extra, $result) {
					global $wp_filesystem;
					$pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname( $this->get_prop('plugin_slug') );
					$wp_filesystem->move($result['destination'], $pluginFolder);
					$result['destination'] = $pluginFolder;

					// Re-activate plugin if needed
					if ($this->pluginActivated) {
					    $activate = activate_plugin( $this->get_prop('plugin_slug') );
					}
			        return $result;
			    }

	// set up eventon product
		function setup($args){
			$data = EVO_Prods()->get_prod_data($this->slug);
				
			if($data){

				// update prod data that might be updated from initial args
				$new_data = false;
				foreach(array('version','plugin_slug','guide_file', 'ID') as $field){
					// if values are not existing currently
					if(!isset($data[$field]) && isset($args[$field]) ){
						$this->set_prop($field, $args[$field]); $new_data = true;
						continue;
					} 							

					// if values are not same
					if( isset($args[$field]) && isset($data[$field]) && $data[$field] !=  $args[$field] ){
						$this->set_prop($field, $args[$field]); $new_data = true; continue;
					}
				}

				// load product data again
				$this->prod = ($new_data)? EVO_Prods()->get_prod_data($this->slug): $data;

			}else{
				$this->new_product($args);
			}
		}

	// get addon data from the file
		function plugindata_from_file(){
			if(file_exists(AJDE_EVCAL_DIR.'/'.$this->slug.'/'.$this->slug.'.php')){
				if(!function_exists('get_plugin_data')) return;
				
				$this->prod_loc = get_plugin_data(AJDE_EVCAL_DIR.'/'.$this->slug.'/'.$this->slug.'.php');
				$this->prod_name = isset($this->prod_loc['Name'])? $this->prod_loc['Name']: $this->prod_name;

				foreach(array(
					'PluginURI'=>'plugin_uri',
					'AuthorName'=>'author_name',
					//'Version'=>'version',
				) as $field=>$var){
					if(!isset($this->prod_loc[$field])) continue;

					if($this->get_prop($field)) continue;					
					$this->set_prop($var, $this->prod_loc[$field], false );
				}

				$this->save();
			}
		}

	// add new eventon product OR create eventon product for the
	// first time on first time installations
		public function new_product(){			
			$array = array(
				$this->slug=>array(
					'ID'=> 			(isset($this->args['ID'])?$this->args['ID']:''),
					'name'=> 		(isset($this->args['name'])?$this->args['name']:''),				
					'slug'=>		$this->slug,
					'plugin_slug'=>	(isset($this->args['plugin_slug'])?$this->args['plugin_slug']:$this->slug),	
					'plugin_uri'=>'',
					'version'=>		(isset($this->args['version'])?$this->args['version']:''),				
					'remote_version'=>'',
					'lastchecked'=>'',
					'status'=>'inactive',
					'instance'=>'',
					'remote_validity'=>'none',
					'email'=>'',
					'key'=>'',
					'siteurl'=>get_site_url(),				
					'guide_file'=>(!empty($this->args['guide_file'])? $this->args['guide_file']: null),
					'package'=>'',
					'requires'=>'',
					'last_updated'=>'',
					'tested'=>'',
					'file'=>'',					
				)
			);

			// set new product properties for class
			$this->prod = $array[$this->slug];

			EVO_Prods()->add_new_prod($array);			
		}

	// product data related
		// update with new remote version
			public function update_remote_version($slug, $remote_version, $lastchecked=false){
				
				// compare versions
				$has_updates = version_compare( $this->get_prop('version'), $remote_version, '<' )? true:false;

				if($has_updates)
					$this->set_prop('remote_version', $remote_version, false);

				$this->set_prop('has_new_updates', $has_updates);

				// last check update
				if($lastchecked){
					date_default_timezone_set("UTC"); 
					$this->set_prop('lastchecked', time() );
				}
			}
				
		// update last check time for new version
			public function update_lastchecked(){				
				date_default_timezone_set("UTC"); 
				$this->set_prop('lastchecked', time() );
			}

		// update any given fiels 
			function set_prop($prop, $value, $update = true){
				$this->prod[$prop] = $value;
				if($update){
					EVO_Prods()->update_prods($this->slug, $this->prod);
				} 
			}
			function save(){
				EVO_Prods()->update_prods($this->slug, $this->prod);
			}
			function get_prop($prop){
				return isset($this->prod[$prop])? $this->prod[$prop]: false;
			}

// RETURNS	
	function is_evo(){
		return $this->slug=='eventon'? true: false;
	}
	// check if there is new version available
	function has_update(){
		if(!$this->get_prop('remote_version')) return false;
		if(!$this->get_prop('version')) return false;

		return version_compare( $this->get_prop('version'), $this->get_prop('remote_version'), '<' )? true: false;
	}
	// get eventon product information array by product slug
	public function get_product_array(){
		return $this->prod;
	}
	public function get_license_status(){			
		$prop = $this->get_prop('status');

		if($prop) return $prop;

		$this->set_prop('status', 'inactive');
		return $this->get_prop('status');
	}
	function is_active(){
		$status = $this->get_prop('status');
		return $status=='active'? $status: false;
	}

	function get_version(){
		return $this->get_prop('version');
	}
	function set_version($value){
		$this->set_prop('version', $value);
	}

	public function kriyathmakada(){			
		return ($this->get_prop('status') && $this->get_prop('status') == 'active' && $this->get_prop('key') ) ?
			true: false;
	}

	// eventon products kriyathmakada kiya baleema		
	public function kriyathmaka_localda(){
		if(!$this->get_prop('remote_validity')) false;
		$local = $this->get_prop('remote_validity') == 'local'? true: false;

		return ($this->kriyathmakada() && $local) ? true: false;
	}
	public function akriyamath_niwedanaya(){
		$url = esc_url( ( is_multisite() ? network_admin_url( 'admin.php?page=eventon&tab=evcal_4' ) : admin_url( 'admin.php?page=eventon&tab=evcal_4' ) ) );
		$redirect = sprintf( '<a href="%s" target="_blank">%s</a>', $url, __( 'settings', 'eventon' ) );
		return sprintf( ' ' . __( 'EventON license need activated for this to work. Please visit %s to activate your EventON.', 'eventon' ), $redirect );
	}

	public function get_current_version($slug){
		return $this->get_prop('version');
	}

	public function get_remote_version(){
		
		$remote_version = $this->get_prop('remote_version');
		$version = $this->get_prop('version');

		if(empty($version) && empty($remote_version)) return false;

		if(version_compare($version, $remote_version,'>')){
			$this->set_prop('remote_version', $version);
			return $version;
		}

		return $remote_version;
	}

	// checking for updates
	public function can_check_remotely($product = ''){

		// if doing force check then proceed
		// @updated 2.3.19
		if(!empty($_REQUEST['force-check']) && $_REQUEST['force-check']=='1')
		 	return true;

		$lastchecked = $this->get_prop('lastchecked');

		if( $lastchecked ){

			date_default_timezone_set("UTC"); 
			$timenow = time();
			$lastchecked = (int)$lastchecked;

			$checking_gap = 86400; // every 24 hours 3600x 24

			return ( ($lastchecked+$checking_gap)<$timenow)? true:false;

		}
		return true;
	}

	// check if addon is installed in the site
	function is_installed(){
		$active_plugins = get_option( 'active_plugins' );  

		if(in_array( $this->prod['plugin_slug'] , $active_plugins)) return true;

		return false;

	}

}