<?php
/**
 * User Access Control for Users
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class UsersAccessControl extends AccessControlBase {
	/**
	 * Constructor. Sets type on parent.
	 */
	public function __construct() {
		parent::__construct('users');
	}
	
	/**
	 * Overloadable. Check if action on object is allowed for given user
	 *
	 * User is always valid
	 * 
	 * @param string $action The action to perform (edit, delete, ....)  
	 * @param mixed $item Item to perform the action on (may be a DataObject, e.g.)
	 * @param DAOUsers $user A user, role, ACO, depending on user management chosen
	 * @return int One of Constants ALLOWED, NOT_ALLOWED and NOT_RESPONSIBLE
	 */
	protected function do_is_allowed_for_user($action, $item, $user, $params = false) {
		// we know that item is of type "users"
		$ret = self::NOT_ALLOWED;
		$is_admin = $user->has_role(array(USER_ROLE_ADMIN, USER_ROLE_SYSTEM)); 
		switch ($action) {
			case 'update':
				$ret = $this->to_result(($is_admin) || ($item->id == $user->id));
				break;
			case 'create':
			case 'status':
			case 'edit':
			case 'delete':
				$ret = $this->to_result($is_admin);
				break;
		}
		return $ret;		
	}
	
	/**
	 * Overloadable. Check if action on object is allowed for no user
	 * 
	 * @param string $action The action to perform (edit, delete, ....)  
	 * @param mixed $item Item to perform the action on (may be a DataObject, e.g.)
	 * @return int One of Constants ALLOWED, NOT_ALLOWED and NOT_RESPONSIBLE
	 */
	protected function do_is_allowed_for_anonymous($action, $item, $params = false) {
		return self::NOT_ALLOWED;
	}	
}
