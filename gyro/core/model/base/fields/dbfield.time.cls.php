<?php
require_once dirname(__FILE__) . '/dbfield.datetime.cls.php';

/**
 * A time field
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBFieldTime extends DBFieldDateTime {
	/**
	 * Returns fucntion or constant to set current time in DB (like NOW() or CURRENT_TIMESTAMP)
	 *
	 * @return string
	 */
	protected function get_db_now_constant() {
		return 'CURRENT_TIME';
	}
	
	/**
	 * Formats a value for use in DB
	 *
	 * @param datetime $value
	 * @return string
	 */
	protected function format_date_value($value) {
		return $this->quote(GyroDate::mysql_time($value, false));	
	}	

	/**
	 * Allow replacements for field in select from clause
	 */
	public function format_select() {
		return 'TIME_TO_SEC(' . DBField::format_select() . ')';	
	}
}
