<?php

require_once( "TabView.php");
require_once( "PluginContext.php");

class bg_show_hide_TabManager {
	public function __construct( $tabView) {
		$this->_tabView = $tabView;
	}

	public function addTab( $tab, $isActiveTab = false) {			
		$this->_tabs[ $tab->getTabName()] = $tab;
		
		if(	$isActiveTab) {
			$this->_activeTab = $tab;
		}
	}
	
	public function setActiveTab( $activeTab) {
		$_activeTab = $activeTab;
	}
	
	public function displayActiveTab() {		
		$this->_tabView->displayTab( $this->_tabs, $this->_activeTab );
		
		return true;
	}

	private $_tabView = null;
	private $_tabs = array();
	private $_activeTab = null;
};

?>