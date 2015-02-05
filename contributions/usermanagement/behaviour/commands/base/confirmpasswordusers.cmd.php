<?php
/**
 * Confirm assword change of user
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class ConfirmPasswordUsersBaseCommand extends CommandChain {
	/**
	 * Execute this command
	 */
	protected function do_execute() {
		$ret = new Status(); 
		$user = $this->get_instance();
		$pwd = $this->get_params();

		$params = array(
			'emailstatus' => Users::EMAIL_STATUS_CONFIRMED,
			'emailconfirmationdate' => time()
		);
		if ($pwd) {
			$params['password'] = $pwd;
		}

        // We do a direct update, and bypass the UpdateUsersCommand, since
        // it does not allow for update of password
		Load::commands('generics/update');
		$this->append(new UpdateCommand($user, $params));
		$this->append(CommandsFactory::create_command($user, 'invalidatepermanentlogins', null));

		return $ret;
	}	
} 
