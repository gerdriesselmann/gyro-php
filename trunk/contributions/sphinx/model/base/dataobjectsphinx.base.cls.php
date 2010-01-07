<?php
/**
 * A DataObject class for Sphinx indexes
 * 
 * @author Gerd Riesselmann
 * @ingroup Sphinx
 */
class DataObjectSphinxBase extends DataObjectBase {
	/**
	 * Used to keep query for all fields
	 * 
	 * @var string
	 */
	public $sphinx_all_fields = '';
	
	/**
	 * Configure a select query
	 *
	 * @param DBQuerySelect $query
	 * @param int $policy
	 */
	protected function configure_select_query($query, $policy) {
    	parent::configure_select_query($query, $policy);
    	if (!empty($this->sphinx_all_fields)) {
			$query->add_where('*', '=', $this->sphinx_all_fields);    		
    	}
	}	
	
	// ----------------------------------------------
	// Sphinx specific functions 
	// ----------------------------------------------
	
	/**
	 * Reindex
	 * 
	 * @return Status
	 */
	public function index_rotate() {
		Load::commands('sphinx/index.rotate');
		$cmd = new SphinxIndexRotateCommand($this);
		return $cmd->execute(); 	
	}
}