<?php
/**
 * Build a delete query
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSqlBuilderDelete extends DBSqlBuilderBase {
	protected function get_sql_template() {		
		return 'DELETE FROM %!table%where%order_by%limit';		
	}
	
	protected function get_substitutes() {
		$ret = array(
			'%!table' => $this->get_table($this->query->get_table()),
			'%where' => $this->get_where($this->query->get_wheres()),
			'%limit' => $this->get_limit(Arr::get_item($this->params, 'limit', array(0,0))),
			'%order_by' => $this->get_order_by(Arr::get_item($this->params, 'order_by', array()))
		);	
		return $ret;
	}
}
