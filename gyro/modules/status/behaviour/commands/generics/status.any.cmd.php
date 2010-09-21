<?php
/**
 * Implementation of delegated command to set status
 * 
 * Expects new status as param.
 * 
 * Specialized status commands should derive from this class
 * 
 * @author Gerd Riesselmann
 * @ingroup Status
 */
class StatusAnyCommand extends CommandChain {
 	/**
 	 * Returns true, if changing status is allowed
 	 * 
 	 * Validates $user and than calls template method do_can_execute_status()
 	 */
 	protected function do_can_execute($user) {
 		$ret = false;
 		$inst = $this->get_instance();
 		if ($inst instanceof IStatusHolder) {
 			$new_status = $this->get_params();
			$ret = ($inst->get_status() != $new_status);
			// Note that functions below are not be called, if $ret becomes false	
 			$ret = $ret && parent::do_can_execute($user);
			$ret = $ret && $this->do_can_execute_status($user, $inst, $new_status);
 		}
 		return $ret;
 	}
 	
 	/**
 	 * Check if command can be executed
 	 *
 	 * @param mixed $user
 	 * @param IStatusHolder $inst
 	 * @param mixed $new_status
 	 * @return bool
 	 */
 	protected function do_can_execute_status($user, IStatusHolder $inst, $new_status) {
 		return true;
 	}
 	
 	/**
 	 * Change status 
 	 * 
 	 * @return Status
 	 */
 	protected function do_execute() {
 		$ret = new Status();
 		Load::commands('generics/setstatus');
 		$this->append(new SetstatusCommand($this->get_instance(), $this->get_params()));
 		$this->set_result($this->get_instance());
 		return $ret;
 	}
 	
	/**
	 * Returns params
	 *
	 * @return mixed
	 */
	public function get_params() {
		$ret = parent::get_params();
		if (is_array($ret)) {
			$ret = Arr::get_item($ret, 0, '');
		}
		return $ret;
	} 	

	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'status';
	}
	
	/**
	 * Returns a description of this command
	 */
	public function get_description() {
		$text = 'Change to ' . Cast::string($this->get_params());
		$transmods = array('app', 'status');
		$inst = $this->get_instance();
 		if ($inst instanceof IDataObject) {
 			array_unshift($transmods, $inst->get_table_name());
 		}
		return tr(
			$text, 
			$transmods
		);	
	} 
}
