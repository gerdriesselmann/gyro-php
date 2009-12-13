<?php
require_once dirname(__FILE__) . '/dbquery.ordered.cls.php';

/**
 * An Update Query
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBQueryUpdate extends DBQueryOrdered {
	/**
	 * Ignore Errors Policy
	 */
	const IGNORE = 2;
	
	/**
	 * Return SQL fragment
	 * 
	 * @return string
	 */
	public function get_sql() {
		$params = array();
		if ($this->policy & self::IGNORE) {
			$params['ignore'] = true;
		}
		$params['fields'] = $this->fields; 
		$params['limit'] = $this->get_limit();
		$params['order_by'] = $this->get_orders();
		$builder = DBSqlBuilderFactory::create_builder(DBSqlBuilderFactory::UPDATE, $this, $params);
		return $builder->get_sql();		
	}	
}