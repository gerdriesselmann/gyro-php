<?php
/**
 * Where Clause Query Builder for Sphinx
 * 
 * @author Gerd Riesselmann
 * @ingroup Sphinx
 */
class DBSqlBuilderWhereSphinx extends DBSqlBuilderWhere {
	/**
	 * Return SQL fragment
	 * 
	 * @return array Array with two members "filter" and "query"
	 */
	public function get_sql() {		
		$operator =  $this->where->get_operator();
		$column = $this->where->get_column();
		/* @var $table DBTable */
		$value = $this->where->get_value();
		$table = $this->where->get_table();
		$dbfield = $table->get_table_field($column);
		
		$ret = array('filter' => array(), 'query' => '');
		if ($dbfield && $dbfield->has_policy(DBDriverSphinx::SPHINX_ATTRIBUTE)) {
			// Attributes must be filtered 
			$ret['filter'][] = $this->process_as_filter($column, $operator, $value, $table);
		}
		else {
			$ret['query'] = $this->process_as_query($column, $operator, $value, $table);
		}
		return $ret;
	}

	/**
	 * Create a query from WHERE
	 * 
	 * @param string $column
	 * @param string $operator
	 * @param mixed $value
	 * @param IDTable $table
	 */
	protected function process_as_query($column, $operator, $value, $table) {
		if (empty($operator)) {
			return $column;
		}
		
		/* @var $table DBTable */
		$field = $this->prefix_column($column, $table);
		switch ($operator) {
			case '=':
				$value = DB::escape($value, $table->get_table_driver());
				break;
			default:
				throw new Exception('Only = operator supported with sphinx queries at this time');
				break;
		}
		$ret = $field . ' ' . $value;
		// There is a bug in Sphinx 0.9.9 that if query ends on some escaped characters, query fails.. 
		// strip them off
		while (substr($ret, -2, -1) === "\\") {
			$str_failures = '-!()|@~"/^&';
			if (strpos($str_failures, substr($ret, -1)) !== false) {
				$ret = substr($ret, 0, -2);
			}
			else {
				break;
			}
		}
		return $ret;
	}
	
	/**
	 * Create a filter from WHERE
	 * 
	 * @param string $column
	 * @param string $operator
	 * @param mixed $value
	 * @param IDTable $table
	 */
	protected function process_as_filter($columm, $operator, $value) {
		$exclude = false;
		$values = Arr::force($value, false);
		switch ($operator) {
			case '=':
			case DBWhere::OP_IN:
				$exclude = false;
				break;
			case '!=':
			case '<>':
			case DBWhere::OP_NOT_IN:
				$exclude = true;
				break;
			default:
				throw new Exception('Only =, != and IN, NOT IN operator supported with sphinx attributes at this time');
				break;
		}
		return array(
			'attribute' => $columm,
			'exclude' => $exclude,
			'values' => $values
		);
	}
	
	/**
	 * Prefix column with table alias
	 *
	 * @param string $column
	 * @param IDBTable | string $table
	 * @return string
	 */
	protected function prefix_column($column, $table) {
		return '@' . DB::escape_database_entity($column, $table->get_table_driver(), IDBDriver::FIELD);
	}
}
