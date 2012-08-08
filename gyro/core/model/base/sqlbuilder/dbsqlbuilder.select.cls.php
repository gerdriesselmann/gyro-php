<?php
/**
 * Build a select query
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSqlBuilderSelect extends DBSqlBuilderBase {
	protected function get_sql_template() {
		return 'SELECT%distinct %!fields FROM %!from%join%where%group_by%having%order_by%limit%for_update';
	}

	protected function get_substitutes() {
		$table = $this->query->get_table();
		$ret = array(
			'%!fields' => $this->get_fieldnames($this->fields, $table),
			'%!from' => $this->get_table_and_alias($table),
			'%where' => $this->get_where($this->query->get_wheres()),
			'%join' => $this->get_join($this->query->get_subqueries()),
			'%distinct' => $this->get_feature_sql($this->params, 'distinct', 'DISTINCT'),
			'%for_update' => $this->get_feature_sql($this->params, 'for_update', 'FOR UPDATE'),
			'%group_by' => $this->get_group_by(Arr::get_item($this->params, 'group_by', array())),
			'%having' => $this->get_having($this->query->get_havings()),
			'%limit' => $this->get_limit(Arr::get_item($this->params, 'limit', array(0,0))),
			'%order_by' => $this->get_order_by(Arr::get_item($this->params, 'order_by', array()))
		);
		return $ret;
	}

	protected function get_fieldnames($arr_fields, IDBTable $table) {
		$ret = '';
		$connection = $table->get_table_driver();
		$fieldnames = array();
		if (count($arr_fields) == 0) {
			$arr_fields = array_keys($table->get_table_fields());
		}
		foreach($arr_fields as $key => $name) {
			$has_alias = !is_numeric($key);
			$fieldname = $has_alias ? $key : $name;
			$fieldalias = $name; //String::plain_ascii($name, '', true);

			$dbfield = $table->get_table_field($fieldname);
			$statement = ($dbfield) ? $dbfield->format_select() : $fieldname;
			$statement = str_replace($fieldname, $this->prefix_column($fieldname, $table), $statement);
			if (substr($fieldalias, -1) != '*') {
				$statement .=  ' AS ' . DB::escape_database_entity($fieldalias, $connection, IDBDriver::ALIAS);
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

	protected function get_join($arr_subqueries) {
		$ret = '';
		foreach($arr_subqueries as $subquery) {
			$ret .= $this->get_single_join($subquery);
		}
		return $ret;
	}

	protected function get_single_join(DBQueryJoined $joined_query) {
		$ret = ' ';
		switch ($joined_query->get_join_type()) {
			case DBQueryJoined::LEFT:
				$ret .= 'LEFT JOIN';
				break;
			case DBQueryJoined::RIGHT:
				$ret .= 'RIGHT JOIN';
				break;
			default:
				$ret .= 'INNER JOIN';
				break;
		}
		$ret .= ' ';
		$ret .= $this->get_table_and_alias($joined_query->get_table());
		$ret .= ' ON ';
		$ret .= $joined_query->get_join_conditions()->get_sql();

		// Recursive..
		$ret .= $this->get_join($joined_query->get_subqueries());

		return $ret;
	}

	protected function get_group_by($arr_group_by) {
		$ret = '';
		if (count($arr_group_by) > 0) {
			$ret .= ' GROUP BY ';
			$items = array();
			foreach($arr_group_by as $group_by) {
				$column = Arr::get_item($group_by, 'field', '');
				if (empty($column)) {
					continue;
				}
				$table =  Arr::get_item($group_by, 'table', null);
				$items[] = $column;
			}
			$ret .= implode(', ', $items);
		}
		return $ret;
	}

	protected function get_having(IDBWhere $having) {
		$ret = $having->get_sql();
		if (!empty($ret)) {
			$ret = ' HAVING ' . $ret;
		}
		return $ret;
	}
	
}
