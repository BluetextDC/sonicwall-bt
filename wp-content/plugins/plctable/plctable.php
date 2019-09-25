<?php
/*
 * Plugin Name: PLC Table
 * Plugin URI: https://www.sonicwall.com/
 * Description: This plugin pulls information from Salesforce to create a support section for the Sonicwall.com website
 * Author: Sonicwall
 * Author URI: https://www.sonicwall.com/
 * Version: 1.0
 */

if(!class_exists('PLCTable')) {
    class PLCTable {
        public function __construct() {
            // PLC
            add_shortcode( 'sw-plc-tables', array( &$this, 'sw_plc_table_page') );

            //Include any files needed for the operation of the plugins
            require_once(plugin_dir_path( __FILE__ ) . '/plc-table-admin.php');
            
            add_action('wp_enqueue_scripts', array( &$this, 'plc_table_public_scripts') );
            // add_action('admin_enqueue_scripts', array( &$this, 'plc_table_admin_scripts') );
        }

        /**
        * Activate the plugin
        */
        public static function activate() {
            // Initialize table to store database information
                    global $wpdb;
                        //$table = $wpdb->prefix . '[insert name of table]'; 
                        // $sql = 'CREATE TABLE IF NOT EXISTS ' . $table . ' (
                        //id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                        //name_of_field VARCHAR(255) NOT NULL,
                        // more_fields...
                        //)';
                
                //the file below is required to make sure all proper wordpress core files are included
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                //command to execute sql query from above
                // dbDelta( $sql );

        } // END public static function activate
        
        public static function deactivate() {
        // If you need to do anything on the deactivation, you do it here
        } 

        public static function uninstall() {
            // This is where you should clean up any database tables or other stuff you may have inserted into the site
        }

        public function sw_plc_table_page( $atts, $content = null ) {
            // Enable output buffering
            ob_start();

            // Render template
            
            include plugin_dir_path( __FILE__ ) . 'templates/plc-tables.php';

            // Return buffer
            $contents = ob_get_contents();
            ob_end_clean();
		// wp_enqueue_style('datatables_style_resp');
                wp_enqueue_style('datatables_style');

                // wp_enqueue_script('datatables_bootstrap_resp');
                wp_enqueue_script('datatables_bootstrap');
	        wp_enqueue_style('plc-public-styles');
                wp_enqueue_script('plc-table-script');
            return $contents;
        }
        public function plc_table_public_scripts() {
            // global $wp_query;
            // $shortcode_status = false;
		// if(is_page(9820)) {
		//	$shortcode_status = true;
		// }
            //foreach($wp_query->get_posts() as $key => $post) {
                //if (preg_match("/\[sw-plc-tables\]/", $post->post_content)) {
                  // $shortcode_status = true;
               //}
            // }

            wp_enqueue_script('jquery');
	    
            wp_register_style('plc-public-styles', plugins_url('assets/css/plc_table_public_css.css', __FILE__));

            wp_register_style('bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
            wp_enqueue_style( 'bootstrap' );
            wp_register_style('bootstrap_style', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
            wp_enqueue_style('bootstrap_style');
            
            wp_register_style('datatables_style', 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');
            wp_register_style('datatables_style_resp', 'https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css');
            wp_register_script('datatables_bootstrap', 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', array('jquery'), true);
            wp_register_script('datatables_bootstrap_resp', 'https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js', array('jquery'), true);

            wp_register_script('plc-table-script', plugins_url('assets/js/plc_table.js', __FILE__), 'jquery', '2.0', true);
            
            
            // if($shortcode_status) {
		// wp_enqueue_style('datatables_style_resp');
               // wp_enqueue_style('datatables_style');

                // wp_enqueue_script('datatables_bootstrap_resp');
                // wp_enqueue_script('datatables_bootstrap');
	        // wp_enqueue_style('plc-public-styles');
                // wp_enqueue_script('plc-table-script');
            // }
        }

        public function plc_table_admin_scripts() {
            // wp_register_style('plugin-name-admin-mods-nickname', plugins_url('assets/css/plugin-name-admin-mods.css', __FILE__));
            // wp_enqueue_style('plugin-name-admin-mods-nickname'); 
        }
	
    }

}
if(class_exists('PLCTable')) {
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('PLCTable', 'activate'));
	register_deactivation_hook(__FILE__, array('PLCTable', 'deactivate'));
	register_uninstall_hook(__FILE__, array('PLCTable', 'uninstall')); 

	// instantiate the plugin class
	$wp_plugin_template = new PLCTable();
    
    //Add in custom rewrite rules
    add_action('init', 'plc_custom_rewrite_rule', 0, 0);

    function plc_custom_rewrite_rule() {
        add_rewrite_rule('^support/product-lifecycle-tables/(.+)/?$','index.php?page_id=9820','top');
      }


}
