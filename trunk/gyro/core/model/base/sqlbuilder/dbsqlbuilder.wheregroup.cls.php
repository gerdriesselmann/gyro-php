<?php
/**
 * SQL Builder for grouped WHERE clauses
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSqlBuilderWhereGroup implements IDBSqlBuilder {
	/**
	 * @var DBWhereGroup
	 */
	protected $group = null;
	
	/**
	 * @param DBWhereGroup $where
	 */
	public function __construct($group, $params = false) {
		$this->group = $group;
	}	
	
	/**
	 * Return SQL fragment
	 * 
	 * @return string
	 */
	public function get_sql() {		
		$ret = '';
		$items = array();
		foreach($this->group->get_children() as $where) {
			$sql = $where->get_sql();
			if (!empty($sql)) {
				if (count($items)) {
					$this->push_item($items, $this->translate_logical_operator($where->get_logical_operator()));					
				}
				$this->push_item($items, $sql);
			}
		}
		if (count($items)) {
			// We did something, so $ret is not empty
			$ret = '(' . implode(' ', $items) . ')';
		}
		return $ret;
	}
	
	/**
	 * Append item to list
	 */
	protected function push_item(&$items, $item) {
		if ($item) {
			$items[] = $item;
		}
	}
	
	/**
	 * Return Driver specific value for AND or OR 
	 */
	protected function translate_logical_operator($operator) {
		return $operator;
	}
}