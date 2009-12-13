<?php
/**
 * Build an update query
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSqlBuilderUpdate extends DBSqlBuilderBase {
	protected function get_sql_template() {		
		return 'UPDATE%ignore %!table SET %!fields_values%where%order_by%limit';		
	}
	
	protected function get_substitutes() {
		$ret = array(
			'%!fields_values' => $this->get_fields_values($this->fields, $this->query->get_table()),
			'%!table' => $this->get_table_and_alias($this->query->get_table()),
			'%where' => $this->get_where($this->query->get_wheres()),
			'%ignore' => $this->get_feature_sql($this->params, 'ignore', 'IGNORE'),
			'%limit' => $this->get_limit(Arr::get_item($this->params, 'limit', array(0,0))),
			'%order_by' => $this->get_order_by(Arr::get_item($this->params, 'order_by', array()))
		);	
		return $ret;
	}
	
	protected function get_fields_values($arr_fields, IDBTable $table) {
		$fields = array();
		foreach($arr_fields as $column => $value) {
			$fieldname = $this->prefix_column($column, $table);
			$fields[$fieldname] = DB::format($value, $table, $column);;
		}
		return Arr::implode(', ', $fields, ' = ');
	}

	
	protected function get_limit($arr_limit) {
		$arr_limit = array_map('intval', $arr_limit);
		$ret = '';
		if ($arr_limit[1] > 0) {
			$ret = ' LIMIT ' . $arr_limit[1];
		}
		return $ret;
	}
	
}
