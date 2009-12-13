<?php
require_once dirname(__FILE__) . '/rescheduler.base.php';

/**
 * Run twice
 */
class ReschedulerDiehard2 extends ReschedulerBase {
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
			1 => 2 * GyroDate::ONE_MINUTE,
			2 => 3 * GyroDate::ONE_MINUTE,
			3 => 4 * GyroDate::ONE_MINUTE,
			4 => 6 * GyroDate::ONE_MINUTE,
			5 => 10 * GyroDate::ONE_MINUTE,
			6 => 15 * GyroDate::ONE_MINUTE,
			7 => 30 * GyroDate::ONE_MINUTE,
			8 => 1 * GyroDate::ONE_HOUR,
			9 => 2 * GyroDate::ONE_HOUR,
			10 => 4 * GyroDate::ONE_HOUR,
			11 => 8 * GyroDate::ONE_HOUR,
			12 => 12 * GyroDate::ONE_HOUR,
			13 => 24 * GyroDate::ONE_HOUR,
			12 => 48 * GyroDate::ONE_HOUR,
		);
	}
}
