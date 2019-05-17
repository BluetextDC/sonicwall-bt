<?php


	if ( isset( $_REQUEST['searchtext'] ) ){

		$sterm = strip_tags( $_REQUEST['searchtext'] ); 
        
		if ( !isset( $_REQUEST['curpage'] ) || $_REQUEST['curpage'] == '' ) {
			$pgnum = 1;
		}
		else {
			$pgnum = $_REQUEST['curpage'];
		}

        $main_data = general_ra_search( $sterm, $pgnum );

	}
    else
    {
        redirect_support_home();
    }

?>

<div id="results-content-holder">
	<div id="cats-results-holder"><?php echo $main_data; ?></div>
</div>