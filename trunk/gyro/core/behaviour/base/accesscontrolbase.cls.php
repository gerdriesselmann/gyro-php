<?php
/**
 * Base class for Access Control Cheching
 * 
 * @ingroup Behaviour
 */
class AccessControlBase implements IAccessControl {
	/**
	 * Old implementation to delegate to  
	 *
	 * @var IAccessControl
	 */
	private $delegate = null;
	/**
	 * Array of item types, this implementation is responsible
	 *
	 * @var array
	 */
	private $types = null;
	
	/**
	 * If returned by do_is_allowed() functions, will set return value of is_allowed() to false
	 */
	const NOT_ALLOWED = 0;
	/**
	 * If returned by do_is_allowed() functions, will set return value of is_allowed() to true 
	 */
	const ALLOWED = 1;
	/**
	 * If returned by do_is_allowed() functions, will force delegate to be called (if any) 
	 */
	const NOT_RESPONSIBLE = -1;
	
	/**
	 * Pass types (=model names), the implementation is responsible of 
	 *
	 * @param string|array $item_types
	 */
	public function __construct($item_types = false) {
		if (!empty($item_types)) {
			$this->types = Arr::force($item_types, false);
		}
	}
	
	/**
	 * Check if action on object is allowed for user
	 *
	 * @param string $action The action to perform (edit, delete, ....)  
	 * @param mixed $item Item to perform the action on (may be a DataObject, e.g.)
	 * @param mixed $user A user, role, ACO, depending on user management chosen
	 * @return bool 
	 */
	public function is_allowed($action, $item, $user, $params = false) {
		$ret = false;
		$result = $this->do_is_allowed($action, $item, $user, $params);
		if ($result === true) {
			$result = self::ALLOWED;
		}
		switch ($result) {
			case self::NOT_RESPONSIBLE:
				if ($this->delegate) {
					$ret = $this->delegate->is_allowed($action, $item, $user, $params);
				}
				break; 
			default:
				$ret = ($result) ? true : false;
				break;			
		}
		return $ret;
	}
	
	/**
	 * Overloadable. Check if action on object is allowed for user
	 *
	 * @param string $action The action to perform (edit, delete, ....)  
	 * @param mixed $item Item to perform the action on (may be a DataObject, e.g.)
	 * @param mixed $user A user, role, ACO, depending on user management chosen
	 * @return int One of Constants ALLOWED, NOT_ALLOWED and NOT_RESPONSIBLE
	 */
	protected function do_is_allowed($action, $item, $user, $params = false) {
		// Check type based responsibility
		if (!empty($this->types)) {
			$resposible = false;
			$item_type = $this->get_item_type($item);
			foreach($this->types as $type) {	
				if ($type === $item_type) {
					$resposible = true;
					break;
				}
			}
			if (!$resposible) {
				return self::NOT_RESPONSIBLE;
			}
		}

		// We are responsible
		if (!empty($user)) {
			return $this->do_is_allowed_for_user($action, $item, $user, $params);
		}
		else {
			return $this->do_is_allowed_for_anonymous($action, $item, $params);
		}
	}

	/**
	 * Overloadable. Check if action on object is allowed for given user
	 *
	 * User is always valid
	 * 
	 * @param string $action The action to perform (edit, delete, ....)  
	 * @param mixed $item Item to perform the action on (may be a DataObject, e.g.)
	 * @param mixed $user A user, role, ACO, depending on user management chosen
	 * @return int One of Constants ALLOWED, NOT_ALLOWED and NOT_RESPONSIBLE
	 */
	protected function do_is_allowed_for_user($action, $item, $user, $params = false) {
		return self::NOT_RESPONSIBLE;
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
		return self::NOT_RESPONSIBLE;
	}
	
	/**
	 * Convert Bool to tri-state
	 *
	 * @param bool $bool
	 * @return int
	 */
	protected function to_result($bool) {
		return ($bool) ? self::ALLOWED : self::NOT_ALLOWED;
	}
	
	/**
	 * Returns type of item
	 *
	 * @param mixed $item
	 * @return string
	 */
	protected function get_item_type($item) {
		$ret = $item;
		if ($item instanceof IDBTable) {
			$ret = $item->get_table_name();
		}
		else if (is_object($item)) {
			$ret = get_class($item); 
		}
		else if (is_null($item)) {
			$ret = '';
		}
		return $ret;		
	}
	
	/**
	 * Set old implementation. Requests not handled should be delegated to this
	 *
	 * @param IAccessControl $implementation
	 */
	public function set_old_implementation(IAccessControl $implementation) {
		$this->delegate = $implementation;
	}
}
