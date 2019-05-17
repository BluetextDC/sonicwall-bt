<?php
/** 
 * EventON products class
 * @version 0.1
 */

class evo_prods{
	private $prods_data = false;
	protected static $_instance = null;

	public function __construct(){
		$this->set_prods_data();
	}

// setup one instance of eventon
		public static function instance(){
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

// update the addons
	public function update_addons(){
		$evo_addons = $this->get_prods_data();

		// site have eventon addons and its an array
		if(!empty($evo_addons) && is_array($evo_addons)){
			$active_plugins = get_option( 'active_plugins' );  
			
			//print_r($evo_addons);
			$new_addons = $evo_addons;

			// for each addon prod
			foreach($evo_addons as $addon=>$data){

				if($addon =='evo_subscription') continue;
				
				if(!is_array($new_addons[$addon])) continue;
				if(empty($addon)){
					unset($new_addons[$addon]);
					continue;
				} 

				foreach($data as $field=>$val){
					if(is_int($field)) unset($new_addons[$addon][$field]);
				}

				//echo is_array($new_addons[$addon])? 'this'.$addon.'<br/>':$addon;
				// addon actually doesn not exist in plugins
				if($addon!='eventon' && !in_array($addon.'/'.$addon.'.php', $active_plugins)){
					// change status to removed if addon doesnt exists anymore
					$new_addons[$addon]["status"] = 'removed';
				}
			}
			$this->update_prods_option($new_addons);
		}
	}

// PRODUCT DATA
	// set prods data
		function set_prods_data(){
			$products = $this->get_prods_option();

			if(empty($products)) return false;

			$this->prods_data = $products;
		}

	// get all prod data
	// @return false or array of data
		function get_prods_data(){
			$products = $this->get_prods_option();
			if(empty($products)) return $this->data_cruncher();
			return $products;
		}

	// get all data for single product
		function get_prod_data($slug){
			$data = $this->get_prods_data();
			if(empty($data))	return $this->data_cruncher();	
			if(!isset($data[$slug])) return false;
			return $data[$slug];
		}

	// crunch prod data if saved in older method
		private function data_cruncher(){
			$license = get_option('_evo_licenses');
			$addons = get_option('eventon_addons');

			// if both these exists
			if(!empty($license) && !empty($addons)){
				$data = array_merge($license, $addons);
				update_option('_evo_products', $data);
				
				delete_option('_evo_licenses');
				delete_option('eventon_addons');

			}elseif(!empty($license) && empty($addons)){
				
				update_option('_evo_products', $license);				
				delete_option('_evo_licenses');
			}

			if( empty($license) && empty($addons) ) return false;

			return get_option('_evo_products');

		}

	function add_new_prod($data){
		$prods = $this->get_prods_data();

		if(empty($prods)){
			$prods = $data;
		}else{
			$prods = array_merge($prods, $data);
		}
		$this->update_prods_option($prods);
	}
	function update_prods($slug, $data){
		$prods = $this->get_prods_data();

		if(empty($prods)){
			$prods = array($slug=>$data);
		}else{
			$prods[$slug] = $data;
		}

		$this->update_prods_option($prods);
	}

	private function get_prods_option(){
		return get_option('_evo_products');
	}
	private function update_prods_option($data){
		update_option('_evo_products',$data);
		$this->prods_data = $data;
	}

// get remote product information
	function get_remote_prods_data($force = false){

		// if last checked is within allowed check duration
		if( !$this->can_check_remote($force) ) return false;

		global $wp_version;
		$remote_data = array(); $latest_result = $results = '';

		// get subscription data
		$SUB = new EVO_Product_Lic('evo_subscription',false);
		$sval = $SUB->has_valid_subscription()?'yes':'no';

		$data = $this->get_prods_data();
		foreach($data as $slug=>$info){

			// skip products that doesnt have saved license key
			if( !isset($info['key'])) continue;

			// skip subscription product
			if($slug == 'evo_subscription') continue;

			$remote_data[$slug] = array(
				'slug'=>		$slug,
				'license_key'=> (isset($info['key'])? $info['key']: ''),
				'version'=> (isset($info['version'])? $info['version']: ''),
				'remote_validity'=> (isset($info['remote_validity'])? $info['remote_validity']: 'none'),
				'sub_validity'	=>$sval
			);
		}

		// /print_r($remote_data);
		// get eventon addon data from eventon remote server
		$args = array(
			'data' => $remote_data, 
			'requester_email'=> get_bloginfo('admin_email')
		);

		$request_string = array(
			'body' => array(
				'action' => 'evo_all_products', 
				'request' => serialize($args),
				'api-key' => md5(get_bloginfo('url'))
			),
			'user-agent' => 'WordPress/' . $wp_version . ';' . get_bloginfo('url')
		);	
 
		// SSL support
		$ssl = wp_http_supports( array( 'ssl' ) );

		// get the release information and store it
	        $request = wp_remote_post($this->get_evo_api_url(), $request_string);

	        if($force) echo 'Initiate wp_remote_post to '.$this->get_evo_api_url() .'.</br>';
	        
	        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
	        	$results = maybe_unserialize( wp_remote_retrieve_body( $request ) );
	        }else{
	        	if($force) echo 'wp_error '.$request->get_error_message() .'!<br/>';
	        }

	        // If the remote addon data sent
	        if($results != null ){ 

	        	$result_count = count($results->products);
	        	if($force) echo 'Received results for count: '.$result_count.'</br>';

	        	$prods = $this->get_prods_data();
	        	$newProducts = $prods? $prods: array();

	        	// for all the prods in the site
	        	foreach($prods as $slug=>$product){
					if(!empty($results->products[$slug])){
						$newProducts[$slug]['remote_version'] = $results->products[$slug]['version'];
						$newProducts[$slug]['tested'] = $results->products[$slug]['tested'];
						$newProducts[$slug]['requires'] = $results->products[$slug]['requires'];
						$newProducts[$slug]['last_updated'] = $results->products[$slug]['last_updated'];
						$newProducts[$slug]['lastchecked'] = $this->get_time_now();
						
						if(
							isset($results->products[$slug]['package']) &&
							version_compare( $newProducts[$slug]['version'], $newProducts[$slug]['remote_version'], '<' )
						){
							$newProducts[$slug]['package'] = $results->products[$slug]['package'];

							// include package expiration
							if(isset($results->products[$slug]['package_expiration']) ){
								$newProducts[$slug]['package_expiration'] = $results->products[$slug]['package_expiration'];
							}
						}
						if($force){ echo 'Results for: '.$slug .'<br/>'; print_r($results->products[$slug]); echo "</br>";}
					}else{
						if($force) echo 'No remote data for product: '.$slug .'!</br>';
					}
				}

				EVO_Error()->record_gen_log('Checked for remote updates', 'all', '',"Received {$result_count} updates");
				$this->update_prods_option($newProducts);
				
	        }else{
	        	if($force) echo 'Results output null!<br/>';
	        }

	    $this->save_last_checked();

	}

	// if its good to check remote for version information
	function can_check_remote($force = false){

		if($force) return true;

		$last_checked =  get_option('_evo_prods_last_check');

		if(empty($last_checked)) true;

		$now = current_time('timestamp');

		if( ($last_checked + (60*60*10)) <= $now ) return true;

		return false;
	}
	function save_last_checked(){
		update_option('_evo_prods_last_check', current_time('timestamp'));
	}

	// forcefully debug remote data to check if connections are able to make through
	function debug_remote_data(){

		if(isset($_REQUEST['page']) && $_REQUEST['page']=='eventon' && isset($_REQUEST['tab']) && $_REQUEST['tab']=='evcal_4' 
			&& isset($_REQUEST['task']) && $_REQUEST['task']=='force_remote_debug'
		){
			echo "<div style='padding:20px; font-family:courier'>";
			$results = $this->get_remote_prods_data(true);
			print_r($results);
			echo "</div>";
		}
		
	}

	function get_evo_api_url(){
		$rand = rand(1,5);
	   	return 'http://get.myeventon.com/index_'.$rand.'.php';		
	    //return 'http://get.myeventon.com/index_x.php';		
	}

	function get_time_now(){
		date_default_timezone_set("UTC"); 
		return time();
	}
}