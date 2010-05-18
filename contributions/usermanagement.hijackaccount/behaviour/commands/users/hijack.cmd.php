<?php
/**
 * Command to login as another user
 */
class HijackUsersCommand extends CommandComposite {
	protected $session_data;
	
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'hijack';
	}
	
	/**
	 * Returns a description of this command
	 */
	public function get_description() {
		$ret = tr(
			'Hijack Account',
			'hijack'
		);
		return $ret;
	}
	
	/**
	 * Does executing
	 * 
	 * @return Status
	 */
	protected function do_execute() {
		$ret = new Status();
		$this->session_data = array_merge(array(), $_SESSION);
		
		Load::commands('generics/massdelete', 'users/loginknown', 'generics/cookie.set');
		// Store session data
		$duration = GyroDate::ONE_DAY;
		$cur_user = Users::get_current_user();
		$saved_session_id = session::get_session_id();
		$params = array('id' => $saved_session_id, 'id_user' => $cur_user->id, 'data' => $_SESSION, 'expirationdate' => time() + $duration);
		$this->append(CommandsFactory::create_command('hijackaccountsavedsessions', 'create', $params));
		// Clear session, but keep current user as fallback
		Session::clear();
		Session::push('current_user', clone($cur_user));
		// Delete expired
		$this->append(new MassDeleteCommand('hijackaccountsavedsessions', new DBCondition('expirationdate', '<', time())));
		// Login as given user
		$user = $this->get_instance();
		$this->append(new LoginknownUsersCommand($user));
		// Notify USer
		if (Load::is_module_loaded('usermanagement.notifications')) {
			$notify = $this->create_notification_command($cur_user, $user);
			if ($notify->can_execute($user)) {
				$this->append($notify);
			}
		}
		// Set a Cookie
		$this->append(new CookieSetCommand(HijackAccount::COOKIE_NAME, $saved_session_id, 0));
		return $ret;
	}
	
	protected function create_notification_command($hijacker, $hijacked) {
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'hijackaccount/notification', false);
		$view->assign('hijacker', $hijacker);
		$cmd = CommandsFactory::create_command(
			$hijacked, 
			'notify', 
			array(
				'title' => tr('%name logged into your account', 'hijackaccount', array('%name' => $hijacker->name)),
				'message' => $view->render(),
				'source' => 'usermanagement.hijackaccount'
			)
		);
		return $cmd;		
	}

	/**
	 * Does undo
	 */
	protected function do_undo() {
		foreach($this->session_data as $key => $data) {
			Session::push($key, $data);
		}
		Session::restart();
	}	
}
