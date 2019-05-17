<?php
/**
 * WordPress wrapper class for the Envato marketplaces API.
 * 
 * This is based off @Jeffrey Way's Wrapper Class 
 *
 * @author Chris Kelley <chris@organicbeemedia.com>
 * @author-url http://chrisakelley.me
 * @twitter chrisakelley
 * @url somewhere on github
 * @version 0.0001 - yeah its that fresh!
 * @license GPL v2
*/ 

// Exit if accessed directly 
defined('_VALID_AI') or die('Direct Access to this location is not allowed.');
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'cw_WP_EnvatoAPI' ) ) :

final class cw_WP_EnvatoAPI {
	
	private $username;
	
	private $api_key;
		
	/**
	 * Construct function, allows you to set username and api key upon instantiation
	 * 
	 * @since 1.0
	 * @access public
	 * @param mixed $username (default: null)
	 * @param mixed $api_key (default: null)
	 * @return void
	 */
	public function __construct( $username = null , $api_key = null ){
	
		if( isset( $username ) ){
			
			$this->username = $username;
			
		}		
		
		if( isset( $api_key ) ){ 
		
			$this->api_key = $api_key;
			
		}
		
	}
	
	/**
	 * Set Envato username
	 * 
	 * http://themeforest.net/user/USERNAME/
	 *
	 * @since 1.0
	 * @access public
	 * @param mixed $username
	 * @return void
	 */
	public function set_username( $username ){
		
		if( $username == ''){

			$this->errors()->add( 'cw_empty_username', __('Please enter a username.','cw_e_api') );
	
		}
		
		$this->username = $username;
		
	}
	
	/**
	 * Set Envato API key
	 * 
	 * http://themeforest.net/user/USERNAME/api_keys/edit
	 *
	 * @since 1.0
	 * @access public
	 * @param mixed $api_key
	 * @return void
	 */
	public function set_api_key( $api_key ){
		
		if( $api_key == ''){
		
			$this->errors()->add( 'cw_empty_api_key', __('Please enter a API key','cw_e_api') );
	
		}
		
		$this->api_key = $api_key;
		
	}
	
	/**
	 * Helper function for our errors , requires WP_Error class
	 * 
	 * http://codex.wordpress.org/Class_Reference/WP_Error
	 *
	 * @thanks @pippinsplugins 
	 * @since 1.0
	 * @uses WP_Error 
	 * @access private
	 * @return void
	 */
	private function errors(){
	
		$wp_error;
    
		return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	
	}
	
	/**
	 * Helper function to make remote requests, makes use of WordPress's HTTP API
	 * 
	 * http://codex.wordpress.org/HTTP_API
	 *
	 * @since 1.0
	 * @uses wp_remote_request
	 * @uses wp_remote_retrieve_body
	 * @uses wp_remote_retrieve_response_code
	 * @uses wp_remote_retrieve_response_message
	 * @access private
	 * @param string $url
	 * @return object
	 */
	private function remote_request( $url ){
	
		if( empty( $url ) ) {
			
			$this->errors()->add( 'cw_empty_url', __('Empty URL','cw_e_api') );
			
		}
	
		$http = wp_remote_request( $url );
		
		$data = json_decode(  wp_remote_retrieve_body( $http ) );
		
		$response_code = wp_remote_retrieve_response_code( $http );
		
		$response_message = wp_remote_retrieve_response_message( $http );
		
		if( $response_code !== 200){
		
			$this->errors()->add( 'cw_remote_response_error', $response_message );
			
		} else {
			
			return $data;
	
		}
		
	}
	
	/**
	 * Set the cache using the Transient API.
	 *
	 * http://codex.wordpress.org/Transients_API
	 * 
	 * @since 1.0
	 * @access private
	 * @param string $transient (default: '')
	 * @param string $url (default: '')
	 * @param int $timeout (default: '' ) 24 hours
	 * @return void
	 */
	private function set_cache( $transient, $url, $timeout){
	
		if($timeout= ''){
		
			$timeout = 60*60*24;
		}
	
		if( $transient == ''){
		 
			$this->errors()->add( 'cw_empty_trasient', __('There is no transient set','cw_e_api') );
		 
		}

		if( $url == '' ){
		
			$this->errors()->add( 'cw_empty_url', __('Empty URL','cw_e_api') );
		
		}
			
		$results = get_transient( $transient );
	
		if( false === $results ){
			
			$results = $this->remote_request( $url );
			
			set_transient( $transient, $results, $timeout );
			
		}
		
		return $results;

	
	}
	
	/**
	 * Clear the cache aka delete transient 
	 *
	 * http://codex.wordpress.org/Function_Reference/delete_transient
	 * 
	 * @since 1.0
	 * @access public
	 * @param mixed $transient
	 * @return void
	 */
	private function clear_cache( $transient ){
		
		delete_transient( $transient );
		
	}	
	
