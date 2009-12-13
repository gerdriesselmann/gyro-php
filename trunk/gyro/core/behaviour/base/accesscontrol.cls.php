<?php
/**
 * Facade for access control checking
 * 
 * @ingroup Behaviour
 */
class AccessControl {
	/**
	 * Implementation to delegate view creation to
	 *
	 * @var IAccessControl
	 */
	private static $implementation = null;
	/**
	 * Current "Access Request Object", most probabaly a user 
	 *
	 * @var mixed
	 */
	private static $current_aro = null;
	
	/**
	 * Change Implementation
	 *
	 * @param IAccessControl $implementation
	 * @param bool $keep_old If set to TRUE, new implementation will not get replaced, if there already is any 
	 */
	public static function set_implementation(IAccessControl $implementation, $keep_old = false) {
		// keep old, if told so 
		if (!empty(self::$implementation)) {
			if ($keep_old) {
			 	return;
			}
			$implementation->set_old_implementation(self::$implementation); 
		}
		self::$implementation = $implementation;
	}

	/**
	 * Set current access request object (e.g. current user)
	 *
	 * @param mixed $aro
	 */	
	public static function set_current_aro($aro) {
		self::$current_aro = $aro;
	}
	
	/**
	 * Returns current Access Request Object
	 *
	 * @return mixed
	 */
	public static function get_current_aro() {
		return self::$current_aro;
	}
	
	/**
	 * Check if action on object is allowed for user
	 * 
	 * Returns false if no implementation is set!
	 *
	 * @param string $action The action to perform  
	 * @param mixed $item Item (e.g. table) to perform action on 
	 * @param mixed $user An ARO. If ommited, self::get_current_aro() is substituted
	 * @return bool
	 */
	public static function is_allowed($action, $item, $params = false, $user = false) {
		if (empty($user)) {
			$user = self::get_current_aro();
		}
		if (self::$implementation) {
			return self::$implementation->is_allowed($action, $item, $user, $params);
		}
		return false;
	}	
	
	/**
	 * Load all accesscontrols located in /behaviour/accesscontrol/
	 */
	public static function load() {
		$dirs = Load::get_base_directories(Load::ORDER_DECORATORS);
		foreach($dirs as $dir) {
			foreach (gyro_glob($dir . 'behaviour/accesscontrol/*.access.php') as $inc) {
				include_once($inc);
				// Detect access control name from filename
				// ".", "-" and "_" get translated to camel case:
				// users.access.php => UsersAccessControl
				$clsname = basename($inc, '.access.php');
				$clsname = strtr($clsname, '.-_', '   ');
				$clsname = ucwords($clsname);
				$clsname = str_replace(' ', '', $clsname) . 'AccessControl';
				if (class_exists($clsname)) {
					self::set_implementation(new $clsname());
				}
				else {
					throw new Exception(tr('AccessControl %c not found', 'core', array('%c' => $clsname))); 			  	
				}
			}
		}
	}
}
