<?php
/**
 * Sets a Cookie
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */
class CookieSetCommand extends CommandBase {
	protected $name;
	protected $data;
	protected $duration;
		
	public function __construct($name, $data, $duration) {
		$this->name = $name;
		$this->data = $data;
		$this->duration = $duration;
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
		Cookie::create($this->name, $this->data, $this->duration);
		return parent::execute();
	}
	
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'cookie.set';
	}	
}