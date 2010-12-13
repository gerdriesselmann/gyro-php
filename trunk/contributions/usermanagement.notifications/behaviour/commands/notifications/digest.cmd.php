<?php
/**
 * Send a digest based upon settings
 * 
 * NOT transactional! Since Sending a Mail cannot be undone
 */
class DigestNotificationsCommand extends CommandBase {
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
		Load::models('notificationssettings');
		$ret = new Status();
		
		$possible_digests = NotificationsSettings::create_possible_digest_adapter();
		$possible_digests->find();
		while($possible_digests->fetch()) {
			// Calls notificationssettings/digest
			$cmd = CommandsFactory::create_command(clone($possible_digests), 'digest', false);
			$ret->merge($cmd->execute());
		}
		
		return $ret;
	}
} 