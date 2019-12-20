<?php

// $request_uri = strpos( $_SERVER['REQUEST_URI'], '/product-notification' );

if ( isset( $_REQUEST['sol_id'] ) ) { //  &&  ($request_uri !== false)

    global $wp_query;

    if (isset($wp_query->query_vars['kb-slug'])) {
        $main_data = single_solution_search( $_REQUEST['sol_id'], true);  
        
        //If the RA type is "Solutions", redirect to the same slug but with knowledge-base instead of product-notification
        if ($main_data->type == "Solutions") {
            global $wp;
            $url = home_url( $wp->request );
            $url = str_replace("/support/product-notification/","/support/knowledge-base/",$url);
            if (wp_redirect($url, 301)) {
                exit();
            }
        }
    } else {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}ra_slugs WHERE sol_id=%s LIMIT 1";
        $sql = $wpdb->prepare($sql, $_REQUEST['sol_id']);

        $result = $wpdb->get_results($sql);
        
        // $test_sql = "SELECT * FROM {$wpdb->prefix}ra_slugs d WHERE d.sol_id=%s ORDER BY created_at DESC";
        // $test_sql = $wpdb->prepare($test_sql, $_REQUEST['sol_id']);
        // $test_res= $wpdb->get_results($test_sql);
        // echo '<pre>'.json_encode($sql).json_encode($result).'</pre>';
        // echo '<pre>'.json_encode($test_sql).json_encode($test_res).'</pre>';
        // die();
        if ($result && count($result) > 0) {
            //Redirect to the proper url
            if ( wp_redirect( "/support/product-notification/".$result[0]->slug."/".$_REQUEST['sol_id']."/", 301) ) {
                exit;
            }
        } else {
            //Failed to find slug, set 404
            ra_404();
        }
    }
} else 
// if ( $request_uri !== false ) {
    //Redirect to support home
    if ( wp_redirect( "/support" ) ) {
        exit;
    }
// }

?>

<div id="back-to-alerts-link-holder">
	&#60; <a id="back-to-all-alerts-link" href="/support/product-notifications/">Back to Alerts</a>
</div>

<div id="results-content-holder">
	<div id="cats-results-holder"><?php echo $main_data->html; ?></div>
</div>