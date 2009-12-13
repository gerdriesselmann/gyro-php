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
	public static function create($user, $params, &$created) {
		$params['id_user'] = $user->id;
		$cmd = CommandsFactory::create_command('notifications', 'create', $params);
		$ret = $cmd->execute();
		$created = $cmd->get_result();
		return $ret;
	}
}
