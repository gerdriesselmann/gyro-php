<?php
/**
 * Delete a Cookie
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */
class CookieDeleteCommand extends CommandBase {
	protected $name;
		
	public function __construct($name) {
		$this->name = $name;
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
		Cookie::delete($this->name);
		return parent::execute();
	}
	
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'cookie.delete';
	}	
}