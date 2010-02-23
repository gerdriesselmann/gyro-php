<?php
/**
 * Generic SQL script execution command
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */  
class ExecuteSqlScriptCommand extends CommandTransactional  {
	/**
	 * Constructor
	 *
	 * @param array|string $sql
	 */
	public function __construct($sql, $connection = DB::DEFAULT_CONNECTION) {
		parent::__construct($connection, $sql);
	}
	
	/**
	 * Executes commands
	 * 
	 * @return Status
	 */
	protected function do_execute() {
		$sqls = Arr::force($this->get_params(), false);
		$ret = new Status();
		foreach($sqls as $sql) {
			$ret->merge(DB::execute_script($sql, $this->get_instance()));
			if ($ret->is_error()) {
				break;
			}
		}
		return $ret;
	}
}
