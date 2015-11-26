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
	protected $http_only;
	protected $ssl_only;

	public function __construct($name, $data, $duration, $http_only = false, $ssl_only = false) {
		parent::__construct(null, false);
		$this->name = $name;
		$this->data = $data;
		$this->duration = $duration;
		$this->http_only = $http_only;
		$this->ssl_only = $ssl_only;
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
		Cookie::create($this->name, $this->data, $this->duration, '/', $this->http_only, false, $this->ssl_only);
		return parent::execute();
	}
	
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'cookie.set';
	}	
}