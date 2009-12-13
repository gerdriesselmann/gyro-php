<?php
class HijackaccountController extends ControllerBase {
	/**
	 * Return array of Route instances which are handled by this controller
	 * 
	 * @return array Array of Routes 
 	 */
	public function get_routes() {
		return array(
			new ParameterizedRoute('process_commands/users/{id:ui>}/hijack', $this, 'users_hijack', new UsersAccessControl())
		);
	}	
	
	/**
	 * Hijack given account
	 */
	public function action_users_hijack(PageData $page_data, $id) {
		$user = Users::get($id);
		if ($user === false) {
			return self::NOT_FOUND;
		}
		
		$cmd = CommandsFactory::create_command($user, 'hijack', false);
		$ok = $cmd->can_execute(false);
		if (!$ok) {
			return self::ACCESS_DENIED;
		}
		
		$err = $cmd->execute();
		if ($err->is_ok()) {
			$err = new Message(tr(
				'You now have been logged in as %name. If you log out, you will return to you old session', 
				'hijackaccount', 
				array('%name' => $user->get_title())
			));
		}
		History::go_to(0, $err);
	}
}