	/**
	 * Function to retrive private data from the API.
	 * 
	 * @since 1.0
	 * @access private
	 * @param mixed $set
	 * @param mixed $username
	 * @param mixed $purchase_code (default: null)
	 * @param bool $allow_cache (default: false)
	 * @param mixed $timeout (default: '')
	 * @return void
	 */
	private function private_user_data( $set, $set_data = '', $do_cache = false, $timeout = '' ){
		
		
		if (! isset($set)){ 
	
			$this->errors()->add( 'cw_no_set', __('Missing Set Data','cw_e_api') );
			
		}
		
		if( ! isset($this->username) ){
					
			$this->errors()->add( 'cw_no_username', __('Missing Username','cw_e_api') );

		}
		
		if ( ! isset($this->api_key)){
		
			$this->errors()->add( 'cw_no_api', __('Missing API key','cw_e_api') );
			
		}
		
		$url = 'http://marketplace.envato.com/api/edge/' . $this->username . '/' . $this->api_key . '/' . $set . ':' . $set_data . '.json';
		
		$transient = $this->username . '_' . $set . $set_data;
		
		if ( $do_cache ) {
		
			$results = $this->set_cache( $transient, $url, $timeout );

		} else {
			
			$results = $this->remote_request( $url );
			
		}
		
		if ( $this->errors()->get_error_code() ){
	
			$this->clear_cache( $transient );
			
			$errors = $this->errors()->get_error_messages();

			return $errors;
		
		} 
		
		return $results->$set;
		
	}
	
	/**
	 * Pulls the balance from the Vitals, no to pull the other information 
	 * 
	 * http://marketplace.envato.com/api/edge/USERNAME/API-KEY/vitals.json
	 *
	 * @since 1.0
	 * @uses private_user_data
	 * @access public
	 * @param bool $do_cache (default: true)
	 * @param string $timeout (default: '')
	 * @return void
	 */
	public function balance( $do_cache = true, $timeout = '' ){
		
		$vitals = $this->private_user_data('vitals', '' , $do_cache, $timeout);
	
		return $vitals->balance;
	
	}
	/**
	 * Pulls the earnings by month
	 *
	 * http://marketplace.envato.com/api/edge/USERNAME/API-KEY/earnings-and-sales-by-month.json 
	 * 
	 * @access public
	 * @param mixed $api_key
	 * @return void
	 */
	public function earnings_by_month( $do_cache = true, $timeout = '' ){
		
		$earnings = $this->private_user_data('earnings-and-sales-by-month', '' , $do_cache, $timeout);
		
		return $earnings;
		
	}
	/**
	 * statement function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @param mixed $api_key
	 * @return void
	 */
	public function statement(  $do_cache = true, $timeout = ''  ){
			
		$statement = $this->private_user_data('statement', '' , $do_cache, $timeout);
		
		return $statement;	
		
	}
	/**
	 * recent_sales function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @param mixed $api_key
	 * @param mixed $limit
	 * @return void
	 */
	public function recent_sales( $do_cache = true, $timeout = '' ){
		
		$recent = $this->private_user_data('recent-sales', '' , $do_cache, $timeout);
		
		return $recent;	
	
	}
	/**
	 * account function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @param mixed $api_key
	 * @return void
	 */
	public function account( $do_cache = true, $timeout = ''){
		
		$account = $this->private_user_data('account', '' , $do_cache, $timeout);
		
		return $account;	
		
	}
	
	/**
	 * verify_purchase function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @param mixed $purchase_code
	 * @return void
	 */
	public function verify_purchase( $purchase_code, $do_cache = true, $timeout = '' ){
		
		$verify = $this->private_user_data('verify-purchase', $purchase_code , $do_cache, $timeout);
		
		return isset($verify->buyer) ? $verify : false;
	
	} 	
	
	/**
	 * download_purchase function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @param mixed $api_key
	 * @param mixed $purchase_code
	 * @return void
	 */
	public function download_purchase( $purchase_code, $do_cache = false, $timeout = '' ){
		
		$download = $this->private_user_data('download-purchase', $purchase_code , $do_cache, $timeout);
		
		return $download;
	
	}
	
	/**
	 * wp_download function.
	 * 
	 * @access public
	 * @return void
	 */
	public function wp_download( $item_id , $do_cache = false, $timeout = '' ){
		
		$wp_download = $this->private_user_data('wp-download', $item_id , $do_cache , $timeout);
		
		return $wp_download;
	}
	
	/**
	 * wp_list_themes function.
	 * 
	 * @access public
	 * @return void
	 */
	public function wp_list_themes( $purchase_code, $do_cache = true, $timeout = '' ){
		
		$wp_list = $this->private_user_data('wp-list-themes', $purchase_code , $do_cache , $timeout);

	}

