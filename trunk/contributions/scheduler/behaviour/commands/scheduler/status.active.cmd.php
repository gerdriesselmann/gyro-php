<?php
Load::commands('generics/status.any');

class StatusActiveSchedulerCommand extends StatusAnyCommand {
 	/**
 	 * Returns true, if changing status is allowed
 	 * 
 	 * Validates $user and than calls template method do_change_status($user)
 	 */
 	protected function do_can_execute($user) {
 		$ret = false;
 		$inst = $this->get_instance();
 		if ($inst instanceof IStatusHolder) {
 			$new_status = $this->get_params();
			$ret = !($inst->is_active());
			// Note that functions below are not be called, if $ret becomes false	
 			$ret = $ret && parent::do_can_execute($user);
 		}
 		return $ret;
 	}
	
}
