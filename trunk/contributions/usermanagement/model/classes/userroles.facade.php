<?php
/**
 * Created on 14.11.2006
 *
 * @author Gerd Riesselmann
 */

/**
 * Usermanagement Business Logic
 */
class UserRoles {
	/**
	 * Returns user role by name
	 *
	 * @param string $name
	 * @return DAOUserRoles
	 */
	public static function get_by_name($name) {
		return DB::get_item('userroles', 'name', $name);	 
	}
	
	/**
	 * Return all roles for given user
	 *
	 * @param int $user_id
	 * @return array
	 */
	public static function get_for_user($user_id) {
		$dao = new DAOUserroles();
		$link = new DAOUsers2userroles();
		$link->id_user = $user_id;
		
		$dao->join($link);
		return $dao->find_array();
	}
}
