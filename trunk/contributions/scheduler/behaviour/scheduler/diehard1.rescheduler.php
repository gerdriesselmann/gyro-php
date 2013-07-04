<?php
require_once dirname(__FILE__) . '/rescheduler.base.php';

/**
 * Rerun 6 times, with delay increasing every time
 */
class ReschedulerDiehard1 extends ReschedulerBase {
	/**
	 * Returns all schedules for this policy as array.
	 * 
	 * Array index is number of runs done, value is the difference to last run in seconds
	 *
	 * @return array
	 */
	protected function get_schedules() {
		return array(
			0 => 2 * GyroDate::ONE_MINUTE,
			1 => 15 * GyroDate::ONE_MINUTE,
			2 => 30 * GyroDate::ONE_MINUTE,
			3 => 1 * GyroDate::ONE_HOUR,
			4 => 2 * GyroDate::ONE_HOUR,
			5 => 3 * GyroDate::ONE_HOUR,
			6 => 6 * GyroDate::ONE_HOUR,
			7 => 12 * GyroDate::ONE_HOUR,
		);
	}
}
