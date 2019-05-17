<?php 
	
	wp_cache_flush();

	$ra = new RARequests();

    $alerts =  $ra->alert_search(1); 
    $lang_home_url = apply_filters( 'wpml_home_url', get_option( 'home' ) );
?>
<div class="alerts-bar">
	<!-- Need to make a shortcode for all alerts, run an all alert search and then link from here to that page -->
	<div class="ra-orange-alert-nav">Top Alerts: <!-- (<span id="ra-alert-number">1</span> of 3) --> 
		<div class="ra-alert-nav-arrows"><a class="ra-all-alerts-link" href="<?php echo $lang_home_url;?>/support/product-notifications/"> View All Alerts &#62;</a></div>
		<!-- <div class="ra-alert-nav-arrows"><a id="ra-prev-alert" href="#">&#60;</a><a id="ra-next-alert" href="#">&#62;</a></div> -->
	</div>
	<div id="alert-display-holder">
		<?php 
			$alerts_txt = '';
			for ( $i=0; $i<3; $i++) {
                if (isset($alerts->solutions[$i]))
                {
                    $alerts_txt .= '<div class="alerts-display" id="alert-no-' . $i . '">';
                    // need to put a form in here that will submit with the solution ID and do a single solution search.
                    $alerts_txt .= '<p class="alert-title">' . $alerts->solutions[$i]->title . '  <a class="alert-title-link" href="/support/product-notification/?sol_id=' . $alerts->solutions[$i]->id . '" id="' . $alerts->solutions[$i]->id . '">Read More > </a></p></div>';
                }
				
			}
			echo $alerts_txt;
		?>
		</p>
	</div>
</div>