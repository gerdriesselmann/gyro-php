<?php
/**
 * Send a notification mail, if necessary
 */
class MailNotificationsCommand extends CommandChain {
	protected function do_execute() {
		$ret = new Status();
		$n = $this->get_instance();
		Load::models('notificationssettings');
		$settings = NotificationsSettings::get_for_user($n->id_user);
		if ($settings === false || $settings->source_matches($n->source, NotificationsSettings::TYPE_MAIL)) {
			$this->append($this->create_mail_command($n));
			$n->add_sent_as(Notifications::DELIVER_MAIL);
			$this->append(CommandsFactory::create_command($n, 'update', array()));
		}
		return $ret;
	}
	
	protected function create_mail_command($notification) {
		Load::commands('generics/mail');
		$templates = array(
			'notifications/mail/single_' . strtolower($notification->source),
			'notifications/mail/single'
		);
		$cmd = new MailCommand(
			$notification->get_title(),
			$notification->get_user()->email,
			$templates,
			array(
				'notification' => $notification
			)
		);	
		return $cmd;
	}
}