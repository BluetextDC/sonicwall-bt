<?php
/**
 * Installation related functions and actions
 *
 * @author   AJDE
 * @category Admin
 * @package  eventon/Classes
 * @version  2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class evo_install {

	private static $evo_updates = array(
		'2.3.16'=>'updates/eventon-update-2.3.16.php',
		'2.3.22'=>'updates/eventon-update-2.3.22.php',
		'2.4.7'=>'updates/eventon-update-2.4.7.php',
	);

	public static function init(){
		//add_action('init', array( __CLASS__, 'check_version'),5);
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ), 5 );
	}

	// check eventon version and run the updater if required
		public static function install_actions(){
			$installed_version = get_option('eventon_plugin_version');
			if(empty($installed_version) || $installed_version != EVO()->version){

				self::update();
				do_action('eventon_updated');

				
				// redirect to welcome screen from eventon settings page
				if (  isset( $_GET['page'] ) && 'eventon' == $_GET['page'] && (isset($_REQUEST['type'] ) && $_REQUEST['type'] != 'bypass' || !isset($_REQUEST['type']) ) )
					wp_safe_redirect( admin_url( 'index.php?page=evo-about&evo-updated=true' ) );
			}

			// setup cron jobs
			self::create_cron_jobs();
		}


	// create cron jobs after clearning them
	// Will run on all admin pages
	private static function create_cron_jobs(){

		$crons = apply_filters('evo_schedule_cron', array(
			'evo_trash_past_events'=>array('frequency'=>'daily','callback'=>''),
			'evo_check_updates'=>array('frequency'=>'weekly','callback'=>''),
		));

		foreach($crons as $cron_hook=>$data){

			if (! wp_next_scheduled ( $cron_hook )){
				// echo $cron_hook;
				wp_schedule_event( time(), $data['frequency'], $cron_hook );
			} 				

			if(!empty($data['callback'])) add_action($cron_hook, $data['callback'] );

		}

		add_action('evo_check_updates', array(__CLASS__, 'check_updates'));

		do_action('evo_create_cron_jobs');
	}

	// check updates on remote
		function check_updates(){
			EVO_Prods()->get_remote_prods_data();
		}

	// Update EVO
		public static function update(){
			$current_evo_version = get_option('eventon_plugin_version');
			
			foreach ( self::$evo_updates as $version => $updater ) {
				if(version_compare( $current_evo_version, $version, '<' )){
					include($updater);
					self::update_evo_version($version);
				}
			}

			// after each version update to latest
			self::update_evo_version(EVO()->version);
		}

	// update eventon version to current
		private static function update_evo_version($version=null){

			$installed_version = get_option('eventon_plugin_version');
			$newversion = ( empty( $version ) ? EVO()->version : $version );
			if(empty($installed_version)){
				add_site_option( 'eventon_plugin_version', $newversion );
				update_option( 'eventon_plugin_version',$newversion );
			}else{
				update_site_option( 'eventon_plugin_version', $newversion );
				update_option( 'eventon_plugin_version',$newversion );
				
			}
		}

	// create pages that the plugin relies on 
		public static function create_pages(){
			include_once('admin/eventon-admin-functions.php');

			$pages = apply_filters('eventon_create_pages',array(
				'events_page' => array(
					'name'=> _x( 'event-directory', 'page_slug', 'eventon' ),
					'title'=> _x( 'Events', 'eventon' ),
					'content'=>'[add_eventon]'
				)
			));

			foreach ( $pages as $key => $page ) {
				eventon_create_page( esc_sql( $page['name'] ), 'eventon_' . $key . '_id', $page['title'], $page['content'], '' );
			}

			delete_transient( 'eventon_cache_excluded_uris' );
			update_option('_eventon_create_pages',1);

		}
}

evo_install::init();