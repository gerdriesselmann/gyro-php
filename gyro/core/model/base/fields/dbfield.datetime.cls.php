<?php
/**
 * A date and time field in DB
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBFieldDateTime extends DBField {
	const NOW = 'now';
	const TIMESTAMP = 32;	
	
	/**
	 * Returns the default value for this field
	 *
	 * @return mixed
	 */
	public function get_field_default() {
		if ($this->has_policy(self::TIMESTAMP)) {
			return null; // Timestamps are managed entirely by DB
		}
		$ret = parent::get_field_default();
		if ($ret == self::NOW) {
			$ret = time();
		}
		return $ret;		
	}
	
	/**
	 * Reformat passed value to DB format
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function format($value) {
		if ($this->has_policy(self::TIMESTAMP)) {
			return 'DEFAULT';	 
		}
		
		// Treat '', false and 0 as NULL in updates and inserts
		if (empty($value)) {
			return $this->do_format_null(null);
		}
		else {
			return $this->format_where($value);
		}
	}
	
	/**
	 * Format for use in WHERE clause
	 *  
	 * @param mixed $value
	 * @return string
	 */
	public function format_where($value) {
		if ($this->is_null($value)) {
			return parent::format($value);
		}
		else if ($value == self::NOW) {
			return $this->get_db_now_constant();
		} 
		else {
			return $this->format_date_value(GyroDate::datetime($value));;
		}
	}	
	
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param mixed $value
	 * @return Status
	 */
	public function validate($value) {
		$ret = parent::validate($value);
		if ($ret->is_ok()) {
			$test = GyroDate::datetime($value);
			if ($test === false) {
				$ret->append(tr(
					'%field must be a date and time value', 
					'core', 
					array(
						'%field' => $this->get_field_name_translation()
					)
				));
			}
		}
		return $ret;
	}	
	
	/**
	 * Returns fucntion or constant to set current time in DB (like NOW() or CURRENT_TIMESTAMP)
	 *
	 * @return string
	 */
	protected function get_db_now_constant() {
		return 'CURRENT_TIMESTAMP';
	}
	
	/**
	 * Formats a value for use in DB
	 *
	 * @param datetime $value
	 * @return string
	 */
	protected function format_date_value($value) {
		return $this->quote(GyroDate::mysql_date($value));	
	}

	/**
	 * Allow replacements for field in select from clause
	 */
	public function format_select() {
		return 'UNIX_TIMESTAMP(' . parent::format_select() . ')';	
	}

	/**
	 * Transform result from SELECT to native
	 * 
	 * @param mixed $value
	 * @return mixed    
	 */
	public function convert_result($value) {
		return is_null($value) ? null : GyroDate::datetime($value);
	}	
}
