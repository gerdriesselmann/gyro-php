<?php
/**
 * Command to invoke a callback
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */
class CallbackCommand extends CommandBase {
	protected $callback;
	protected $args;
		
	public function __construct($callback, $args = array()) {
		$this->callback = $callback;
		$this->args = $args;
	}
	
	// ************************************
	// ICommand
	// ************************************
	
	/**
	 * Executes commands
	 * 
	 * @return Status
	 */
	public function execute() {
		$ret = new Status();
		$callable_name = '';
		if (!is_callable($this->callback, false, $callable_name)) {
			throw new Exception('Callback Command could not call ' . $callable_name . '. Passed callback was: ' . serialize($this->callback)); 
		}
		$result = call_user_func_array($this->callback, $this->args);
		$ret->merge($result);
		return $ret;
	}
	
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'callback';
	}	
	
}