<?php
Load::models(array('userroles', 'users2userroles'));

/**
 * Table Definition for users
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class DAOUsers extends DataObjectTimestampedCached implements IStatusHolder, ISelfDescribing {
	public $id;                              // int(10)  not_null primary_key unsigned auto_increment
	public $name;
	public $password;                        // string(50)  
	public $hash_type;
	public $email;                           // string(100)
	public $emailconfirmationdate; 
	public $emailstatus;
	public $tos_version; 
	public $status;                          // string(11)  not_null enum
	
	// now define your table structure.
	// key is column name, value is type
	protected function create_table_object() {
		return new DBTable(
			'users',
			array_merge(array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBField::NOT_NULL),
				new DBFieldText('name', 100, null, DBField::NOT_NULL),
				new DBFieldTextEmail('email', null, DBField::NOT_NULL),
				new DBFieldText('password', 100, null, DBField::NOT_NULL),
				new DBFieldText('hash_type', 5, 'md5', DBField::NOT_NULL | DBField::INTERNAL),
				new DBFieldDateTime('emailconfirmationdate', null, DBField::NONE | DBField::INTERNAL), 
				new DBFieldEnum('emailstatus', array_keys(Users::get_email_statuses()), Users::EMAIL_STATUS_UNCONFIRMED, DBField::NOT_NULL | DBField::INTERNAL),
				new DBFieldInt('tos_version', 0, DBFieldInt::UNSIGNED | DBField::NOT_NULL | DBField::INTERNAL), 
				new DBFieldEnum('status', array_keys($this->get_allowed_status()), Users::STATUS_UNCONFIRMED, DBField::NOT_NULL | DBField::INTERNAL),
				), $this->get_timestamp_field_declarations()
			),
			'id'
		);
	}
	
	/**
	 * Return roles of this user
	 *
	 * @return array
	 */
	public function get_roles() {
		return $this->get_from_cache('userroles', 'do_get_roles');
	}
	
	/**
	 * Return roles of this user (worker)
	 *
	 * @return array
	 */
	protected function do_get_roles($params) {
		return UserRoles::get_for_user($this->id);
	}	
	
 	/**
	 * Return array of allowed status for table description
	 *
	 * @return array
	 */
	protected function get_allowed_status() {
		return Users::get_statuses(); 	
	}
	
	/**
	 * Create a user (and seed ) specific token
	 * 
	 * @param string $creator Module or class (or more wider: name of) creating the token. Is is part of the seed
	 * @param string|array $data Data passed into seed  
	 */
	public function create_token($creator, $data) {
		$src = '';
		$src .= $this->email;
		$src .= $this->password;
		$src .= $creator;
		$src .= $this->id;
		if (is_array($data)) {
			$data = Arr::implode('§', $data, '~');
		}
		$src .= $data;
		$src .= $this->get_creation_date(); // modificationdate ist not that stable
		$src .= $this->name;
		return sha1($src);
	}

	/**
	 * Returns true, if the user confirmed the latest tos
	 * 
	 * @return bool
	 */
	public function confirmed_tos() {
		return $this->tos_version >= Config::get_value(ConfigUsermanagement::TOS_VERSION);
	}
	
	/**
	 * Returns true if the email is confirmed
	 * 
	 * @return bool
	 */
	public function confirmed_email() {
		return $this->emailstatus == Users::EMAIL_STATUS_CONFIRMED;
	}
	
	/**
	 * Check if password matches the user's one
	 */
	public function password_match($pwd) {
		$algo = Users::create_hash_algorithm($this->hash_type);
		return $algo->check($pwd, $this->password);
	}
 	
	// ***********************************************
	// Self-Describing
	// ***********************************************

	/**
	 * Get title for this class
	 * 
	 * @return string
	 */
	public function get_title() {
		return $this->name;
	}

	/**
	 * Get description for this instance
	 *  
	 * @return string 
	 */
	public function get_description() {
		return '';	
	}

	// ************************************************
	// IStatusHolder
	// ************************************************
	
	/**
	 * Set status
	 *
	 * @param string $status
	 */
	public function set_status($status) {
		$this->status = $status;
	}
	
	/**
	 * Returns status
	 * 
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}
	
	/**
	 * Returns true, if status is active
	 *
	 * @return bool
	 */
	public function is_active() {
		return $this->status == Users::STATUS_ACTIVE;
	}

	/**
	 * Returns true, if status is unconfirmed
	 *
	 * @return bool
	 */
	public function is_unconfirmed() {
		return $this->status == Users::STATUS_UNCONFIRMED;
	}
	
	/**
	 * Returns true, if status is deleted
	 *
	 * @return bool
	 */
	public function is_deleted() {
		return $this->status == Users::STATUS_DELETED;
	}
	
	/**
	 * Returns true, if status is disabled
	 *
	 * @return bool
	 */
	public function is_disabled() {
		return $this->status == Users::STATUS_DISABLED;
	}
	
	// **************************************
	// Access Check Functions
	// **************************************
	
	/**
	 * Returns true, if user has given role
	 * 
	 * @param string|array $role The role(s) to check for. If param is array, it is checked if user has any of the roles passed
	 * @return bool
	 */
	public function has_role($role) {
		$ret = false;
		$my_roles = $this->get_role_names();
		foreach(Arr::force($role) as $check) {
			if (array_key_exists($check, $my_roles)) {
				$ret = true;
				break;
			}
		}
		return $ret;
	}
	
	/**
	 * Returns array of role names
	 * 
	 * @return array
	 */
	public function get_role_names() {
		return $this->get_from_cache('userrolenames', 'do_get_role_names');
	}

	/**
	 * Returns array of role names
	 * 
	 * @return array
	 */
	protected function do_get_role_names($params) {
		$ret = array();
		foreach($this->get_roles() as $role) {
			$t = trim($role->name);
			$ret[$t] = $t;
		}
		return $ret;
	}
	
	
	/**
	 * Confirm user (set status to ACTIVE) 
	 */
	public function confirm() {
		$this->status = Users::STATUS_ACTIVE;
	}

	// ******************************************
	// DataObject overloads
	// ******************************************
	
	/**
	 * Return array of user status filters. Array has filter as key and a readable description as value  
	 */
	public function get_filters() {
		return array(
			new DBFilterGroup(
				'status',
				tr('Status'),
				array(
					'unconfirmed' => new DBFilterColumn('users.status', Users::STATUS_UNCONFIRMED, tr('Unconfirmed', 'users')),
					'disabled' => new DBFilterColumn('users.status', Users::STATUS_DISABLED, tr('Disabled', 'users')),
					'deleted' => new DBFilterColumn('users.status', Users::STATUS_DELETED, tr('Deleted', 'users')),
					'active' => new DBFilterColumn('users.status', Users::STATUS_ACTIVE, tr('Active', 'users')),
				)
			),
			// TODO Rewrite
			/*
			new DBFilterGroup(
				'role',
				tr('User Role', 'users'),
				array(
					'admin' => new DBFilterColumn('users.role', USER_ROLE_ADMIN, tr('Admin', 'users')),
					'user' => new DBFilterColumn('users.role', USER_ROLE_USER, tr('User', 'users')),
					'editor' => new DBFilterColumn('users.role', USER_ROLE_EDITOR, tr('Editor', 'users')),
				)
			)
			*/
		);
	}
	
	/**
	 * Return array of sortable columns. Array has column name as key and some sort of sort-column-object or an array as values  
	 */
	public function get_sortable_columns() {
		return array(
			'name' => new DBSortColumn('name', tr('Name', 'users'), DBSortColumn::TYPE_TEXT),	
			'email' => new DBSortColumn('email', tr('E-mail', 'users'), DBSortColumn::TYPE_TEXT),
			'creationdate' => new DBSortColumn('creationdate', tr('Registered since', 'users'), DBSortColumn::TYPE_DATE)
		);
	}

	/**
	 * Get the column to sort by default
	 */
	public function get_sort_default_column() {
		return 'name';
	}

	/**
	 * To be overloaded. Returns array of actions with action title as key and action description as value 
	 *
	 * Subclasses can return array of actions, this class will detect if they are commands or actions.
	 * 
	 * Optionally, params can be added in brackets like 'status[DISABLED]' => 'Disable this item'.  
	 * 
	 * @param string $context
	 * @param mixed $user
	 * @param mixed $params
	 * @return array
	 */
	protected function get_actions_for_context($context, $user, $params) {
		$real_deletion = Config::has_feature(ConfigUsermanagement::REAL_DELETION);
		$ret = array();
		$ret['edit'] = tr('Edit user', 'users');
		
		$arrStates = array(
			Users::STATUS_ACTIVE,
			Users::STATUS_DISABLED,
		);
		if (!$real_deletion) {
			$arrStates[] = Users::STATUS_DELETED;
		}
		foreach($arrStates as $state) {
			$cmd = 'status[' . $state . ']';
			$desc = tr('Set ' . $state); 
			$ret[$cmd] = $desc;
		}
		if ($real_deletion) {
			$ret['delete'] = tr('Delete');
		}
		return $ret;
	}
}
