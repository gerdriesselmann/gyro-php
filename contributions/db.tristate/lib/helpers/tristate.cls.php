<?php
/**
 * Provides tristate logic
 * 
 * @author Gerd Riesselmann
 * @ingroup Tristate
 */
class Tristate {
	const YES = 'TRUE';
	const NO = 'FALSE';
	const UNKOWN = 'UNKNOWN';
	
	/**
	 * Returns possible states as associative array with DB vlaue as key and localized display value as value
	 *  
	 * @return array
	 */
	public static function get_states() {
		return array(
			self::YES => tr(self::YES, 'tristate'),
			self::NO => tr(self::NO, 'tristate'),
			self::UNKOWN => tr(self::UNKOWN, 'tristate')		
		);
	}
	
	/**
	 * Depending on $tristate returns of of the values $yes, $no, or $unkown
	 * 
	 * @param string $tristate Tristate value
	 * @param mixed $yes Value to return if $tristate is Tristate::YES
	 * @param mixed $no Value to return if $tristate is Tristate::NO
	 * @param mixed $unknown Value to return if $tristate is Tristate::UNKOWN
	 * @return mixed
	 */
	public static function resolve($tristate, $yes, $no, $unkown) {
		switch ($tristate) {
			case self::YES:
				return $yes;
			case self::NO:
				return $no;
			default:
				return $unkown;
		}
	} 
}