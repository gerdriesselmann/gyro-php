<?php
/**
 * An Insert Query
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBQueryInsert extends DBQuery {
	/**
	 * Delayed Insert Policy
	 */
	const DELAYED = 1;
	/**
	 * Ignore Errors Policy
	 */
	const IGNORE = 2;
	/**
	 * An optional Select query  
	 *
	 * @var DBQuerySelect
	 */
	protected $select_query = null;
	
	/**
	 * Set optional select query
	 *
	 * @param DBQuerySelect $query
	 */
	public function set_select(DBQuerySelect $query) {
		$this->select_query = $query; 
	}
	
	/**
	 * Return select query
	 *
	 * @return DBQuerySelect
	 */
	public function get_select() {
		return $this->select_query;
	}
	
	/**
	 * Return SQL fragment
	 * 
	 * @return string
	 */
	public function get_sql() {
		$params = array();
		if ($this->policy & self::DELAYED) {
			$params['delayed'] = true;
		}
		if ($this->policy & self::IGNORE) {
			$params['ignore'] = true;
		}
		if (!empty($this->select_query)) {
			$params['select'] = $this->select_query;
		}
		$params['fields'] = $this->fields; 
		$builder = DBSqlBuilderFactory::create_builder(DBSqlBuilderFactory::INSERT, $this, $params);
		return $builder->get_sql();		
	}	
}