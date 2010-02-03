<?php
// This is a somewhat complex way of saying "include", due to possible overloading of builders  
Load::classes_in_directory("model/base/sqlbuilder/", "dbsqlbuilder.insert", 'cls');
Load::classes_in_directory("model/drivers/mysql/sqlbuilder/", "dbsqlbuilder.insert.mysql", 'cls');

/**
 * Implementation of Replace for MySQL. 
 * 
 * Does not use REPLACE but INSERT ... ON DUPLICATED KEY UPDATE, since REPLACE means 
 * to DELETE and then INSERT, which is a pain 
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSqlBuilderReplaceMysql extends DBSqlBuilderInsertMysql {
	protected function get_sql_template() {		
		return 'INSERT INTO %!table (%fields) %!values ON DUPLICATE KEY UPDATE %!fields_values';		
	}	
	
	protected function get_substitutes() {
		$ret = array(
			'%fields' => $this->get_fieldnames($this->fields, $this->query->get_table()),
			'%!table' => $this->get_table($this->query->get_table()),
			'%!values' => $this->get_values($this->fields, $this->query->get_table()),
			'%!fields_values' => $this->get_fields_values($this->fields, $this->query->get_table()),
		);	
		return $ret;
	}

	protected function get_fields_values($arr_fields, IDBTable $table) {
		$fields = array();
		$keyfields = $table->get_table_keys();
		foreach($arr_fields as $column => $value) {
			if (!array_key_exists($column, $keyfields)) {
				$fieldname = $this->prefix_column($column, $table);
				$fields[$fieldname] = DB::format($value, $table, $column);
			}
		}
		foreach($keyfields as $column => $dbfield) { 
			if ($dbfield instanceof DBFieldInt && $dbfield->has_policy(DBFieldInt::AUTOINCREMENT)) {
				// LAst isnert id fix
				$fieldname = $this->prefix_column($column, $table);
				$fields[$fieldname] = 'LAST_INSERT_ID(' . DB::format($value, $table, $column) . ')';
			}
		}
		return Arr::implode(', ', $fields, ' = ');
	}
	
}
