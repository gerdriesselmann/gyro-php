<?php
/**
 * Interface for access control implementations
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IAccessControl {
	/**
	 * Check if action on object is allowed for user
	 *
	 * @param string $action The action to perform (edit, delete, ....)  
	 * @param mixed $item Item to perform the action on (may be a DataObject, e.g.)
	 * @param mixed $user A user, role, ACO, depending on user management chosen
	 * @return bool 
	 */
	public function is_allowed($action, $item, $user, $params = false);

	/**
	 * Set old implementation. Requests not handled should be delegated to this
	 *
	 * @param IAccessControl $implementation
	 */
	public function set_old_implementation(IAccessControl $implementation);
}
