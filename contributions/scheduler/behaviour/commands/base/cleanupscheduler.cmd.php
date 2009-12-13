<?php
Load::commands('scheduler/process');

/**
 * Find tasks that have crashed and treat them as failed
 * 
 * @author Gerd Riesselmann
 */
class CleanupSchedulerBaseCommand extends ProcessSchedulerCommand {
	/**
	 * Does processing
	 */
	protected function do_execute() {
		$ret = new Status();
		
		$tasks = new DAOScheduler();
		$tasks->status = Scheduler::STATUS_PROCESSING;
		$tasks->add_where('scheduledate', '<', time() - 2* Date::ONE_HOUR);
		$tasks->find();
		$err = new Status(tr('Task has crashed, possible out of memory or time', 'scheduler'));
		while ($tasks->fetch()) {
			$ret->merge($this->on_error(clone($tasks), $err));
			if ($ret->is_error()) {
				break;
			}	
		}
		return $ret;
	} 
	
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'cleanup';
	}
	
	/**
	 * Returns a description of this command
	 */
	public function get_description() {
		return tr('Cleanup', 'scheduler');	
	} 	
}