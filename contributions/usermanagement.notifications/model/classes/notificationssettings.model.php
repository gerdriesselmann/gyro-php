<?php
Load::models('notifications');

/**
 * Manage norification subcriptions	
 */
class DAONotificationssettings extends DataObjectCached {
	public $id;
	public $id_user;
	public $mail_enable;
	public $mail_settings;
	public $digest_enable;
	public $digest_settings;
	public $digest_last_sent;
	public $feed_enable;
	public $feed_settings;
	public $feed_token;

	/**
	 * Create the table object describing this dataobejcts table
	 */
	protected function create_table_object() {
		return new DBTable(
			'notificationssettings',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBField::NOT_NULL),
				new DBFieldInt('id_user', null, DBFieldInt::UNSIGNED), // Null allowed!
				new DBFieldBool('mail_enable', true, DBField::NOT_NULL),
				new DBFieldSerialized('mail_settings', DBFieldSerialized::BLOB_LENGTH_SMALL, array(Notifications::SOURCE_ALL), DBField::NONE),
				new DBFieldBool('digest_enable', false, DBField::NOT_NULL),
				new DBFieldSerialized('digest_settings', DBFieldSerialized::BLOB_LENGTH_SMALL, array(Notifications::SOURCE_ALL), DBField::NONE),
				new DBFieldDateTime('digest_last_sent', null, DBField::INTERNAL),
				new DBFieldBool('feed_enable', false, DBField::NOT_NULL),
				new DBFieldSerialized('feed_settings', DBFieldSerialized::BLOB_LENGTH_SMALL, array(Notifications::SOURCE_ALL), DBField::NONE),
				new DBFieldText('feed_token', 40, null, DBField::INTERNAL)
			),
			'id',
			new DBRelation('users',	new DBFieldRelation('id_user', 'id'))
		);		
	}	

	/**
	 * Check if everything is OK
	 * 
	 * @see gyro/core/model/base/DataObjectBase#validate()
	 */
	public function validate() {
		// Create a feed token if feed was enbaled
		if ($this->feed_enable === false) {
			$this->feed_token = new DBNull();
		} 
		else if ($this->feed_enable === true && (empty($this->feed_token) || $this->feed_token instanceof DBNull)) {
			$this->feed_token = $this->create_feed_token();
		}
		// Set last sent date to now, if digest is enabled
		if ($this->digest_enable === false) {
			$this->digest_last_sent = new DBNull();
		} 
		else if ($this->digest_enable === true && (empty($this->digest_last_sent) || $this->digest_last_sent instanceof DBNull)) {
			$this->digest_last_sent = time();
		}
		// Set last sent date to now, if digest is enabled		
		 
		return parent::validate();
	}
	
	public function get_user() {
		return Users::get($this->id_user);
	}
	
	/**
	 * Returns wether a given type of notiofications is enabled 
	 */
	public function is_type_enabled($type) {
		$ret = false;
		switch($type) {
			case NotificationsSettings::TYPE_FEED:
				$ret = $this->feed_enable;
				break; 
			case NotificationsSettings::TYPE_MAIL:
				$ret = $this->mail_enable;
				break; 
			case NotificationsSettings::TYPE_DIGEST:
				$ret = $this->digest_enable;
				break; 
		}
		return $ret;
	}

	/**
	 * Returns settings for given type 
	 */
	public function get_settings_for_type($type) {
		$ret = array();
		switch($type) {
			case NotificationsSettings::TYPE_FEED:
				$ret = Arr::force($this->feed_settings, false);
				break; 
			case NotificationsSettings::TYPE_MAIL:
				$ret = Arr::force($this->mail_settings, false);
				break; 
			case NotificationsSettings::TYPE_DIGEST:
				$ret = Arr::force($this->digest_settings, false);
				break; 
		}
		return $ret;
	}
	
	/**
	 * Returns true if given notifications should be processed for given
	 * type (FEED, MAIL, DIGEST)
	 */
	public function should_notification_be_processed(DAONotifications $n, $type) {
		$ret = $this->source_matches($n->source, $type);
		
		return $ret;
	}
	
	/**
	 * Returns true if given source is part of settings for given type
	 */
	public function source_matches($source, $type) {
		$ret = false;
		if ($this->is_type_enabled($type)) {
			$settings = $this->get_settings_for_type($type);
			if (in_array(Notifications::SOURCE_ALL, $settings)) {
				$ret = true;
			}
			else {
				$ret = in_array($source, $settings);
			}
		}
		return $ret;
	}
		
	/**
	 * Create a feed token
	 * 
	 * @return string
	 */
	protected function create_feed_token() {
		$user = Users::get($this->id_user);
		$seed = rand(1000000, 9999999);
		if ($user) {
			$seed .= $user->password . $user->creationdate;
		}
		return Common::create_token($seed);			
	}
	
	/**
	 * Returns true, if feed is enabled and valid
	 * 
	 * @return bool
	 */
	public function is_feed_enabled() {
		return $this->feed_enable && $this->feed_token;
	}
}