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
		Load::models('notifications');
		$user = $this->get_instance();
		$params = $this->get_params();
		$created = false;
		return Notifications::create($user, $params, $created);
	}
} 