<?php
/**
 * Create a user command to be overloaded
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class CreateUsersBaseCommand extends CommandChain {		
	/**
	 * Execute this command
	 */
	protected function do_execute() {
		$ret = new Status();
		
		$params = $this->preprocess_params($this->get_params());
		
		// Validate
		$cmd_validate = CommandsFactory::create_command('users', 'validate', $params);
		$ret->merge($cmd_validate->execute()); 
		
		// Insert
		if ($ret->is_ok()) {
			Load::commands('generics/create');
			$cmd = new CreateCommand('users', $params);
			$ret->merge($cmd->execute());
			$this->set_result($cmd->get_result());
		}

		// Link Role
		if ($ret->is_ok()) {
			$user = $this->get_result();
			$ret->merge($this->link_roles($user, $params));
		}
		// Allow overloads to do stuff 
		if ($ret->is_ok()) {
			$user = $this->get_result();
			$ret->merge($this->postprocess($user, $params));
		}
		
		return $ret;
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
		
		$roles = Arr::force(Arr::get_item($params, 'roles', array()));
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

	/**
	 * Postprocess 
	 *
	 * @param DAOUsers $user
	 * @param array $params
	 * @return Status
	 */
	protected function postprocess($user, $params) {
		return new Status();
	}
		
	/**
	 * Preprocess param array
	 *
	 * @param array $params
	 * @return array
	 */
	protected function preprocess_params($params) {
		// Encrypt password
		$pwd = Arr::get_item($params, 'password', '');
		if (!empty($pwd)) {
			$params['password'] = md5($pwd);
		}
		else {
			unset($params['password']);
		}
		
		// Check roles
		$roles = Arr::force(Arr::get_item($params, 'roles', array()));
		if (count($roles) == 0) {
			// Assign at least default role
			$role = UserRoles::get_by_name(USER_DEFAULT_ROLE);
			if ($role) {
				$roles[] = $role->id;
				$params['roles'] = $roles;
			}	
		}
		
		return $params;
	}
} 