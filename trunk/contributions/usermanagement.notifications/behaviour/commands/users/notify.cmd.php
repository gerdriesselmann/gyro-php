<?php
/**
 * Send a notification to user
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
