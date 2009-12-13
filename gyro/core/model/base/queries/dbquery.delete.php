<?php
require_once dirname(__FILE__) . '/dbquery.ordered.cls.php';

/**
 * A delete query
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBQueryDelete extends DBQueryOrdered {
	/**
	 * Return SQL fragment
	 * 
	 * @return string
	 */
	public function get_sql() {
		$params = array();
		$params['fields'] = $this->fields; 
		$params['limit'] = $this->get_limit();
		$params['order_by'] = $this->get_orders();
		$builder = DBSqlBuilderFactory::create_builder(DBSqlBuilderFactory::DELETE, $this, $params);
		return $builder->get_sql();		
	}	
}
