<?php
/**
 * Defauklt Access Control for hijacking accounts
 */
class HijackaccountAccessControl extends AccessControlBase {
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
	 * @param DAOHtmlpage $item Item to perform the action on (may be a DataObject, e.g.)
	 * @param DAOUsers $user A user, role, ACO, depending on user management chosen
	 * @return int One of Constants ALLOWED, NOT_ALLOWED and NOT_RESPONSIBLE
	 */
	protected function do_is_allowed_for_user($action, $item, $user, $params = false) {
		// we know that item is of type "users"
		$ret = self::NOT_RESPONSIBLE;
		switch ($action) {
			case 'hijack':
				// Admins are allowed to hijack
				$ret = $this->to_result($user->has_role(USER_ROLE_ADMIN) && $item->is_active());
				break;
		}
		return $ret;
	}
}
