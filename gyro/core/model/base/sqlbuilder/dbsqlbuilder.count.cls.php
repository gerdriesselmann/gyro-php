<?php
require_once dirname(__FILE__) . '/dbsqlbuilder.select.cls.php';

/**
 * Build count query for a given (SELECT) query
 * 
 * Note that HAVING clauses get ignored, so don't use them, if automatically 
 * build count queries. Use sub queries and joining using DBTableSubquery instead
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSqlBuilderCount extends DBSqlBuilderSelect {
	protected function get_sql_template() {
		return 'SELECT COUNT(%distinct%!fields) AS c FROM %!from%join%where';
	}
	
	protected function get_substitutes() {
		$table = $this->query->get_table();
		$ret = array(
			'%!fields' => $this->get_fieldnames($this->get_field_array(), $table),
			'%!from' => $this->get_table_and_alias($table),
			'%distinct' => $this->get_feature_sql($this->params, 'distinct', 'DISTINCT '),
			'%where' => $this->get_where($this->query->get_wheres()),
			'%join' => $this->get_join($this->query->get_subqueries()),
			'%having' => $this->get_having($this->query->get_havings()),
		);
		return $ret;
	}	

	/**
	 * Return field list (either fields or group by)
	 * 
	 * @return array
	 */
	protected function get_field_array() {
		$ret = array();
		$group_by = Arr::get_item($this->params, 'group_by', array());
		if ($group_by) {
			foreach($group_by as $g) {
				$f = $g['field'];
				$ret[] = $f;
			}			
		}
		else {
			$ret = $this->fields;
		}
		return $ret;
	}
	
	protected function get_fieldnames($arr_fields, IDBTable $table) {
    	$count_fields = '*';
    	$fieldnames = array();
    	if (count($arr_fields) == 0) {
    		// * on joins is no good idea
	    	if (count($this->query->get_subqueries()) > 0) {
				$arr_fields = array_keys($table->get_table_keys());
	    	}
		}
		
		foreach($arr_fields as $key => $name) {
			if (is_numeric($key)) {
				$fieldnames[] = $this->prefix_column($name, $table);
			}
			else {
				if (!$this->is_function($key)) {
					$fieldnames[] = $this->prefix_column($key, $table);
				}
			}
		}		
		if (count($fieldnames) > 0) {
	    	$count_fields = implode(', ', $fieldnames);
	    }
		
	    if ($count_fields == '*') {
	    	unset($this->params['distinct']);
	    }
	    
    	return $count_fields;
	}
}
