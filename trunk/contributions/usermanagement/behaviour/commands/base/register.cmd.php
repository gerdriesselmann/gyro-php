<?php
/**
 * Created on 13.03.2007
 *
 * @author Gerd Riesselmann
 */

class RegisterUsersBaseCommand extends CommandChain {		
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'register';
	}
	
	/**
	 * Execute this command
	 */
	protected function do_execute() {
		$ret = new Status();
		
		$create_cmd = CommandsFactory::create_command('users', 'create', $this->get_params());
		$ret->merge($create_cmd->execute());
		if ($ret->is_ok()) {
			$user = $create_cmd->get_result();
			// Create Double OptIn
			$params_double_opt_in = array(
				'id_item' => $user->id,
				'expirationdate' => time() + 7 * 24 * 60 * 60, // today + 7 days
				'action' => 'createaccount'
			); 			
			$this->append(CommandsFactory::create_command('confirmations', 'create', $params_double_opt_in));
		}
		return $ret;
	}	
} 
