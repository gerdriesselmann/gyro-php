<?php
/**
 * Command to set status. Expects new status as $param
 *
 * This command sets status on given IStatusHolder instance
 * and updates afterwards 
 * 
 * @author Gerd Riesselmann
 * @ingroup Status
 */
class SetstatusCommand extends CommandTransactional {
	/**
	 * Does executing
	 * 
	 * @return Status
	 */
	public function do_execute() {
		$ret = new Status();
		$inst = $this->get_instance();
		$new_status = $this->get_params();
		if ($inst instanceof IStatusHolder) {
			if ($inst->get_status() != $new_status) {
				$inst->set_status($new_status);
				if ($inst instanceof DataObjectBase) {
					$ret->merge($inst->update());
				}
			}
		}
		else {
			$ret->merge($this->illegal_data_error());
		}
		return $ret; 
	}
}
