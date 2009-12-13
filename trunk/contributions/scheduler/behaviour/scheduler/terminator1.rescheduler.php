<?php
/**
 * Default reschaeduler: Only run once
 */
class ReschedulerTerminator1 implements IRescheduler {
	/**
	 * Return new schedule time for given task or FALSE if task schould end
	 *
	 * @param DAOScheduler $task
	 * @param Status Indicated if schould be rescheduled on success or failure
	 * @return datetime
	 */
	public function reschedule($task, $status) {
		return false; // Only run once		
	}	
}
