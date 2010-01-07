<?php
/**
 * Where Group Query Builder for Sphinx
 * 
 * @author Gerd Riesselmann
 * @ingroup Sphinx
 */
class DBSqlBuilderWhereGroupSphinx extends DBSqlBuilderWhereGroup {
	/**
	 * Return SQL fragment
	 * 
	 * @return array Array with two members "filter" and "query"
	 */
	public function get_sql() {		
		$query_ret = '';
		$items_query = array();
		$items_filter = array();
		foreach($this->group->get_children() as $where) {
			$arr = $where->get_sql();
			$items_filter = array_merge($items_filter, $arr['filter']);
			$query = $arr['query'];
			if (!empty($query)) {
				if (count($items_query)) {
					$this->push_item($items_query, $this->translate_logical_operator($where->get_logical_operator()));					
				}
				$this->push_item($items_query, $query);
			}
		}
		if (count($items_query)) {
			// We did something, so $ret is not empty
			$query_ret = '(' . implode(' ', $items_query) . ')';
		}
		return array(
			'filter' => $items_filter,
			'query' => $query_ret
		);
	}
	
	/**
	 * Return Driver specific value for AND or OR 
	 */
	protected function translate_logical_operator($operator) {
		switch ($operator) {
			case DBWhere::LOGIC_OR:
				return '|';
			default:
				return '';
		}
	}	
}
