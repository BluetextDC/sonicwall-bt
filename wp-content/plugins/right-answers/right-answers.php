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

 
if ( class_exists( 'WP_CLI' ) ) {
    
class cli_right_answers extends WP_CLI_Command {
     
  /**
   * Update right answers data
   */
  function update() {
    global $wpdb;
    
    $languages = array(
        "Chinese",
        "English",
        "French",
        "German",
        "Italian",
        "Japanese",
        "Korean",
        "Portuguese",
        "Spanish"
    );
      
      
    $sol_ids = [];
    
    foreach($languages as $lang)
    {
        //Download all solutions
        
        $page = 1;

        $per_page = 10;

        $total_pages = false;

        $map = array();

        $data = download($page, $lang, 'solution');
        $total_pages = ceil($data->totalHits / $per_page);
        
        WP_CLI::line( $lang.' - Downloading Solutions Page: 1 / '.$total_pages );

        $map = array_merge($map, buildMapArray($data->solutions));

        //Now loop and download all the other pages
        for ($page = 2; $page <= $total_pages; $page++)
        {
            WP_CLI::line( $lang.' - Downloading Solutions Page: '. $page .' / '.$total_pages );
            // echo "Downloading page: $page\n";
            $data = download($page, $lang, 'solution');

            $map = array_merge($map, buildMapArray($data->solutions));
        }

        foreach ($map as $m)
        {
            $now = date("Y-m-d H:i:s");
            $sql = "INSERT INTO {$wpdb->prefix}ra_slugs (sol_id,slug,created_at, type) VALUES (%d,%s,%s,%s) ON DUPLICATE KEY UPDATE slug = %s, type=%s";
            $sql = $wpdb->prepare($sql, $m->id, $m->slug, $now, 'solution', $m->slug, 'solution');
            $wpdb->query($sql);
            
            $sol_ids[] = $m->id;
        }  
        
        
        //Download all alerts
        
        $page = 1;

        $per_page = 10;

        $total_pages = false;

        $map = array();

        $data = download($page, $lang, 'alert');
        $total_pages = ceil($data->totalHits / $per_page);
        
        WP_CLI::line( $lang.' - Downloading Solutions Page: 1 / '.$total_pages );

        $map = array_merge($map, buildMapArray($data->solutions));

        //Now loop and download all the other pages
        for ($page = 2; $page <= $total_pages; $page++)
        {
            WP_CLI::line( $lang.' - Downloading Solutions Page: '. $page .' / '.$total_pages );
            // echo "Downloading page: $page\n";
            $data = download($page, $lang, 'alert');

            $map = array_merge($map, buildMapArray($data->solutions));
        }

        foreach ($map as $m)
        {
            $now = date("Y-m-d H:i:s");
            $sql = "INSERT INTO {$wpdb->prefix}ra_slugs (sol_id,slug,created_at, type) VALUES (%d,%s,%s,%s) ON DUPLICATE KEY UPDATE slug = %s, type=%s";
            $sql = $wpdb->prepare($sql, $m->id, $m->slug, $now, 'alert', $m->slug, 'alert');
            $wpdb->query($sql);
            
            $sol_ids[] = $m->id;
        }  
    }
      
    //Delete anything that isn't there if we have more than 10 sol_ids
      
    if (count($sol_ids) > 10)
    {
         //Remove old solutions that are missing or archived
        $sol_id_str = implode(",", $sol_ids);
        $delete_query = "DELETE FROM {$wpdb->prefix}ra_slugs WHERE sol_id NOT IN (".$sol_id_str.")";
        $wpdb->query($delete_query);
    }
      
  
      
    WP_CLI::line( 'Finished' );
  }
    
    
    /**
    * Preload right answers data
    */
    function preload()
    {
        global $wpdb;
        
        require_once(plugin_dir_path( __FILE__ ) . '/right-answers-admin.php');
        
        $ra = new KBRequests();
        
        $languages = array(
            "Chinese",
            "English",
            "French",
            "German",
            "Italian",
            "Japanese",
            "Korean",
            "Portuguese",
            "Spanish"
        );
        
        $categories = array("0","1","2","3","4","5","6","7","8","9");

        WP_CLI::line( "Starting to preload KB articles" );

        //Loop through each language
        foreach ($languages as $language)
        {
            $_GLOBAL['lang_override'] = $language;
            
            //Preload all the categories
            $ra->get_ra_categories(true);
            
            
            //Loop through 20 pages of alerts
            
            for ($i = 1; $i <= 20; $i++)
            {
                $ra->alert_search($i, true); 
            }
            
            
          //Loop through every category 
            foreach ($categories as $categoryid)
            {
                $c_name = cat_translator( $categoryid );
                
                //Download the drilldown part too
                drilldown_menu($c_name);

                for ($i = 1; $i <= 200; $i++)
                {
                    WP_CLI::line( "Saving {$c_name} Page: {$i} Language: {$language}" );                    
                    $data = show_ra_cat( $c_name, $i, false, true);

                }
            }  
        }
   
        //Download and cache every solution ID
        $sql = "SELECT * FROM {$wpdb->prefix}ra_slugs";
        $solutions = $wpdb->get_results($sql);
        
        foreach ($solutions as $solution)
        {
            //Save each solution
            WP_CLI::line( "Saving sol id: ". $solution->sol_id );
            $ra->get_single_solution( $solution->sol_id, true  );
        }
        
        WP_CLI::line( 'Finished Preloading' );
    }
    
    
    /**
    * Download all right answers data (takes 2+ hours)
    */
    function download()
    {
        global $wpdb;
        
        require_once(plugin_dir_path( __FILE__ ) . '/right-answers-admin.php');
                   
        //Download and cache every solution ID
        $sql = "SELECT * FROM {$wpdb->prefix}ra_slugs";
        $solutions = $wpdb->get_results($sql);
        
        foreach ($solutions as $solution)
        {
            //Save each solution
            WP_CLI::line( "Saving sol id: ". $solution->sol_id );
            download_single_solution_data($solution->sol_id, $solution->type == 'alert');
        }
        
        WP_CLI::line( 'Finished Downloading' );
    }
    
    /**
    * Download only solutions updated within the last 24 hours
    */
    function update_new_solutions()
    {
        global $wpdb;
        
        require_once(plugin_dir_path( __FILE__ ) . '/right-answers-admin.php');
        
        $kb = new KBRequests();
        
        $languages = array(
            "Chinese",
            "English",
            "French",
            "German",
            "Italian",
            "Japanese",
            "Korean",
            "Portuguese",
            "Spanish"
        );
        
        foreach($languages as $language)
        {
            $_GLOBAL['lang_override'] = $language;
            
            //Get first page of updates and calculate the number of pages
        
            $data = $kb->get_new_solutions(1, $language);

            if ($data->totalHits > 0)
            {
                $total_solutions = $data->totalHits;
                $solutions = $data->solutions;

                //Calculate out the number of pages
                $number_of_pages = ceil($total_solutions / count($solutions));

                //Loop through each solution and download the data
                foreach($solutions as $solution)
                {
                    //Get the solution type
                    $sql = "SELECT * FROM {$wpdb->prefix}ra_slugs WHERE sol_id = %d ORDER BY created_at DESC LIMIT 1";
                    $sql = $wpdb->prepare($sql, $solution->id);
                    $sol_data = $wpdb->get_results($sql);

                    if ($sol_data && count($sol_data) == 1)
                    {
                        $solution = $sol_data[0];

                        download_single_solution_data($solution->sol_id, $solution->type == 'alert'); 
                    }
                    else
                    {
                        //Can't find the slug, but let's download the raw content and assume it isn't an alert to speed things up
                        download_single_solution_data($solution->id, false);
                    }
                }

                //Loop through all the pages except for page 1
                for ($i = 2; $i <= $number_of_pages; $i++)
                {
                    $data = $kb->get_new_solutions($i, $language);
                    
                    if ($data->totalHits > 0)
                    {
                        $solutions = $data->solutions;

                        //Loop through each solution and download the data
                        foreach($solutions as $solution)
                        {
                            //Get the solution type
                            $sql = "SELECT * FROM {$wpdb->prefix}ra_slugs WHERE sol_id = %d ORDER BY created_at DESC LIMIT 1";
                            $sql = $wpdb->prepare($sql, $solution->id);
                            $sol_data = $wpdb->get_results($sql);

                            if ($sol_data && count($sol_data) == 1)
                            {
                                $solution = $sol_data[0];

                                download_single_solution_data($solution->sol_id, $solution->type == 'alert'); 
                            }
                            else
                            {
                                //Can't find the slug, but let's download the raw content and assume it isn't an alert to speed things up
                                download_single_solution_data($solution->id, false);
                            }
                        }   
                    }
                }
            }
            
            
        }
    }
}

    WP_CLI::add_command( 'right_answers', 'cli_right_answers' );
}


