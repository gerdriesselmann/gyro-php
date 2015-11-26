<?php
/**
 * Create a permanent login
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class CreatePermanentloginsCommand extends CommandComposite {
	/**
	 * Does executing
	 */
	protected function do_execute() {
		$ret = new Status();
		// Delete expired
		Load::commands('generics/massdelete', 'generics/create', 'generics/cookie.set');
		$this->append(new MassDeleteCommand('permanentlogins', new DBCondition('expirationdate', '<', time())));
		
		// Create new entry
		$user = $this->get_params();
		$salt = $user->creationdate . $user->password . $user->modificationdate;
		$code = Common::create_token($salt);
		$validtime = Cast::int(Config::get_value(ConfigUsermanagement::PERMANENT_LOGIN_DURATION)) * GyroDate::ONE_DAY;
		$params = array(
			'code' => $code,
			'expirationdate' => time() + $validtime,
			'id_user' => $user->id
		);
		$this->append(new CreateCommand('permanentlogins', $params));
		
		// Set cookie. Get secure settings from session cookie settings
		$this->append(new CookieSetCommand(
				PermanentLogins::COOKIE_NAME, $code, $validtime,
				Session::cookies_are_http_only(), Session::cookies_are_secure()
		));
		
		return $ret;
	}
}