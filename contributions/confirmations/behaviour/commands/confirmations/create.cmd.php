<?php
/**
 * Create a confirmation
 *
 * @author Gerd Riesselmann
 * @ingroup Confirmations
 */
 
class CreateConfirmationsCommand extends CommandChain {
	/**
	 * Execute this command
	 */
	protected function do_execute() {
		$ret = new Status();

		$params = $this->get_params();
		$params['code'] = Common::create_token(); // Tokens must not be unique!
		$params['expirationdate'] = Arr::get_item($params, 'expirationdate', time() + GyroDate::ONE_DAY); // 24 hours default expiration
		
		Load::commands('generics/create');
		$cmd = new CreateCommand('confirmations', $params);
		$ret->merge($cmd->execute());
		$this->set_result($cmd->get_result());
		
		if ($ret->is_ok()) {
            /** @var DAOConfirmations $confirmation */
            $confirmation = $this->get_result();
			$handler = $confirmation->create_handler();
			$ret->merge($handler->created());
		}
		
		return $ret;
	}	
} 
