<?php
/**
 * Overload create command, to respect excusive paramter
 */
class CreateSchedulerBaseCommand extends CommandChain {
	protected function do_execute() {
		$ret = new Status();
		$params = $this->get_params();
		
		//Create the Sourcedomain
		$created = false;
		$ret->merge($this->create_scheduler($params, $created));
		if ($ret->is_ok()) {
			$ret->merge($this->check_exclusive($params, $created));
		}
		return $ret;
	}
	
	/**
	 * Create the Scheduler Task
	 * 
	 * @return Status
	 */
	protected function create_scheduler($params, &$created) {
		Load::commands('generics/create');
		$cmd = new CreateCommand('scheduler', $params);
		$ret = $cmd->execute();
		if ($ret->is_ok()) {
			$created = $cmd->get_result();
			$this->set_result($created);
		}
		return $ret;		
	}
	
	/**
	 * If exclusive, delate old
	 * 
	 * @return Status
	 */
	protected function check_exclusive($params, $created) {
		$ret = new Status();
		if (Arr::get_item($params, 'exclusive', false)) {
			$scheduler = new DAOScheduler();
			$scheduler->add_where('action', '=', $created->action);
			$scheduler->add_where('scheduledate', '<', $created->scheduledate);
			$scheduler->add_where('status', '!=', Scheduler::STATUS_PROCESSING);
			$ret->merge($scheduler->delete(DAOScheduler::WHERE_ONLY));
		}
		return $ret;
	}
}