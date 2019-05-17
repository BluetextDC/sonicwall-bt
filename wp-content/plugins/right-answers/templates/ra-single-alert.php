<?php


	if ( isset( $_REQUEST['sol_id'] ) ){

        $key = $_REQUEST['sol_id'] . '_solution';
        
		if ( false === ( $main_data = getRACacheItem($key) ) ) {
		  // It wasn't there, so regenerate the data and save the transient
		  $main_data = single_solution_search( $_REQUEST['sol_id'], true );
          setRACacheItem($key, $main_data);
		}

	}

?>

<div id="back-to-alerts-link-holder">
	&#60; <a id="back-to-all-alerts-link" href="/support/product-notifications/">Back to Alerts</a>
</div>

<div id="results-content-holder">
	<div id="cats-results-holder"><?php echo $main_data; ?></div>
</div>