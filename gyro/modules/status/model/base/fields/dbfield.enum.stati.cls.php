<?php
/**
 * DBField for default stati
 * 
 * @author Gerd Riesselmann
 * @ingroup Status
*/
class DBFieldEnumStati extends DBFieldEnum {
	/**
	 * Constructor
	 * 
	 * @param string $name Name
	 * @param string $default_value Default value
	 * @param int $policy
	 */
	public function __construct($name = 'status', $default_value = Stati::UNCONFIRMED, $policy = self::NOT_NULL) {
		parent::__construct($name, array_keys(Stati::get_stati()), $default_value, $policy);
	}
	
}