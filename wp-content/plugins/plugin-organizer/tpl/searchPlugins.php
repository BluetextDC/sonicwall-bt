<div id="wrap">
  <div class="po-setting-icon fa fa-search" id="icon-po-search"> <br /> </div>

  <h2 class="po-setting-title">Search Plugins</h2>
  <div style="clear: both;"></div>
  <p>Select the plugins you would like to disable/enable on the search results page.</p>
  <div id="PO-search-plugins-container">
    <div class="PO-loading-container fa fa-spinner fa-pulse"></div>
	<div id="pluginListdiv" class="stuffbox inside" style="width: 98%">
	  <?php
	    $ajaxSaveFunction = "PO_submit_search_plugins();";
		require_once('postMetaBox.php');
      ?>
	</div>
  </div>
</div>