	public function public_data($set, $set_data , $do_cache = false, $timeout = ''){
			
		if (! isset($set)){ 
	
			$this->errors()->add( 'cw_no_set', __('Missing Set','cw_e_api') );
			
		}
		
		if( ! isset($set_data) ){
					
			$this->errors()->add( 'cw_no_username', __('Missing Username','cw_e_api') );

		}
		$url = 'http://marketplace.envato.com/api/edge/'.$set.':'.$set_data.'.json';
		
		$transient = $set .'_.'. $set_data;
		
		if( $do_cache ){
		
			$results = $this->set_cache( $transient, $url, $timeout );

		} else {
			
			$results = $this->remote_request( $url );
			
		}
		
		if ( $this->errors()->get_error_code() ){
	
			$this->clear_cache( $transient );
			
			$errors = $this->errors()->get_error_messages();

			return $errors;
		
		} 
		
		return $results->$set;
	
		
	}

	/**
	 * Shows the number of files in the major categories of a particular site. Requires a site paramater, e.g. number-of-files:activeden
	 *
	 * http://marketplace.envato.com/api/edge/number-of-files:activeden.json
	 * 
	 * @access public
	 * @param mixed $marketplace
	 * @return void
	 */
	public function number_of_files($marketplace, $do_cache = true, $timeout = '' ) {
				
		$files =  $this->public_data('number-of-files', $marketplace, $do_cache, $timeout);

		return $files;
		
	}
	/**
	 * popular function.
	 * 
	 * @access public
	 * @param mixed $marketplace
	 * @return void
	 */
	public function popular( $marketplace, $do_cache = true, $timeout = ''  ){
		
		$popular =  $this->public_data('popular', $marketplace, $do_cache, $timeout);

		return $popular;	
	}
	/**
	 * collection function.
	 * 
	 * @access public
	 * @param mixed $collection_id
	 * @return void
	 */
	public function collection( $collection_id, $do_cache = true, $timeout = ''  ){
		
		$collection =  $this->public_data('collection', $collection_id, $do_cache, $timeout);

		return $collection;	
		
	}
	/**
	 * public_user_data function.
	 * 
	 * example url http://marketplace.envato.com/api/edge/user:collis.json
	 * 
	 * @access public
	 * @param mixed $username
	 * @return void
	 */
	private function public_user_data( $username, $do_cache = true, $timeout = '' ){
	
		$user =  $this->public_data('user', $username, $do_cache, $timeout);

		return $user;
	}
	

	/**
	 * featured function.
	 * 
	 * @access public
	 * @param mixed $narketplace
	 * @return void
	 */
	public function features( $marketplace, $do_cache = true, $timeout = '' ){
		
		$features =  $this->public_data('features', $marketplace, $do_cache, $timeout);

		return $features;	

	}
	/**
	 * new_files function.
	 * 
	 * @access public
	 * @param mixed $marketplace
	 * @param mixed $category
	 * @return void
	 */
	public function new_files( $marketplace, $category, $do_cache = true, $timeout = ''  ){
		
		$new_files =  $this->public_data('new-files', $marketplace .','. $category , $do_cache, $timeout);

		return $new_files;		
	}
		/**
	 * new_files_from_user function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @param mixed $marketplace
	 * @return void
	 */
	public function new_files_from_user( $username, $marketplace, $do_cache = true, $timeout = ''  ){
		
		$new_files =  $this->public_data('new-files-from-user', $username .','. $marketplace , $do_cache, $timeout);

		return $new_files;	
	}
	
	/**
	 * random_new_files function.
	 * 
	 * @access public
	 * @return void
	 */
	public function random_new_files( $marketplace, $do_cache = true, $timeout = '' ){
	
		$random_new =  $this->public_data('random-new-files', $marketplace, $do_cache, $timeout);

		return $random_new;		
	}
	/**
	 * item_details function.
	 * 
	 * @access public
	 * @param mixed $item_id
	 * @return void
	 */
	public function item_details(  $item_id, $do_cache = true, $timeout = '' ){
			
		$item =  $this->public_data('item', $item_id, $do_cache, $timeout);

		return $item;			
	}
	/**
	 * item_price function.
	 * 
	 * @access public
	 * @param mixed $item_id
	 * @return void
	 */
	public function item_prices( $item_id, $do_cache = true, $timeout = '' ){
		
		$item_price =  $this->public_data('item-prices', $item_id, $do_cache, $timeout);

		return $$item_price;
	}
	
	/**
	 * user_items_by_site function.
	 * 
	 * @access public
	 * @return void
	 */
	public function user_items_by_site(){
		
		$user_items =  $this->public_data('user-items-by-site', $username, $do_cache, $timeout);

		return $user_items;	
	
	}	
	
	//add search and total users.... aka quit being lazy and doing shit half ass
	//only been semi-tested 
	
}//End class

endif; //End if class exists

?>