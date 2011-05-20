<?php
/**
 * Update a user
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class UpdateUsersBaseCommand extends CommandChain {
	/**
	 * Execute this command
	 */
	protected function do_execute() {
		$ret = new Status();
		
		$params = $this->get_params();
		$user = $this->get_instance();
		
		// Validate
		$cmd_validate = CommandsFactory::create_command($user, 'validate', $params);
		$ret->merge($cmd_validate->execute());

		if ($ret->is_ok()) {
			// Check if to send an email confirmation mail
			$this->check_for_email_confirmation($user, $params);
			
			// HAsh password
			$this->hash_password($user, $params);
			$this->check_for_pwd_confirmation($user, $params);
	
			// Chain next commands
			Load::commands('generics/update');
			$this->append(new UpdateCommand($user, $params));
	
			$ret->merge($this->link_roles($user, $params));
		}
		return $ret;
	}	

	/**
	 * Hash the password
	 * 
	 * @since 0.5.1
	 */
	protected function hash_password($user, &$params) {
		$pwd = Arr::get_item($params, 'password', '');
		if (!empty($pwd)) {
			$params['password'] = Users::create_hash($pwd, $user->hash_type);
		}
		else {
			unset($params['password']);
		}
	}	
	
	/**
	 * Check if an email confirmation must be created (that is user changed email)
	 * 
	 * @since 0.5.1
	 */
	protected function check_for_email_confirmation($user, &$params) {
		$email = Arr::get_item($params, 'email', $user->email);
		if (!Users::current_has_role(USER_ROLE_ADMIN)) {
			// None-admins cannot change mail directly!
			if ($user->email !== $email) {
				$this->send_email_notification($user, $email);
			} 
			unset($params['email']);
		}
	}
	
	/**
	 * Check if an password confirmation must be created (that is user changed password)
	 * 
	 * @since 0.6
	 */
	protected function check_for_pwd_confirmation($user, &$params) {
		$pwd = Arr::get_item($params, 'password', $user->password);
		if (!Users::current_has_role(USER_ROLE_ADMIN)) {
			// None-admins cannot change mail directly!
			if ($user->password !== $pwd) {
				$this->send_pwd_notification($user, $pwd);
			} 
			unset($params['password']);
		}
	}	

	/**
	 * Create an email change notification
	 * 
	 * @since 0.5.1
	 */
	protected function send_email_notification($user, $email) {
		// Indirectly change mail, if desired
		$params = array(
			'id_item' => $user->id,
			'action' => 'changeemail',
			'data' => $email
		);
		$this->append(CommandsFactory::create_command('confirmations', 'create', $params));
	}

	/**
	 * Create a password change notification
	 * 
	 * @since 0.6
	 */
	protected function send_pwd_notification($user, $pwd) {
		// Indirectly change mail, if desired
		$params = array(
			'id_item' => $user->id,
			'action' => 'changepassword',
			'data' => $pwd
		);
		$this->append(CommandsFactory::create_command('confirmations', 'create', $params));
	}
	
	/**
	 * Link Roles
	 *
	 * @param DAOUsers $user
	 * @param array $params
	 * @return Status
	 */
	protected function link_roles($user, $params) {
		$ret = new Status();
		$roles = Arr::get_item($params, 'roles', false);
		if ($roles === false) {
			return $ret;
		}
		
		if (empty($roles)) {
			$ret->append(tr('You must assign at least one role', 'users'));
			return $ret;
		}
		
		// delete old
		$dao = new DAOUsers2userroles();
		$dao->add_where('id_user', '=', $user->id);
		$sql = $dao->create_delete_query(DAOUsers2userroles::WHERE_ONLY)->get_sql();
		Load::commands('generics/execute.sql');
		$this->append(new ExecuteSqlCommand($sql));
		
		// Append link creation commands
		foreach($roles as $role) {
			$params_link = array(
				'id_user' => $user->id,
				'id_role' => $role 
			);
			$this->append(CommandsFactory::create_command('users2userroles', 'create', $params_link));
		}
		return $ret;
	}		
		
} 
