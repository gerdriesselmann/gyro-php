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
	 * Set Sphinx features, like weights
	 * 
	 * @var array
	 */
	public $sphinx_features = array();
	
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
    	$query->sphinx_features = $this->sphinx_features;
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
	
	/**
	 * Return a count suitable for pagers. 
	 *  
	 * Sphinx will return a limited number of results, usually 1,000, regardless
	 * of the number of matches. This is defined by the configuration setting 
	 * "max_matches" in the server config file.
	 * 
	 * This means limiting a result to - say - items 1,100 to 1,110 will result in 
	 * an error, and due to the Gyro way of dealing with DB error will raise an exception.
	 * 
	 * Therefor when creating a pager instance, use this function rather than the
	 * usual count() function. 
	 *  
	 * @return int
	 */
	public function count_pager() {
		return min($this->count(), APP_SPHINX_MAX_MATCHES);
	}

	/**
	 * Set a sphinx feature
	 */
	public function set_sphinx_feature($name, $value) {
		$this->sphinx_features[$name] = $value;
	}

	/**
	 * Get a sphinx feature
	 * 
	 * @return mixed The feature's value or NULL, if not set
	 */
	public function get_sphinx_feature($name) {
		return Arr::get_item($this->sphinx_features, $name, null);
	}
}