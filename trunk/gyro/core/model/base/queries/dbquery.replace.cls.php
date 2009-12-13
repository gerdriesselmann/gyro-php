<?php
/**
 * A Replace Query
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBQueryReplace extends DBQueryInsert {
	/**
	 * Return SQL fragment
	 * 
	 * @return string
	 */
	public function get_sql() {
		$params['fields'] = $this->fields; 
		$builder = DBSqlBuilderFactory::create_builder(DBSqlBuilderFactory::REPLACE, $this, $params);
		return $builder->get_sql();		
	}
}