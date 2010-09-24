<?php
/**
 * Build an insert query
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSqlBuilderInsert extends DBSqlBuilderBase {
	protected function get_sql_template() {		
		return 'INSERT%delayed%ignore INTO %!table (%fields) %!values_or_select';		
	}
	
	protected function get_substitutes() {
		$value_or_select = $this->get_select(Arr::get_item($this->params, 'select', null));
		if (empty($value_or_select)) {
			$value_or_select = $this->get_values($this->fields, $this->query->get_table());
		}
		$ret = array(
			'%fields' => $this->get_fieldnames($this->fields, $this->query->get_table()),
			'%!table' => $this->get_table($this->query->get_table()),
			'%!values_or_select' => $value_or_select,
			'%delayed' => $this->get_feature_sql($this->params, 'delayed', 'DELAYED'),
			'%ignore' => $this->get_feature_sql($this->params, 'ignore', 'IGNORE'),
		);	
		return $ret;
	}
	
	protected function get_fieldnames($arr_fields, IDBTable $table) {
		$conn = $table->get_table_driver();
		$ret = array();
		foreach($arr_fields as $key => $field) {
			if (!is_numeric($key)) {
				// FOrm array('col1' => val1, 'col2' => val2): Used with INSERT VALUES
				$ret[] = DB::escape_database_entity($key, $conn, IDBDriver::FIELD);
			}
			else {
				// Form array("col1", "col2", ...): Used with INSERT SELECT only
				$ret[] = DB::escape_database_entity($field, $conn, IDBDriver::FIELD); 
			}
		}
		return implode(', ', $ret);
	}

	protected function get_values($arr_fields, IDBTable $table) {
		$values = array();
		foreach($arr_fields as $column => $value) {
			$values[] = DB::format($value, $table, $column);			
		}
		$values = 'VALUES (' . implode(', ', $values) . ')';
		return $values;
	}

	protected function get_select($query) {
		if ($query) {
			return $query->get_sql();
		}
		return '';
	}
	
	protected function get_table(IDBTable $table) {
		return $table->get_table_name_escaped();
	}
}
