<?php
/**
 * Sends events to all registered event sinks
 *  
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class EventSource implements IEventSource {
	/**
	 * Array of EventSinks
	 *
	 * @var IEventSink[]
	 */
	private $sinks = array();
	
	/**
	 * Cosntructor
	 */
	protected function __construct() {
		// private constructor for singleton
	}

	/**
	 * Returns singleton instance 
	 * 
	 * @return EventSource
	 */
	public static function Instance() {
		static $inst = null;
		if ($inst == null) {
			$inst = new EventSource();
		}
		return $inst;
	}

	/**
	 * Register an event sink
	 */
	public function register($sink) {
		$this->sinks[] = $sink;
	}

	/**
	 * Invokes an event without result
	 *
	 * @param string $event_name
	 * @param mixed $event_params
	 * @return Status
	 */
	public function invoke_event_no_result($event_name, $event_params) {
		$result = array();
		return self::invoke_event($event_name, $event_params, $result);
	}

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
	public function invoke_event($event_name, $event_params, &$event_result) {
		$ret = new Status();
		foreach($this->sinks as $sink) {
			if (method_exists($sink, 'on_event')) {
				$ret->merge($sink->on_event($event_name, $event_params, $event_result));
			}
		}
		return $ret;
	}	
 }
?>