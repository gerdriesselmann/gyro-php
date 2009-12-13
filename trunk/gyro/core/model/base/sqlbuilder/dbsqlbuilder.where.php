<?php
/**
 * SQL Builder for WHERE clauses
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSqlBuilderWhere implements IDBSqlBuilder {
	/**
	 * @var IDBWhere
	 */
	protected $where = null;
	
	/**
	 * @param IDBWhere $where
	 */
	public function __construct($where, $params = false) {
		$this->where = $where;
	}	
	
	/**
	 * Return SQL fragment
	 * 
	 * @return string
	 */
	public function get_sql() {		
		$operator =  $this->where->get_operator();
		$column = $this->where->get_column();
		if (empty($operator)) {
			return $column;
		}
		
		$table = $this->where->get_table();
		$field = $this->prefix_column($column, $table);
		$value = $this->where->get_value();
		$ret = '';
		switch ($operator) {
			case IDBWhere::OP_IN:
			case IDBWhere::OP_NOT_IN:
				if ($value instanceof DBQuerySelect) {
					$value = $value->get_sql();
				}
				else {
					$arr_formatted_values = array();
					foreach(Arr::force($value) as $orgvalue) {
						$arr_formatted_values[] = DB::format_where($orgvalue, $table, $column);
					}
					$value = implode(', ', $arr_formatted_values);
				}
				$value = '(' . $value . ')';
				break;
			case IDBWhere::OP_IS_NULL:
			case IDBWhere::OP_NOT_NULL:
				$value = '';
				break;
			case IDBWhere::OP_IN_SET:
				$value = DB::format_where($value, $table, $column);
				$value = $value . ' = ' . $value; 
				$operator = '&';
				break;
			case IDBWhere::OP_NOT_IN_SET:
				$value = DB::format_where($value, $table, $column);
				$value = $value . ' = 0'; 
				$operator = '&';
				break;				
			default:
				$value = DB::format_where($value, $table, $column);
				break;
		}
		return '(' . $field . ' ' . $operator . ' ' . $value . ')';
	}

	/**
	 * Prefix column with table name
	 *
	 * @param string $column
	 * @param IDBTable|string $table
	 * @return string
	 */
	protected function prefix_column($column, $table) {
		$ret = $column;
		if (!String::contains($column, '.')) {
			if ($table instanceof IDBTable) {
				$ret = DB::escape_database_entity($column, $table->get_table_driver()); 
				if ($table->get_table_field($column)) {
					$ret = $table->get_table_alias_escaped() . '.' . $ret;
				}
			}
			else {
				$ret = DB::escape_database_entity($column);
			}
		}
		return $ret;
	}	
}