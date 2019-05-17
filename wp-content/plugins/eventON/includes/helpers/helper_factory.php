<?php
/**
 * Helpers for eventon
 */

if ( ! defined( 'ABSPATH' ) )	die();

if(!function_exists('EVO_Prods')){
	function EVO_Prods(){ return evo_prods::instance();}
}
if(!function_exists('EVO_Error')){
	function EVO_Error(){ return EVO_Error::instance();}
}