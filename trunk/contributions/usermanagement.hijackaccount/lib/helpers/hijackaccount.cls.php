<?php
/**
 * A class wrapping some stuff regarding hikacking
 */
class HijackAccount {
	const COOKIE_NAME = 'C128A';
	
	/**
	 * Returns true, if the current account is hijacked
	 * 
	 * @return bool
	 */
	public static function is_hijacked() {
		return (Cookie::get_cookie_value(HijackAccount::COOKIE_NAME) !== false);		
	}
}