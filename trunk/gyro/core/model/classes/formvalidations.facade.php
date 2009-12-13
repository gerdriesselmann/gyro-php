<?php
define('FORMVALIDATION_EXPIRATION_TIME', 60); // Time in minutes form tokens are valid   

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
		
		$token = substr(uniqid(dechex(mt_rand()), true), 0, 35);
		
		$validations = new DAOFormvalidations();
		$validations->name = $name;
		$validations->token = $token;
		$validations->expirationdate = time() + FORMVALIDATION_EXPIRATION_TIME * GyroDate::ONE_MINUTE; // 
		$validations->insert();
		
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
