<?php
/*
Plugin Name: Right Answers
Plugin URI: https://sonicwall.com
Description: A plugin to retreive answers from Upland Right Answers
Version: 3.2.6
Author: Russell Kasselman
Author URI: https://irondogmedia.com
Text Domain: right-answers
License: GPL2
 
*/

// need to write a script to loop through each of the right answers public answers and put them in transient cache
// set the cache time to 15 minutes 
// then instead of making a call for each answer to the database, we load the answer from the transient cache if it is there
// if it is not there, the system falls back and makes the call to the RA databse to grab the answer from there and caches it. 
// also need to cache searches for each of the category searches and lastly we need to cache the individual searches being made
// by the users in the general search field. 


    function add_lang_home() {
        $lang_home_url = apply_filters( 'wpml_home_url', get_option( 'home' ) );
        if (!(substr($lang_home_url, -strlen("/")) === "/"))
	{
	    $lang_home_url = $lang_home_url."/";
	}
        echo '<script type="text/javascript">var lang_home_url = "'.$lang_home_url.'";</script>';
    }
    // Add hook for admin <head></head>
    add_action( 'admin_head', 'add_lang_home' );
    // Add hook for front-end <head></head>
    add_action( 'wp_head', 'add_lang_home' );

    add_action( 'send_headers', 'do_techdoc_pdf' );
    function do_techdoc_pdf() {
        
        $first_bit = "/support/technical-documentation/";
        if (substr($_SERVER["REQUEST_URI"], 0, strlen($first_bit)) === $first_bit && substr($_SERVER["REQUEST_URI"], -strlen(".pdf")) === ".pdf")
        {
            
            $file_name = substr($_SERVER["REQUEST_URI"], strlen($first_bit),  -strlen(".pdf"));
        
            
            echo '<iframe style="position:fixed; top:0; left:0; bottom:0; right:0; width:100%; height:100%; border:none; margin:0; padding:0; overflow:hidden; z-index:999999;" src="https://techdocs.sonicwall.com/wp-content/uploads/pdf/'.$file_name.'.pdf"></iframe>';
            
            die();
        }
        
    }

     //Add in custom rewrite rules
    add_action('init', 'custom_rewrite_rule', 0, 0);

    //Custom flush function
    add_action( 'wp_ajax_flush_permalinks', 'flush_permalinks' );
    add_action( 'wp_ajax_nopriv_flush_permalinks', 'flush_permalinks' );


add_filter('wpseo_title','custom_RA_title',10,1);

function custom_RA_title($title){
    
    global $post;

    switch ($post->ID) {
        case 12368:
            //Category page
            $category = get_query_var('kb-slug');

            if ($category)
            {
                $c_name = category_slug_translator($category);
            }
            else if ( isset( $_REQUEST['categoryid'] ) ) {
                $c_name = cat_translator( $_REQUEST['categoryid'] );
            }
            return $c_name." | SonicWall";
            break;
        case 12371:
        case 12364:
            //KB Single solution or Product notification
            $ra = new RARequests();
            $solution = $ra->get_single_solution( $_REQUEST['sol_id'] );
            $sol = json_decode( $solution );
            return $sol->title." | SonicWall";
            break;
        default:
            return $title;
    }    
}

    function preload_kb()
    {
        echo "Starting to preload KB articles\n";
        require_once(plugin_dir_path( __FILE__ ) . '/right-answers-admin.php');
    
        $categories = array("0","1","2","3","4","5","6","7","8","9");

        foreach ($categories as $categoryid)
        {
            $c_name = cat_translator( $categoryid );
      
            for ($i = 1; $i <= 200; $i++)
            {
                echo "Saving $c_name Page: $i\n";
                
                $data = show_ra_cat( $c_name, $i );
                
                $pattern = '/sol_id=[0-9A-Za-z]*/';
                preg_match_all($pattern, $data, $matches);
                foreach ($matches[0] as $match)
                {
                    $prefix = "sol_id=";
                    if (substr($match, 0, strlen($prefix)) == $prefix) 
                    {
                        $sol_id = substr($match, strlen($prefix));
                        echo "\tSaving solution: {$sol_id}\n";
                        single_solution_search($sol_id);
                    }  
                }
            }
           
        }
        
    }

   function flush_permalinks() {
        global $wp_rewrite;
        $wp_rewrite->flush_rules( true );
       exit("Flushed Rewrite Rules");
       
    }
   function custom_rewrite_rule() {
       add_filter('query_vars', function($vars) {
            $vars[] = "td-slug";
            $vars[] = "kb-slug";
            return $vars;
        });
        add_rewrite_rule('^support/technical-documentation/(.+)/?$','index.php?page_id=22984&td-slug=$matches[1]','top');
        add_rewrite_rule('^support/knowledge-base/(.+)/?$','index.php?page_id=12368&kb-slug=$matches[1]','top');
      }



