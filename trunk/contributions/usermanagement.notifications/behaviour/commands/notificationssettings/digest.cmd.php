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
		$dao = NotificationsSettings::create_digest_adapter($settings);
		if ($dao->count()) {
			$cmd = new MailCommand(
				tr('Your %appname Notifications', 'notifications', array('%appname' => Config::get_value(Config::TITLE))),
				$user->email,
				'notifications/mail/digest',
				array(
					'notifications' => $dao->execute(),
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