<?php
/**
 * Helper class for click track injction
 */
class ClickTrackInjecter {
	private $source;
	private $notification;
	
	public function __construct($notification, $source) {
		$this->notification = $notification;
		$this->source = $source;
	}
	
	public function callback($matches)  {
		// $matches[2] => URL
		$old_url = $matches[2]; 
		$new_url = $this->notification->create_click_track_link($this->source, $old_url);
		return "<a{$matches[1]}href=\"$new_url\"{$macthes[3]}>";		
	}
}

/**
 * Model for notifications
 */
class DAONotifications extends DataObjectTimestampedCached implements ISelfDescribing, IStatusHolder {
	public $id;
	public $id_user;
	public $title;
	public $message;
	public $source;
	public $source_id;
	public $source_data;
	public $sent_as;
	public $read_through;
	public $read_action;
	public $status;
	
	/**
	 * Create the table object describing this dataobejcts table
	 */
	protected function create_table_object() {
		return new DBTable(
			'notifications',
			array_merge(array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBField::NOT_NULL),
				new DBFieldInt('id_user', null, DBFieldInt::UNSIGNED), // Null allowed!
				new DBFieldText('title', 200, null, DBField::NOT_NULL),
				new DBFieldText('message', DBFieldText::BLOB_LENGTH_SMALL, null, DBField::NOT_NULL),
				new DBFieldText('source', 100, Notifications::SOURCE_APP, DBField::NOT_NULL),
				new DBFieldInt('source_id', null, DBFieldInt::UNSIGNED), // Null allowed!
				new DBFieldSerialized('source_data', DBFieldSerialized::BLOB_LENGTH_SMALL, null, DBField::NONE),
				new DBFieldSet('sent_as', array_keys(Notifications::get_delivery_methods()), null, DBField::NONE),
				new DBFieldEnum('read_through', array_keys(Notifications::get_read_sources()), Notifications::READ_UNKNOWN, DBField::NOT_NULL),
				new DBFieldText('read_action', 30, null, DBField::NONE),
				new DBFieldEnum('status', array_keys(Notifications::get_status()), Notifications::STATUS_NEW, DBField::NOT_NULL),
				), $this->get_timestamp_field_declarations()
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
	 * Add a sent as 
	 */
	public function add_sent_as($val) {
		DBFieldSet::set_set_value($this->sent_as, $val);
	}
	
	/**
	 * Returns message
	 * 
	 * @param string $click_track_source 
	 *   If set, all Links are turned into click tracking links for given source 
	 *   (one of the DELIVER_* constants)
	 * @return string         
	 */
	public function get_message($click_track_source = false) {
		$ret = $this->message;
		if ($click_track_source) {
			$injector = new ClickTrackInjecter($this, $click_track_source);
			$reg = '@<a(.*?)href="(.*?)"(.*?)>@';
			$ret = String::preg_replace_callback($reg, array($injector, 'callback'), $ret);	
		}
		return $ret;
	}
	
	/**
	 * Return a fingerprint for given source and url
	 * 
	 * Used for click tracking, to protect it from beeing spoofed 
	 */
	public function click_track_fingerprint($source, $url) {
		return sha1(
			$this->id .
			$this->id_user .
			$this->message .
			$source .
			$this->creationdate .
			$url .
			$this->title
		);
	}

	/**
	 * Turn given URL into a clicktracked URL
	 */
	public function create_click_track_link($source, $url) {
		$new_url = Url::create(ActionMapper::get_url('clicktrack', $this));
		$new_url->replace_query_parameter('src', $source);
		$new_url->replace_query_parameter('url', $url);
		$new_url->replace_query_parameter('token', $this->click_track_fingerprint($source, $url));
		return $new_url->build();
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
		$ret['exclude'] = tr('End notification', 'notifications');
		return $ret;
	}	
	
	/**
	 * Return array of user status filters. Array has filter as key and a readable description as value  
	 */
	public function get_filters() {
		$sources = array();
		foreach(Notifications::get_all_sources($this->id_user) as $source => $descr) {
			$sources[$source] = new DBFilterColumn('notifications.source', $source, $descr);			
		}
		return array(
			new DBFilterGroup(
				'status',
				tr('Status', 'notifications'),
				array(
					'new' => new DBFilterColumn('notifications.status', Notifications::STATUS_NEW, tr('Unread', 'notifications')),
					'read' => new DBFilterColumn('notifications.status', Notifications::STATUS_READ, tr('Read', 'notifications')),
				),
				'new'
			),
			new DBFilterGroup(
				'source',
				tr('Source', 'notifications'),
				$sources
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
