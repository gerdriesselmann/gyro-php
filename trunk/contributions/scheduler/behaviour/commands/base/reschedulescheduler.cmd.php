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
		
		if ($err->is_ok()) {
			$ret->merge($this->reschedule_success($task, $err));
		}
		else {
			$ret->merge($this->reschedule_error($task, $err));
		}
		
		return $ret;
	} 
	
	/**
	 * Reschedule on success
	 * 
	 * @param DAOScheduler $task The task to reschedule
	 * @param Status $err The result of task's last run
	 * 
	 * @return Status
	 */
	protected function reschedule_success(DAOScheduler $task, Status $err) {
		$ret = new Status();
		
		$policy = $task->reschedule_success;
		$params = $this->success_get_params($task, $err);
		
		if ($this->do_reschedule($task, $policy, $params, Scheduler::STATUS_ACTIVE, $err)) {
			$ret->merge($this->success_on_rescheduled($task, $params, $err));
		}
		else {
			$ret->merge($this->success_on_finished($task, $params, $err));
		}
		
		return $ret;		
	}
	
	/**
	 * Return properties of rescheduled tasks
	 * 
	 * @param DAOScheduler $task The task to reschedule
	 * @param Status $err The result of task's last run
	 * 
	 * @return array
	 */
	protected function success_get_params(DAOScheduler $task, Status $err) {
		return array(
			'runs_success' => $task->runs_success + 1,
			'runs_error' => 0,
			'error_message' => '',
		);				
	}

	/**
	 * Called if task was successfull and rescheduled
	 *  
	 * @param DAOScheduler $task The task to reschedule
	 * @param array $params Params for updateing task
	 * @param Status $err The result of the task
	 * 
	 * @return Status  
	 */
	protected function success_on_rescheduled(DAOScheduler $task, $params, Status $err) {
		return new Status();
	}
	
	/**
	 * Called if task was successfull and does not get rescheduled any more
	 *  
	 * @param DAOScheduler $task The task to reschedule
	 * @param array $params Params for updateing task
	 * @param Status $err The result of the task
	 * 
	 * @return Status  
	 */
	protected function success_on_finished(DAOScheduler $task, $params, Status $err) {
		$this->append(CommandsFactory::create_command($task, 'delete', false));
		return new Status();
	}
	
	/**
	 * Reschedule on error
	 * 
	 * @param DAOScheduler $task The task to reschedule
	 * @param Status The result of task's last run
	 * 
	 * @return Status
	 */
	protected function reschedule_error(DAOScheduler $task, Status $err) {
		$ret = new Status();
		
		$policy = $task->reschedule_error;
		$params = $this->error_get_params($task, $err);
		
		if ($this->do_reschedule($task, $policy, $params, Scheduler::STATUS_RESCHEDULED, $err)) {
			$ret->merge($this->error_on_rescheduled($task, $params, $err));
		}
		else {
			$ret->merge($this->error_on_give_up($task, $params, $err));
		}
		
		return $ret;
	}
	
	/**
	 * Return properties of rescheduled tasks
	 * 
	 * @param DAOScheduler $task The task to reschedule
	 * @param Status $err The result of task's last run
	 * 
	 * @return array
	 */
	protected function error_get_params(DAOScheduler $task, Status $err) {
		return array(
			'runs_success' => $task->runs_success,
			'runs_error' => $task->runs_error + 1,
			'error_message' => $err->message,
		);		
	}
	
	/**
	 * Called if task was not successfull and rescheduled
	 *  
	 * @param DAOScheduler $task The task to reschedule
	 * @param array $params Params for updateing task
	 * @param Status $err The result of the task
	 * 
	 * @return Status  
	 */
	protected function error_on_rescheduled(DAOScheduler $task, $params, Status $err) {
		return new Status();
	}
	
	/**
	 * Called if task was not successfull and does not get rescheduled any more
	 *  
	 * @param DAOScheduler $task The task to reschedule
	 * @param array $params Params for updateing task
	 * @param Status $err The result of the task
	 * 
	 * @return Status  
	 */
	protected function error_on_give_up(DAOScheduler $task, $params, Status $err) {
		$this->append(CommandsFactory::create_command($task, 'update', $params));
		$this->append(CommandsFactory::create_command($task, 'status', Scheduler::STATUS_ERROR));
		return new Status();
	}	
	
	/**
	 * Actually perform rescheduling
	 * 
	 * @param DAOScheduler $task The task to reschedule
	 * @param string $policy Reschedule policy. Must map to a file and class in behaviour/scheduler
	 * @param array $params Params for updateing task
	 * @param string $new_status Either ACTIVE or RESCHEDULED
	 * @param Status $err The result of the task
	 * 
	 * @return bool TRUE if task was rescheduled. FALSE if task has had its last run and no rescheduling was done  
	 */
	protected function do_reschedule(DAOScheduler $task, $policy, $params, $new_status, Status $err) {
		$ret = false;
		
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
			
			$ret = true;
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
