<?php
/**
 * Stores form tokens
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class FormValidations {
	/**
	 * Create a token for form of given name
	 */
	public static function create_token($name) {
		self::remove_expired();
		
		$token = Common::create_token();
		$validations = self::create_token_instance($name, $token);
		$validations->expirationdate = time() + Config::get_value(Config::FORMVALIDATION_EXPIRATION_TIME) * GyroDate::ONE_MINUTE; // 
		$validations->insert();
		
		return $token;
	}

	/**
	 * Create a token for form of given name or reuse if it was created for this request
	 */
	public static function create_or_reuse_token($name) {
		$token = RuntimeCache::get(array('reusedtokens', $name));
		if (!$token) {
			$token = self::create_token($name);
			RuntimeCache::set(array('reusedtokens', $name), $token);
		}
		return $token;
	}

	/**
	 * Create a token for form of given name or reuse if it was created for this request
	 */
	public static function create_or_reuse_token_across_requests($name) {
		$ret = '';

		$validations = new DAOFormvalidations();
		$validations->name = $name;
		$validations->sessionid = Session::get_session_id();
		if ($validations->find(IDataObject::AUTOFETCH)) {
			if ($validations->is_valid_for_at_least(10)) {
				$ret = $validations->token;
			}
		}
		if ($ret) {
			return $ret;
		} else {
			return self::create_token($name);
		}
	}

	/**
	 * Validate a given token for form of given name
	 * 
	 * @return Boolean
	 */
	public static function validate_token($name, $token) {
		$ret = false;

		$validations = self::create_token_instance($name, $token);
		if ($validations->find(IDataObject::AUTOFETCH)) {
			$ret = ($validations->expirationdate > time());
			$validations->delete();
		}
		
		return $ret;
	}

	/**
	 * @static
	 * @param $name
	 * @param $token
	 * @return DAOFormvalidations
	 */
	private static function create_token_instance($name, $token) {
		$validations = new DAOFormvalidations();

		$validations->name = $name;
		$validations->token = $token;
		$validations->sessionid = Session::get_session_id();

		return $validations;
	}


	/**
	 * Removes expired cache entries
	 */
	public static function remove_expired() {
		$dao = new DAOFormvalidations();
		$dao->add_where('expirationdate', '<', DBFieldDateTime::NOW);
		$dao->delete(DAOFormvalidations::WHERE_ONLY);
	}
}
