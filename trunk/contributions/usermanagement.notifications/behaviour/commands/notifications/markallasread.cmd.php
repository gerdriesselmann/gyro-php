<?php
/**
 * Mark all notifications of user as read
 */
class MarkallasreadNotificationsCommand extends CommandChain {
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'markallasread';
	}
	
	/**
	 * Returns a description of this command
	 */
	public function get_description() {
		return tr('Mark all as read', 'notifications');
	} 	

	/**
	 * Do it
	 * 
	 * @see core/behaviour/base/CommandTransactional#do_execute()
	 */
	protected function do_execute() {
		$ret = new Status();
		$user = Users::get_current_user();
		if ($user) {
			Load::models('notifications');
			$params = array('read_through' => Notifications::READ_MARK_ALL);
			$notifications = Notifications::create_unread_user_adapter($user->id);
			$notifications->find();
			while($notifications->fetch()) {
				$this->append(CommandsFactory::create_command(clone($notifications), 'markread', $params));
			}
		}
		return $ret;
	}
}
