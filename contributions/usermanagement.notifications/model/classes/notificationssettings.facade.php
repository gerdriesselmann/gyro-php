<?php
/**
 * Facade for notofocation settings
 */
class NotificationsSettings {
	const TYPE_MAIL = 'mail';
	const TYPE_DIGEST = 'digest';
	const TYPE_FEED = 'feed';
	
	/**
	 * Get settings for user
	 * 
	 * @return DAONotificationssettings 
	 */
	public static function get_for_user($user_id) {
		return DB::get_item('notificationssettings', 'id_user', $user_id);
	}
	
	/**
	 * Collect all sources for given user
	 * 
	 * - Raise event notifications_collect_sources
	 * - Select DISTINCT from DB
	 */
	public static function collect_sources(DAOUsers $user) {
		Load::components('notifications/sources');
		$collected = array(
			Notifications::SOURCE_ALL => tr(Notifications::SOURCE_ALL, 'notifications'),
			Notifications::SOURCE_APP => tr(Notifications::SOURCE_APP, 'notifications'),
		);
		EventSource::Instance()->invoke_event('notifications_collect_sources', $user, $collected);

		$ret = array();
		// Collected may contain instances of INotificationSource or just key-value pairs
		foreach ($collected as $key => $item_or_title) {
			if ($item_or_title instanceof INotificationsSource) {
				$ret[$item_or_title->get_key()] = $item_or_title;
			} else {
				$ret[$key] = new NotificationSource($key, $item_or_title);
			}
		}
		
		$dao = new DAONotifications();
		$dao->id_user = $user->id;
		
		$query = $dao->create_select_query();
		$query->set_fields('source');
		$query->set_policy(DBQuerySelect::DISTINCT);
		
		$result = DB::query($query);
		while($row = $result->fetch()) {
			$key = $row['source'];
			if (!array_key_exists($key, $ret)) {
				$ret[$key] = new NotificationSourceByKey($key);
			}
		}
		
		return $ret;
	}
	
	/**
	 * Return adapter to collect notifications for feed
	 * 
	 * @return DAONotifications
	 */
	public static function create_feed_adapter(DAONotificationssettings $settings) {
		$dao = self::create_notification_adapter($settings, self::TYPE_FEED);
		// Entries of last 5 days, but at least 10 entries
		$dao->add_where('creationdate', '>=', time() - GyroDate::ONE_DAY);
		if ($dao->count() < 10) {
			$dao = self::create_notification_adapter($settings, self::TYPE_FEED);
			$dao->limit(10);
		}		
		$dao->sort('creationdate', DataObjectBase::DESC);
		return $dao;		
	}
	
	/**
	 * Return adapter to collect notifications for digest
	 * 
	 * @return DAONotifications
	 */
	public static function create_digest_adapter(DAONotificationssettings $settings) {
		$dao = self::create_notification_adapter($settings, self::TYPE_DIGEST);
		// Entries as of last sent
		$dao->add_where('creationdate', '>=', $settings->digest_last_sent);
		$dao->sort('creationdate', DataObjectBase::DESC);
		return $dao;		
	}	
	
	/**
	 * Prepapre basic adapter fepending on settigns
	 * 
	 * @param string $type Either "feed" or "digest" 
	 * @return DAONotifications
	 */
	private static function create_notification_adapter(DAONotificationssettings $settings, $type) {
		// Fidn notifications
		$dao = Notifications::create_user_adapter($settings->id_user);
		$sources = $settings->get_settings_for_type($type);
		
		if (count($sources) == 0 || !$settings->is_type_enabled($type)) {
			$dao->add_where('1 = 2');	
		}
		else {
			if (!in_array(Notifications::SOURCE_ALL, $sources)) {
				$dao->add_where('source', DBWhere::OP_IN, $sources);
			}
		}
		return $dao;
	}
	
	/**
	 * Create an adapter to find all settings that may have digests to send 
	 * 
	 * @return DAONotificationssettings
	 */
	public static function create_possible_digest_adapter() {
		// SELECT DISTINCT s.*
		// FROM notificationssettings s
		// INNER JOIN notifications n ON (n.id_user = s.id_user AND n.creationdate >= s.digest_last_sent)
		// WHERE s.digest_enable = 'TRUE';
		Load::models('notifications');
		
		$dao = new DAONotificationssettings();
		$dao->digest_enable = true;
		
		$n = new DAONotifications();
		$dao->join($n, array(
			new DBJoinCondition($dao, 'id_user', $n, 'id_user'),
			new DBWhere($dao, 'notifications.creationdate >= notificationssettings.digest_last_sent')			
		));
		
		return $dao;
	}
}