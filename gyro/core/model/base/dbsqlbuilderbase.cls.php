<?php
/**
 * Base class for SQL builders
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSqlBuilderBase implements IDBSqlBuilder {
	protected $params;
	/**
	 * The query to work upon
	 *
	 * @var DBQuery
	 */
	protected $query;
	protected $fields;

	/**
	 * @param DBQuery $query
	 */
	public function __construct($query, $params) {
		$this->query = $query;
		$this->params = $params;
		$this->fields = Arr::get_item($params, 'fields', array());
	}

	/**
	 * Return SQL fragment
	 *
	 * @return string
	 */
	public function get_sql() {
		$template = $this->get_sql_template();
		$vars = $this->get_substitutes();
		return $this->substitute_template($template, $vars);
	}
	
	/**
	 * SUbsitute vars in given template
	 * 
	 * @param string $template
	 * @param array $vars
	 * @return string
	 */
	protected function substitute_template($template, $vars) {
		foreach($vars as $key => $value) {
			$template = str_replace($key, $value, $template);
			if (empty($value) && substr($key, 0, 2) == '%!') {
				throw new Exception(tr('Required SQL substitute %param not set', 'core', array('%param' => $key)));
			}
		}
		return $template;		
	}

	protected function get_sql_template() {
		throw new Exception(tr('Not implemented'));
	}

	protected function get_substitutes() {
		return array();
	}

	protected function get_feature_sql($arr_params, $name, $sql) {
		return Arr::get_item($arr_params, $name, false) ? ' ' . $sql : '';
	}

	protected function get_where(IDBWhere $where) {
		$ret = $where->get_sql();
		if (!empty($ret)) {
			$ret = ' WHERE ' . $ret;
		}
		return $ret;
	}

	protected function get_table_and_alias(IDBTable $table) {
		return $table->get_table_name_escaped() . ' AS ' . $table->get_table_alias_escaped();
	}

	protected function get_table(IDBTable $table) {
		return $table->get_table_name();
	}

	protected function get_order_by($arr_orders) {
		$ret = '';
		if (count($arr_orders) > 0) {
			$ret .= ' ORDER BY ';
			$items = array();
			foreach($arr_orders as $order) {
				$column = Arr::get_item($order, 'field', '');
				if (empty($column)) {
					continue;
				}
				$table =  Arr::get_item($order, 'table', null);
				$direction = Arr::get_item($order, 'direction', 'ASC');
				$items[] = $column. ' ' . $direction;
			}
			$ret .= implode(', ', $items);
		}
		return $ret;
	}

	protected function get_limit($arr_limit) {
		$arr_limit = array_map('intval', $arr_limit);
		$ret = '';
		if ($arr_limit[1] > 0) {
			$ret = ' LIMIT ' . $arr_limit[0] . ',' . $arr_limit[1];
		}
		return $ret;
	}
	
	/**
	 * @return IDBDriver|string
	 */
	protected function get_connection() {
		return $this->query->get_table()->get_table_driver();
	}

	/**
	 * Prefix column with table alias
	 *
	 * @param string $column
	 * @param IDBTable | string $table
	 * @return string
	 */
	protected function prefix_column($column, $table) {
		if ($this->is_non_prefixable_colum($column, $table)) {
			return $column; // Already has prefix
		}

		$is_itable = ($table instanceof IDBTable);
		$connection = ($is_itable) ? $table->get_table_driver() : $this->get_connection();	

		if ($column !== '*') {
			$column = DB::escape_database_entity($column, $connection, IDBDriver::FIELD);
		}
		if ($is_itable) {
			return $table->get_table_alias_escaped() . '.' . $column;
		}

		if (!empty($table)) {
			return DB::escape_database_entity($table, $this->get_connection(), IDBDriver::TABLE) . '.' . $column;
		}

		return '';
	}
	
	/**
	 * Returns true, if coulumn is not prefixable, e.g. becuase it is a function
	 * 
	 * @param string $column
	 * @param IDBTable | string $table
	 * @return bool
	 */
	protected function is_non_prefixable_colum($column, $table) {
		$ret = false;
		if (strpos($column, '.') !== false) {
			$ret = true; // Already prefixed
		}
		else if (strpos($column, '(') !== false) {
			$ret = true; // a fucntion
		}
		else {
			$test = substr($column, 0, 1);
			$ret = ($test === '"' || $test === "'");
		}
		return $ret;
	}
}