<?php
require_once dirname(__FILE__) . '/rescheduler.base.php';

/**
 * Rerun 24 times, with delay increasing up to 3 days
 */
class ReschedulerDiehard3 extends ReschedulerBase {
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
			13 => 1 * GyroDate::ONE_DAY,
			14 => 1 * GyroDate::ONE_HOUR,
			15 => 1 * GyroDate::ONE_HOUR,
			16 => 2 * GyroDate::ONE_HOUR,
			17 => 2 * GyroDate::ONE_HOUR,
			18 => 2 * GyroDate::ONE_HOUR,
			19 => 2 * GyroDate::ONE_HOUR,
			20 => 3 * GyroDate::ONE_HOUR,
			21 => 3 * GyroDate::ONE_HOUR,
			22 => 3 * GyroDate::ONE_HOUR,
			23 => 3 * GyroDate::ONE_HOUR,
			24 => 3 * GyroDate::ONE_HOUR,
			25 => 3 * GyroDate::ONE_HOUR,
		);
	}
}
