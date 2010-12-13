<?php
/**
 * Facade class for notifications model
 */
class Notifications {
	const SOURCE_ALL = 'all';
	const SOURCE_APP = 'app';
	
	const STATUS_NEW = 'NEW';
	const STATUS_READ = 'READ';
	
	const DELIVER_MAIL = 'MAIL';
	const DELIVER_DIGEST = 'DIGEST';
	const DELIVER_FEED = 'FEED';
	
	const READ_UNKNOWN = 'UNKNOWN';
	const READ_MARK_MANUALLY = 'MANUALLY'; // Explcitly marked read in UI
	const READ_MARK_AUTO = 'AUTO'; // Automatically marked read in UI
	const READ_MARK_ALL = 'ALL'; // Mark all as read button clicked
	const READ_MAIL = 'MAIL'; 
	const READ_DIGEST = 'DIGEST';
	const READ_FEED = 'FEED';
	const READ_CONTENT = 'CONTENT'; // User browsed to notified content without clicking a notification link
		
	/**
	 * return given Notification
	 * 
	 * @return DAONotifications
	 */
	public static function get($id) {
		return DB::get_item('notifications', 'id', $id);
	}
	
	/**
	 * Returns all possible status
	 * 
	 * @return array
	 */
	public static function get_status() {
		return array(
			self::STATUS_NEW => tr(self::STATUS_NEW, 'notifications'),
			self::STATUS_READ => tr(self::STATUS_READ, 'notifications')
		);
	}
	
	/**
	 * Returns all possible status
	 * 
	 * @return array
	 */
	public static function get_delivery_methods() {
		return array(
			self::DELIVER_MAIL => tr(self::DELIVER_MAIL, 'notifications'),
			self::DELIVER_FEED => tr(self::DELIVER_FEED, 'notifications'),
			self::DELIVER_DIGEST => tr(self::DELIVER_DIGEST, 'notifications'),
		);
	}
	
	/**
	 * Returns all possible status
	 * 
	 * @return array
	 */
	public static function get_read_sources() {
		return array(
			self::READ_UNKNOWN => tr(self::READ_UNKNOWN, 'notifications'),
			self::READ_MARK_MANUALLY => tr(self::READ_MARK_MANUALLY, 'notifications'),
			self::READ_MARK_AUTO => tr(self::READ_MARK_AUTO, 'notifications'),
			self::READ_MARK_ALL => tr(self::READ_MARK_ALL, 'notifications'),
			self::READ_MAIL => tr(self::READ_MAIL, 'notifications'),
			self::READ_FEED => tr(self::READ_FEED, 'notifications'),
			self::READ_DIGEST => tr(self::READ_DIGEST, 'notifications'),
			self::READ_CONTENT => tr(self::READ_CONTENT, 'notifications'),			
		);
	}	
	
	/**
	 * Finds all notifications for given user
	 * 
	 * @return DAONotifications
	 */
	public static function create_user_adapter($id_user) {
		$ret = new DAONotifications();
		$ret->id_user = $id_user;
		$ret->sort('creationdate', DataObjectBase::DESC);
		return $ret;
	}
	
	/**
	 * Finds all unread notifications for given user
	 * 
	 * @return DAONotifications
	 */
	public static function create_unread_user_adapter($id_user) {
		$ret = self::create_user_adapter($id_user);
		$ret->status = self::STATUS_NEW;
		return $ret;
	}

	/**
	 * Returns $num latest notifications for user
	 * 
	 * @return array
	 */
	public static function get_latest($user_id, $num) {
		$dao = self::create_unread_user_adapter($user_id);
		$dao->limit(0, $num);
		return $dao->find_array();
	}
	
	/**
	 * Create a notification for given user
	 * 
	 * @param DAOUsers $user
	 * @param array $params
	 * @param mixed $created
	 * @return Status
	 */
	public static function create(DAOUsers $user, $params, &$created) {
		$ret = new Status();
		$params['id_user'] = $user->id;
		$params['title'] = self::compute_title(Arr::get_item($params, 'message', ''), Arr::get_item($params, 'title', ''));
		$cmd = CommandsFactory::create_command('notifications', 'create', $params);
		$ret->merge($cmd->execute());
		$created = $cmd->get_result();
		return $ret;
	}
	
