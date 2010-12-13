<?php
/**
 * Mark notification as read
 */
class MarkreadNotificationsCommand extends CommandChain {
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'markread';
	}
	
	/**
	 * Returns a description of this command
	 */
	public function get_description() {
		return tr('Mark as read', 'notifications');
	}

	/**
	 * Do it
	 * 
	 * @see core/behaviour/base/CommandTransactional#do_execute()
	 */
	protected function do_execute() {
		$ret = new Status();
		/* @var $notification DAONotifications */
		$notification = $this->get_instance();
		if ($notification->is_active()) {
			$update_params = array(
				'read_through' => Notifications::READ_UNKNOWN,
				'read_action' => new DBNull()
			);
			Arr::clean($update_params, $this->get_params());
			$this->append(CommandsFactory::create_command($notification, 'status', Notifications::STATUS_READ));
			$this->append(CommandsFactory::create_command($notification, 'update', $update_params));
		}
		return $ret;
	}
}
