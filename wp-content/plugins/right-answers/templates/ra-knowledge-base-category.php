<?php
	
    $category = get_query_var('kb-slug');

    if ($category)
    {
        $c_name = category_slug_translator($category);
    }
    else if ( isset( $_REQUEST['categoryid'] ) ) {
        $c_name = cat_translator( $_REQUEST['categoryid'] );
    }
    else
    {
        //Invalid Category ID
        redirect_support_home();
    }

    if (!$c_name)
    {
        ra_404();
    }
    
    $main_data = show_ra_cat( $c_name, $curpg);
    $sidebar_data = drilldown_menu( $c_name );
         


?>

<div id="results-content-holder">
	<div id="filter-sidebar" class="one-quarter"><?php echo $sidebar_data; ?></div>
	<div id="cats-results-holder" class="three-quarters"><?php echo $main_data; ?></div>
</div>