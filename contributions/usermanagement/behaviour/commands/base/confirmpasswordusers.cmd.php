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
		
		// Chain next commands
		Load::commands('generics/update');
		$this->append(new UpdateCommand($user, $params));

		return $ret;
	}	
} 
