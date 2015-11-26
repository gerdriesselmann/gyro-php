<?php
/**
 * Helper functions around permant logins
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class PermanentLogins {
	const COOKIE_NAME = 'C128';
	
	/**
	 * Returns current, if any
	 * 
	 * @return DAOPermanentlogins
	 */
	public static function get_current() {
		$ret = false;
		$code = Cookie::get_cookie_value(self::COOKIE_NAME);
		if ($code) {
			$tmp = DB::get_item('permanentlogins', 'code', $code);
			if ($tmp && $tmp->expirationdate > time()) {
				$ret = $tmp;
			}
		}
		return $ret; 
	}
	
	/**
	 * Enable permanent login for given user
	 */ 
	public static function enable_permanent_login($user) {
		$cmd = CommandsFactory::create_command('permanentlogins', 'create', $user);
		$cmd->execute();
	}
	
	/**
	 * Ends permanent login for current user
	 */ 
	public static function end_permanent_login() {
		$cmd = CommandsFactory::create_command('permanentlogins', 'end', false);
		$cmd->execute();		
	}
}