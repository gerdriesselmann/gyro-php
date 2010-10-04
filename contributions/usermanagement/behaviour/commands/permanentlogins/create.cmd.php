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
		$code = sha1(uniqid($user->creationdate . $user->password . $user->modificationdate, true));
		$validtime = Cast::int(Config::get_value(ConfigUsermanagement::PERMANENT_LOGIN_DURATION)) * GyroDate::ONE_DAY;
		$params = array(
			'code' => $code,
			'expirationdate' => time() + $validtime,
			'id_user' => $user->id
		);
		$this->append(new CreateCommand('permanentlogins', $params));
		
		// Set cookie
		$this->append(new CookieSetCommand(PermanentLogins::COOKIE_NAME, $code, $validtime));
		
		return $ret;
	}
}