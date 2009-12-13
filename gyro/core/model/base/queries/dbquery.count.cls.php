<?php
require_once dirname(__FILE__) . '/dbquery.select.cls.php';

/**
 * A query counting results insteead of returning them
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBQueryCount extends DBQuerySelect {
	public function __construct($table) {
		parent::__construct($table, self::NORMAL);
	}

	/**
	 * Create SQL builder
	 * 
	 * @param array $params
	 * @return IDBSqlBuilder
	 */
	protected function create_sql_builder($params) {
		$builder = DBSqlBuilderFactory::create_builder(DBSqlBuilderFactory::COUNT, $this, $params);
		return $builder;
	}	
}