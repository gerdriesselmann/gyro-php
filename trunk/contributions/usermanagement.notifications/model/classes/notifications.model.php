<?php
/**
 * Model for notifications
 */
class DAONotifications extends DataObjectBase implements ISelfDescribing, IStatusHolder {
	public $id;
	public $id_user;
	public $title;
	public $message;
	public $status;
	public $creationdate;
	
	/**
	 * Create the table object describing this dataobejcts table
	 */
	protected function create_table_object() {
		return new DBTable(
			'notifications',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBField::NOT_NULL),
				new DBFieldInt('id_user', null, DBFieldInt::UNSIGNED), // Null allowed!
				new DBFieldText('title', 200, null, DBField::NOT_NULL),
				new DBFieldText('message', DBFieldText::BLOB_LENGTH_SMALL, null, DBField::NOT_NULL),
				new DBFieldEnum('status', array_keys(Notifications::get_status()), Notifications::STATUS_NEW, DBField::NOT_NULL),
				new DBFieldDateTime('creationdate', DBFieldDateTime::NOW, DBFieldDateTime::TIMESTAMP | DBField::NOT_NULL)
			),
			'id',
			new DBRelation(
				'users',
				new DBFieldRelation('id_user', 'id'),
				DBRelation::NONE // null allowed!
			)
		);		
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
		$arrStates = array_keys(Notifications::get_status());
		foreach($arrStates as $state) {
			$cmd = 'status[' . $state . ']';
			$desc = tr('Set ' . $state); 
			$ret[$cmd] = $desc;
		}
		return $ret;
	}	
	
	/**
	 * Return array of user status filters. Array has filter as key and a readable description as value  
	 */
	public function get_filters() {
		return array(
			new DBFilterGroup(
				'status',
				tr('Status', 'notifications'),
				array(
					'new' => new DBFilterColumn('notifications.status', Notifications::STATUS_NEW, tr('Unread', 'notifications')),
					'read' => new DBFilterColumn('notifications.status', Notifications::STATUS_READ, tr('Read', 'notifications')),
				),
				'new'
			)
		);
	}
	
	/**
	 * Return assigned User
	 * 
	 * @return DAOUsers
	 */
	public function get_user() {
		return Users::get($this->id_user);
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
		return $this->title;
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
		return $this->status == Notifications::STATUS_NEW;
	}

	/**
	 * Returns true, if status is unconfirmed
	 *
	 * @return bool
	 */
	public function is_unconfirmed() {
		return false;
	}
	
	/**
	 * Returns true, if status is deleted
	 *
	 * @return bool
	 */
	public function is_deleted() {
		return false;
	}
	
	/**
	 * Returns true, if status is disabled
	 *
	 * @return bool
	 */
	public function is_disabled() {
		return $this->status == Notifications::STATUS_READ;
	}	
}
