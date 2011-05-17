<?php
Load::commands('generics/status.any');

/**
 * Command to set status
 * 
 * Notifies user of change by sending a mail
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class StatusAnyUsersCommand extends StatusAnyCommand {
 	/**
 	 * Check if command can be executed
 	 *
 	 * @param mixed $user
 	 * @param IStatusHolder $inst
 	 * @param mixed $new_status
 	 * @return bool
 	 */
 	protected function do_can_execute_status($user, IStatusHolder $inst, $new_status) {
 		return $new_status != Users::STATUS_UNCONFIRMED;
 	}
 	
 	/**
 	 * Change status 
 	 * 
 	 * @return Status
 	 */
 	protected function do_execute() {
 		$ret = new Status();
 		$ret->merge(parent::do_execute());
 		if ($ret->is_ok()) { 			
	 		$user = $this->get_instance();
	 		$new_status = tr($this->get_params(), 'users');
	 		//Cas: ist das hier korrekt?
	 		if (Config::get_value(ConfigUsermanagement::MAIL_STATUSCHANGE)) {	 		
	 			Load::commands('generics/mail'); 
	 			$cmd = new MailCommand(
	 				tr('Your account was set to "%status%"', 'users', array('%status%' => $new_status)),
	 				$user->email,
	 				'users/mail/statuschange',
	 				array('new_status' => $new_status) 
	 			);
	 			$this->append($cmd);
	 		}
 		}
 		return $ret;
 	}
}
