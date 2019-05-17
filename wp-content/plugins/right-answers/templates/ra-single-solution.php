<?php
    gravity_form_enqueue_scripts("65", "ajax");
    gravity_form_enqueue_scripts(4, true);

	if ( isset( $_REQUEST['sol_id'] ) ){

            $main_data = single_solution_search( $_REQUEST['sol_id'] );
	}

?>

<div id="results-content-holder">
	<div id="cats-results-holder"><?php echo $main_data; ?></div>
</div>