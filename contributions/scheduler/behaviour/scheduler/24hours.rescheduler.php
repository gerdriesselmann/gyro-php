<?php
/**
 * Default reschaeduler: Only run once
 */
class Rescheduler24hours implements IRescheduler {
	/**
	 * Return new schedule time for given task or FALSE if task schould end
	 *
	 * @param DAOScheduler $task
	 * @param Status Indicated if schould be rescheduled on success or failure
	 * @return datetime
	 */
	public function reschedule($task, $status) {
		return time() + GyroDate::ONE_DAY;		
	}	
}
