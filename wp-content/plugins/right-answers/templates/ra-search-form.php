<?php
$lang_home_url = apply_filters( 'wpml_home_url', get_option( 'home' ) );
if (!(substr($lang_home_url, -strlen("/")) === "/"))
{
    $lang_home_url = $lang_home_url."/";
}
?>
<div id="main_ra_search_form">

	<form class="ra-main-search-form" action="<?php echo $lang_home_url;?>support/search-results" method="get">
		<p>
			<!-- <label class="ra-search-field-label" for="search_term">Enter Search Term</label> -->
			<input class="ra-search-field" type="text" name="searchtext" placeholder="Search Support" />
			<div class="ra-search-submit-button"><img src="<?php echo plugins_url('../img/search-icon.png', __FILE__); ?>"></div>
		</p>
	</form>
</div>

<div id="ra_category_search_links">
	<p><strong>Browse Knowledgebase by Category(s)</strong></p>
</div>
