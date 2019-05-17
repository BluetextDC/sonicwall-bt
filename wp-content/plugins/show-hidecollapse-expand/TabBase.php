<?php

abstract class bg_show_hide_TabBase {
	public function __construct ( $tabName, $tabCaptionText, $pluginContext){
		$this->_tabName = $tabName;
		$this->_tabCaptionText = $tabCaptionText;
		$this->_pluginContext = $pluginContext;
	}
	
	public function getTabName() {
		return $this->_tabName;
	}
	
	public function getPluginContext() {
		return $this->_pluginContext;
	}

	public function getTabCaptionText() {
		return $this->_tabCaptionText;
	}
	
	abstract public function display();
	
	private $_tabName = null;
	private $_tabCaptionText = null;
	private $_pluginContext = null;
};

?>