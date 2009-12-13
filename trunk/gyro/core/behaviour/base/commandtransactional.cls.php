<?php
require_once dirname(__FILE__) . '/commandbase.cls.php';
 
/**
 * The Transaction Command starts a database transaction before execution
 * 
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */
class CommandTransactional extends CommandBase {
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
	 * Does executing
	 */
	protected function do_execute() {
		return new Status();
	} 
}