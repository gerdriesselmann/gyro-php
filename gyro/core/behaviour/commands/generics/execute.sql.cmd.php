<?php
/**
 * Generic SQL execution command
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */  
class ExecuteSqlCommand extends CommandTransactional  {
	/**
	 * Constructor
	 *
	 * @param array|string $sql
	 */
	public function __construct($sql, $connection = DB::DEFAULT_CONNNECTION) {
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
			$ret->merge(DB::execute($sql, $this->get_instance()));
			if ($ret->is_error()) {
				break;
			}
		}
		return $ret;
	}
}
