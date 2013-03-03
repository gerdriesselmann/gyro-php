<?php
/**
 * A fake user class to use as system user
 * 
 * There is no SYSTEM role in DB, since SYSTEM is not a real user.
 * Therefore this class is used
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class DAOSystemUsers extends DAOUsers {
	public function __construct() {
		parent::__construct();
		$this->email = Config::get_value(Config::MAIL_ADMIN);
		$this->tos_version = Config::get_value(ConfigUsermanagement::TOS_VERSION);
		$this->emailstatus = Users::EMAIL_STATUS_CONFIRMED;
	}
	
	/**
	 * Returns status
	 * 
	 * @return string
	 */
	public function get_status() {
		return Users::STATUS_ACTIVE;
	}
	
	/**
	 * Returns true, if status is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return true;
	}

	/**
	 * Returns true, if status is unconfirmed
	 *
	 * @return bool
	 */
	public function is_unconfirmed() {
		return false;
	}
	
	/**
	 * Returns true, if status is deleted
	 *
	 * @return bool
	 */
	public function is_deleted() {
		return false;
	}
	
	/**
	 * Returns true, if status is disabled
	 *
	 * @return bool
	 */
	public function is_disabled() {
		return false;
	}

	// **************************************
	// Access Check Functions
	// **************************************
	
	
	/**
	 * Returns array of role names
	 * 
	 * @return array
	 */
	public function get_role_names() {
		return array(USER_ROLE_SYSTEM => USER_ROLE_SYSTEM);		
	}
}
