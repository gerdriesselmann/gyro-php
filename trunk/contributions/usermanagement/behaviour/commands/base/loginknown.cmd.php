<?php
/**
 * Created on 11.04.2007
 *
 * @author Gerd Riesselmann
 */

/**
 * Login known user
 */
class LoginknownUsersBaseCommand extends CommandChain {
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'login';
	}
	
	public function do_can_execute($user) {
		return empty($user); // Not logged in only
	}
	
	public function do_execute() {
		$ret = new Status();
		$user = $this->get_instance();
		if ($user && $user->is_active()) {
			Session::push('current_user', clone($user));
			AccessControl::set_current_aro($user);
			
			Load::commands('generics/triggerevent');
			$this->append(new TriggerEventCommand('login', $user));
		}
		else {
			$ret->append(tr('You are not allowed to login', 'users'));			
		}		
		return $ret;
	}
}
?>