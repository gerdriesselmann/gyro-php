<?php
/**
 * Validate a user before create, update etc 
 * 
 * @since 0.5.1
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class ValidateUsersBaseCommand extends CommandComposite {		
	/**
	 * Execute this command
	 */
	protected function do_execute() {
		$ret = new Status();
		
		$params = $this->get_params();
		
		$user = $this->get_instance();
		$user_is_instance = ($user instanceof DAOUsers);
		$email = Arr::get_item($params, 'email', $user_is_instance ? $user->email : '');
		$name = Arr::get_item($params, 'name', $user_is_instance ? $user->name : '');
		
		// Validate. Assume that current name/email is always OK
		if (!$user_is_instance || $user->email != $email) {
			$ret->merge($this->validate_email($email));
		}
		if (!$user_is_instance || $user->name != $name) {
			$ret->merge($this->validate_username($name));
		}
		return $ret;
	}
	
	/**
	 * Validate the email address given. 
	 * 
	 * Checks for valid mail address and uniqueness only
	 * 
	 * You may however overload this to do any check you like  
	 *
	 * @param string $email
	 * @return Status
	 */
	protected function validate_email($email) {
		$ret = new Status();
		if (!Validation::is_email($email)) {
			$ret->append(tr('Please enter a valid email address'));
			return $ret;
		}
				
		$user = new DAOUsers();
		$user->add_where('status', '!=', Users::STATUS_DELETED);
		$user->email = $email;
		$c = $user->count();
		if ($c > 0) {
			$ret->append(tr('An user with this email address already exists', 'users'));
		}
		return $ret;
	}

	/**
	 * Validate the user name 
	 * 
	 * Checks for uniqueness only
	 * 
	 * You may however overload this to exclude some chars or do any check you like  
	 *
	 * @param string $name
	 * @return Status
	 */
	protected function validate_username($name) {
		$ret = new Status();
		$user = new DAOUsers();
		$user->add_where('status', '!=', Users::STATUS_DELETED);
		$user->name = $name;
		$c = $user->count();
		if ($c > 0) {
			$ret->append(tr('An user with this username already exists', 'users'));
		}
		return $ret;
	}
	
} 
