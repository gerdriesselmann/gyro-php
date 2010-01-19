<?php
/**
 * Login command to be overloaded
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class LoginUsersBaseCommand extends CommandChain {
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
		$params = $this->get_params();
		
		$user = $this->do_create_user_dao($params, $ret);
		$this->do_prepare_user_dao($user);
		
		if ($ret->is_ok()) {
			// Try to load user
			if ($user->find(IDataObject::AUTOFETCH)) {
				switch ($user->status) {
					case Users::STATUS_UNCONFIRMED;
						$ret->append(tr('Your account has not yet been activated', 'users')); 
						break;
					case Users::STATUS_ACTIVE:
						// We can login this user
						Session::restart();
						$this->append(CommandsFactory::create_command($user, 'loginknown', $params));
						break;
					default:
						$ret->append($this->do_get_default_error_message());
						break;
				}
			}
			else {
				$ret->append($this->do_get_default_error_message());
			}
		}
		return $ret;
	}
	
	/**
	 * Find user from parameters given
	 *
	 * @param array $params
	 * @param Status $err
	 * @return DAOUsers
	 */
	protected function do_create_user_dao($params, $err) {
		$name = Cast::string(Arr::get_item($params, 'name', ''));
		$pwd = Cast::string(Arr::get_item($params, 'password', ''));
		
		if ($name == '') {
			$err->append(tr('Please provide a user name for login', 'users'));
		}
		if ($pwd == '') {
			$err->append(tr('Please provide a password for login', 'users'));
		}
		
		$user = new DAOUsers();
		$user->name = $name;
		$user->password = md5($pwd);
		
		return $user;
	}
	
	/**
	 * Prepares dao object 
	 *
	 * @param DAOUsers $user
	 */
	protected function do_prepare_user_dao(DAOUsers $user) {
		$user->add_where('status', DBWhere::OP_IN, array(Users::STATUS_ACTIVE, Users::STATUS_UNCONFIRMED));		
	}
	
	/**
	 * Returns default error message
	 */
	protected function do_get_default_error_message() {
		return tr('Username or password are wrong. Please try again.', 'users');		
	}
}
