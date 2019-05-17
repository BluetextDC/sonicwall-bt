<?php

class bg_show_hide_PluginContext {
	public function __construct(){
	}
	
	/* Admin post URL */
	public function setAdminPostUrl( $adminPostUrl) {
		$this->_adminPostUrl = $adminPostUrl;
	}
	public function getAdminPostUrl() {
		return $this->_adminPostUrl;
	}

	/* Redirect value */
	public function setRedirectValue( $redirectValue) {
		$this->_redirectValue = $redirectValue;
	}	
	public function getRedirectValue() {
		return $this->_redirectValue;
	}

	/* Plugin Version */
	public function setPluginVersion( $pluginVersion) {
		$this->_pluginVersion = $pluginVersion;
	}	
	public function getPluginVersion() {
		return $this->_pluginVersion;
	}
	
	/* Plugin Name */
	public function setPluginName( $pluginName) {
		$this->_pluginName = $pluginName;
	}	
	public function getPluginName() {
		return $this->_pluginName;
	}
	
	/* Plugin Slug */
	public function setPluginSlug( $pluginSlug) {
		$this->_pluginSlug = $pluginSlug;
	}	
	public function getPluginSlug() {
		return $this->_pluginSlug;
	}
	
	/* Plugin Options */
	public function setEffectsEnabledOption( $effectsEnabled) {
		$this->_effectsEnabled = $effectsEnabled;
	}	
	public function getEffectsEnabledOption() {
		return $this->_effectsEnabled;
	}
	
	public function setAnimationEffect( $animationEffect) {
		$this->_animationEffect = $animationEffect;
	}	
	public function getAnimationEffect() {
		return $this->_animationEffect;
	}
	
	public function setAnimationSpeed( $animationSpeed) {
		$this->_animationSpeed = $animationSpeed;
	}	
	public function getAnimationSpeed() {
		return $this->_animationSpeed;
	}
	
	public function setStickToBottom( $stickToBottom) {
		$this->_stickToBottom = $stickToBottom;
	}
	public function getStickToBottom() {
		return $this->_stickToBottom;
	}
	
	public function setPreset1( $preset1) {
		$this->_preset1 = $preset1;
	}
	public function getPreset1() {
		return $this->_preset1;
	}
	
	private $_redirectValue = null;
	private $_adminPostUrl = null;
	private $_pluginVersion = null;
	private $_pluginName = null;
	private $_pluginSlug = null;
	private $_effectsEnabled = null;
	private $_animationSpeed = null;
	private $_animationEffect = null;
	private $_stickToBottom = null;
	private $_preset1 = null;
	private $_preset2 = null;
	private $_preset3 = null;
};

?>