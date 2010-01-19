<?php
/**
 * Facade class for notifications model
 */
class Notifications {
	const STATUS_NEW = 'NEW';
	const STATUS_READ = 'READ';
	
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
	public static function notify_single_user(DAOUsers $user, $message, $title = '') {
		$params = array(
			'title' => self::compute_title($message, $title),
			'message' => $message
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
	public static function notify_some_users($arr_users, $message, $title = '') {
		$ret = new Status();
		$title = self::compute_title($message, $title);
		foreach($arr_users as $user) {
			$ret->merge(self::notify_single_user($user, $message, $title));
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
	public static function notify_all_users($message, $title = '') {
		$params = array(
			'title' => self::compute_title($message, $title),
			'message' => $message
		);		
		$cmd = CommandsFactory::create_command('users', 'notifyall', $params);
		return $cmd->execute();		
	}
	
	/**
	 * If title is empty return begin of message as title
	 */
	private static function compute_title($message, $title) {
		if (empty($title)) {
			$title = String::substr_word($message, 0, 150) . '...';
		}
		return $title;
	}
}
