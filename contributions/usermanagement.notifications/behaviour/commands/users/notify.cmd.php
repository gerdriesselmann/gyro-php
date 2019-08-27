<?php
/**
 * Send a notification to user
 *
 * Takes DAOUsers as instance
 *
 * Takes params:
 *   - message - Message to send to user
 *   - title (optional). Title of message
 *   - source_data (optional) - Additional data
 *   - source  (optional) - Source of notification
 *   - source_id  (optional) - ID of source, if any
 */
class NotifyUsersCommand extends CommandTransactional {
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'notify';
	}
	
	/**
	 * Aktually do something :)
	 */
	protected function do_execute() {
		$ret = new Status();

		Load::models('notifications', 'notificationsexceptions');
		$user = $this->get_instance();
		$params = $this->get_params();
		$created = false;
		if (!NotificationsExceptions::excluded($user->id, Arr::get_item($params, 'source', ''), Arr::get_item($params, 'source_id', ''))) {
			$ret->merge(Notifications::create($user, $params, $created));
		}
		return $ret;
	}
}