//Download a single RA solution and store it
function download_single_solution_data($solution_id, $alert = false)
{
    global $wpdb;
        
    require_once(plugin_dir_path( __FILE__ ) . '/right-answers-admin.php');
    
    $kb = new KBRequests();
    
    $solution = $kb->get_single_solution( $solution_id, true);
            
    $sql = "INSERT INTO {$wpdb->prefix}ra_data (sol_id,data) VALUES (%d,%s) ON DUPLICATE KEY UPDATE data = %s";
    $sql = $wpdb->prepare($sql, $solution_id, json_encode($solution), json_encode($solution));
    
    $wpdb->query($sql); 
}

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

     

    //Custom flush function
    add_action( 'wp_ajax_flush_permalinks', 'flush_permalinks' );
    add_action( 'wp_ajax_nopriv_flush_permalinks', 'flush_permalinks' );


add_filter( 'wpseo_canonical', 'custom_RA_canonical', 10, 1 );

function custom_RA_canonical($canonical)
{
    global $post;

    switch ($post->ID) {
        case 12368:
        case 12371:
        case 12364:
            //Category page
            $category = get_query_var('kb-slug');
            return get_site_url()."/support/knowledge-base/".$category;
            break;
        case 14738:
            return $canonical.'?vid_id='.$_REQUEST['vid_id'];
		    break;
        default:
            return $canonical;
    }    
}


