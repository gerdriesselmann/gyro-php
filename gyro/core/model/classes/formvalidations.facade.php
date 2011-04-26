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
		
		$validations = new DAOFormvalidations();
		$validations->name = $name;
		$validations->token = $token;
		$validations->expirationdate = time() + Config::get_value(Config::FORMVALIDATION_EXPIRATION_TIME) * GyroDate::ONE_MINUTE; // 
		$validations->insert();
		
		return $token;
	}

	/**
	 * Create a token for form of given name
	 */
	public static function create_or_reuse_token($name, $token) {
		$already_validated = RuntimeCache::get(array('reusetokensvalidated', $name));
		if (!$already_validated) {
			self::remove_expired();
			
			$validations = new DAOFormvalidations();
			$validations->name = $name;
			$validations->token = $token;
			
			if ($validations->find(IDataObject::AUTOFETCH)) {
				if ($validations->expirationdate - 2 * GyroDate::ONE_MINUTE < time()) {
					$token = self::create_token($name);
				}
			}	
			else {
				$token = self::create_token($name);
			}
			RuntimeCache::set(array('reusetokensvalidated', $name), true);
		}
		return $token;
	}

	/**
	 * Validate a given token for form of given name
	 * 
	 * @return Boolean
	 */
	public static function validate_token($name, $token) {
		$validations = new DAOFormvalidations();
		
		$validations->name = $name;
		$validations->token = $token;
		
		$ret = false;
		if ($validations->find(IDataObject::AUTOFETCH)) {
			$ret = ($validations->expirationdate > time());
			$validations->delete();
		}
		
		return $ret;
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
