<?php
Load::models(array('userroles', 'users2userroles'));

/**
 * Table Definition for users
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class DAOUsers extends DataObjectBase implements IStatusHolder, ISelfDescribing  {
	public $id;                              // int(10)  not_null primary_key unsigned auto_increment
	public $name;
	public $password;                        // string(50)  
	public $email;                           // string(100)  
	public $status;                          // string(11)  not_null enum
	public $creationdate;                    // datetime(19)  binary
	public $modificationdate;                // timestamp  binary

	/**
	 * All role names in a flat array
	 *
	 * @var array
	 */
	protected $cached_role_names = null;
	/**
	 * All roles
	 *
	 * @var array
	 */
	protected $cached_roles = null;
	
	// now define your table structure.
	// key is column name, value is type
	protected function create_table_object() {
		return new DBTable(
			'users',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldText('name', 100, null, DBFieldText::NOT_NULL),
				new DBFieldText('email', 100, null, DBFieldText::NOT_NULL),
				new DBFieldText('password', 50),
				new DBFieldEnum('status', $this->get_allowed_status(), Users::STATUS_UNCONFIRMED, DBFieldEnum::NOT_NULL),
				new DBFieldDateTime('creationdate', DBFieldDateTime::NOW, DBFieldDateTime::NOT_NULL),
				new DBFieldDateTime('modificationdate', DBFieldDateTime::NOW,  DBFieldDateTime::TIMESTAMP | DBFieldDateTime::NOT_NULL)
			),
			'id'
		);
	}
	
	/**
	 * Return roels of this user
	 *
	 * @return array
	 */
	public function get_roles() {
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
		$my_roles = $this->get_role_names();
		foreach(Arr::force($role) as $check) {
			if (in_array($check, $my_roles)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Returns array of role names
	 * 
	 * @return array
	 */
	public function get_role_names() {
		if (is_null($this->cached_role_names)) {
			$this->cached_role_names = array();
			$link = new DAOUsers2userroles();
			$link->id_user = $this->id;
			
			$dao = new DAOUserroles();
			$dao->join($link);
			$dao->find();
			
			while($dao->fetch()) {
				$this->cached_role_names[trim($dao->name)] = trim($dao->name);
			}
		}
		return $this->cached_role_names;
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
		$ret = array();
		$ret['edit'] = tr('Edit user', 'users');
		
		$arrStates = array(
			Users::STATUS_ACTIVE,
			Users::STATUS_DISABLED,
			Users::STATUS_DELETED
		);
		foreach($arrStates as $state) {
			$cmd = 'status[' . $state . ']';
			$desc = tr('Set ' . $state); 
			$ret[$cmd] = $desc;
		}
		return $ret;
	}
		
	/**
	 * Validate user
	 */
	public function validate() {
		$err = parent::validate();
		if (Validation::is_email($this->email) == false) {
			$err->append(tr('No valid e-mail', 'users'));
		}
		return $err;		
	}

	/**
	 * Insert data. Autoincrement IDs will be automatically set.
 	 * 
 	 * @return Status
 	 */
 	public function insert() {
		if (empty($this->creationdate)) {
 			$this->creationdate = time();
		}
		return parent::insert(); 		
 	}
 	
 	/**
 	 * Update current item
 	 * 
 	 * @param int $policy If DBDataObject::WHERE_ONLY is used, no conditions are build automatically
 	 * @return Status
 	 */
 	public function update($policy = self::NORMAL) {
 		$this->modificationdate = time();
 		return parent::update($policy);	 		
 	}
	
}
