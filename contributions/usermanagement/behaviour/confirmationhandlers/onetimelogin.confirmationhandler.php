<?php
/**
 * Confirm onetime login (lost password)
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class OnetimeloginConfirmationHandler extends ConfirmationHandlerBase {
	/**
	 * Template method to be overloaded by subclasses to do what should be done
	 * on successfull confirmation
	 * 
	 * @param DAOConfirmations Data of confirmation, not necessarily up to date, depending on status
	 * @param enum Indicates success or failure
	 *   
	 */
	protected function do_confirm($confirmation, $success) {
		if ($success == self::SUCCESS) {
			$user = Users::get($confirmation->id_item);
			if ($user && $user->is_active()) {
				Users::confirm_email($user);
				if (Users::do_login($user)) {
					$msg = new Message(tr('You have been automatically logged in. You now can change your password.', 'users'));
					$msg->persist();
					$token = Common::create_token('reset password');
					Session::push(Users::LOST_PASSWORD_TOKEN, $token);
					$redirect = ActionMapper::get_url('lost_password_reenter', array('token' => $token));
					Url::create($redirect)->redirect();
					exit;					
				}
				else {
					return new Status(tr('Automatically login in failed', 'users'));
				}
			}
			else {
				return new Status(tr('No matching user account was found', 'users'));
			}					
		}
		else {
			return parent::do_confirm($confirmation, $success);
		}
	}
	
	/**
	 * Template method to be overloaded by subclasses to do what should be done
	 * on creation
	 * 
	 * @param DAOConfirmations Data of confirmation
	 * @return Status
	 *   
	 */
	protected function do_created($confirmation) {
		$ret = new Status();
		
		$user = Users::get($confirmation->id_item);
		if ($user) {
			Load::commands('generics/mail');
			$cmd = new MailCommand(
				tr('One time login', 'users'),
				$user->email,
				'users/mail/onetimelogin',
				array('confirmation' => $confirmation)
			);
			$ret->merge($cmd->execute());
		}
		else {
			$ret->append(tr('Unkown User set on one time login confirmation', 'users'));	
		}
		
		return $ret;
	}		
	
}