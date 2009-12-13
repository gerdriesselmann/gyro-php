<?php
/**
 * Generic update command
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */  
class UpdateCommand extends CommandTransactional  {
	/**
	 * Executes commands
	 * 
	 * @return Status
	 */
	protected function do_execute() {
		$ret = new Status();
	
		$inst = $this->get_instance();
		$inst->read_from_array($this->get_params());
		$ret->merge($inst->validate());
		if ($ret->is_ok()) {
			$ret->merge($inst->update());
			$this->set_result($inst);
		}
		return $ret;
	}

	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'update';
	}
	
	/**
	 * Returns a description of this command
	 */
	public function get_description() {
		$ret = '';
		$ret = tr(
			'Update instance',
			'app'
		);
		return $ret;
	}
	
} 
