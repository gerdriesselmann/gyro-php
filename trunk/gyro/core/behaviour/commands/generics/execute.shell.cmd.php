<?php
/**
 * Run a shell command
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */
class ExecuteShellCommand extends CommandBase {
	/*
	 * Constructor takes command to execute, which can be either a string  or an aray
	 * 
	 * @param string|array $shellcmd
	 */
	public function __construct($shellcmd) {
		parent::__construct(null, $shellcmd);
	}
	
	
	/**
	 * Executes commands
	 * 
	 * @return Status
	 */
	public function execute() {
		$ret = new Status();
		$cmds = Arr::force($this->get_params(), false);
		foreach ($cmds as $cmd) {
			$ret->merge($this->invoke($cmd));
			if ($ret->is_error()) {
				break;
			}
		}
		return $ret;
	} 

	/**
	 * Run shell with given command
	 * 
	 * @param string $call
	 * @return Status
	 */
	protected function invoke($call) {
		$ret = new Status();
		$output = array();
		$result = 0;
		$call = escapeshellcmd($call);
		exec($call, $output, $result);
		if ($result > 0) {
			if (count($output) > 0) {
				foreach($output as $err_line) {
					$ret->append($err_line);
				}
			}
			else {
				$ret->append(tr('Error while invoking %call: %e', 'core', array('%call' => $call, '%e' => $result)));
			}
		}
		return $ret;
	}
	
}