<?php
/**
 * Facade class for notifications model
 */
class Notifications {
	const SOURCE_ALL = 'all';
	const SOURCE_APP = 'app';
	
	const STATUS_NEW = 'NEW';
	const STATUS_READ = 'READ';
	
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
		$params['id_user'] = $user->id;
		$params['title'] = self::compute_title(Arr::get_item($params, 'message', ''), Arr::get_item($params, 'title', ''));
		$cmd = CommandsFactory::create_command('notifications', 'create', $params);
		$ret = $cmd->execute();
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
	public static function notify_single_user(DAOUsers $user, $message, $title = '', $source = 'app') {
		$params = array(
			'title' => self::compute_title($message, $title),
			'message' => $message,
			'source' => $source
		);
		$cmd = CommandsFactory::create_command($user, 'notify', $params);
		return $cmd->execute();
	}
	
	/**
	 * Notify a couple of user by sending a message
	 * 
	 * @param array $arr_users Array of DAOUsers
	 * @param string $message
	 * @param string $title If title is empty a title will be computed from message
	 * @return Status
	 */
	public static function notify_some_users($arr_users, $message, $title = '', $source = 'app') {
		$ret = new Status();
		$title = self::compute_title($message, $title);
		foreach($arr_users as $user) {
			$ret->merge(self::notify_single_user($user, $message, $title, $source));
			if ($ret->is_error()) {
				break;
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
	public static function notify_all_users($message, $title = '', $source = 'app') {
		$params = array(
			'title' => self::compute_title($message, $title),
			'message' => $message,
			'source' => $source
		);		
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
}