if(!class_exists('RightAnswers')) {
	class RightAnswers {
	/**
	* Construct the plugin object
	*/
	public function __construct() {
				// Initialize shortcodes
        
                //LB
				add_shortcode( 'ra-single-solution', array( &$this, 'single_solution' ) ); 
				add_shortcode( 'ra-search-form', array( &$this, 'solution_search_form' ) ); 
				add_shortcode( 'ra-search', array( &$this, 'solution_search_results' ) ); 
				add_shortcode( 'ra-categories', array( &$this, 'ra_categories_page' ) ); 
				add_shortcode( 'ra-single-alert', array( &$this, 'single_alert' ) );
				add_shortcode( 'ra-alerts-bar', array( &$this, 'ra_alerts_bar' ) ); 
				add_shortcode( 'ra-all-alerts', array( &$this, 'ra_all_alerts' ) ); 
				add_shortcode( 'ra-video-grid', array( &$this, 'ra_video_grid_page' ) );
				add_shortcode( 'ra-single-video', array( &$this, 'ra_single_video_page' ) );
                add_shortcode( 'ra-browse-category', array( &$this, 'ra_browse_categories' ) );
        
                //PLC
				add_shortcode( 'plc-tables', array( &$this, 'sf_plc_table_page') );
        
                //Tech Docs
        
				add_shortcode( 'tech-doc-dropdown', array( &$this, 'tech_doc_dropdown_page') );
				add_shortcode( 'tech-doc-single-doc', array( &$this, 'tech_doc_single_doc') );
				
				//Include any files needed for the operation of the plugins
				require_once(plugin_dir_path( __FILE__ ) . '/right-answers-admin.php');
				require_once(plugin_dir_path( __FILE__ ) . '/video-admin.php');
				require_once(plugin_dir_path( __FILE__ ) . '/tech-docs-requests.php');
				
				add_action('wp_enqueue_scripts', array( &$this, 'right_answers_public_scripts') );
				add_action('admin_enqueue_scripts', array( &$this, 'right_answers_admin_scripts') );
        
        
               
		
	} // END public function __construct 

 
        
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
			
	public function right_answers_public_scripts() {
		wp_enqueue_script('jquery'); 
		
		wp_register_style('ra-pub-styles', plugins_url('/css/ra-public-styles.css', __FILE__), '', '2.0');
		wp_enqueue_style('ra-pub-styles');

		wp_register_style('bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
	    wp_enqueue_style( 'bootstrap' );

	    // wp_register_style('fonteawesome', 'https://use.fontawesome.com/releases/v5.7.2/css/all.css' );
	    // wp_enqueue_style('fonteawesome');

	
		wp_register_script('ra-public', plugins_url('/js/right-answers-public.js', __FILE__), 'jquery', '2.0', true);
		
		wp_localize_script('ra-public', 'ra_ajax_object', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'cat_name' => 'data_val_1',
			'curpg' => 'data_val_2',
			'jax' => 'data_val_3'
		));
		wp_enqueue_script('ra-public');
	}
	
	public function right_answers_admin_scripts() {
	// wp_register_style('plugin-name-admin-mods-nickname', plugins_url('/css/plugin-name-admin-mods.css', __FILE__));
	// wp_enqueue_style('plugin-name-admin-mods-nickname'); 
	 }

	public function single_solution( $atts, $content = null ) {

				// Enable output buffering
				ob_start();

				// Render template
				
				include plugin_dir_path( __FILE__ ) . 'templates/ra-single-solution.php';

				// Return buffer
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}

	public function single_alert( $atts, $content = null ) {

				// Enable output buffering
				ob_start();

				// Render template
				
				include plugin_dir_path( __FILE__ ) . 'templates/ra-single-alert.php';

				// Return buffer
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}

	public function solution_search_results( $atts, $content = null ) {

				// Enable output buffering
				ob_start();

				// Render template
				
				include plugin_dir_path( __FILE__ ) . 'templates/ra-search-results.php';

				// Return buffer
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}

	public function solution_search_form( $atts, $content = null ) {

				// Enable output buffering
				ob_start();

				// Render template
				
				include plugin_dir_path( __FILE__ ) . 'templates/ra-search-form.php';

				// Return buffer
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}
        
    public function ra_browse_categories( $atta, $content = null ) {
        // Enable output buffering
        ob_start();

        // Render template

        include plugin_dir_path( __FILE__ ) . 'templates/ra-browse-categories.php';

        // Return buffer
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

	public function ra_categories_page( $atts, $content = null ) {

				// Enable output buffering
				ob_start();

				// Render template
				
				include plugin_dir_path( __FILE__ ) . 'templates/ra-knowledge-base-category.php';

				// Return buffer
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}
	

	public function ra_alerts_bar( $atts, $content = null ) {

				// Enable output buffering
				ob_start();

				// Render template
				
				include plugin_dir_path( __FILE__ ) . 'templates/ra-alerts-bar.php';

				// Return buffer
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}

	public function ra_all_alerts( $atts, $content = null ) {

				// Enable output buffering
				ob_start();

				// Render template
				
				include plugin_dir_path( __FILE__ ) . 'templates/ra-alerts.php';

				// Return buffer
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}


	public function ra_video_grid_page( $atts, $content = null ) {

				// Enable output buffering
				ob_start();

				// Render template
				
				include plugin_dir_path( __FILE__ ) . 'templates/ra-video-grid.php';

				// Return buffer
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}

	public function ra_single_video_page( $atts, $content = null ) {

				// Enable output buffering
				ob_start();

				// Render template
				
				include plugin_dir_path( __FILE__ ) . 'templates/ra-single-video.php';

				// Return buffer
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}

	public function sf_plc_table_page( $atts, $content = null ) {

				// Enable output buffering
				ob_start();

				// Render template
				
				include plugin_dir_path( __FILE__ ) . 'templates/plc-tables.php';

				// Return buffer
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}

	public function tech_doc_dropdown_page( $atts, $content = null ) {

				// Enable output buffering
				ob_start();

				// Render template
				
				include plugin_dir_path( __FILE__ ) . 'templates/tech-doc-dropdown.php';

				// Return buffer
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}

	public function tech_doc_single_doc( $atts, $content = null ) {

				// Enable output buffering
				ob_start();

				// Render template
				
				include plugin_dir_path( __FILE__ ) . 'templates/tech-docs-single-doc.php';

				// Return buffer
				$contents = ob_get_contents();
				ob_end_clean();
				return $contents;
			}

	}

 // END RightAnswers
} // END if class exists ('RightAnswers')

if(class_exists('RightAnswers')) {
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('RightAnswers', 'activate'));
	register_deactivation_hook(__FILE__, array('RightAnswers', 'deactivate'));
	register_uninstall_hook(__FILE__, array('RightAnswers', 'uninstall')); 

	// instantiate the plugin class
	$wp_plugin_template = new RightAnswers();

}
