<?php
require_once dirname(__FILE__) . '/dbfield.datetime.cls.php';

/**
 * A date only field in DB
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBFieldDate extends DBFieldDateTime {
	/**
	 * Returns fucntion or constant to set current time in DB (like NOW() or CURRENT_TIMESTAMP)
	 *
	 * @return string
	 */
	protected function get_db_now_constant() {
		return 'CURRENT_DATE';
	}
	
	/**
	 * Formats a value for use in DB
	 *
	 * @param datetime $value
	 * @return string
	 */
	protected function format_date_value($value) {
		return $this->quote(GyroDate::mysql_date($value, false));	
	}
}
