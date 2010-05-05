<?php
Load::commands('generics/create');

/**
 * Create a notifcation and send a mail, if necessary
 */
class CreateNotificationsCommand extends CreateCommand {
	protected function do_execute() {
		$ret = parent::do_execute();
		if ($ret->is_ok()) {
			$n = $this->get_result();
			
			Load::models('notificationssettings');
			$settings = NotificationsSettings::get_for_user($n->id_user);
			if ($settings === false || $settings->source_matches($n->source, NotificationsSettings::TYPE_MAIL)) {
				Load::commands('generics/mail');
				$cmd = new MailCommand(
					$n->get_title(),
					$n->get_user()->email,
					'notifications/mail/single',
					array(
						'notification' => $n
					)
				);
				$cmd->execute();
			}			
		}
		return $ret;
	}
}