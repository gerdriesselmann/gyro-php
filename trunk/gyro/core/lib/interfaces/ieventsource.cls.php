<?php
/**
 * Something issuing events
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IEventSource {
	/**
	 * Triggers events
	 * 
	 * Events can be anything, and they are invoked through the router
	 * One event is "cron", it has no parameters
	 * 
	 * @param string Event name
	 * @param mixed Event parameter(s)
	 * @return Status
	 */
	public function invoke_event($event_name, $event_params, &$result);

	/**
	 * Invokes an event without result
	 *
	 * @param string $event_name
	 * @param mixed $event_params
	 * @return status
	 */
	public function invoke_event_no_result($event_name, $event_params);
}
