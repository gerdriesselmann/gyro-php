<?php
/**
 * Login known user command to be overloaded
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
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
			Session::push('current_user_id', $user->id);
			Session::pull('current_user');
			AccessControl::set_current_aro($user);

			if (Config::has_feature(ConfigUsermanagement::TRACE_LAST_LOGIN)) {
				$hijack_enabled = Load::is_module_loaded('usermanagement.hijackaccount');
				$update_last_login = $hijack_enabled
					? !HijackAccount::is_hijacked()
					: true;

				if ($update_last_login) {
					$cmd = CommandsFactory::create_command($user, 'update', array('lastlogindate' => time()));
					$cmd->execute();
				}
			}
			
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