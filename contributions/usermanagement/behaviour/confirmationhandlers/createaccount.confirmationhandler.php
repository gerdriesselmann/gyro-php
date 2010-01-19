<?php
/**
 * Confirm account creation
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class CreateaccountConfirmationHandler extends ConfirmationHandlerBase {
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
			if ($user) {
				$cmd = CommandsFactory::create_command($user, 'status', Users::STATUS_ACTIVE);
				$ret = $cmd->execute();
				if ($ret->is_ok()) {
					return new Message(tr('Your registration has been confirmed', 'users'));
				}
				else {
					return $ret;
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
				tr('Your registration', 'users'),
				$user->email,
				'users/mail/register',
				array(
					'confirmation' => $confirmation,
					'user' => $user
				)
			);
			$ret->merge($cmd->execute());
		}
		else {
			$ret->append(tr('Unkown User set on register confirmation', 'users'));	
		}
		
		return $ret;
	}		
}
