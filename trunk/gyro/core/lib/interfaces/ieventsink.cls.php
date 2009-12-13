<?php
/**
 * Event sink interface
 * 
 * Event sink are the targets of events, that actual process them
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IEventSink {	
	/**
	 * Invoked to handle events
	 * 
	 * Events can be anything, and they are invoked through the router
	 * One event is "cron", it has no parameters
	 * 
	 * @param string Event name
	 * @param mixed Event parameter(s)
	 */
	public function on_event($event_name, $event_params, &$result);
}
?>