<?php
Load::models('permanentlogins');

/**
 * Usermanagement Business Logic
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class Users {
	const STATUS_ACTIVE = 'ACTIVE';
	const STATUS_DELETED = 'DELETED';
	const STATUS_DISABLED = 'DISABLED';
	const STATUS_UNCONFIRMED = 'UNCONFIRMED';
	
	/**
	 * Returns the user logged in or null, if no user is logged in
	 * 
	 * @return DAOUsers
	 */
	public static function get_current_user() {
		return AccessControl::get_current_aro();
	}
	
	/**
	 * Returns true, if an user is logged in, else false 
	 * 
	 * @return bool
	 */
	public static function is_logged_in() {
		$user = self::get_current_user();
		return ($user instanceof DAOUsers);
	}
	
	/**
	 * Returns true, if the user passed is the user logged in
	 */
	public static function is_current($user) {
		$ret = false;
		if ($user) {
			$current = self::get_current_user();
			if ($current) {
				$ret = ($current->id == $user->id);
			}
		}
		return $ret;
	}
	
	/**
	 * Returns true, if current user has given access level (or higher)
	 */
	public static function current_has_role($role) {
		if (self::is_logged_in()) {
			return self::get_current_user()->has_role($role);
		}
		return false;
	}

	/**
	 * Returns user with given ID or false if not found
	 * 
	 * @return DAOUsers
	 */
	public static function get($id) {
		return DB::get_item('users', 'id', $id); 
	} 
	
	/**
	 * Return all possible status
	 * 
	 * @return array
	 */
	public static function get_statuses() {
		return array(
			self::STATUS_ACTIVE => tr(self::STATUS_ACTIVE, 'users'),
			self::STATUS_DELETED => tr(self::STATUS_DELETED, 'users'),
			self::STATUS_DISABLED => tr(self::STATUS_DISABLED, 'users'),
			self::STATUS_UNCONFIRMED => tr(self::STATUS_UNCONFIRMED, 'users'),
		);
	}
	
	/**
	 * Logout current user
	 */
	public static function logout() {
		$cmd = CommandsFactory::create_command('users', 'logout', false);
		$cmd->execute();
	}
	
	/**
	 * Login given user. 
	 * 
	 * @param array $params Associative array with login information
	 * @param bool $permanent If TRUE, user get's permanently logged in
	 * 
	 * @return Status
	 */
	public static function login($params, $permanent) {
		$params['permanent'] = $permanent;
		$cmd = CommandsFactory::create_command('users', 'login', $params);
		$ret = $cmd->execute();

		if ($ret->is_ok() && $permanent) {
			$user = self::get_current_user();
			PermanentLogins::enable_permanent_login($user);
		}
		
		return $ret;
	}
	
	/**
	 * Performs actual login for a given user
	 * 
	 * @return Boolean True on success, false otherwise 
	 */
	public static function do_login($user) {
		$cmd = CommandsFactory::create_command($user, 'loginknown', array());
		$ret = $cmd->execute();
		return $ret->is_ok();
	}
	
	/**
	 * Creates an account
	 * 
	 * @param array $params Associative array with account's properties
	 * @param DAOUsers $result User created
	 * @return Status
	 */
	public static function create($params, &$result) {
		$cmd = CommandsFactory::create_command('users', 'create', $params);
		$err = $cmd->execute();
		$result = $cmd->get_result();
		return $err;
	}

	/**
	 * Registers a new User
	 * 
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param DAOUsers $result User created
	 * @return Status
	 */
	public static function register($username, $password, $email, &$result) {
		$params = array(
			'name' => $username,
			'email' => $email,
			'password' => $password
		);
		$cmd = CommandsFactory::create_command('users', 'register', $params);
		$err = $cmd->execute();
		$result = $cmd->get_result();
		return $err;
	}
	
	/**
	 * Edit account data of current user
	 * 
	 * @return Status
	 */
	public static function update(DAOUsers $user, $params) {
		$cmd = CommandsFactory::create_command($user, 'update', $params);
		$err = $cmd->execute();
		
		// If all is OK, update user (only self)	
		if ($err->is_ok()) {
			if (self::is_current($user)) {
  				self::do_login(clone($user));
			}
		}

		return $err;		
	}
	
	/**
	 * Prepare DAO instance for retrieving all user
	 * 
	 * @return DAOUsers
	 */
	public static function create_all_user_adapter() {
		$users = new DAOUsers();	
		return $users;
	} 
	
	/**
	 * Return number of unconfirmed users
	 * 
	 * @return int
	 */
	public static function count_unconfirmed() {
		$users = new DAOUsers();
		$users->status = USER_STATUS_UNCONFIRMED;	
		return $users->count();
	}	
	
	/**
	 * Get user roles
	 * 
	 * @return array Associative array with role id as key and role name as value
	 */
	public static function get_user_roles() {
		$ret = array();
		$dao = new DAOUserroles();
		$dao->find();
		while($dao->fetch()) {
			$ret[$dao->id] = tr($dao->name, 'app');
		}
		return $ret;								
	}	
					
	/**
	 * Initialize user management, session et al
	 */
	public static function initialize() {
		$current_user = Session::peek('current_user');
		
		if (empty($current_user)) {
			self::check_permanent_login();
		}
		else {
			self::do_login($current_user);
		}
	}
	
	/**
	 * Check if there is a permanent login to process. If so, log in according user
	 */
	private static function check_permanent_login() {
		$permanent = PermanentLogins::get_current();
		if ($permanent) {
			if ($user = self::get($permanent->id_user)) {
				Session::restart();
				self::do_login($user);
			}
		} 
	} 
	
	/**
	 * Log in as System user
	 */
	public static function login_as_system() {
		Load::models('systemusers');
		$user = new DAOSystemUsers();
		self::do_login($user);
	}
	
	/**
	 * Creates one time login for user that lost password 
	 *
	 * @param string $email
	 * @return Status
	 */
	public static function lost_password($email) {
		$ret = new Status();
		
		$user = new DAOUsers();
		$user->email = $email;
		$user->status = self::STATUS_ACTIVE;
		if ($user->find(IDataObject::AUTOFETCH)) {
			$params = array(
				'id_item' => $user->id,
				'action' => 'onetimelogin',
				'data' => $email
			);
			$cmd = CommandsFactory::create_command('confirmations', 'create', $params);
			$ret->merge($cmd->execute());
		}
		else {
			$ret->append(tr('Unknown email', 'users'));
		}
		return $ret;
	}
	
	/**
	 * Resend registration mail 
	 *
	 * @param string $email
	 * @return Status
	 */
	public static function resend_registration_mail($email) {
		$user = new DAOUsers();
		$user->email = $email;
		$ret = new Status();
		if ($user->find(IDataObject::AUTOFETCH)) {
			switch ($user->status) {
				case Users::STATUS_ACTIVE:
					$ret->append(tr('Your account already has been activated, use you email address and password to log in.', 'users'));
					$ret->persist();
					Url::create(ActionMapper::get_url('login'))->redirect();
					exit;
					break;
				case Users::STATUS_UNCONFIRMED:
					Load::models('confirmations');
					$confirmation = new DAOConfirmations();
					$confirmation->id_item = $user->id;
					$confirmation->action = 'createaccount'; 
					if ($confirmation->find(IDataObject::AUTOFETCH)) {
						$handler = $confirmation->create_handler();
						$ret->merge($handler->created());
					}
					else {
						$ret->append(tr('You activation request already has expired.', 'users'));
					}
					break;
				default:
					// Deleted, banned, or watever
					$ret->append(tr('Unknown email', 'users'));
					break;
			}
		}
		else {
			$ret->append(tr('Unknown email', 'users'));
		}
		return $ret;
	}
}
