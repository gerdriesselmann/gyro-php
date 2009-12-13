<?php
/**
 * Generic create command
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */  
class CreateCommand extends CommandTransactional  {
	/**
	 * Executes commands
	 * 
	 * @return Status
	 */
	protected function do_execute() {
		$ret = new Status();
		$inst = DB::create($this->get_instance());
		if ($inst) {
			$inst->set_default_values();
			$inst->read_from_array($this->get_params());
			$ret->merge($inst->validate());
			if ($ret->is_ok()) {
				$ret->merge($inst->insert());
				$this->set_result($inst);		
			}
		}
		else {
			$ret->append(tr('Unknown model %s', 'core', array('%s' => $this->get_instance())));
		}
		
		return $ret;
	}

	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'create';
	}
	
	/**
	 * Returns a description of this command
	 */
	public function get_description() {
		$ret = '';
		$ret = tr(
			'Create new %type',
			'app',
			array('%type' => $this->get_instance())
		);
		return $ret;
	}
	
}
