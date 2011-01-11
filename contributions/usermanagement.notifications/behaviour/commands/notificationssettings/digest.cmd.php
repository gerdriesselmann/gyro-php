<?php
Load::commands('generics/mail');

/**
 * Send a digest based upon settings
 */
class DigestNotificationssettingsCommand extends CommandBase {
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'digest';
	}
	
	/**
	 * Aktually do something :)
	 */
	public function execute() {
		$ret = parent::execute();
		
		/* @var $settings DAONotificationssettings */
		$settings = $this->get_instance();
		$user = $settings->get_user();
		$nots = array();
		$dao = NotificationsSettings::create_digest_adapter($settings);
		$dao->find();
		while($dao->fetch()) {
			if ($settings->should_notification_be_processed($dao, NotificationsSettings::TYPE_DIGEST)) {
				$n = clone($dao);
				$nots[] = $n;
				$n->add_sent_as(Notifications::DELIVER_DIGEST);
				$cmd = CommandsFactory::create_command($n, 'update', array());
				$cmd->execute();
			}
		}
		if (count($nots)) {
			$cmd = new MailCommand(
				tr('Your %appname Notifications', 'notifications', array('%appname' => Config::get_value(Config::TITLE))),
				$user->email,
				'notifications/mail/digest',
				array(
					'notifications' => $nots,
					'user' => $user,
					'settings' => $settings
				)
			);
			$ret->merge($cmd->execute());
		}
		if ($ret->is_ok()) {
			$settings->digest_last_sent = time();
			$ret->merge($settings->update());			
		}
		
		return $ret;
	}
} 