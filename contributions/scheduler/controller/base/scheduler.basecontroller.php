<?php
/**
 * SchedulerController
 *
 */
class SchedulerBaseController extends ControllerBase {
	/**
	 * Returns array of routes
	 *
	 * @return array
	 */
	public function get_routes() {
		$ret = array(
			'process' => new ExactMatchRoute('scheduler/process', $this, 'scheduler_process', new ConsoleOnlyRenderDecorator()),
			'test' => new ExactMatchRoute('scheduler/test', $this, 'scheduler_test', new ConsoleOnlyRenderDecorator()),
			'watchdog' => new ExactMatchRoute('scheduler/watchdog', $this, 'scheduler_watchdog', new ConsoleOnlyRenderDecorator())
		);
		return $ret;
	}
	
	/**
	 * Load classes before action
	 */
	public function before_action() {
		Load::models('scheduler');
	}
	
	/**
	 * Scheduler Action to execute the next task
	 *
	 * @param PageData $page_data
	 */
	public function action_scheduler_process($page_data) {
		$task = Scheduler::get_next_task();
		if ($task) {
			$cmd = CommandsFactory::create_command('scheduler', 'process', $task);
			$page_data->status = $cmd->execute();
		}
	}

	/**
	 * Look for tasks that obiously have crashed and treat them as failed
	 * 
	 * It may happen that a task is processed but crashes due to memory or time limitations. 
	 * Since these events can not be catched within PHP, the watchdogs checks for tasks 
	 * staying PROCESSING for longer then two hours and closes them, respecting the error 
	 * rescheduling policies.
	 *
	 * @param PageData $page_data
	 */
	public function action_scheduler_watchdog($page_data) {
		$cmd = CommandsFactory::create_command('scheduler', 'cleanup', false);
		$page_data->status = $cmd->execute();
	}
	
	/**
	 * A simple task to be invoked for testing purposes
	 * 
	 * Does nothing but sleeping for half a minute
	 *
	 * @param PageData $page_data
	 */
	public function action_scheduler_test($page_data) {
		sleep(0.5 * GyroDate::ONE_MINUTE);
	}
}