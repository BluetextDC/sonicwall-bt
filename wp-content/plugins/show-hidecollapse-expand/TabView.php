<?php
	
class bg_show_hide_TabView {
	public function __construct( $pluginContext) {
		$this->_pluginContext = $pluginContext;
	}
	
	public function displayTab( $tabsToDisplay, $activeTab) {
	
		$tabViewTemplate = "
			<div class=\"wrap\">
				<h1> %%PLUGIN_NAME_PLAIN_TEXT%% </h1>

				<h2 class=\"nav-tab-wrapper\">
					%%TAB_URLS%%
				</h2>

			</div>

			%%ACTIVE_TAB_CONTENT%%
		";
		
		
		$tabUrlTemplateStr = "<a href=\"?page=%%PLUGIN_SLUG%%&tab=%%TAB_NAME%%\"
			class=\"nav-tab %%TAB_ACTIVE_OR_NOT%%\"> %%TAB_CAPTION_TEXT%% </a>";
			
		$tmpTabUrls = "";
		foreach( $tabsToDisplay as $tabName => $tab) {
			$tabUrlStr = "";
			if( $tab === $activeTab) {
				$tabUrlStr = str_replace( "%%TAB_ACTIVE_OR_NOT%%", "nav-tab-active", $tabUrlTemplateStr);
			}
			else {
				$tabUrlStr = str_replace( "%%TAB_ACTIVE_OR_NOT%%", $tabName, $tabUrlTemplateStr);
			}

			$tabUrlStr = str_replace( "%%TAB_NAME%%", $tabName, $tabUrlStr);
			
			$tabUrlStr = str_replace( "%%PLUGIN_SLUG%%", $this->_pluginContext->getPluginSlug(), $tabUrlStr);
			
			$tmpTabUrls .= str_replace( "%%TAB_CAPTION_TEXT%%", $tab->getTabCaptionText(), $tabUrlStr);
		}

		$tabViewTemplate = str_replace( "%%TAB_URLS%%", $tmpTabUrls, $tabViewTemplate);
			
		$tabViewTemplate = str_replace( "%%PLUGIN_NAME_PLAIN_TEXT%%",
			$this->_pluginContext->getPluginName(), $tabViewTemplate);

		$tabViewTemplate = str_replace( "%%ACTIVE_TAB_CONTENT%%", $activeTab->display() , $tabViewTemplate);

		echo $tabViewTemplate;
	}
	
	

	private $_pluginContext;
};

?>