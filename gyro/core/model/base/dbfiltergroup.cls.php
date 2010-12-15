<?php
require_once dirname(__FILE__) . '/dbfilter.cls.php';

/**
 * Contains a set of DBFilter instances that form a group.
 * For example, all possible filters on a column can form a group
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBFilterGroup implements IDBQueryModifier {
	/**
	 * An associative array of DBFilter instances 
	 */
	protected $filters = array();
	protected $name = '';
	protected $group_key = ''; 
	protected $key_default_filter = '';
	protected $key_current_filter = '';
	
	/**
	 * Constructor
	 * 
	 * @param array Associative array of DBFilter instances
	 */
	public function __construct($key = '', $name = '', $filters = array(), $default = '') {
		$this->name = $name;
		$this->group_key = $key;
		foreach($filters as $key => $value) {
			$this->add_filter(strval($key), $value);
		}
		$this->set_default_key($default);
		$this->set_current_key($default);
	}
	
	/**
	 * Return name of this filter
	 */
	public function get_name() {
		return $this->name;
	}
	
	/**
	 * Return key of this group
	 */
	public function get_group_id() {
		return $this->group_key;
	}	
	
	/**
	 * Add a new filter
	 * 
	 * @param string The key for this filter
	 * @param DBFilter DBFilter instance 
	 */
	public function add_filter($key, $filter) {
		$filter->set_key($key);
		$this->filters[$key] = $filter;
	}
	
	/**
	 * Get filter for give key
	 */
	public function get_filter($key) {
		return Arr::get_item($this->filters, $key, false);
	}
	
	/**
	 * Returns an array of all filter keys
	 */
	public function get_keys() {
		return array_keys($this->filters);
	}
	
	/**
	 * Returns an array of all filters
	 */
	public function get_filters() {
		return array_values($this->filters);
	}

	/**
	 * Returns number of filters
	 */
	public function count() {
		return count($this->filters);
	}
	
	/**
	 * Set default filter's key
	 */
	public function set_default_key($key) {
		$this->key_default_filter = $key;
		foreach($this->filters as $f) {
			$f->set_is_default($f->get_key() == $key);
		}
	}
	
	/**
	 * Return default filter's key
	 */
	public function get_default_key() {
		return $this->key_default_filter;
	}	

	/**
	 * Set current filter's key
	 */
	public function set_current_key($key) {
		$this->key_current_filter = $key;
	}

	/**
	 * Return current filter's key
	 */
	public function get_current_key() {
		return $this->key_current_filter;
	}
	
	/**
	 * Returns current filter
	 * 
	 * @return DBFilter
	 */
	public function get_current_filter() {
		$ret = $this->get_filter($this->get_current_key());
		if ($ret == false) {
			$ret = $this->get_filter($this->get_default_key());
		}
		return $ret;
	}
	
	/**
	 * Apply filter
	 */
	public function apply($query) {
		$query->apply_modifier($this->get_current_filter());
	}
}