<?php
/**
 * An tristate enum field
 * 
 * Field in DB should be defined as ENUM('FALSE','TRUE','UNKNOWN').
 * 
 * @since 0.5.1
 * 
 * @author Gerd Riesselmann
 * @ingroup Tristate
 */
class DBFieldTristate extends DBFieldEnum {
	public function __construct($name, $default_value = Tristate::UNKOWN, $policy = self::NOT_NULL) {
		parent::__construct($name, array_keys(Tristate::get_states()), $default_value, $policy);
	}
}