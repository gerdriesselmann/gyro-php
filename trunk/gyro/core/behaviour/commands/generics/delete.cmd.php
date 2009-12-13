<?php
/**
 * Delete command
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */  
class DeleteCommand extends CommandTransactional {
	/**
	 * Executes commands
	 * 
	 * @return Status
	 */
	protected function do_execute() {
		$ret = new Status();
		$o = $this->get_instance();		
		if ($o) {
			$ret->merge($o->delete());
			$this->set_result($o);
			if ($ret->is_ok()) {
				$this->clear_history($o);
			}
		}
		else {
			$ret->append(tr('Delete Command: No instance set to delete', 'core'));
		}
		return $ret;
	}
	
	/**
	 * Clear history 
	 *
	 * @param IActionSource $deleted_instance
	 */
	protected function clear_history(IActionSource $deleted_instance) {
		$actions = $deleted_instance->get_actions(AccessControl::get_current_aro(), '');
		foreach($actions as $action) {
			if ($action instanceof IAction) {
				/* @var $action IAction */
				$url = ActionMapper::get_url($action->get_name(), $deleted_instance);
				History::remove($url);
			}
		}		
	}
		
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'delete';
	}
	
	/**
	 * Returns a description of this command
	 */
	public function get_description() {
		$ret = '';
		$inst = $this->get_instance();
		if ($inst) {
			$ret = tr(
				'Delete',
				'app'
			);
		}
		return $ret;
	}

} 