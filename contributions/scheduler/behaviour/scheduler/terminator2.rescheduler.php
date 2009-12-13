<?php
require_once dirname(__FILE__) . '/rescheduler.base.php';

/**
 * Run twice
 */
class ReschedulerTerminator2 extends ReschedulerBase {
	/**
	 * Returns all schedules for this policy as array.
	 * 
	 * Array index is number of runs done, value is the difference to last run in seconds
	 *
	 * @return array
	 */
	protected function get_schedules() {
		return array(
			0 => GyroDate::ONE_HOUR
		);
	}
}