add_filter('wpseo_opengraph_url', 'custom_RA_canonical', 10, 1);
function alternate_hrefs_manipulator_callback($languages) 
{
    global $post;
    if($post->ID == 14738) {
        foreach ($languages as $lang_code => $language) {
            $languages[$lang_code] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . 
            $_SERVER['HTTP_HOST'].'/'.$lang_code.$_SERVER['REQUEST_URI'];
        }
    }
	return $languages;
}
add_filter( 'wpml_hreflangs', 'alternate_hrefs_manipulator_callback' );

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
            
            if ($c_name)
            {
                return $c_name." | SonicWall";
            }
            else
            {
                return "Page Not Found | SonicWall";
            }
            
            break;
        case 12371:
        case 12364:
            //KB Single solution or Product notification
            $ra = new KBRequests();
            $solution = $ra->get_single_solution( $_REQUEST['sol_id'] );
            
            $sol = json_decode( $solution );
                    
            if ($sol && $sol->title)
            {
               return $sol->title." | SonicWall"; 
            }
            else
            {
               return "Page Not Found | SonicWall";   
            }
            
            break;
        case 14738:
			try {
                if (!class_exists('BC_Logging'))
                {
                    require_once(get_home_path().'/wp-content/plugins/brightcove-video-connect/includes/class-bc-logging.php');
                }
                $vid = new BC_CMS_API();
                $vid_details = $vid->video_get( $_REQUEST['vid_id'] );
                if($vid_details["state"] == "ACTIVE") {
                            return $vid_details['name'];
                } else {
			     get_404_support_videos();
                }
            }
 			catch(exception $e)
            {
		       get_404_support_videos();  
            }
			break;
        default:
            return $title;
    }    
}

   	function get_404_support_videos() {
                    global $wp_query;
                    $wp_query->set_404();
                    status_header( 404 );
                    nocache_headers();
                    require get_404_template();
                    return "Page Not Found | SonicWall";
		
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
            $vars[] = 'kb-alert';
            return $vars;
        });
        add_rewrite_rule('^support/technical-documentation/(.+)/?$','index.php?page_id=22984&td-slug=$matches[1]','top');
        add_rewrite_rule('^support/product-notification/(.+)/?$','index.php?kb-slug=$matches[1]&kb-alert=true','top');
        add_rewrite_rule('^support/knowledge-base/(.+)/?$','index.php?kb-slug=$matches[1]','top');
      }

    //Add in custom rewrite rules
    add_action('init', 'custom_rewrite_rule', 0, 0);

    add_filter( 'request', function( array $query_vars ) {
        
        
        if (isset($query_vars['kb-slug']))
        {
            global $wpdb;
            
            
            $parts = explode( "/", $query_vars['kb-slug']);
            
            if ($parts && count($parts) > 0)
            {
                 $slug = urldecode($parts[0]);
            }
           
            if ($parts && count($parts) > 1)
            {
                $sol_id = $parts[1];    
            }
            
            if ($sol_id && slug)
            {
                $sql = "SELECT d.*, (SELECT n.slug FROM {$wpdb->prefix}ra_slugs n WHERE sol_id = d.sol_id ORDER BY created_at DESC LIMIT 1) as recent_slug FROM {$wpdb->prefix}ra_slugs d WHERE d.sol_id=%s ORDER BY created_at DESC LIMIT 1";
                $sql = $wpdb->prepare($sql, $sol_id);
            
                $result = $wpdb->get_results($sql);
                
                if ($result && count($result) > 0)
                {                    
                    $_REQUEST['sol_id'] = $result[0]->sol_id;

                    //Check if it is an alert or single solution
                    if (isset($query_vars['kb-alert']))
                    {
                        $query_vars['page_id'] = 12371; //Single Solution
                        
                        //Check for a newer slug
                        if ($result[0]->slug != $slug)
                        {
                            if ( wp_redirect( "/support/product-notification/".$result[0]->recent_slug."/".$result[0]->sol_id, 301) ) {
                                exit;
                            }
                        }

                        if ($result[0]->sol_id != $sol_id)
                        {
                            if ( wp_redirect( "/support/product-notification/".$result[0]->slug."/".$result[0]->sol_id, 301) ) {
                                exit;
                            }
                        }

                    }
                    else
                    {
                        
                        //Check for a newer slug
                        if ($result[0]->slug != $slug)
                        {
                            if ( wp_redirect( "/support/knowledge-base/".$result[0]->recent_slug."/".$result[0]->sol_id, 301) ) {
                                exit;
                            }
                        }
                        
                        if ($result[0]->sol_id != $sol_id)
                        {
                            if ( wp_redirect( "/support/knowledge-base/".$result[0]->slug."/".$result[0]->sol_id, 301) ) {
                                exit;
                            }
                        }
                        
                        $query_vars['page_id'] = 12364; //Single Solution
                    }
                }
                else
                {
                    //Check if it's a category, else return a 404
                    $query_vars['page_id'] = 12368; //Category
                } 
            }
            else if ($sol_id)
            {
                $sql = "SELECT d.*, (SELECT n.slug FROM {$wpdb->prefix}ra_slugs n WHERE sol_id = d.sol_id ORDER BY created_at DESC LIMIT 1) as recent_slug FROM {$wpdb->prefix}ra_slugs d WHERE d.sol_id=%s ORDER BY created_at DESC LIMIT 1";
                $sql = $wpdb->prepare($sql, $sol_id);
            
                
                $result = $wpdb->get_results($sql);
                
                if ($result && count($result) > 0)
                {                    
                    $_REQUEST['sol_id'] = $result[0]->sol_id;

                    //Check if it is an alert or single solution
                    if (isset($query_vars['kb-alert']))
                    {
                        $query_vars['page_id'] = 12371; //Single Solution
                        
                        //Check for a newer slug
                        if ($result[0]->slug != $result[0]->recent_slug)
                        {
                            if ( wp_redirect( "/support/product-notification/".$result[0]->recent_slug."/".$result[0]->sol_id, 301) ) {
                                exit;
                            }
                        }

                        if ($result[0]->sol_id != $sol_id)
                        {
                            if ( wp_redirect( "/support/product-notification/".$result[0]->slug."/".$result[0]->sol_id, 301) ) {
                                exit;
                            }
                        }

                    }
                    else
                    {
                        
                        //Check for a newer slug
                        if ($result[0]->slug != $result[0]->recent_slug)
                        {
                            if ( wp_redirect( "/support/knowledge-base/".$result[0]->recent_slug."/".$result[0]->sol_id, 301) ) {
                                exit;
                            }
                        }
                        
                        if ($result[0]->sol_id != $sol_id)
                        {
                            if ( wp_redirect( "/support/knowledge-base/".$result[0]->slug."/".$result[0]->sol_id, 301) ) {
                                exit;
                            }
                        }
                        
                        $query_vars['page_id'] = 12364; //Single Solution
                    }
                }
                else
                {
                    //Check if it's a category, else return a 404
                    $query_vars['page_id'] = 12368; //Category
                } 
            }
            else if ($slug)
            {
                $sql = "SELECT d.*, (SELECT n.slug FROM {$wpdb->prefix}ra_slugs n WHERE sol_id = d.sol_id ORDER BY created_at DESC LIMIT 1) as recent_slug FROM {$wpdb->prefix}ra_slugs d WHERE d.slug=%s ORDER BY created_at DESC LIMIT 1";
                $sql = $wpdb->prepare($sql, $slug);
            
                
                $result = $wpdb->get_results($sql);
                
                if ($result && count($result) > 0)
                {                    
                    $_REQUEST['sol_id'] = $result[0]->sol_id;

                    //Check if it is an alert or single solution
                    if (isset($query_vars['kb-alert']))
                    {
                        $query_vars['page_id'] = 12371; //Single Solution
                        
                        //Check for a newer slug
                        if ($result[0]->slug != $result[0]->recent_slug)
                        {
                            if ( wp_redirect( "/support/product-notification/".$result[0]->recent_slug."/".$result[0]->sol_id, 301) ) {
                                exit;
                            }
                        }

                        if ($result[0]->sol_id != $sol_id)
                        {
                            if ( wp_redirect( "/support/product-notification/".$result[0]->slug."/".$result[0]->sol_id, 301) ) {
                                exit;
                            }
                        }

                    }
                    else
                    {
                        
                        //Check for a newer slug
                        if ($result[0]->slug != $result[0]->recent_slug)
                        {
                            if ( wp_redirect( "/support/knowledge-base/".$result[0]->recent_slug."/".$result[0]->sol_id, 301) ) {
                                exit;
                            }
                        }
                        
                        if ($result[0]->sol_id != $sol_id)
                        {
                            if ( wp_redirect( "/support/knowledge-base/".$result[0]->slug."/".$result[0]->sol_id, 301) ) {
                                exit;
                            }
                        }
                        
                        $query_vars['page_id'] = 12364; //Single Solution
                    }
                }
                else
                {
                    //Check if it's a category, else return a 404
                    $query_vars['page_id'] = 12368; //Category
                }    
            }
            else
            {
                ra_404();
            }
            
            
        }
        
        return $query_vars;

    });



