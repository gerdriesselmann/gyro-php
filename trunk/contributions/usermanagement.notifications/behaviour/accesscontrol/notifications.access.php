<?php
class NotificationsAccessControl extends AccessControlBase {
	/**
	 * Constructor. Sets type on parent.
	 */
	public function __construct() {
		parent::__construct(array('notifications', 'users'));
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
		// we know that item is of type "notifications" or "users"
		$ret = self::NOT_RESPONSIBLE;
		if ($item instanceof DAONotifications) {
			$ret = self::NOT_ALLOWED;
			switch ($action) {
				case 'status':
					$ret = ($item->id_user == $user->id); 
					break;
			}
		} else if ($item instanceof DAOUsers) {
			switch ($action) {
				case 'notifyall':
					$ret = $this->to_result($user->has_role(USER_ROLE_ADMIN)); 
					break;
				case 'notify':
					$ret = $this->to_result($item->is_active());
					break;
			}			
		}
		return $ret;		
	}
	
	/**
	 * Overloadable. Check if action on object is allowed for no user
	 *
	 * User is always valid
	 * 
	 * @param string $action The action to perform (edit, delete, ....)  
	 * @param mixed $item Item to perform the action on (may be a DataObject, e.g.)
	 * @return int One of Constants ALLOWED, NOT_ALLOWED and NOT_RESPONSIBLE
	 */
	protected function do_is_allowed_for_anonymous($action, $item, $params = false) {
		return self::NOT_ALLOWED;
	}		
}