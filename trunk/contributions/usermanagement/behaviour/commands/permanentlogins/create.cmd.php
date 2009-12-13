<?php
/**
 * Create a permanent login
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
		$code = uniqid('', true) . '-' . mt_rand(0, 999999);
		$validtime = 14 * GyroDate::ONE_DAY;
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