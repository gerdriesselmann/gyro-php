<?php
require_once dirname(__FILE__) . '/commandbase.cls.php';
 
/**
 * The comnmand composite executes a set of commands
 * 
 * Use the command to exeucute a set of independend commands
 * 
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */
class CommandComposite extends CommandBase {
	protected $commands = array();
	
	/**
	 * Append a command to the chain 
	 */
	public function append($command) {
		if ($command instanceof ICommand) {
			$this->commands[] = $command;
		}
	}

	/**
	 * Append a command array to the chain 
	 */
	public function append_array($commands) {
		if (is_array($commands)) {
			foreach($commands as $command) {
				$this->append($command);
			}
		}
	}


	/**
	 * Returns TRUE, if all commands in the chain can be executed by given user
	 */
	public function can_execute($user) {
		$ret = $this->do_can_execute($user);
		if ($ret) {
			foreach($this->commands as $command) {
				if (!$command->can_execute($user)) {
					return false;
				}			
			}
		}
		return $ret;
	}
	
	/**
	 * Execute all commands (until one fails..)
	 */
	public function execute() {
		$ret = new Status();
		$arr_executed = array();
		DB::start_trans();
		try { 
			$ret = $this->do_execute(); 
			if ($ret->is_ok()) {
				foreach($this->commands as $command) {
					$ret->merge($command->execute());
					if ($ret->is_error()) {
						// Ups, something went wrong
						break;
					}
					$arr_executed[] = $command;
				}
			}
		}
		catch (Exception $ex) {
			$ret->merge($ex);
		}
		
		if ($ret->is_error()) {
			$this->undo_on_chain($arr_executed);
			$this->undo();
		}
		DB::end_trans($ret);
		return $ret;
	}
	
	/**
	 * Undo all commands in chain
	 */
	public function undo() {
		$this->do_undo();
		$this->undo_on_chain($this->commands);
	}
	
	/** 
	 * Undo comnmands passed
	 */
	protected function undo_on_chain($commands) {
		// Undoing is done reverse, that is last command first.
		$cnt = count($commands);
		for ($i = $cnt - 1; $i >= 0; $i--) {
			$commands[$i]->undo();
		}	
	}
	
	/**
	 * Does execution checking
	 * 
	 * @return bool
	 */ 
	protected function do_can_execute($user) {
		return parent::can_execute($user);
	}
	
	/**
	 * Does executing
	 * 
	 * @return Status
	 */
	protected function do_execute() {
		return parent::execute();
	} 
	
	/**
	 * Does undo
	 */
	protected function do_undo() {
		parent::undo();
	}	
}
?>