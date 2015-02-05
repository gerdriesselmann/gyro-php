<?php
/**
 * Confirm email of user
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class ConfirmEmailUsersBaseCommand extends CommandChain {
	/**
	 * Execute this command
	 */
	protected function do_execute() {
		$ret = new Status(); 
		$user = $this->get_instance();
		$mail = $this->get_params();

		$params = array(
			'emailstatus' => Users::EMAIL_STATUS_CONFIRMED,
			'emailconfirmationdate' => time()
		);
		if ($mail) {
			$params['email'] = $mail;
		}
		
		// We do a direct update, and bypass the UpdateUsersCommand, since
        // it does not allow for update of email address
		Load::commands('generics/update');
		$this->append(new UpdateCommand($user, $params));
        $this->append(CommandsFactory::create_command($user, 'invalidatepermanentlogins', null));

		return $ret;
	}	
} 
