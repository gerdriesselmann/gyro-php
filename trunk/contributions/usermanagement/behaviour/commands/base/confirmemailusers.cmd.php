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
		
		// Chain next commands
		Load::commands('generics/update');
		$this->append(new UpdateCommand($user, $params));

		return $ret;
	}	
} 
