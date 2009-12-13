<?php
/**
 * Command to trigger an event on the EventSource class
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */
class TriggerEventCommand extends CommandBase {
	public function can_execute($user) {
		return true;
	}
	
	public function execute() {
		return EventSource::Instance()->invoke_event_no_result($this->get_instance(), $this->get_params());
	}	
}
	
?>