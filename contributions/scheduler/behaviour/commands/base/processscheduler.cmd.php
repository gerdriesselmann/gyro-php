<?php
/**
 * Run the next job in scheduler list
 */
class ProcessSchedulerBaseCommand extends CommandChain {
	/**
	 * Does processing
	 */
	protected function do_execute() {
		$ret = new Status();
		$task = $this->get_params();
		$err_task = $this->run_task($task);
		$this->append(CommandsFactory::create_command($task, 'reschedule', $err_task));
		if ($err_task->is_error()) {
			$ret->merge($this->on_error($task, $err_task));
		}
		else {
			$ret->merge($this->on_success($task, $err_task));
		}
		return $ret;
	} 
	
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'process';
	}
	
	/**
	 * Returns a description of this command
	 */
	public function get_description() {
		return tr('Process', 'scheduler');	
	} 	
	
	/**
	 * Run a task
	 *
	 * @param DAOScheduler $task
	 * @return Status
	 */
	protected function run_task($task) {
		$ret = new Status();
		if (empty($task)) {
			return $ret;		
		}
		
		Load::components('console');
		$ret->merge(Console::invoke($task->action));
		return $ret;
	}
	
	/**
	 * Handle errors  
	 *
	 * @param DAOScheduler $task
	 * @param Status $err
	 * @return Status
	 */
	protected function on_error($task, $err) {
		$ret = new Status();

		if (Config::has_feature(ConfigScheduler::SEND_ERROR_MAIL)) {
			Load::commands('generics/mail');
			$cmd_admin = new MailCommand(
				tr('Task "%t" failed', 'scheduler', array('%t' => $task->name)),
				Config::get_value(Config::MAIL_ADMIN),
				'scheduler/mail/error_admin',
				array(
					'error' => $err,
					'task' => $task			
				)
			);
			$this->append($cmd_admin);
		}
		
		return $ret;
	}

	/**
	 * Handle success  
	 *
	 * @param DAOScheduler $task
	 * @param Status $err
	 * @return Status
	 */
	protected function on_success($task, $err) {
		$ret = new Status();
		return $ret;
	}
}
