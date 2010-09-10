<?php
/**
 * Command to invoke a callback
 * 
 * This commands calls the given callback via call_user_func_array. That is: 
 * $args passed in passed in constructor must be an ampty or asssociative
 * array.
 * 
 * The function called must return a Status or a string.
 * 
 * Example:
 * 
 * @code
 * Load::commands('generics/callback');
 * $cmd = new CallbackCommand('fancy_func', array('left' => 100, 'right' => 50));
 * $status = $cmd->execute();
 * 
 * ...
 * 
 * // Callback: Should return Status or anything Status->merge() accepts
 * function fancy_func($left, $right) { ... } 
 * @endcode
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