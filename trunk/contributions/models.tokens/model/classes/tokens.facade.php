<?php
/**
 * Facade for tokens
 */
class Tokens {
	/**
	 * Create a token for given context
	 * 
	 * @param string $context
	 * @param string $seed Optional extra seed for token
	 */
	public static function create_token($seed = false) {
		self::clear();
		
		$dao = new DAOTokens();
		$token = '';
		do {
			$token = Common::create_token($seed);
			$dao->token = $token;
		} while ($dao->count() > 0);
		
		$cmd = CommandsFactory::create_command('tokens', 'create', array('token' => $token));
		$cmd->execute();
		
		return $token;
	}
	
	private static function clear() {
		$dao = new DAOTokens();
		$dao->add_where('creationdate', '<', time() - 10 * GyroDate::ONE_DAY);
		$dao->delete(DataObjectBase::WHERE_ONLY);
	}
}