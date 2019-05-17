<?php
/**
 * EVO Calendar
 * @version 2.6.1
 */

class EVO_Calendar{
	private $props = false;
	public function __construct(){

	}

	function set_prop($options_field_name){
		$this->props = get_option( $options_field_name );
	}

	function get_prop($field){
		if(!isset($this->props[$field])) return false;
		return maybe_unserialize($this->props[$field]);
	}


}