<?php
    gravity_form_enqueue_scripts("65", "ajax");
    gravity_form_enqueue_scripts(4, true);

	if ( isset( $_REQUEST['sol_id'] ) ){
        
        global $wp_query;
        
        if (isset($wp_query->query_vars['kb-slug']))
        {
            $main_data = single_solution_search( $_REQUEST['sol_id'] );   
        }
        else
        {
            global $wpdb;
    
            $sql = "SELECT * FROM {$wpdb->prefix}ra_slugs WHERE sol_id=%s LIMIT 1";
            $sql = $wpdb->prepare($sql, $_REQUEST['sol_id']);
      
            $result = $wpdb->get_results($sql);

            if ($result && count($result) > 0)
            {
                //Redirect to the proper url
                if ( wp_redirect( "/support/knowledge-base/".$result[0]->slug, 301) ) {
                    exit;
                }
            }
            else
            {
                //Failed to find slug, set 404
                ra_404();
            }
        }
            
	}
    else
    {
        //Redirect to support home
        if ( wp_redirect( "/support" ) ) {
            exit;
        }
    }
  
?>

<div id="results-content-holder">
	<div id="cats-results-holder"><?php echo $main_data; ?></div>
</div>