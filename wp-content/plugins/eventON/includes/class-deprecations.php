<?php
/**
 * Deprecated items for eventon
 */

// evo version 2.6.2
class evo_this_event extends EVO_Event{

	public function __construct($event_id){
		_deprecated_function( 'evo_this_event()', 'EventON 2.6.1' ,'EVO_Event()');

		parent::__construct($event_id);
		
	}
}