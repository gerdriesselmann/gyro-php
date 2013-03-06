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

	const EMAIL_STATUS_UNCONFIRMED = 'UNCONFIRMED';
	const EMAIL_STATUS_CONFIRMED = 'CONFIRMED';
	const EMAIL_STATUS_EXPIRED = 'EXPIRED';
	const EMAIL_STATUS_BOUNCED = 'BOUNCED';
	
	/**
	 * Returns the user logged in or null, if no user is logged in
	 * 
	 * @return DAOUsers
	 */
	public static function get_current_user() {
		return AccessControl::get_current_aro();
	}

	/**
	 * Returns the id of the user logged in or false, if no user is logged in
	 * 
	 * @return int
	 */
	public static function get_current_user_id() {
		$ret = false;
		$user = self::get_current_user();
		if ($user) {
			$ret = $user->id;
		}
		return $ret;
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
	 * Reload current user (which is stored in session)
	 */
	public static function reload_current() {
		if (self::is_logged_in()) {
			$user = self::get(self::get_current_user_id());
			if ($user) {
				self::do_login($user);
			}
			else {
				self::logout();
			}
		}
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
	 * Return all possible email status
	 * 
	 * @return array
	 */
	public static function get_email_statuses() {
		return array(
			self::EMAIL_STATUS_UNCONFIRMED => tr(self::EMAIL_STATUS_UNCONFIRMED, 'users'),
			self::EMAIL_STATUS_CONFIRMED => tr(self::EMAIL_STATUS_CONFIRMED, 'users'),
			self::EMAIL_STATUS_EXPIRED => tr(self::EMAIL_STATUS_EXPIRED, 'users'),
			self::EMAIL_STATUS_BOUNCED => tr(self::EMAIL_STATUS_BOUNCED, 'users'),
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

	public static function create_deletion_command($user) {
		if (Config::has_feature(ConfigUsermanagement::REAL_DELETION)) {
			return CommandsFactory::create_command($user, 'delete', false);
		} else {
			return CommandsFactory::create_command($user, 'status', self::STATUS_DELETED);
		}
	}

	/**
	 * Prepare DAO instance for retrieving users (that are ACTIVE)
	 * 
	 * @return DAOUsers
	 */
	public static function create_adapter() {
		$users = new DAOUsers();	
		$users->status = self::STATUS_ACTIVE;
		return $users;
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
	 * Prepare DAO instance for retrieving all user with given roles
	 * 
	 * @return DAOUsers
	 */
	public static function create_role_adapter($roles) {
		$users = new DAOUsers();
		
		$all_roles = self::get_user_roles();
		$arr_roles_in = array();
		foreach(Arr::force($roles, false) as $r) {
			$key = array_search($r, $all_roles);
			if ($key) {
				$arr_roles_in[] = $key;
			}
		} 
		if (count($arr_roles_in) > 0) {
			$link = new DAOUsers2userroles();
			$link->add_where('id_role', DBWhere::OP_IN, $arr_roles_in);
			$users->status = self::STATUS_ACTIVE;	
			$users->join($link);			
		}
		else {
			$users->add_where('1 = 2');
		}
		
		return $users;
	}
	
	/**
	 * Return number of unconfirmed users
	 * 
	 * @return int
	 */
	public static function count_unconfirmed() {
		$users = new DAOUsers();
		$users->status = self::STATUS_UNCONFIRMED;
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
		$current_user_id = Session::peek('current_user_id');
		if (empty($current_user_id)) {
			// Backward compatability
			$current_user = Session::pull('current_user');
		}
		else {
			$current_user = self::get($current_user_id);
		}
		
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
				$cmd = CommandsFactory::create_command($user, 'restartsession', false);
				$cmd->execute();
				self::do_login($user);
				/*
				 * Experimental. May prevent problems with stuff that is packed in
				 * sessions when doing permanent login.
				 *
				 * E.g. opening pages with form validation tokens in a new
				 * browser instance generally failed.
				 *
				 * After the redirect session should be initialized correctly.
				 *
				 * TODO Somewhat hackish. There must be a solution in session, I presume
				 */
				sleep(1);
				Url::current()->redirect(Url::TEMPORARY);
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
		if ($email && $user->find(IDataObject::AUTOFETCH)) {
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
		if ($email && $user->find(IDataObject::AUTOFETCH)) {
			switch ($user->status) {
				case Users::STATUS_ACTIVE:
					$ret->append(tr('Your account already has been activated, use you user name and password to log in.', 'users'));
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
	
	/**
	 * Create a hash algorith instance of $hash_type
	 * 
	 * @since 0.5.1
	 * 
	 * @return IHashAlgorithm
	 */
	public static function create_hash_algorithm($hash_type) {
		// Load hash class
		$hash_type = strtolower($hash_type);
		Load::classes_in_directory('behaviour/commands/users/hashes', $hash_type, 'hash', true);
		$cls_name = Load::filename_to_classname($hash_type, 'hash');
		
		return new $cls_name();
	}

	/**
	 * Create a hash of $source using algorith $hash_type
	 * 
	 * @since 0.5.1
	 * 
	 * @return string
	 */
	public static function create_hash($source, $hash_type) {
		$algo = self::create_hash_algorithm($hash_type);
		return $algo->hash($source);
	}

	/**
	 * Confirm the email address of given user
	 * 
	 * @param DAOUsers $user
	 * @return Status
	 */
	public static function confirm_email($user) {
		$cmd = CommandsFactory::create_command($user, 'confirmemail', false);
		return $cmd->execute();
	}
	
	/**
	 * Returns wether a given username is unique or not
	 * 
	 * @return bool
	 */
	public static function is_unique_username($name) {
		$user = new DAOUsers();
		$user->add_where('status', '!=', Users::STATUS_DELETED);
		$user->name = $name;
		return ($user->count() == 0);				
	}
}
