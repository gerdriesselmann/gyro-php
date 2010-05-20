<?php
/**
 * Select Query Builder for Sphinx
 * 
 * @author Gerd Riesselmann
 * @ingroup Sphinx
 */
class DBSqlBuilderSelectSphinx extends DBSqlBuilderSelect {
	/**
	 * Return SQL fragment
	 *
	 * @return string
	 */
	public function get_sql() {
		$table = $this->query->get_table();
		return serialize(
			array(
				'conditions' => $this->get_where($this->query->get_wheres()),
				'fields' => $this->get_fieldnames($this->fields, $table),
				'from' => $this->get_table($table),
				'limit' => $this->get_limit(Arr::get_item($this->params, 'limit', array(0,0))),
				'order' => $this->get_order_by(Arr::get_item($this->params, 'order_by', array())),
				'features' => $this->get_features($this->query)
			)
		); 
		// 'SELECT%distinct %!fields FROM %!from%join%where%group_by%having%order_by%limit%for_update';
	}
	
	/**
	 * Returns al whers transformed to a string
	 * 
	 * @see core/model/base/DBSqlBuilderBase#get_where($where)
	 */
	protected function get_where(IDBWhere $where) {
		return $where->get_sql();
	}
		
	/**
     * Returns names of fields to select as string
	 */
	protected function get_fieldnames($arr_fields, IDBTable $table) {
		$ret = '';
		$fieldnames = array();
		foreach($arr_fields as $key => $name) {
			$has_alias = !is_numeric($key);
			$fieldname = $has_alias ? $key : $name;
			$fieldalias = $name;

			$statement = $this->prefix_column($fieldname, $table);
			if ($fieldalias != '*') {
				$statement .=  ' AS ' . DB::escape_database_entity($fieldalias, $table->get_table_driver(), IDBDriver::ALIAS);
			}
			
			$fieldnames[] = $statement;
		}
		if (count($fieldnames)) {
			$ret = implode(', ', $fieldnames);
		}
		else {
			$ret = $this->prefix_column('*', $table);
		}
		return $ret;
	}
	
	/**
	 * Return limit as string
	 */
	protected function get_limit($arr_limit) {
		$arr_limit = array_map('intval', $arr_limit);
		$ret = '';
		if ($arr_limit[1] > 0) {
			$ret = $arr_limit[0] . ';' . $arr_limit[1];
		}
		return $ret;
	}

	/**
	 * returns order by clause
	 */
	protected function get_order_by($arr_orders) {
		$ret = '';
		if (count($arr_orders) > 0) {
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
	
	/**
	 * returns features
	 * 
	 * @return array
	 */
	protected function get_features($query) {
		$ret = array();
		if (isset($query->sphinx_features)) {
			$ret = $query->sphinx_features;
		}
		return $ret;
	}
	
	/**
	 * Prefix column with table alias
	 *
	 * @param string $column
	 * @param IDBTable | string $table
	 * @return string
	 */
	protected function prefix_column($column, $table) {
		return DB::escape_database_entity($column, $table->get_table_driver(), IDBDriver::FIELD);
	}	
}
