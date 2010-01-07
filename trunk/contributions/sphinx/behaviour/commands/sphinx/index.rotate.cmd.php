<?php
/**
 * Rotates (reindex) given index 
 * 
 * @author Gerd Riesselmann
 * @ingroup Sphinx
 */
class SphinxIndexRotateCommand extends CommandBase {
	/**
	 * Executes commands
	 * 
	 * @return Status
	 */
	public function execute() {
		/* @var $index DataObjectSphinx */
		$index = $this->get_instance();
		$index_name = $index->get_table_driver()->get_db_name() . $index->get_table_name();
		$call = APP_SPHINX_INDEXER_INVOKE . ' --quiet --rotate ' . $index_name;
		Load::commands('generics/execute.shell');
		$cmd = new ExecuteShellCommand($call);
		return $cmd->execute();
	}	
}