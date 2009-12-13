<?php
/**
 * A base class for recheduling
 */
class ReschedulerBase implements IRescheduler {
	/**
	 * Return new schedule time for given task or FALSE if task schould end
	 *
	 * @param DAOScheduler $task
	 * @param Status Indicated if schould be rescheduled on success or failure
	 * @return datetime
	 */
	public function reschedule($task, $status) {
		$ret = false;
		$schedules = $this->get_schedules();
		$runs = ($status->is_ok()) ? $task->runs_success : $task->runs_error;
		$delta = Arr::get_item($schedules, $runs, -1);
		if ($delta > 0) {
			$ret = time() + $delta;		
		}
		return $ret;		
	}		
	
	/**
	 * Returns all schedules for this policy as array.
	 * 
	 * Array index is number of runs done, value is the difference to last run in seconds
	 *
	 * @return array
	 */
	protected function get_schedules() {
		return array();
	}
}
