<?php
/**
 * Facade for tokens
 */
class Tokens {
	/**
	 * Create a token for given context
	 * 
	 * @param string $seed Optional extra seed for token
	 * @param int $duration_seconds Valid duration time in seconds. Defaults to 10 days
	 */
	public static function create_token($seed = false, $duration_seconds = 0) {
		self::clear();
		
		$dao = new DAOTokens();
		$token = '';
		do {
			$token = Common::create_token($seed);
			$dao->token = $token;
		} while ($dao->count() > 0);

		if ($duration_seconds == 0) { $duration_seconds = 10 * GyroDate::ONE_DAY; }

		$cmd = CommandsFactory::create_command('tokens', 'create', array(
			'token' => $token,
			'expirationdate' => time() + $duration_seconds
		));
		$cmd->execute();
		
		return $token;
	}
	
	private static function clear() {
		$dao = new DAOTokens();
		$dao->add_where('expirationdate', '<', time());
		$dao->delete(DataObjectBase::WHERE_ONLY);
	}

	public static function exists($token) {
		$dao = new DAOTokens();
		$dao->token = $token;
		$dao->add_where('expirationdate', '>', time());

		return $dao->count() > 0;
	}

	public static function remove($token) {
		$dao = new DAOTokens();
		$dao->token = $token;

		$cmd = CommandsFactory::create_command($dao, 'delete', array());
		return $cmd->execute();
	}

}