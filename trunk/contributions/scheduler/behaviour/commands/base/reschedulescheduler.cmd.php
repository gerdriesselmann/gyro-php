<?php
/**
 * Reschedule a given task
 */
class RescheduleSchedulerBaseCommand extends CommandChain {
	/**
	 * Does executing
	 */
	protected function do_execute() {
		$ret = new Status();

		/* @var $task DAOScheduler */
		$task = $this->get_instance();
		/* @var $err Status */
		$err = $this->get_params();
		$policy = '';
		$new_status = '';
		
		if ($err->is_ok()) {
			$policy = $task->reschedule_success;
			$new_status = Scheduler::STATUS_ACTIVE;
		}
		else {
			$policy = $task->reschedule_error;
			$new_status = Scheduler::STATUS_RESCHEDULED;
		}

		$params = array(
			'runs_success' => $err->is_error() ? $task->runs_success : $task->runs_success + 1,
			'runs_error' => $err->is_error() ? $task->runs_error + 1 : 0,
			'error_message' => $err->is_error() ? $err->message : '',
		);
		
		$policy = strtolower($policy);
		$file = 'behaviour/scheduler/' . $policy . '.rescheduler.php';
		$cls = 'Rescheduler' . String::to_upper($policy, 1);
		Load::first_file($file);
		$rescheduler = new $cls();

		$newdate = $rescheduler->reschedule($task, $err);
		if ($newdate !== false) {
			// Reschedule
			$params['scheduledate'] = $newdate;
			$this->append(CommandsFactory::create_command($task, 'update', $params));
			$this->append(CommandsFactory::create_command($task, 'status', $new_status));
		}
		else {
			// No rescheduling
			if ($err->is_ok()) {
				$this->append(CommandsFactory::create_command($task, 'delete', false));
			}
			else {
				$this->append(CommandsFactory::create_command($task, 'update', $params));
				$this->append(CommandsFactory::create_command($task, 'status', Scheduler::STATUS_ERROR));
			}
		}
		
		return $ret;
	} 
	
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'reschedule';
	}
	
	/**
	 * Returns a description of this command
	 */
	public function get_description() {
		return tr('Reschedule', 'scheduler');	
	} 	
		
}
