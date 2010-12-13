<?php
Load::commands('generics/create');

/**
 * Create a notifcation and send a mail, if necessary
 */
class CreateNotificationsCommand extends CreateCommand {
	protected function do_execute() {
		$ret = new Status();
		Load::models('notifications');
		$existing = Notifications::existing($this->get_params()); 
		if (!$existing) {
			$ret->merge(parent::do_execute());
			if ($ret->is_ok()) {
				$n = $this->get_result();
				$cmd = CommandsFactory::create_command($n, 'mail', false);
				$ret->merge($cmd->execute());
			}
		}
		else {
			$this->set_result($existing);
		}
		return $ret;
	}
}