register_activation_hook ( __FILE__, 'on_activate' );

function on_activate() {
    global $wpdb;
    $create_slug_table = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ra_slugs` (
              `sol_id` bigint(20) NOT NULL PRIMARY KEY,
              `slug` VARCHAR(255) NOT NULL,
              `created_at` DATETIME NOT NULL,
              `type` VARCHAR(255) NOT NULL
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
    ";
    
    $create_data_table = "
            CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}ra_data` (
              `sol_id` bigint(20) NOT NULL PRIMARY KEY,
              `data` LONGTEXT NOT NULL
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
    ";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $create_slug_table );
    dbDelta( $create_data_table );
}



function ra_404() {
    global $wp_query; //$posts (if required)
    if(is_page()){ // your condition
        status_header( 404 );
    }
}

function buildMapArray($solutions)
{
	$map = array();

	foreach($solutions as $solution)
	{
        $s = new stdClass;
        $s->id = $solution->id;
        $s->slug = slugifyRA($solution->title);
        $s->title = $solution->title;
        $s->type = $solution->solutionType;

        $map[] = $s;
	}

	return $map;
}

function slugifyRA($str, $options = array())
{
     // Make sure string is in UTF-8 and strip invalid UTF-8 characters
	$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
	
	$defaults = array(
		'delimiter' => '-',
		'limit' => null,
		'lowercase' => true,
		'replacements' => array(),
		'transliterate' => false,
	);
	
	// Merge options
	$options = array_merge($defaults, $options);
	
	$char_map = array(
		// Latin
		'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C', 
		'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 
		'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O', 
		'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH', 
		'ß' => 'ss', 
		'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 
		'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 
		'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 
		'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 
		'ÿ' => 'y',
		// Latin symbols
		'©' => '(c)',
		// Greek
		'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
		'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
		'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
		'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
		'Ϋ' => 'Y',
		'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
		'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
		'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
		'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
		'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
		// Turkish
		'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
		'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g', 
		// Russian
		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
		'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
		'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
		'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
		'Я' => 'Ya',
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
		'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
		'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
		'я' => 'ya',
		// Ukrainian
		'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
		'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
		// Czech
		'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U', 
		'Ž' => 'Z', 
		'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
		'ž' => 'z', 
		// Polish
		'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z', 
		'Ż' => 'Z', 
		'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
		'ż' => 'z',
		// Latvian
		'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 
		'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
		'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
		'š' => 's', 'ū' => 'u', 'ž' => 'z'
	);
	
	// Make custom replacements
	$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
	
	// Transliterate characters to ASCII
	if ($options['transliterate']) {
		$str = str_replace(array_keys($char_map), $char_map, $str);
	}
	
	// Replace non-alphanumeric characters with our delimiter
	$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
	
	// Remove duplicate delimiters
	$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
	
	// Truncate slug to max. characters
	$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
	
	// Remove delimiter from ends
	$str = trim($str, $options['delimiter']);
	
	return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}


function download($page, $lang, $type = 'solution')
{
	if ($data = getCache($page))
	{
		return json_decode($data);
	}
	else
	{
		//Sleep to prevent killing the server
//		sleep(1);
        
        //Default the type to solution
        $template = "template-sonicwall-Solutions";
        
        if ($type == 'alert')
        {
            $template = "template-sonicwall-Alerts";    
        }
       
        

		$curl = curl_init();

		curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://sonicwall.rightanswers.com/portal/api/rest/search/?companyCode=sonicwall&appInterface=ss&&collections=custom_SS&page={$page}&language={$lang}&templates={$template}",
			  CURLOPT_USERPWD => "apitest:nC0@jC4uIJ3ng",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			  CURLOPT_FRESH_CONNECT => true,
			  CURLOPT_HTTPHEADER => array(
			    "Accept-Encoding: application/gzip,deflate",
			    "Connection: Keep-Alive",
			    "User-Agent: Apache-HttpClient/4.1.1 (java 1.7)",
			    "cache-control: no-cache"
			  ),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
			  return "cURL Error #:" . $err;
			} else {
			  setCache($page, $response);
			  return json_decode($response);
			}
	}
}


function getCache($page)
{
    return false;
	$cache_file = __DIR__."/cache/{$page}.json";
	if (file_exists($cache_file))
	{
		return file_get_contents($cache_file);
	}

	return false;
}

function setCache($page, $data)
{
    return;
	$cache_file = __DIR__."/cache/{$page}.json";
	file_put_contents($cache_file, $data);
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

	
		wp_register_script('ra-public', plugins_url('/js/right-answers-public-v2.js', __FILE__), 'jquery', '2.1', true);
		
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
