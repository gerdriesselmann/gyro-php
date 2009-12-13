<?php
/**
 * Created on 13.03.2007
 *
 * @author Gerd Riesselmann
 */
 
class UpdateUsersCommand extends CommandChain {
	/**
	 * Execute this command
	 */
	protected function do_execute() {
		$ret = new Status();
		
		$params = $this->get_params();
		$user = $this->get_instance();
		
		$email = Arr::get_item($params, 'email', $user->email);
		$create_email_confirmation = false;
		if (Users::current_has_role(USER_ROLE_ADMIN) == false) {
			// None-admins cannot change mail directly!
			$create_email_confirmation = ($user->email !== $email); 
			unset($params['email']);
		}
		
		$pwd = Arr::get_item($params, 'password', '');
		if (!empty($pwd)) {
			$params['password'] = md5($pwd);
		}
		else {
			unset($params['password']);
		}

		// Chain next commands
		Load::commands('generics/update');
		$this->append(new UpdateCommand($user, $params));

		$ret->merge($this->link_roles($user, $params));
		
		// Indirectly change mail, if desired
		if ($create_email_confirmation) {
			$params = array(
				'id_item' => $user->id,
				'action' => 'changeemail',
				'data' => $email
			);
			$this->append(CommandsFactory::create_command('confirmations', 'create', $params));
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
