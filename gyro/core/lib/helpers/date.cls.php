<?php
/**
 * Common Date and DateTime functions
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class GyroDate {
	const ONE_MINUTE = 60; // 60 sec
	const ONE_HOUR = 3600; // 60 * 60 sec 
	const ONE_DAY = 86400; // 60 * 60 * 24 sec
	
	const MONDAY = 1;
	const TUESDAY = 2;
	const WEDNESDAY = 3;
	const THURSDAY = 4;
	const FRIDAY = 5;
	const SATURDAY = 6;
	const SUNDAY = 0;
	
	const DAY_MONTH_YEAR = 'DMY';
	const MONTH_DAY_YEAR = 'MDY';
	const YEAR_MONTH_DAY = 'YMD';
	
	/**
	 * Array of non-working days. 0 stands for Sunday, 1 for Monday, .. 6 for Saturday
	 *
	 * @var array
	 */
	public static $non_workdays = array(self::SUNDAY, self::SATURDAY); 
	/**
	 * Array of holiday-dates. Date can be anything understand by GyroDate::datetime() 
	 *
	 * @var array
	 */
	public static $holidays = array();
	/**
	 * How local dates are ordered
	 *
	 * @var string
	 */
	public static $local_date_order = self::MONTH_DAY_YEAR;
	
	/**
	 * Static. Converts string retrieved from PHP to date
	 *
	 * @param String Anything that possible can be interpreted as a date
	 * @return date
	 */
	public static function datetime($string) {
		if (is_int($string)) {
			return $string;
		}
		
		$time = false;
		if(empty($string)) {
			// use "now":
			$time = time();
		}
		elseif (preg_match('/^\d{14}$/', $string)) {
			// it is mysql timestamp format of YYYYMMDDHHMMSS?
			$time = mktime(
				substr($string, 8, 2),
				substr($string, 10, 2),
				substr($string, 12, 2),
				substr($string, 4, 2),
				substr($string, 6, 2),
				substr($string, 0, 4)
			);

		}
		elseif (is_numeric($string)) {
			// it is a numeric string, we handle it as timestamp
			$time = (int)$string;
		}
		else {
			// strtotime should handle it
			$time = strtotime($string);
			if ($time == -1 || $time === false) {
				// strtotime() was not able to parse $string, use "now":
				$time = false;
			}
		}
		return $time;
	}
	
	/**
	 * Calls strptime, but returns unix timestamp
	 * 
	 * Usually it should be enough to call GyroDate::datetime(), but if you deal with unusual
	 * date and time string of known format, use this function.
	 *
	 * @param string $date The datetime as a string
	 * @param string $format See strptime for format description
	 * @return mixed Timestamp or FALSE
	 */
	public static function parse($date, $format) {
		$ret = false;
		$dt_arr = strptime($date, $format);
		if (is_array($dt_arr)) {
			$ret = mktime(
				$dt_arr['tm_hour'],
				$dt_arr['tm_min'],
				$dt_arr['tm_sec'],
				$dt_arr['tm_mon'] + 1,
				$dt_arr['tm_mday'],
				$dt_arr['tm_year'] + 1900	
			);
		}
		return $ret;
	}	

	/**
	 * Static. Converts timestamp to MYSQL Data
	 *
	 * @param int Timestamp
	 * @return string
	 */
	public static function mysql_date($date, $includetime = true) {
		if ($includetime) {
			return date('Y-m-d H:i:s', $date);
		} else {
			return date('Y-m-d', $date);
		}
	}

	/**
	 * Static. Converts timestamp to MYSQL Time
	 *
	 * @param int Timestamp
	 * @return string
	 */	
	public static function mysql_time($date) {
		return date('H:i:s', $date);
	}

	/**
	 * Static. Converts timestamp to ISO DateTime string
	 *
	 * @param int Timestamp
	 * @return string
	 */
	public static function iso_date($date) {
		return date('c', $date);
	}
	
	/**
	 * Static. Converts timestamp to RFC 2822 DateTime string
	 *
	 * @param int Timestamp
	 * @return string
	 */
	public static function rfc_date($date) {
		return date('r', $date);
	}
	
	/**
	 * Converts timestamp to string used in HTTP-Header fields (such as "Expires") 
	 *
	 * @param timestamp $date
	 * @return string
	 */
	public static function http_date($date) {
		return gmdate('D, d M Y H:i:s \G\M\T', $date);
	}

	/**
	 * Static. Converts timestamp to DateTime string that respects locale settings
	 *
	 * @param int Timestamp
	 * @param bool $includetime True to include time
	 * @return string
	 */
	public static function local_date($date, $includetime = true) {
		$format = '';
		switch (self::$local_date_order) {
			case self::DAY_MONTH_YEAR:
				$format = ($includetime) ? 'j.n.Y, G:i:s' : 'j.n.Y';
				break;
			case self::MONTH_DAY_YEAR:
				$format = ($includetime) ? 'n/j/Y, G:i:s' : 'n/j/Y';
				break;
			case self::YEAR_MONTH_DAY:
			default:
				$format = ($includetime) ? 'Y-m-d, H:i:s' : 'Y-m-d';
				break;
		}
		return date($format, $date);
	}

	/**
	 * Static. Adds the number of months to given date
	 *
	 * @param int Timestamp
	 * @param int Number of Months
	 *
	 * @return int Timestamp
	 */
	public static function add_months($date, $months) {
		if ($months === 0) {
			return $date;
		}

		$newDate = strtotime("+$months months", $date);
		$arrDate = getdate($date);
		$arrNewDate = getdate($newDate);
		$monNew = $arrNewDate["mon"] + 12 * ($arrNewDate["year"] - $arrDate["year"]);
		$mon = $arrDate["mon"];

		if ($monNew > $mon + $months) {
			// ups, we added more then one month
			// Make last day of next month
			$newDate = mktime(
				$arrDate["hours"], 
				$arrDate["minutes"], 
				$arrDate["seconds"],
				$mon + $months + 1, 
				0, 
				$arrDate["year"]
			);
		}
		return $newDate;
	}
	
	/**
	 * Static. Substracts the number of months to given date
	 *
	 * @param int Timestamp
	 * @param int Number of Months
	 *
	 * @return int Timestamp
	 */
	public static function substract_months($date, $months) {
		if ($months === 0) {
			return $date;
		}

		$newDate = strtotime("-$months months", $date);
		$arrDate = getdate($date);
		$arrNewDate = getdate($newDate);
		//print_r($arrDate);
		//print_r($arrNewDate);
		//die();
		$monNew = $arrNewDate["mon"] - 12 * ($arrDate["year"] - $arrNewDate["year"]);
		$mon = $arrDate["mon"];

		if ($monNew < $mon - $months) {
			// ups, we substracted more then one month
			// Make first day of next month
			$newDate = mktime(
				$arrDate["hours"], 
				$arrDate["minutes"], 
				$arrDate["seconds"],
				$mon - $months + 1, 
				1, 
				$arrDate["year"]
			);
		}
		else if ($monNew > $mon - $months) {
			// ups, we substracted less then one month
			// Make last day of next month
			$newDate = mktime(
				$arrDate["hours"], 
				$arrDate["minutes"], 
				$arrDate["seconds"],
				$mon - $months + 1, 
				0, 
				$arrDate["year"]
			);
		}
		return $newDate;
	}
	
	/**
	 * Returns days since 1.1.2000
	 */
	public static function convert_to_days($timestamp) {
		$arr_date = getdate($timestamp);
		
		$ret = ($arr_date['year'] - 2000) * 365;
		$ret += $arr_date['yday'];
		
		return $ret; 
	}

	/**
	 * Casts date to day (That is 0:00:00)
	 * 
	 * @param date $date
	 * @return date
	 */
	public static function day($date) {
		return self::set_time(GyroDate::datetime($date), 0);
	}

	/**
	 * Casts date to month (That is 1st, 0:00:00)
	 * 
	 * @param date $date
	 * @return date
	 */
	public static function month($date) {
		$ret = GyroDate::datetime($date);
		$ret = self::set_time($ret, 0);
		$ret = self::set_day($ret, 1);
		return $ret;		
	}	
	
	/**
	 * Return weekday of given Date
	 * 
	 * @param date $date
	 * @return int 0 = Sunday, 1 = Monday, ..., 6 = Saturday
	 */
	public static function get_weekday($date) {
		$arr_date = getdate(self::datetime($date));
		return $arr_date['wday'];
	}
	
	/**
	 * Returns true if given date is a holiday. 
	 * 
	 * Attention: This method will not necessarily recognize Sundays as holidays! It only 
	 * compares given date with self::$holidays.
	 *
	 * @param date $date
	 * @return bool
	 */
	public static function is_holiday($date) {
		$date = self::day($date);
		// Check date for holidays
		foreach(self::$holidays as $holiday) {
			$holiday = self::day($holiday);
			if ($date == $holiday) {
				return true; 
			}
		}
		return false;				
	}
	
	/**
	 * Returns if given date is a work day (that is: not saturday or sunday) 
	 *
	 * @param date $date
	 * @return bool
	 */
	public static function is_workday($date) {
		$date = self::day($date);
		// Check weekday
		if (in_array(self::get_weekday($date), self::$non_workdays)) {
			return false;		 	
		}
		// Check date for holidays
		return !self::is_holiday($date);
	}
	
	/**
	 * Sets Time on given date (keeps date)
	 *
	 * @param date $date
	 * @param int $hour
	 * @param int $min
	 * @param int $sec
	 * @return date
	 */
	public static function set_time($date, $hour, $min = 0, $sec = 0) {
		$arr_date = getdate($date);
		return mktime($hour, $min, $sec, $arr_date['mon'], $arr_date['mday'], $arr_date['year']);
	}

	/**
	 * Sets day on given date (keeps date)
	 *
	 * @param date $date
	 * @param int $day
	 * @return date
	 */
	public static function set_day($date, $day) {
		$arr_date = getdate($date);
		return mktime($arr_date['hours'], $arr_date['minutes'], $arr_date['seconds'], $arr_date['mon'], $day, $arr_date['year']);
	}
	
	
	/**
	 * Adds work days to given date
	 * 
	 * E.g. 27th February, 2008 is a Wednesday. If you add 5 workdays, you get Wednesday, March 5th.
	 * 
	 * You may also pass negative days, which will substract workdays. If you pass 0 for $days_to_add,
	 * $date will be forced to be a workday, which is: Saturday and Sunday will be turned into Monday,
	 * but all other days will be kept untouched 
	 *
	 * @param date $date
	 * @param int $days_to_add
	 * @return date
	 */
	public static function add_workdays($date, $days_to_add) {
		$absdays = abs($days_to_add);
		$sign = ($absdays == $days_to_add) ? 1 : -1; 
		$one_day = $sign * self::ONE_DAY;
		while (!self::is_workday($date)) {
			$date += $one_day;
		}		
		for ($i = 0; $i < $absdays; $i++) {
			// Add a day
			$date += $one_day;
			while (!self::is_workday($date)) {
				$date += $one_day;
			}	
		}
		return $date;
	}	

	/**
	 * Returns true if the given datetime is of today
	 */
	public static function is_today($time) {
		return self::day($time) == self::day(time()); 
	}
	
	/**
	 * Returns true if the given datetime is of this month
	 */
	public static function is_this_month($time) {
		$arr_time = getdate(self::datetime($time));
		$arr_now = getdate(time());
		return ($arr_now['year'] == $arr_time['year'] && $arr_now['mon'] == $arr_time['mon']);	
	}

	/**
	 * Returns true if the given datetime is of this year
	 */
	public static function is_this_year($time) {
		$arr_time = getdate(self::datetime($time));
		$arr_now = getdate(time());
		return ($arr_now['year'] == $arr_time['year']);			
	}
}

if (!class_exists('Date')) {
	class Date extends GyroDate {}
}