<?php
require_once dirname(__FILE__) . '/commandbase.cls.php';
 
/**
 * The command chain executes commands that form a hierarchy
 * 
 * Every command you append is put at the end of the chain 
 *
 * This command wraps a database transaction around the execution of all commands
 * 
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */
class CommandChain extends CommandBase {
	/**
	 * Next command in chain
	 *
	 * @var CommandChain
	 */
	protected $next = null;
	/**
	 * Previous command in chain
	 *
	 * @var CommandChain
	 */
	protected $prev = null;
	
	/**
	 * Returns TRUE, if the command can be executed by given user
	 */
	public function can_execute($user) {
		$ret = $this->do_can_execute($user);
		if ($ret) {
			$next = $this->get_next();
			if ($next) {
				$ret = $next->can_execute($user);
			}
		}
		return $ret;
	}
	
	/**
	 * Executes commands
	 * 
	 * @return Status
	 */
	public function execute() {
		$ret = new Status();
		
		DB::start_trans();
		try { 
			$ret = $this->do_execute();
			if ($ret->is_ok()) {
				$next = $this->get_next();
				if ($next) {
					$ret = $next->execute();
				}
			}
		}
		catch (Exception $ex) {
			$ret->merge($ex);
		}
		
		if ($ret->is_ok()) {
			$ret->merge(parent::execute());
		}
		
		if ($ret->is_error()) {
			$this->undo();
		}
		DB::end_trans($ret);		

		return $ret;
	}
	
	/**
	 * Revert execution
	 */
	public function undo() {
		$this->do_undo();
		$prev = $this->get_prev();
		if ($prev) {
			$prev->undo();
		}
	}
	
	/**
	 * Return next command
	 */
	protected function get_next() {
		return $this->next;
	}

	/**
	 * Return previous command
	 */
	protected function get_prev() {
		return $this->prev;
	}
		
	/**
	 * Set the next command in chain
	 */
	public function append($cmd) {
		if ($this->next) {
			$this->next->append($cmd);
		}
		else {
			if ($cmd instanceof ICommand) {
				if ($cmd instanceof CommandChain) {
					$this->next = $cmd;
					$cmd->set_prev($this);
				}
				else {
					$adapter = new CommandChainAdapter($cmd);
					$this->append($adapter);
				}
			}
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
	 * Set previous command
	 */
	protected function set_prev($cmd) {
		if ($cmd instanceof ICommand) {
			$this->prev = $cmd;
		}		
	}
	
	/**
	 * Does execution checking
	 */ 
	protected function do_can_execute($user) {
		return parent::can_execute($user);
	}
	
	/**
	 * Does executing
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

/**
 * Adapt any command to be chainable
 */
class CommandChainAdapter extends CommandChain {
	/**
	 * The command to adapt
	 *
	 * @var ICommand
	 */
	protected $cmd = null;

	/**
	 * Constructor
	 * 
	 * @param ICommand Command to adapt
	 */
	public function __construct($cmd) {
		if ($cmd instanceof ICommand) {
			$this->cmd = $cmd;
		}
	}
	
	/**
	 * Does execution checking
	 */ 
	protected function do_can_execute($user) {
		if ($this->cmd) {
			return $this->cmd->can_execute($user);
		}
		return parent::do_can_execute($user);
	}
	
	/**
	 * Does executing
	 */
	protected function do_execute() {
		if ($this->cmd) {
			return $this->cmd->execute();
		}
		return parent::do_execute();
	} 
	
	/**
	 * Does undo
	 */
	protected function do_undo() {
		if ($this->cmd) {
			return $this->cmd->undo(); 
		}
		return parent::do_undo();
	}
}
?>