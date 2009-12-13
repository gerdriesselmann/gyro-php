<?php
require_once dirname(__FILE__) . '/rescheduler.base.php';

/**
 * Run three times
 */
class ReschedulerTerminator3 extends ReschedulerBase {
	/**
	 * Returns all schedules for this policy as array.
	 * 
	 * Array index is number of runs done, value is the difference to last run in seconds
	 *
	 * @return array
	 */
	protected function get_schedules() {
		return array(
			0 => GyroDate::ONE_HOUR,
			1 => GyroDate::ONE_HOUR,
		);
	}
}