	/**
	 * Notify a user by sending a message
	 * 
	 * @param DAOUsers $user
	 * @param string $message
	 * @param string $title If title is empty a title will be computed from message
	 * @return Status
	 */
	public static function notify_single_user(DAOUsers $user, $message, $title = '', $source = 'app', $params = array()) {
		$ret = new Status();
		$params['title'] = self::compute_title($message, $title);
		$params['message'] = $message;
		$params['source'] = $source;

		$cmd = CommandsFactory::create_command($user, 'notify', $params);
		if ($cmd->can_execute($user)) {
			$ret->merge($cmd->execute());
		}
		return $ret;
	}
	
	/**
	 * Notify a couple of user by sending a message
	 * 
	 * @param array $arr_users Array of DAOUsers
	 * @param string $message
	 * @param string $title If title is empty a title will be computed from message
	 * @return Status
	 */
	public static function notify_some_users($arr_users, $message, $title = '', $source = 'app', $params = array()) {
		$ret = new Status();
		$already_notified_ids = array();
		$title = self::compute_title($message, $title);
		foreach($arr_users as $user) {
			$user_id = $user->id;
			if (!in_array($user_id, $already_notified_ids)) {
				$ret->merge(self::notify_single_user($user, $message, $title, $source, $params));
				if ($ret->is_error()) {
					break;
				}	
				$already_notified_ids[] = $user_id;
			}
		}
		return $ret;
	}
	
	/**
	 * Notify all users by sending a message
	 * 
	 * @param string $message
	 * @param string $title If title is empty a title will be computed from message
	 * @return Status
	 */
	public static function notify_all_users($message, $title = '', $source = 'app', $params = array()) {
		$params['title'] = self::compute_title($message, $title);
		$params['message'] = $message;
		$params['source'] = $source;
		$cmd = CommandsFactory::create_command('users', 'notifyall', $params);
		return $cmd->execute();		
	}
	
	/**
	 * If title is empty return begin of message as title
	 */
	private static function compute_title($message, $title) {
		if (empty($title)) {
			$title = String::substr_word(String::clear_html($message), 0, 150) . '...';
		}
		return $title;
	}
	
	/**
	 * Returns all sources available (for user)
	 * 
	 * @return array
	 */
	public static function get_all_sources($id_user) {
		$dao = new DAONotifications();
		$dao->id_user = $id_user;
		$query = $dao->create_select_query();
		$query->set_fields('source');
		$query->set_policy(DBQuerySelect::DISTINCT);
		
		$result = DB::query($query);
		$ret = array();
		while($row = $result->fetch()) {
			$ret[$row['source']] = self::translate_source($row['source']);
		}
		return $ret;
	}
	
	/**
	 * Translates source
	 */
	public static function translate_source($src) {
		$ret = $src;
		switch($src) {
			case self::SOURCE_ALL:
			case self::SOURCE_APP:
				$ret = tr($src, 'notifications');
				break;
			default:
				EventSource::Instance()->invoke_event('notifications_translate', $src, $ret);
				break;
		}
		return $ret;
	}
	
	/**
	 * Returns Notiifcation, if a notification with given params already exists
	 * 
	 * Checked are id_user, source and source_id (which must be non-empty!)
	 * 
	 * @return DAONotifications Existing instance or false if none
	 */
	public static function existing($params) {
		$ret = false;
		$source_id = Arr::get_item($params, 'source_id', false);
		$id_user = Arr::get_item($params, 'id_user', false);
		$source =  Arr::get_item($params, 'source', false);
		if ($source_id && $id_user && $source) {
			$dao = new DAONotifications();
			$dao->id_user = $id_user;
			$dao->source = $source;
			$dao->source_id = $source_id;
			$dao->status = self::STATUS_NEW;
			if ($dao->find(DataObjectBase::AUTOFETCH)) {
				$ret = $dao;
			}
		}
		return $ret;
	}
	
	/**
	 * Mark matching notifications as read
	 * 
	 * @return void
	 */
	public static function mark_as_read_by_source_id($id_user, $source, $source_id, $read_through = self::READ_CONTENT, $read_action = '') {
		if ($source_id && $id_user && $source) {
			$params = array(
				'read_through' => $read_through,
				'read_action' => $read_action 
			);
			$dao = new DAONotifications();
			$dao->id_user = $id_user;
			$dao->source = $source;
			$dao->source_id = $source_id;
			$dao->status = self::STATUS_NEW;
			$dao->find();
			while($dao->fetch()) {
				$cmd = CommandsFactory::create_command(clone($dao), 'markread', $params);
				$cmd->execute();
			}
		}
	}
}
