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
		if ($ret->is_error()) {
			return $ret;
		}
		
		$this->do_prepare_user_dao($user);
		// Try to load user
		if ($user->find(IDataObject::AUTOFETCH)) {
			$ret->merge($this->check_password_hash($user, $params));			
			if ($ret->is_ok()) {
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
		}
		else {
			$ret->append($this->do_get_default_error_message());
		}
		return $ret;
	}
	
	/**
	 * Validate password hash
	 * 
	 * @since 0.5.1
	 */
	protected function check_password_hash(DAOUsers $user, $params) {
		$ret = new Status();
		$password = $this->params_extract_password($params);
		$algo = Users::create_hash_algorithm($user->hash_type);
		
		if (!$algo->check($password, $user->password)) {
			$ret->append($this->do_get_default_error_message()); 
		}
		else if ($user->hash_type != Config::get_value(ConfigUsermanagement::USER_HASH_TYPE)) {
			$user->hash_type = Config::get_value(ConfigUsermanagement::USER_HASH_TYPE);
			$algo = Users::create_hash_algorithm($user->hash_type);
			$user->password = $algo->hash($password);
			$this->append(CommandsFactory::create_command($user, 'update', array()));
		}
		return $ret;
	}
	
	/**
	 * Extracts name from param array
	 * 
	 * @return string
	 */
	protected function params_extract_name($params) {
		return Cast::string(Arr::get_item($params, 'name', ''));
	} 
	
	/**
	 * Extracts password from param array
	 * 
	 * @return string
	 */
	protected function params_extract_password($params) {
		return Cast::string(Arr::get_item($params, 'password', ''));
	} 
	
	/**
	 * Find user from parameters given
	 *
	 * @param array $params
	 * @param Status $err
	 * @return DAOUsers
	 */
	protected function do_create_user_dao($params, $err) {
		$name = $this->params_extract_name($params);
		$pwd = $this->params_extract_password($params);
		
		if ($name == '') {
			$err->append(tr('Please provide a user name for login', 'users'));
		}
		if ($pwd == '') {
			$err->append(tr('Please provide a password for login', 'users'));
		}
		
		$user = new DAOUsers();
		$user->name = $name;
		
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
