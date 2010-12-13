<?php
Load::commands('generics/status.any');

/**
 * Command to set status to read
 * 
 * @author Gerd Riesselmann
 * @ingroup Notifications
 */
class StatusReadNotificationsCommand extends StatusAnyCommand {
 	/**
 	 * Change status 
 	 * 
 	 * @return Status
 	 */
 	protected function do_execute() {
 		$ret = new Status();
 		Load::models('notifications');
 		$inst = $this->get_instance();
 		$inst->read_through = Notifications::READ_MARK_MANUALLY;
 		$ret->merge(parent::do_execute());
 		return $ret;
 	}
}
