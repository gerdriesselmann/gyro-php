<?php
require_once dirname(__FILE__) . '/commandbase.cls.php';
 
/**
 * The command delegate delegates all request to another command
 * 
 * Use the command to wrap another
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */
class CommandDelegate implements ICommand {
	/**
	 * Commadn to delegate to
	 *
	 * @var ICommand
	 */
	protected $delegate;

	public function __construct($delegate) {
		$this->delegate = $delegate;
	}

	protected function check_delegate() {
		if (empty($this->delegate)) {
			throw new Exception('No delegate set for delegation command ' . get_class($this));
		}
	}
	
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		$this->check_delegate();
		return $this->delegate->get_name();
	}
	
	/**
	 * Returns a description of this command
	 */
	public function get_description() {
		$this->check_delegate();
		return $this->delegate->get_description();
	} 
		
	/**
	 * Returns the object this actionworks upon
	 *
	 * @return mixed
	 */
	public function get_instance() {
		$this->check_delegate();
		return $this->delegate->get_instance();
	}

	/**
	 * Returns params
	 *
	 * @return mixed
	 */
	public function get_params() {
		$this->check_delegate();
		return $this->delegate->get_params();
	}	

	/**
	 * Returns success message for this command
	 */
	public function get_success_message() {
		$this->check_delegate();
		return $this->delegate->get_success_message();
	}		

	/**
	 * Return result of command
	 * 
	 * @return mixed
	 */
	public function get_result() {
		$this->check_delegate();
		return $this->delegate->get_result();
	}		

	/**
	 * Returns a name that has parameters build in  
	 *
	 * @return string
	 */
	public function get_name_serialized() {
		$this->check_delegate();
		return $this->delegate->get_name_serialized();
	}	
	
	/**
	 * Make this command available for text processing systems (that is: the HTML code)
	 */
	public function serialize() {
		$this->check_delegate();
		return $this->delegate->serialize();
	}	
	
	public function can_execute($user) {
		$this->check_delegate();
		return $this->delegate->can_execute($user);
	}
	
	public function execute() {
		$this->check_delegate();
		return $this->delegate->execute();
	}
	
	public function undo() {
		$this->check_delegate();
		$this->delegate->undo();
	}	
}
